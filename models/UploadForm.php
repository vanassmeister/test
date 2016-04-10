<?php

/*
 * @author Ivan Nikiforov
 * Apr 10, 2016
 */

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model {

    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
           [['file'], 'image',  'mimeTypes' => ['image/png','image/jpeg','image/gif'], 'skipOnEmpty' => false],
        ];
    }
}
