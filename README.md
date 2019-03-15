# Google Invisible reCaptcha for Yii2
Based on reCaptcha API 2.0.

### Installation
The preferred way to install this extension is through composer.

- Either run

`php composer.phar require --prefer-dist "szaboolcs/yii2-invisible-recaptcha" "*"`

or add

`"szaboolcs/yii2-invisible-recaptcha" : "*"`

to the require section of your application's composer.json file.

- Sign up for an [reCAPTCHA API keys](https://www.google.com/recaptcha/admin#createsite).
- Add the next component to your configuration file (web.php)

```
'components' =>  [
  'captcha' => [
    'name'    => 'captcha',
    'class'   => 'szaboolcs\recaptcha\InvisibleRecaptcha',
    'siteKey' => 'your siteKey',
    'secret'  => 'your secret'
  ]
]
```
### Usage

login.php

```
<?php
use szaboolcs\recaptcha\InvisibleRecaptcha;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
	'id' => 'login-form'
]);
echo $form->field($model, 'username');
echo $form->field($model, 'password');
echo InvisibleRecaptcha::widget([
  'name'         => 'Submit',
  'formSelector' => '#login-form'
]);
ActiveForm::end();
```

LoginController.php

```
<?php
namespace app\controllers;

use szaboolcs\recaptcha\InvisibleRecaptchaValidator;
use app\models\Login;
use Yii;

class LoginController
{
  public function actionLoginForm()
  {
    $model = new Login();
    
    return $this->render('login', [
      'model' => $model
    ]);
  }

  public function actionLogin()
  {
    $model = new Login();

    $model->load(Yii::$app->request->post());

    if ($model->validate() && InvisibleRecaptchaValidator::validate(Yii::$app->request->post(InvisibleRecaptchaValidator::POST_ELEMENT)) && Yii::$app->user->login($model->getUser())) {
      return $this->goHome();
    }

    return $this->render('login', [
      'model' => $model
    ]);
  }
}
```

Login.php

```
<?php
use yii\base\Model;
use app\models\User;

class Login extends Model
{
  public $username;

  public $password;

  private $_user;

  public function rules()
  {
    // ...
  }

  public function getUser()
  {
    if (!$this->_user) {
      $this->_user = User::find()->byUsername($this->username)->one();
    }
    
    return $this->_user;
  }
}
```
