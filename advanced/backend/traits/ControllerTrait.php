<?php
namespace backend\traits;

use Yii;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use backend\utils\LogUtil;

trait ControllerTrait
{
    private $_verbs = ['POST', 'GET', 'PUT', 'DELETE', 'OPTIONS'];

    public function behaviors()
    {
        return ArrayHelper::merge([
          'corsFilter' => [
              'class' => Cors::className(),
              'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => $this->_verbs,
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Request-Headers' => ['*'],
              ],
          ]
        ], parent::behaviors());
    }

    public function actionOptions()
    {
        if (Yii::$app->getRequest()->getMethod() !== 'OPTIONS') {
            Yii::$app->getResponse()->setStatusCode(405);
        }
        $options = $this->_verbs;
        Yii::$app->getResponse()->getHeaders()->set('Allow', implode(', ', $options));
    }
}
