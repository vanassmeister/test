<?php

use yii\db\Migration;

class m160410_151045_create_tbl_pictures extends Migration
{
    public function up()
    {
        $this->createTable('tbl_pictures', [
            'id' => $this->primaryKey(),
            'base_name' => $this->string(),
            'extension' => $this->string(4),
            'mime_type' => $this->string(),
            'body' => $this->binary()
        ]);
    }

    public function down()
    {
        $this->dropTable('tbl_pictures');
    }
}
