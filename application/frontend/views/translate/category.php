<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

?>


<?php
$form = ActiveForm::begin([
    'type'=>ActiveForm::TYPE_HORIZONTAL,
    'options'=>['class'=>'modal-form'],
    'action' => ['/translate/category', 'id' => $model->id, 'language' => $model->language],
]); ?>
<?= \frontend\widgets\TranslationLanguageSelector::widget(['model'=>$model,'baseUrl'=>'/translate/category']) ?>
<?= $form->field($model, 'name'); ?>
<?= $form->field($model, 'description')->textarea(); ?>

<?php ActiveForm::end(); ?>