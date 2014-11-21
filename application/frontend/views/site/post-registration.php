<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = Yii::t('app', 'Signup');
$this->params['breadcrumbs'][] = $this->title;
if ($model->username_is_temporary) $model->username = '';
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p><?= Yii::t('app', 'Please fill in required fields to complete registration') ?></p>
    <?php $form = ActiveForm::begin(['id' => 'form-signup', 'action' => ['/site/complete-registration']]); ?>
    <?= \frontend\widgets\Alert::widget() ?>
    <div class="row">
        <div class="col-md-6">

            <?= $form->field($model, 'username') ?>
            <?= $form->field($model, 'email') ?>
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'company') ?>
            <?= $form->field($model, 'location') ?>
            <?= $form->field($model, 'url') ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Complete registration'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>

        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
