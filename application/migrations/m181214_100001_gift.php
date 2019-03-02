<?php

use hauntd\core\migrations\Migration;
use app\models\Profile;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m181214_100001_gift extends Migration
{
    public function up()
    {
        $this->createTable('{{%gift}}', [
            'id' => $this->primaryKey(),
            'gift_item_id' => $this->integer()->notNull(),
            'from_user_id' => $this->integer(),
            'to_user_id' => $this->integer()->notNull(),
            'is_private' => $this->boolean()->defaultValue(false),
            'message' => $this->string(64),
            'created_at' => $this->integer(),
        ]);

        $this->createIndex('from_user_idx', '{{%gift}}', 'from_user_id');
        $this->createIndex('to_user_idx', '{{%gift}}', 'to_user_id');
        $this->createIndex('is_private_idx', '{{%gift}}', 'is_private');

        $this->addForeignKey('fk_gift_item',
            '{{%gift}}', 'gift_item_id',
            '{{%gift_item}}', 'id',
            $this->cascade, $this->restrict
        );
        $this->addForeignKey('fk_gift_from_user',
            '{{%gift}}', 'from_user_id',
            '{{%user}}', 'id',
            $this->cascade, 'set null'
        );
        $this->addForeignKey('fk_gift_to_user',
            '{{%gift}}', 'to_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_gift_item', '{{%gift}}');
        $this->dropForeignKey('fk_gift_from_user', '{{%gift}}');
        $this->dropForeignKey('fk_gift_to_user', '{{%gift}}');

        $this->dropTable('{{%gift}}');
    }
}
