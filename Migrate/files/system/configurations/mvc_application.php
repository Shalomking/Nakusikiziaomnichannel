<?php
/**
 * Framework Application Config
 * @package MVC Framework
 * @author Titan Systems <mail@titansystems.ph>
 */

get_configs();

define("configuration", [
	"root_controller" => false,
	"root_action" => false,
	"default_controller" => "default",
	"default_action" => "index",
	"error_handler_class" => "MVC_ErrorHandler"
]);

define("autoload", [
	"libraries" => [
		["Datatables", "datatable"],
		["Timezones", "timezones"],
		["PhoneNumber", "phone"],
		["Sanitize", "sanitize"],
		["Process", "process"],
		["Whatsapp", "wa"],
		["Session", "session"],
		["Smarty", "smarty"],
		["Paypal", "paypal"],
		["Header", "header"],
		["Guzzle", "guzzle"],
		["Upload", "upload"],
		["Sheets", "sheet"],
		["Cache", "cache"],
		["Zip", "zip"],
		["Zip", "zippy"],
		["File", "file"],
		["SCSS", "scss"],
		["Hash", "hash"],
		["Slug", "slug"],
		["Echo", "echo"],
		["Mail", "mail"],
		["Fcm", "fcm"],
		["URI", "url"],
		["Lex", "lex"]
	],
	"models" => [
		["Api_Model", "api"],
		["Cron_Model", "cron"],
		["Admin_Model", "admin"],
		["Table_Model", "table"],
		["Social_Model", "social"],
		["Widget_Model", "widget"],
		["System_Model", "system"],
		["Device_Model", "device"],
		["Gateway_Model", "gateway"],
		["Whatsapp_Model", "whatsapp"]
	]
]);