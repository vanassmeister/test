<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';

use yii\helpers\Html;
use dosamigos\fileupload\FileUpload;
use yii\web\JsExpression;

?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <?= FileUpload::widget([
                'model' => $model,
                'attribute' => 'file',
                'url' => ['site/upload'],
                'options' => ['accept' => 'image/*'],
                'clientOptions' => [
                    'maxFileSize' => 2000000,
                    'acceptFileTypes' => new JsExpression('/(\.|\/)(gif|jpe?g|png)$/i')
                ],
                // Also, you can specify jQuery-File-Upload events
                // see: https://github.com/blueimp/jQuery-File-Upload/wiki/Options#processing-callback-options
                'clientEvents' => [
                    'fileuploaddone' => 'function(e, data) {
                                            console.log(e);
                                            console.log(data);
                                        }',
                    'fileuploadfail' => 'function(e, data) {
                                            console.log(e);
                                            console.log(data);
                                        }',
                ],
            ]);?>            

            <?=Html::button('Очистка хранилища', ['class' => 'btn btn-default'])?>
            <?=Html::button('Перечитать данные с сервера', ['class' => 'btn btn-default'])?>
        </div>
    </div>
</div>
