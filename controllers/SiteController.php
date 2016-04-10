<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\UploadForm;
use app\models\TblPictures;

use Imagine\Image\Box;
use Imagine\Gd\Imagine;


class SiteController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex() {
        $model = new UploadForm();
        return $this->render('index', [
            'model' => $model
        ]);
    }

    public function actionLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact() {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
                    'model' => $model,
        ]);
    }

    public function actionAbout() {
        return $this->render('about');
    }

    public function actionUpload() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model = new UploadForm();
        
        if(!Yii::$app->request->isPost) {
            throw new HttpException(400,'Неверный запрос');
        }
        
        $model->file = UploadedFile::getInstance($model, 'file');
        if(!$model->file) {
            throw new HttpException(400,'Файл не был загружен');
        }
        
        if(!$model->validate()) {
            return [
                'status' => 'error',
                'errors' => $model->getErrors()
            ];            
        }
        
        $savePath = Yii::getAlias('@runtime/uploads');
        if(!is_dir($savePath)) {
            mkdir($savePath);
        }

        $picture = new TblPictures();
        $picture->base_name = $model->file->baseName;
        $picture->extension = $model->file->extension;
        $picture->mime_type = $model->file->type;
        $picture->body = fopen($model->file->tempName, "r");
        
        if(!$picture->save()) {
            return [
                'status' => 'error',
                'errors' => $picture->getErrors()
            ];              
        }
        
        return [
            'status' => 'ok',
            'files' => [
                0 => ['name' => "{$picture->id}.{$picture->extension}"]
            ]
        ];   
    }
    
    public function actionDownload($id, $extension) {
        $dbPicture = TblPictures::findOne(['id' => $id, 'extension' => $extension]);
        if(!$dbPicture) {
            throw new HttpException(404, "Изображение с id = $id не найдено в БД");
        }
        
        $publishedName = Yii::getAlias("@webroot/assets/images/$id.$extension");        
        if(!file_exists($publishedName)) {
            
            $path = pathinfo($publishedName, PATHINFO_DIRNAME);
            if(!is_dir($path)) {
                mkdir($path, 0755, true);
            }            
            
            $imagine = new Imagine();
            
            $stream = fopen('php://memory','r+');
            fwrite($stream, $dbPicture->body);
            rewind($stream);            
            
            $image = $imagine->read($stream);
            
            $maxSize = new Box(100, 100);
            if(!$maxSize->contains($image->getSize())) {
                $ratio = min(array(
                    $maxSize->getHeight()/$image->getSize()->getHeight(), 
                    $maxSize->getWidth()/$image->getSize()->getWidth()
                ));            

                $image->resize($image->getSize()->scale($ratio));
            } 
            
            $image->save($publishedName);
        }
        
        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', $dbPicture->mime_type);
        $response->format = Response::FORMAT_RAW;
        if (!is_resource($response->stream = fopen($publishedName, 'r')) ) {
           throw new ServerErrorHttpException("Опубликованный файл $publishedName не может быть прочитан");
        }
        return $response->send();        
    }
}
