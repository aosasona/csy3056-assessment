<?php
/*
* Author: Ayodeji O.
*
* This class is responsible for making calls to the Lobsters API.
*/

namespace Burrow;

final class Lobsters
{
	// Field that holds the singleton instance of the class
	private static ?self $instance = null;
	// Field that holds the client instance
	private Client $client;

	/**
	 * Private constructor to prevent instantiation from outside the class.
	 *
	 * @throws \Exception if the class is instantiated directly.
	 */
	private function __construct()
	{
		$client = new Client([
			"baseUrl" => "https://lobste.rs/",
			"object" => true,
		]);
		$this->client = $client;
	}

	/**
	 * Returns a singleton instance of the Lobsters class.
	 *
	 * @return self The singleton instance of the class.
	 */
	public static function shared(): self
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Fetches the active page from the Lobsters API.
	 *
	 * @param int $page The page number to fetch.
	 * @return array The data for the specified page.
	 * @throws \InvalidArgumentException if the page number is less than 1.
	 */
	public function getActiveItems(int $page): array
	{
		if ($page < 1) {
			throw new \InvalidArgumentException("Page number must be greater than 0.");
		}

		// Fetches the active page from the Lobsters API.
		$data = $this->client->get("/active/page/{$page}.json")->data;
		return $data;
	}

	/**
	 * Fetches an item by its ID from the Lobsters API.
	 *
	 * @param string $id The ID of the item to fetch.
	 * @return object The item data.
	 * @throws \InvalidArgumentException if the ID is empty or not a string.
	 */
	public function getItemById(string $id): object
	{
		if (empty($id)) {
			throw new \InvalidArgumentException("ID cannot be empty.");
		} elseif (!is_string($id)) {
			throw new \InvalidArgumentException("ID must be a string.");
		}

		// Fetches the item by its ID from the Lobsters API.
		$data = $this->client->get("/s/{$id}.json")->data;
		return $data;
	}
}
