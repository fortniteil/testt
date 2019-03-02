<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m190114_180001_private_photos extends Migration
{
    public function up()
    {
        $this->addColumn('{{%photo}}', 'is_private', $this->boolean()->defaultValue(false)->after('is_verified'));
        $this->createIndex('photo_private_idx', '{{%photo}}', 'is_private');

        $this->createTable('{{%photo_access}}', [
            'id' => $this->primaryKey(),
            'from_user_id' => $this->integer()->notNull(),
            'to_user_id' => $this->integer()->notNull(),
            'status' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createIndex('photo_access_from_user_idx', '{{%photo_access}}', 'from_user_id');
        $this->createIndex('photo_access_from_idx', '{{%photo_access}}', 'from_user_id');
        $this->createIndex('photo_access_to_idx', '{{%photo_access}}', 'to_user_id');
        $this->createIndex('photo_access_unique_idx', '{{%photo_access}}', ['from_user_id', 'to_user_id'], true);

        $this->addForeignKey('fk_photo_access_from_user',
            '{{%photo_access}}', 'from_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );

        $this->addForeignKey('fk_photo_access_to_user',
            '{{%photo_access}}', 'to_user_id',
            '{{%user}}', 'id',
            $this->cascade, $this->restrict
        );
    }

    public function down()
    {
        $this->dropColumn('{{%photo}}', 'is_private');

        $this->dropForeignKey('fk_photo_access_from_user', '{{%photo_access}}');
        $this->dropForeignKey('fk_photo_access_to_user', '{{%photo_access}}');

        $this->dropTable('{{%photo_access}}');
    }
}
