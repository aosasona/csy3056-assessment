<?php
/*
* Author: Ayodeji O.
*
* This is the main entry point for the application, it sets up the router and serves the application.
*/


declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Phlo\Core\{Router, Rule, RuleType};

try {
	Router::new()
		->addRule(Rule::new("public")->setRuleType(RuleType::STATIC)->setTarget(__DIR__ . "/public"))
		->addRule(Rule::new("")->setRuleType(RuleType::STATIC)->setTarget(__DIR__ . "/pages"))
		->serve();
} catch (Exception | Error $e) {
	echo "Internal Server Error: " . $e->getMessage() . PHP_EOL;
}
