<?php
/**
 * Created by PHPStorm
 * 05.03.2013.
 */

namespace Datapuffer;
use Datapuffer\Repository\Session;

class Datapuffer extends Core
{
	/**
	 * Datapuffer constructor sets params.
	 *
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		if (empty($config)) {
			throw new Exception('Configuration is empty, pass an array with auth parameters.');
		}
		self::$config = array_merge(self::$config, $config);
		if (
			isset(self::$config['storage'])
			and is_object(self::$config['storage'])
			and self::$config['storage'] instanceof Repository\StorageInterface
		) {
			self::$storage = self::$config['storage'];
		} else {
			self::$storage = new Repository\Session;
		}
		isset(self::$config['access_token']) and self::setAccessToken(self::$config['access_token']);
		// todo: Separate "listen" to code method
		if (!filter_has_var(INPUT_GET, 'code')) {
			return;
		}
		$code = filter_input(INPUT_GET, 'code');
		if (empty($code)) {
			throw new Exception('Incoming parameter "code" either does not exist or is empty.');
		}
		self::getAccessToken($code);
	}

	/**
	 * checks an valid authorisation
	 * @return mixed
	 */
	public function isAuthorized()
	{
		return self::$storage->has('access_token');
	}

	/**
	 * get URL for forwarding to the authorization
	 * @return string
	 */
	public function getAuthUrl()
	{
		return self::$api_urls['authorize'] . '?' . http_build_query([
			'client_id' => self::$config['consumer_key'],
			'redirect_uri' => self::$config['callback_url'],
			'response_type' => 'code'
		]);
	}

	/**
	 * @param $name
	 *
	 * @return object|Profiles
	 * @throws Exception
	 */
	public function __get($name)
	{
		switch ($name) {
			// User profile data
			case 'user':
				return (object) $this->get('user');
			// General configuration options
			case 'configuration':
				return (object) $this->get('info/configuration');
			// Get all profiles
			case 'profiles':
				return new Profiles;
		}
		throw new Exception('You have called an undefined attribute "' . $name . '".');
	}

	/**
	 * @param $link
	 *
	 * @return mixed
	 */
	public function shares($link)
	{
		return $this->get('links/shares', [
			'url' => $link
		]);
	}
}