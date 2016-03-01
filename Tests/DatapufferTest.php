<?php
/**
 * Created by PHPStorm
 * 06.03.2013.
 */

namespace Datapuffer\Tests;

class DatapufferTest extends Tester
{

	
	public function test_is_authorized()
	{
		$is_authorized = self::$dataPuffer->isAuthorized();
		$this->assertTrue($is_authorized);
	}


	public function test_get_auth_url()
	{
		$url = self::$dataPuffer->getAuthUrl();
		$check = filter_var($url, FILTER_VALIDATE_URL);
		$this->assertNotFalse($check);
	}


	public function test_user()
	{
		$user = (array)self::$dataPuffer->user;
		$this->assertArrayHasKeys(
			$user,
			[
				'_id',
				'id',
				'name',
			]
		);
		$this->assertEquals($user['id'], self::$config['user_id']);
	}


	public function test_configuration()
	{
		$configuration = (array)self::$dataPuffer->configuration;
		$this->assertArrayHasKeys(
			$configuration,
			[
				'services',
				'media',
			]
		);
	}


	public function test_shares()
	{
		$shares = self::$dataPuffer->shares(self::$config['share_url']);
		$this->assertArrayHasKey('shares', $shares);
		$this->assertGreaterThan(
			self::$config['share_greater_than'],
			$shares['shares']
		);
	}
}