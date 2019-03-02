<?php

namespace youdate\widgets;

use app\helpers\Html;
use app\models\Photo;
use app\models\PhotoAccess;
use app\models\Profile;
use dosamigos\gallery\DosamigosAsset;
use dosamigos\gallery\GalleryAsset;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package youdate\widgets
 */
class ProfilePhotos extends Widget
{
    /**
     * @var Photo[]|array
     */
    public $photos = [];
    /**
     * @var Profile
     */
    public $profile;
    /**
     * @var integer
     */
    public $privatePhotosAccess;
    /**
     * @var array
     */
    public $options = [];
    /**
     * @var array
     */
    public $rowOptions = [];
    /**
     * @var array
     */
    public $wrapperOptions = [];
    /**
     * @var array
     */
    public $photoOptions = [];
    /**
     * @var array
     */
    public $firstPhotoOptions = [];
    /**
     * @var array
     */
    public $labelOptions = [];
    /**
     * @var array
     */
    public $imageOptions = [];
    /**
     * @var array
     */
    public $templateOptions = [];
    /**
     * @var array
     */
    public $clientOptions = [];
    /**
     * @var array
     */
    public $clientEvents = [];
    /**
     * @var bool
     */
    public $showControls = true;
    /**
     * @var int
     */
    public $maxPhotosVisible = 4;
    /**
     * @var int
     */
    public $maxPrivatePhotosVisible = 3;
    /**
     * @var int
     */
    public $privatePhotoBlur = 35;
    /**
     * @var Photo[]
     */
    private $_publicPhotos;
    /**
     * @var Photo[]
     */
    private $_privatePhotos;


    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        $this->templateOptions['id'] = ArrayHelper::getValue($this->templateOptions, 'id', 'blueimp-gallery');
        Html::addCssClass($this->templateOptions, 'blueimp-gallery');
        if ($this->showControls) {
            Html::addCssClass($this->templateOptions, 'blueimp-gallery-controls');
        }

