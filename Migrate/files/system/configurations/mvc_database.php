<?php
/**
 * Framework Database
 * @package MVC Framework
 * @author Titan Systems <mail@titansystems.ph>
 */

define("database", [
	"default" => [
		"plugin" => "MVC_PDO",
		"type" => "mysql",
		"host" => env["dbhost"],
		"port" => env["dbport"],
		"name" => env["dbname"],
		"user" => env["dbuser"],
		"pass" => env["dbpass"],
		"persistent" => false
	]
]);