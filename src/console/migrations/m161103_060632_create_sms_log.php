<?php

use yii\db\Migration;

class m161103_060632_create_sms_log extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sms_log}}', [
            'id' => $this->primaryKey(),
            'phone' => $this->string(),
            'message' => $this->text(),
            'send' => $this->boolean(),
            'user_id' => $this->integer(),
            'created_at' => $this->time(),
            'updated_at' => $this->time(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%sms_log}}');
    }
}
