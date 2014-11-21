<?php
namespace frontend\controllers;

use common\models\UserService;
use frontend\components\AuthClientHelper;
use Yii;
use common\models\LoginForm;
use common\models\User;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\caching\TagDependency;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\HttpException;

/**
 * Site controller
 */
class SiteController extends Controller
{
    use \common\traits\LoadModel;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
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
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'successCallback'],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function successCallback($client)
    {
        $model  = AuthClientHelper::findUserByService($client);
        if ($model === null) {
            // user not found
            $model = new User(['scenario' => 'registerService']);
            $security = new \yii\base\Security();

            $client = AuthClientHelper::retrieveAdditionalData($client);

            $attributes = AuthClientHelper::mapUserAttributesWithService($client);

            $model->setAttributes($attributes['user']);
            $model->status = User::STATUS_ACTIVE;
            $model->role = User::ROLE_USER;
            if (empty($model->username)) {
                // if we doesn't have username - generate unique random temporary username
                // it will be needed for saving purposes
                $model->username = $security->generateRandomString(18);
                $model->username_is_temporary = 1;
            }

            $model->setPassword($security->generateRandomString(16));

            $model->generateAuthKey();


            if ($model->save() === false) {

                if (isset($model->errors['username'])) {
                    // regenerate username
                    $model->username = $security->generateRandomString(18);
                    $model->username_is_temporary = 1;
                    $model->save();
                }

                if (isset($model->errors['email'])) {
                    // empty email
                    $model->email = null;
                    $model->save();
                }
//                if (count($model->errors) > 0) {
//                    // что-то не так
//                    echo "<PRE>";
//                    var_export($model->errors);
//                    die();
//                }
            }


            $service = new UserService();
            $service->service_type = $client->className();
            $service->service_id = $attributes['service']['service_id'];
            $service->user_id = $model->id;
            $service->save();

        }

        Yii::$app->user->login($model, 86400, new TagDependency([
            'tags' => [
                \common\behaviors\TagDependencyHelper::getObjectTag(User::className(), $model->id),
            ],
        ]));

        if ($model->username_is_temporary == 1 || empty($model->email)) {
            // show post-registration form
            $this->layout = 'minimum-layout';
            $model->setScenario('completeRegistration');

            echo $this->render('post-registration', [
                'model' => $model,
            ]);
            Yii::$app->end();
            return;
        }
    }

    public function actionCompleteRegistration()
    {
        $model = Yii::$app->user->identity;
        if ($model->username_is_temporary) {
            $model->username = '';
        }
        $model->setScenario('completeRegistration');
        $model->load(Yii::$app->request->post());

        if (Yii::$app->request->isPost && $model->validate()) {
            
            $model->username_is_temporary = 0;
            $model->save();

            $auth_action = new \yii\authclient\AuthAction('post-registration', $this);
            return $auth_action->redirect('/');
        } else {
            $this->layout = 'minimum-layout';
            return $this->render('post-registration', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
