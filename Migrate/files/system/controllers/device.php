<?php
/**
 * @controller Device
 */

class Device_Controller extends MVC_Controller
{
	public function index()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_GET);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

		if(!isset(
			$request["hash"], 
			$request["device_unique"]
		)):
			set_language(system_default_lang);

			$device = [
				"linked" => false
			];
		else:
			$decode = $this->hash->decode($request["hash"], system_token);

			if(!$decode)
				response(403);

			$uid = $decode;
			$hash = md5($uid);

			if($this->device->checkUserId($uid) < 1)
        		response(403);

			if($this->device->checkDevice($request["device_unique"]) < 1)
        		response(403);

			set_language($this->device->getUserLanguage($uid));

			$device = $this->device->getDevice($request["device_unique"]);

			if($device["uid"] != $uid):
				$userEmail = $this->device->getUserEmail($device["uid"]);
				response(403, ___(__("lang_app_response_devicealreadylinkedunablenew"), [maskEmail($userEmail)]));
			endif;

			$packages = array_map("trim", explode("\n", $device["packages"]));

			$device = [
				"linked" => true,
				"packages" => empty($packages) ? false : implode(",", $packages),
				"receive_sms" => $device["global_device"] < 2 ? 2 : $device["receive_sms"],
				"random_send" => $device["random_send"],
				"random_min" => $device["random_min"],
				"random_max" => $device["random_max"],
			];

			if(isset($request["cleaner"]) && !empty($request["cleaner"])):
				try {
					$cleanerIds = array_values(array_filter(array_unique(explode(",", $request["cleaner"])), function($id){
						return $this->sanitize->isInt($id);
					}));

					$cleanerRows = $this->device->getCleanerSms(implode(",", $cleanerIds), $request["device_unique"]);

					$device["cleaner"] = array_diff($cleanerIds, $cleanerRows);
				} catch(Exception $e){
					// Ignore
				}
        	else:
        		$device["cleaner"] = [];
        	endif;
		endif;

		$device["version"] = $this->file->exists("uploads/builder/" . strtolower(system_package_name . ".apk")) ? (int) system_apk_version : (int) system_apk_version - 1;
		$device["background"] = $this->smarty->fetch("_device/background.tpl");
		$device["language"] = [
			"status_gateway_running" => __("lang_app_status_gateway_running"),
			"status_gateway_touch" => __("lang_app_status_gateway_touch"),
			"device_registered" => __("lang_app_device_registered"),
			"device_unregistered" => __("lang_app_device_unregistered"),
			"terminal_gateway_ready" => __("lang_app_terminal_gateway_ready"),
			"terminal_gateway_register" => __("lang_app_terminal_gateway_register"),
			"terminal_gateway_hash" => __("lang_app_terminal_gateway_hash"),
			"terminal_gateway_registered" => __("lang_app_terminal_gateway_registered"),
			"terminal_gateway_device" => __("lang_app_terminal_gateway_device"),
			"terminal_gateway_connecterror" => __("lang_app_terminal_gateway_connecterror"),
			"terminal_gateway_started" => __("lang_app_terminal_gateway_started"),
			"terminal_gateway_stopped" => __("lang_app_terminal_gateway_stopped"),
			"terminal_gateway_unregistered" => __("lang_app_terminal_gateway_unregistered"),
			"terminal_feature_error" => __("lang_app_terminal_feature_error"),
			"terminal_sms_sent" => __("lang_app_terminal_sms_sent"),
			"terminal_message_failed" => __("lang_app_terminal_message_failed"),
			"dialog_wait" => __("lang_app_dialog_wait"),
			"dialog_exit" => __("lang_app_dialog_exit"),
			"dialog_exit_desc" => __("lang_app_dialog_exit_desc"),
			"camera_qrcode_inside" => __("lang_app_camera_qrcode_inside"),
			"ui_status" => __("lang_app_ui_status"),
			"ui_exit" => __("lang_app_ui_exit"),
			"dialog_update_desc" => __("lang_app_dialog_update_desc"),
			"dialog_update_download" => __("lang_app_dialog_update_download"),
			"gateway_build_text" => __("lang_app_gateway_build_text"),
			"dialog_downloading_update" => __("lang_app_dialog_downloading_update"),
			"dialog_interface_disabled" => __("lang_app_dialog_interface_disabled"),
			"dialog_somethingwent_wrong" => __("lang_app_dialog_somethingwent_wrong"),
			"intro_level_1" => __("lang_app_intro_level_1"),
			"intro_level_2" => __("lang_app_intro_level_2"),
			"intro_level_3" => __("lang_app_intro_level_3"),
			"intro_level_4" => __("lang_app_intro_level_4"),
			"intro_level_5" => __("lang_app_intro_level_5"),
			"intro_level_6" => __("lang_app_intro_level_6"),
			"intro_level_7" => __("lang_app_intro_level_7"),
			"intro_level_8" => __("lang_app_intro_level_8new"),
			"dialog_tour_complete" => __("lang_app_dialog_tour_complete"),
			"push_right_now" => __("lang_app_push_right_now"),
			"device_detailes_updated" => __("lang_app_device_detailes_updated"),
			"ussd_requests_notsupported" => __("lang_app_ussd_requests_notsupported"),
			"notifications_listening_now" => __("lang_app_notifications_listening_now"),
			"button_ok" => __("lang_app_button_ok"),
			"dialog_update_smoothperm" => __("lang_app_dialog_update_smoothperm"),
			"dialog_logging_in" => __("lang_app_dialog_logging_in"),
			"dialog_invalid_email" => __("lang_app_dialog_invalid_email"),
			"dialog_invalid_password" => __("lang_app_dialog_invalid_password"),
			"dialog_notifications_permission" => __("lang_app_dialog_notifications_permission"),
			"dialog_gateway_listennoti" => __("lang_app_dialog_gateway_listennoti"),
			"dialog_gateway_notirestart" => __("lang_app_dialog_gateway_notirestart"),
			"dialog_needto_link" => __("lang_app_dialog_needto_link"),
			"dialog_logoutfrom_device" => __("lang_app_dialog_logoutfrom_device"),
			"document_runbackground" => __("lang_app_document_runbackground"),
			"document_input_email" => __("lang_app_document_input_email"),
			"document_input_password" => __("lang_app_document_input_password"),
			"document_btn_login" => __("lang_app_document_btn_login"),
			"document_text_or" => __("lang_app_document_text_or"),
			"document_btn_scan" => __("lang_app_document_btn_scan"),
			"document_btn_cancel" => __("lang_app_document_btn_cancel"),
			"intro_next_btn" => __("lang_app_intro_next_btn"),
			"intro_back_btn" => __("lang_app_intro_back_btn"),
			"intro_done_btn" => __("lang_app_intro_done_btn"),
			"fetching_messages" => __("lang_app_fetching_messages"),
			"fetching_ussd" => __("lang_app_fetching_ussd"),
			"campaign_resumed" => __("lang_app_campaign_resumed"),
			"campaign_paused" => __("lang_app_campaign_paused")
		];

		response(200, false, $device);
	}

	public function echo()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_GET);

		if(!isset($request["hash"], $request["socket"], $request["did"]))
			response(400);

		$decode = $this->hash->decode($request["hash"], system_token);

		if(!$decode)
			response(403);

		$uid = $decode;
		$hash = md5($uid);

		if($this->device->checkUserId($uid) < 1)
    		response(403);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

		try {
			$this->echo->_cache = $this->cache;
			$this->echo->_guzzle = $this->guzzle;

			$echoToken = $this->echo->token();
		} catch(Exception $e){
			response(400);
		}

		if(!in_array($request["socket"], ["false"]) && !in_array($request["did"], ["false"])):
        	if($this->device->checkDevice($request["did"]) > 0):
        		$device = $this->device->getDevice($request["did"]);

        		if($device["uid"] != $uid):
					$userEmail = $this->device->getUserEmail($device["uid"]);
					response(403, ___(__("lang_app_response_devicealreadylinkedunablenew"), [maskEmail($userEmail)]));
				endif;

				$this->system->update($device["id"], false, "devices", [
					"online_id" => $request["socket"],
					"online_status" => 1
				]);
			endif;
		endif;

		response(200, false, [
			"hash" => $hash,
			"token" => $echoToken,
			"echo" => titansys_echo
		]);
	}

	public function messages()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_GET);

		if(!isset($request["hash"], $request["did"], $request["fetch"]))
			response(400);

		$decode = $this->hash->decode($request["hash"], system_token);

		if(!$decode)
			response(403);

		$uid = $decode;
		$hash = md5($uid);

		if($this->device->checkUserId($uid) < 1)
    		response(403);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        if($this->device->checkDevice($request["did"]) < 1)
        	response(403);

        $device = $this->device->getDevice($request["did"]);

        if($device["uid"] != $uid):
			$userEmail = $this->device->getUserEmail($device["uid"]);
			response(403, ___(__("lang_app_response_devicealreadylinkedunablenew"), [maskEmail($userEmail)]));
		endif;

        $messages = $this->device->getPendingMessages($request["did"]);

        $messageContainer = [];
        $hashContainer = [];

        if(!empty($messages)):
	        foreach($messages as $row):
	        	if($uid == $row["uid"]):
	        		$subscription = set_subscription(
			            $this->system->checkSubscription($uid), 
			            $this->system->getSubscription(false, $uid), 
			            $this->system->getSubscription(false, false, true)
			        );

					if(empty($subscription))
						response(400);

		        	if(!limitation($subscription["send_limit"], $this->system->countQuota($uid, "sent"))):
		        		$messageContainer[] = $row;

			        	$this->system->update($row["id"], false, "sent", [
		        			"status" => 2
		        		]);

		        		$this->system->increment($uid, "sent");
		        	else:
		        		$this->system->update($row["id"], false, "sent", [
		        			"status" => 4
		        		]);
		        	endif;

		        	$hashContainer[$uid] = $hash;
		        else:
		        	$messageContainer[] = $row;

		        	$this->system->update($row["id"], false, "sent", [
	        			"status" => 2
	        		]);

	        		$hashContainer[$row["uid"]] = md5($row["uid"]);
		        endif;
			endforeach;

			try {
				$this->echo->_cache = $this->cache;
				$this->echo->_guzzle = $this->guzzle;

				$echoToken = $this->echo->token();
			} catch(Exception $e){
				response(403);
			}

			if($echoToken):
				if(!empty($hashContainer)):
					foreach($hashContainer as $uhash):
						$this->echo->notify($uhash, [
							"type" => "table"
						]);
					endforeach;
				endif;
			endif;

			if($request["fetch"] > 0):
	        	$global = $device["global_device"] < 2 ? 1 : 0;

	        	$currency = country($device["country"])->getCurrency()["iso_4217_code"];

				$fetch = [
					"payload" => [
						"global" => $global,
						"currency" => $global < 1 ? "None" : $currency,
						"rate" => $device["rate"]
					],
					"messages" => $messageContainer
				];
	        endif;
        endif;

        response(200, false, isset($fetch) ? $fetch : $messageContainer);
	}

	public function ussd()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_GET);

		if(!isset($request["hash"], $request["did"]))
			response(400);

		$decode = $this->hash->decode($request["hash"], system_token);

		if(!$decode)
			response(403);

		$uid = $decode;
		$hash = md5($uid);

		if($this->device->checkUserId($uid) < 1)
    		response(403);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        if($this->device->checkDevice($request["did"]) < 1)
        	response(403);

        $device = $this->device->getDevice($request["did"]);

        if($device["uid"] != $uid):
			$userEmail = $this->device->getUserEmail($device["uid"]);
			response(403, ___(__("lang_app_response_devicealreadylinkedunablenew"), [maskEmail($userEmail)]));
		endif;

        $ussd = $this->device->getPendingUssd($request["did"]);

        if(!empty($ussd)):
	        foreach($ussd as $row):
	        	$this->system->update($row["id"], false, "ussd", [
        			"status" => 2
        		]);
			endforeach;

			try {
				$this->echo->_cache = $this->cache;
				$this->echo->_guzzle = $this->guzzle;

				$echoToken = $this->echo->token();
			} catch(Exception $e){
				response(403);
			}

			if($echoToken):
				$this->echo->notify($hash, [
					"type" => "table"
				]);
			endif;
        endif;

        response(200, false, $ussd);
	}

	public function login()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_POST);

		if(!isset(
			$request["email"], 
			$request["password"], 
			$request["device_unique"], 
			$request["device_model"], 
			$request["device_version"], 
			$request["device_manufacturer"]
		))
			response(400);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_language(system_default_lang);

		if(!$this->sanitize->isEmail($request["email"]))
			response(400, __("lang_device_login_usevalidemail"));

		if($this->device->checkUserEmail($request["email"]) > 0):
			$access = $this->device->getUserAccess($request["email"]);

			if($access["suspended"] > 0):
				response(403, __("lang_device_login_accountsuspended"));
			endif;

			if(!password_verify($request["password"], $access["password"])):
				response(401, __("lang_device_login_incorrectmailorpass"));
			endif;
		else:
			response(401, __("lang_device_login_incorrectmailorpass"));
		endif;

        $subscription = set_subscription(
            $this->system->checkSubscription($access["id"]), 
            $this->system->getSubscription(false, $access["id"]), 
            $this->system->getSubscription(false, false, true)
        );

		if(empty($subscription))
			response(403, __("lang_device_login_nosubscription"));

		if($this->device->checkDevice($request["device_unique"]) > 0):
			$device = $this->device->getDevice($request["device_unique"]);

			if($device["uid"] != $access["id"]):
				$userEmail = $this->device->getUserEmail($device["uid"]);
				response(403, ___(__("lang_app_response_devicealreadylinkednew"), [maskEmail($userEmail)]));
			endif;
			
			response(200, false, [
				"hash" => $this->hash->encode($access["id"], system_token),
				"topic" => md5($access["id"] . $request["device_unique"]),
				"name" => $device["name"],
				"receive_sms" => $device["global_device"] < 2 ? 2 : $device["receive_sms"],
				"random_send" => $device["random_send"],
				"random_min" => $device["random_min"],
				"random_max" => $device["random_max"]
			]);
		endif;

		if(limitation($subscription["device_limit"], $this->system->countDevices($access["id"])))
			response(403, __("lang_device_login_nomoredevice"));

    	date_default_timezone_set($this->device->getUserTimezone($access["id"]));

		$filtered = [
			"uid" => $access["id"],
			"did" => $request["device_unique"],
			"name" => $request["device_model"],
			"version" => $request["device_version"],
			"manufacturer" => ucfirst($request["device_manufacturer"]),
			"random_send" => 1,
			"random_min" => 5,
			"random_max" => 10,
			"limit_status" => 2,
			"limit_interval" => 1,
			"limit_number" => 100,
			"packages" => false,
			"receive_sms" => 1,
			"global_device" => 2,
			"global_priority" => 2,
			"global_slots" => 1,
			"country" => "US",
			"rate" => "0.01",
			"online_id" => false,
			"online_status" => 2,
			"create_date" => date("Y-m-d H:i:s", time())
		];

		if($this->system->create("devices", $filtered)):
			$this->cache->container("user.{$access["hash"]}");
			$this->cache->clear();

			try {
				$this->echo->_cache = $this->cache;
				$this->echo->_guzzle = $this->guzzle;

				$echoToken = $this->echo->token();
			} catch(Exception $e){
				response(400);
			}

			if($echoToken):
				$this->echo->notify($access["hash"], [
					"type" => "table",
					"modal" => true
				]);
			endif;

			if(!empty(system_mailing_address) && in_array("admin_new_device", explode(",", system_mailing_triggers))):
				$mailingContent = <<<HTML
				<p>Hi there!</p>
				<p>This is to inform you that a new device has been linked to account: <strong>{$request["email"]}</strong></p> 
				HTML;

    			$this->mail->send([
					"title" => system_site_name,
					"data" => [
						"subject" => mail_title("Admin Alert Message from " . system_site_name . "!"),
						"content" => $mailingContent
					]
				], system_mailing_address, "_mail/default.tpl", $this->smarty);
    		endif;

			response(200, false, [
				"hash" => $this->hash->encode($access["id"], system_token),
				"topic" => md5($access["id"] . $request["device_unique"]),
				"name" => $request["device_model"],
				"random_send" => 2,
				"random_min" => 5,
				"random_max" => 10
			]);
		else:
			response(500);
		endif;
	}

	public function scan()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_POST);

		if(!isset(
			$request["hash"], 
			$request["device_unique"], 
			$request["device_model"], 
			$request["device_version"], 
			$request["device_manufacturer"]
		))
			response(400);

		$decode = $this->hash->decode($request["hash"], system_token);

		if(!$decode)
			response(403);

		$uid = $decode;
		$hash = md5($uid);

		if($this->device->checkUserId($uid) < 1)
    		response(403);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_language(system_default_lang);

        if($this->device->checkUserHash($hash) < 1)
			response(403, __("lang_device_scan_qrcodeinvalid"));

        $subscription = set_subscription(
	        $this->system->checkSubscription($uid), 
	        $this->system->getSubscription(false, $uid), 
	        $this->system->getSubscription(false, false, true)
	    );

		if(empty($subscription))
			response(403, __("lang_device_scan_nosub"));

		if($this->device->checkSuspension($uid) > 0)
			response(403, __("lang_device_scan_usersuspended"));

		if($this->device->checkDevice($request["device_unique"]) > 0):
			$device = $this->device->getDevice($request["device_unique"]);

			if($device["uid"] != $uid):
				$userEmail = $this->device->getUserEmail($device["uid"]);
				response(403, ___(__("lang_app_response_devicealreadylinkednew"), [maskEmail($userEmail)]));
			endif;

			try {
				$this->echo->_cache = $this->cache;
				$this->echo->_guzzle = $this->guzzle;

				$echoToken = $this->echo->token();
			} catch(Exception $e){
				response(400);
			}

			if($echoToken):
				$this->echo->notify($hash, [
					"type" => "table",
					"modal" => true
				]);
			endif;

			response(200, false, [
				"hash" => $this->hash->encode($uid, system_token),
				"topic" => md5($uid . $request["device_unique"]),
				"name" => $device["name"],
				"receive_sms" => $device["global_device"] < 2 ? 2 : $device["receive_sms"],
				"random_send" => $device["random_send"],
				"random_min" => $device["random_min"],
				"random_max" => $device["random_max"]
			]);
		endif;

		if(limitation($subscription["device_limit"], $this->system->countDevices($uid)))
			response(403, __("lang_device_scan_nomoredevices"));

    	date_default_timezone_set($this->device->getUserTimezone($uid));

		$filtered = [
			"uid" => $uid,
			"did" => $request["device_unique"],
			"name" => $request["device_model"],
			"version" => $request["device_version"],
			"manufacturer" => ucfirst($request["device_manufacturer"]),
			"random_send" => 1,
			"random_min" => 5,
			"random_max" => 10,
			"limit_status" => 2,
			"limit_interval" => 1,
			"limit_number" => 100,
			"packages" => false,
			"receive_sms" => 1,
			"global_device" => 2,
			"global_priority" => 2,
			"global_slots" => 1,
			"country" => "US",
			"rate" => "0.01",
			"online_id" => false,
			"online_status" => 2,
			"create_date" => date("Y-m-d H:i:s", time())
		];

		if($this->system->create("devices", $filtered)):
			$this->cache->container("user.{$hash}");
			$this->cache->clear();

			try {
				$this->echo->_cache = $this->cache;
				$this->echo->_guzzle = $this->guzzle;

				$echoToken = $this->echo->token();
			} catch(Exception $e){
				response(400);
			}

			if($echoToken):
				$this->echo->notify($hash, [
					"type" => "table",
					"modal" => true
				]);
			endif;

			if(!empty(system_mailing_address) && in_array("admin_new_device", explode(",", system_mailing_triggers))):
				$userAccount = $this->system->getUser($uid);

				$mailingContent = <<<HTML
				<p>Hi there!</p>
				<p>This is to inform you that a new device has been linked to account: <strong>{$userAccount["email"]}</strong></p> 
				HTML;

    			$this->mail->send([
					"title" => system_site_name,
					"data" => [
						"subject" => mail_title("Admin Alert Message from " . system_site_name . "!"),
						"content" => $mailingContent
					]
				], system_mailing_address, "_mail/default.tpl", $this->smarty);
    		endif;

			response(200, false, [
				"hash" => $request["hash"],
				"topic" => md5($uid . $request["device_unique"]),
				"name" => $request["device_model"],
				"random_send" => 2,
				"random_min" => 5,
				"random_max" => 10
			]);
		else:
			response(500);
		endif;
	}

	public function report()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_POST);

		if(!isset($request["hash"], $request["did"], $request["code"], $request["status"], $request["message"]))
			response(400);

		try {
			$request["message"] = json_decode(html_entity_decode($request["message"]), true);
		} catch(Exception $e){
			response(400);
		}

		if(!is_array($request["message"]))
			response(400);

		if(!$this->sanitize->isInt($request["status"]))
			response(400);

		if(!in_array($request["status"], [1, 2]))
			response(400);

		$decode = $this->hash->decode($request["hash"], system_token);

		if(!$decode)
			response(403);

		$uid = $decode;
		$hash = md5($uid);

		if($this->device->checkUserId($uid) < 1)
    		response(403);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_language($this->device->getUserLanguage($uid));

		if($this->device->checkDevice($request["did"]) < 1)
        	response(403);

        $device = $this->device->getDevice($request["did"]);

        if($device["uid"] != $uid):
			$userEmail = $this->device->getUserEmail($device["uid"]);
			response(403, ___(__("lang_app_response_devicealreadylinkedunablenew"), [maskEmail($userEmail)]));
		endif;

        date_default_timezone_set($this->device->getUserTimezone($request["message"]["sms"]["uid"]));

        if($this->system->checkQuota($request["message"]["sms"]["uid"]) < 1):
			$this->system->create("quota", [
				"uid" => $request["message"]["sms"]["uid"],
				"sent" => 0,
				"received" => 0,
				"wa_sent" => 0,
				"wa_received" => 0,
				"ussd" => 0,
				"notifications" => 0
			]);
		endif;

        $this->system->update($request["message"]["sms"]["id"], false, "sent", [
			"status" => $request["status"] < 2 ? 3 : 4,
			"status_code" => $request["code"],
			"create_date" => date("Y-m-d H:i:s", time())
        ]);

		if($request["message"]["meta"]["global"] > 0 && $this->system->getPartnership($uid) < 2 && $request["message"]["sms"]["uid"] != $uid):
			$this->cache->container("system.payments", true);

			if(!$this->cache->has("exchange")):
				try {
		            $exchange = json_decode($this->guzzle->get(titansys_api . "/currency?code=" . system_purchase_code, [
		                "allow_redirects" => true,
		                "http_errors" => false
		            ])->getBody()->getContents(), true);

		            if($exchange["status"] == 200):
		            	$this->cache->set("exchange", $exchange, 43200);
		            else:
		            	response(403);
		            endif;
		        } catch(Exception $e){
		            response(403);
		        }
		    endif;

		    $rates = $this->cache->get("exchange");

		    $base_rate = $rates["data"]["USD"] / $rates["data"][strtoupper($request["message"]["meta"]["currency"])];
	        $usd_price = ($base_rate * $request["message"]["meta"]["rate"]) * $rates["data"]["USD"];
	        $final_price = (float) abs($usd_price * $rates["data"][strtoupper(system_currency)]);
	        $commission = (float) (system_partner_commission / 100) * $final_price;

        	if($request["status"] < 2):
		        $this->system->credits($request["message"]["sms"]["uid"], "decrease", $final_price);
		        $this->system->earnings($uid, "increase", $final_price - $commission);

		        $this->system->create("commissions", [
		        	"pid" => $uid,
		        	"sid" => $request["message"]["sms"]["uid"],
		        	"mid" => $request["message"]["sms"]["id"],
		        	"did" => $request["did"],
		        	"original_amount" => $final_price,
		        	"commission_amount" => $commission,
		        	"currency" => system_currency,
		        	"create_date" => date("Y-m-d H:i:s", time())
		        ]);
		    endif;
    	endif;

		try {
			$this->echo->_cache = $this->cache;
			$this->echo->_guzzle = $this->guzzle;

			$echoToken = $this->echo->token();
		} catch(Exception $e){
			response(403);
		}

		if($request["status"] < 2):
			if($echoToken):
				$this->echo->notify($request["message"]["meta"]["global"] > 0 ? md5($request["message"]["sms"]["uid"]) : $hash, [
					"type" => "message",
					"status" => 1,
					"content" => ___(__("lang_device_report_smssentsuccess"), ["<strong><a href=\"#\" class=\"text-warning\" zender-toggle=\"zender.view/sent-{$request["message"]["sms"]["id"]}\">#{$request["message"]["sms"]["id"]}</a></strong>", "<strong>{$device["name"]}</strong>"])
				]);
			endif;
		else:
			if($echoToken):
				$this->echo->notify($request["message"]["meta"]["global"] > 0 ? md5($request["message"]["sms"]["uid"]) : $hash, [
					"type" => "message",
					"status" => 2,
					"content" => ___(__("lang_device_report_smssentfailed"), ["<strong><a href=\"#\" class=\"text-warning\" zender-toggle=\"zender.view/sent-{$request["message"]["sms"]["id"]}\">#{$request["message"]["sms"]["id"]}</a></strong>", "<strong>{$device["name"]}</strong>"])
				]);
			endif;
		endif;

		if($request["message"]["meta"]["global"] < 1):
			$this->process->_sanitize = $this->sanitize;
			$this->process->_guzzle = $this->guzzle;
			$this->process->_lex = $this->lex;

			/**
			 * Process Action Hooks
			 */

			$hooks = $this->process->actionHooks($uid, 1, 1, $request["message"]["sms"]["phone"], $request["message"]["sms"]["message"], $this->device->getActions($uid, 1));

			if(!empty($hooks)):
				foreach($hooks as $hook):
					$this->system->create("events", [
						"uid" => $uid,
						"type" => 2,
						"create_date" => date("Y-m-d H:i:s", time())
					]);
				endforeach;
			endif;
		endif;

		response(200);
	}

	public function received()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_POST);

		if(!isset($request["hash"], $request["device_unique"], $request["slot"], $request["message"]))
			response(400);

		try {
			$request["message"] = json_decode(html_entity_decode($request["message"]), true);
		} catch(Exception $e){
			response(400);
		}

		if(!is_array($request["message"]))
			response(400);

		$decode = $this->hash->decode($request["hash"], system_token);

		if(!$decode)
			response(403);

		$uid = $decode;
		$hash = md5($uid);

		if($this->device->checkUserId($uid) < 1)
    		response(403);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_language($this->device->getUserLanguage($uid));

        $subscription = set_subscription(
            $this->system->checkSubscription($uid), 
            $this->system->getSubscription(false, $uid), 
            $this->system->getSubscription(false, false, true)
        );

		if(empty($subscription))
			response(403);

		if($this->device->checkDevice($request["device_unique"]) < 1)
        	response(403);

        $device = $this->device->getDevice($request["device_unique"]);

        if($device["uid"] != $uid):
			$userEmail = $this->device->getUserEmail($device["uid"]);
			response(403, ___(__("lang_app_response_devicealreadylinkedunablenew"), [maskEmail($userEmail)]));
		endif;

        if($this->system->checkQuota($uid) < 1):
			$this->system->create("quota", [
				"uid" => $uid,
				"sent" => 0,
				"received" => 0,
				"wa_sent" => 0,
				"wa_received" => 0,
				"ussd" => 0,
				"notifications" => 0
			]);
		endif;

        if($device["global_device"] < 2):
        	if($this->system->checkDeleted($uid, $request["message"]["id"], $request["device_unique"]) < 1):
	        	$this->system->create("deleted", [
					"rid" => $request["message"]["id"],
					"uid" => $uid,
					"did" => $request["device_unique"]
				]);
	        endif;

			response(200, false, [
	        	"id" => $request["message"]["id"],
        		"timestamp" => $request["message"]["date"]
	        ]);
        endif;

        if(!empty($request["message"])):
        	date_default_timezone_set($this->device->getUserTimezone($uid));

        	if(strtolower(substr($request["message"]["body"], 0, 4)) === "stop"):
            	try {
				    $number = $this->phone->parse($request["message"]["address"]);

				    $number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

				    if(!$number->isValidNumber())
						response(403);

					if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
						response(403);

					$phone = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);

					if($this->system->checkUnsubscribed($uid, $phone) < 1):
						$this->system->create("unsubscribed", [
							"uid" => $uid,
							"phone" => $phone
						]);
					endif;
				} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
					// Ignore
				}
            endif;

        	if($this->system->checkDeleted($uid, $request["message"]["id"], $request["device_unique"]) < 1):
				if($this->device->checkReceived($request["message"]["id"], $uid, $request["device_unique"]) < 1):
	        		if(!limitation($subscription["receive_limit"], $this->system->countQuota($uid, "received"))):
	        			$filtered = [
							"rid" => $request["message"]["id"],
							"uid" => $uid,
							"did" => $request["device_unique"],
							"slot" => $request["slot"] < 1 ? 1 : 2,
							"phone" => $request["message"]["address"],
							"message" => $request["message"]["body"],
							"receive_date" => date("Y-m-d H:i:s", time())
						];

						$received = $this->system->create("received", $filtered);

						if($received):
							try {
								$this->echo->_cache = $this->cache;
								$this->echo->_guzzle = $this->guzzle;

								$echoToken = $this->echo->token();
							} catch(Exception $e){
								response(403);
							}

							if($echoToken):
								$this->echo->notify($hash, [
									"type" => "message",
									"status" => 1,
									"content" => ___(__("lang_device_received_smsreceived"), ["<strong><a href=\"#\" class=\"text-warning\" zender-toggle=\"zender.view/received-{$received}\">message</a></strong>", "<strong>{$device["name"]}</strong>"])
								]);
							endif;

							$this->system->increment($uid, "received");

							$this->process->_sanitize = $this->sanitize;
							$this->process->_guzzle = $this->guzzle;
							$this->process->_lex = $this->lex;

							/**
							 * Process Webhooks
							 */

							$webhooks = $this->process->webhooks($uid, "sms", [
								"id" => (int) $received,
								"rid" => (int) $filtered["rid"],
								"sim" => $request["slot"] < 1 ? 1 : 2,
								"device" => $filtered["did"],
								"phone" => $filtered["phone"],
								"message" => $filtered["message"],
								"timestamp" => strtotime($filtered["receive_date"])
							], $this->device->getWebhooks($uid, "sms"));

							if(!empty($webhooks)):
								foreach($webhooks as $webhook):
									$this->system->create("events", [
										"uid" => $uid,
										"type" => 1,
										"create_date" => date("Y-m-d H:i:s", time())
									]);
								endforeach;
							endif;

							/**
							 * Process Action Hooks
							 */

							$hooks = $this->process->actionHooks($uid, 1, 2, $filtered["phone"], $filtered["message"], $this->device->getActions($uid, 1));

							if(!empty($hooks)):
								foreach($hooks as $hook):
									$this->system->create("events", [
										"uid" => $uid,
										"type" => 2,
										"create_date" => date("Y-m-d H:i:s", time())
									]);
								endforeach;
							endif;

							/**
							 * Process Action Autoreplies
							 */

							$autoreplies = $this->process->actionAutoreplies($uid, 1, $filtered["phone"], $filtered["message"], $this->device->getActions($uid, 2));

							if(!empty($autoreplies)):
								foreach($autoreplies as $autoreply):
									$this->system->create("sent", [
										"cid" => 0,
							        	"uid" => $uid,
										"did" => $filtered["did"],
										"gateway" => 0,
										"sim" => $request["slot"] < 1 ? 1 : 2,
										"mode" => 1,
										"phone" => $filtered["phone"],
										"message" => $autoreply["message"],
										"status" => 1,
										"status_code" => false,
										"priority" => 2,
										"api" => 2,
										"create_date" => date("Y-m-d H:i:s", time())
							        ]);

								    $this->system->create("events", [
										"uid" => $uid,
										"type" => 2,
										"create_date" => date("Y-m-d H:i:s", time())
									]);

								    $this->fcm->send(md5($uid . $filtered["did"]), [
								    	"type" => "sms",
								    	"global" => 0,
								    	"currency" => "None",
								    	"rate" => (float) 0
								    ]);
								endforeach;
							endif;
						endif;
					endif;
	        	endif;
			endif;
	    endif;

        response(200, false, [
        	"id" => $request["message"]["id"],
        	"save_date" => date("Y-m-d", $request["message"]["date"] / 1000)
        ]);
	}

	public function response()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_POST);

		if(!isset($request["hash"], $request["did"], $request["ussd"], $request["response"]))
			response(400);

		try {
			$request["ussd"] = json_decode(html_entity_decode($request["ussd"]), true);
		} catch(Exception $e){
			response(400);
		}

		if(!is_array($request["ussd"]))
			response(400);

		$decode = $this->hash->decode($request["hash"], system_token);

		if(!$decode)
			response(403);

		$uid = $decode;
		$hash = md5($uid);

		if($this->device->checkUserId($uid) < 1)
    		response(403);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_language($this->device->getUserLanguage($uid));

		if($this->device->checkDevice($request["did"]) < 1)
        	response(403);

        $device = $this->device->getDevice($request["did"]);

        if($device["uid"] != $uid):
			$userEmail = $this->device->getUserEmail($device["uid"]);
			response(403, ___(__("lang_app_response_devicealreadylinkedunablenew"), [maskEmail($userEmail)]));
		endif;

        if($this->system->checkQuota($uid) < 1):
			$this->system->create("quota", [
				"uid" => $uid,
				"sent" => 0,
				"received" => 0,
				"wa_sent" => 0,
				"wa_received" => 0,
				"ussd" => 0,
				"notifications" => 0
			]);
		endif;

        if(!empty($request["ussd"]) && !empty($request["response"])):
        	date_default_timezone_set($this->device->getUserTimezone($uid));

        	$filtered = [
        		"response" => $request["response"],
        		"status" => 3,
        		"create_date" => date("Y-m-d H:i:s", time())
        	];

        	$ussd = $this->system->update($request["ussd"]["id"], false, "ussd", $filtered);

        	if($ussd):
				try {
					$this->echo->_cache = $this->cache;
					$this->echo->_guzzle = $this->guzzle;

					$echoToken = $this->echo->token();
				} catch(Exception $e){
					response(403);
				}

				if($echoToken):
					$this->echo->notify($hash, [
						"type" => "ussd",
						"status" => 1,
						"content" => ___(__("lang_device_ussd_responsereceive"), ["<strong>{$device["name"]}</strong>"])
					]);
				endif;

				$this->system->increment($uid, "ussd");

				$this->system->create("utilities", [
        			"uid" => $uid,
        			"type" => 1,
        			"create_date" => date("Y-m-d H:i:s", time())
	        	]);

				$this->process->_sanitize = $this->sanitize;
				$this->process->_guzzle = $this->guzzle;

				/**
				 * Process Webhooks
				 */

	        	$webhooks = $this->process->webhooks($uid, "ussd", [
					"id" => (int) $ussd,
					"sim" => (int) $request["ussd"]["sim"] < 1 ? 1 : 2,
					"device" => $request["did"],
					"code" => $request["ussd"]["code"],
					"response" => $filtered["response"],
					"timestamp" => strtotime($filtered["create_date"])
				], $this->device->getWebhooks($uid, "ussd"));

				if(!empty($webhooks)):
					foreach($webhooks as $webhook):
						$this->system->create("events", [
							"uid" => $uid,
							"type" => 1,
							"create_date" => date("Y-m-d H:i:s", time())
						]);
					endforeach;
				endif;
        	endif;
	    endif;

        response(200);
	}

	public function notification()
	{
		$this->header->allow();

		$request = $this->sanitize->array($_POST);

		if(!isset($request["hash"], $request["did"], $request["notification"]))
			response(400);

		try {
			$request["notification"] = json_decode(html_entity_decode($request["notification"]), true);
		} catch(Exception $e){
			response(400);
		}

		if(!is_array($request["notification"]))
			response(400);

		$decode = $this->hash->decode($request["hash"], system_token);

		if(!$decode)
			response(403);

		$uid = $decode;
		$hash = md5($uid);

		if($this->device->checkUserId($uid) < 1)
    		response(403);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_language($this->device->getUserLanguage($uid));

        $subscription = set_subscription(
            $this->system->checkSubscription($uid), 
            $this->system->getSubscription(false, $uid), 
            $this->system->getSubscription(false, false, true)
        );

		if(empty($subscription))
			response(403);

		if(limitation($subscription["notification_limit"], $this->system->countQuota($uid, "notifications")))
			response(403);

		if($this->device->checkDevice($request["did"]) < 1)
        	response(403);

        $device = $this->device->getDevice($request["did"]);

        if($device["uid"] != $uid):
			$userEmail = $this->device->getUserEmail($device["uid"]);
			response(403, ___(__("lang_app_response_devicealreadylinkedunablenew"), [maskEmail($userEmail)]));
		endif;

        if($this->system->checkQuota($uid) < 1):
			$this->system->create("quota", [
				"uid" => $uid,
				"sent" => 0,
				"received" => 0,
				"wa_sent" => 0,
				"wa_received" => 0,
				"ussd" => 0,
				"notifications" => 0
			]);
		endif;

        if(!empty($request["notification"])):
        	date_default_timezone_set($this->device->getUserTimezone($uid));

        	$filtered = [
        		"uid" => $uid,
        		"did" => $request["did"],
        		"package" => $request["notification"]["package"],
        		"title" => $request["notification"]["title"],
        		"text" => $request["notification"]["text"],
        		"create_date" => date("Y-m-d H:i:s", $request["notification"]["postTime"] / 1000)
        	];

        	$notification = $this->system->create("notifications", $filtered);

        	if($notification):
				try {
					$this->echo->_cache = $this->cache;
					$this->echo->_guzzle = $this->guzzle;
				
					$echoToken = $this->echo->token();
				} catch(Exception $e){
					response(403);
				}

				if($echoToken):
					$this->echo->notify($hash, [
						"type" => "notification",
						"status" => 1,
						"content" => ___(__("lang_device_notification_notireceive"), ["<strong>{$device["name"]}</strong>"])
					]);
				endif;

	        	$this->system->increment($uid, "notifications");

	        	$this->system->create("utilities", [
        			"uid" => $uid,
        			"type" => 2,
        			"create_date" => date("Y-m-d H:i:s", $request["notification"]["postTime"] / 1000)
	        	]);

	        	$this->process->_sanitize = $this->sanitize;
				$this->process->_guzzle = $this->guzzle;

				/**
				 * Process Webhooks
				 */

	        	$webhooks = $this->process->webhooks($uid, "notifications", [
					"id" => (int) $notification,
					"device" => $filtered["did"],
					"package" => $filtered["package"],
					"title" => $filtered["title"],
					"content" => $filtered["text"],
					"timestamp" => strtotime($filtered["create_date"])
				], $this->device->getWebhooks($uid, "notifications"));

				if(!empty($webhooks)):
					foreach($webhooks as $webhook):
						$this->system->create("events", [
							"uid" => $uid,
							"type" => 1,
							"create_date" => date("Y-m-d H:i:s", time())
						]);
					endforeach;
				endif;
        	endif;
	    endif;

        response(200);
	}
}