<?php
/**
 * Script Installer
 * @author Titan Systems
 */

class Install_Controller extends MVC_Controller
{
	public function index()
	{
		define("install", true);
		
		$this->smarty->assign("data", [
			"timezones" => $this->timezones->generate(),
            "countries" => \CountryCodes::get("alpha2", "country")
		]);

		$this->smarty->display("_install/layout.tpl");
	}

	public function request()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_POST);

		if(!isset($request["site_name"], $request["protocol"], $request["site_desc"], $request["purchase_code"], $request["dbhost"], $request["dbport"], $request["dbname"], $request["dbuser"], $request["dbpass"], $request["name"], $request["email"], $request["password"], $request["timezone"], $request["country"]))
			response(500, "Some fields are empty!");

		if(!$this->sanitize->isEmail($request["email"]))
			response(500, "Invalid email address!");

		if(!$this->sanitize->length($request["name"]))
			response(500, "Name is too short!");

		if(!$this->sanitize->length($request["password"], 5))
			response(500, "Password is too short!");

		if(!in_array($request["timezone"], $this->timezones->generate()))
        	response(500, "Invalid Request!");

        if(!array_key_exists($request["country"], \CountryCodes::get("alpha2", "country")))
        	response(500, "Invalid Request!");

		try {
			new Thamaraiselvam\MysqlImport\Import("install.sql", $request["dbuser"], $request["dbpass"], $request["dbname"], $request["dbhost"], $request["dbport"]);
		} catch(Exception $e){
			response(500, "Invalid Database Credentials!");
		}

		try {
			$systoken = hash("sha256", password_hash(uniqid(time(), true), PASSWORD_DEFAULT));

			$env = <<<ENV
dbhost<=>{$request["dbhost"]}
dbport<=>{$request["dbport"]}
dbname<=>{$request["dbname"]}
dbuser<=>{$request["dbuser"]}
dbpass<=>{$request["dbpass"]}
systoken<=>{$systoken}
installed<=>true
ENV;

			$this->file->put("system/configurations/cc_env.inc", $env);
		} catch(Exception $e){
			response(500, "Invalid Database Credentials!");
		}

		$filtered = [
			"role" => 1,
			"email" => $this->sanitize->email($request["email"]),
			"password" => password_hash($request["password"], PASSWORD_DEFAULT),
			"credits" => 0,
			"earnings" => 0,
			"name" => $request["name"],
			"country" => $request["country"],
			"language" => 1,
			"providers" => false,
			"alertsound" => 1,
			"suspended" => 0,
			"timezone" => $request["timezone"],
			"formatting" => false,
			"confirmed" => 1,
			"partner" => 1
		];

		$qKeys = [];
		$qValues = [];

		foreach($filtered as $key => $value):
			$qKeys[] = $key;
			$qValues[] = is_int($value) ? $value : (is_bool($value) ? "\"\"" : "\"{$value}\"");
		endforeach;

		$qKeys = implode(",", $qKeys);
		$qValues = implode(",", $qValues);

		try {
			$query = <<<SQL
INSERT INTO users ({$qKeys}) VALUES ({$qValues});
UPDATE settings SET value = "{$request["site_name"]}" WHERE name = "site_name";
UPDATE settings SET value = "{$request["site_desc"]}" WHERE name = "site_desc";
UPDATE settings SET value = "{$request["purchase_code"]}" WHERE name = "purchase_code";
UPDATE settings SET value = "{$request["protocol"]}" WHERE name = "protocol";
SQL;

			if($this->file->put("populate.sql", $query)):
				new Thamaraiselvam\MysqlImport\Import("populate.sql", $request["dbuser"], $request["dbpass"], $request["dbname"], $request["dbhost"], $request["dbport"]);
			else:
				response(500, "Unable to populate the database!");
			endif;
		} catch(Exception $e){
			response(500, "Something went wrong!");
		}

		rmrf("templates/_install");
		$this->file->delete("install.sql");
		$this->file->delete("populate.sql");
		$this->file->delete("system/controllers/install.php");

		response(200);
	}
}