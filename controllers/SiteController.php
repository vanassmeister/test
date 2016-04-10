<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\UploadForm;
use app\models\TblPictures;

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

        $fileName = $model->file->baseName . '.' . $model->file->extension;
        
        $picture = new TblPictures();
        $picture->original_name = $fileName;
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
                0 => ['name' => $fileName]
            ]
        ];   
    }
}
