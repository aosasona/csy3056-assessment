<?php
/*
 * Author: Ayodeji O.
 *
 * This is a test class for the Serializer class in the Burrow library.
 */

namespace Burrow;

use PHPUnit\Framework\TestCase;

class SerializerTest extends TestCase
{
	private static function makeMockClient(bool $object = true): Client
	{
		return new Client([
			'baseUrl' => 'https://jsonplaceholder.typicode.com/posts',
			'object' => $object,
		]);
	}

	/**
	 * Test parsing response body as an array
	 */
	public function testParseResponseBodyAsArray(): void
	{
		$client = self::makeMockClient(object: false);
		$serializer = new Serializer($client);
		$response = '{"id": 1, "title": "Test"}';
		$parsedResponse = $serializer->parseResponseBody($response);

		$this->assertIsArray($parsedResponse);
		$this->assertEquals(1, $parsedResponse['id']);
		$this->assertEquals('Test', $parsedResponse['title']);
	}

	/**
	 * Test parsing response body as an object
	 */
	public function testParseResponseBodyAsObject(): void
	{
		$client = self::makeMockClient(object: true);
		$serializer = new Serializer($client);
		$response = '{"id": 1, "title": "Test"}';
		$parsedResponse = $serializer->parseResponseBody($response);

		$this->assertIsObject($parsedResponse);
		$this->assertEquals(1, $parsedResponse->id);
		$this->assertEquals('Test', $parsedResponse->title);
	}

	/**
	 * Test parsing response headers as an array
	 */
	public function testParseResponseHeaders(): void
	{
		$client = self::makeMockClient(object: false);
		$serializer = new Serializer($client);
		$rawHeaders = "Content-Type: application/json\r\nContent-Length: 123\r\n";
		$parsedHeaders = $serializer->parseResponseHeaders($rawHeaders);

		$this->assertIsArray($parsedHeaders);
		$this->assertEquals('application/json', $parsedHeaders['content-type']);
		$this->assertEquals('123', $parsedHeaders['content-length']);
	}

	/**
	 * Test parsing response headers as an object
	 */
	public function testParseResponseHeadersAsObject(): void
	{
		$client = self::makeMockClient(object: true);
		$serializer = new Serializer($client);
		$rawHeaders = "Content-Type: application/json\r\nContent-Length: 123\r\n";
		$parsedHeaders = $serializer->parseResponseHeaders($rawHeaders);

		$this->assertIsObject($parsedHeaders);
		$this->assertEquals('application/json', $parsedHeaders->contentType);
		$this->assertEquals('123', $parsedHeaders->contentLength);
	}

	public function testCamelCaseConversion(): void
	{
		$client = self::makeMockClient(object: true);
		$serializer = new Serializer($client);

		$cases = [
			"content-type" => "contentType",
			"content-length" => "contentLength",
			"accept-encoding" => "acceptEncoding",
			"text-123" => "text123",
			"Test_Header" => "testHeader",
		];

		foreach ($cases as $input => $expected) {
			$actual = $serializer->toCamelCase($input);
			$this->assertEquals($expected, $actual, "Failed for input: $input");
		}
	}
}
