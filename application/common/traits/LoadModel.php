<?php

namespace common\traits;

use Yii;
use yii\caching\TagDependency;
use yii\web\NotFoundHttpException;

/**
 * LoadModel trait is used in controllers for loading model by it's ID with Taggable caching support
 * If model not found - throws 404 exception
 *
 * @package common\traits
 */
trait LoadModel
{
    /**
     * Loads model, can cause exception if not found or create new if it is empty.
     * @param string $modelName
     * @param string $id
     * @param bool $createIfEmptyId
     * @param bool $useCache
     * @param int $cacheLifetime
     * @param bool $throwException
     * @return mixed|null
     * @throws NotFoundHttpException
     */
    public static function loadModel(
        string $modelName,
        $id,
        $createIfEmptyId = false,
        $useCache = true,
        $cacheLifetime = 86400,
        $throwException = true
    ) {
        $model = null;
        if (empty($id)) {
            if ($createIfEmptyId === true) {
                $model = new $modelName;
            } else {
                if ($throwException) {
                    throw new NotFoundHttpException;
                } else {
                    return null;
                }
            }
        }
        if ($useCache === true) {
            $model = Yii::$app->cache->get($modelName::className() . ":" . $id);
        }
        if (!is_object($model)) {
            $model = $modelName::findOne($id);
            
            if (is_object($model) && $useCache === true) {
                Yii::$app->cache->set(
                    $modelName::className() . ":" . $id,
                    $model,
                    $cacheLifetime,
                    new TagDependency(
                        [
                            'tags' => [
                                \common\behaviors\TagDependencyHelper::getObjectTag($modelName::className(), $model->id),
                            ],
                        ]
                    )
                );
            }
        }
        if (!is_object($model)) {
            if ($throwException) {
                throw new NotFoundHttpException;
            } else {
                return null;
            }
        }
        return $model;
    }
}
