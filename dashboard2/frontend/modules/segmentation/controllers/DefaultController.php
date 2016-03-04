<?php
/**
 * Created by PhpStorm.
 * User: Hoang
 * Date: 3/2/2016
 * Time: 4:46 PM
 */

namespace frontend\modules\segmentation\controllers;


use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex(){
        return $this->render('index');
    }
}