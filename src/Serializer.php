<?php
/*
* Author: Ayodeji O.
*
* This is a utility class for parsing http response body and headers.
*/

namespace Burrow;

use stdClass;

final class Serializer
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
			$response = self::toObject($response);
			return $response;
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
			return self::toObject($headers);
		}
		return $headers;
	}

	public static function toCamelCase(string $string): string
	{
		// Remove any leading or trailing whitespace
		$string = trim($string);

		if (empty($string)) {
			return '';
		}

		// If it is already in camel case, return it as is
		if (preg_match('/^[a-z][a-zA-Z0-9]*$/', $string)) {
			return $string;
		}

		$string = preg_replace('/[^a-z0-9]+/i', ' ', $string);
		$string = strtolower($string);
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
			if (is_string($key)) {
				// Convert the key to camel case
				$camelCaseKey = self::toCamelCase($key);
				if ($camelCaseKey !== $key) {
					unset($array[$key]);
					$array[$camelCaseKey] = $value;
				}
			}

			// If the value is an array, recursively convert its keys
			if (is_array($value)) {
				self::camelCaseArray($value);
			}
		}
	}

	/**
	 * Convert an array to an object.
	 *
	 * @param array $array
	 * @return object
	 */
	public static function toObject(array &$array): object|array
	{
		self::camelCaseArray($array);
		return json_decode(json_encode($array), false);
	}
}
