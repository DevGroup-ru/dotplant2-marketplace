<?php

namespace frontend\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\widgets\Menu;

class TranslationLanguageSelector extends Widget
{
    /**
     * @var \common\models\Category
     */
    public $model = null;

    public $baseUrl = null;

    public function run()
    {
        if ($this->model === null) {
            throw new InvalidConfigException("Model should be set");
        }
        if ($this->baseUrl === null) {
            throw new InvalidConfigException("BaseUrl should be set");
        }

        $items = Yii::$app->params['languages'];
        foreach ($items as $key => $value) {
            $items[$key] = [
                'label' => $value,
                'url' => $this->baseUrl . '?id='.$this->model->id.'&language='.$key,
            ];

            if ($key === $this->model->language) {
                $items[$key]['active'] = true;
            }
        }
        $this->view->registerJs('
        $(".translation-language-selector a").click(function(){
            var $this = $(this);
            $.get($this.attr("href"), function(data){

                $this.closest(".bootbox-body").empty().append($("<div>"+data+"</div>"));

            });
            return false;
        });
        ');
        return Menu::widget(['items'=>$items,'options' => ['class'=>'nav nav-pills translation-language-selector']]);
    }
}