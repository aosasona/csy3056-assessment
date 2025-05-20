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
			array_map(
				function ($key, $value) use (&$response) {
					unset($response[$key]);
					$key = $this->toCamelCase($key);
					$response[$key] = $value;
				},
				array_keys($response),
				$response
			);
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
			array_map(
				function ($key, $value) use (&$headers) {
					unset($headers[$key]);
					$key = $this->toCamelCase($key);
					$headers[$key] = $value;
				},
				array_keys($headers),
				$headers
			);
			return (object)$headers;
		}
		return $headers;
	}

	public static function toCamelCase(string $string): string {
		if(!preg_match('/[^a-z0-9]+/i', $string)) {
			return $string;
		}
		$string = strtolower($string);
		$string = preg_replace('/[^a-z0-9]+/i', ' ', $string);
		$string = ucwords($string);
		return lcfirst(str_replace(' ', '', $string));
	}
}
