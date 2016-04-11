<?php namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;
use app\models\UploadForm;
use app\models\TblPictures;
use app\components\Pictures;
use Imagine\Image\Box;
use Imagine\Gd\Imagine;

class SiteController extends Controller
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new UploadForm();

        $this->view->registerJsFile('js/main.js', ['depends' => [
                'app\assets\AppAsset',
        ]]);

        $pictures = TblPictures::find()->all();
        return $this->render('index', [
                'model' => $model,
                'pictures' => Pictures::widget(['pictures' => $pictures])
        ]);
    }

    public function actionUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new UploadForm();

        if (!Yii::$app->request->isPost) {
            throw new HttpException(400, 'Неверный запрос');
        }

        $model->file = UploadedFile::getInstance($model, 'file');
        if (!$model->file) {
            throw new HttpException(400, 'Файл не был загружен');
        }

        if (!$model->validate()) {
            return [
                'status' => 'error',
                'errors' => $model->getErrors('file')
            ];
        }

        $savePath = Yii::getAlias('@runtime/uploads');
        if (!is_dir($savePath)) {
            mkdir($savePath);
        }

        $picture = new TblPictures();
        $picture->base_name = $model->file->baseName;
        $picture->extension = $model->file->extension;
        $picture->mime_type = $model->file->type;
        $picture->body = fopen($model->file->tempName, "r");

        if (!$picture->save()) {
            return [
                'status' => 'error',
                'errors' => $picture->getErrors()
            ];
        }

        $pictures = TblPictures::find()->all();

        return [
            'status' => 'ok',
            'files' => [
                0 => ['name' => "{$picture->id}.{$picture->extension}"]
            ],
            'html' => Pictures::widget(['pictures' => $pictures])
        ];
    }

    public function actionDownload($id, $extension)
    {
        $dbPicture = TblPictures::findOne(['id' => $id, 'extension' => $extension]);
        if (!$dbPicture) {
            throw new HttpException(404, "Изображение с id = $id не найдено в БД");
        }

        $publishedName = Yii::getAlias("@webroot/assets/images/$id.$extension");
        if (!file_exists($publishedName)) {

            $path = pathinfo($publishedName, PATHINFO_DIRNAME);
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            $imagine = new Imagine();

            $stream = fopen('php://memory', 'r+');
            fwrite($stream, $dbPicture->body);
            rewind($stream);

            $image = $imagine->read($stream);

            $maxSize = new Box(100, 100);
            if (!$maxSize->contains($image->getSize())) {
                $ratio = min(array(
                    $maxSize->getHeight() / $image->getSize()->getHeight(),
                    $maxSize->getWidth() / $image->getSize()->getWidth()
                ));

                $image->resize($image->getSize()->scale($ratio));
            }

            $image->save($publishedName);
        }

        $response = Yii::$app->getResponse();
        $response->headers->set('Content-Type', $dbPicture->mime_type);
        $response->format = Response::FORMAT_RAW;
        if (!is_resource($response->stream = fopen($publishedName, 'r'))) {
            throw new ServerErrorHttpException("Опубликованный файл $publishedName не может быть прочитан");
        }
        return $response->send();
    }

    public function actionPictures()
    {
        $pictures = TblPictures::find()->all();
        return Pictures::widget(['pictures' => $pictures]);
    }

    public function actionClean()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        TblPictures::deleteAll();
        $imagesPath = Yii::getAlias('@webroot/assets/images') . '/*';
        exec("rm -f $imagesPath");

        return ['status' => 'ok'];
    }
}
