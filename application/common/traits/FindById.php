<?php

namespace common\traits;

use Yii;

trait FindById
{
    public static function findById($id)
    {
        $cache_key = static::className() . ':' . $id;
        $model = Yii::$app->cache->get($cache_key);
        if ($model === null) {
            $model = static::findOne($id);

            if (is_object($model)) {

                Yii::$app->cache->set(
                    static::className() . ":" . $id,
                    $model,
                    86400,
                    new TagDependency(
                        [
                            'tags' => [
                                \common\behaviors\TagDependencyHelper::getObjectTag(static::className(), $model->id),
                            ],
                        ]
                    )
                );
            }
        }
        return $model;
    }
}
