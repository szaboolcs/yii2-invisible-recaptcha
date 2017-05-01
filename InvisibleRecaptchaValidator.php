<?php
namespace szaboolcs\recaptcha;

use yii\helpers\Json;
use Yii;

/**
 * 
 * @author szabo
 *
 */
class InvisibleRecaptchaValidator
{
	/**
	 * 
	 * @var string
	 */
	const VERIFY_URL   = 'https://www.google.com/recaptcha/api/siteverify';
	const POST_ELEMENT = 'g-recaptcha-response';
	
	/**
	 * 
	 * @var unknown
	 */
	private $_response;
	
	/**
	 * 
	 * @param string $response
	 */
	private function __construct($response)
	{
		$this->_response = $response;
	}
	
	/**
	 * 
	 * @throws InvalidConfigException
	 */
	private function _checkConfig()
	{
		if (empty(Yii::$app->captcha)) {
			throw new InvalidConfigException('Required `captcha` component isn\'t set.');
		}
		
		if (empty(Yii::$app->captcha->secret)) {
			throw new InvalidConfigException('Required `secret` param isn\'t set.');
		}
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @return unknown
	 */
	function _recaptcha_qsencode ($data) {
		$req = "";
		foreach ( $data as $key => $value )
			$req .= $key . '=' . urlencode( stripslashes($value) ) . '&';
			
			// Cut the last '&'
			$req=substr($req,0,strlen($req)-1);
			
			return $req;
	}
	
	/**
	 * 
	 * @param array $params
	 * @return bool
	 */
	private function _validate(array $params)
	{
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, self::VERIFY_URL);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		
		$curlData = curl_exec($curl);
		
		curl_close($curl);
		
		return !empty(Json::decode($curlData, true)['success']);
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function run()
	{
		$this->_checkConfig();
		
		$params = [
			'secret'   => Yii::$app->captcha->secret,
			'response' => $this->_response,
			'remoteip' => Yii::$app->request->userIP
		];
		
		return $this->_validate($params);
	}
	
	/**
	 * 
	 * @param string $response
	 * @return bool
	 */
	public static function validate($response)
	{
		return (new static($response))->run();
	}
}