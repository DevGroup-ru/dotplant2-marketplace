<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => '<i class="fa fa-shopping-cart"></i> Marketplace',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);

            $translatorItems = [
                [
                    'label' => Yii::t('app', 'Translate categories'),
                    'url' => ['/translate/categories'],
                ],
                [
                    'label' => Yii::t('app', 'Translate items'),
                    'url' => ['/translate/items'],
                ]
            ];

            $moderatorItems = [
                [
                    'label' => Yii::t('app', 'Moderate items'),
                    'url' => ['/moderate/items'],
                ],
                [
                    'label' => Yii::t('app', 'Moderate tags'),
                    'url' => ['/moderate/tags'],
                ],
            ];

            $menuItems = [
                ['label' => 'Home', 'url' => ['/site/index']],
                ['label' => 'About', 'url' => ['/site/about']],
                ['label' => 'Contact', 'url' => ['/site/contact']],
                [
                    'label' => Yii::t('app', 'Admin'),
                    'url' => '#',
                    'visible' => Yii::$app->user->can('admin'),
                    'items' => [
                        [
                            'label' => Yii::t('app', 'Manage categories'),
                            'url' => ['/categories/manage'],
                        ],
                    ],
                ],
                [
                    'label' => Yii::t('app', 'Translate'),
                    'url' => '#',
                    'visible' => Yii::$app->user->can('translator'),
                    'items' => $translatorItems,
                ],
                [
                    'label' => Yii::t('app', 'Moderate'),
                    'url' => '#',
                    'visible' => Yii::$app->user->can('moderator'),
                    'items' => $moderatorItems,
                ],
            ];
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
                $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
            } else {
                $menuItems[] = [
                    'label' => Html::img(Yii::$app->user->identity->getAvatar(), ['class'=>'avatar']) .  'Logout (' . Yii::$app->user->identity->username . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post', 'class'=>'logout-link']
                ];
            }
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
                'encodeLabels' => false,
            ]);
            NavBar::end();
        ?>

        <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
