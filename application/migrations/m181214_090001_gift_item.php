<?php

use hauntd\core\migrations\Migration;
use app\models\Profile;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m181214_090001_gift_item extends Migration
{
    public function up()
    {
        $this->createTable('{{%gift_item}}', [
            'id' => $this->primaryKey(),
            'category_id' => $this->integer()->notNull(),
            'file' => $this->string(255)->notNull(),
            'language_category' => $this->string(64)->notNull(),
            'title' => $this->string(255)->notNull(),
            'price' => $this->integer(),
            'is_visible' => $this->boolean()->defaultValue(true),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createIndex('category_idx', '{{%gift_item}}', 'category_id');

        $this->addForeignKey('fk_gift_category',
            '{{%gift_item}}', 'category_id',
            '{{%gift_category}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_gift_category', '{{%gift_item}}');

        $this->dropTable('{{%gift_item}}');
    }
}
