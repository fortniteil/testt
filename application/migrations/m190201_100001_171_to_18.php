<?php

use hauntd\core\migrations\Migration;
use yii\helpers\ArrayHelper;
use app\models\User;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m190201_100001_171_to_18 extends Migration
{
    public function up()
    {
        $notificationsUserSettings = Yii::$app->notificationManager->getUserSettings();
        $keys = ArrayHelper::getColumn($notificationsUserSettings, 'alias');
        $query = User::find()->select('id')->asArray();
        foreach ($query->batch(500) as $users) {
            $data = [];
            foreach ($users as $user) {
                foreach ($keys as $key) {
                    $data[] = ["user.{$user['id']}", $key, 1];
                }
            }
            $this->batchInsert('{{%setting}}', [
                'category',
                'key',
                'value',
            ], $data);
        }
    }

    public function down()
    {
    }
}
