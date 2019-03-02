<?php

use hauntd\core\migrations\Migration;
use app\models\Profile;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m181214_080001_gift_category extends Migration
{
    public function up()
    {
        $this->createTable('{{%gift_category}}', [
            'id' => $this->primaryKey(),
            'directory' => $this->string(255)->notNull(),
            'language_category' => $this->string(64)->notNull(),
            'title' => $this->string(255)->notNull(),
            'is_visible' => $this->boolean()->defaultValue(true),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%gift_category}}');
    }
}
