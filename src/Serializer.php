<?php
/*
* Author: Ayodeji O.
*
* This is a utility class for parsing http response body and headers.
*/

namespace Burrow;

use stdClass;

class Serializer
{
	private Client $client;

	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * @param string $response
	 * @return array|object
	 */
	public function parseResponseBody(string $response)
	{
		$response = json_decode($response, true);
		if ($this->client->isObject()) {
			self::camelCaseArray($response);
			return (object)$response;
		}
		return (array)$response;
	}

	/**
	 * @param string $raw_headers
	 * @return array|stdClass
	 */
	public function parseResponseHeaders(string $raw_headers): array|stdClass
	{
		$headers = [];
		$raw_headers = explode("\r", $raw_headers);
		foreach ($raw_headers as $header) {
			if (!strpos($header, ':')) {
				continue;
			}
			$header = explode(':', $header, 2);
			$key = strtolower(trim($header[0]));
			$value = strtolower(trim($header[1]));
			if ($key) {
				$headers[$key] = $value;
			}
		}
		if ($this->client->isObject()) {
			self::camelCaseArray($headers);
			return (object)$headers;
		}
		return $headers;
	}

	public static function toCamelCase(string $string): string
	{
		// Remove any leading or trailing whitespace
		$string = trim($string);
		// If the string is already in "camel case", return it as is
		if (!preg_match('/[^a-zA-Z0-9]+/i', $string)) {
			return $string;
		}
		$string = strtolower($string);
		$string = preg_replace('/[^a-z0-9]+/i', ' ', $string);
		$string = ucwords($string);
		return lcfirst(str_replace(' ', '', $string));
	}

	/**
	 * Recursively convert all keys in an array to camel case.
	 *
	 * @param array $array
	 * @return void
	 */
	public static function camelCaseArray(array &$array): void
	{
		foreach ($array as $key => &$value) {
			// Convert the key to camel case
			$camelCaseKey = self::toCamelCase($key);
			if ($camelCaseKey !== $key) {
				unset($array[$key]);
				$array[$camelCaseKey] = $value;
			}
			// If the value is an array, recursively convert its keys
			if (is_array($value)) {
				self::camelCaseArray($value);
			}
		}
	}
}
