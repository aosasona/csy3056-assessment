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
	/**
	 * Create a mock client for testing
	 * @param bool $object Whether to return objects or arrays
	 * @return Client The mock client
	 */
	private static function makeMockClient(bool $object = true): Client
	{
		return new Client([
			'baseUrl' => 'https://jsonplaceholder.typicode.com/posts',
			'object' => $object,
		]);
	}

	/**
	 * Test parsing response body as an array
	 * @covers \Burrow\Serializer::parseResponseBody()
	 */
	public function testParseResponseBodyAsArray(): void
	{
		$client = self::makeMockClient(object: false);
		$serializer = new Serializer($client);
		$response = '{"id": 1, "title": "Test", "content_type": "application/json"}';
		$parsedResponse = $serializer->parseResponseBody($response);

		$this->assertIsArray($parsedResponse);
		$this->assertEquals(1, $parsedResponse['id']);
		$this->assertEquals('Test', $parsedResponse['title']);
		$this->assertEquals('application/json', $parsedResponse['content_type']);
	}

	/**
	 * Test parsing response body as an object
	 * @covers \Burrow\Serializer::parseResponseBody()
	 */
	public function testParseResponseBodyAsObject(): void
	{
		$client = self::makeMockClient(object: true);
		$serializer = new Serializer($client);
		$response = '{"id": 1, "title": "Test", "content_type": "application/json"}';
		$parsedResponse = $serializer->parseResponseBody($response);

		$this->assertIsObject($parsedResponse);
		$this->assertEquals(1, $parsedResponse->id);
		$this->assertEquals('Test', $parsedResponse->title);
		$this->assertEquals('application/json', $parsedResponse->contentType);
	}

	/**
	 * Test parsing response headers as an array
	 * @covers \Burrow\Serializer::parseResponseHeaders()
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
	 * @covers \Burrow\Serializer::parseResponseHeaders()
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

	/**
	 * Test toCamelCase conversion of array keys
	 * @covers \Burrow\Serializer::parseResponseHeaders()
	 */
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

	/**
	 * Test camel-casing of nested arrays
	 * @covers \Burrow\Serializer::parseResponseHeaders()
	 */
	public function testCamelCaseNestedArray(): void
	{
		$input = [
			"content-type" => "application/json",
			"nested" => [
				"content-length" => 123,
				"another-nested" => [
					"accept-encoding" => "gzip, deflate",
				],
			],
		];

		$expected = [
			"contentType" => "application/json",
			"nested" => [
				"contentLength" => 123,
				"anotherNested" => [
					"acceptEncoding" => "gzip, deflate",
				],
			],
		];

		Serializer::camelCaseArray($input);
		self::assertEquals($expected, $input);
	}

	/**
	 * Test conversion of array to object
	 * @covers \Burrow\Serializer::toObject()
	 */
	public function testToObject(): void
	{
		$input = [
			"content-type" => "application/json",
			"nested" => [
				"content-length" => 123,
				"another-nested" => [
					"accept-encoding" => "gzip, deflate",
				],
			],
		];

		$expected = new \stdClass();
		$expected->contentType = "application/json";
		$expected->nested = new \stdClass();
		$expected->nested->contentLength = 123;
		$expected->nested->anotherNested = new \stdClass();
		$expected->nested->anotherNested->acceptEncoding = "gzip, deflate";

		self::assertEquals($expected, Serializer::toObject($input));
	}
}
