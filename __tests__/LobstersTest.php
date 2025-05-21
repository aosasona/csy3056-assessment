<?php
/*
 * Author: Ayodeji O.
 *
 * This is a test class for the Lobsters class.
 */

namespace Burrow;

use PHPUnit\Framework\TestCase;

class LobstersTest extends TestCase
{
	/**
	 * @test
	 * @covers \Burrow\Lobsters::fetchPage()
	 */
	public function testGetActiveItems(): void
	{
		$page = 1;
		$lobsters = Lobsters::shared();
		$data = $lobsters->getActiveItems($page);

		$this->assertIsArray($data, "Data should be an array.");
		$this->assertNotEmpty($data, "Data should not be empty.");
		$this->assertObjectHasProperty("title", $data[0], "All items should have a title attribute.");
		$this->assertObjectHasProperty("shortId", $data[0], "All items should have a shortId attribute.");
		$this->assertObjectHasProperty("score", $data[0], "All items should have a score attribute.");
		$this->assertObjectHasProperty("commentCount", $data[0], "All items should have a commentCount attribute.");
		$this->assertObjectHasProperty("submitterUser", $data[0], "All items should have a submitterUser attribute.");
		$this->assertObjectHasProperty("createdAt", $data[0], "All items should have a createdAt attribute.");
		$this->assertObjectHasProperty("tags", $data[0], "All items should have a tags attribute.");
		$this->assertIsArray($data[0]->tags, "Tags should be an array.");
	}

	public function testGetItemById(): void
	{
		$lobsters = Lobsters::shared();
		$item = $lobsters->getItemById("xriq3g");

		$this->assertIsObject($item, "Item should be an object.");
		$this->assertObjectHasProperty("title", $item, "Item should have a title attribute.");
		$this->assertObjectHasProperty("shortId", $item, "Item should have a shortId attribute.");
		$this->assertObjectHasProperty("url", $item, "Item should have a url attribute for the original article.");

		$this->assertEquals($item->shortId, "xriq3g", "Item shortId should be '8iz3sl'.");
		$this->assertEquals($item->title, "Why You Should Never Use MongoDB (2013)", "Item title should be 'Why You Should Never Use MongoDB (2013)'.");
	}
}
