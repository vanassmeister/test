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
            [['base_name'], 'string', 'max' => 255],
            [['extension'], 'string', 'max' => 4],
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
    
    public function getPictureUrl() {
        return "/assets/images/{$this->id}.{$this->extension}";
    }
}
