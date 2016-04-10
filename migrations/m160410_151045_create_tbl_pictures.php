<?php

use yii\db\Migration;

class m160410_151045_create_tbl_pictures extends Migration
{
    public function up()
    {
        $this->createTable('tbl_pictures', [
            'id' => $this->primaryKey(),
            'original_name' => $this->string(),
            'body' => $this->binary()
        ]);
    }

    public function down()
    {
        $this->dropTable('tbl_pictures');
    }
}
