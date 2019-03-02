<?php

namespace app\controllers;

use app\forms\MessageForm;
use app\forms\ReportForm;
use app\models\PhotoAccess;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\controllers
 */
class ProfileController extends \app\base\Controller
{
    /**
     * @return array
     * @throws \Exception
     */
    public function behaviors()
    {
        $hideProfiles = Yii::$app->settings->get('frontend', 'siteHideUsersFromGuests', false) &&
            Yii::$app->user->isGuest;

        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    ['allow' => true, 'actions' => ['index', 'view', 'request-access'], 'roles' => ['@']],
                    ['allow' => true, 'actions' => ['view'], 'roles' => [$hideProfiles ? '@' : '?']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'request-access' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Profile page (logged-in user)
     */
    public function actionIndex()
    {
        return $this->redirect(['profile/view', 'username' => Yii::$app->user->identity->username]);
    }

    /**
     * Profile page
     *
     * @param $username
     * @return string
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionView($username)
    {
        $user = $this->findUser($username);
        $currentUser = $this->getCurrentUser();
        $likeByCurrentUser = null;
        $blockByCurrentUser = false;
        if (!Yii::$app->user->isGuest) {
            $likeByCurrentUser = $this->likeManager->getUserLike($this->getCurrentUser(), $user);
            $blockByCurrentUser = $this->userManager->isUserBlocked(Yii::$app->user->id, $user->id);
            $this->guestManager->trackVisit($this->getCurrentUser(), $user);
        }

        $privatePhotosAccessStatus = null;
        if (!Yii::$app->user->isGuest) {
            if ($currentUser->isAdmin || $user->id == $currentUser->id) {
                $privatePhotosAccessStatus = PhotoAccess::STATUS_APPROVED;
            } else {
                $privatePhotosAccess = Yii::$app->photoManager->getPhotoAccessStatus($this->getCurrentUser(), $user);
                $privatePhotosAccessStatus = null;
                if ($privatePhotosAccess !== null) {
                    $privatePhotosAccessStatus = $privatePhotosAccess->status;
                }
            }
        }

        return $this->render('view', [
            'user' => $user,
            'profile' => $user->profile,
            'currentUser' => $this->getCurrentUser(),
            'newMessageForm' => new MessageForm(),
            'reportForm' => new ReportForm(),
            'likeByCurrentUser' => $likeByCurrentUser,
            'blockByCurrentUser' => $blockByCurrentUser,
            'photos' => $this->photoManager->getPhotosProvider([
                'userId' => $user->id,
                'pagination' => false,
            ])->getModels(),
            'privatePhotosAccessStatus' => $privatePhotosAccessStatus,
        ]);
    }

    /**
     * @param $username
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     * @throws \yii\base\Exception
     */
    public function actionRequestAccess($username)
    {
        $fromUser = $this->getCurrentUser();
        $toUser = $this->findUser($username);

        Yii::$app->photoManager->requestAccess($fromUser, $toUser);
        $this->sendJson([
            'success' => true,
            'message' => Yii::t('app', 'Photo access requested'),
        ]);
    }

    /**
     * Find user by username
     *
     * @param $username
     * @return \app\models\User|\yii\web\IdentityInterface
     * @throws NotFoundHttpException
     */
    protected function findUser($username = null)
    {
        if (!Yii::$app->user->isGuest && $username == null) {
            return Yii::$app->user->identity;
        }

        /** @var $user \app\models\User */
        $user = $this->userManager->getUser($username);

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        return $user;
    }
}
