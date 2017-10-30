<?php
namespace szaboolcs\recaptcha;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use Yii;
use yii\web\View;

/**
 * Google invisible reCaptcha validator.
 */
class InvisibleRecaptcha extends Widget
{
	/**
	 * Google reCaptcha api file url.
	 */
	const JS_API_URL = '//www.google.com/recaptcha/api.js';

	/**
	 * @var string   Submit button text.
	 */
	public $name = 'Submit';

	/**
	 * @var string   reCaptcha siteKey.
	 */
	public $siteKey;

	/**
	 * @var string   reCaptcha secret key.
	 */
	public $secret;

	/**
	 * @var string   Submit button 
	 (es).
	 */
	public $btnClass = 'btn btn-primary btn-block';

	/**
	* @var string Recaptcha badge position
	*/
	public $badgePosition = 'inline';
	
	/**
	 * @var string   The form selector what in use the recaptcha.
	 */
	public $formSelector = 'form';

	/**
	 * @var string   Random string.
	 */
	private $_randomString;

	/**
	 * Yii built in "constructor" which set the [_randomString] attribute.
	 */
	public function init()
	{
		$this->_randomString = time();
	}

	/**
	 * Check component config.
	 *
	 * @throws InvalidConfigException
	 */
	protected function _checkConfig()
	{
		if (empty(Yii::$app->captcha)) {
			throw new InvalidConfigException('Required `captcha` component isn\'t set.');
		}
		
		if (empty(Yii::$app->captcha->siteKey)) {
			throw new InvalidConfigException('Required `siteKey` param isn\'t set.');
		}
	}

	/**
	 * Register JS files and strings.
	 * 
	 * @return void
	 */
	protected function _registerJs()
	{
		$this->getView()->registerJs($this->_getCallbackFunction(), View::POS_BEGIN);
		$this->getView()->registerJsFile(self::JS_API_URL, [
			'position' => View::POS_END
		]);
	}

	/**
	 * Render buttons.
	 * 
	 * @return string   Buttons HTML string.
	 */
	protected function _getButtons()
	{
		return Html::button($this->name, [
			'class'         => 'g-recaptcha recaptcha-' . $this->_randomString . ' ' . $this->btnClass,
			'data-sitekey'  => Yii::$app->captcha->siteKey,
			'data-callback' => 'recaptchaCallback_' . $this->_randomString,
			'data-badge' => $this->badgePosition,
		]) . Html::submitButton($this->name, [
			'class' => $this->btnClass. ' recaptcha-' . $this->_randomString . ' submit hide'
		]);
	}

	/**
	 * Render callback function to the reCaptcha button.
	 * 
	 * @return string   Callback function.
	 */
	protected function _getCallbackFunction()
	{
		return 'var recaptchaCallback_' . $this->_randomString. ' = function() {
			$(\'button.recaptcha-' . $this->_randomString. ':not(.submit)\').remove();
			$(\'button.recaptcha-' . $this->_randomString. '.submit\').removeClass(\'hide\');
			$(\'' . $this->formSelector . '\').submit();
		}';
	}
	
	/**
	 * Run the widget.
	 * 
	 * @throws InvalidConfigException
	 * 
	 * @return string   Google reCaptcha buttons.
	 */
	public function run()
	{
		$this->_checkConfig();
		$this->_registerJs();

		return $this->_getButtons();
	}
}
