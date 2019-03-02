<?php

namespace app\modules\admin\forms;

use app\modules\admin\models\GiftCategory;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\forms
 */
class GiftItemsUploadForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $files;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['files', 'file', 'skipOnEmpty' => false,
                'extensions' => 'png, svg',
                'mimeTypes' => ['image/png', 'image/svg+xml'],
                'maxFiles' => 25,
            ],
        ];
    }

    /**
     * @param GiftCategory $category
     * @return bool
     * @throws \Exception
     */
    public function upload(GiftCategory $category)
    {
        if (!$this->validate()) {
            return false;
        }

        foreach ($this->files as $file) {
            Yii::$app->giftManager->saveGiftItem($category, $file);
        }

        Yii::$app->giftManager->deleteCache();

        return true;
    }
}
