<?php
/*
 * Author: Ayodeji O.
 *
 * This is a test class for the Utils class in the Burrow library.
 */

namespace Burrow;

use PHPUnit\Framework\TestCase;


class UtilsTest extends TestCase
{
	private static function makeMockClient(bool $object = true): Client
	{
		return new Client([
			'baseUrl' => 'https://jsonplaceholder.typicode.com/posts',
			'object' => $object,
		]);
	}

	public function testMakeHeaders(): void
	{
		$client = self::makeMockClient(object: false);
		$client->setHeaders([
			'Content-Type' => 'application/json',
		]);

		$utils = new Utils($client);

		// Without any additional headers
		$headers = $utils->makeHeaders([]);
		$this->assertEquals(array('Content-Type: application/json'), $headers);

		// With additional headers
		$headers = $utils->makeHeaders(['Content-Type' => 'text/html']);

		$this->assertEquals(array('Content-Type: text/html'), $headers);
	}

	public function testBuildCurlOptions(): void
	{
		$client = self::makeMockClient(object: false);
		$utils = new Utils($client);

		$expected = [
			CURLOPT_URL => 'https://jsonplaceholder.typicode.com/posts',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 20,
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
			],
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_FAILONERROR => true,
			CURLOPT_HEADER => true,
			CURLOPT_POSTFIELDS => json_encode(["key" => "value"]),
		];

		$output = $utils->buildCurlOptions(
			"",
			"POST",
			['Content-Type' => 'application/json'],
			["key" => "value"]
		);

		$this->assertEquals($expected, $output);
	}
}
