<?php
namespace app\components;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use Yii;
use yii\web\View;

/**
 * @author szabo
 *
 */
class InvisibleRecaptcha extends Widget
{
	/**
	 * 
	 * @var string
	 */
	const JS_API_URL = '//www.google.com/recaptcha/api.js';

	/**
	 * 
	 * @var string
	 */
	public $name = 'Submit';

	/**
	 * 
	 * @var unknown
	 */
	public $siteKey;

	/**
	 * 
	 * @var unknown
	 */
	public $secret;

	/**
	 * 
	 * @var string
	 */
	public $class = 'btn btn-primary btn-block';

	/**
	 * 
	 * @var string
	 */
	public $formSelector = 'form';

	/**
	 * 
	 * @throws InvalidConfigException
	 * @return string
	 */
	public function run()
	{
		if (empty(Yii::$app->captcha)) {
			throw new InvalidConfigException('Required `captcha` component isn\'t set.');
		}
		
		if (empty(Yii::$app->captcha->siteKey)) {
			throw new InvalidConfigException('Required `siteKey` param isn\'t set.');
		}

		$callbackRandomString = time();
		$callback             = 'var recaptchaCallback_' . $callbackRandomString . ' = function() {
			$(\'button.recaptcha-' . $callbackRandomString . ':not(.submit)\').remove();
			$(\'button.recaptcha-' . $callbackRandomString . '.submit\').removeClass(\'hide\');
			$(\'' . $this->formSelector . '\').submit();
		}';

		$view = $this->getView();
		
		$view->registerJs($callback, View::POS_BEGIN);
		$view->registerJsFile('https://www.google.com/recaptcha/api.js', [
			'position' => View::POS_END
		]);

		return Html::button($this->name, [
			'class'         => 'g-recaptcha recaptcha ' . $this->class,
			'data-sitekey'  => Yii::$app->captcha->siteKey,
			'data-callback' => 'recaptchaCallback_' . $callbackRandomString
		]) . Html::submitButton($this->name, [
			'class' => $this->class. ' recaptcha submit hide'
		]);
	}
}