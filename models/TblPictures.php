<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_pictures".
 *
 * @property integer $id
 * @property string $original_name
 * @property resource $body
 */
class TblPictures extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_pictures';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['original_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'original_name' => 'Original Name',
            'body' => 'Body',
        ];
    }
}
