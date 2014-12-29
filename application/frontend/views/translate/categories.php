<?php

use yii\helpers\Html;
use \devgroup\JsTreeWidget\TreeWidget;
use yii\web\JsExpression;

echo TreeWidget::widget([
    'treeDataRoute' => ['/translate/categories-tree'],
    'contextMenuItems' => [
        'edit' => [
            'action' => new JsExpression("function(node){
                var \$a = $(node.reference);
                Utils.ModalEdit('/translate/category?id=' + \$a.data('id'));
                return true;
            }
            "),
            'label' => 'Edit',
            'icon' => 'fa fa-pencil',
        ]
    ],
]);