        foreach ($this->clientEvents as $key => $event) {
            if (!($event instanceof JsExpression)) {
                $this->clientOptions[$key] = new JsExpression($event);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::beginTag('div', $this->options);

        if (count($this->photos)) {
            echo Html::beginTag('div', $this->rowOptions);
            echo $this->renderPhotos(
                $this->getPublicPhotos($this->privatePhotosAccess == PhotoAccess::STATUS_APPROVED),
                $this->maxPhotosVisible,
                true
            );
            if ($this->privatePhotosAccess !== PhotoAccess::STATUS_APPROVED) {
                echo $this->renderPhotos($this->getPrivatePhotos(), $this->maxPrivatePhotosVisible);
            }
            echo Html::endTag('div');
        } else {
            echo $this->renderFallback();
        }

        if (!Yii::$app->user->isGuest && $this->privatePhotosAccess !== PhotoAccess::STATUS_APPROVED && count($this->getPrivatePhotos())) {
            echo $this->renderRequestActions();
        }

        echo Html::endTag('div');

        echo $this->renderTemplate();
        $this->registerClientScript();
    }

    /**
     * @param $photos
     * @param $maxPhotosVisible
     * @param bool $renderMainPhoto
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function renderPhotos($photos, $maxPhotosVisible, $renderMainPhoto = false)
    {
        $counter = 1;
        $totalCount = count($photos);
        $hiddenCount = $totalCount - $maxPhotosVisible;

        $html = [];
        $firstPhotoRendered = false;
        foreach ($photos as $photo) {
            if ($renderMainPhoto && !$firstPhotoRendered) {
                $options = $this->firstPhotoOptions;
                $firstPhotoRendered = true;
            } else {
                $options = $this->photoOptions;
            }
            $html[] = $this->renderPhoto($photo, $options,
                $counter > $maxPhotosVisible,
                $counter == $maxPhotosVisible && $hiddenCount ? Yii::t('youdate', '+{0} photos', $hiddenCount) : null
            );
            $counter++;
        }

        return implode("\n", $html);
    }

    /**
     * @param Photo $photo
     * @param array $options
     * @param bool $hidden
     * @param null $label
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function renderPhoto($photo, $options, $hidden = false, $label = null)
    {
        $wrapperOptions = $this->wrapperOptions;
        $labelOptions = $this->labelOptions;
        $imageOptions = $this->imageOptions;

        Html::addCssClass($wrapperOptions, 'gallery-item');
        Html::addCssClass($hiddenPhotosOptions, 'hidden-photos');

        $contents = '<div class="loader"><i class="fa fa-spin fa-spin"></i></div>';

        if ($photo->is_private && $this->privatePhotosAccess !== PhotoAccess::STATUS_APPROVED) {
            $src = $photo->getThumbnail(
                ArrayHelper::getValue($options, 'width'),
                ArrayHelper::getValue($options, 'height'),
                'crop-center',
                ['blur' => $this->privatePhotoBlur]
            );
            $url = false;
            Html::addCssClass($imageOptions, 'img-private');
            if ($label == null) {
                $label = Html::tag('i', '', ['class' => 'fe fe-eye-off']);
                $labelOptions = array_merge([
                    'rel' => 'tooltip',
                    'title' => Yii::t('youdate', 'Private photo'),
                ], $labelOptions);
            }
        } else {
            $src = $photo->getThumbnail(ArrayHelper::getValue($options, 'width'), ArrayHelper::getValue($options, 'height'));
            $url = $photo->getUrl();
        }

        if ($hidden === true) {
            Html::addCssClass($options, 'hidden');
        }

        $contents .= Html::img($src, $imageOptions);

        if ($label) {
            $contents .= Html::tag('div', Html::tag('span', $label), $labelOptions);
        }

        $contents = Html::tag('div', $contents, $wrapperOptions);

        return $url ? Html::a($contents, $url, $options) : Html::tag('div', $contents, $options);
    }

    /**
     * @param bool $allPhotos
     * @return Photo[]
     */
    protected function getPublicPhotos($allPhotos = false)
    {
        if (isset($this->_publicPhotos)) {
            return $this->_publicPhotos;
        }

        $photos = [];

        if ($this->profile->photo_id !== null) {
            $photos[] = $this->profile->photo;
        }

        foreach ($this->photos as $photo) {
            if (($allPhotos || $photo->is_private == false) && $photo->id !== $this->profile->photo_id) {
                $photos[] = $photo;
            }
        }

        $this->_publicPhotos = $photos;

        return $photos;
    }

    /**
     * @return Photo[]
     */
    protected function getPrivatePhotos()
    {
        if (isset($this->_privatePhotos)) {
            return $this->_privatePhotos;
        }

        $photos = [];
        foreach ($this->photos as $photo) {
            if ($photo->is_private == true) {
                $photos[] = $photo;
            }
        }

        $this->_privatePhotos = $photos;

        return $photos;
    }

    /**
     * @return string
     */
    public function renderFallback()
    {
        $contents = '<div class="loader"><i class="fa fa-spin fa-spin"></i></div>';
        $contents .= Html::img($this->profile->getAvatarUrl(600, 600), []);
        $contents = Html::tag('div', $contents, $this->wrapperOptions);

        return Html::tag('div', $contents, $this->firstPhotoOptions);
    }

    /**
     * @return string
     */
    public function renderRequestActions()
    {
        return $this->render('profile/photo-request', [
            'profile' => $this->profile,
            'requestStatus' => $this->privatePhotosAccess,
        ]);
    }

    /**
     * @return string
     */
    public function renderTemplate()
    {
        $template[] = '<div class="slides"></div>';
        $template[] = '<h3 class="title"></h3>';
        $template[] = '<a class="prev text-light">‹</a>';
        $template[] = '<a class="next text-light">›</a>';
        $template[] = '<a class="close text-light"></a>';
        $template[] = '<a class="play-pause"></a>';
        $template[] = '<ol class="indicator"></ol>';

        return Html::tag('div', implode("\n", $template), $this->templateOptions);
    }

    public function registerClientScript()
    {
        $view = $this->getView();
        GalleryAsset::register($view);
        DosamigosAsset::register($view);

        $id = $this->options['id'];
        $options = Json::encode($this->clientOptions);
        $js = "dosamigos.gallery.registerLightBoxHandlers('#$id a', $options);";
        $view->registerJs($js);

        if (!empty($this->clientEvents)) {
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "jQuery('$id').on('$event', $handler);";
            }
            $view->registerJs(implode("\n", $js));
        }
    }
}
