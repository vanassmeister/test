<?php

/* @var $this yii\web\View */

$this->title = 'Тестовое задание';

use yii\helpers\Html;
use dosamigos\fileupload\FileUpload;
use yii\web\JsExpression;

?>
<div class="site-index">
    <div class="body-content">
        <div class="row" id="pictures_container">
            <?=$pictures?>
        </div>
        <div class="row">
            <div class="alert alert-danger alert-dismissible" style="display: none" id="upload_error" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Ошибка при загрузке файла!</strong>
                <span class="error-text">Indicates a warning that might need attention.</span>
            </div>
            <?= FileUpload::widget([
                'model' => $model,
                'attribute' => 'file',
                'url' => ['site/upload'],
                'options' => ['accept' => 'image/*'],
                'clientOptions' => [
                    'maxFileSize' => 2000000,
                    'acceptFileTypes' => new JsExpression('/(\.|\/)(gif|jpe?g|png)$/i')
                ],
                'clientEvents' => [
                    'fileuploaddone' => 'window.onFileUploadDone',
                ],
            ]);?>            

            <?=Html::button('Очистка хранилища', ['class' => 'btn btn-default', 'id' => 'button_clean'])?>
            <?=Html::button('Перечитать данные с сервера', ['class' => 'btn btn-default', 'id' => 'button_reload'])?>
        </div>
    </div>
</div>
