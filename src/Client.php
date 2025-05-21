<?php
/*
* Author: Ayodeji O.
*
* This is the main class that wraps the cURL library to safely and easily make HTTP requests.
*/

namespace Burrow;

use stdClass;
use Exception;
use CurlHandle;

final class Client
{
	private false|CurlHandle $curl;
	public string $baseUrl;
	protected bool $object = true;
	protected array $headers = [];
	private Utils $utils;

	/**
	 * Client constructor.
	 *
	 * @param array $options
	 */
	public final function __construct(array $options = [])
	{
		if (!empty($options['baseUrl']) && !is_string($options['baseUrl'])) {
			throw new Exception('`baseUrl` must be a string');
		}

		if (!empty($options['object']) && !is_bool($options['object'])) {
			throw new Exception('`object` must be a boolean');
		}

		if (!empty($options['headers']) && !is_array($options['headers'])) {
			throw new Exception('`headers` must be an array');
		}

		$this->curl = curl_init();
		$this->baseUrl = $options['baseUrl'] ?? '';
		$this->object = $options['object'] ?? true;
		$this->headers = $options['headers'] ?? [
			'Content-Type' => 'application/json',
		];
		$this->utils = new Utils($this);
	}

	/**
	 * Send a GET request.
	 *
	 * @throws Exception
	 */
	final public function get(string $endpoint, ?array $options = []): array|stdClass
	{
		return $this->request($endpoint, array_merge($options, [
			'method' => 'GET',
		]));
	}

	/**
	 * Send a POST request.
	 *
	 * @param string $endpoint
	 * @param array|null $options
	 * @return array|stdClass
	 * @throws Exception
	 */
	final public function post(string $endpoint, ?array $options = []): array|stdClass
	{
		return $this->request($endpoint, array_merge($options, [
			'method' => 'POST',
			'headers' => [
				'Content-Type' => 'application/json',
			],
		]));
	}

	/**
	 * Send a PUT request
	 *
	 * @param string $endpoint
	 * @param array|null $options
	 * @return array|stdClass
	 * @throws Exception
	 */
	final public function put(string $endpoint, ?array $options = []): array|stdClass
	{
		return $this->request($endpoint, array_merge($options, [
			'method' => 'PUT',
			'headers' => [
				'Content-Type' => 'application/json',
			],
		]));
	}

	/**
	 * Send a PATCH request
	 *
	 * @throws Exception
	 */
	final public function patch(string $endpoint, ?array $options = []): array|stdClass
	{
		return $this->request($endpoint, array_merge($options, [
			'method' => 'PATCH',
			'headers' => [
				'Content-Type' => 'application/json',
			],
		]));
	}

	/**
	 * @param string $endpoint
	 * @param array|null $options
	 * @return array|stdClass
	 * @throws Exception
	 */
	final public function delete(string $endpoint, ?array $options = []): array|stdClass
	{
		return $this->request($endpoint, array_merge($options, [
			'method' => 'DELETE',
		]));
	}

	/**
	 * Send a request
	 *
	 * @param string $endpoint
	 * @param array|null $options
	 * @return array|stdClass
	 * @throws Exception
	 */
	final public function request(string $endpoint, ?array $options = []): array|stdClass
	{
		try {
			$curl = $this->curl;
			list($method, $endpoint, $headers, $data) = $this->prepareRequestParams($options, $endpoint);
			$this->setCurlOptions($curl, $endpoint, $method, $headers, $data);
			$full_response = $this->extractHeadersAndBody($curl, $this->executeCurlAndRetryOnSSLError($curl));
			$body = $this->parseResponse($full_response['body'] ?? '');
			$headers = $this->parseResponseHeaders($full_response['headers'] ?? []);
			return $this->makeResponse([
				'headers' => $headers,
				'data' => $body,
				'status' => curl_getinfo($curl, CURLINFO_HTTP_CODE) ?? 200,
			]);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Check if the response should be an object.
	 *
	 * @return bool
	 */
	public function isObject(): bool
	{
		return $this->object;
	}

	/**
	 * Get the headers array.
	 *
	 * @return array
	 */
	public function getHeaders(): array
	{
		return $this->headers;
	}

	/**
	 * Get a single header from the headers array.
	 *
	 * @param string $key
	 * @return string
	 */
	public function getHeader(string $key): string|null
	{
		return $this->headers[$key] ?? null;
	}

	/**
	 * Set the headers array.
	 *
	 * @param array $headers
	 * @return void
	 */
	public function setHeaders(array $headers): void
	{
		$this->headers = $headers;
	}

	/**
	 * Set a single header in the headers array.
	 *
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function setHeader(string $key, string $value): void
	{
		$this->headers[$key] = $value;
	}

	/**
	 * Remove an header from the headers array.
	 *
	 * @param string $key
	 * @return void
	 */
	public function removeHeader(string $key): void
	{
		unset($this->headers[$key]);
	}

	/**
	 * Format the headers array to a proper HTTP header format.
	 *
	 * WARNING: this will override the headers set in the constructor if provided here
	 *
	 * @param array $headers
	 * @return array
	 */
	final public function makeHeaders(array $headers = []): array
	{
		return $this->utils->makeHeaders($headers);
	}

	/**
	 * @param array $response
	 * @return array|stdClass
	 */
	private function makeResponse(array $response = []): array|stdClass
	{
		return $this->utils->makeResponse($response);
	}

	/**
	 * @param string $raw_headers
	 * @return array|stdClass
	 */
	private function parseResponseHeaders(string $raw_headers): array|stdClass
	{
		return $this->utils->parseResponseHeaders($raw_headers);
	}


	/**
	 * @param CurlHandle $curl
	 * @param string $endpoint
	 * @param string $method
	 * @param array $headers
	 * @param array $data
	 * @return void
	 */
	private function setCurlOptions(CurlHandle $curl, string $endpoint, string $method, array $headers, array $data): void
	{
		$this->utils->setCurlOptions($curl, $endpoint, $method, $headers, $data);
	}

	/**
	 * @param string $response
	 * @return array|object
	 */
	private function parseResponse(string $response)
	{
		return $this->utils->parseResponseBody($response);
	}

	/**
	 * @param CurlHandle|bool $curl
	 * @param bool|string $response
	 * @return array{'headers': string, 'body': string}
	 */
	private function extractHeadersAndBody(CurlHandle|bool $curl, bool|string $response): array
	{
		return $this->utils->extractHeadersAndBody($curl, $response);
	}

	/**
	 * @param array|null $options
	 * @param string $endpoint
	 * @return array
	 */
	private function prepareRequestParams(?array $options, string $endpoint): array
	{
		return $this->utils->prepareRequestParams($options, $endpoint);
	}


	/**
	 * @param CurlHandle|bool $curl
	 * @return bool|string
	 * @throws HTTPException
	 */
	private function executeCurlAndRetryOnSSLError(CurlHandle|bool $curl): string|bool
	{
		return $this->utils->executeCurlAndRetryOnSSLError($curl);
	}


	/**
	 * @return void
	 */
	final public function __destruct()
	{
		if ($this->curl) {
			curl_close($this->curl);
		}
	}
}
