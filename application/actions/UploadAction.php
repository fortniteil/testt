<?php

namespace app\actions;

use Imagine\Image\Box;
use Yii;
use yii\web\UploadedFile;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\base\DynamicModel;
use yii\imagine\Image;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\actions
 */
class UploadAction extends \trntv\filekit\actions\UploadAction
{
    /**
     * @var integer
     */
    public $thumbnailWidth = 500;
    /**
     * @var integer
     */
    public $thumbnailHeight = 250;
    /**
     * @var string
     */
    public $thumbnailParam = 'thumbnail';
    
    public function run()
    {
        $result = [];
        $uploadedFiles = UploadedFile::getInstancesByName($this->fileparam);

        foreach ($uploadedFiles as $uploadedFile) {
            /* @var \yii\web\UploadedFile $uploadedFile */
            $output = [
                $this->responseNameParam => Html::encode($uploadedFile->name),
                $this->responseMimeTypeParam => $uploadedFile->type,
                $this->responseSizeParam => $uploadedFile->size,
                $this->responseBaseUrlParam =>  $this->getFileStorage()->baseUrl
            ];
            if ($uploadedFile->error === UPLOAD_ERR_OK) {
                $validationModel = DynamicModel::validateData(['file' => $uploadedFile], $this->validationRules);
                if (!$validationModel->hasErrors()) {
                    $path = $this->getFileStorage()->save($uploadedFile);
                    $absolutePath = $this->getFileStorage()->getAbsolutePath($path);
                    try {
                        Image::autorotate($absolutePath)
                            ->strip()
                            ->save();
                    } catch (\Exception $e) {
                        Yii::warning($e->getMessage());
                    }

                    if ($path) {
                        $thumbnail = $this->createThumbnail($path, $this->thumbnailWidth, $this->thumbnailHeight);
                        $output[$this->responsePathParam] = $path;
                        $output[$this->responseUrlParam] = $this->getFileStorage()->baseUrl . '/' . $path;
                        $output[$this->responseDeleteUrlParam] = Url::to([$this->deleteRoute, 'path' => $path]);
                        $output[$this->thumbnailParam] = $this->getFileStorage()->baseUrl . '/' . $thumbnail;
                        $paths = \Yii::$app->session->get($this->sessionKey, []);
                        $paths[] = $path;
                        Yii::$app->session->set($this->sessionKey, $paths);
                        $this->afterSave($path);
                    } else {
                        Yii::warning('Could not save uploaded photo');
                        $output['error'] = true;
                        $output['errors'] = [];
                    }
                } else {
                    $output['error'] = true;
                    $output['errors'] = $validationModel->errors;
                }
            } else {
                $output['error'] = true;
                $output['errors'] = $this->resolveErrorMessage($uploadedFile->error);
            }

            $result['files'][] = $output;
        }
        return $this->multiple ? $result : array_shift($result);
    }

    /**
     * @param $path
     * @param $thumbnailWidth
     * @param $thumbnailHeight
     * @return bool|string
     * @throws \League\Flysystem\FileExistsException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected function createThumbnail($path, $thumbnailWidth, $thumbnailHeight)
    {
        $absolutePath = $this->getFileStorage()->path . '/' . $path;
        $thumbPath = $this->getFileStorage()->save($absolutePath);
        $absoluteThumbPath = $this->getFileStorage()->path . '/' . $thumbPath;
        $thumbImage = Image::thumbnail($absoluteThumbPath, $thumbnailWidth, $thumbnailHeight);
        $thumbImage->save($absoluteThumbPath, ['jpg_quality' => 90]);

        return $thumbPath;
    }

    /**
     * @return \app\files\Storage|\trntv\filekit\Storage
     * @throws \yii\base\InvalidConfigException
     */
    protected function getFileStorage()
    {
        return parent::getFileStorage();
    }
}
