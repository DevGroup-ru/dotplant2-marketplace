<?php
namespace frontend\controllers;

use common\models\Category;
use common\models\CategoryLang;
use Yii;
use yii\caching\TagDependency;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;

class TranslateController extends Controller {
    use \common\traits\LoadModel;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['categories', 'categories-tree', 'category'],
                        'allow' => true,
                        'roles' => ['translateCategories'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'categories-tree' => [
                'class' => '\devgroup\JsTreeWidget\AdjacencyFullTreeDataAction',
                'class_name' => Category::className(),
                'model_label_attribute' => 'slug', // we are using slug, because names are in related record
            ],
        ];
    }

    public function actionCategories()
    {
        return $this->render('categories');
    }

    public function actionCategory($id, $language = 'en')
    {
        $model = Category::findById($id);
        if ($model === null) {
            throw new NotFoundHttpException();
        }

        $model->language = $language;

        $post = \Yii::$app->request->post();
        if ($model->load($post) && $model->validate()) {
            $model->saveTranslation();

            $this->redirect(Url::toRoute(['categories']));
        }

        return $this->renderAjax(
            'category',
            [
                'model' => $model,
            ]
        );
    }
} 