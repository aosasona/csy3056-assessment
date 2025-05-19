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

	// public function testMergeHeaders(): void
	// {
	// 	$client = self::makeMockClient(object: false);
	// 	$client->makeHeaders
	// }
}
