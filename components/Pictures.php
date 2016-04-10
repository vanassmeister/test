<?php

/*
 * @author Ivan Nikiforov
 * Apr 10, 2016
 */

namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class Pictures extends Widget {
    
    /* @var $pictures \app\models\TblPictures[] */
    public $pictures = [];

    public function run() {
        $html = '';
        foreach ($this->pictures as $picture) {
            $html.= Html::img($picture->getPictureUrl(), ['class' => 'img-thumbnail']);
        }
        
        return $html;
    }
}
