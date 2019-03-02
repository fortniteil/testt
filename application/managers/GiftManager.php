<?php

namespace app\managers;

use app\forms\GiftForm;
use app\models\Gift;
use app\models\GiftCategory;
use app\models\GiftItem;
use app\models\User;
use app\payments\GiftTransaction;
use app\traits\EventTrait;
use Exception;
use Symfony\Component\Finder\Finder;
use Yii;
use yii\base\Component;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class GiftManager extends Component
{
    const CACHE_KEY = 'giftItemsAndCategories';
    const EVENT_BEFORE_SEND_GIFT = 'beforeSendGift';
    const EVENT_AFTER_SEND_GIFT = 'afterSendGift';

    use EventTrait;

    /**
     * @var string
     */
    public $giftsDirectory;
    /**
     * @var string
     */
    public $giftsUrl;

    public function init()
    {
        parent::init();
        $this->giftsDirectory = Yii::getAlias($this->giftsDirectory);
        if (!is_dir($this->giftsDirectory)) {
            FileHelper::createDirectory($this->giftsDirectory);
        }
    }

    /**
     * @param array $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getCategories($params = [])
    {
        return $this->getCategoriesQuery($params)->all();
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function getCategoriesProvider($params = [])
    {
        return new ActiveDataProvider([
            'query' => $this->getCategoriesQuery($params),
        ]);
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getCategoryById($id)
    {
        return $this->getCategoriesQuery()->andWhere(['id' => $id])->one();
    }

    /**
     * @param array $params
     * @return \app\models\query\GiftCategoryQuery
     */
    public function getCategoriesQuery($params = [])
    {
        $query = GiftCategory::find()->orderBy('title asc');

        if (ArrayHelper::getValue($params, 'visibleOnly', false) === true) {
            $query->visible();
        }

        return $query;
    }

    /**
     * @param GiftCategory $giftCategory
     * @param array $params
     * @return ActiveDataProvider
     */
    public function getItemsProvider(GiftCategory $giftCategory, $params = [])
    {
        return new ActiveDataProvider([
            'query' => $this->getItemsQuery($params)->andWhere(['gift_item.category_id' => $giftCategory->id]),
        ]);
    }

    /**
     * @param array $params
     * @return \app\models\query\GiftItemQuery
     */
    public function getItemsQuery($params = [])
    {
        $query = GiftItem::find()->orderBy('title asc');

        if (ArrayHelper::getValue($params, 'visibleOnly', false) === true) {
            $query->visible();
        }

        return $query;
    }

    /**
     * @param $id
     * @return array|null|\yii\db\ActiveRecord|GiftItem
     */
    public function getItemById($id)
    {
        return $this->getItemsQuery()->andWhere(['id' => $id])->one();
    }

    /**
     * @return mixed
     */
    public function getDirectories()
    {
        $directories = FileHelper::findDirectories($this->giftsDirectory, ['recursive' => false]);
        $relativeDirectories = [];
        foreach ($directories as $directory) {
            $relativeDirectory = basename($directory);
            $relativeDirectories[$relativeDirectory] = $relativeDirectory;
        }

        return $relativeDirectories;
    }

    /**
     * @param $directory
     * @throws \yii\base\Exception
     */
    public function checkDirectory($directory)
    {
        $path = $this->giftsDirectory . '/' . $directory;
        if (!is_dir($path)) {
            FileHelper::createDirectory($path);
        }
    }

    /**
     * @throws \Exception
     */
    public function scanDirectories()
    {
        $directories = FileHelper::findDirectories($this->giftsDirectory, ['recursive' => false]);
        foreach ($directories as $directory) {
            $this->scanDirectory(basename($directory));
        }
    }

    /**
     * @param $directory
     * @param null $giftCategory
     * @return bool
     * @throws \Exception
     */
    public function scanDirectory($directory, $giftCategory = null)
    {
        $path = $this->giftsDirectory . '/' . $directory;
        if (!is_dir($path)) {
            Yii::warning("Directory '$path' doesn't exist");
            return false;
        }

        $finder = new Finder();
        $giftsFinder = $finder->files()->depth('== 0')->name('*.png')->name('*.svg');

        if ($giftCategory == null) {
            $giftCategory = $this->getCategoriesQuery()->andWhere(['directory' => $directory])->one();
            if ($giftCategory == null) {
                $giftCategory = new GiftCategory();
                $giftCategory->title = ucfirst($directory);
                $giftCategory->directory = $directory;
                if (!$giftCategory->save()) {
                    throw new \Exception('Could not create new gift category');
                }
            }
        }

        foreach ($giftsFinder->in($path) as $file) {
            /** @var $file \SplFileInfo */
            $this->createGiftItem($giftCategory, $file->getFilename());
        }

        return true;
    }

    /**
     * @param GiftItem $giftItem
     * @return string
     */
    public function getItemUrl(GiftItem $giftItem)
    {
        return sprintf('%s/%s/%s', Yii::getAlias($this->giftsUrl), $giftItem->category->directory, $giftItem->file);
    }

    /**
     * @param GiftCategory $category
     * @param UploadedFile $file
     * @return bool
     * @throws \Exception
     */
    public function saveGiftItem(GiftCategory $category, UploadedFile $file)
    {
        $path = $this->giftsDirectory . '/' . $category->directory . '/' . $file->name;
        if ($file->saveAs($path)) {
            $this->createGiftItem($category, $file->name);
            return true;
        }

        return false;
    }

    /**
     * @param GiftItem $item
     * @return bool
     * @throws \Throwable
     */
    public function deleteGiftItem(GiftItem $item)
    {
        $file = $this->giftsDirectory . '/' . $item->category->directory . '/' . $item->file;

        try {
            FileHelper::unlink($file);
            return $item->delete();
        } catch (\Exception $e) {
            Yii::warning($e->getMessage());
        }

        return false;
    }

    /**
     * @param $category
     * @param $filename
     * @return bool
     * @throws \Exception
     */
    private function createGiftItem($category, $filename)
    {
        $pathInfo = pathinfo($filename);
        $giftItem = new GiftItem();
        $giftItem->category_id = $category->id;
        $giftItem->file = $filename;
        $giftItem->title = ucfirst(strtolower($pathInfo['filename']));
        $giftItem->price = Yii::$app->settings->get('common', 'priceGift');

        return $giftItem->save();
    }

    /**
     * @param GiftCategory $category
     * @return false|int
     * @throws \Throwable
     * @throws \yii\base\ErrorException
     * @throws \yii\db\StaleObjectException
     */
    public function deleteGiftCategory(GiftCategory $category)
    {
        $directory = $this->giftsDirectory . '/' . $category->directory;
        FileHelper::removeDirectory($directory);

        return $category->delete();
    }

    /**
     * @return array
     */
    public function getGiftItems()
    {
        $data = Yii::$app->cache->get(self::CACHE_KEY . Yii::$app->language);
        if ($data === false) {
            /** @var GiftItem[] $giftItems */
            $giftItems = $this->getItemsQuery()->visible()->joinWith(['category'])->orderBy('price asc')->all();
            /** @var GiftCategory[] $giftCategories */
            $giftCategories = $this->getCategoriesQuery()->visible()->all();
            $data = [];
            /**
             * @param $category GiftCategory
             * @param $items GiftItem[]
             * @return array|bool
             */
            $prepareItems = function ($category, $items) {
                $data = [];
                foreach ($items as $item) {
                    if ($category->id == $item->category_id) {
                        $data[] = [
                            'id' => $item->id,
                            'url' => $item->getUrl(),
                            'price' => $item->getPrice(),
                            'title' => $item->getTitle(),
                        ];
                    }
                }
                return $data;
            };

            foreach ($giftCategories as $giftCategory) {
                $data[] = [
                    'category' => [
                        'id' => $giftCategory->id,
                        'title' => $giftCategory->getTitle(),
                    ],
                    'items' => $prepareItems($giftCategory, $giftItems),
                ];
            }
            Yii::$app->cache->set(self::CACHE_KEY, $data, 86400);
        }

        return $data;
    }

    /**
     * @return bool
     */
    public function deleteCache()
    {
        return Yii::$app->cache->delete(self::CACHE_KEY);
    }

    /**
     * @param User $user
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getUserGifts(User $user)
    {
        return Gift::find()->joinWith(['fromUser', 'giftItem'])->latest()->forUser($user->id)->all();
    }

    /**
     * @param User $fromUser
     * @param User $toUser
     * @param GiftForm $giftForm
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function sendGift(User $fromUser, User $toUser, GiftForm $giftForm)
    {
        $giftItem = Yii::$app->giftManager->getItemById($giftForm->giftItemId);
        $this->trigger(self::EVENT_BEFORE_SEND_GIFT, $this->getFromToUserEvent($fromUser, $toUser, $giftItem));
        if (!Yii::$app->balanceManager->hasEnoughCredits($fromUser->id, $giftItem->getPrice())) {
            return false;
        }

        $gift = new Gift();
        $gift->from_user_id = $fromUser->id;
        $gift->to_user_id = $toUser->id;
        $gift->gift_item_id = $giftItem->id;
        $gift->message = $giftForm->message;
        $gift->is_private = $giftForm->isPrivate;
        if (!$gift->save()) {
            throw new Exception('Could not save gift entry');
        }

        if ($giftItem->getPrice() > 0) {
            Yii::$app->balanceManager->decrease(['user_id' => $fromUser->id], $giftItem->getPrice(), [
                'class' => GiftTransaction::class,
                'toUserId' => $giftForm->toUserId,
                'giftItemId' => $giftForm->giftItemId,
                'message' => $giftForm->message,
                'isPrivate' => $giftForm->isPrivate,
            ]);
        }

        $this->trigger(self::EVENT_AFTER_SEND_GIFT, $this->getFromToUserEvent($fromUser, $toUser, $gift));

        return true;
    }
}
