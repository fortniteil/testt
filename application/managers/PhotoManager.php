<?php

namespace app\managers;

use app\events\FromToUserEvent;
use app\models\PhotoAccess;
use app\models\Profile;
use app\models\Photo;
use app\models\query\PhotoQuery;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\data\ActiveDataProvider;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\managers
 */
class PhotoManager extends Component
{
    const EVENT_PHOTO_ACCESS_REQUEST = 'photoAccessRequest';
    const EVENT_PHOTO_ACCESS_ACTION = 'photoAccessAction';

    /**
     * @param $id
     * @param array $params
     * @return Photo|array|null
     * @throws \Exception
     */
    public function getPhoto($id, $params = [])
    {
        return $this->getQuery($params)->andWhere(['photo.id' => $id])->one();
    }

    /**
     * @param $photo Photo
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deletePhoto($photo)
    {
        if ($photo->delete()) {
            Yii::$app->photoStorage->delete($photo->source);
            return true;
        }

        return false;
    }

    /**
     * @param $userId
     * @param $photoId
     * @param array $params
     * @return Photo|array|null
     * @throws \Exception
     */
    public function getUserPhoto($userId, $photoId, $params = [])
    {
        return $this->getQuery($params)->andWhere(['photo.id' => $photoId, 'photo.user_id' => $userId])->one();
    }

    /**
     * @param $userId
     * @param null $photoId
     * @return bool
     * @throws \Exception
     */
    public function resetUserPhoto($userId, $photoId = null)
    {
        $profile = Profile::findOne(['user_id' => $userId]);
        if ($profile == null) {
            throw new \Exception('Profile not found');
        }
        if ($photoId == null) {
            $photo = $this->getQuery()->forUser($userId)->orderBy('id desc')->one();
            if ($photo == null) {
                return false;
            }
            $photoId = $photo->id;
        }

        $profile->photo_id = $photoId;

        return $profile->save();
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     * @throws \Exception
     */
    public function getPhotosProvider($params = [])
    {
        $query = $this->getQuery($params);

        if (isset($params['unverifiedFirst']) && $params['unverifiedFirst']) {
            $query->orderBy('photo.is_verified asc, photo.id desc');
        }

        $dataProviderOptions = [
            'query' => $query,
        ];

        if (isset($params['pagination'])) {
            $dataProviderOptions['pagination'] = $params['pagination'];
        }

        return new ActiveDataProvider($dataProviderOptions);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function isVerificationEnabled()
    {
        return Yii::$app->settings->get('common', 'photoModerationEnabled');
    }

    /**
     * @param \app\models\User $fromUser
     * @param \app\models\User $toUser
     * @return bool
     */
    public function hasPhotosAccess($fromUser, $toUser)
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        $status = $this->getPhotoAccessStatus($fromUser, $toUser);
        if ($status == null) {
            return false;
        }

        return $status == PhotoAccess::STATUS_APPROVED;
    }

    /**
     * @param \app\models\User $fromUser
     * @param \app\models\User $toUser
     * @return PhotoAccess|array|null|\yii\db\ActiveRecord
     */
    public function getPhotoAccessStatus($fromUser, $toUser)
    {
        return PhotoAccess::find()->where(['from_user_id' => $fromUser->id, 'to_user_id' => $toUser->id])->one();
    }

    /**
     * @param \app\models\User $fromUser
     * @param \app\models\User $toUser
     * @return bool
     * @throws Exception
     */
    public function requestAccess($fromUser, $toUser)
    {
        $photoAccess = $this->getPhotoAccessStatus($fromUser, $toUser);
        if ($photoAccess == null) {
            $photoAccess = new PhotoAccess();
            $photoAccess->from_user_id = $fromUser->id;
            $photoAccess->to_user_id = $toUser->id;
            $photoAccess->status = PhotoAccess::STATUS_REQUESTED;
            if (!$photoAccess->save()) {
                throw new Exception('Could not save request entry');
            }

            $event = new FromToUserEvent(['fromUser' => $fromUser, 'toUser' => $toUser, 'relatedData' => $photoAccess]);
            $this->trigger(self::EVENT_PHOTO_ACCESS_REQUEST, $event);

            return true;
        }

        return $photoAccess->status == PhotoAccess::STATUS_APPROVED;
    }

    /**
     * @param $fromUser
     * @param $toUser
     * @param bool|int $action
     * @return bool
     * @throws Exception
     */
    public function approveOrRejectPhotoAccess($fromUser, $toUser, $action)
    {
        $photoAccessRequest = $this->getPhotoAccessStatus($fromUser, $toUser);
        if ($photoAccessRequest == null) {
            return false;
        }

        if (is_bool($action)) {
            $photoAccessRequest->status = $action ? PhotoAccess::STATUS_APPROVED : PhotoAccess::STATUS_REQUESTED;
        } else {
            $photoAccessRequest->status = $action;
        }

        if (!$photoAccessRequest->save()) {
            throw new Exception('Could not save access entry');
        }

        $event = new FromToUserEvent([
            'fromUser' => $fromUser,
            'toUser' => $toUser,
            'relatedData' => $photoAccessRequest,
            'extraData' => ['action' => $action]
        ]);
        $this->trigger(self::EVENT_PHOTO_ACCESS_ACTION, $event);

        return true;
    }

    /**
     * @param $user
     * @return ActiveDataProvider
     */
    public function getPhotoAccessProvider($user)
    {
        return new ActiveDataProvider([
            'query' => PhotoAccess::find()->joinWith(['toUser', 'toUser.profile'])
                ->andWhere('user.blocked_at is null')
                ->andWhere(['photo_access.to_user_id' => $user->id]),
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);
    }

    /**
     * @param array $params
     * @return PhotoQuery
     * @throws \Exception
     */
    protected function getQuery($params = [])
    {
        if (!isset($params['verifiedOnly'])) {
            $params['verifiedOnly'] = $this->isVerificationEnabled();
        }

        $query = Photo::find()
            ->verified($params['verifiedOnly'])
            ->orderBy('photo.id desc');

        if (isset($params['userId'])) {
            $query->andWhere(['photo.user_id' => $params['userId']]);
        }

        return $query;
    }
}
