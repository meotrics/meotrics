<?php
/**
 * Created by PhpStorm.
 * User: Hoang
 * Date: 3/2/2016
 * Time: 4:46 PM
 */

namespace frontend\modules\trend\controllers;


use yii\web\Controller;
use yii\web\Response;

class DefaultController extends Controller
{
    public function actionIndex(){
        return $this->render('index');
    }

    public function actionAjaxContent(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
          'items' => [
              ['id' => 1, 'text' => 'conghd', 'formated' => 'cc'],
          ],
            'total_count' => 10,
        ];
    }
}