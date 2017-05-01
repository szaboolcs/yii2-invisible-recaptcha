<?php
namespace szaboolcs\recaptcha;

use yii\helpers\Json;
use Yii;

/**
 * Google invisible reCaptcha validator.
 *
 */
class InvisibleRecaptchaValidator
{
	/**
	 * Google reCaptcha validation URL and POST element name.
	 */
	const VERIFY_URL   = 'https://www.google.com/recaptcha/api/siteverify';
	const POST_ELEMENT = 'g-recaptcha-response';
	
	/**
	 * @var string   Google reCaptcha textarea value.
	 */
	private $_response;
	
	/**
	 * @var string   Google reCaptcha textarea value.
	 */
	private function __construct($response)
	{
		$this->_response = $response;
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
		
		if (empty(Yii::$app->captcha->secret)) {
			throw new InvalidConfigException('Required `secret` param isn\'t set.');
		}
	}

	protected function _getValidationParams()
	{
		return [
			'secret'   => Yii::$app->captcha->secret,
			'response' => $this->_response,
			'remoteip' => Yii::$app->request->userIP
		];
	}
	
	/**
	 * Validate the response with curl.
	 * 
	 * @return bool   If successed true, otherwise false.
	 */
	protected function _validate()
	{
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, self::VERIFY_URL);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $this->_getValidationParams());
		
		$curlData = curl_exec($curl);
		
		curl_close($curl);
		
		return !empty(Json::decode($curlData, true)['success']);
	}
	
	/**
	 * Run validation.
	 * 
	 * @return bool
	 */
	public function run()
	{
		$this->_checkConfig();

		return $this->_validate();
	}
	
	/**
	 * Run validation with singleton pattern.
	 * 
	 * @var string   Google reCaptcha textarea value.
	 * 
	 * @return bool   If successed true, otherwise false.
	 */
	public static function validate($response)
	{
		return (new static($response))->run();
	}
}