<?php
/**
 * @controller Requests
 * @desc Handles all ajax requests from front-end
 */

class Requests_Controller extends MVC_Controller
{
	public function index()
	{	
		$this->header->allow(site_url);

		$type = $this->sanitize->string($this->url->segment(4));
		$request = $this->sanitize->array($_POST);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        $this->cache->container("system.plugins");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getPlugins());
        endif;

        set_plugins($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

		switch($type):
			case "login":
				if($this->session->has("logged"))
	            	response(302);

				if(!isset($request["email"], $request["password"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isEmail($request["email"]))
					response(500, __("lang_response_invalid_emailpass"));

				if(!$this->sanitize->length($request["password"], 5))
					response(500, __("lang_response_invalid_emailpass"));

				if(system_recaptcha < 2):
					if(empty(system_recaptcha_key) || empty(system_recaptcha_secret))
	            		response(500, __("lang_recaptcha_add_keys"));

					if(!isset($request["g-recaptcha-response"]))
						response(500, __("lang_response_invalid"));
				endif;
				
				if(system_recaptcha < 2):
					try {
		            	$recaptcha = json_decode($this->guzzle->get("https://www.recaptcha.net/recaptcha/api/siteverify?secret=" . system_recaptcha_secret . "&response={$request["g-recaptcha-response"]}", [
			                "http_errors" => false
			            ])->getBody()->getContents());
		            } catch(Exception $e){
		            	response(500, __("lang_response_went_wrong"));
		            }
		        endif;

				$filtered = [
					"email" => $this->sanitize->email($request["email"]),
					"password" => $request["password"]
				];

				if(system_recaptcha < 2 && !$recaptcha->success)
					response(500, __("lang_response_solve_captcha"));

				if($this->system->checkEmail($filtered["email"]) > 0):
					$this->cache->container("forgot.confirm", true);

					$raw = $this->system->getPassword($filtered["email"]);

					if($raw["suspended"] > 0)
						response(500, __("lang_response_suspended"));

					$userAccount = $this->system->getUser($raw["id"]);

					if(!in_array("register_confirm", explode(",", system_mailing_triggers))):
						$userAccount["confirmed"] = 1;
					endif;

					if($userAccount["confirmed"] < 2):
						if($this->cache->has($filtered["email"])):
							$this->cache->delete($filtered["email"]);	

							$this->session->delete("language");
							$this->session->set("logged", $userAccount);

							response(301, __("lang_response_loggedin_success"));
						else:
							if(password_verify($filtered["password"], $raw["password"])):
								$this->cache->delete($filtered["email"]);

								$this->session->delete("language");
								$this->session->set("logged", $userAccount);

								response(301, __("lang_response_loggedin_success"));
							else:
								response(500, __("lang_response_invalid_emailpass"));
							endif;
						endif;
					else:
        				$this->cache->container("register.confirm", true);

        				$confirmHash = $this->hash->encode(rand(0, time()), system_token);
        				$confirmLink = site_url("dashboard/authenticate/register", true) . "/{$confirmHash}";

        				$this->cache->set($confirmHash, $userAccount["id"], 600);

        				$mailContent = <<<HTML
						<p>{$GLOBALS["__"]("lang_requests_loginnotverified_emaildesc1")}</p>
						<p>{$GLOBALS["__"]("lang_requests_loginnotverified_emaildesc2")}</p> 
						<p><a href="{$confirmLink}">{$confirmLink}</a></p>
						<p>{$GLOBALS["__"]("lang_requests_loginnotverified_emaildesc3")}</p>
						HTML;

        				if($this->mail->send([
							"title" => system_site_name,
							"data" => [
								"subject" => mail_title(___(__("lang_requests_loginnotverified_emailtitle"), [system_site_name])),
								"content" => $mailContent
							]
						], $filtered["email"], "_mail/default.tpl", $this->smarty)):
        					response(200, ___(__("lang_requests_loginnotverified_response"), ["<strong>{$filtered["email"]}</strong>"]));
            			else:
            				response(500, __("lang_requests_loginnotverified_responseunable"));
            			endif;
					endif;
				else:
					response(500, __("lang_response_invalid_emailpass"));
				endif;

				break;
			case "forgot":
				if($this->session->has("logged"))
	            	response(301, __("lang_response_session_true"));

				if(!isset($request["email"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isEmail($request["email"]))
					response(500, __("lang_response_invalid_email"));

				if(system_recaptcha < 2):
					if(empty(system_recaptcha_key) || empty(system_recaptcha_secret))
	            		response(500, __("lang_recaptcha_add_keys"));

					if(!isset($request["g-recaptcha-response"]))
						response(500, __("lang_response_invalid"));
				endif;

				$filtered = [
					"email" => $this->sanitize->email($request["email"]),
				];

				if(system_recaptcha < 2):
				 	try {
		            	$recaptcha = json_decode($this->guzzle->get("https://www.recaptcha.net/recaptcha/api/siteverify?secret=" . system_recaptcha_secret . "&response={$request["g-recaptcha-response"]}", [
			                "http_errors" => false
			            ])->getBody()->getContents());
		            } catch(Exception $e){
		            	response(500, __("lang_response_went_wrong"));
		            }
		        endif;

		        if(system_recaptcha < 2 && !$recaptcha->success)
					response(500, __("lang_response_solve_captcha"));

				if($this->system->checkEmail($filtered["email"]) > 0):
					$this->cache->container("forgot.confirm", true);

					$temporaryPass = $this->hash->encode(rand(0, time()), system_token);

					$this->cache->set($temporaryPass, $filtered["email"], 600);
					
					if($this->mail->send([
						"title" => system_site_name,
						"data" => [
							"subject" => mail_title(__("lang_response_retrieval_received")),
							"password" => $temporaryPass
						]
					], $filtered["email"], "_mail/forgot.tpl", $this->smarty)):
						response(200, ___(__("lang_forgot_response_recoverysent"), [$filtered["email"]]));
					else:
						response(500, __("lang_forgot_response_recoveryfail"));
					endif;
				else:
					response(500, __("lang_response_invalid_email"));
				endif;

				break;
			case "register":
				if($this->session->has("logged"))
	            	response(302);

	            if(system_registrations > 1)
	            	response(500, __("lang_response_register_false"));
	            
	            if(!isset($request["name"], $request["email"], $request["timezone"], $request["country"], $request["password"], $request["cpassword"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
	            	response(500, __("lang_response_name_short"));

	            if(!$this->sanitize->isEmail($request["email"]))
	            	response(500, __("lang_response_invalid_email"));

	            if(!in_array($request["timezone"], $this->timezones->generate()))
	            	response(500, __("lang_response_invalid"));

	            if(!array_key_exists($request["country"], \CountryCodes::get("alpha2", "country")))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["password"], 5))
	            	response(500, __("lang_response_password_short"));

	            if($request["password"] !== $request["cpassword"])
	            	response(500, __("lang_response_password_notmatch"));

	            if(system_recaptcha < 2):
					if(empty(system_recaptcha_key) || empty(system_recaptcha_secret))
	            		response(500, __("lang_recaptcha_add_keys"));

					if(!isset($request["g-recaptcha-response"]))
						response(500, __("lang_response_invalid"));
				endif;

	            if(system_recaptcha < 2):
		            try {
		            	$recaptcha = json_decode($this->guzzle->get("https://www.recaptcha.net/recaptcha/api/siteverify?secret=" . system_recaptcha_secret . "&response={$request["g-recaptcha-response"]}", [
			                "http_errors" => false
			            ])->getBody()->getContents());
		            } catch(Exception $e){
		            	response(500, __("lang_response_went_wrong"));
		            }
		        endif;

	            if(system_recaptcha < 2 && !$recaptcha->success)
					response(500, __("lang_response_solve_captcha"));

            	$filtered = [
            		"role" => 1,
            		"name" => $request["name"],
            		"email" => $this->sanitize->email($request["email"]),
            		"credits" => 0,
            		"earnings" => 0,
            		"language" => system_default_lang,
            		"suspended" => 0,
            		"providers" => false,
            		"alertsound" => 1,
            		"timezone" => strtolower($request["timezone"]),
            		"formatting" => false,
            		"country" => $request["country"],
            		"partner" => 2,
            		"confirmed" => 2,
            		"password" => password_hash($request["password"], PASSWORD_DEFAULT)
            	];

            	if($this->system->checkEmail($filtered["email"]) < 1):
            		$create = $this->system->create("users", $filtered);

            		if($create):
            			if(!empty(system_mailing_address) && in_array("admin_new_user", explode(",", system_mailing_triggers))):
            				$mailingContent = <<<HTML
							<p>Hi there!</p>
							<p>This is to inform you that a new user with email <strong>{$filtered["email"]}</strong> have registered!</p> 
							HTML;

	            			$this->mail->send([
								"title" => system_site_name,
								"data" => [
									"subject" => mail_title("Admin Alert Message from " . system_site_name . "!"),
									"content" => $mailingContent
								]
							], system_mailing_address, "_mail/default.tpl", $this->smarty);
	            		endif;

            			if(in_array("register_confirm", explode(",", system_mailing_triggers))):
            				$this->cache->container("register.confirm", true);

            				$confirmHash = $this->hash->encode(rand(0, time()), system_token);
            				$confirmLink = site_url("dashboard/authenticate/register", true) . "/{$confirmHash}";

            				$this->cache->set($confirmHash, $create, 600);

            				$mailContent = <<<HTML
							<p>{$GLOBALS["__"]("lang_requests_loginnotverified_emaildesc1")}</p>
							<p>{$GLOBALS["__"]("lang_requests_loginnotverified_emaildesc2")}</p> 
							<p><a href="{$confirmLink}">{$confirmLink}</a></p>
							<p>{$GLOBALS["__"]("lang_requests_loginnotverified_emaildesc3")}</p>
							HTML;

            				if($this->mail->send([
								"title" => system_site_name,
								"data" => [
									"subject" => mail_title(___(__("lang_requests_loginnotverified_emailtitle"), [system_site_name])),
									"content" => $mailContent
								]
							], $filtered["email"], "_mail/default.tpl", $this->smarty)):
            					response(200, ___(__("lang_requests_loginnotverified_response2"), ["<strong>{$filtered["email"]}</strong>"]));
	            			else:
	            				response(500, __("lang_requests_loginnotverified_responseunable"));
	            			endif;
            			else:
            				$this->system->update($create, false, "users", [
            					"confirmed" => 1
            				]);

            				$this->session->set("logged", 
	            				$this->system->getUser($create)
	            			);

	            			$this->cache->container("system.users");
	    					$this->cache->clear();

							response(301, __("lang_response_register_success"));
            			endif;
            		else:
            			response(500, __("lang_response_went_wrong"));
            		endif;
            	else:
            		response(500, __("lang_response_email_unavailable"));
            	endif;

				break;
			case "logout":
				if(!$this->session->has("logged"))
	            	response(302);

	            if($this->session->destroy())
	            	response(200, __("lang_response_loggedout_success"));
	            else
	            	response(500, __("lang_response_went_wrong"));

				break;
			case "download":
				$type = $this->sanitize->string($this->url->segment(5));

				if(!in_array($type, ["gateway", "dashboard"]))
					response(500, __("lang_response_invalid"));

				if($type == "gateway"):
					$gateway = strtolower(system_package_name . ".apk");

					if($this->file->exists("uploads/builder/{$gateway}")):
						response(200, __("lang_response_gateway_available"), [
							"link" => site_url . "/uploads/builder/{$gateway}?_=" . time()
						]);
					else:
						response(500, __("lang_response_gateway_unavailable"));
					endif;
				else:
					response(500, __("lang_response_invalid"));
				endif;

				break;
			case "livechat":
				if(!$this->session->has("logged"))
	            	response(302);

	            if(system_livechat < 2):
	            	response(200, false, logged_name);
				else:
					response(202);
				endif;			
	            
				break;
			case "support":
				if(!$this->session->has("logged"))
	            	response(302);

	            if(!super_admin)
					response(500, __("lang_response_no_permission"));

	            response(200, false, titansys_api . "/authenticate?code=" . system_purchase_code);
	            
				break;
			case "regenerate":
				if(!$this->session->has("logged"))
	            	response(302);

	            if(!super_admin)
					response(500, __("lang_response_no_permission"));

				$token = hash("sha256", password_hash(uniqid(time(), true), PASSWORD_DEFAULT));

				$env = explode("\n", $this->file->get("system/configurations/cc_env.inc"));

				$newEnv = "";

				foreach($env as $row):
					$line = explode("<=>", trim($row));

					if($line[0] == "systoken"):
						$newEnv .= "{$line[0]}<=>{$token}\n";
					else:
						$newEnv .= "{$line[0]}<=>{$line[1]}\n";
					endif;
				endforeach;

				$this->file->put("system/configurations/cc_env.inc", trim($newEnv));

	            response(200, __("lang_response_token_generated"), $token);
	            
				break;
			case "trigger":
				if(!$this->session->has("logged"))
	            	response(302);

	            $request = $this->sanitize->array($_POST);

	            if(!isset($request["secret"], $request["type"], $request["url"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->isUrl($request["url"]))
	            	response(500, __("lang_response_invalid"));

	            try {
		            $phoneSample = $this->phone->getExampleNumber(logged_country, Brick\PhoneNumber\PhoneNumberType::MOBILE);
		        } catch(Exception $e){
		            $phoneSample = "+63123456789";
		        }

	            switch($request["type"]):
	            	case "sms":
	            		$this->guzzle->post($request["url"], [
				            "form_params" => [
				            	"secret" => $request["secret"],
				            	"type" => "sms",
				            	"data" => [
			            			"id" => rand(1, 5000), 
							        "rid" => rand(1, 10000),
							        "sim" => rand(1, 2), 
							        "device" => "00000000-0000-0000-d57d-f30cb6a89289", 
							        "phone" => $phoneSample, 
							        "message" => "Hello World!", 
							        "timestamp" => time() 
				            	]
				            ],
				            "allow_redirects" => true,
				            "http_errors" => false
				        ]);

	            		break;
	            	case "whatsapp":
	            		$this->guzzle->post($request["url"], [
				            "form_params" => [
				            	"secret" => $request["secret"],
				            	"type" => "whatsapp",
				            	"data" => [
			            			"id" => rand(1, 5000), 
							        "wid" => $phoneSample, 
							        "phone" => $phoneSample, 
							        "message" => "Hello World!", 
							        "timestamp" => time() 
				            	]
				            ],
				            "allow_redirects" => true,
				            "http_errors" => false
				        ]);

	            		break;
	            	case "ussd":
	            		$this->guzzle->post($request["url"], [
				            "form_params" => [
				            	"secret" => $request["secret"],
				            	"type" => "ussd",
				            	"data" => [
			            			"id" => rand(1, 5000),
							        "sim" => rand(1, 2), 
							        "device" => "00000000-0000-0000-d57d-f30cb6a89289", 
							        "code" => "*143#", 
							        "response" => "Sorry! You are not allowed to use this service.", 
							        "timestamp" => time()  
				            	]
				            ],
				            "allow_redirects" => true,
				            "http_errors" => false
				        ]);

	            		break;
	            	case "notification":
	            		$this->guzzle->post($request["url"], [
				            "form_params" => [
				            	"secret" => $request["secret"],
				            	"type" => "notification",
				            	"data" => [
			            			"id" => rand(1, 5000), 
							        "device" => "00000000-0000-0000-d57d-f30cb6a89289",
							        "package" => "com.facebook.katana",
							        "title" => "Someone commented on your post!", 
							        "content" => "Someone commented on your post!", 
							        "timestamp" => time()
				            	]
				            ],
				            "allow_redirects" => true,
				            "http_errors" => false
				        ]);

	            		break;
	            	default:
	            		response(500, __("lang_response_invalid"));
	            endswitch;

	            response(200, __("lang_response_trigger_done"));

				break;
			default:
				response(500, __("lang_response_invalid"));
		endswitch;
	}

	public function build()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
        	response(302);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        if(!super_admin)
			response(500, __("lang_response_no_permission"));

        if(!$this->sanitize->length(system_purchase_code, 5))
        	response(500, __("lang_response_pcode_empty"));

        if(!$this->file->exists("system/storage/temporary/google.json"))
        	response(500, __("lang_response_build_gservicesfile"));

        if(!$this->file->exists("system/storage/temporary/firebase.json"))
        	response(500, __("lang_response_build_fcredentialsfile"));

        if(!$this->file->exists("uploads/builder/icon.png"))
        	response(500, __("lang_response_upload_appicon"));

        if(!$this->file->exists("uploads/builder/logo.png"))
        	response(500, __("lang_response_upload_applogo"));

        if(!$this->file->exists("uploads/builder/logo-login.png"))
        	response(500, __("lang_response_build_loginlogo"));

        if(!$this->file->exists("uploads/builder/splash.png"))
        	response(500, __("lang_response_upload_appsplash"));

        try {
        	$build = $this->guzzle->post(titansys_api . "/zender/builder", [
	            "form_params" => [
	            	"token" => system_token,
	            	"code" => system_purchase_code,
	            	"site_url" => site_url(false, true),
	            	"package_name" => strtolower(system_package_name),
	            	"build_email" => system_build_email,
	            	"app_name" => system_app_name,
	            	"app_desc" => system_app_desc,
	            	"app_color" => system_app_color,
	            	"apk_version" => system_apk_version,
	            	"app_icon_remote" => system_app_icon_remote,
	            	"app_splash_remote" => system_app_splash_remote,
	            	"app_logo_remote" => system_app_logo_remote,
	            	"app_loginlogo_remote" => system_app_loginlogo_remote,
	            	"app_js" => system_app_js,
	            	"app_css" => system_app_css,
	            	"app_layout" => system_app_layout,
	            	"google_services" => $this->file->get("system/storage/temporary/google.json")
	            ],
	            "allow_redirects" => true,
	            "http_errors" => false
	        ]);

	        $response = json_decode($build->getBody()->getContents());

	        if($response->status == 200):
	        	$this->file->delete("uploads/builder/" . strtolower(system_package_name . ".apk"));
	        endif;

	        response($response->status == 200 ? 200 : 500, $response->message);
        } catch(Exception $e){
        	response(500, __("lang_response_buildserver_false"));
        }
	}

	public function languages()
	{
		$this->header->allow(site_url);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        $type = $this->sanitize->string($this->url->segment(4));

        if($type == "change"):
        	$language = $this->sanitize->string($this->url->segment(5));

			if(!$this->sanitize->isInt($language))
				response(500, __("lang_response_invalid"));

	        if($this->system->checkLanguage($language) < 1)
	        	response(500, __("lang_response_invalid"));

			if(logged_id):
				if($this->system->update(logged_id, false, "users", [
					"language" => $language
				])):
					$this->session->set("logged", $this->system->getUser(logged_id));

					$this->fcm->send($this->hash->encode(logged_id, system_token), [
				    	"type" => "language"
				    ]);
				else:
					response(500, __("lang_response_went_wrong"));
				endif;
			else:
				$this->session->set("language", $language);
			endif;

			response(200, __("lang_response_lang_changed"));
        else:
        	$languages = $this->system->getLanguages();

        	$languageIndex = 1;
			$languageArray = [];

	        foreach($languages as $language):
	        	if($languageIndex < 4):
	        		$languageArray[] = <<<HTML
					<li>
					    <a href="#" data-mfb-label="{$language["name"]}" class="mfb-component__button--child" zender-language="{$language["id"]}">
					        <i class="mfb-component__child-icon flag-icon flag-icon-{$language["iso"]}"></i>
					    </a>
					</li>
					HTML;
				endif;
				
				$languageIndex++;
	        endforeach;

	        if(count($languages) > 3):
	        	$label = __("lang_all_languages");

	        	$languageArray[] = <<<HTML
				<li>
				    <a href="#" data-mfb-label="{$label}" class="mfb-component__button--child bg-dark" zender-toggle="zender.languages">
				        <i class="mfb-component__child-icon la la-braille la-lg text-white more-lang"></i>
				    </a>
				</li>
				HTML;
	        endif;
			
	        response(200, false, $languageArray);
        endif;
	}

	public function echo()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
            response(401);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        $type = $this->sanitize->string($this->url->segment(4));
        $request = $this->sanitize->array($_GET);

        if($type == "reload"):
        	$this->session->set("logged", 
				$this->system->getUser(logged_id)
			);

			response(200);
		elseif($type == "offline"):
			if(!isset($request["id"]))
				response(500);

        	$device = $this->system->getDevice(false, $request["id"], "sid");

        	if($device):
	        	if($this->system->updateOffline($request["id"])):
	        		response(200, false, [
	        			"device" => $device["id"]
	        		]);
	        	endif;
        	else:
        		response(500);
        	endif;
		elseif($type == "online"):
			if(!isset($request["did"]))
				response(500);

			$device = $this->system->getDevice(false, $request["did"], "global");

        	if($device):
	        	if($this->system->updateOnline($request["did"])):
	        		response(200, false, [
	        			"device" => $device["id"]
	        		]);
	        	endif;
        	else:
        		response(500);
        	endif;
		else:
			try {
				$this->echo->_cache = $this->cache;
				$this->echo->_guzzle = $this->guzzle;

				$echoToken = $this->echo->token();
			} catch(Exception $e){
				response(500);
			}

			if($echoToken):
		        response(200, false, [
		        	"id" => logged_id,
		        	"hash" => logged_hash,
		        	"token" => $echoToken
		        ]);
		    else:
		    	response(401);
		    endif;
		endif;
	}

	public function whatsapp()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
			response(401);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

		$type = $this->sanitize->string($this->url->segment(4));

		if(!in_array($type, ["link"]))
			response(500, __("lang_response_invalid"));

		if($type == "link"):
			$subscription = set_subscription(
	            $this->system->checkSubscription(logged_id), 
	            $this->system->getSubscription(false, logged_id), 
	            $this->system->getSubscription(false, false, true)
	        );

	        if(empty($subscription))
	            response(500, __("lang_response_whatsapp_notprem"));

	        if(limitation($subscription["wa_account_limit"], $this->system->countWaAccounts(logged_id)))
				response(500, __("lang_response_whatsapp_maxaccounts"));

			if(empty(system_wa_server) && empty(system_wa_port))
				response(500, __("lang_requests_whatsapp_notavail"));

			$this->wa->_guzzle = $this->guzzle;

			if($this->wa->check()):
				$qrResult = $this->wa->create(logged_id, logged_hash);

				if($qrResult):
					response(200, false, $qrResult);
				else:
					response(500, __("lang_requests_whatsapp_unablegenqr"));
				endif;
			else:
				response(500, __("lang_response_whatsapp_noconnectserver"));
			endif;
		else:
			response(500, __("lang_response_invalid"));
		endif;
	}

	public function chart()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
            response(401);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        $type = $this->sanitize->string($this->url->segment(4));

		switch($type):
			case "dashboard.messages":
				$this->cache->container("user." . logged_hash, true);

				if(!$this->cache->has("statistics.messages")):
					$sent = []; 
					$received = [];
					$wa_sent = [];
					$wa_received = [];

					foreach($this->system->getStatisticsSent(logged_id) as $key => $value):
						$sent[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					foreach($this->system->getStatisticsReceived(logged_id) as $key => $value):
						$received[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					foreach($this->system->getStatisticsSent(logged_id, true) as $key => $value):
						$wa_sent[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					foreach($this->system->getStatisticsReceived(logged_id, true) as $key => $value):
						$wa_received[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					$series = [
						"sent" => $sent,
						"received" => $received,
						"wa_sent" => $wa_sent,
						"wa_received" => $wa_received
					];

					$this->cache->set("statistics.messages", $series, 3600);
				endif;

				$chart = $this->cache->get("statistics.messages");

				$vars = [
					"series" => [
						[
							"name" => strtoupper(__("lang_response_chart_dashsmssent")),
							"data" => $chart["sent"]
						],
						[
							"name" => strtoupper(__("lang_response_chart_dashsmsreceive")),
							"data" => $chart["received"]
						],
						[
							"name" => strtoupper(__("lang_response_chart_dashwasent")),
							"data" => $chart["wa_sent"]
						],
						[
							"name" => strtoupper(__("lang_response_chart_dashwareceive")),
							"data" => $chart["wa_received"]
						]
					],
					"colors" => [
						"#1ccf13", 
						"#1099da",
						"#fe9431",
						"#e82753"
					]
				];

				break;
			case "dashboard.events":
				$this->cache->container("user." . logged_hash, true);

				if(!$this->cache->has("statistics.events")):
					$webhook = []; 
					$actions = [];

					foreach($this->system->getStatisticsEvents(logged_id, 1) as $key => $value):
						$webhook[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					foreach($this->system->getStatisticsEvents(logged_id, 2) as $key => $value):
						$actions[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					$series = [
						"webhook" => $webhook,
						"actions" => $actions
					];

					$this->cache->set("statistics.events", $series, 3600);
				endif;

				$chart = $this->cache->get("statistics.events");

				$vars = [
					"series" => [
						[
							"name" => strtoupper(__("lang_response_chart_dashwebhooks")),
							"data" => $chart["webhook"]
						],
						[
							"name" => strtoupper(__("lang_response_chart_dashactions")),
							"data" => $chart["actions"]
						]
					],
					"colors" => [
						"#1ccf13", 
						"#1099da"
					]
				];

				break;
			case "dashboard.utilities":
				$this->cache->container("user." . logged_hash, true);

				if(!$this->cache->has("statistics.utilities")):
					$ussd = []; 
					$notifications = [];

					foreach($this->system->getStatisticsUtilities(logged_id, 1) as $key => $value):
						$ussd[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					foreach($this->system->getStatisticsUtilities(logged_id, 2) as $key => $value):
						$notifications[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					$series = [
						"ussd" => $ussd,
						"notifications" => $notifications
					];

					$this->cache->set("statistics.utilities", $series, 3600);
				endif;

				$chart = $this->cache->get("statistics.utilities");

				$vars = [
					"series" => [
						[
							"name" => strtoupper(__("lang_response_chart_dashussd")),
							"data" => $chart["ussd"]
						],
						[
							"name" => strtoupper(__("lang_response_chart_dashnoti")),
							"data" => $chart["notifications"]
						]
					],
					"colors" => [
						"#1ccf13", 
						"#1099da"
					]
				];

				break;
			case "admin.countries":
				$this->cache->container("system.statistics", true);

				if(!$this->cache->has("visitors.countries")):
					$series = [];
					
					foreach($this->system->getStatisticsVisitors("country") as $key => $value):
						unset($country);

						foreach($value as $vkey => $vvalue){
							$country[] = [
								(int)("{$vkey}000"),
								count($vvalue)
							];
						}

						$series[] = [
							"name" => strtoupper($key),
							"data" => $country
						];
					endforeach;

					$this->cache->set("visitors.countries", $series, 3600);
				endif;

				$vars = [
					"series" => $this->cache->get("visitors.countries")
				];

				break;
			case "admin.browsers":
				$this->cache->container("system.statistics", true);

				if(!$this->cache->has("visitors.browsers")):
					$series = [];
					
					foreach($this->system->getStatisticsVisitors("browser") as $key => $value):
						unset($browser);

						foreach($value as $vkey => $vvalue){
							$browser[] = [
								(int)("{$vkey}000"),
								count($vvalue)
							];
						}

						$series[] = [
							"name" => strtoupper($key),
							"data" => $browser
						];
					endforeach;

					$this->cache->set("visitors.browsers", $series, 3600);
				endif;

				$vars = [
					"series" => $this->cache->get("visitors.browsers")
				];

				break;
			case "admin.os":
				$this->cache->container("system.statistics", true);

				if(!$this->cache->has("visitors.os")):
					$series = [];
					
					foreach($this->system->getStatisticsVisitors("os") as $key => $value):
						unset($os);

						foreach($value as $vkey => $vvalue){
							$os[] = [
								(int)("{$vkey}000"),
								count($vvalue)
							];
						}

						$series[] = [
							"name" => strtoupper($key),
							"data" => $os
						];
					endforeach;

					$this->cache->set("visitors.os", $series, 3600);
				endif;

				$vars = [
					"series" => $this->cache->get("visitors.os")
				];

				break;
			case "admin.messages":
				if(!is_admin)
					response(500, __("lang_response_no_permission"));

				$this->cache->container("system.statistics", true);

				if(!$this->cache->has("messages")):
					$sent = []; 
					$received = [];
					$wa_sent = [];
					$wa_received = [];

					foreach($this->system->getStatisticsSent(false, false, true) as $key => $value):
						$sent[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					foreach($this->system->getStatisticsReceived(false, false, true) as $key => $value):
						$received[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					foreach($this->system->getStatisticsSent(false, true, true) as $key => $value):
						$wa_sent[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					foreach($this->system->getStatisticsReceived(false, true, true) as $key => $value):
						$wa_received[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					$series = [
						"sent" => $sent,
						"received" => $received,
						"wa_sent" => $wa_sent,
						"wa_received" => $wa_received
					];

					$this->cache->set("messages", $series, 3600);
				endif;

				$chart = $this->cache->get("messages");

				$vars = [
					"series" => [
						[
							"name" => strtoupper(__("lang_requests_charts_smssent")),
							"data" => $chart["sent"]
						],
						[
							"name" => strtoupper(__("lang_requests_charts_smsreceived")),
							"data" => $chart["received"]
						],
						[
							"name" => strtoupper(__("lang_requests_charts_wasent")),
							"data" => $chart["wa_sent"]
						],
						[
							"name" => strtoupper(__("lang_requests_charts_wareceived")),
							"data" => $chart["wa_received"]
						]
					],
					"colors" => [
						"#1ccf13", 
						"#1099da",
						"#fe9431",
						"#e82753"
					]
				];

				break;
			case "admin.utilities":
				if(!is_admin)
					response(500, __("lang_response_no_permission"));

				$this->cache->container("system.statistics", true);

				if(!$this->cache->has("utilities")):
					$ussd = []; 
					$notifications = [];

					foreach($this->system->getStatisticsUtilities(false, 1, true) as $key => $value):
						$ussd[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;
					
					foreach($this->system->getStatisticsUtilities(false, 2, true) as $key => $value):
						$notifications[] = [
							(int)("{$key}000"),
							count($value)
						];
					endforeach;

					$series = [
						"ussd" => $ussd,
						"notifications" => $notifications
					];

					$this->cache->set("utilities", $series, 3600);
				endif;

				$chart = $this->cache->get("utilities");

				$vars = [
					"series" => [
						[
							"name" => strtoupper(__("lang_requests_charts_ussd")),
							"data" => $chart["ussd"]
						],
						[
							"name" => strtoupper(__("lang_requests_charts_notifications")),
							"data" => $chart["notifications"]
						]
					],
					"colors" => [
						"#1ccf13", 
						"#1099da"
					]
				];

				break;
			case "admin.subscriptions":
				if(!permission("manage_transactions"))
					response(500, __("lang_response_no_permission"));

				$this->cache->container("system.statistics", true);

				if(!$this->cache->has("subscriptions")):
					$series = [];
					
					foreach($this->system->getStatisticsEarnings() as $key => $value):
						unset($earnings);

						foreach($value as $vkey => $vvalue){
							$earnings[] = [
								(int)("{$vkey}000"),
								array_sum($vvalue)
							];
						}

						$series[] = [
							"name" => strtoupper($key),
							"data" => $earnings
						];
					endforeach;

					$this->cache->set("subscriptions", $series, 3600);
				endif;

				$vars = [
					"series" => $this->cache->get("subscriptions")
				];

				break;
			case "admin.credits":
				if(!permission("manage_transactions"))
					response(500, __("lang_response_no_permission"));

				$this->cache->container("system.statistics", true);

				if(!$this->cache->has("credits")):
					$credits = []; 

					foreach($this->system->getStatisticsEarnings(2) as $key => $value):
						$credits[] = [
							(int)("{$key}000"),
							array_sum($value)
						];
					endforeach;

					$series = [
						"credits" => $credits
					];

					$this->cache->set("credits", $series, 3600);
				endif;

				$chart = $this->cache->get("credits");

				$vars = [
					"series" => [
						[
							"name" => strtoupper(__("lang_requests_charts_credits")),
							"data" => $chart["credits"]
						]
					],
					"colors" => [
						"#1ccf13"
					]
				];

				break;
			case "admin.commissions":
				if(!permission("manage_transactions"))
					response(500, __("lang_response_no_permission"));
				
				$this->cache->container("system.statistics", true);

				if(!$this->cache->has("commissions")):
					$commissions = []; 

					foreach($this->system->getStatisticsEarnings(3) as $key => $value):
						$commissions[] = [
							(int)("{$key}000"),
							array_sum($value)
						];
					endforeach;

					$series = [
						"commissions" => $commissions
					];

					$this->cache->set("commissions", $series, 3600);
				endif;

				$chart = $this->cache->get("commissions");

				$vars = [
					"series" => [
						[
							"name" => __("lang_requests_charts_commisionstitle"),
							"data" => $chart["commissions"]
						]
					],
					"colors" => [
						"#1ccf13"
					]
				];

				break;
			default:
				response(500, __("lang_response_invalid"));
		endswitch;

		response(200, false, [
			"vars" => $vars
		]);
	}

	public function visitors()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
			response(401);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        $visitor = new Lablnet\UserInfo();

        try {
        	$geoip = json_decode($this->guzzle->get(titansys_api . "/geoip?code=" . system_purchase_code . "&ip={$visitor->ip()}", [
                "allow_redirects" => true,
                "http_errors" => false
            ])->getBody()->getContents(), true);
        } catch(Exception $e){
        	response(500, __("lang_response_went_wrong"));
        }

        $info = [
        	"country" => isset($geoip["data"]["country"]) ? $geoip["data"]["country"] : "Unknown",
        	"browser" => $visitor->browser()["browser"],
        	"os" => $visitor->operatingSystem()
        ];

        if($this->system->create("visitors", $info)):
        	$this->cache->container("system.statistics", true);
        	
        	response(200);
        else:
        	response(500);
        endif;
	}

	public function autocomplete()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
            response(401);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        $request = $this->sanitize->array($_POST);
        $type = $this->sanitize->string($this->url->segment(4));

		switch($type){
			case "contacts":
				$this->cache->container("autocomplete.{$type}." . logged_hash);

				if($this->cache->empty()):
					$contacts = $this->system->getContacts(logged_id);

					if(!empty($contacts)):
						foreach($contacts as $contact):
							$autocomplete[] = [
								"value" => "{$contact["name"]} ({$contact["phone"]})",
								"data" => $contact["phone"]
							];
						endforeach;
					else:
						$autocomplete = [];
					endif;

					$this->cache->setArray($autocomplete);
				endif;

				response(200, false, $this->cache->getAll());

				break;
			case "wa.contacts":
				$this->cache->container("autocomplete.contacts." . logged_hash);

				if($this->cache->empty()):
					$contacts = $this->system->getContacts(logged_id);

					if(!empty($contacts)):
						foreach($contacts as $contact):
							$autocomplete[] = [
								"value" => "{$contact["name"]} ({$contact["phone"]})",
								"data" => $contact["phone"]
							];
						endforeach;
					else:
						$autocomplete = [];
					endif;

					$this->cache->setArray($autocomplete);
				endif;

				$autocomplete = $this->cache->getAll();

				if(isset($request["account"])):
					$account = $this->system->getWaAccount(logged_id, $request["account"], "id");

					if($account):
						$waGroups = $this->system->getWaGroups(logged_id, $account["wid"]);

						if(!empty($waGroups)):
							foreach($waGroups as $group):
								$autocomplete[] = [
									"value" => "{$group["name"]} ({$group["gid"]})",
									"data" => $group["gid"]
								];
							endforeach;
						endif;
					endif;
				endif;

				response(200, false, $autocomplete);

				break;
			default:
				response(500, __("lang_response_invalid"));
		}
	}

	public function resend()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
			response(401);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

		$type = $this->sanitize->string($this->url->segment(4));
		$id = $this->sanitize->string($this->url->segment(5));

		if(!$this->sanitize->isInt($id))
			response(500, __("lang_response_invalid"));

		switch($type):
			case "sms":
				try {
					if($this->system->update($id, false, "sent", [
						"status" => 1
					])):
						$sent = $this->system->getSent($id);

						$this->fcm->send(md5(logged_id . $sent["did"]), [
					    	"type" => "resend"
					    ]);
					endif;

					response(200, __("lang_response_resend_queued"));
				} catch(Exception $e){
					response(500, __("lang_requests_resend_devicenotexist"));
				}

				break;
			default:
				response(500, __("lang_response_invalid"));
		endswitch;
	}

	public function payout()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
			response(401);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

		$type = $this->sanitize->string($this->url->segment(4));
		$id = $this->sanitize->string($this->url->segment(5));

		$payout = $this->system->getPayout($id);
		$user = $this->system->getUser($payout["uid"]);

		set_language($user["language"]);

		if(!permission("manage_payouts"))
			response(500, __("lang_response_no_permission"));

		if(!$this->sanitize->isInt($id))
			response(500, __("lang_response_invalid"));

		if($this->system->checkPayout($id) < 1)
			response(500, __("lang_response_invalid"));

		if($this->system->getPartnership(logged_id) > 1)
			response(500, __("lang_response_invalid"));

		switch($type):
			case "confirm":
				$this->mail->send([
					"title" => system_site_name,
					"data" => [
						"subject" => mail_title(__("lang_mail_title_payoutapproved")),
						"payout" => $payout
					]
				], $user["email"], "_mail/payout_paid.tpl", $this->smarty);

				$this->system->delete(false, $id, "payouts");

				response(200, __("lang_response_payout_confirmed"));

				break;
			case "reject":
				$this->system->earnings($user["id"], "increase", $payout["amount"]);

				$this->mail->send([
					"title" => system_site_name,
					"data" => [
						"subject" => mail_title(__("lang_mail_title_payoutrejected")),
						"payout" => $payout
					]
				], $user["email"], "_mail/payout_rejected.tpl", $this->smarty);

				$this->system->delete(false, $id, "payouts");

				response(200, __("lang_response_payout_rejected"));

				break;
			default:
				response(500, __("lang_response_invalid"));
		endswitch;
	}

	public function translate()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
			response(401);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        $request = $this->sanitize->array($_POST);
		$from = $this->sanitize->string($this->url->segment(4));
		$to = $this->sanitize->string($this->url->segment(5));

		if(!isset($request["message"]))
			response(500, __("lang_response_invalid"));

		if(!$this->sanitize->length($request["message"], 3))
			response(500, __("lang_response_translate_msgshort"));

		if(empty($from) || empty($to))
			response(500, __("lang_response_invalid"));

		try {
			$message = (new \Statickidz\GoogleTranslate)->translate($from, $to, $request["message"]);
		} catch(Exception $e){
			response(500, __("lang_response_went_wrong"));
		}

		response(200, false, $message);
	}

	public function clear()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
			response(401);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

		$type = $this->sanitize->string($this->url->segment(4));

		switch($type):
			case "token":
				if(!is_admin)
					response(500, __("lang_response_no_permission"));

				$this->cache->container("system.echo", true);

				$oldToken = $this->cache->get("token");

				try {
					$this->echo->_cache = $this->cache;
					$this->echo->_guzzle = $this->guzzle;

					$echoToken = $this->echo->token($oldToken);
				} catch(Exception $e){
					response(500, __("lang_response_titanecho_invaltoken"));
				}

				response(200, ___(__("lang_requests_token_refreshecho"), ["TitanEcho"]));

				break;
			case "cache":
				if(!is_admin)
					response(500, __("lang_response_no_permission"));

				$this->cache->container("system.echo", true);

				$oldToken = $this->cache->get("token");

				try {
					rmrf("system/storage/cache");
					mkdir("system/storage/cache");
				} catch(Exception $e){
					// Ignore
				}

				try {
					$this->echo->_cache = $this->cache;
					$this->echo->_guzzle = $this->guzzle;

					$echoToken = $this->echo->token($oldToken);
				} catch(Exception $e){
					response(500, __("lang_response_titanecho_invaltoken"));
				}

				response(200, __("lang_response_clear_systemcleared"));

				break;
			case "ussd":
				if($this->system->clearUssd(logged_id)):
					$this->fcm->send($this->hash->encode(logged_id, system_token), [
				    	"type" => "clear_ussd",
				    	"uid" => (int) logged_id
				    ]);
				endif;

				response(200, __("lang_response_clear_ussdcleared"));

				break;
			default:
				response(500, __("lang_response_invalid"));
		endswitch;
	}

	public function remote()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
			response(401);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        $request = $this->sanitize->array($_POST);
		$type = $this->sanitize->string($this->url->segment(4));

		switch($type):
			case "start_sms":
				if(!isset($request["cid"], $request["did"], $request["name"]))
					response(500, __("lang_response_invalid"));

				if($this->system->update($request["cid"], logged_id, "campaigns", [
					"status" => 1
				])):
					$this->fcm->send(md5(logged_id . $request["did"]), [
				    	"type" => "start_sms",
				    	"cid" => (int) $request["cid"],
				    	"name" => $request["name"]
				    ]);
				endif;

				response(200, __("lang_requests_remote_smscampaignresumed"));

				break;
			case "stop_sms":
				if(!isset($request["cid"], $request["did"], $request["name"]))
					response(500, __("lang_response_invalid"));

				if($this->system->update($request["cid"], logged_id, "campaigns", [
					"status" => 2
				])):
					$this->fcm->send(md5(logged_id . $request["did"]), [
				    	"type" => "stop_sms",
				    	"cid" => (int) $request["cid"],
				    	"name" => $request["name"]
				    ]);
				endif;

				response(200, __("lang_requests_remote_smscampaignpaused"));

				break;
			case "start_chats":
				if(!isset($request["cid"]))
					response(500, __("lang_response_invalid"));

				$this->wa->_guzzle = $this->guzzle;

				if($this->wa->check()):
					if($this->system->update($request["cid"], logged_id, "campaigns", [
						"status" => 1
					])):
						$campaign = $this->system->getWaCampaign(logged_id, $request["cid"], "id");

						try {
							$account = $this->system->getWaAccount(logged_id, $campaign["wid"], "wid");

							$this->wa->start_campaign($account["unique"], logged_hash, $request["cid"]);
						} catch(Exception $e){
							// Ignore
						}
					endif;
				else:
					response(500, __("lang_response_whatsapp_noconnectserver"));
				endif;

				response(200, __("lang_requests_remote_wacampaignresumed"));

				break;
			case "stop_chats":
				if(!isset($request["cid"]))
					response(500, __("lang_response_invalid"));

				$this->wa->_guzzle = $this->guzzle;

				if($this->wa->check()):
					if($this->system->update($request["cid"], logged_id, "campaigns", [
						"status" => 2
					])):
						$campaign = $this->system->getWaCampaign(logged_id, $request["cid"], "id");

						try {
							$account = $this->system->getWaAccount(logged_id, $campaign["wid"], "wid");

							$this->wa->stop_campaign($account["unique"], logged_hash, $request["cid"]);
						} catch(Exception $e){
							// Ignore
						}
					endif;
				else:

				endif;

				response(200, __("lang_requests_remote_wacampaignpaused"));

				break;
			default:
				response(500, __("lang_response_invalid"));
		endswitch;
	}

	public function reorder()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
			response(401);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        $request = $this->sanitize->array($_POST);
		$type = $this->sanitize->string($this->url->segment(4));

		if(!isset($request["rows"]) || !is_array($request["rows"]) || empty($request["rows"]))
			response(500, __("lang_response_invalid"));

		$orderIndex = 1;
		$languages = $this->system->getLanguages();

		foreach($request["rows"] as $row):
			$this->system->update($row, false, "languages", [
				"order" => $orderIndex
			]);

			$orderIndex++;
		endforeach;

		if(!empty($languages)):
			foreach($languages as $language):
				if(!in_array($language["id"], $request["rows"])):
					$this->system->update($language["id"], false, "languages", [
						"order" => $orderIndex
					]);

					$orderIndex++;
				endif;
			endforeach;
		endif;

		response(200, __("lang_response_reorder_languages"));
	}

	public function trash()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
			response(401);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        $request = $this->sanitize->array($_POST);
		$type = $this->sanitize->string($this->url->segment(4));

		if(!isset($request["rows"]) || !is_array($request["rows"]) || empty($request["rows"]))
			response(500, __("lang_response_invalid"));

		switch($type):
			case "sms.sent":
				foreach($request["rows"] as $row):
					$sent = $this->system->getSent(str_replace("#", "", $row));

					if($sent["status"] < 3):
						$this->fcm->send($this->hash->encode(logged_id, system_token), [
							"type" => "sms_delete",
							"id" => str_replace("#", "", $row)
						]);
					endif;

					$this->system->delete(logged_id, str_replace("#", "", $row), "sent");
				endforeach;

				break;
			case "sms.received":
				foreach($request["rows"] as $row):
					$received = $this->system->getMessageReceived(logged_id, str_replace("#", "", $row));

					if($this->system->checkDeleted($received["uid"], $received["rid"], $received["did"]) < 1):
						$this->system->create("deleted", [
							"rid" => $received["rid"],
							"uid" => $received["uid"],
							"did" => $received["did"]
						]);
					endif;

					$this->system->delete(logged_id, str_replace("#", "", $row), "received");
				endforeach;

				break;
			case "sms.scheduled":
				foreach($request["rows"] as $row):
					$this->system->delete(logged_id, str_replace("#", "", $row), "scheduled");
				endforeach;

				break;
			case "whatsapp.sent":
				foreach($request["rows"] as $row):
					$sent = $this->system->getWaSent(str_replace("#", "", $row));

					if($sent["status"] < 3):
						$this->wa->_guzzle = $this->guzzle;

						if($this->wa->check()):
							if($this->system->delete(logged_id, str_replace("#", "", $row), "wa_sent")):
								try {
									$account = $this->system->getWaAccount(logged_id, $sent["wid"], "wid");

									$this->wa->delete_chat($account["unique"], logged_hash, str_replace("#", "", $row));
								} catch(Exception $e){
									// Ignore
								}
							endif;
						endif;
					else:
						$this->system->delete(logged_id, str_replace("#", "", $row), "wa_sent");
					endif;
				endforeach;

				break;
			case "whatsapp.received":
				foreach($request["rows"] as $row):
					$this->system->delete(logged_id, str_replace("#", "", $row), "wa_received");
				endforeach;

				break;
			case "whatsapp.scheduled":
				foreach($request["rows"] as $row):
					$this->system->delete(logged_id, str_replace("#", "", $row), "wa_scheduled");
				endforeach;

				break;
			case "whatsapp.groups":
				foreach($request["rows"] as $row):
					$this->system->delete(logged_id, str_replace("#", "", $row), "wa_groups");
				endforeach;

				$this->cache->container("wa.contacts." . logged_hash);
				$this->cache->clear();

				break;
			case "android.ussd":
				foreach($request["rows"] as $row):
					$this->system->delete(logged_id, str_replace("#", "", $row), "ussd");
				endforeach;

				break;
			case "android.notifications":
				foreach($request["rows"] as $row):
					$this->system->delete(logged_id, str_replace("#", "", $row), "notifications");
				endforeach;

				break;
			case "contacts.saved":
				foreach($request["rows"] as $row):
					$this->system->delete(logged_id, str_replace("#", "", $row), "contacts");
				endforeach;

				$this->cache->container("autocomplete.contacts." . logged_hash);
                $this->cache->clear();
                $this->cache->container("contacts." . logged_hash);
                $this->cache->clear();
                $this->cache->container("user." . logged_hash);
                $this->cache->clear();

				break;
			case "contacts.groups":
				foreach($request["rows"] as $row):
					$this->system->delete(logged_id, str_replace("#", "", $row), "groups");
				endforeach;

				break;
			case "contacts.unsubscribed":
				foreach($request["rows"] as $row):
					$this->system->delete(logged_id, str_replace("#", "", $row), "unsubscribed");
				endforeach;

				break;
			case "administration.vouchers":
				foreach($request["rows"] as $row):
					$this->system->delete(false, str_replace("#", "", $row), "vouchers");
				endforeach;

				break;
			case "administration.marketing":
				foreach($request["rows"] as $row):
					$this->system->delete(false, str_replace("#", "", $row), "marketing");
				endforeach;

				break;
			default:
				response(500, __("lang_response_invalid"));
		endswitch;

		response(200, __("lang_response_trash_selectedremoved"));
	}

	public function payment()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
			response(401);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

		$provider = $this->sanitize->string($this->url->segment(4));

		if(empty($provider))
			response(500, __("lang_response_invalid"));

		if(!in_array($provider, explode(",", system_providers)))
			response(500, __("lang_response_invalid"));

		switch($provider):
			case "mollie":
				$mollie = new \Mollie\Api\MollieApiClient();
				$mollie->setApiKey(system_mollie_key);

				$this->cache->container("system.payments");

				$item = $this->cache->get("order." . logged_hash);

				$paymentId = uniqid(time());

				if($item["type"] < 2):
					$payment = $mollie->payments->create([
					    "amount" => [
					        "currency" => "USD",
					        "value" => ($item["data"]["price"] * $item["data"]["duration"]) . ".00"
					    ],
					    "description" => $item["data"]["package"]["name"],
					    "redirectUrl" => site_url("payment/success/mollie/{$paymentId}", true),
					    "webhookUrl"  => site_url("payment/webhook/mollie/" . logged_hash, true),
					    "metadata" => [
				            "order_id" => $paymentId
				        ]
					]);
				else:
					$payment = $mollie->payments->create([
					    "amount" => [
					        "currency" => "USD",
					        "value" => "{$item["data"]["price"]}.00"
					    ],
					    "description" => "Credits",
					    "redirectUrl" => site_url("payment/success/mollie/{$paymentId}", true),
					    "webhookUrl"  => site_url("payment/webhook/mollie/" . logged_hash . "/{$paymentId}", true),
					    "metadata" => [
				            "order_id" => $paymentId
				        ]
					]);
				endif;

				response(200, __("lang_mollie_redirecting_page"), $payment->getCheckoutUrl());

				break;
			default:
				response(500, __("lang_response_invalid"));
		endswitch;
	}

	public function create()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
            response(302);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        $this->cache->container("system.plugins");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getPlugins());
        endif;

        set_plugins($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        $type = $this->sanitize->string($this->url->segment(4));
        $request = $this->sanitize->array($_POST, 
        	in_array($type, ["add.widget", "add.page", "add.mailer", "add.gateway"]) ? ["content"] : []
    	);

        switch($type):
        	case "redeem":
        		if(!isset($request["code"]))
        			response(500, __("lang_response_invalid"));

        		if($this->system->checkVoucher($request["code"]) > 0):
        			$voucher = $this->system->getVoucher($request["code"]);

        			$package = $this->system->getPackage($voucher["package"]);

    				if($this->system->checkSubscription(logged_id) > 0):
						$transaction = $this->system->create("transactions", [
							"uid" => logged_id,
							"pid" => $package["id"],
							"type" => 1,
							"price" => $package["price"],
							"currency" => system_currency,
							"duration" => $voucher["duration"],
							"provider" => "voucher"
						]);

						$filtered = [
							"pid" => $package["id"],
							"tid" => $transaction
						];

						$subscription = $this->system->getSubscription(false, logged_id);

						$this->system->update($subscription["sid"], logged_id, "subscriptions", $filtered);
					else:
						$transaction = $this->system->create("transactions", [
							"uid" => logged_id,
							"pid" => $package["id"],
							"type" => 3,
							"price" => $package["price"],
							"currency" => system_currency,
							"duration" => $voucher["duration"],
							"provider" => "Voucher"
						]);

						$filtered = [
							"uid" => logged_id,
							"pid" => $package["id"],
							"tid" => $transaction
						];

						$this->system->create("subscriptions", $filtered);
					endif;

					$this->mail->send([
						"title" => system_site_name,
						"data" => [
							"subject" => mail_title(__("lang_response_package_purchasedtitle")),
							"package" => $this->system->getPackage($package["id"]),
							"subscription" => $this->system->getSubscription(false, logged_id)
						]
					], logged_email, "_mail/subscribe.tpl", $this->smarty);

					if($this->system->delete(false, $voucher["id"], "vouchers")):
						if(!empty(system_mailing_address) && in_array("admin_voucher_redeem", explode(",", system_mailing_triggers))):
							$userEmail = logged_email;

							$mailingContent = <<<HTML
							<p>Hi there!</p>
							<p>This is to inform you that a voucher with <strong>{$package["name"]}</strong> package has redeemed by: <strong>{$userEmail}</strong></p> 
							HTML;

			    			$this->mail->send([
								"title" => system_site_name,
								"data" => [
									"subject" => mail_title("Admin Alert Message from " . system_site_name . "!"),
									"content" => $mailingContent
								]
							], system_mailing_address, "_mail/default.tpl", $this->smarty);
			    		endif;

						response(200, __("lang_response_voucher_redeemed"));
					else:
						response(500, __("lang_response_went_wrong"));
					endif;
        		else:
        			response(500, __("lang_invalid_voucher_code"));
        		endif;

        		break;
        	case "add.payout":
        		if(!isset($request["amount"], $request["provider"], $request["address"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->isInt($request["amount"]))
        			response(500, __("lang_response_payout_invalidamount"));

        		if(!in_array($request["provider"], ["paypal", "payoneer"]))
        			response(500, __("lang_response_invalid"));

        		if($request["amount"] < system_partner_minimum)
        			response(500, ___(__("lang_requests_create_payoutwithdrawalamountatleast"), [system_partner_minimum, system_currency]));

        		if($this->system->getEarnings(logged_id) < $request["amount"])
        			response(500, __("lang_response_payout_notenoughbal"));

        		if($this->system->checkPayout(logged_id, "uid") < 1):
    				$filtered = [
    					"uid" => logged_id,
    					"amount" => $request["amount"],
    					"currency" => system_currency,
    					"provider" => $request["provider"],
    					"address" => $request["address"],
    					"create_date" => date("Y-m-d H:i:s", time())
    				];

    				if($this->system->create("payouts", $filtered)):
    					$this->system->earnings(logged_id, "decrease", $request["amount"]);

    					if(!empty(system_mailing_address) && in_array("admin_payout_request", explode(",", system_mailing_triggers))):
							$userEmail = logged_email;

							$mailingContent = <<<HTML
							<p>Hi there!</p>
							<p>This is to inform you that a new payout request has been submitted by: <strong>{$userEmail}</strong></p> 
							HTML;

			    			$this->mail->send([
								"title" => system_site_name,
								"data" => [
									"subject" => mail_title("Admin Alert Message from " . system_site_name . "!"),
									"content" => $mailingContent
								]
							], system_mailing_address, "_mail/default.tpl", $this->smarty);
			    		endif;

    					response(200, __("lang_response_payout_reqreceive"));
    				else:
    					response(500, __("lang_response_went_wrong"));
    				endif;
        		else:
        			response(500, __("lang_response_payout_pendingreq"));
        		endif;

        		break;
        	case "sms.quick":
        		if(!isset($request["mode"], $request["shortener"], $request["phone"], $request["message"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->file->exists("system/storage/temporary/firebase.json")):
					response(500, __("lang_response_system_sysconfigerr"));
				endif;

				if(!$this->sanitize->isInt($request["mode"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["shortener"]))
					response(500, __("lang_response_invalid"));

				try {
				    $number = $this->phone->parse($request["phone"], logged_country);

				    $number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

				    if(!$number->isValidNumber())
						response(500, __("lang_response_invalid_number"));

					if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
						response(500, __("lang_response_invalid_number"));

					$phone = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
					$country = $number->getRegionCode();
				} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
					response(500, __("lang_response_invalid_number"));
				}

				if(!$this->sanitize->length($request["message"], system_message_min))
					response(500, __("lang_response_message_short"));

				if(system_message_max > 0):
					if($this->sanitize->length($request["message"], system_message_max, 2))
						response(500, __("lang_response_message_toolong"));
				endif;

        		if($request["mode"] < 2):
        			if(!isset(
	        			$request["device"],
	        			$request["sim"], 
	        			$request["priority"]
	        		))
        				response(500, __("lang_response_invalid"));

        			$subscription = set_subscription(
	                    $this->system->checkSubscription(logged_id), 
	                    $this->system->getSubscription(false, logged_id), 
	                    $this->system->getSubscription(false, false, true)
	                );

					if(empty($subscription))
						response(500, __("lang_response_package_nosubwarn"));

					if(!$this->sanitize->isInt($request["sim"]))
						response(500, __("lang_response_invalid"));

					if(!$this->sanitize->isInt($request["priority"]))
						response(500, __("lang_response_invalid"));

					if(limitation($subscription["send_limit"], $this->system->countQuota(logged_id, "sent")))
	    				response(500, __("lang_response_limitation_send"));

	    			if($this->system->checkDevice(logged_id, $request["device"], "did") < 1)
    					response(500, __("lang_response_invalid"));

	    			$device = $this->system->getDevice(logged_id, $request["device"], "did");

	    			if($device["limit_status"] < 2 && $this->system->checkSmsLimit(logged_id, $request["device"], $device["limit_interval"], $device["limit_number"])):
	    				$intervalType = $device["limit_interval"] < 2 ? __("lang_requests_sms_intervaltypedaily") : __("lang_requests_sms_intervaltypemonthly");
	    				response(500, ___(__("lang_requests_sms_intervaltypereachedmax"), [$intervalType]));
	    			endif;
        		else:
        			if(!isset($request["gateway"]))
        				response(500, __("lang_response_invalid"));

        			if($this->sanitize->isInt($request["gateway"])):
        				$gateways = $this->system->getGateways();

						if(!array_key_exists($request["gateway"], $gateways)):
							response(500, __("lang_response_invalid"));
						endif;

						if(!$this->file->exists("system/gateways/" . md5($request["gateway"]) . ".php"))
							response(500, __("lang_response_went_wrong"));

						try {
                            require "system/gateways/" . md5($request["gateway"]) . ".php";
                        } catch(Exception $e){
                            response(500, __("lang_requests_sms_encounteredgatewayerror"));
                        }
        			else:
        				$device = $this->system->getDevice(false, $request["gateway"], "global");

		    			if($device):
		    				if($device["uid"] == logged_id):
		    					response(500, __("lang_requests_sms_gatewaycannotsendowndevices"));
		    				endif;

		    				if($device["limit_status"] < 2 && $this->system->checkSmsLimit(logged_id, $request["gateway"], $device["limit_interval"], $device["limit_number"])):
			    				$intervalType = $device["limit_interval"] < 2 ? __("lang_requests_sms_intervaltypedaily") : __("lang_requests_sms_intervaltypemonthly");
			    				response(500, ___(__("lang_requests_sms_intervaltypereachedmax"), [$intervalType]));
			    			endif;

			    			if($device["global_device"] > 1):
			    				response(500, __("lang_response_invalid"));
			    			endif;
			    		else:
			    			response(500, __("lang_response_invalid"));
			    		endif;
        			endif;
        		endif;

				if($request["shortener"] > 0):
					if(!$this->file->exists("system/shorteners/" . md5($request["shortener"]) . ".php"))
						response(500, __("lang_response_went_wrong"));

					$messageLinks = (new VStelmakh\UrlHighlight\UrlHighlight)->getUrls($request["message"]);

					if(!empty($messageLinks)):
						try {
							require "system/shorteners/" . md5($request["shortener"]) . ".php";
						} catch(Exception $e){
							response(500, __("lang_requests_create_encounteredshortenererror"));
						}

						foreach($messageLinks as $key => $value):
							$shortLink = shortenUrl($value, $this);

							if($shortLink):
								$request["message"] = str_replace("{$value}", "{$shortLink}", $request["message"]);
							endif;
						endforeach;
					endif;
				endif;

				if($request["mode"] < 2):
					$this->system->create("sent", [
						"cid" => 0,
			        	"uid" => logged_id,
						"did" => $request["device"],
						"gateway" => 0,
						"sim" => $request["sim"] < 2 ? 1 : 2,
						"mode" => 1,
						"phone" => $phone,
						"message" => bjoernffm\Spintax\Parser::parse(footermark($subscription["footermark"], $request["message"], system_message_mark))->generate(),
						"status" => 1,
						"status_code" => false,
						"priority" => $request["priority"] < 2 ? 1 : 2,
						"api" => 2,
						"create_date" => date("Y-m-d H:i:s", time())
			        ]);
				else:
					$credits = $this->system->getCredits(logged_id);

					if($this->sanitize->isInt($request["gateway"])):
						$pricing = json_decode($gateways[$request["gateway"]]["pricing"], true);

						if(array_key_exists(strtolower($country), $pricing["countries"])):
							$price = $pricing["countries"][strtolower($country)];
						else:
							$price = $pricing["default"];
						endif;

						if($credits < $price)
				        	response(500, __("lang_response_message_notenoughcredsend"));

						$gateway = $gateways[$request["gateway"]];

						$message = bjoernffm\Spintax\Parser::parse($request["message"])->generate();

						$send = gatewaySend($request["phone"], $message, $this);

						if($send):
							$create = $this->system->create("sent", [
								"cid" => 0,
								"uid" => logged_id,
								"did" => false,
								"gateway" => $request["gateway"],
								"api" => 0,
								"sim" => 0,
								"mode" => 2,
								"priority" => 0,
								"phone" => $phone,
								"message" => $message,
								"status" => $gateway["callback"] < 2 ? 2 : 3,
								"status_code" => false,
								"create_date" => date("Y-m-d H:i:s", time())
							]);

							if($create):
								if($gateway["callback"] < 2):
									$this->cache->container("system.gateways");

									$this->cache->set("{$gateway["callback_id"]}.{$send}", $create);

									response(200, __("lang_response_message_queuedforsend"));
								else:
									$this->process->_sanitize = $this->sanitize;
									$this->process->_guzzle = $this->guzzle;
									$this->process->_lex = $this->lex;

									$hooks = $this->process->actionHooks(logged_id, 1, 1, $phone, $message, $this->device->getActions(logged_id, 1));

									if(!empty($hooks)):
										foreach($hooks as $hook):
											$this->system->create("events", [
												"uid" => logged_id,
												"type" => 2,
												"create_date" => date("Y-m-d H:i:s", time())
											]);
										endforeach;
									endif;

									$this->system->credits(logged_id, "decrease", $price);

									response(200, __("lang_response_message_sentsuccess"));
								endif;
							endif;
						else:
							response(500, __("lang_response_message_unablesendmesg"));
						endif;
					else:
						$currency = country($device["country"])->getCurrency()["iso_4217_code"];

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
					            	response(500, __("lang_response_went_wrong"));
					            endif;
					        } catch(Exception $e){
					            response(500, __("lang_response_went_wrong"));
					        }
					    endif;

					    $rates = $this->cache->get("exchange");

					    $base_rate = $rates["data"]["USD"] / $rates["data"][strtoupper($currency)];
				        $usd_price = ($base_rate * $device["rate"]) * $rates["data"]["USD"];
				        $final_price = (float) abs($usd_price * $rates["data"][strtoupper(system_currency)]);

				        if($credits < $final_price)
				        	response(500, __("lang_response_message_notenoughcredsend"));

						$slots = explode(",", $device["global_slots"]);

						$this->system->create("sent", [
							"cid" => 0,
				        	"uid" => logged_id,
							"did" => $request["gateway"],
							"gateway" => 0,
							"sim" => count($slots) > 1 ? rand(1, 2) : ($slots[0] < 2 ? 1 : 2),
							"mode" => 2,
							"phone" => $phone,
							"message" => bjoernffm\Spintax\Parser::parse($request["message"])->generate(),
							"status" => 1,
							"status_code" => false,
							"priority" => $device["global_priority"],
							"api" => 2,
							"create_date" => date("Y-m-d H:i:s", time())
				        ]);
					endif;
				endif;

				if($request["mode"] < 2):
					$this->fcm->send(md5(logged_id . $request["device"]), [
				    	"type" => "sms",
				    	"global" => 0,
				    	"currency" => "None",
				    	"rate" => (float) 0
				    ]);
				else:
					if(!$this->sanitize->isInt($request["gateway"])):
						$this->fcm->send(md5($device["uid"] . $request["gateway"]), [
					    	"type" => "sms",
					    	"global" => 1,
					    	"currency" => $currency,
					    	"rate" => (float) $device["rate"]
					    ]);
					endif;
				endif;

				response(200, __("lang_response_message_queued"));

        		break;
        	case "sms.bulk":
        		if(!isset($request["campaign"], $request["mode"], $request["numbers"], $request["groups"], $request["shortener"], $request["message"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->file->exists("system/storage/temporary/firebase.json")):
					response(500, __("lang_response_system_sysconfigerr"));
				endif;

				if(!$this->sanitize->length($request["campaign"]))
					response(500, __("lang_requests_create_campaignnametooshort"));

				if(!$this->sanitize->isInt($request["mode"]))
					response(500, __("lang_response_invalid"));

				if(!is_array($request["groups"]))
        			response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["shortener"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->length($request["message"], system_message_min))
					response(500, __("lang_response_message_short"));

				if(system_message_max > 0):
					if($this->sanitize->length($request["message"], system_message_max, 2))
						response(500, __("lang_response_message_toolong"));
				endif;

        		if($request["mode"] < 2):
        			if(!isset(
	        			$request["device"],
	        			$request["sim"], 
	        			$request["priority"]
	        		))
        				response(500, __("lang_response_invalid"));

        			$subscription = set_subscription(
	                    $this->system->checkSubscription(logged_id), 
	                    $this->system->getSubscription(false, logged_id), 
	                    $this->system->getSubscription(false, false, true)
	                );

					if(empty($subscription))
						response(500, __("lang_response_package_nosubwarn"));

					if(!$this->sanitize->isInt($request["sim"]))
						response(500, __("lang_response_invalid"));

					if(!$this->sanitize->isInt($request["priority"]))
						response(500, __("lang_response_invalid"));

	    			if($this->system->checkDevice(logged_id, $request["device"], "did") < 1)
    					response(500, __("lang_response_invalid"));

	    			$device = $this->system->getDevice(logged_id, $request["device"], "did");
        		else:
        			if(!isset($request["gateway"]))
        				response(500, __("lang_response_invalid"));

        			if($this->sanitize->isInt($request["gateway"])):
        				$gateways = $this->system->getGateways();

						if(!array_key_exists($request["gateway"], $gateways)):
							response(500, __("lang_response_invalid"));
						endif;

						if(!$this->file->exists("system/gateways/" . md5($request["gateway"]) . ".php"))
							response(500, __("lang_response_went_wrong"));

						try {
                            require "system/gateways/" . md5($request["gateway"]) . ".php";
                        } catch(Exception $e){
                            response(500, __("lang_requests_sms_encounteredgatewayerror"));
                        }
        			else:
        				$device = $this->system->getDevice(false, $request["gateway"], "global");

		    			if($device):
		    				if($device["uid"] == logged_id):
		    					response(500, __("lang_requests_sms_gatewaycannotsendowndevices"));
		    				endif;

			    			if($device["global_device"] > 1):
			    				response(500, __("lang_response_invalid"));
			    			endif;
			    		else:
			    			response(500, __("lang_response_invalid"));
			    		endif;
        			endif;
        		endif;

				$contactBook = [];

				$numbers = explode("\n", trim($request["numbers"]));

				if(!empty($numbers) && !empty($numbers[0])):
					foreach($numbers as $number):
						$rejected = false;

						try {
						    $phone = $this->phone->parse($number, logged_country);

			    			$phone->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

						    if(!$phone->isValidNumber())
								$rejected = true;

							if(!$phone->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
								$rejected = true;

							$phoneNumber = $phone->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
							$country = $phone->getRegionCode();
						} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
							$rejected = true;
						}

						if($this->system->checkUnsubscribed(logged_id, $phoneNumber) > 0)
							$rejected = true;

						if(!$rejected):
							$contactBook[$phoneNumber] = [
								"name" => $phoneNumber,
								"phone" => $phoneNumber,
								"group" => "Unknown",
								"country" => $country
							];
						endif;
					endforeach;
				endif;

				if(!in_array(0, $request["groups"])):
					foreach($request["groups"] as $group):
						if($this->system->checkGroup(logged_id, $group) > 0):
							$contacts = $this->system->getContactsByGroup(logged_id, $group);

							if(!empty($contacts)):
								foreach($contacts as $contact):
									$rejected = false;

									try {
									    $phone = $this->phone->parse($contact["phone"], logged_country);
										$country = $phone->getRegionCode();
									} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
										$rejected = true;
									}

									if($this->system->checkUnsubscribed(logged_id, $contact["phone"]) > 0)
										$rejected = true;

									if(!$rejected):
										$contactBook[$contact["phone"]] = [
											"name" => $contact["name"],
											"phone" => $contact["phone"],
											"group" => $contact["group"],
											"country" => $country
										];
									endif;
								endforeach;
							endif;
						endif;
					endforeach;
				endif;

				if(empty($contactBook))
					response(500, __("lang_form_smsbulknonumbers"));

				if($request["shortener"] > 0):
					if(!$this->file->exists("system/shorteners/" . md5($request["shortener"]) . ".php"))
						response(500, __("lang_response_went_wrong"));

					$messageLinks = (new VStelmakh\UrlHighlight\UrlHighlight)->getUrls($request["message"]);

					if(!empty($messageLinks)):
						try {
							require "system/shorteners/" . md5($request["shortener"]) . ".php";
						} catch(Exception $e){
							response(500, __("lang_requests_create_encounteredshortenererror"));
						}

						foreach($messageLinks as $key => $value):
							$shortLink = shortenUrl($value, $this);

							if($shortLink):
								$request["message"] = str_replace("{$value}", "{$shortLink}", $request["message"]);
							endif;
						endforeach;
					endif;
				endif;

				$smsCampaign = $this->system->create("campaigns", [
					"uid" => logged_id,
					"did" => $request["mode"] < 2 ? $request["device"] : ($this->sanitize->isInt($request["gateway"]) ? false : $request["gateway"]),
					"gateway" => $request["mode"] > 1 && $this->sanitize->isInt($request["gateway"]) ? $request["gateway"] : 0,
					"mode" => $request["mode"],
					"status" => 1,
					"name" => $request["campaign"],
					"contacts" => count($contactBook),
					"create_date" => date("Y-m-d H:i:s", time())
				]);

				$sendCounter = 0;

				foreach($contactBook as $contact):
					if($request["mode"] < 2):
						if(!limitation($subscription["send_limit"], $this->system->countQuota(logged_id, "sent"))):
							$rejectLimit = false;

							if($device["limit_status"] < 2 && $this->system->checkSmsLimit(logged_id, $request["device"], $device["limit_interval"], $device["limit_number"])):
			    				$rejectLimit = true;
			    			endif;

			    			if(!$rejectLimit):
				        		$this->system->create("sent", [
				        			"cid" => $smsCampaign,
						        	"uid" => logged_id,
									"did" => $request["device"],
									"gateway" => 0,
									"sim" => $request["sim"] < 2 ? 1 : 2,
									"mode" => 1,
									"phone" => $contact["phone"],
									"message" => bjoernffm\Spintax\Parser::parse($this->lex->parse(footermark($subscription["footermark"], $request["message"], system_message_mark), [
				        				"contact" => [
				        					"name" => $contact["name"],
				        					"number" => $contact["phone"]
				        				],
				        				"group" => [
				        					"name" => $contact["group"]
				        				],
				        				"unsubscribe" => [
				        					"command" => "STOP",
				        					"link" => site_url("unsubscribe/" . logged_id . "/{$contact["phone"]}", true)
				        				],
				        				"date" => [
				        					"now" => date("F j, Y"),
				        					"time" => date("h:i A") 
				        				]
				        			]))->generate(),
									"status" => 1,
									"status_code" => false,
									"priority" => $request["priority"] < 1 ? 1 : 2,
									"api" => 2,
									"create_date" => date("Y-m-d H:i:s", time())
						        ]);

						        $sendCounter++;
				        	endif;
					    endif;
					else:
						$credits = $this->system->getCredits(logged_id);

						if($this->sanitize->isInt($request["gateway"])):
							$pricing = json_decode($gateways[$request["gateway"]]["pricing"], true);

							if(array_key_exists(strtolower($contact["country"]), $pricing["countries"])):
								$price = $pricing["countries"][strtolower($contact["country"])];
							else:
								$price = $pricing["default"];
							endif;

							if($credits >= ($price * count($contactBook))):
								$gateway = $gateways[$request["gateway"]];

								$message = bjoernffm\Spintax\Parser::parse($this->lex->parse($request["message"], [
			        				"contact" => [
			        					"name" => $contact["name"],
			        					"number" => $contact["phone"]
			        				],
			        				"group" => [
			        					"name" => $contact["group"]
			        				],
			        				"unsubscribe" => [
			        					"command" => "STOP",
			        					"link" => site_url("unsubscribe/" . logged_id . "/{$contact["phone"]}", true)
			        				],
			        				"date" => [
			        					"now" => date("F j, Y"),
			        					"time" => date("h:i A") 
			        				]
			        			]))->generate();

								$send = gatewaySend($contact["phone"], $message, $this);

								if($send):
									$create = $this->system->create("sent", [
										"cid" => $smsCampaign,
										"uid" => logged_id,
										"did" => false,
										"gateway" => $request["gateway"],
										"api" => 0,
										"sim" => 0,
										"mode" => 2,
										"priority" => 0,
										"phone" => $contact["phone"],
										"message" => $message,
										"status" => $gateway["callback"] < 2 ? 2 : 3,
										"status_code" => false,
										"create_date" => date("Y-m-d H:i:s", time())
									]);

									if($create):
										if($gateway["callback"] < 2):
											$this->cache->container("system.gateways");

											$this->cache->set("{$gateway["callback_id"]}.{$send}", $create);
										else:
											$this->process->_sanitize = $this->sanitize;
											$this->process->_guzzle = $this->guzzle;
											$this->process->_lex = $this->lex;
						
											$hooks = $this->process->actionHooks(logged_id, 1, 1, $contact["phone"], $message, $this->device->getActions(logged_id, 1));

											if(!empty($hooks)):
												foreach($hooks as $hook):
													$this->system->create("events", [
														"uid" => logged_id,
														"type" => 2,
														"create_date" => date("Y-m-d H:i:s", time())
													]);
												endforeach;
											endif;

											$this->system->credits(logged_id, "decrease", ($price * count($contactBook)));
										endif;
									endif;
								endif;
							endif;
						else:
							$currency = country($device["country"])->getCurrency()["iso_4217_code"];

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
						            	response(500, __("lang_response_went_wrong"));
						            endif;
						        } catch(Exception $e){
						            response(500, __("lang_response_went_wrong"));
						        }
						    endif;

						    $rates = $this->cache->get("exchange");

						    $base_rate = $rates["data"]["USD"] / $rates["data"][strtoupper($currency)];
					        $usd_price = ($base_rate * $device["rate"]) * $rates["data"]["USD"];
					        $final_price = (float) abs($usd_price * $rates["data"][strtoupper(system_currency)]);

					        if($credits >= ($final_price * count($contactBook))):
								$slots = explode(",", $device["global_slots"]);

								$sim = count($slots) > 1 ? rand(1, 2) : ($slots[0] < 2 ? 1 : 2);

								$rejectLimit = false;

								if($device["limit_status"] < 2 && $this->system->checkSmsLimit(logged_id, $request["gateway"], $device["limit_interval"], $device["limit_number"])):
				    				$rejectLimit = true;
				    			endif;

				    			if(!$rejectLimit):
					        		$this->system->create("sent", [
					        			"cid" => $smsCampaign,
							        	"uid" => logged_id,
										"did" => $request["gateway"],
										"gateway" => 0,
										"sim" => $sim,
										"mode" => 2,
										"phone" => $contact["phone"],
										"message" => bjoernffm\Spintax\Parser::parse($this->lex->parse($request["message"], [
					        				"contact" => [
					        					"name" => $contact["name"],
					        					"number" => $contact["phone"]
					        				],
					        				"group" => [
					        					"name" => $contact["group"]
					        				],
					        				"unsubscribe" => [
					        					"command" => "STOP",
					        					"link" => site_url("unsubscribe/" . logged_id . "/{$contact["phone"]}", true)
					        				],
					        				"date" => [
					        					"now" => date("F j, Y"),
					        					"time" => date("h:i A") 
					        				]
					        			]))->generate(),
										"status" => 1,
										"status_code" => false,
										"priority" => $request["priority"] < 1 ? 1 : 2,
										"api" => 2,
										"create_date" => date("Y-m-d H:i:s", time())
							        ]);

					        		if($device["limit_status"] < 2):
								        $sendCounter++;
								    endif;
					        	endif;
				        	endif;
						endif;
					endif;
				endforeach;

				if($request["mode"] < 2 || !$this->sanitize->isInt($request["gateway"])):
					if($device["limit_status"] < 2 && $this->system->checkSmsLimit(logged_id, $request["mode"] < 2 ? $request["device"] : $request["gateway"], $device["limit_interval"], $device["limit_number"])):
						if($sendCounter < 1):
							$this->system->delete(logged_id, $smsCampaign, "campaigns");
							$intervalType = $device["limit_interval"] < 2 ? __("lang_requests_sms_intervaltypedaily") : __("lang_requests_sms_intervaltypemonthly");
		    				response(500, ___(__("lang_requests_sms_intervaltypereachedmax"), [$intervalType]));
						endif;
					else:
						if($sendCounter < 1):
							$this->system->delete(logged_id, $smsCampaign, "campaigns");
							response(500, __("lang_requests_sms_reachedmaximumsentpackage"));
						endif;
					endif;

					if($sendCounter < count($contactBook)):
						$this->system->update($smsCampaign, logged_id, "campaigns", [
							"contacts" => $sendCounter
						]);
					endif;
				endif;

				if($request["mode"] < 2):
					$this->fcm->send(md5(logged_id . $request["device"]), [
				    	"type" => "sms",
				    	"global" => 0,
				    	"currency" => "None",
				    	"rate" => (float) 0
				    ]);
				else:
					if(!$this->sanitize->isInt($request["gateway"])):
						$this->fcm->send(md5($device["uid"] . $request["gateway"]), [
					    	"type" => "sms",
					    	"global" => 1,
					    	"currency" => $currency,
					    	"rate" => (float) $device["rate"]
					    ]);
					endif;
				endif;

				response(200, ___(__("lang_response_message_bulkqueuednew"), [$sendCounter]));

        		break;
        	case "sms.excel":
        		if(!isset($_FILES["excel"], $request["campaign"], $request["mode"], $request["shortener"], $request["message"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->file->exists("system/storage/temporary/firebase.json")):
					response(500, __("lang_response_system_sysconfigerr"));
				endif;

				if(!$this->sanitize->length($request["campaign"]))
					response(500, __("lang_requests_create_campaignnametooshort"));

				if(!$this->sanitize->isInt($request["mode"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["shortener"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->length($request["message"], system_message_min))
					response(500, __("lang_response_message_short"));

				if(system_message_max > 0):
					if($this->sanitize->length($request["message"], system_message_max, 2))
						response(500, __("lang_response_message_toolong"));
				endif;

        		if($request["mode"] < 2):
        			if(!isset(
	        			$request["device"]
	        		))
        				response(500, __("lang_response_invalid"));

        			$subscription = set_subscription(
	                    $this->system->checkSubscription(logged_id), 
	                    $this->system->getSubscription(false, logged_id), 
	                    $this->system->getSubscription(false, false, true)
	                );

					if(empty($subscription))
						response(500, __("lang_response_package_nosubwarn"));

	    			if($this->system->checkDevice(logged_id, $request["device"], "did") < 1)
    					response(500, __("lang_response_invalid"));

	    			$device = $this->system->getDevice(logged_id, $request["device"], "did");
        		else:
        			if(!isset($request["gateway"]))
        				response(500, __("lang_response_invalid"));

        			if($this->sanitize->isInt($request["gateway"])):
        				$gateways = $this->system->getGateways();

						if(!array_key_exists($request["gateway"], $gateways)):
							response(500, __("lang_response_invalid"));
						endif;

						if(!$this->file->exists("system/gateways/" . md5($request["gateway"]) . ".php"))
							response(500, __("lang_response_went_wrong"));

						try {
                            require "system/gateways/" . md5($request["gateway"]) . ".php";
                        } catch(Exception $e){
                            response(500, __("lang_requests_sms_encounteredgatewayerror"));
                        }
        			else:
        				$device = $this->system->getDevice(false, $request["gateway"], "global");

		    			if($device):
		    				if($device["uid"] == logged_id):
		    					response(500, __("lang_requests_sms_gatewaycannotsendowndevices"));
		    				endif;

			    			if($device["global_device"] > 1):
			    				response(500, __("lang_response_invalid"));
			    			endif;
			    		else:
			    			response(500, __("lang_response_invalid"));
			    		endif;
        			endif;
        		endif;

        		try {
        			$this->upload->upload($_FILES["excel"]);
	    			if($this->upload->uploaded):
	    				if(!in_array($this->upload->file_src_name_ext, ["xlsx"]))
	    					response(500, __("lang_response_invalid_excel"));

	    				$this->upload->mime_check = false;
		                $this->upload->file_new_name_body = logged_hash;
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/sheets/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, __("lang_response_invalid_excel"));
					endif;
        		} catch(Exception $e){
        			response(500, __("lang_response_invalid_excel"));
        		}

        		try {
        			$reader = $this->sheet->read("uploads/sheets/" . logged_hash . ".xlsx");
        		} catch(Exception $e){
        			response(500, __("lang_response_invalid_excel"));
        		}

        		$contactBook = [];

        		foreach($reader->getSheetIterator() as $sheet):
				    if($sheet->getIndex() === 0):
				    	$headers = [];
				    	$rowIndex = 0;

				        foreach($sheet->getRowIterator() as $row):
			        		if($rowIndex < 1):
			        			if(!in_array("phone", $row->toArray()))
			        				response(500, ___(__("lang_response_smsexcel_phonenotfound"), ["phone"]));

			        			if(!in_array("sim", $row->toArray()))
			        				response(500, ___(__("lang_response_smsexcel_phonenotfound"), ["sim"]));

			        			if(!in_array("priority", $row->toArray()))
			        				response(500, ___(__("lang_response_smsexcel_phonenotfound"), ["priority"]));

					        	foreach($row->toArray() as $key => $value):
					        		$headers[$value] = $key;
					        	endforeach;
					        else:
					        	$singleHeader = [];
					        	$cols = $row->toArray();

					        	foreach($headers as $key => $value):
					        		if(!in_array($key, ["phone", "sim", "priority"])):
						        		$singleHeader[$key] = $cols[$value];
						        	endif;
					        	endforeach;

					        	$phone = $cols[$headers["phone"]];
					        	$sim = $cols[$headers["sim"]];
					        	$priority = $cols[$headers["priority"]];

				        		$rejected = false;				        		

				        		try {
								    $number = $this->phone->parse($phone, logged_country);

			    					$number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

								    if(!$number->isValidNumber())
										$rejected = true;

									if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
										$rejected = true;

									$phoneNumber = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
									$country = $number->getRegionCode();
								} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
									$rejected = true;
								}

								if($this->system->checkUnsubscribed(logged_id, $phoneNumber) > 0)
									$rejected = true;

								if(!in_array($sim, [1, 2]))
									$rejected = true;

								if(!in_array($priority, [0, 1]))
									$rejected = true;

								if(!$rejected):
									$contactBook[$phoneNumber] = [
										"phone" => $phoneNumber,
										"sim" => $sim,
										"priority" => $priority,
										"country" => $country
									];
					        	endif;
					        endif;

					        $rowIndex++;
				        endforeach;
				    endif;	 
				endforeach;

				$reader->close();

				if(empty($contactBook))
					response(500, __("lang_form_smsbulknonumbers"));

				if($request["shortener"] > 0):
					if(!$this->file->exists("system/shorteners/" . md5($request["shortener"]) . ".php"))
						response(500, __("lang_response_went_wrong"));

					$messageLinks = (new VStelmakh\UrlHighlight\UrlHighlight)->getUrls($request["message"]);

					if(!empty($messageLinks)):
						try {
							require "system/shorteners/" . md5($request["shortener"]) . ".php";
						} catch(Exception $e){
							response(500, __("lang_requests_create_encounteredshortenererror"));
						}

						foreach($messageLinks as $key => $value):
							$shortLink = shortenUrl($value, $this);

							if($shortLink):
								$request["message"] = str_replace("{$value}", "{$shortLink}", $request["message"]);
							endif;
						endforeach;
					endif;
				endif;

				$smsCampaign = $this->system->create("campaigns", [
					"uid" => logged_id,
					"did" => $request["mode"] < 2 ? $request["device"] : ($this->sanitize->isInt($request["gateway"]) ? false : $request["gateway"]),
					"gateway" => $request["mode"] > 1 && $this->sanitize->isInt($request["gateway"]) ? $request["gateway"] : 0,
					"mode" => $request["mode"],
					"status" => 1,
					"name" => $request["campaign"],
					"contacts" => count($contactBook),
					"create_date" => date("Y-m-d H:i:s", time())
				]);

				$sendCounter = 0;

				foreach($contactBook as $contact):
					if($request["mode"] < 2):
						if(!limitation($subscription["send_limit"], $this->system->countQuota(logged_id, "sent"))):
							$rejectLimit = false;

							if($device["limit_status"] < 2 && $this->system->checkSmsLimit(logged_id, $request["device"], $device["limit_interval"], $device["limit_number"])):
			    				$rejectLimit = true;
			    			endif;

			    			if(!$rejectLimit):
				        		$this->system->create("sent", [
				        			"cid" => $smsCampaign,
						        	"uid" => logged_id,
									"did" => $request["device"],
									"gateway" => 0,
									"sim" => $contact["sim"] < 1 ? 1 : 2,
									"mode" => 1,
									"phone" => $contact["phone"],
				        			"message" => bjoernffm\Spintax\Parser::parse($this->lex->parse(footermark($subscription["footermark"], $request["message"], system_message_mark), [
			        					"phone" => $contact["phone"],
			        					"sim" => $contact["sim"],
			        					"priority" => $contact["priority"],
				        				"custom" => $singleHeader,
				        				"date" => [
				        					"now" => date("F j, Y"),
				        					"time" => date("h:i A") 
				        				]
				        			]))->generate(),
									"status" => 1,
									"status_code" => false,
									"priority" => $contact["priority"] < 1 ? 1 : 2,
									"api" => 2,
									"create_date" => date("Y-m-d H:i:s", time())
						        ]);

						        $sendCounter++;
				        	endif;
					    endif;
					else:
						$credits = $this->system->getCredits(logged_id);

						if($this->sanitize->isInt($request["gateway"])):
							$pricing = json_decode($gateways[$request["gateway"]]["pricing"], true);

							if(array_key_exists(strtolower($contact["country"]), $pricing["countries"])):
								$price = $pricing["countries"][strtolower($contact["country"])];
							else:
								$price = $pricing["default"];
							endif;

							if($credits >= ($price * count($contactBook))):
								$gateway = $gateways[$request["gateway"]];

								$message = bjoernffm\Spintax\Parser::parse($request["message"])->generate();

								$send = gatewaySend($contact["phone"], $message, $this);

								if($send):
									$create = $this->system->create("sent", [
										"cid" => $smsCampaign,
										"uid" => logged_id,
										"did" => false,
										"gateway" => $request["gateway"],
										"api" => 0,
										"sim" => 0,
										"mode" => 2,
										"priority" => 0,
										"phone" => $contact["phone"],
										"message" => $message,
										"status" => $gateway["callback"] < 2 ? 2 : 3,
										"status_code" => false
									]);

									if($create):
										if($gateway["callback"] < 2):
											$this->cache->container("system.gateways");

											$this->cache->set("{$gateway["callback_id"]}.{$send}", $create);
										else:
											$this->process->_sanitize = $this->sanitize;
											$this->process->_guzzle = $this->guzzle;
											$this->process->_lex = $this->lex;
						
											$hooks = $this->process->actionHooks(logged_id, 1, 1, $contact["phone"], $message, $this->device->getActions(logged_id, 1));

											if(!empty($hooks)):
												foreach($hooks as $hook):
													$this->system->create("events", [
														"uid" => logged_id,
														"type" => 2,
														"create_date" => date("Y-m-d H:i:s", time())
													]);
												endforeach;
											endif;

											$this->system->credits(logged_id, "decrease", ($price * count($contactBook)));
										endif;
									endif;
								endif;
							endif;
						else:
							$currency = country($device["country"])->getCurrency()["iso_4217_code"];

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
						            	response(500, __("lang_response_went_wrong"));
						            endif;
						        } catch(Exception $e){
						            response(500, __("lang_response_went_wrong"));
						        }
						    endif;

						    $rates = $this->cache->get("exchange");

						    $base_rate = $rates["data"]["USD"] / $rates["data"][strtoupper($currency)];
					        $usd_price = ($base_rate * $device["rate"]) * $rates["data"]["USD"];
					        $final_price = (float) abs($usd_price * $rates["data"][strtoupper(system_currency)]);

					        if($credits >= ($final_price * count($contactBook))):
								$slots = explode(",", $device["global_slots"]);

								$sim = count($slots) > 1 ? rand(1, 2) : ($slots[0] < 2 ? 1 : 2);

								$rejectLimit = false;

								if($device["limit_status"] < 2 && $this->system->checkSmsLimit(logged_id, $request["gateway"], $device["limit_interval"], $device["limit_number"])):
				    				$rejectLimit = true;
				    			endif;

				    			if(!$rejectLimit):
					        		$this->system->create("sent", [
					        			"cid" => $smsCampaign,
							        	"uid" => logged_id,
										"did" => $request["gateway"],
										"gateway" => 0,
										"sim" => $contact["sim"],
										"mode" => 2,
										"phone" => $contact["phone"],
										"message" => bjoernffm\Spintax\Parser::parse($this->lex->parse(request["message"], [
				        					"phone" => $contact["phone"],
				        					"sim" => $contact["sim"],
				        					"priority" => $contact["priority"],
					        				"custom" => $singleHeader,
					        				"date" => [
					        					"now" => date("F j, Y"),
					        					"time" => date("h:i A") 
					        				]
					        			]))->generate(),
										"status" => 1,
										"status_code" => false,
										"priority" => $contact["priority"] < 1 ? 1 : 2,
										"api" => 2,
										"create_date" => date("Y-m-d H:i:s", time())
							        ]);

					        		if($device["limit_status"] < 2):
								        $sendCounter++;
								    endif;
					        	endif;
				        	endif;
						endif;
					endif;
				endforeach;

				if($request["mode"] < 2 || !$this->sanitize->isInt($request["gateway"])):
					if($device["limit_status"] < 2 && $this->system->checkSmsLimit(logged_id, $request["mode"] < 2 ? $request["device"] : $request["gateway"], $device["limit_interval"], $device["limit_number"])):
						if($sendCounter < 1):
							$this->system->delete(logged_id, $smsCampaign, "campaigns");
							$intervalType = $device["limit_interval"] < 2 ? __("lang_requests_sms_intervaltypedaily") : __("lang_requests_sms_intervaltypemonthly");
		    				response(500, ___(__("lang_requests_sms_intervaltypereachedmax"), [$intervalType]));
						endif;
					else:
						if($sendCounter < 1):
							$this->system->delete(logged_id, $smsCampaign, "campaigns");
							response(500, __("lang_requests_sms_reachedmaximumsentpackage"));
						endif;
					endif;

					if($sendCounter < count($contactBook)):
						$this->system->update($smsCampaign, logged_id, "campaigns", [
							"contacts" => $sendCounter
						]);
					endif;
				endif;

				if($request["mode"] < 2):
					$this->fcm->send(md5(logged_id . $request["device"]), [
				    	"type" => "sms",
				    	"global" => 0,
				    	"currency" => "None",
				    	"rate" => (float) 0
				    ]);
				else:
					if(!$this->sanitize->isInt($request["gateway"])):
						$this->fcm->send(md5($device["uid"] . $request["gateway"]), [
					    	"type" => "sms",
					    	"global" => 1,
					    	"currency" => $currency,
					    	"rate" => (float) $device["rate"]
					    ]);
					endif;
				endif;

				response(200, ___(__("lang_response_message_bulkqueuednew"), [$sendCounter]));

        		break;
        	case "add.sms.scheduled":
        		if(!isset($request["mode"], $request["shortener"], $request["schedule"], $request["repeat"], $request["numbers"], $request["groups"], $request["message"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->isInt($request["mode"]))
					response(500, __("lang_response_invalid"));

				if(!is_array($request["groups"]))
        			response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["shortener"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["repeat"]))
					response(500, __("lang_response_invalid"));

				if(empty($request["numbers"]) && in_array(0, $request["groups"]))
					response(500, __("lang_form_smsbulknonumbers"));

				if(!$this->sanitize->length($request["message"], system_message_min))
					response(500, __("lang_response_message_short"));

				if(system_message_max > 0):
					if($this->sanitize->length($request["message"], system_message_max, 2))
						response(500, __("lang_response_message_toolong"));
				endif;

				$subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

				if($request["shortener"] > 0):
					if(!$this->file->exists("system/shorteners/" . md5($request["shortener"]) . ".php"))
						response(500, __("lang_response_went_wrong"));

					$messageLinks = (new VStelmakh\UrlHighlight\UrlHighlight)->getUrls($request["message"]);

					if(!empty($messageLinks)):
						try {
							require "system/shorteners/" . md5($request["shortener"]) . ".php";
						} catch(Exception $e){
							response(500, __("lang_requests_create_encounteredshortenererror"));
						}

						foreach($messageLinks as $key => $value):
							$shortLink = shortenUrl($value, $this);

							if($shortLink):
								$request["message"] = str_replace("{$value}", "{$shortLink}", $request["message"]);
							endif;
						endforeach;
					endif;
				endif;

        		if($request["mode"] < 2):
        			if(!isset($request["device"], $request["sim"]))
        				response(500, __("lang_response_invalid"));

					if(empty($subscription))
						response(500, __("lang_response_package_nosubwarn"));

					if(!$this->sanitize->isInt($request["sim"]))
						response(500, __("lang_response_invalid"));

	    			if($this->system->checkDevice(logged_id, $request["device"], "did") < 1)
    					response(500, __("lang_response_invalid"));

	    			$device = $this->system->getDevice(logged_id, $request["device"], "did");

	    			if($device):
    					$filtered = [
							"uid" => logged_id,
							"did" => $request["device"],
							"sim" => $request["sim"] < 2 ? 1 : 2,
							"mode" => 1,
							"gateway" => 0,
							"groups" => implode(",", $request["groups"]),
							"name" => $request["name"],
							"numbers" => $request["numbers"],
							"message" => $request["message"],
							"repeat" => $request["repeat"],
							"last_send" => false,
							"send_date" => strtotime($request["schedule"])
						];
		    		else:
		    			response(500, __("lang_response_invalid"));
		    		endif;
        		else:
        			if(!isset($request["gateway"]))
        				response(500, __("lang_response_invalid"));

        			if($this->sanitize->isInt($request["gateway"])):
        				$gateways = $this->system->getGateways();

						if(!array_key_exists($request["gateway"], $gateways)):
							response(500, __("lang_response_invalid"));
						endif;

						$filtered = [
							"uid" => logged_id,
							"did" => false,
							"sim" => 0,
							"mode" => 2,
							"gateway" => $request["gateway"],
							"groups" => implode(",", $request["groups"]),
							"name" => $request["name"],
							"numbers" => $request["numbers"],
							"message" => $request["message"],
							"repeat" => $request["repeat"] < 2 ? 1 : 2,
							"last_send" => false,
							"send_date" => strtotime($request["schedule"])
						];
        			else:
        				$device = $this->system->getDevice(false, $request["gateway"], "global");

		    			if($device):
			    			if($device["global_device"] > 1):
			    				response(500, __("lang_response_invalid"));
			    			else:
			    				$slots = explode(",", $device["global_slots"]);

			    				$filtered = [
									"uid" => logged_id,
									"did" => $request["gateway"],
									"sim" => count($slots) > 1 ? rand(1, 2) : ($slots[0] < 2 ? 1 : 2),
									"mode" => 2,
									"gateway" => 0,
									"groups" => implode(",", $request["groups"]),
									"name" => $request["name"],
									"numbers" => $request["numbers"],
									"message" => $request["message"],
									"repeat" => $request["repeat"] < 2 ? 1 : 2,
									"last_send" => false,
									"send_date" => strtotime($request["schedule"])
								];
			    			endif;
			    		else:
			    			response(500, __("lang_response_invalid"));
			    		endif;
        			endif;
        		endif;

				if($this->system->create("scheduled", $filtered)):
					response(200, __("lang_response_smssched_queued"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "whatsapp.quick":
        		if(!isset($request["phone"], $request["shortener"], $request["account"], $request["message"], $request["message_type"]))
        			response(5001, __("lang_response_invalid"));

        		if(empty(system_wa_server) && empty(system_wa_port))
					response(500, __("lang_requests_whatsapp_notavail"));

        		if(!$this->sanitize->isInt($request["shortener"]))
        			response(5002, __("lang_response_invalid"));

        		if(!$this->sanitize->isInt($request["account"]))
					response(5003, __("lang_response_invalid"));

        		if(!in_array($request["message_type"], ["text", "media", "button", "list", "template"]))
        			response(5004, __("lang_response_invalid"));

        		if(in_array($request["message_type"], ["text", "button", "list", "template"])):
					if(!$this->sanitize->length($request["message"], system_message_min))
						response(500, __("lang_response_message_short"));

					if(system_message_max > 0):
						if($this->sanitize->length($request["message"], system_message_max, 2))
							response(500, __("lang_response_message_toolong"));
					endif;
				endif;

        		$subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

				if(empty($subscription))
					response(500, __("lang_response_package_nosubwarn"));

				if($this->system->checkWaAccount(logged_id, $request["account"], "id") < 1)
					response(5005, __("lang_response_invalid"));

				if($this->system->checkQuota(logged_id) < 1):
					$this->system->create("quota", [
						"uid" => logged_id,
						"sent" => 0,
						"received" => 0,
						"wa_sent" => 0,
						"wa_received" => 0,
						"ussd" => 0,
						"notifications" => 0
					]);
				endif;

				if(limitation($subscription["wa_send_limit"], $this->system->countQuota(logged_id, "wa_sent")))
    				response(500, __("lang_response_whatsapp_reachedmaxchat"));

    			if(!find("@g.us", $request["phone"])):
	        		try {
					    $number = $this->phone->parse($request["phone"], logged_country);

					    $number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

					    if(!$number->isValidNumber())
							response(500, __("lang_response_invalid_number"));

						if(logged_country != "BR" && !$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
							response(500, __("lang_response_invalid_number"));

						$request["phone"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
					} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
						response(500, __("lang_response_invalid_number"));
					}
				endif;

				if($request["shortener"] > 0):
					if(!$this->file->exists("system/shorteners/" . md5($request["shortener"]) . ".php"))
						response(500, __("lang_response_went_wrong"));

					$messageLinks = (new VStelmakh\UrlHighlight\UrlHighlight)->getUrls($request["message"]);

					if(!empty($messageLinks)):
						try {
							require "system/shorteners/" . md5($request["shortener"]) . ".php";
						} catch(Exception $e){
							response(500, __("lang_requests_create_encounteredshortenererror"));
						}

						foreach($messageLinks as $key => $value):
							$shortLink = shortenUrl($value, $this);

							if($shortLink):
								$request["message"] = str_replace("{$value}", "{$shortLink}", $request["message"]);
							endif;
						endforeach;
					endif;
				endif;

				$request["message"] = bjoernffm\Spintax\Parser::parse(footermark($subscription["footermark"], $request["message"], system_message_mark))->generate();

				switch($request["message_type"]):
        			case "media":
        				try {
		        			$this->upload->upload($_FILES["media_file"]);
			    			if($this->upload->uploaded):
			    				if(!in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif", "mp4"]))
			    					response(500, __("lang_response_whatsapp_invalidmediafile"));

			    				$mediaName = logged_hash . "_" . uniqid(logged_hash, true);

			    				$this->upload->mime_check = false;
				                $this->upload->file_new_name_body = $mediaName;
				                $this->upload->file_overwrite = true;
				                $this->upload->process("uploads/whatsapp/");

								if($this->upload->processed):
									if(in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif"])):
										// image
				        				$message = [
				        					"image" => [
				        						"url" => site_url("uploads/whatsapp/{$mediaName}.{$this->upload->file_src_name_ext}", true)
				        					],
				        					"caption" => $request["message"]
				        				];
									else:
										// video
				        				$message = [
				        					"video" => [
				        						"url" => site_url("uploads/whatsapp/{$mediaName}.{$this->upload->file_src_name_ext}", true)
				        					],
				        					"caption" => $request["message"]
				        				];
									endif;

									$this->upload->clean();
								else:
									response(500, __("lang_response_whatsapp_invalidmediafile"));
								endif;
							endif;
		        		} catch(Exception $e){
		        			response(500, __("lang_response_whatsapp_invalidmediafile"));
		        		}

        				break;
        			case "button":
                        if(!isset($request["button_1"], $request["button_2"], $request["button_3"]))
                        	response(500, __("lang_response_invalid"));

                        if(empty($request["button_1"]))
                        	response(500, __("lang_response_whatsapp_atleastonebutt"));

                        $mediaUrl = false;

                        try {
		        			$this->upload->upload($_FILES["button_image"]);
			    			if($this->upload->uploaded):
			    				if(!in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif"]))
			    					response(500, __("lang_response_whatsapp_invalidimagefile"));

			    				$mediaName = logged_hash . "_" . uniqid(logged_hash, true);

			    				$this->upload->mime_check = false;
				                $this->upload->file_new_name_body = $mediaName;
				                $this->upload->file_overwrite = true;
				                $this->upload->process("uploads/whatsapp/");

								if($this->upload->processed):
									$mediaUrl = site_url("uploads/whatsapp/{$mediaName}.{$this->upload->file_src_name_ext}", true);

									$this->upload->clean();
								else:
									response(500, __("lang_response_whatsapp_invalidimagefile"));
								endif;
							endif;
		        		} catch(Exception $e){
		        			response(500, __("lang_response_whatsapp_invalidimagefile"));
		        		}

		        		$waButtons = [
		        			[
								"buttonId" => "btn1",
								"buttonText" => [
									"displayText" => $request["button_1"]
								],
								"type" => 1
							]
		        		];

		        		if(!empty($request["button_2"])):
		        			$waButtons[] = [
    							"buttonId" => "btn2",
    							"buttonText" => [
    								"displayText" => $request["button_2"]
    							],
    							"type" => 1
    						];
		        		endif;

		        		if(!empty($request["button_3"])):
		        			$waButtons[] = [
    							"buttonId" => "btn3",
    							"buttonText" => [
    								"displayText" => $request["button_3"]
    							],
    							"type" => 1
    						];
		        		endif;

		        		if(!$mediaUrl):
		        			$message = [
		        				"text" => $request["message"],
	        					"buttons" => $waButtons,
	        					"headerType" => 1
	        				];
		        		else:
		        			$message = [
	        					"buttons" => $waButtons,
	        					"image" => [
	        						"url" => $mediaUrl
	        					],
	        					"caption" => $request["message"],
	        					"headerType" => 4
	        				];
		        		endif;

        				break;
        			case "list":
        				if(!isset($request["list_title"], $request["list_menu"], $request["list_footer"], $request["list_format"]))
                        	response(500, __("lang_response_invalid"));

                        if(empty($request["list_title"]))
                        	response(500, __("lang_response_whatsapp_listtitlereq"));

                        if(empty($request["list_menu"]))
                        	response(500, __("lang_response_whatsapp_listmenureq"));

                        if(empty($request["list_format"]))
                        	response(500, __("lang_response_whatsapp_listformatreq"));

                        try {
							$listFormat = json_decode(htmlspecialchars_decode($request["list_format"]), true, JSON_THROW_ON_ERROR);
						} catch(Exception $e){
							response(500, __("lang_response_whatsapp_listformatinval"));
						}

        				$message = [
        					"text" => $request["message"],
        					"footer" => $request["list_footer"],
        					"title" => $request["list_title"],
        					"buttonText" => $request["list_menu"],
        					"sections" => $listFormat
        				];

        				break;
        			case "template":
        				if(!isset($request["template_footer"], $request["template_format"]))
                        	response(500, __("lang_response_invalid"));

                        if(empty($request["template_format"]))
                        	response(500, __("lang_response_whatsapp_tempformatreq"));

                        try {
							$templateFormat = json_decode(htmlspecialchars_decode($request["template_format"]), true, JSON_THROW_ON_ERROR);
						} catch(Exception $e){
							response(500, __("lang_response_whatsapp_tempformatinval"));
						}

        				$message = [
        					"text" => $request["message"],
        					"footer" => $request["template_footer"],
        					"templateButtons" => $templateFormat
        				];

        				break;
        			default:
        				$message = [
        					"text" => $request["message"]
        				];
        		endswitch;

				$account = $this->system->getWaAccount(logged_id, $request["account"], "id");

				$filtered = [
					"cid" => 0,
					"uid" => logged_id,
					"wid" => $account["wid"],
					"unique" => $account["unique"],
					"phone" => $request["phone"],
					"message" => json_encode($message),
					"status" => 1,
					"api" => 2,
					"create_date" => date("Y-m-d H:i:s", time())
				];

				if($this->system->create("wa_sent", $filtered)):
					$this->wa->_guzzle = $this->guzzle;

					if(!$this->wa->check())
						response(500, __("lang_response_whatsapp_noconnectserver"));

					$addQueue = $this->wa->send($account["unique"]);

					if($addQueue):
						if($addQueue == 200):
							response(200, __("lang_response_whatsapp_chatqueued"));
						else:
							response(500, __("lang_response_whatsapp_failedchatsqueue"));
						endif;
					else:
						response(500, __("lang_response_whatsapp_unableconnectserv"));
					endif;
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "whatsapp.bulk":
        		if(!isset($request["campaign"], $request["groups"], $request["numbers"], $request["shortener"], $request["account"], $request["message"], $request["message_type"]))
        			response(500, __("lang_response_invalid"));

        		if(empty(system_wa_server) || empty(system_wa_port))
        			response(500, __("lang_requests_whatsapp_notavail"));

        		if(!$this->sanitize->length($request["campaign"]))
					response(500, __("lang_requests_create_campaignnametooshort"));

				if(!is_array($request["groups"]))
        			response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["shortener"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["account"]))
					response(500, __("lang_response_invalid"));

				if(!in_array($request["message_type"], ["text", "media", "button", "list", "template"]))
        			response(500, __("lang_response_invalid"));

				if(in_array($request["message_type"], ["text", "button", "list", "template"])):
					if(!$this->sanitize->length($request["message"], system_message_min))
						response(500, __("lang_response_message_short"));

					if(system_message_max > 0):
						if($this->sanitize->length($request["message"], system_message_max, 2))
							response(500, __("lang_response_message_toolong"));
					endif;
				endif;

				$subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

				if(empty($subscription))
					response(500, __("lang_response_package_nosubwarn"));

				if($this->system->checkWaAccount(logged_id, $request["account"], "id") < 1)
					response(500, __("lang_response_invalid"));

				if($this->system->checkQuota(logged_id) < 1):
					$this->system->create("quota", [
						"uid" => logged_id,
						"sent" => 0,
						"received" => 0,
						"wa_sent" => 0,
						"wa_received" => 0,
						"ussd" => 0,
						"notifications" => 0
					]);
				endif;

				$contactBook = [];

				$numbers = explode("\n", trim($request["numbers"]));

				if(!empty($numbers) && !empty($numbers[0])):
					foreach($numbers as $number):
						$rejected = false;

						$number = trim($number);

						if(!find("@g.us", $number)):
							try {
							    $phone = $this->phone->parse($number, logged_country);

				    			$phone->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

							    if(!$phone->isValidNumber())
									$rejected = true;

								if(!$phone->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
									$rejected = true;

								$phoneNumber = $phone->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
							} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
								$rejected = true;
							}

							if($this->system->checkUnsubscribed(logged_id, $phoneNumber) > 0):
								$rejected = true;
							endif;
						else:
							$phoneNumber = $number;
						endif;

						if(!$rejected):
							$contactBook[$phoneNumber] = [
								"name" => $phoneNumber,
								"phone" => $phoneNumber,
								"group" => "Unknown"
							];
						endif;

						unset($rejected);
					endforeach;
				endif;

				if(!in_array(0, $request["groups"])):
					foreach($request["groups"] as $group):
						if($this->system->checkGroup(logged_id, $group) > 0):
							$contacts = $this->system->getContactsByGroup(logged_id, $group);
							
							if(!empty($contacts)):
								foreach($contacts as $contact):
									$rejected = false;

									try {
									    $phone = $this->phone->parse($contact["phone"], logged_country);

										$phoneNumber = $phone->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
									} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
										$rejected = true;
									}

									if($this->system->checkUnsubscribed(logged_id, $phone) > 0)
										$rejected = true;

									if(!$rejected):
										$contactBook[$contact["phone"]] = [
											"name" => $contact["name"],
											"phone" => $phoneNumber,
											"group" => $contact["group"]
										];
									endif;
								endforeach;
							endif;
						endif;
					endforeach;
				endif;

				if(empty($contactBook))
					response(500, __("lang_form_smsbulknonumbers"));

				if($request["shortener"] > 0):
					if(!$this->file->exists("system/shorteners/" . md5($request["shortener"]) . ".php"))
						response(500, __("lang_response_went_wrong"));

					$messageLinks = (new VStelmakh\UrlHighlight\UrlHighlight)->getUrls($request["message"]);

					if(!empty($messageLinks)):
						try {
							require "system/shorteners/" . md5($request["shortener"]) . ".php";
						} catch(Exception $e){
							response(500, __("lang_requests_create_encounteredshortenererror"));
						}

						foreach($messageLinks as $key => $value):
							$shortLink = shortenUrl($value, $this);

							if($shortLink):
								$request["message"] = str_replace("{$value}", "{$shortLink}", $request["message"]);
							endif;
						endforeach;
					endif;
				endif;

				switch($request["message_type"]):
        			case "media":
        				try {
		        			$this->upload->upload($_FILES["media_file"]);
			    			if($this->upload->uploaded):
			    				if(!in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif", "mp4"]))
			    					response(500, __("lang_response_whatsapp_invalidmediafile"));

			    				$mediaName = logged_hash . "_" . uniqid(logged_hash, true);

			    				$this->upload->mime_check = false;
				                $this->upload->file_new_name_body = $mediaName;
				                $this->upload->file_overwrite = true;
				                $this->upload->process("uploads/whatsapp/");

								if($this->upload->processed):
									if(in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif"])):
										// image
				        				$message = [
				        					"image" => [
				        						"url" => site_url("uploads/whatsapp/{$mediaName}.{$this->upload->file_src_name_ext}", true)
				        					],
				        					"caption" => $request["message"]
				        				];
									else:
										// video
				        				$message = [
				        					"video" => [
				        						"url" => site_url("uploads/whatsapp/{$mediaName}.{$this->upload->file_src_name_ext}", true)
				        					],
				        					"caption" => $request["message"]
				        				];
									endif;

									$this->upload->clean();
								else:
									response(500, __("lang_response_whatsapp_invalidmediafile"));
								endif;
							endif;
		        		} catch(Exception $e){
		        			response(500, __("lang_response_whatsapp_invalidmediafile"));
		        		}

        				break;
        			case "button":
                        if(!isset($request["button_1"], $request["button_2"], $request["button_3"]))
                        	response(500, __("lang_response_invalid"));

                        if(empty($request["button_1"]))
                        	response(500, __("lang_response_whatsapp_atleastonebutt"));

                        $mediaUrl = false;

                        try {
		        			$this->upload->upload($_FILES["button_image"]);
			    			if($this->upload->uploaded):
			    				if(!in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif"]))
			    					response(500, __("lang_response_whatsapp_invalidimagefile"));

			    				$mediaName = logged_hash . "_" . uniqid(logged_hash, true);

			    				$this->upload->mime_check = false;
				                $this->upload->file_new_name_body = $mediaName;
				                $this->upload->file_overwrite = true;
				                $this->upload->process("uploads/whatsapp/");

								if($this->upload->processed):
									$mediaUrl = site_url("uploads/whatsapp/{$mediaName}.{$this->upload->file_src_name_ext}", true);

									$this->upload->clean();
								else:
									response(500, __("lang_response_whatsapp_invalidimagefile"));
								endif;
							endif;
		        		} catch(Exception $e){
		        			response(500, __("lang_response_whatsapp_invalidimagefile"));
		        		}

		        		$waButtons = [
		        			[
								"buttonId" => "btn1",
								"buttonText" => [
									"displayText" => $request["button_1"]
								],
								"type" => 1
							]
		        		];

		        		if(!empty($request["button_2"])):
		        			$waButtons[] = [
    							"buttonId" => "btn2",
    							"buttonText" => [
    								"displayText" => $request["button_2"]
    							],
    							"type" => 1
    						];
		        		endif;

		        		if(!empty($request["button_3"])):
		        			$waButtons[] = [
    							"buttonId" => "btn3",
    							"buttonText" => [
    								"displayText" => $request["button_3"]
    							],
    							"type" => 1
    						];
		        		endif;

		        		if(!$mediaUrl):
		        			$message = [
		        				"text" => $request["message"],
	        					"buttons" => $waButtons,
	        					"headerType" => 1
	        				];
		        		else:
		        			$message = [
	        					"buttons" => $waButtons,
	        					"image" => [
	        						"url" => $mediaUrl
	        					],
	        					"caption" => $request["message"],
	        					"headerType" => 4
	        				];
		        		endif;

        				break;
        			case "list":
        				if(!isset($request["list_title"], $request["list_menu"], $request["list_footer"], $request["list_format"]))
                        	response(500, __("lang_response_invalid"));

                        if(empty($request["list_title"]))
                        	response(500, __("lang_response_whatsapp_listtitlereq"));

                        if(empty($request["list_menu"]))
                        	response(500, __("lang_response_whatsapp_listmenureq"));

                        if(empty($request["list_format"]))
                        	response(500, __("lang_response_whatsapp_listformatreq"));

                        try {
							$listFormat = json_decode(htmlspecialchars_decode($request["list_format"]), true, JSON_THROW_ON_ERROR);
						} catch(Exception $e){
							response(500, __("lang_response_whatsapp_listformatinval"));
						}

        				$message = [
        					"text" => $request["message"],
        					"footer" => $request["list_footer"],
        					"title" => $request["list_title"],
        					"buttonText" => $request["list_menu"],
        					"sections" => $listFormat
        				];

        				break;
        			case "template":
        				if(!isset($request["template_footer"], $request["template_format"]))
                        	response(500, __("lang_response_invalid"));

                        if(empty($request["template_format"]))
                        	response(500, __("lang_response_whatsapp_tempformatreq"));

                        try {
							$templateFormat = json_decode(htmlspecialchars_decode($request["template_format"]), true, JSON_THROW_ON_ERROR);
						} catch(Exception $e){
							response(500, __("lang_response_whatsapp_tempformatinval"));
						}

        				$message = [
        					"text" => $request["message"],
        					"footer" => $request["template_footer"],
        					"templateButtons" => $templateFormat
        				];

        				break;
        			default:
        				$message = [
        					"text" => $request["message"]
        				];
        		endswitch;

				$account = $this->system->getWaAccount(logged_id, $request["account"], "id");

				$waCampaign = $this->system->create("wa_campaigns", [
					"uid" => logged_id,
					"wid" => $account["wid"],
					"type" => $request["message_type"],
					"status" => 1,
					"name" => $request["campaign"],
					"contacts" => count($contactBook),
					"create_date" => date("Y-m-d H:i:s", time())
				]);

				$sendCounter = 0;

				foreach($contactBook as $contact):
					if(!limitation($subscription["wa_send_limit"], $this->system->countQuota(logged_id, "wa_sent"))):
						if(isset($message["text"]) || isset($message["caption"])):
							$formatMessage = bjoernffm\Spintax\Parser::parse($this->lex->parse(footermark($subscription["footermark"], isset($message["text"]) ? $message["text"] : $message["caption"], system_message_mark), [
		        				"contact" => [
		        					"name" => $contact["name"],
		        					"number" => $contact["phone"]
		        				],
		        				"group" => [
		        					"name" => $contact["group"]
		        				],
		        				"unsubscribe" => [
		        					"command" => "STOP",
		        					"link" => site_url("unsubscribe/" . logged_id . "/{$contact["phone"]}", true)
		        				],
		        				"date" => [
		        					"now" => date("F j, Y"),
		        					"time" => date("h:i A") 
		        				]
		        			]))->generate();

		        			if(isset($message["text"])):
		        				$message["text"] = $formatMessage;
		        			else:
		        				$message["caption"] = $formatMessage;
		        			endif;

	        				unset($formatMessage);
	        			endif;

						$this->system->create("wa_sent", [
							"cid" => $waCampaign,
							"uid" => logged_id,
							"wid" => $account["wid"],
							"unique" => $account["unique"],
							"phone" => $contact["phone"],
							"message" => json_encode($message),
							"status" => 1,
							"api" => 2,
							"create_date" => date("Y-m-d H:i:s", time())
						]);

						$sendCounter++;
    				endif;
				endforeach;

				if($sendCounter > 0):
					$this->wa->_guzzle = $this->guzzle;

					if(!$this->wa->check())
						response(500, __("lang_response_whatsapp_unableconnectserv"));

					$addQueue = $this->wa->send($account["unique"]);

					if($addQueue):
						if($addQueue == 200):
							response(200, ___(__("lang_response_whatsapp_bulkchatssentcount"), [$sendCounter]));
						else:
							response(500, __("lang_response_whatsapp_failedchatsqueuemany"));
						endif;
					else:
						response(500, __("lang_response_whatsapp_unableconnectserv"));
					endif;
				else:
					response(500, __("lang_response_whatsapp_nochatsqueuedpacklimit"));
				endif;

        		break;
        	case "whatsapp.excel":
        		if(!isset($_FILES["excel"], $request["shortener"], $request["account"], $request["message"], $request["message_type"]))
        			response(500, __("lang_response_invalid"));

        		if(empty(system_wa_server) || empty(system_wa_port))
        			response(500, __("lang_requests_whatsapp_notavail"));

				if(!$this->sanitize->isInt($request["shortener"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["account"]))
					response(500, __("lang_response_invalid"));

				if(!in_array($request["message_type"], ["text", "media", "button", "list", "template"]))
        			response(500, __("lang_response_invalid"));

        		if(in_array($request["message_type"], ["text", "button", "list", "template"])):
					if(!$this->sanitize->length($request["message"], system_message_min))
						response(500, __("lang_response_message_short"));

					if(system_message_max > 0):
						if($this->sanitize->length($request["message"], system_message_max, 2))
							response(500, __("lang_response_message_toolong"));
					endif;
				endif;

				$subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

				if(empty($subscription))
					response(500, __("lang_response_package_nosubwarn"));

				if($this->system->checkWaAccount(logged_id, $request["account"], "id") < 1)
					response(500, __("lang_response_invalid"));

				if($this->system->checkQuota(logged_id) < 1):
					$this->system->create("quota", [
						"uid" => logged_id,
						"sent" => 0,
						"received" => 0,
						"wa_sent" => 0,
						"wa_received" => 0,
						"ussd" => 0,
						"notifications" => 0
					]);
				endif;

        		try {
        			$this->upload->upload($_FILES["excel"]);
	    			if($this->upload->uploaded):
	    				if(!in_array($this->upload->file_src_name_ext, ["xlsx"]))
	    					response(500, __("lang_response_invalid_excel"));

	    				$this->upload->mime_check = false;
		                $this->upload->file_new_name_body = logged_hash;
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/sheets/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, __("lang_response_invalid_excel"));
					endif;
        		} catch(Exception $e){
        			response(500, __("lang_response_invalid_excel"));
        		}

        		if($request["shortener"] > 0):
					if(!$this->file->exists("system/shorteners/" . md5($request["shortener"]) . ".php"))
						response(500, __("lang_response_went_wrong"));

					$messageLinks = (new VStelmakh\UrlHighlight\UrlHighlight)->getUrls($request["message"]);

					if(!empty($messageLinks)):
						try {
							require "system/shorteners/" . md5($request["shortener"]) . ".php";
						} catch(Exception $e){
							response(500, __("lang_requests_create_encounteredshortenererror"));
						}

						foreach($messageLinks as $key => $value):
							$shortLink = shortenUrl($value, $this);

							if($shortLink):
								$request["message"] = str_replace("{$value}", "{$shortLink}", $request["message"]);
							endif;
						endforeach;
					endif;
				endif;

				switch($request["message_type"]):
        			case "media":
        				try {
		        			$this->upload->upload($_FILES["media_file"]);
			    			if($this->upload->uploaded):
			    				if(!in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif", "mp4"]))
			    					response(500, "Invalid media file!");

			    				$mediaName = logged_hash . "_" . uniqid(logged_hash, true);

			    				$this->upload->mime_check = false;
				                $this->upload->file_new_name_body = $mediaName;
				                $this->upload->file_overwrite = true;
				                $this->upload->process("uploads/whatsapp/");

								if($this->upload->processed):
									if(in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif"])):
										// image
				        				$message = [
				        					"image" => [
				        						"url" => site_url("uploads/whatsapp/{$mediaName}.{$this->upload->file_src_name_ext}", true)
				        					],
				        					"caption" => $request["message"]
				        				];
									else:
										// video
				        				$message = [
				        					"video" => [
				        						"url" => site_url("uploads/whatsapp/{$mediaName}.{$this->upload->file_src_name_ext}", true)
				        					],
				        					"caption" => $request["message"]
				        				];
									endif;

									$this->upload->clean();
								else:
									response(500, "Invalid media file!");
								endif;
							endif;
		        		} catch(Exception $e){
		        			response(500, "Invalid media file!");
		        		}

        				break;
        			case "button":
                        if(!isset($request["button_1"], $request["button_2"], $request["button_3"]))
                        	response(500, __("lang_response_invalid"));

                        if(empty($request["button_1"]))
                        	response(500, "Please add at least one button!");

                        $mediaUrl = false;

                        try {
		        			$this->upload->upload($_FILES["button_image"]);
			    			if($this->upload->uploaded):
			    				if(!in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif"]))
			    					response(500, "Invalid image file!");

			    				$mediaName = logged_hash . "_" . uniqid(logged_hash, true);

			    				$this->upload->mime_check = false;
				                $this->upload->file_new_name_body = $mediaName;
				                $this->upload->file_overwrite = true;
				                $this->upload->process("uploads/whatsapp/");

								if($this->upload->processed):
									$mediaUrl = site_url("uploads/whatsapp/{$mediaName}.{$this->upload->file_src_name_ext}", true);

									$this->upload->clean();
								else:
									response(500, "Invalid image file!");
								endif;
							endif;
		        		} catch(Exception $e){
		        			response(500, "Invalid image file!");
		        		}

		        		$waButtons = [
		        			[
								"buttonId" => "btn1",
								"buttonText" => [
									"displayText" => $request["button_1"]
								],
								"type" => 1
							]
		        		];

		        		if(!empty($request["button_2"])):
		        			$waButtons[] = [
    							"buttonId" => "btn2",
    							"buttonText" => [
    								"displayText" => $request["button_2"]
    							],
    							"type" => 1
    						];
		        		endif;

		        		if(!empty($request["button_3"])):
		        			$waButtons[] = [
    							"buttonId" => "btn3",
    							"buttonText" => [
    								"displayText" => $request["button_3"]
    							],
    							"type" => 1
    						];
		        		endif;

		        		if(!$mediaUrl):
		        			$message = [
		        				"text" => $request["message"],
	        					"buttons" => $waButtons,
	        					"headerType" => 1
	        				];
		        		else:
		        			$message = [
	        					"buttons" => $waButtons,
	        					"image" => [
	        						"url" => $mediaUrl
	        					],
	        					"caption" => $request["message"],
	        					"headerType" => 4
	        				];
		        		endif;

        				break;
        			case "list":
        				if(!isset($request["list_title"], $request["list_menu"], $request["list_footer"], $request["list_format"]))
                        	response(500, __("lang_response_invalid"));

                        if(empty($request["list_title"]))
                        	response(500, "List title is required!");

                        if(empty($request["list_menu"]))
                        	response(500, "List menu title is required!");

                        if(empty($request["list_format"]))
                        	response(500, "List format is required!");

                        try {
							$listFormat = json_decode(htmlspecialchars_decode($request["list_format"]), true, JSON_THROW_ON_ERROR);
						} catch(Exception $e){
							response(500, "List format is invalid!");
						}

        				$message = [
        					"text" => $request["message"],
        					"footer" => $request["list_footer"],
        					"title" => $request["list_title"],
        					"buttonText" => $request["list_menu"],
        					"sections" => $listFormat
        				];

        				break;
        			case "template":
        				if(!isset($request["template_footer"], $request["template_format"]))
                        	response(500, __("lang_response_invalid"));

                        if(empty($request["template_format"]))
                        	response(500, "Template format is required!");

                        try {
							$templateFormat = json_decode(htmlspecialchars_decode($request["template_format"]), true, JSON_THROW_ON_ERROR);
						} catch(Exception $e){
							response(500, "Template format is invalid!");
						}

        				$message = [
        					"text" => $request["message"],
        					"footer" => $request["template_footer"],
        					"templateButtons" => $templateFormat
        				];

        				break;
        			default:
        				$message = [
        					"text" => $request["message"]
        				];
        		endswitch;

				$account = $this->system->getWaAccount(logged_id, $request["account"], "id");

				$waCampaign = $this->system->create("wa_campaigns", [
					"uid" => logged_id,
					"wid" => $account["wid"],
					"type" => $request["message_type"],
					"status" => 1,
					"name" => $request["campaign"],
					"contacts" => 0,
					"create_date" => date("Y-m-d H:i:s", time())
				]);

        		try {
        			$reader = $this->sheet->read("uploads/sheets/" . logged_hash . ".xlsx");
        		} catch(Exception $e){
        			response(500, __("lang_response_invalid_excel"));
        		}

        		$contactCount = 0;
        		$sendCounter = 0;

        		foreach($reader->getSheetIterator() as $sheet):
				    if($sheet->getIndex() === 0):
				    	$headers = [];
				    	$rowIndex = 0;

				        foreach($sheet->getRowIterator() as $row):
			        		if($rowIndex < 1):
			        			if(!in_array("phone", $row->toArray()))
			        				response(500, ___(__("lang_response_whatsapp_excelphonecolnotfound"), ["phone"]));

					        	foreach($row->toArray() as $key => $value):
					        		$headers[$value] = $key;
					        	endforeach;
					        else:
					        	$singleHeader = [];
					        	$cols = $row->toArray();

					        	foreach($headers as $key => $value):
					        		if(!in_array($key, ["phone"])):
						        		$singleHeader[$key] = $cols[$value];
						        	endif;
					        	endforeach;

					        	$phone = trim($cols[$headers["phone"]]);

				        		$rejected = false;				        		

				        		if(!find("@g.us", $phone)):
					        		try {
									    $number = $this->phone->parse($phone, logged_country);

				    					$number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

									    if(!$number->isValidNumber())
											$rejected = true;

										if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
											$rejected = true;

										$phoneNumber = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
									} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
										$rejected = true;
									}

									if($this->system->checkUnsubscribed(logged_id, $phoneNumber) > 0):
										$rejected = true;
									endif;
								else:
									$phoneNumber = $phone;
								endif;

								if(!$rejected):
									if(!limitation($subscription["wa_send_limit"], $this->system->countQuota(logged_id, "wa_sent"))):

										if(isset($message["text"]) || isset($message["caption"])):
											$formatMessage = bjoernffm\Spintax\Parser::parse($this->lex->parse(footermark($subscription["footermark"], isset($message["text"]) ? $message["text"] : $message["caption"], system_message_mark), [
						        				"phone" => $phoneNumber,
						        				"unsubscribe" => [
						        					"command" => "STOP",
						        					"link" => site_url("unsubscribe/" . logged_id . "/{$phoneNumber}", true)
						        				],
						        				"custom" => $singleHeader,
						        				"date" => [
						        					"now" => date("F j, Y"),
						        					"time" => date("h:i A") 
						        				]
						        			]))->generate();

						        			if(isset($message["text"])):
						        				$message["text"] = $formatMessage;
						        			else:
						        				$message["caption"] = $formatMessage;
						        			endif;

					        				unset($formatMessage);
					        			endif;

						        		$this->system->create("wa_sent", [
						        			"cid" => $waCampaign,
											"uid" => logged_id,
											"wid" => $account["wid"],
											"unique" => $account["unique"],
											"phone" => $phone,
											"message" => json_encode($message),
											"status" => 1,
											"api" => 2,
											"create_date" => date("Y-m-d H:i:s", time())
										]);

										$contactCount++;
										$sendCounter++;
								    endif;
					        	endif;
					        endif;

					        $rowIndex++;
				        endforeach;
				    endif;	 
				endforeach;

				$reader->close();

				if($sendCounter > 0):
					$this->wa->_guzzle = $this->guzzle;

					$addQueue = $this->wa->send($account["unique"]);

					if($addQueue):
						if($addQueue == 200):
							$this->system->update($waCampaign, logged_id, "wa_campaigns", [
								"contacts" => $contactCount
							]);

							response(200, ___(__("lang_response_whatsapp_bulkchatssentcount"), [$sendCounter]));
						else:
							response(500, __("lang_response_whatsapp_failedchatsqueuemany"));
						endif;
					else:
						response(500, __("lang_response_whatsapp_unableconnectserv"));
					endif;
				else:
					response(500, __("lang_response_whatsapp_nochatsqueuedpacklimit"));
				endif;

        		break;
        	case "add.whatsapp.scheduled":
        		if(!isset($request["shortener"], $request["account"], $request["message_type"], $request["schedule"], $request["repeat"], $request["numbers"], $request["groups"], $request["message"]))
        			response(500, __("lang_response_invalid"));

				if(!is_array($request["groups"]))
        			response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["shortener"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["repeat"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["account"]))
					response(500, __("lang_response_invalid"));

				if(!in_array($request["message_type"], ["text", "media", "button", "list", "template"]))
        			response(500, __("lang_response_invalid"));

        		if(in_array($request["message_type"], ["text", "button", "list", "template"])):
					if(!$this->sanitize->length($request["message"], system_message_min))
						response(500, __("lang_response_message_short"));

					if(system_message_max > 0):
						if($this->sanitize->length($request["message"], system_message_max, 2))
							response(500, __("lang_response_message_toolong"));
					endif;
				endif;

				$subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

				if(empty($subscription))
					response(500, __("lang_response_package_nosubwarn"));

				if($this->system->checkWaAccount(logged_id, $request["account"], "id") < 1)
					response(500, __("lang_response_invalid"));

				if($request["shortener"] > 0):
					if(!$this->file->exists("system/shorteners/" . md5($request["shortener"]) . ".php"))
						response(500, __("lang_response_went_wrong"));

					$messageLinks = (new VStelmakh\UrlHighlight\UrlHighlight)->getUrls($request["message"]);

					if(!empty($messageLinks)):
						try {
							require "system/shorteners/" . md5($request["shortener"]) . ".php";
						} catch(Exception $e){
							response(500, __("lang_requests_create_encounteredshortenererror"));
						}

						foreach($messageLinks as $key => $value):
							$shortLink = shortenUrl($value, $this);

							if($shortLink):
								$request["message"] = str_replace("{$value}", "{$shortLink}", $request["message"]);
							endif;
						endforeach;
					endif;
				endif;

				$request["message"] = bjoernffm\Spintax\Parser::parse(footermark($subscription["footermark"], encodeBraces($request["message"]), system_message_mark))->generate();

				switch($request["message_type"]):
        			case "media":
        				try {
		        			$this->upload->upload($_FILES["media_file"]);
			    			if($this->upload->uploaded):
			    				if(!in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif", "mp4"]))
			    					response(500, __("lang_response_whatsapp_invalidmediafile"));

			    				$mediaName = logged_hash . "_" . uniqid(logged_hash, true);

			    				$this->upload->mime_check = false;
				                $this->upload->file_new_name_body = $mediaName;
				                $this->upload->file_overwrite = true;
				                $this->upload->process("uploads/whatsapp/scheduled/");

								if($this->upload->processed):
									if(in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif"])):
										// image
				        				$message = [
				        					"image" => [
				        						"url" => site_url("uploads/whatsapp/scheduled/{$mediaName}.{$this->upload->file_src_name_ext}", true)
				        					],
				        					"caption" => $request["message"]
				        				];
									else:
										// video
				        				$message = [
				        					"video" => [
				        						"url" => site_url("uploads/whatsapp/scheduled/{$mediaName}.{$this->upload->file_src_name_ext}", true)
				        					],
				        					"caption" => $request["message"]
				        				];
									endif;

									$this->upload->clean();
								else:
									response(500, __("lang_response_whatsapp_invalidmediafile"));
								endif;
							endif;
		        		} catch(Exception $e){
		        			response(500, __("lang_response_whatsapp_invalidmediafile"));
		        		}

        				break;
        			case "button":
                        if(!isset($request["button_1"], $request["button_2"], $request["button_3"]))
                        	response(500, __("lang_response_invalid"));

                        if(empty($request["button_1"]))
                        	response(500, __("lang_response_whatsapp_atleastonebutt"));

                        $mediaUrl = false;

                        try {
		        			$this->upload->upload($_FILES["button_image"]);
			    			if($this->upload->uploaded):
			    				if(!in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif"]))
			    					response(500, __("lang_response_whatsapp_invalidmediafile"));

			    				$mediaName = logged_hash . "_" . uniqid(logged_hash, true);

			    				$this->upload->mime_check = false;
				                $this->upload->file_new_name_body = $mediaName;
				                $this->upload->file_overwrite = true;
				                $this->upload->process("uploads/whatsapp/scheduled/");

								if($this->upload->processed):
									$mediaUrl = site_url("uploads/whatsapp/scheduled/{$mediaName}.{$this->upload->file_src_name_ext}", true);

									$this->upload->clean();
								else:
									response(500, __("lang_response_whatsapp_invalidmediafile"));
								endif;
							endif;
		        		} catch(Exception $e){
		        			response(500, __("lang_response_whatsapp_invalidmediafile"));
		        		}

		        		$waButtons = [
		        			[
								"buttonId" => "btn1",
								"buttonText" => [
									"displayText" => $request["button_1"]
								],
								"type" => 1
							]
		        		];

		        		if(!empty($request["button_2"])):
		        			$waButtons[] = [
    							"buttonId" => "btn2",
    							"buttonText" => [
    								"displayText" => $request["button_2"]
    							],
    							"type" => 1
    						];
		        		endif;

		        		if(!empty($request["button_3"])):
		        			$waButtons[] = [
    							"buttonId" => "btn3",
    							"buttonText" => [
    								"displayText" => $request["button_3"]
    							],
    							"type" => 1
    						];
		        		endif;

		        		if(!$mediaUrl):
		        			$message = [
		        				"text" => $request["message"],
	        					"buttons" => $waButtons,
	        					"headerType" => 1
	        				];
		        		else:
		        			$message = [
	        					"buttons" => $waButtons,
	        					"image" => [
	        						"url" => $mediaUrl
	        					],
	        					"caption" => $request["message"],
	        					"headerType" => 4
	        				];
		        		endif;

        				break;
        			case "list":
        				if(!isset($request["list_title"], $request["list_menu"], $request["list_footer"], $request["list_format"]))
                        	response(500, __("lang_response_invalid"));

                        if(empty($request["list_title"]))
                        	response(500, __("lang_response_whatsapp_listtitlereq"));

                        if(empty($request["list_menu"]))
                        	response(500, __("lang_response_whatsapp_listmenureq"));

                        if(empty($request["list_format"]))
                        	response(500, __("lang_response_whatsapp_listformatreq"));

                        try {
							$listFormat = json_decode(htmlspecialchars_decode($request["list_format"]), true, JSON_THROW_ON_ERROR);
						} catch(Exception $e){
							response(500, __("lang_response_whatsapp_listformatinval"));
						}

        				$message = [
        					"text" => $request["message"],
        					"footer" => $request["list_footer"],
        					"title" => $request["list_title"],
        					"buttonText" => $request["list_menu"],
        					"sections" => $listFormat
        				];

        				break;
        			case "template":
        				if(!isset($request["template_footer"], $request["template_format"]))
                        	response(500, __("lang_response_invalid"));

                        if(empty($request["template_format"]))
                        	response(500, __("lang_response_whatsapp_tempformatreq"));

                        try {
							$templateFormat = json_decode(htmlspecialchars_decode($request["template_format"]), true, JSON_THROW_ON_ERROR);
						} catch(Exception $e){
							response(500, __("lang_response_whatsapp_tempformatinval"));
						}

        				$message = [
        					"text" => $request["message"],
        					"footer" => $request["template_footer"],
        					"templateButtons" => $templateFormat
        				];

        				break;
        			default:
        				$message = [
        					"text" => $request["message"]
        				];
        		endswitch;

				$account = $this->system->getWaAccount(logged_id, $request["account"], "id");

        		$filtered = [
					"uid" => logged_id,
					"wid" => $account["wid"],
					"unique" => $account["unique"],
					"groups" => implode(",", $request["groups"]),
					"name" => $request["name"],
					"numbers" => $request["numbers"],
					"message" => json_encode($message),
					"repeat" => $request["repeat"] < 2 ? 1 : 2,
					"last_send" => false,
					"send_date" => strtotime($request["schedule"])
				];

				if($this->system->create("wa_scheduled", $filtered)):
					response(200, __("lang_response_whatsapp_chatscheduled"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "whatsapp.groups":
        		if(!isset($request["account"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->isInt($request["account"]))
					response(500, __("lang_response_invalid"));

        		$subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

				if(empty($subscription))
					response(500, __("lang_response_package_nosubwarn"));

				if($this->system->checkWaAccount(logged_id, $request["account"], "id") < 1)
					response(500, __("lang_response_invalid"));

				$account = $this->system->getWaAccount(logged_id, $request["account"], "id");

				$this->wa->_guzzle = $this->guzzle;

				if(!$this->wa->check())
					response(500, __("lang_response_whatsapp_noconnectserver"));

				$getGroups = $this->wa->get_groups($account["unique"]);

				if($getGroups):
					if(!empty($getGroups)):
						foreach($getGroups as $group):
							if($this->system->checkWaGroup(logged_id, $group["id"]) < 1):
								$this->system->create("wa_groups", [
									"uid" => logged_id,
									"wid" => $account["wid"],
									"gid" => $group["id"],
									"name" => $group["subject"],
									"create_date" => date("Y-m-d H:i:s", time())
								]);
							endif;
						endforeach;

						$this->cache->container("wa.contacts." . logged_hash);
						$this->cache->clear();

						response(200, ___(__("lang_response_whatsapp_successfetchgroups"), [count($getGroups)]));
					else:
						response(500, __("lang_response_whatsapp_nogroupsfoundfetch"));
					endif;
				else:
					response(500, __("lang_response_whatsapp_noconnectserver"));
				endif;

        		break;
        	case "add.ussd":
        		if(!isset($request["code"], $request["sim"], $request["device"]))
        			response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["sim"]))
					response(500, __("lang_response_invalid"));

				$subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

				if(empty($subscription))
					response(500, __("lang_response_package_nosubwarn"));

				if(limitation($subscription["ussd_limit"], $this->system->countQuota(logged_id, "ussd")))
					response(500, __("lang_response_ussd_maximumreq"));

				if($this->system->checkDevice(logged_id, $request["device"], "did") < 1)
					response(500, __("lang_response_invalid"));

    			$device = $this->system->getDevice(logged_id, $request["device"], "did");

    			if(!$device)
    				response(500, __("lang_response_invalid"));

        		$filtered = [
					"uid" => logged_id,
					"code" => $request["code"],
					"did" => $request["device"],
					"sim" => $request["sim"] < 2 ? 1 : 2,
					"response" => false,
					"status" => 1,
					"create_date" => date("Y-m-d H:i:s", time())
				];

				if($this->system->create("ussd", $filtered)):
					$this->fcm->send(md5(logged_id . $request["device"]), [
				    	"type" => "ussd"
				    ]);

					response(200, __("lang_response_ussd_queued"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "add.template":
        		if(!isset($request["name"], $request["format"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				if(!$this->sanitize->length($request["format"], 5))
					response(500, __("lang_response_format_short"));

				$filtered = [
					"uid" => logged_id,
					"name" => $request["name"],
					"format" => $request["format"]
				];

				if($this->system->create("templates", $filtered)):
					$this->cache->container("messages." . logged_hash);
					$this->cache->clear();

					response(200, __("lang_response_template_added"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "add.contact":
        		if(!isset($request["name"], $request["phone"], $request["groups"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

        		$subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

				if(empty($subscription))
					response(500, __("lang_response_package_nosubwarn"));

				if(limitation($subscription["contact_limit"], $this->system->countContacts(logged_id)))
    				response(500, __("lang_response_limitation_contact"));

        		try {
				    $number = $this->phone->parse($request["phone"], logged_country);

					$number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

				    if (!$number->isValidNumber())
						response(500, __("lang_response_invalid_number"));

					if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
						response(500, __("lang_response_invalid_number"));

					$request["phone"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
				} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
					response(500, __("lang_response_invalid_number"));
				}

				if(!is_array($request["groups"]))
					response(500, __("lang_response_invalid"));

    			foreach($request["groups"] as $group):
    				if($this->system->checkGroup(logged_id, $group) < 1)
						response(500, __("lang_response_invalid"));
    			endforeach;

				if($this->system->checkNumber(logged_id, $request["phone"]) > 0)
					response(500, __("lang_response_number_exist"));

				$filtered = [
					"uid" => logged_id,
					"groups" => implode(",", $request["groups"]),
					"phone" => $request["phone"],
					"name" => $request["name"]
				];

				if($this->system->create("contacts", $filtered)):
					$this->cache->container("autocomplete.contacts." . logged_hash);
        			$this->cache->clear();
        			$this->cache->container("contacts." . logged_hash);
        			$this->cache->clear();
        			$this->cache->container("user." . logged_hash);
					$this->cache->clear();

					response(200, __("lang_response_contact_added"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "import.contacts":
        		if(!isset($_FILES["excel"]))
        			response(500, __("lang_response_invalid"));

        		$subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

				if(empty($subscription))
					response(500, __("lang_response_package_nosubwarn"));

        		try {
        			$this->upload->upload($_FILES["excel"]);
	    			if($this->upload->uploaded):
	    				if(!in_array($this->upload->file_src_name_ext, ["xlsx"]))
	    					response(500, __("lang_response_invalid_excel"));

	    				$this->upload->mime_check = false;
		                $this->upload->file_new_name_body = logged_hash;
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/sheets/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, __("lang_response_invalid_excel"));
					endif;
        		} catch(Exception $e){
        			response(500, __("lang_response_invalid_excel"));
        		}

        		try {
        			$reader = $this->sheet->read("uploads/sheets/" . logged_hash . ".xlsx");
        		} catch(Exception $e){
        			response(500, __("lang_response_invalid_excel"));
        		}

        		$countIndex = 0;

        		foreach($reader->getSheetIterator() as $sheet):
				    if($sheet->getIndex() === 0):
				    	$headers = [];
				    	$rowIndex = 0;

				        foreach($sheet->getRowIterator() as $row):
			        		if($rowIndex < 1):
			        			if(!in_array("phone", $row->toArray()))
			        				response(500, ___(__("lang_response_importcontacts_phonenotfound"), ["phone"]));

			        			if(!in_array("groups", $row->toArray()))
			        				response(500, ___(__("lang_response_importcontacts_groupsnotfound"), ["groups"]));

			        			if(!in_array("name", $row->toArray()))
			        				response(500, ___(__("lang_response_importcontacts_namenotfound"), ["name"]));

					        	foreach($row->toArray() as $key => $value):
					        		$headers[$value] = $key;
					        	endforeach;
					        else:
					        	$singleHeader = [];
					        	$cols = $row->toArray();

					        	foreach($headers as $key => $value):
					        		if(!in_array($key, ["phone", "groups", "name"])):
						        		$singleHeader[$key] = $cols[$value];
						        	endif;
					        	endforeach;

					        	$phone = $cols[$headers["phone"]];
					        	$groups = $cols[$headers["groups"]];
					        	$name = $cols[$headers["name"]];

				        		$rejected = false;				        		

				        		try {
								    $number = $this->phone->parse($phone, logged_country);

			    					$number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

								    if(!$number->isValidNumber())
										$rejected = true;

									if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
										$rejected = true;

									$phoneNumber = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
								} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
									$rejected = true;
								}

								if(empty($groups))
									$rejected = true;

								$groupsArray = explode(",", $groups);

								if(!empty($groupsArray)):
									foreach($groupsArray as $group):
										if($this->system->checkGroup(logged_id, $group) < 1)
											$rejected = true;
									endforeach;
								endif;

								if(!$rejected):
									if(limitation($subscription["contact_limit"], $this->system->countContacts(logged_id))):
						        		$this->cache->container("user." . logged_hash);
										$this->cache->clear();

						        		response(500, ___(__("lang_response_limitation_contact2"), [
						        			$countIndex
						        		]));
						        	endif;

						        	if($this->system->checkNumber(logged_id, $phoneNumber) < 1):
										$filtered = [
						        			"uid" => logged_id,
						        			"groups" => $groups,
						        			"phone" => $phoneNumber,
						        			"name" => $name
						        		];

						        		$this->system->create("contacts", $filtered);

						        		$countIndex++;
					        		endif;
					        	endif;
					        endif;

					        $rowIndex++;
				        endforeach;
				    endif;	 
				endforeach;

				$reader->close();

				$this->cache->container("autocomplete.contacts." . logged_hash);
    			$this->cache->clear();
    			$this->cache->container("contacts." . logged_hash);
    			$this->cache->clear();
    			$this->cache->container("user." . logged_hash);
				$this->cache->clear();

				response(200, ___(__("lang_response_contacts_imported2"), [
        			$countIndex
        		]));

        		break;
        	case "add.group":
        		if(!isset($request["name"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				$filtered = [
					"uid" => logged_id,
					"name" => $request["name"]
				];

				if($this->system->create("groups", $filtered)):
					$this->cache->container("contacts." . logged_hash);
					$this->cache->clear();
					$this->cache->container("groups." . logged_hash);
					$this->cache->clear();

					response(200, __("lang_response_group_added"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "add.apikey":
        		if(!isset($request["name"], $request["permissions"]))
        			response(500, __("lang_response_invalid"));

        		$subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

				if(empty($subscription))
					response(500, __("lang_response_package_nosubwarn"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

        		if(!is_array($request["permissions"]))
        			response(500, __("lang_response_invalid"));

        		if(empty($request["permissions"]))
        			response(500, __("lang_response_permission_min"));

        		if(limitation($subscription["key_limit"], $this->system->countKeys(logged_id)))
    				response(500, __("lang_response_limitation_key"));

        		foreach($request["permissions"] as $permission):
        			if(!in_array($permission, [
        				"otp",
        				"sms_send",
        				"sms_send_bulk",
        				"wa_send",
        				"wa_send_bulk",
        				"ussd",
        				"get_credits",
        				"get_earnings",
        				"get_subscription",
        				"get_sms_pending",
        				"get_wa_pending",
        				"get_sms_received",
        				"get_wa_received",
        				"get_sms_sent",
        				"get_sms_campaigns",
        				"get_wa_sent",
        				"get_wa_campaigns",
        				"get_contacts",
        				"get_groups",
        				"get_ussd",
        				"get_notifications",
        				"get_wa_accounts",
        				"get_devices",
        				"get_rates",
        				"get_shorteners",
        				"get_unsubscribed",
        				"create_contact",
        				"create_group",
        				"start_sms_campaign",
        				"stop_sms_campaign",
        				"start_wa_campaign",
        				"stop_wa_campaign",
        				"delete_contact",
        				"delete_group",
        				"delete_sms_sent",
        				"delete_sms_campaign",
        				"delete_wa_sent",
        				"delete_wa_campaign",
        				"delete_sms_received",
        				"delete_wa_received",
        				"delete_ussd",
        				"delete_unsubscribed",
        				"delete_notification"
        			])):
        				response(500, __("lang_response_invalid"));
        			endif;
        		endforeach;

				$filtered = [
					"uid" => logged_id,
					"secret" => sha1(uniqid(time() . logged_hash, true)),
					"name" => $request["name"],
					"permissions" => implode(",", $request["permissions"])
				];

				if($this->system->create("keys", $filtered)):
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();

					response(200, __("lang_response_key_added"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "add.webhook":
        		if(!isset($request["name"], $request["events"], $request["url"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				if(!is_array($request["events"]))
        			response(500, __("lang_response_invalid"));

        		foreach($request["events"] as $event):
        			if(!in_array($event, ["sms", "whatsapp", "ussd", "notifications"]))
        				response(500, __("lang_response_invalid"));
        		endforeach;

        		if(!$this->sanitize->isUrl($request["url"]))
        			response(500, __("lang_response_invalid_webhookurl"));

        		$subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

				if(empty($subscription))
					response(500, __("lang_response_package_nosubwarn"));

        		if(limitation($subscription["webhook_limit"], $this->system->countWebhooks(logged_id)))
    				response(500, __("lang_response_limitation_webhook"));

				$filtered = [
					"uid" => logged_id,
					"secret" => sha1(uniqid(time() . logged_id, true)),
					"name" => $request["name"],
					"url" => $this->sanitize->url($request["url"]),
					"events" => implode(",", $request["events"])
				];

				if($this->system->create("webhooks", $filtered)):
					response(200, __("lang_response_webhook_added"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "add.hook":
        		if(!isset($request["name"], $request["source"], $request["event"], $request["link"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

        		if(!$this->sanitize->isInt($request["source"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->isInt($request["event"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->isUrl($request["link"]))
        			response(500, __("lang_response_invalid_linkstructure"));

				$filtered = [
					"uid" => logged_id,
					"type" => 1,
					"name" => $request["name"],
					"source" => $request["source"] < 2 ? 1 : 2,
					"event" => $request["event"] < 2 ? 1 : 2,
					"link" => $request["link"],
					"message" => false,
					"keywords" => false
				];

				if($this->system->create("actions", $filtered)):
					response(200, __("lang_response_hook_added"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "add.autoreply":
        		if(!isset($request["name"], $request["source"], $request["keywords"], $request["message"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				if(!$this->sanitize->isInt($request["source"]))
        			response(500, __("lang_response_invalid"));

				if(!$this->sanitize->length($request["keywords"]))
					response(500, __("lang_response_invalid_keywords"));

				if(!$this->sanitize->length($request["message"]))
					response(500, __("lang_response_message_short"));

				$filtered = [
					"uid" => logged_id,
					"type" => 2,
					"source" => $request["source"] < 2 ? 1 : 2,
					"name" => $request["name"],
					"event" => 0,
					"keywords" => $request["keywords"],
					"message" => $request["message"],
					"link" => false
				];

				if($this->system->create("actions", $filtered)):
					response(200, __("lang_response_autoreply_added"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "add.user":
        		if(!permission("manage_users"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["name"], $request["email"], $request["password"], $request["timezone"], $request["country"], $request["alertsound"], $request["role"], $request["language"], $request["credits"], $request["partner"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

	            if(!$this->sanitize->isEmail($request["email"]))
	            	response(500, __("lang_response_invalid_email"));

	            if(!$this->sanitize->length($request["password"], 5))
	            	response(500, __("lang_response_password_short"));

	            if(!$this->sanitize->isInt($request["role"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->isInt($request["language"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->isInt($request["alertsound"]))
	            	response(500, __("lang_response_invalid"));

	            if(!empty($request["credits"])):
		            if(!$this->sanitize->isNumeric($request["credits"]) || $request["credits"] < 0):
		            	response(500, __("lang_response_invalid"));
		            endif;
		        else:
		        	$request["credits"] = 0;
		        endif;

	            if(!$this->sanitize->isInt($request["partner"]))
	            	response(500, __("lang_response_invalid"));

	            if($this->system->checkRole($request["role"]) < 1)
	            	response(500, __("lang_response_invalid"));

	            if($this->system->checkLanguage($request["language"]) < 1)
	            	response(500, __("lang_response_invalid"));

	            if(!in_array($request["timezone"], $this->timezones->generate()))
	            	response(500, __("lang_response_invalid"));

	            if(!array_key_exists($request["country"], \CountryCodes::get("alpha2", "country")))
	            	response(500, __("lang_response_invalid"));

            	$filtered = [
            		"country" => $request["country"],
            		"role" => $request["role"],
            		"name" => $request["name"],
            		"language" => $request["language"],
            		"email" => $this->sanitize->email($request["email"]),
            		"credits" => $request["credits"],
            		"earnings" => 0,
            		"suspended" => 0,
            		"providers" => false,
            		"timezone" => false,
            		"country" => $request["country"],
            		"timezone" => strtolower($request["timezone"]),
            		"formatting" => false,
            		"alertsound" => $request["alertsound"] < 2 ? 1 : 2,
            		"credits" => $request["credits"],
            		"partner" => $request["partner"] < 2 ? 1 : 2,
            		"confirmed" => 1,
            		"password" => password_hash($request["password"], PASSWORD_DEFAULT)
            	];

            	if($this->system->checkEmail($filtered["email"]) < 1):
            		$create = $this->system->create("users", $filtered);

            		if($create):
            			$this->cache->container("system.users");
        				$this->cache->clear();

            			response(200, __("lang_response_user_added"));
            		else:
            			response(500, __("lang_response_went_wrong"));
            		endif;
            	else:
            		response(500, __("lang_response_email_unavailable"));
            	endif;

				break;
			case "add.role":
				if(!permission("manage_roles"))
					response(500, __("lang_response_no_permission"));

        		if(!isset($request["name"], $request["permissions"]))
        			response(500, __("lang_response_invalid"));

        		if(!is_array($request["permissions"]))
        			response(500, __("lang_response_invalid"));

        		if(empty($request["permissions"]))
        			response(500, __("lang_response_permission_min"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

        		foreach($request["permissions"] as $permission):
        			if(!in_array($permission, [
				        "manage_users",
				        "manage_roles",
				        "manage_packages",
				        "manage_vouchers",
				        "manage_subscriptions",
				        "manage_transactions",
				        "manage_payouts",
				        "manage_widgets",
				        "manage_pages",
				        "manage_marketing",
				        "manage_languages",
				        "manage_gateways",
				        "manage_shorteners",
				        "manage_plugins",
				        "manage_templates",
				        "manage_api"
        			])):
        				response(500, __("lang_response_invalid"));
        			endif;
        		endforeach;

				$filtered = [
					"name" => $request["name"],
					"permissions" => implode(",", $request["permissions"])
				];

				if($this->system->create("roles", $filtered)):
					$this->cache->container("system.roles");
					$this->cache->clear();
					
					response(200, __("lang_role_added"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
			case "add.package":
				if(!permission("manage_packages"))
					response(500, __("lang_response_no_permission"));

				$columns = [
					"send_limit",
					"receive_limit",
					"ussd_limit",
					"notification_limit",
					"contact_limit",
					"device_limit",
					"key_limit",
					"webhook_limit",
					"action_limit",
					"scheduled_limit",
					"wa_send_limit",
					"wa_receive_limit",
					"wa_account_limit",
					"name",
					"price",
					"footermark",
					"hidden"
				];

				foreach($columns as $column):
					if(!isset($request[$column])):
						response(500, __("lang_response_invalid"));
					endif;

					if(!in_array($column, ["name"])):
						if(!$this->sanitize->isInt($request[$column])):
							response(500, __("lang_requests_packageform_invalidint"));
						endif;
					endif;
				endforeach;

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				if($request["price"] < 1)
					response(500, __("lang_response_package_pricenotlessone"));

				$request["footermark"] = $request["footermark"] < 2 ? 1 : 2;
				$request["hidden"] = $request["hidden"] < 2 ? 1 : 2;

				$filtered = [];

            	foreach($columns as $column):
            		$filtered[$column] = $request[$column];
            	endforeach;

            	if($this->system->create("packages", $filtered)):
            		foreach($this->system->getUsers() as $user):
    					$this->cache->container("user.{$user["hash"]}");
						$this->cache->clear();
        			endforeach;

            		$this->cache->container("system.packages");
            		$this->cache->clear();

        			response(200, __("lang_response_package_added"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "add.voucher":
				if(!permission("manage_vouchers"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["name"], $request["count"], $request["duration"], $request["package"]))
	            	response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["count"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["duration"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["package"]))
					response(500, __("lang_response_invalid"));

				if($request["count"] < 1 || $request["count"] > 1000)
					response(500, __("lang_response_invalid"));

				if($request["duration"] < 1)
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				if($request["package"] < 2)
                    response(500, __("lang_response_invalid"));
                
				if($this->system->checkPackage($request["package"]) < 1)
	            	response(500, __("lang_response_invalid"));

	            if($request["count"] < 2):
	            	$filtered = [
	            		"code" => md5(uniqid(time(), true)),
	            		"name" => $request["name"],
	            		"package" => $request["package"],
	            		"duration" => $request["duration"]
	            	];

	            	if($this->system->create("vouchers", $filtered)):
	        			response(200, __("lang_voucher_added"));
	        		else:
	        			response(500, __("lang_response_went_wrong"));
	        		endif;
	            else:
	            	for($i = 1; $i <= $request["count"]; $i++):
	            		$filtered = [
		            		"code" => md5(uniqid(time() . "_{$i}", true)),
		            		"name" => $request["name"] . " #{$i}",
		            		"package" => $request["package"],
		            		"duration" => $request["duration"]
		            	];

		            	$this->system->create("vouchers", $filtered);
	            	endfor;

					response(200, __("lang_voucher_added"));
	            endif;

				break;
			case "add.subscription":
				if(!permission("manage_subscriptions"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["user"], $request["package"], $request["duration"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->isInt($request["user"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["package"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["duration"]))
					response(500, __("lang_response_invalid"));

				if($this->system->checkUser($request["user"]) < 1)
	            	response(500, __("lang_response_invalid"));

    			if($this->system->checkPackage($request["package"]) < 1)
	            	response(500, __("lang_response_invalid"));

	            $package = $this->system->getPackage($request["package"]);

	            $transaction = $this->system->create("transactions", [
					"uid" => $request["user"],
					"pid" => $request["package"],
					"type" => 1,
					"price" => $package["price"],
					"currency" => system_currency,
					"duration" => $request["duration"],
					"provider" => "manual"
				]);

            	$filtered = [
            		"uid" => $request["user"],
            		"pid" => $request["package"],
            		"tid" => $transaction
            	];

            	$this->system->delete($filtered["uid"], false, "subscriptions");

        		if($this->system->create("subscriptions", $filtered)):
        			$this->cache->container("system.packages");
        			$this->cache->clear();
        			$this->cache->container("system.transactions");
        			$this->cache->clear();

        			response(200, __("lang_response_subscription_addedsuccess"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "add.widget":
				if(!permission("manage_widgets"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["name"], $request["icon"], $request["type"], $request["size"], $request["position"], $request["content"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

	            if(!$this->sanitize->isInt($request["type"]))
	            	response(500, __("lang_response_invalid"));

	           	if(!in_array($request["type"], [1, 2]))
	           		response(500, __("lang_response_invalid"));

	           	if(!in_array($request["size"], ["sm", "md", "lg", "xl"]))
	           		response(500, __("lang_response_invalid"));

	           	if(!in_array($request["position"], ["center", "left", "right"]))
	           		response(500, __("lang_response_invalid"));

            	$filtered = [
            		"icon" => $request["icon"],
            		"name" => $request["name"],
            		"type" => $request["type"],
            		"size" => $request["size"],
            		"position" => $request["position"],
            		"content" => $this->sanitize->htmlEncode($request["content"])
            	];

            	if($this->system->create("widgets", $filtered)):
					$this->cache->container("system.blocks");
					$this->cache->clear();
					$this->cache->container("system.modals");
					$this->cache->clear();

        			response(200, __("lang_response_widget_added"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "add.page":
				if(!permission("manage_pages"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["name"], $request["roles"], $request["logged"], $request["content"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

	            if(!is_array($request["roles"]))
	            	response(500, __("lang_response_invalid"));

	            if(empty($request["roles"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->isInt($request["logged"]))
					response(500, __("lang_response_invalid"));

				if(!in_array($request["logged"], [1, 2]))
					response(500, __("lang_response_invalid"));

            	$filtered = [
            		"slug" => $this->slug->create($request["name"]),
            		"logged" => $request["logged"],
            		"name" => $request["name"],
            		"roles" => implode(",", $request["roles"]),
            		"content" => $this->sanitize->htmlEncode($request["content"])
            	];

            	if($this->system->create("pages", $filtered)):
            		$this->cache->container("system.pages");
            		$this->cache->clear();

        			response(200, __("lang_response_page_added"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "add.push":
				if(!permission("manage_marketing"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["title"], $request["message"], $request["users"], $request["roles"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["title"]))
					response(500, __("lang_response_push_titletooshort"));

				if(!$this->sanitize->length($request["message"]))
					response(500, __("lang_response_push_mesgtooshort"));

	            if(!is_array($request["users"]))
	            	response(500, __("lang_response_invalid"));

	            if(!is_array($request["roles"]))
	            	response(500, __("lang_response_invalid"));

	            if(in_array("0", $request["users"]) && in_array("0", $request["roles"]))
	            	response(500, __("lang_response_push_selectatleastoneuser"));

	            $image = uniqid(time() . "push", true);

				if(isset($_FILES["image"])):
        			$this->upload->upload($_FILES["image"]);
        			if($this->upload->uploaded):
        				$this->upload->allowed = [
        					"image/*"
        				];

		                $this->upload->file_new_name_body = $image;
		                $this->upload->image_convert = "png";
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/temporary/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, __("lang_response_push_imgfailed"));
					endif;
	            endif;

	            $users = [];

	            if(!empty($request["users"]) && !in_array("0", $request["users"])):
		            foreach($request["users"] as $user):
	        			$users[] = $user;
	        		endforeach;
	        	endif;

	            if(!empty($request["roles"]) && !in_array("0", $request["roles"])):
		            foreach($request["roles"] as $role):
	        			$roleUsers = $this->system->getUsersByRole($role);

	        			if(!empty($roleUsers)):
		        			foreach($roleUsers as $user):
		        				$users[] = $user["id"];
		        			endforeach;
		        		endif;
	        		endforeach;
	        	endif;

	        	if(!empty($users)):
	            	$filtered = [
	            		"type" => 1,
	            		"users" => implode(",", $request["users"]),
	            		"roles" => implode(",", $request["roles"]),
	            		"title" => $request["title"],
	            		"content" => $request["message"],
	            		"image" => $this->file->exists("uploads/temporary/{$image}.png") ? $image : false
	            	];

	            	if($this->system->create("marketing", $filtered)):
		        		foreach($users as $user):
							if($this->file->exists("uploads/temporary/{$image}.png")):
								$this->fcm->send($this->hash->encode($user, system_token), [
									"type" => "push",
							    	"title" => $filtered["title"],
							    	"content" => $filtered["content"],
							    	"image" => site_url("uploads/temporary/{$image}.png", true)
								], [
							    	"title" => $filtered["title"],
							    	"body" => $filtered["content"],
							    	"image" => site_url("uploads/temporary/{$image}.png", true)
							    ]);
							else:
								$this->fcm->send($this->hash->encode($user, system_token), [
									"type" => "push",
							    	"title" => $filtered["title"],
							    	"content" => $filtered["content"],
							    	"image" => "false"
								], [
							    	"title" => $filtered["title"],
							    	"body" => $filtered["content"]
							    ]);
							endif;
						endforeach;
	        		else:
	        			response(500, __("lang_response_went_wrong"));
	        		endif;

        			response(200, __("lang_response_push_created"));
	        	else:
	        		response(500, __("lang_response_push_nousers"));
	        	endif;

				break;
			case "add.notify":
				if(!permission("manage_marketing"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["title"], $request["message"], $request["color"], $request["users"], $request["roles"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["title"]))
					response(500, __("lang_response_notify_titletooshort"));

				if(!$this->sanitize->length($request["message"]))
					response(500, __("lang_response_notify_mesgtooshort"));

				if(!in_array($request["color"], [1, 2, 3, 4]))
					response(500, __("lang_response_invalid"));

	            if(!is_array($request["users"]))
	            	response(500, __("lang_response_invalid"));

	            if(!is_array($request["roles"]))
	            	response(500, __("lang_response_invalid"));

	            if(in_array("0", $request["users"]) && in_array("0", $request["roles"]))
	            	response(500, __("lang_response_notify_selectuserrole"));

	            $image = uniqid(time() . "notify", true);

	            if(isset($_FILES["image"])):
        			$this->upload->upload($_FILES["image"]);
        			if($this->upload->uploaded):
        				$this->upload->allowed = [
        					"image/*"
        				];	

		                $this->upload->file_new_name_body = $image;
		                $this->upload->image_convert = "png";
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/temporary/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, __("lang_response_notify_imgfailed"));
					endif;
	            endif;

	            $users = [];

	            if(!empty($request["users"]) && !in_array("0", $request["users"])):
		            foreach($request["users"] as $user):
	        			$users[] = $user;
	        		endforeach;
	        	endif;

	            if(!empty($request["roles"]) && !in_array("0", $request["roles"])):
		            foreach($request["roles"] as $role):
	        			$roleUsers = $this->system->getUsersByRole($role);

	        			if(!empty($roleUsers)):
		        			foreach($roleUsers as $user):
		        				$users[] = $user["id"];
		        			endforeach;
		        		endif;
	        		endforeach;
	        	endif;

	        	if(!empty($users)):
	            	$filtered = [
	            		"type" => 2,
	            		"users" => implode(",", $request["users"]),
	            		"roles" => implode(",", $request["roles"]),
	            		"title" => $request["title"],
	            		"content" => $request["message"],
	            		"image" => $this->file->exists("uploads/temporary/{$image}.png") ? $image : false
	            	];

	            	if($this->system->create("marketing", $filtered)):
		        		try {
		        			$this->echo->_cache = $this->cache;
							$this->echo->_guzzle = $this->guzzle;

							$echoToken = $this->echo->token();
		        		} catch(Exception $e){
		        			response(500, __("lang_response_titanecho_invaltoken"));
		        		}

		        		if($echoToken):
							$this->echo->notify("broadcast", [
								"color" => $request["color"],
								"title" => $filtered["title"],
								"content" => $filtered["content"],
								"image" => $this->file->exists("uploads/temporary/{$image}.png") ? site_url("uploads/temporary/{$image}.png", true) : false,
								"recipients" => $users
							], true);
						endif;

		        		response(200, __("lang_response_notify_created"));
	        		else:
	        			response(500, __("lang_response_went_wrong"));
	        		endif;
        		else:
	        		response(500, __("lang_response_notify_nousers"));
	        	endif;

				break;
			case "add.mailer":
				if(!permission("manage_marketing"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["title"], $request["content"], $request["users"], $request["roles"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["title"]))
					response(500, __("lang_response_mailer_titletooshort"));

				if(!$this->sanitize->length($request["content"]))
					response(500, __("lang_response_mailer_contentooshort"));

	            if(!is_array($request["users"]))
	            	response(500, __("lang_response_invalid"));

	            if(!is_array($request["roles"]))
	            	response(500, __("lang_response_invalid"));

	            if(in_array("0", $request["users"]) && in_array("0", $request["roles"]))
	            	response(500, __("lang_response_mailer_nouserorrole"));

	            $users = [];

	            if(!empty($request["users"]) && !in_array("0", $request["users"])):
		            foreach($request["users"] as $user):
	        			$users[] = $this->system->getUser($user);
	        		endforeach;
	        	endif;

	            if(!empty($request["roles"]) && !in_array("0", $request["roles"])):
		            foreach($request["roles"] as $role):
	        			$roleUsers = $this->system->getUsersByRole($role);

	        			if(!empty($roleUsers)):
		        			foreach($roleUsers as $user):
		        				$users[] = $this->system->getUser($user["id"]);
		        			endforeach;
		        		endif;
	        		endforeach;
	        	endif;

	        	if(!empty($users)):
	            	$filtered = [
	            		"type" => 3,
	            		"users" => implode(",", $request["users"]),
	            		"roles" => implode(",", $request["roles"]),
	            		"title" => $request["title"],
	            		"content" => $request["content"],
	            		"image" => false
	            	];

	            	if($this->system->create("marketing", $filtered)):
			        	$sendIndex = 1;
			        	$sendTotal = count($users);

			        	foreach($users as $user):
							try {
								$this->echo->_cache = $this->cache;
								$this->echo->_guzzle = $this->guzzle;

								$echoToken = $this->echo->token();
							} catch(Exception $e){
								response(500, __("lang_response_titanecho_invaltoken"));
							}

							if($this->mail->send([
								"title" => system_site_name,
								"data" => [
									"subject" => mail_title($request["title"])
								]
							], $user["email"], "string: {$request["content"]}", $this->smarty)):
								if($echoToken):
									$this->echo->notify(logged_hash, [
										"type" => "notification",
										"status" => 1,
										"content" => ___(__("lang_response_mailer_sendcounter"), [$sendIndex, $sendTotal])
									], true);
								endif;
							endif;

							$sendIndex++;
			        	endforeach;
	        		else:
	        			response(500, __("lang_response_went_wrong"));
	        		endif;

    				response(200, __("lang_response_mailer_mailprocessed"));
		        else:
		        	response(500, __("lang_response_mailer_nousersorrole"));
		        endif;

				break;
			case "add.language":
				if(!permission("manage_languages"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["name"], $request["iso"], $request["rtl"], $request["translations"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

	            if(!array_key_exists($request["iso"], \CountryCodes::get("alpha2", "country")))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->isInt($request["rtl"]))
	            	response(500, __("lang_response_invalid"));

	            if(!in_array($request["rtl"], [1, 2]))
	            	response(500, __("lang_response_invalid"));

            	$filtered = [
            		"name" => $request["name"],
            		"rtl" => $request["rtl"],
            		"iso" => strtoupper($request["iso"]),
            		"order" => 0
            	];

            	$create = $this->system->create("languages", $filtered);

            	if($create):
            		$this->cache->container("system.languages");
            		$this->cache->clear();

            		$this->file->put("system/languages/" . md5($create) . ".lang", $request["translations"]);

        			response(301, __("lang_response_language_added"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "add.gateway":
				if(!permission("manage_gateways"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["name"], $request["callback"], $request["callback_id"], $request["content"], $_FILES["controller"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->isInt($request["callback"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				if($_FILES["controller"]["error"] == 4)
					response(500, __("lang_response_gateway_controllercannotempty"));

            	$filtered = [
            		"name" => $request["name"],
            		"callback" => $request["callback"] < 2 ? 1 : 2,
            		"callback_id" => $request["callback_id"],
            		"pricing" => $request["content"]
            	];

            	$create = $this->system->create("gateways", $filtered);

            	if($create):
            		try {
	        			$this->upload->upload($_FILES["controller"]);
	        			
		    			if($this->upload->uploaded):
		    				if(!in_array($this->upload->file_src_name_ext, ["php"]))
		    					response(500, "Invalid controller file!");

			                $this->upload->file_new_name_body = md5($create);
			                $this->upload->file_new_name_ext = "php";
			                $this->upload->file_overwrite = true;
			                $this->upload->process("system/gateways/");

							if($this->upload->processed)
								$this->upload->clean();
							else
								response(500, __("lang_response_gateway_invalidcontroller"));
						endif;
	        		} catch(Exception $e){
	        			response(500, __("lang_response_gateway_invalidcontroller"));
	        		}

        			response(200, __("lang_response_gateway_addeddcontroller"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "add.shortener":
				if(!permission("manage_shorteners"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["name"], $_FILES["controller"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				if($_FILES["controller"]["error"] == 4)
					response(500, __("lang_response_shortener_cannotempty"));

            	$filtered = [
            		"name" => $request["name"]
            	];

            	$create = $this->system->create("shorteners", $filtered);

            	if($create):
            		try {
	        			$this->upload->upload($_FILES["controller"]);
	        			
		    			if($this->upload->uploaded):
		    				if(!in_array($this->upload->file_src_name_ext, ["php"]))
		    					response(500, "Invalid controller file!");

			                $this->upload->file_new_name_body = md5($create);
			                $this->upload->file_new_name_ext = "php";
			                $this->upload->file_overwrite = true;
			                $this->upload->process("system/shorteners/");

							if($this->upload->processed)
								$this->upload->clean();
							else
								response(500, __("lang_response_shortener_invalidcontroller"));
						endif;
	        		} catch(Exception $e){
	        			response(500, __("lang_response_shortener_invalidcontroller"));
	        		}

        			response(200, __("lang_response_shortener_addedcontroller"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "add.plugin":
				if(!permission("manage_plugins"))
					response(500, __("lang_response_no_permission"));

				if(!isset($_FILES["plugin"]))
        			response(500, __("lang_response_invalid"));

	            $this->upload->upload($_FILES["plugin"]);
    			if($this->upload->uploaded):
    				if(!in_array($this->upload->file_src_name_ext, ["zip"]))
	    				response(500, __("lang_response_invalid"));

	                $this->upload->file_new_name_body = "plugin";
	                $this->upload->file_overwrite = true;
	                $this->upload->process("uploads/temporary/");

					if($this->upload->processed)
						$this->upload->clean();
				endif;

				if(!$this->file->exists("uploads/temporary/plugin.zip"))
					response(500, __("lang_response_pluginwentwrong"));

				try {
					rmrf("uploads/temporary/plugin");
					mkdir("uploads/temporary/plugin");
				} catch(Exception $e){
					// Ignore
				}

				try {
					$zip = $this->zippy->open("uploads/temporary/plugin.zip")->extract("uploads/temporary/plugin");
				} catch(Exception $e){
					$this->file->put("system/storage/temporary/error.log", "Plugin Install Error: {$e->getMessage()} (" . date("m/d/Y g:i A") . ")\n\n", FILE_APPEND);
				}

				if(!$this->file->exists("uploads/temporary/plugin/install.json"))
					response(500, __("lang_response_pluginwentwrong"));

				try {
					copyDirectory("uploads/temporary/plugin/files/", "./");
				} catch(Exception $e){
					// Ignore
				}

				$installData = json_decode($this->file->get("uploads/temporary/plugin/install.json"), true);

            	$filtered = [
            		"name" => $installData["id"],
            		"data" => [
            			"name" => $installData["name"],
            			"data" => $installData["data"]
            		]
            	];

            	if($this->system->checkPlugin($installData["id"]) > 0)
            		response(500, __("lang_response_plugin_samenameinstalled"));

            	if(array_key_exists("actions", $installData)):
            		$filtered["data"]["actions"] = $installData["actions"];
            	endif;

            	if(array_key_exists("version", $installData)):
            		$filtered["data"]["version"] = $installData["version"];
            	endif;

            	$filtered["data"] = json_encode($filtered["data"]);

            	if($this->system->create("plugins", $filtered)):
            		$this->cache->container("system.plugins");
            		$this->cache->clear();

					try {
						rmrf("uploads/temporary/plugin");
					} catch(Exception $e){
						// Ignore
					}

        			response(200, __("lang_response_plugininstalled"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "system.update":
				if(!super_admin)
					response(500, __("lang_response_no_permission"));

				if(!isset($_FILES["update"]))
        			response(500, __("lang_response_invalid"));

	            $this->upload->upload($_FILES["update"]);

    			if($this->upload->uploaded):
    				if(!in_array($this->upload->file_src_name_ext, ["zip"]))
	    				response(500, __("lang_response_invalid"));

	                $this->upload->file_new_name_body = "update";
	                $this->upload->file_overwrite = true;
	                $this->upload->process("uploads/temporary/");

					if($this->upload->processed)
						$this->upload->clean();
				endif;

				if(!$this->file->exists("uploads/temporary/update.zip"))
					response(500, __("lang_requests_systemupdate_faileduploadingfile"));

				try {
					rmrf("uploads/temporary/update");
					mkdir("uploads/temporary/update");
				} catch(Exception $e){
					// Ignore
				}

				try {
					$zip = $this->zippy->open("uploads/temporary/update.zip")->extract("uploads/temporary/update");
				} catch(Exception $e){
					$this->file->put("system/storage/temporary/error.log", "System Update Error: {$e->getMessage()} (" . date("m/d/Y g:i A") . ")\n\n", FILE_APPEND);
				}

				try {
					copyDirectory("uploads/temporary/update/files/", "./");
				} catch(Exception $e){
					// Ignore
				}

				if($this->file->exists("uploads/temporary/update/instructions.conf")):
					$instructions = explode("\n", $this->file->get("uploads/temporary/update/instructions.conf"));

					if(!empty($instructions)):
						foreach($instructions as $instruction):
							if(trim($instruction) == "database"):
								try {
									new Thamaraiselvam\MysqlImport\Import("uploads/temporary/update/update.sql", env["dbuser"], env["dbpass"], env["dbname"], env["dbhost"], env["dbport"]);
								} catch(Exception $e){
									$this->file->put("system/storage/temporary/error.log", "Update SQL Error: {$e->getMessage()} (" . date("m/d/Y g:i A") . ")\n\n", FILE_APPEND);
								}
							endif;

							if(trim($instruction) == "cache"):
								try {
									rmrf("system/storage/cache");
									mkdir("system/storage/cache");
								} catch(Exception $e){
									// Ignore
								}
							endif;
						endforeach;
					endif;
				endif;

				if($this->file->exists("uploads/temporary/update/remove.conf")):
					$remove = explode("\n", $this->file->get("uploads/temporary/update/remove.conf"));

					if(!empty($remove)):
						foreach($remove as $file):
							try {
								$this->file->delete($file);
							} catch(Exception $e){
								// Ignore
							}
						endforeach;
					endif;
				endif;
				
				try {
					rmrf("uploads/temporary/update");
				} catch(Exception $e){
					// Ignore
				}

				response(200, __("lang_response_update_success"));

				break;
        	default:
        		response(500, __("lang_response_invalid"));
        endswitch;
	}

	public function update()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
            response(302);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        $this->cache->container("system.plugins");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getPlugins());
        endif;

        set_plugins($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        $type = $this->sanitize->string($this->url->segment(4));
        $request = $this->sanitize->array($_POST, 
        	in_array($type, ["edit.widget", "edit.page", "edit.gateway", "admin.theme", "admin.builder", "admin.settings"]) ? ["content", "layout", "script", "css", "bank_template"] : []
    	);

        if(!isset($request["id"]) || !$this->sanitize->isInt($request["id"]))
        	response(500, __("lang_response_invalid"));

        switch($type):
        	case "user.settings":
        		if(!isset($request["name"], $request["email"], $request["password"], $request["timezone"], $request["clock_format"], $request["date_format"], $request["date_separator"], $request["country"], $request["alertsound"], $request["current_email"]))
        			response(500, __("lang_response_invalid"));

        		if($request["id"] != logged_id)
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->isInt($request["alertsound"]))
        			response(500, __("lang_response_invalid"));

        		if(!in_array($request["timezone"], $this->timezones->generate()))
	            	response(500, __("lang_response_invalid"));

	            if(!in_array($request["clock_format"], [1, 2]))
	            	response(500, __("lang_response_invalid"));

	            if(!in_array($request["date_format"], [1, 2, 3, 4]))
	            	response(500, __("lang_response_invalid"));

	            if(!in_array($request["date_separator"], [1, 2, 3, 4]))
	            	response(500, __("lang_response_invalid"));

	            if(!array_key_exists($request["country"], \CountryCodes::get("alpha2", "country")))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
        			response(500, __("lang_response_name_short"));

        		if(!$this->sanitize->isEmail($request["email"]))
        			response(500, __("lang_response_invalid_email"));

        		if($request["current_email"] != $request["email"] && $this->system->checkEmail($request["email"]) > 0)
	            	response(500, __("lang_response_email_unavailable"));

	            $date_separator = [
	            	1 => "-",
	            	2 => "/",
	            	3 => ".",
	            	4 => " "
	            ];

	            $date_format = [
	            	1 => "n{$date_separator[$request["date_separator"]]}j{$date_separator[$request["date_separator"]]}Y",
	            	2 => "j{$date_separator[$request["date_separator"]]}n{$date_separator[$request["date_separator"]]}Y",
	            	3 => "Y{$date_separator[$request["date_separator"]]}n{$date_separator[$request["date_separator"]]}j",
	            	4 => "Y{$date_separator[$request["date_separator"]]}j{$date_separator[$request["date_separator"]]}n"
	            ];

        		$filtered = [
        			"name" => $request["name"],
        			"timezone" => strtolower($request["timezone"]),
        			"formatting" => json_encode([
        				"clock" => $request["clock_format"] < 2 ? "g:i A" : "H:i",
        				"date" => $date_format[$request["date_format"]],
        				"container" => [
        					"clock_format" => (int) $request["clock_format"],
        					"date_format" => (int) $request["date_format"],
        					"date_separator" => (int) $request["date_separator"],
        					"separator_selected" => $date_separator[$request["date_separator"]]
        				]
        			]),
        			"country" => strtoupper($request["country"]),
        			"alertsound" => $request["alertsound"] < 2 ? 1 : 2,
        			"email" => $this->sanitize->email($request["email"])
        		];

        		if(!empty($request["password"])):
        			if(!$this->sanitize->length($request["password"], 5))
        				response(500, __("lang_response_password_short"));
        			else
        				$filtered["password"] = password_hash($request["password"], PASSWORD_DEFAULT);
        		endif;

        		if(isset($_FILES["avatar"])):
        			$this->upload->upload($_FILES["avatar"]);
        			if($this->upload->uploaded):
        				$this->upload->allowed = [
        					"image/*"
        				];

		                $this->upload->file_new_name_body = logged_hash;
		                $this->upload->image_convert = "jpg";
						$this->upload->image_resize = true;
						$this->upload->image_x = 150;
						$this->upload->image_ratio_y = true;
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/avatars/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, __("lang_response_avatar_invalid"));
					endif;
	            endif;

        		if($this->system->update($request["id"], false, "users", $filtered)):
        			$this->session->set(
        				"logged", 
        				$this->system->getUser(logged_id)
        			);

        			response(301, __("lang_response_profile_updated"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

        		break;
			case "edit.sms.scheduled":
				if(!isset($request["mode"], $request["schedule"], $request["repeat"], $request["numbers"], $request["groups"], $request["message"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->isInt($request["mode"]))
					response(500, __("lang_response_invalid"));

				if(!is_array($request["groups"]))
        			response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["repeat"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->length($request["message"], system_message_min))
					response(500, __("lang_response_message_short"));

				if(system_message_max > 0):
					if($this->sanitize->length($request["message"], system_message_max, 2))
						response(500, __("lang_response_message_toolong"));
				endif;

        		if($request["mode"] < 2):
        			if(!isset(
	        			$request["device"],
	        			$request["sim"]
	        		))
        				response(500, __("lang_response_invalid"));

        			$subscription = set_subscription(
	                    $this->system->checkSubscription(logged_id), 
	                    $this->system->getSubscription(false, logged_id), 
	                    $this->system->getSubscription(false, false, true)
	                );

					if(empty($subscription))
						response(500, __("lang_response_package_nosubwarn"));

					if(!$this->sanitize->isInt($request["sim"]))
						response(500, __("lang_response_invalid"));

	    			if($this->system->checkDevice(logged_id, $request["device"], "did") < 1)
    					response(500, __("lang_response_invalid"));

	    			$device = $this->system->getDevice(logged_id, $request["device"], "did");

	    			if($device):
    					$filtered = [
							"did" => $request["device"],
							"sim" => $request["sim"] < 2 ? 1 : 2,
							"mode" => 1,
							"gateway" => 0,
							"groups" => implode(",", $request["groups"]),
							"name" => $request["name"],
							"numbers" => $request["numbers"],
							"message" => $request["message"],
							"repeat" => $request["repeat"],
							"last_send" => false,
							"send_date" => strtotime($request["schedule"])
						];
		    		else:
		    			response(500, __("lang_response_invalid"));
		    		endif;
        		else:
        			if(!isset($request["gateway"]))
        				response(500, __("lang_response_invalid"));

        			if($this->sanitize->isInt($request["gateway"])):
        				$gateways = $this->system->getGateways();

						if(!array_key_exists($request["gateway"], $gateways)):
							response(500, __("lang_response_invalid"));
						endif;

						$filtered = [
							"did" => false,
							"sim" => 0,
							"mode" => 2,
							"gateway" => $request["gateway"],
							"groups" => implode(",", $request["groups"]),
							"name" => $request["name"],
							"numbers" => $request["numbers"],
							"message" => $request["message"],
							"repeat" => $request["repeat"] < 2 ? 1 : 2,
							"last_send" => false,
							"send_date" => strtotime($request["schedule"])
						];
        			else:
        				$device = $this->system->getDevice(false, $request["gateway"], "global");

		    			if($device):
			    			if($device["global_device"] > 1):
			    				response(500, __("lang_response_invalid"));
			    			else:
			    				$slots = explode(",", $device["global_slots"]);

			    				$filtered = [
									"did" => $request["gateway"],
									"sim" => count($slots) > 1 ? rand(1, 2) : ($slots[0] < 2 ? 1 : 2),
									"mode" => 2,
									"gateway" => 0,
									"groups" => implode(",", $request["groups"]),
									"name" => $request["name"],
									"numbers" => $request["numbers"],
									"message" => $request["message"],
									"repeat" => $request["repeat"] < 2 ? 1 : 2,
									"last_send" => false,
									"send_date" => strtotime($request["schedule"])
								];
			    			endif;
			    		else:
			    			response(500, __("lang_response_invalid"));
			    		endif;
        			endif;
        		endif;

				if($this->system->update($request["id"], logged_id, "scheduled", $filtered)):
					response(200, __("lang_response_smscheduled_editsuccess"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;
			case "edit.whatsapp.scheduled":
        		if(!isset($request["account"], $request["schedule"], $request["repeat"], $request["numbers"], $request["groups"], $request["message"]))
        			response(500, __("lang_response_invalid"));

				if(!is_array($request["groups"]))
        			response(500, __("lang_response_invalid"));

				if(!$this->sanitize->isInt($request["repeat"]))
					response(500, __("lang_response_invalid"));

				if(!$this->sanitize->length($request["message"], system_message_min))
					response(500, __("lang_response_message_short"));

				if(system_message_max > 0):
					if($this->sanitize->length($request["message"], system_message_max, 2))
						response(500, __("lang_response_message_toolong"));
				endif;

				$subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

				if(empty($subscription))
					response(500, __("lang_response_package_nosubwarn"));

				if($this->system->checkWaAccount(logged_id, $request["account"], "id") < 1)
					response(500, __("lang_response_invalid"));

				$waSent = $this->system->getWaScheduled($request["id"]);

				$decodeMessage = json_decode($waSent["message"], true);

				if(isset($decodeMessage["text"])):
					$decodeMessage["text"] = encodeBraces($request["message"]);
				else:
					$decodeMessage["caption"] = encodeBraces($request["message"]);
				endif;

        		$filtered = [
					"groups" => implode(",", $request["groups"]),
					"name" => $request["name"],
					"numbers" => $request["numbers"],
					"message" => json_encode($decodeMessage),
					"repeat" => $request["repeat"],
					"send_date" => strtotime($request["schedule"])
				];

				if($this->system->update($request["id"], logged_id, "wa_scheduled", $filtered)):
					response(200, __("lang_response_chatscheduled_editsuccess"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "edit.whatsapp":
        		if(!isset($request["receive_chats"], $request["random_send"], $request["random_min"], $request["random_max"]))
        			response(500, __("lang_response_invalid"));

        		if(empty(system_wa_server) && empty(system_wa_port))
					response(500, __("lang_requests_whatsapp_notavail"));

        		if(!$this->sanitize->isInt($request["receive_chats"]) || !$this->sanitize->isInt($request["random_send"]) || !$this->sanitize->isInt($request["random_min"]) || !$this->sanitize->isInt($request["random_max"]))
					response(500, __("lang_response_invalid"));

				if($request["random_max"] < $request["random_min"])
					response(500, __("lang_response_editdevice_invalidmaxinterval"));

				if($request["random_max"] > 300)
					response(500, __("lang_response_gateway_randommax"));

        		$filtered = [
        			"receive_chats" => $request["receive_chats"],
        			"random_send" => $request["random_send"],
        			"random_min" => $request["random_min"],
        			"random_max" => $request["random_max"]
        		];

        		$this->wa->_guzzle = $this->guzzle;

				if($this->wa->check()):
	        		if($this->system->update($request["id"], logged_id, "wa_accounts", $filtered)):
						$account = $this->system->getWaAccount(logged_id, $request["id"], "id");

						if($this->wa->update($account)):
							response(200, __("lang_requests_editwhatsapp_success"));
						endif;
	        		else:
	        			response(500, __("lang_response_went_wrong"));
	        		endif;
	        	else:
	        		response(500, __("lang_response_whatsapp_noconnectserver"));
	        	endif;

        		break;
        	case "edit.template":
        		if(!isset($request["name"], $request["format"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				if(!$this->sanitize->length($request["format"], 5))
					response(500, __("lang_response_format_short"));

        		$filtered = [
        			"name" => $request["name"],
        			"format" => $request["format"]
        		];

        		if($this->system->update($request["id"], logged_id, "templates", $filtered)):
        			response(200, __("lang_response_template_updated"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

        		break;
        	case "edit.contact":
        		if(!isset($request["name"], $request["phone"], $request["groups"], $request["current_phone"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

        		if($request["phone"] != $request["current_phone"]):
    				try {
					    $number = $this->phone->parse($request["phone"], logged_country);

						$number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

					    if(!$number->isValidNumber())
							response(500, __("lang_response_invalid_number"));

						if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
							response(500, __("lang_response_invalid_number"));

						$request["phone"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
					} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
						response(500, __("lang_response_invalid_number"));
					}

        			if($this->system->checkNumber(logged_id, $request["phone"]) > 0)
        				response(500, __("lang_response_number_exist"));
        		endif;

        		if(!is_array($request["groups"]))
        			response(500, __("lang_response_invalid"));

        		foreach($request["groups"] as $group):
    				if($this->system->checkGroup(logged_id, $group) < 1)
						response(500, __("lang_response_invalid"));
    			endforeach;

        		$filtered = [
        			"groups" => implode(",", $request["groups"]),
        			"name" => $request["name"],
        			"phone" => $request["phone"]
        		];

        		if($this->system->update($request["id"], logged_id, "contacts", $filtered)):
        			$this->cache->container("autocomplete.contacts." . logged_hash);
        			$this->cache->clear();
        			$this->cache->container("contacts." . logged_hash);
        			$this->cache->clear();
        			$this->cache->container("user." . logged_hash);
					$this->cache->clear();

        			response(200, __("lang_response_contact_updated"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

        		break;
        	case "edit.group":
        		if(!isset($request["name"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

        		$filtered = [
        			"name" => $request["name"]
        		];

        		if($this->system->update($request["id"], logged_id, "groups", $filtered)):
        			$this->cache->container("contacts." . logged_hash);
					$this->cache->clear();
					$this->cache->container("groups." . logged_hash);
					$this->cache->clear();

        			response(200, __("lang_response_group_updated"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

        		break;
        	case "edit.device":
        		if(!isset($request["name"], $request["receive_sms"], $request["random_send"], $request["random_min"], $request["random_max"], $request["limit_status"], $request["limit_interval"], $request["limit_number"], $request["packages"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_editdevice_nametooshort"));

				if($this->sanitize->length($request["name"], 15, 2))
					response(500, __("lang_response_editdevice_nametoolong"));

				if(!$this->sanitize->isInt($request["receive_sms"]) || !$this->sanitize->isInt($request["random_send"]) || !$this->sanitize->isInt($request["random_min"]) || !$this->sanitize->isInt($request["random_max"]) || !$this->sanitize->isInt($request["limit_status"]) || !$this->sanitize->isInt($request["limit_interval"]) || !$this->sanitize->isInt($request["limit_number"]))
					response(500, __("lang_response_invalid"));

				if($request["random_max"] < $request["random_min"])
					response(500, __("lang_response_editdevice_invalidmaxinterval"));

				if($request["random_max"] > 300)
					response(500, __("lang_response_gateway_randommax"));

				if($this->system->checkDevice(logged_id, $request["id"], "id") < 1)
					response(500, __("lang_response_invalid"));

				$partnerStatus = $this->system->getPartnership(logged_id);

        		if($partnerStatus < 2):
        			if(isset($request["rate"], $request["global_device"], $request["country"], $request["global_priority"], $request["global_slots"])):
			            if(!array_key_exists($request["country"], \CountryCodes::get("alpha2", "country")))
			            	response(500, __("lang_response_invalid"));

			            if(!$this->sanitize->isInt($request["global_priority"]))
			            	response(500, __("lang_response_invalid"));

			            if(!is_float((float) $request["rate"]))
			            	response(500, __("lang_response_editdevice_invalidrate"));
			        endif;
        		endif;

        		$filtered = [
        			"name" => $request["name"],
        			"receive_sms" => $request["receive_sms"],
        			"random_send" => $request["random_send"],
        			"random_min" => $request["random_min"],
        			"random_max" => $request["random_max"],
        			"limit_status" => $request["limit_status"],
        			"limit_interval" => $request["limit_interval"],
        			"limit_number" => $request["limit_number"],
        			"packages" => trim($request["packages"])
        		];

        		if($partnerStatus < 2):
        			if(isset($request["rate"], $request["global_device"], $request["country"], $request["global_priority"], $request["global_slots"])):
	        			$filtered["rate"] = $request["rate"];
	        			$filtered["global_device"] = $request["global_device"];
	        			$filtered["country"] = $request["country"];
	        			$filtered["global_priority"] = $request["global_priority"];
	        			$filtered["global_slots"] = !is_array($request["global_slots"]) ? 1 : implode(",", $request["global_slots"]);
	        		endif;
        		endif;

        		$device = $this->system->getDevice(logged_id, $request["id"], "id");

        		if($this->system->update($request["id"], logged_id, "devices", $filtered)):
        			$this->cache->container("user." . logged_hash);
					$this->cache->clear();

					$packages = array_map("trim", explode("\n", $request["packages"]));

					$this->fcm->send(md5(logged_id . $device["did"]), [
				    	"type" => "edit",
				    	"name" => $filtered["name"],
				    	"packages" => empty($packages) ? false : implode(",", $packages),
				    	"receive_sms" => $filtered["receive_sms"],
						"random_send" => $filtered["random_send"],
						"random_min" => $filtered["random_min"],
						"random_max" => $filtered["random_max"]
				    ]);

        			response(200, __("lang_response_device_updated"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

        		break;
        	case "edit.apikey":
        		if(!isset($request["name"], $request["permissions"]))
        			response(500, __("lang_response_invalid"));

        		$subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

				if(empty($subscription))
					response(500, __("lang_response_package_nosubwarn"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

        		if(!is_array($request["permissions"]))
        			response(500, __("lang_response_invalid"));

        		if(empty($request["permissions"]))
        			response(500, __("lang_response_permission_min"));

        		foreach($request["permissions"] as $permission):
        			if(!in_array($permission, [
        				"otp",
        				"sms_send",
        				"sms_send_bulk",
        				"wa_send",
        				"wa_send_bulk",
        				"ussd",
        				"get_credits",
        				"get_earnings",
        				"get_subscription",
        				"get_sms_pending",
        				"get_wa_pending",
        				"get_sms_received",
        				"get_wa_received",
        				"get_sms_sent",
        				"get_sms_campaigns",
        				"get_wa_sent",
        				"get_wa_campaigns",
        				"get_contacts",
        				"get_groups",
        				"get_ussd",
        				"get_notifications",
        				"get_wa_accounts",
        				"get_devices",
        				"get_rates",
        				"get_shorteners",
        				"get_unsubscribed",
        				"create_contact",
        				"create_group",
        				"start_sms_campaign",
        				"stop_sms_campaign",
        				"start_wa_campaign",
        				"stop_wa_campaign",
        				"delete_contact",
        				"delete_group",
        				"delete_sms_sent",
        				"delete_sms_campaign",
        				"delete_wa_sent",
        				"delete_wa_campaign",
        				"delete_sms_received",
        				"delete_wa_received",
        				"delete_ussd",
        				"delete_unsubscribed",
        				"delete_notification"
        			])):
        				response(500, __("lang_response_invalid"));
        			endif;
        		endforeach;

				$filtered = [
					"name" => $request["name"],
					"permissions" => implode(",", $request["permissions"])
				];

				if($this->system->update($request["id"], logged_id, "`keys`", $filtered)):
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();

					response(200, __("lang_response_key_updated"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "edit.webhook":
        		if(!isset($request["name"], $request["events"], $request["url"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				if(!is_array($request["events"]))
        			response(500, __("lang_response_invalid"));

        		foreach($request["events"] as $event):
        			if(!in_array($event, ["sms", "whatsapp", "ussd", "notifications"]))
        				response(500, __("lang_response_invalid"));
        		endforeach;

        		if(!$this->sanitize->isUrl($request["url"]))
        			response(500, __("lang_response_invalid_webhookurl"));

				$filtered = [
					"name" => $request["name"],
					"url" => $this->sanitize->url($request["url"]),
					"events" => implode(",", $request["events"])
				];

				if($this->system->update($request["id"], logged_id, "webhooks", $filtered)):
					response(200, __("lang_response_webhook_updated"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "edit.hook":
        		if(!isset($request["name"], $request["source"], $request["event"], $request["link"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				if(!$this->sanitize->isInt($request["source"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->isInt($request["event"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->isUrl($request["link"]))
        			response(500, __("lang_response_invalid_linkstructure"));

				$filtered = [
					"name" => $request["name"],
					"source" => $request["source"] < 2 ? 1 : 2,
					"event" => $request["event"] < 2 ? 1 : 2,
					"link" => $request["link"]
				];

				if($this->system->update($request["id"], logged_id, "actions", $filtered)):
					response(200, __("lang_response_hook_updated"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "edit.autoreply":
        		if(!isset($request["name"], $request["source"], $request["keywords"], $request["message"]))
        			response(500, __("lang_response_invalid"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				if(!$this->sanitize->isInt($request["source"]))
        			response(500, __("lang_response_invalid"));

				if(!$this->sanitize->length($request["keywords"]))
					response(500, __("lang_response_invalid_keywords"));

				if(!$this->sanitize->length($request["message"]))
					response(500, __("lang_response_message_short"));

				$filtered = [
					"name" => $request["name"],
					"source" => $request["source"] < 2 ? 1 : 2,
					"keywords" => $request["keywords"],
					"message" => $request["message"]
				];

				if($this->system->update($request["id"], logged_id, "actions", $filtered)):
					response(200, __("lang_response_autoreply_updated"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
        	case "edit.user":
        		if(!permission("manage_users"))
					response(500, __("lang_response_no_permission"));

				if($request["id"] < 2):
					if(!super_admin)
						response(500, __("lang_response_no_permission"));
				endif;

	            if(!isset($request["name"], $request["email"], $request["password"], $request["timezone"], $request["clock_format"], $request["date_format"], $request["date_separator"], $request["country"], $request["alertsound"], $request["role"], $request["language"], $request["credits"], $request["partner"], $request["current_email"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->isInt($request["role"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->isInt($request["language"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->isInt($request["alertsound"]))
	            	response(500, __("lang_response_invalid"));

	            if(!empty($request["credits"])):
		            if(!$this->sanitize->isNumeric($request["credits"]) || $request["credits"] < 0):
		            	response(500, __("lang_response_invalid"));
		            endif;
		        else:
		        	$request["credits"] = 0;
		        endif;

	            if(!$this->sanitize->isInt($request["partner"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

	            if(!$this->sanitize->isEmail($request["email"]))
	            	response(500, __("lang_response_invalid_email"));

	            if(!in_array(strtolower($request["timezone"]), $this->timezones->generate()))
	            	response(500, __("lang_response_invalid"));

	            if(!in_array($request["clock_format"], [1, 2]))
	            	response(500, __("lang_response_invalid"));

	            if(!in_array($request["date_format"], [1, 2, 3, 4]))
	            	response(500, __("lang_response_invalid"));

	            if(!in_array($request["date_separator"], [1, 2, 3, 4]))
	            	response(500, __("lang_response_invalid"));

	            if(!array_key_exists(strtoupper($request["country"]), \CountryCodes::get("alpha2", "country")))
	            	response(500, __("lang_response_invalid"));

	            if($this->system->checkRole($request["role"]) < 1)
	            	response(500, __("lang_response_invalid"));

	            if($this->system->checkLanguage($request["language"]) < 1)
	            	response(500, __("lang_response_invalid"));

	            if($request["current_email"] != $request["email"] && $this->system->checkEmail($request["email"]) > 0)
	            	response(500, __("lang_response_email_unavailable"));

	            $date_separator = [
	            	1 => "-",
	            	2 => "/",
	            	3 => ".",
	            	4 => " "
	            ];

	            $date_format = [
	            	1 => "n{$date_separator[$request["date_separator"]]}j{$date_separator[$request["date_separator"]]}Y",
	            	2 => "j{$date_separator[$request["date_separator"]]}n{$date_separator[$request["date_separator"]]}Y",
	            	3 => "Y{$date_separator[$request["date_separator"]]}n{$date_separator[$request["date_separator"]]}j",
	            	4 => "Y{$date_separator[$request["date_separator"]]}j{$date_separator[$request["date_separator"]]}n"
	            ];

            	$filtered = [
            		"country" => strtoupper($request["country"]),
            		"role" => $request["role"],
            		"name" => $request["name"],
            		"language" => $request["language"],
            		"timezone" => strtolower($request["timezone"]),
            		"formatting" => json_encode([
        				"clock" => $request["clock_format"] < 2 ? "g:i A" : "H:i",
        				"date" => $date_format[$request["date_format"]],
        				"container" => [
        					"clock_format" => (int) $request["clock_format"],
        					"date_format" => (int) $request["date_format"],
        					"date_separator" => (int) $request["date_separator"],
        					"separator_selected" => $date_separator[$request["date_separator"]]
        				]
        			]),
            		"alertsound" => $request["alertsound"] < 2 ? 1 : 2,
            		"credits" => $request["credits"], 
            		"partner" => $request["partner"] < 2 ? 1 : 2,
            		"email" => $this->sanitize->email($request["email"])
            	];

            	if(!empty($request["password"])):
	            	if(!$this->sanitize->length($request["password"], 5))
	            		response(500, __("lang_response_password_short"));
            		else
            			$filtered["password"] = password_hash($request["password"], PASSWORD_DEFAULT);
		        endif;

        		if($this->system->update($request["id"], false, "users", $filtered)):
        			$this->cache->container("system.users");
        			$this->cache->clear();

        			try {
						$this->echo->_cache = $this->cache;
						$this->echo->_guzzle = $this->guzzle;

						$echoToken = $this->echo->token();
					} catch(Exception $e){
						response(500, __("lang_response_titanecho_invaltoken"));
					}

					if($echoToken):
				        $this->echo->notify(md5($request["id"]), [
				        	"type" => "reload"
				        ]);
				    endif;

        			response(200, __("lang_response_user_updated"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "edit.role":
				if(!permission("manage_roles"))
					response(500, __("lang_response_no_permission"));

				if($request["id"] < 2)
					response(500, __("lang_role_default_update"));

        		if(!isset($request["name"], $request["permissions"]))
        			response(500, __("lang_response_invalid"));

        		if(!is_array($request["permissions"]))
        			response(500, __("lang_response_invalid"));

        		if(empty($request["permissions"]))
        			response(500, __("lang_response_permission_min"));

        		if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				if($this->system->checkRole($request["id"]) < 1)
                    response(500, __("lang_response_invalid"));

        		foreach($request["permissions"] as $permission):
        			if(!in_array($permission, [
				        "manage_users",
				        "manage_roles",
				        "manage_packages",
				        "manage_vouchers",
				        "manage_subscriptions",
				        "manage_transactions",
				        "manage_payouts",
				        "manage_widgets",
				        "manage_pages",
				        "manage_marketing",
				        "manage_languages",
				        "manage_gateways",
				        "manage_shorteners",
				        "manage_plugins",
				        "manage_templates",
				        "manage_api"
        			])):
        				response(500, __("lang_response_invalid"));
        			endif;
        		endforeach;

				$filtered = [
					"name" => $request["name"],
					"permissions" => implode(",", $request["permissions"])
				];

				if($this->system->update($request["id"], false, "roles", $filtered)):
					$this->cache->container("system.roles");
					$this->cache->clear();

					try {
						$this->echo->_cache = $this->cache;
						$this->echo->_guzzle = $this->guzzle;

						$echoToken = $this->echo->token();
					} catch(Exception $e){
						response(500, __("lang_response_titanecho_invaltoken"));
					}

					if($echoToken):
				        $this->echo->notify(logged_hash, [
				        	"type" => "reload"
				        ]);
				    endif;

					response(200, __("lang_role_updated"));
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

        		break;
			case "edit.package":
				if(!permission("manage_packages"))
					response(500, __("lang_response_no_permission"));

	            $columns = [
					"send_limit",
					"receive_limit",
					"ussd_limit",
					"notification_limit",
					"contact_limit",
					"device_limit",
					"key_limit",
					"webhook_limit",
					"action_limit",
					"scheduled_limit",
					"wa_send_limit",
					"wa_receive_limit",
					"wa_account_limit",
					"name",
					"price",
					"footermark",
					"hidden"
				];

				foreach($columns as $column):
					if(!isset($request[$column])):
						response(500, __("lang_response_invalid"));
					endif;

					if(!in_array($column, ["name"])):
						if(!$this->sanitize->isInt($request[$column])):
							response(500, __("lang_requests_packageform_invalidint"));
						endif;
					endif;
				endforeach;

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

				if($request["id"] > 1 && $request["price"] < 1)
					response(500, __("lang_response_package_pricenotlessone"));

				if($request["id"] < 2)
	            	$request["hidden"] = 2;

				$request["footermark"] = $request["footermark"] < 2 ? 1 : 2;
				$request["hidden"] = $request["hidden"] < 2 ? 1 : 2;

				$filtered = [];

            	foreach($columns as $column):
            		$filtered[$column] = $request[$column];
            	endforeach;

        		if($this->system->update($request["id"], false, "packages", $filtered)):
					foreach($this->system->getUsers() as $user):
    					$this->cache->container("user.{$user["hash"]}");
						$this->cache->clear();
        			endforeach;

        			$this->cache->container("system.packages");
            		$this->cache->clear();

        			response(200, __("lang_response_package_updated"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "edit.widget":
				if(!permission("manage_widgets"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["name"], $request["icon"], $request["type"], $request["size"], $request["position"], $request["content"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

	            if(!$this->sanitize->isInt($request["type"]))
	            	response(500, __("lang_response_invalid"));

	           	if(!in_array($request["type"], [1, 2]))
	           		response(500, __("lang_response_invalid"));

	           	if(!in_array($request["size"], ["sm", "md", "lg", "xl"]))
	           		response(500, __("lang_response_invalid"));
	           	
	           	if(!in_array($request["position"], ["center", "left", "right"]))
	           		response(500, __("lang_response_invalid"));

            	$filtered = [
            		"icon" => $request["icon"],
            		"name" => $request["name"],
            		"type" => $request["type"],
            		"size" => $request["size"],
            		"position" => $request["position"],
            		"content" => $this->sanitize->htmlEncode($request["content"]),
            	];

        		if($this->system->update($request["id"], false, "widgets", $filtered)):
					$this->cache->container("system.blocks");
					$this->cache->clear();
					$this->cache->container("system.modals");
					$this->cache->clear();

        			response(200, __("lang_response_widget_updated"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "edit.page":
				if(!permission("manage_pages"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["name"], $request["roles"], $request["logged"], $request["content"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

	            if(!is_array($request["roles"]))
	            	response(500, __("lang_response_invalid"));

	            if(empty($request["roles"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->isInt($request["logged"]))
					response(500, __("lang_response_invalid"));

				if(!in_array($request["logged"], [1, 2]))
					response(500, __("lang_response_invalid"));

            	$filtered = [
            		"slug" => $this->slug->create($request["name"]),
            		"logged" => $request["logged"],
            		"name" => $request["name"],
            		"roles" => implode(",", $request["roles"]),
            		"content" => $this->sanitize->htmlEncode($request["content"])
            	];

            	if($this->system->update($request["id"], false, "pages", $filtered)):
            		$this->cache->container("system.pages");
            		$this->cache->clear();

        			response(200, __("lang_response_page_updated"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "edit.language":
				if(!permission("manage_languages"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["name"], $request["iso"], $request["rtl"], $request["translations"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

	            if(!array_key_exists($request["iso"], \CountryCodes::get("alpha2", "country")))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->isInt($request["rtl"]))
	            	response(500, __("lang_response_invalid"));

	            if(!in_array($request["rtl"], [1, 2]))
	            	response(500, __("lang_response_invalid"));

            	$filtered = [
            		"name" => $request["name"],
            		"rtl" => $request["rtl"],
            		"iso" => strtoupper($request["iso"])
            	];

            	if($this->system->update($request["id"], false, "languages", $filtered)):
            		$this->cache->container("system.languages");
            		$this->cache->clear();
            		
            		$this->file->put("system/languages/" . md5($request["id"]) . ".lang", $request["translations"]);

        			response(301, __("lang_response_language_updated"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "edit.gateway":
				if(!permission("manage_gateways"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["name"], $request["callback"], $request["content"], $_FILES["controller"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->isInt($request["callback"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

            	$filtered = [
            		"name" => $request["name"],
            		"callback" => $request["callback"] < 2 ? 1 : 2,
            		"pricing" => $request["content"]
            	];

            	if($this->system->update($request["id"], false, "gateways", $filtered)):
            		try {
	        			$this->upload->upload($_FILES["controller"]);
	        			
		    			if($this->upload->uploaded):
		    				if(!in_array($this->upload->file_src_name_ext, ["php"]))
		    					response(500, __("lang_response_gateway_invalidcontroller"));

			                $this->upload->file_new_name_body = md5($request["id"]);
			                $this->upload->file_new_name_ext = "php";
			                $this->upload->file_overwrite = true;
			                $this->upload->process("system/gateways/");

							if($this->upload->processed)
								$this->upload->clean();
							else
								response(500, __("lang_response_gateway_invalidcontroller"));
						endif;
	        		} catch(Exception $e){
	        			response(500, __("lang_response_gateway_invalidcontroller"));
	        		}

        			response(200, __("lang_response_gateway_updatedsuccess"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "edit.shortener":
				if(!permission("manage_shorteners"))
					response(500, __("lang_response_no_permission"));

	            if(!isset($request["name"], $_FILES["controller"]))
	            	response(500, __("lang_response_invalid"));

	            if(!$this->sanitize->length($request["name"]))
					response(500, __("lang_response_name_short"));

            	$filtered = [
            		"name" => $request["name"]
            	];

            	if($this->system->update($request["id"], false, "shorteners", $filtered)):
            		try {
	        			$this->upload->upload($_FILES["controller"]);
	        			
		    			if($this->upload->uploaded):
		    				if(!in_array($this->upload->file_src_name_ext, ["php"]))
		    					response(500, __("lang_response_shortener_invalidcontroller"));

			                $this->upload->file_new_name_body = md5($request["id"]);
			                $this->upload->file_new_name_ext = "php";
			                $this->upload->file_overwrite = true;
			                $this->upload->process("system/shorteners/");

							if($this->upload->processed)
								$this->upload->clean();
							else
								response(500, __("lang_response_shortener_invalidcontroller"));
						endif;
	        		} catch(Exception $e){
	        			response(500, __("lang_response_shortener_invalidcontroller"));
	        		}

        			response(200, __("lang_response_shortener_editsuccess"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "edit.plugin":
				if(!permission("manage_plugins"))
					response(500, __("lang_response_no_permission"));

				foreach($request as $key => $value):
					if($key != "id" && $key != "plugin_name")
						$pluginData[$key] = $value;
				endforeach;

				$plugin = $this->system->getPlugin($request["id"]);

				$decode = json_decode($plugin["data"], true);

            	$filtered = [
            		"data" => [
            			"name" => $request["plugin_name"],
            			"data" => $pluginData
            		]
            	];

            	if(array_key_exists("actions", $decode)):
            		$filtered["data"]["actions"] = $decode["actions"];
            	endif;

            	if(array_key_exists("version", $decode)):
            		$filtered["data"]["version"] = $decode["version"];
            	endif;

            	$filtered["data"] = json_encode($filtered["data"]);

            	if($this->system->update($request["id"], false, "plugins", $filtered)):
            		$this->cache->container("system.plugins");
            		$this->cache->clear();

        			response(200, __("lang_response_editplugin_success"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "update.plugin":
				if(!permission("manage_plugins"))
					response(500, __("lang_response_no_permission"));

				if(!isset($_FILES["plugin"]))
        			response(500, __("lang_response_invalid"));

	            $this->upload->upload($_FILES["plugin"]);
    			if($this->upload->uploaded):
    				if(!in_array($this->upload->file_src_name_ext, ["zip"]))
	    				response(500, __("lang_response_invalid"));

	                $this->upload->file_new_name_body = "plugin";
	                $this->upload->file_overwrite = true;
	                $this->upload->process("uploads/temporary/");

					if($this->upload->processed)
						$this->upload->clean();
				endif;

				if(!$this->file->exists("uploads/temporary/plugin.zip"))
					response(500, __("lang_response_pluginwentwrong"));

				try {
					rmrf("uploads/temporary/plugin");
					mkdir("uploads/temporary/plugin");
				} catch(Exception $e){
					// Ignore
				}

				try {
					$zip = $this->zippy->open("uploads/temporary/plugin.zip")->extract("uploads/temporary/plugin");
				} catch(Exception $e){
					$this->file->put("system/storage/temporary/error.log", "Update Plugin Error: {$e->getMessage()} (" . date("m/d/Y g:i A") . ")\n\n", FILE_APPEND);
				}

				if(!$this->file->exists("uploads/temporary/plugin/update.json"))
					response(500, __("lang_response_updatepluin_corrupted"));

				try {
					copyDirectory("uploads/temporary/plugin/files/", "./");
				} catch(Exception $e){
					// Ignore
				}

				$updateData = json_decode($this->file->get("uploads/temporary/plugin/update.json"), true);

            	$filtered = [
            		"name" => $updateData["id"],
            		"data" => [
            			"name" => $updateData["name"],
            			"data" => $updateData["data"]
            		]
            	];

            	if(array_key_exists("actions", $updateData)):
            		$filtered["data"]["actions"] = $updateData["actions"];
            	endif;

            	if(array_key_exists("version", $updateData)):
            		$filtered["data"]["version"] = $updateData["version"];
            	endif;

            	$filtered["data"] = json_encode($filtered["data"]);

            	if($this->system->update($request["id"], false, "plugins", $filtered)):
            		$this->cache->container("system.plugins");
            		$this->cache->clear();

            		try {
						rmrf("uploads/temporary/plugin");
					} catch(Exception $e){
						// Ignore
					}

        			response(200, __("lang_response_updateplugin_updated"));
        		else:
        			response(500, __("lang_response_went_wrong"));
        		endif;

				break;
			case "admin.suspend":
				if(!permission("manage_users"))
					response(500, __("lang_response_no_permission"));

				if($request["id"] < 2)
					response(500, __("lang_response_adminsuspend"));

				if($this->system->checkUser($request["id"]) > 0):
					if($this->system->update($request["id"], false, "users", [
						"suspended" => 1
					])):
	        			response(200, __("lang_response_successsuspend"));
	        		else:
	        			response(500, __("lang_response_went_wrong"));
	        		endif;
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "admin.unsuspend":
				if(!permission("manage_users"))
					response(500, __("lang_response_no_permission"));

				if($request["id"] < 2)
					response(500, __("lang_response_invalid"));

				if($this->system->checkUser($request["id"]) > 0):
					if($this->system->update($request["id"], false, "users", [
						"suspended" => 0
					])):
	        			response(200, __("lang_response_successunsuspend"));
	        		else:
	        			response(500, __("lang_response_went_wrong"));
	        		endif;
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "admin.builder":
				if(!super_admin)
					response(500, __("lang_response_no_permission"));

				if(!$this->sanitize->length($request["package_name"], 5))
					response(500, __("lang_response_builder_packagenameshort"));

				if(!$this->sanitize->length($request["app_name"]))
					response(500, __("lang_response_builder_appnameshort"));

				$request["package_name"] = strtolower($request["package_name"]);
				$request["apk_version"] = floor($request["apk_version"]);
				$request["app_layout"] = empty($request["layout"]) ? $this->file->get("system/storage/temporary/device.html") : $request["layout"];
				$request["app_js"] = $request["script"];
				$request["app_css"] = $request["css"];

	            if(isset($_FILES["google"])):
	            	try {
	        			$this->upload->upload($_FILES["google"]);
		    			if($this->upload->uploaded):
		    				if(!in_array($this->upload->file_src_name_ext, ["json"]))
		    					response(500, __("lang_response_builder_invalidgooglefile"));

		    				$this->upload->mime_check = false;
			                $this->upload->file_new_name_body = "google";
			                $this->upload->file_overwrite = true;
			                $this->upload->process("system/storage/temporary/");

							if($this->upload->processed)
								$this->upload->clean();
							else
								response(500, __("lang_response_builder_invalidgooglefile"));
						endif;
	        		} catch(Exception $e){
	        			response(500, __("lang_response_builder_invalidgooglefile"));
	        		}
	            endif;

	            if(isset($_FILES["firebase"])):
	            	try {
	        			$this->upload->upload($_FILES["firebase"]);
		    			if($this->upload->uploaded):
		    				if(!in_array($this->upload->file_src_name_ext, ["json"]))
		    					response(500, __("lang_response_builder_invalidfirebasefile"));

		    				$this->upload->mime_check = false;
			                $this->upload->file_new_name_body = "firebase";
			                $this->upload->file_overwrite = true;
			                $this->upload->process("system/storage/temporary/");

							if($this->upload->processed)
								$this->upload->clean();
							else
								response(500, __("lang_response_builder_invalidfirebasefile"));
						endif;
	        		} catch(Exception $e){
	        			response(500, __("lang_response_builder_invalidfirebasefile"));
	        		}
	            endif;

	            if(isset($_FILES["app_logo"])):
        			$this->upload->upload($_FILES["app_logo"]);
        			if($this->upload->uploaded):
        				if(!in_array($this->upload->file_src_name_ext, ["png"]))
    						response(500, __("lang_response_builder_applogofail"));

        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "logo";
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/builder/");

						if($this->upload->processed):
							$this->upload->clean();

							try {
								$imgbb = json_decode($this->guzzle->post("https://imgbb.com/json", [
									"multipart" => [
								        [
								            "name" => "type",
								            "contents" => "file"
								        ],
								        [
								            "name" => "action",
								            "contents" => "upload"
								        ],
								        [
								            "name" => "timestamp",
								            "contents" => time()
								        ],
								        [
								            "name" => "auth_token",
								            "contents" => time()
								        ],
								        [
								            "name" => "source",
								            "contents" => fopen("uploads/builder/logo.png", "r"),
								            "filename" => "logo.png"
								        ]
								    ],
						            "allow_redirects" => true,
						            "http_errors" => false
						        ])->getBody()->getContents(), true);

								$request["app_logo_remote"] = $imgbb["image"]["url"];
							} catch(Exception $e){
								// Ignore
							}
						else:
							response(500, __("lang_response_builder_applogofail"));
						endif;
					endif;
	            endif;

	            if(isset($_FILES["app_logo_login"])):
        			$this->upload->upload($_FILES["app_logo_login"]);
        			if($this->upload->uploaded):
        				if(!in_array($this->upload->file_src_name_ext, ["png"]))
    						response(500, __("lang_response_builder_applogofail"));

        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "logo-login";
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/builder/");

						if($this->upload->processed):
							$this->upload->clean();

							try {
								$imgbb = json_decode($this->guzzle->post("https://imgbb.com/json", [
									"multipart" => [
								        [
								            "name" => "type",
								            "contents" => "file"
								        ],
								        [
								            "name" => "action",
								            "contents" => "upload"
								        ],
								        [
								            "name" => "timestamp",
								            "contents" => time()
								        ],
								        [
								            "name" => "auth_token",
								            "contents" => time()
								        ],
								        [
								            "name" => "source",
								            "contents" => fopen("uploads/builder/logo-login.png", "r"),
								            "filename" => "logo-login.png"
								        ]
								    ],
						            "allow_redirects" => true,
						            "http_errors" => false
						        ])->getBody()->getContents(), true);

								$request["app_loginlogo_remote"] = $imgbb["image"]["url"];
							} catch(Exception $e){
								// Ignore
							}
						else:
							response(500, __("lang_response_builder_applogofail"));
						endif;
					endif;
	            endif;

	            if(isset($_FILES["app_icon"])):
        			$this->upload->upload($_FILES["app_icon"]);
        			if($this->upload->uploaded):
        				if(!in_array($this->upload->file_src_name_ext, ["png"]))
    						response(500, __("lang_response_builder_appiconfail"));

        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "icon";
		                $this->upload->file_overwrite = true;
		                $this->upload->image_min_width = 1024;
		                $this->upload->image_min_height = 1024;
		                $this->upload->image_max_width = 1024;
		                $this->upload->image_max_height = 1024;
		                $this->upload->process("uploads/builder/");

						if($this->upload->processed):
							$this->upload->clean();

							try {
								$imgbb = json_decode($this->guzzle->post("https://imgbb.com/json", [
									"multipart" => [
								        [
								            "name" => "type",
								            "contents" => "file"
								        ],
								        [
								            "name" => "action",
								            "contents" => "upload"
								        ],
								        [
								            "name" => "timestamp",
								            "contents" => time()
								        ],
								        [
								            "name" => "auth_token",
								            "contents" => time()
								        ],
								        [
								            "name" => "source",
								            "contents" => fopen("uploads/builder/icon.png", "r"),
								            "filename" => "icon.png"
								        ]
								    ],
						            "allow_redirects" => true,
						            "http_errors" => false
						        ])->getBody()->getContents(), true);

								$request["app_icon_remote"] = $imgbb["image"]["url"];
							} catch(Exception $e){
								// Ignore
							}
						else:
							response(500, __("lang_response_builder_appiconfail"));
						endif;
					endif;
	            endif;

	            if(isset($_FILES["app_splash"])):
        			$this->upload->upload($_FILES["app_splash"]);
        			if($this->upload->uploaded):
        				if(!in_array($this->upload->file_src_name_ext, ["png"]))
    						response(500, __("lang_response_builder_appsplashfail"));

        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "splash";
		                $this->upload->file_overwrite = true;
		                $this->upload->image_min_width = 2732;
		                $this->upload->image_min_height = 2732;
		                $this->upload->image_max_width = 2732;
		                $this->upload->image_max_height = 2732;
		                $this->upload->process("uploads/builder/");

						if($this->upload->processed):
							$this->upload->clean();

							try {
								$imgbb = json_decode($this->guzzle->post("https://imgbb.com/json", [
									"multipart" => [
								        [
								            "name" => "type",
								            "contents" => "file"
								        ],
								        [
								            "name" => "action",
								            "contents" => "upload"
								        ],
								        [
								            "name" => "timestamp",
								            "contents" => time()
								        ],
								        [
								            "name" => "auth_token",
								            "contents" => time()
								        ],
								        [
								            "name" => "source",
								            "contents" => fopen("uploads/builder/splash.png", "r"),
								            "filename" => "splash.png"
								        ]
								    ],
						            "allow_redirects" => true,
						            "http_errors" => false
						        ])->getBody()->getContents(), true);

								$request["app_splash_remote"] = $imgbb["image"]["url"];
							} catch(Exception $e){
								// Ignore
							}
						else:
							response(500, __("lang_response_builder_appsplashfail"));
						endif;
					endif;
	            endif;

	            foreach($request as $key => $value):
            		$this->system->settings($key, $value);
	            endforeach;

	            $this->cache->container("system.settings");
	            $this->cache->clear();

    			response(200, __("lang_response_builder_settingsupdated"));

				break;
			case "admin.theme":
				if(!super_admin)
					response(500, __("lang_response_no_permission"));

				if(isset($_FILES["logo_light_img"])):
        			$this->upload->upload($_FILES["logo_light_img"]);
        			if($this->upload->uploaded):
        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "logo-light";
		                $this->upload->image_convert = "png";
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/theme/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, __("lang_response_theme_sitelogofailed"));
					endif;
	            endif;

	            if(isset($_FILES["logo_dark_img"])):
        			$this->upload->upload($_FILES["logo_dark_img"]);
        			if($this->upload->uploaded):
        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "logo-dark";
		                $this->upload->image_convert = "png";
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/theme/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, __("lang_response_theme_sitelogofailed"));
					endif;
	            endif;

	            if(isset($_FILES["bg_img"])):
        			$this->upload->upload($_FILES["bg_img"]);
        			if($this->upload->uploaded):
        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "bg";
		                $this->upload->image_convert = "png";
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/theme/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, __("lang_response_theme_sitebgfailed"));
					endif;
	            endif;

	            if(isset($_FILES["favicon_img"])):
        			$this->upload->upload($_FILES["favicon_img"]);
        			if($this->upload->uploaded):
        				$this->upload->allowed = [
        					"image/*"
        				];
		                $this->upload->file_new_name_body = "favicon";
		                $this->upload->image_convert = "png";
		                $this->upload->image_resize = true;
						$this->upload->image_x = 50;
						$this->upload->image_ratio_y = true;
		                $this->upload->file_overwrite = true;
		                $this->upload->process("uploads/theme/");

						if($this->upload->processed)
							$this->upload->clean();
						else
							response(500, __("lang_response_theme_sitefavfailed"));
					endif;
	            endif;

	            try {
	            	$script = <<<JAVASCRIPT
window._customZender = {
	hookOnload: () => {
		{$request["script"]}
	},

	hookOnloaded: () => {
		{$request["script"]}
	}
}
JAVASCRIPT;

					$this->file->put("templates/_assets/js/custom.run.js", $script);
					$this->file->put("templates/_assets/js/custom.js", $request["script"]);
					$this->file->put("templates/_assets/css/custom.css", $request["css"]);
	            } catch(Exception $e){

	            }

				try {
	            	$this->scss->setVariables([
					    "theme" => $request["theme_background"],
					    "themeText" => $request["theme_highlight"],
					    "themeSpinner" => $request["theme_spinner"]
					]);
					
					$this->scss->setFormatter("ScssPhp\ScssPhp\Formatter\Crunched");
					$this->scss->setImportPaths("templates/_scss/");

					$this->file->put("templates/dashboard/assets/css/style.min.css", $this->scss->compile("@import 'dashboard.scss';"));
					$this->file->put("templates/default/assets/css/style.min.css", $this->scss->compile("@import 'default.scss';"));

					$this->file->put("templates/dashboard/assets/css/style.rtl.min.css", $this->scss->compile("@import 'dashboard.rtl.scss';"));
					$this->file->put("templates/default/assets/css/style.rtl.min.css", $this->scss->compile("@import 'default.rtl.scss';"));
	            } catch(Exception $e){
	            	response(500, __("lang_response_went_wrong"));
	            }

	            foreach($request as $key => $value):
            		$this->system->settings($key, $value);
	            endforeach;

	            $this->cache->container("system.settings");
	            $this->cache->clear();

    			response(301, __("lang_response_theme_updated"));

				break;
			case "admin.settings":
				if(!super_admin)
					response(500, __("lang_response_no_permission"));

				if(isset($request["admin_api"])):
					if(!is_array($request["admin_api"]))
						response(500, __("lang_response_invalid"));

					if(empty($request["admin_api"]))
						response(500, __("lang_response_invalid"));

					$request["admin_api"] = implode(",", $request["admin_api"]);
				endif;

				if(isset($request["mailing_triggers"])):
					if(!is_array($request["mailing_triggers"]))
						response(500, __("lang_response_invalid"));

					if(empty($request["mailing_triggers"]))
						response(500, __("lang_response_invalid"));

					$request["mailing_triggers"] = implode(",", $request["mailing_triggers"]);
				endif;

				if(isset($request["providers"])):
					if(!is_array($request["providers"]))
						response(500, __("lang_response_invalid"));

					if(empty($request["providers"]))
						response(500, __("lang_response_leastprovider"));

					foreach($request["providers"] as $provider):
						if(!in_array($provider, ["paypal", "mollie", "bank"]))
							response(500, __("lang_response_invalid"));
					endforeach;

					$request["providers"] = implode(",", $request["providers"]);
				else:
					$request["providers"] = false;
				endif;

				if(isset($request["social_platforms"])):
					if(!is_array($request["social_platforms"]))
						response(500, __("lang_response_invalid"));

					if(empty($request["social_platforms"]))
						response(500, __("lang_response_minplatforms"));

					foreach($request["social_platforms"] as $provider):
						if(!in_array($provider, ["facebook", "google", "vk"]))
							response(500, __("lang_response_invalid"));
					endforeach;

					$request["social_platforms"] = implode(",", $request["social_platforms"]);
				else:
					$request["social_platforms"] = false;
				endif;

				if(isset($request["partner_commission"]) && ($request["partner_commission"] < 1 || $request["partner_commission"] > 80)):
					response(500, __("lang_response_partners_commisionerror"));
				endif;

				if(!in_array($request["currency"], ["AED", "AFN", "ALL", "AMD", "ANG", "AOA", "ARS", "AUD", "AWG", "AZN", "BAM", "BBD", "BDT", "BGN", "BHD", "BIF", "BMD", "BND", "BOB", "BRL", "BSD", "BTC", "BTN", "BWP", "BYN", "BZD", "CAD", "CDF", "CHF", "CLF", "CLP", "CNH", "CNY", "COP", "CRC", "CUC", "CUP", "CVE", "CZK", "DJF", "DKK", "DOP", "DZD", "EGP", "ERN", "ETB", "EUR", "FJD", "FKP", "GBP", "GEL", "GGP", "GHS", "GIP", "GMD", "GNF", "GTQ", "GYD", "HKD", "HNL", "HRK", "HTG", "HUF", "IDR", "ILS", "IMP", "INR", "IQD", "IRR", "ISK", "JEP", "JMD", "JOD", "JPY", "KES", "KGS", "KHR", "KMF", "KPW", "KRW", "KWD", "KYD", "KZT", "LAK", "LBP", "LKR", "LRD", "LSL", "LYD", "MAD", "MDL", "MGA", "MKD", "MMK", "MNT", "MOP", "MRO", "MRU", "MUR", "MVR", "MWK", "MXN", "MYR", "MZN", "NAD", "NGN", "NIO", "NOK", "NPR", "NZD", "OMR", "PAB", "PEN", "PGK", "PHP", "PKR", "PLN", "PYG", "QAR", "RON", "RSD", "RUB", "RWF", "SAR", "SBD", "SCR", "SDG", "SEK", "SGD", "SHP", "SLL", "SOS", "SRD", "SSP", "STD", "STN", "SVC", "SYP", "SZL", "THB", "TJS", "TMT", "TND", "TOP", "TRY", "TTD", "TWD", "TZS", "UAH", "UGX", "USD", "UYU", "UZS", "VES", "VND", "VUV", "WST", "XAF", "XAG", "XAU", "XCD", "XDR", "XOF", "XPD", "XPF", "XPT", "YER", "ZAR", "ZMW", "ZWL"])):
					response(500, __("lang_response_invalid"));
				endif;

				$request["tawk_id"] = (string) Stringy\create($request["tawk_id"])->removeLeft("https://tawk.to/chat/");

	            foreach($request as $key => $value):
            		$this->system->settings($key, $value);
	            endforeach;

	            $this->cache->container("system.settings");
	            $this->cache->clear();

    			response(301, __("lang_response_system_settingsupdated"));

				break;
        	default:
        		response(500, __("lang_response_invalid"));
        endswitch;
	}

	public function delete()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
            response(302);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        $this->cache->container("system.plugins");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getPlugins());
        endif;

        set_plugins($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        $type = $this->sanitize->string($this->url->segment(4));
        $id = $this->sanitize->string($this->url->segment(5));

        if(!$this->sanitize->isInt($id))
        	response(500, __("lang_response_invalid"));

		switch($type):
			case "sent":
				$sent = $this->system->getSent($id);

				if($this->system->delete(logged_id, $id, "sent")):
					if($sent["status"] < 3):
						try {
							$this->fcm->send(md5(logged_id . $sent["did"]), [
								"type" => "sms_delete",
								"id" => $id
							]);
						} catch(Exception $e){
							// Ignore
						}
					endif;

					$vars = [
						"message" => __("lang_response_deleted_sent"),
						"table" => "sms.sent"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "sms.campaign":
				if($this->system->delete(logged_id, $id, "campaigns")):
					if($this->system->clearCampaignSms(logged_id, $id)):
						$this->fcm->send($this->hash->encode(logged_id, system_token), [
							"type" => "sms_campaign_delete",
							"cid" => $id
						]);
					endif;

					$vars = [
						"message" => __("lang_response_delete_smsdeletecampaign"),
						"table" => "sms.campaigns"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "received":
				$received = $this->system->getMessageReceived(logged_id, $id);

				if($this->system->checkDeleted($received["uid"], $received["rid"], $received["did"]) < 1):
					$this->system->create("deleted", [
						"rid" => $received["rid"],
						"uid" => $received["uid"],
						"did" => $received["did"]
					]);
				endif;

				if($this->system->delete(logged_id, $id, "received")):
					$vars = [
						"message" => __("lang_response_deleted_received"),
						"table" => "sms.received"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "scheduled":
				if($this->system->delete(logged_id, $id, "scheduled")):
					$vars = [
						"message" => __("lang_response_scheduled_deleted"),
						"table" => "sms.scheduled"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "ussd":
				if($this->system->delete(logged_id, $id, "ussd")):
					$vars = [
						"message" => __("lang_response_ussddelete_success"),
						"table" => "android.ussd"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "notification":
				if($this->system->delete(logged_id, $id, "notifications")):
					$vars = [
						"message" => __("lang_response_notification_deletedsuccess"),
						"table" => "android.notifications"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "wa.sent":
				$this->wa->_guzzle = $this->guzzle;

				$sent = $this->system->getWaSent($id);

				if($sent["status"] < 3):
					if(!$this->wa->check()):
						response(500, __("lang_response_whatsapp_noconnectserver"));
					endif;
				endif;

				if($this->system->delete(logged_id, $id, "wa_sent")):
					if($sent["status"] < 3):
						try {
							$account = $this->system->getWaAccount(logged_id, $sent["wid"], "wid");

							$this->wa->delete_chat($account["unique"], logged_hash, $id);
						} catch(Exception $e){
							// Ignore
						}
					endif;

					$vars = [
						"message" => __("lang_response_whatsapp_sentdeletesuccess"),
						"table" => "whatsapp.sent"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "wa.campaign":
				if(empty(system_wa_server) && empty(system_wa_port))
					response(500, __("lang_requests_whatsapp_notavail"));

				$this->wa->_guzzle = $this->guzzle;

				if(!$this->wa->check()):
					response(500, __("lang_response_whatsapp_noconnectserver"));
				endif;

				$campaign = $this->system->getWaCampaign(logged_id, $id, "id");

				if($this->system->delete(logged_id, $id, "wa_campaigns")):
					if($this->system->clearCampaignChats(logged_id, $id)):
						try {
							$account = $this->system->getWaAccount(logged_id, $campaign["wid"], "wid");

							$this->wa->delete_campaign($account["unique"], logged_hash, $id);
						} catch(Exception $e){
							// Ignore
						}
					endif;

					$vars = [
						"message" => __("lang_response_delete_wacampaigndeleted"),
						"table" => "wa.campaigns"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "wa.received":
				if($this->system->delete(logged_id, $id, "wa_received")):
					$vars = [
						"message" => __("lang_response_whatsapp_receivedeletesuccess"),
						"table" => "whatsapp.received"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "wa.scheduled":
				if($this->system->delete(logged_id, $id, "wa_scheduled")):
					$vars = [
						"message" => __("lang_response_whatsapp_scheduleddeletesuccess"),
						"table" => "whatsapp.scheduled"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "wa.group":
				if($this->system->delete(logged_id, $id, "wa_groups")):
					$this->cache->container("wa.contacts." . logged_hash);
					$this->cache->clear();

					$vars = [
						"message" => __("lang_response_delete_wagroupdeleted"),
						"table" => "whatsapp.groups"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "wa.account":
				if(empty(system_wa_server) && empty(system_wa_port))
					response(500, __("lang_requests_whatsapp_notavail"));

				$account = $this->system->getWaAccount(logged_id, $id, "id");

				if($this->system->delete(logged_id, $id, "wa_accounts")):
					$this->wa->_guzzle = $this->guzzle;
					$this->wa->delete($account["unique"]);

					$vars = [
						"message" => __("lang_response_whatsapp_accountdeletesuccess"),
						"table" => "whatsapp.accounts"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "templates":
				if($this->system->delete(logged_id, $id, "templates")):
					$vars = [
						"message" => __("lang_response_deleted_template"),
						"table" => "messages.templates"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "contacts":
				if($this->system->delete(logged_id, $id, "contacts")):
					$this->cache->container("autocomplete.contacts." . logged_hash);
	                $this->cache->clear();
	                $this->cache->container("contacts." . logged_hash);
	                $this->cache->clear();
	                $this->cache->container("user." . logged_hash);
	                $this->cache->clear();

					$vars = [
						"message" => __("lang_response_deleted_contact"),
						"table" => "contacts.saved"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "groups":
				if($this->system->delete(logged_id, $id, "groups")):
					$contacts = $this->system->getContactsByGroup(logged_id, $id);

					if(!empty($contacts)):
						foreach($contacts as $contact):
							$this->system->delete(logged_id, $contact["id"], "contacts");
						endforeach;
					endif;

					$vars = [
						"message" => __("lang_response_deleted_group"),
						"table" => "contacts.groups"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "unsubscribed":
				if($this->system->delete(logged_id, $id, "unsubscribed")):
					$vars = [
						"message" => __("lang_response_unsibscribe_contactdeletedsuccess"),
						"table" => "contacts.unsubscribed"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "devices":
				$device = $this->system->getDevice(logged_id, $id, "id");

				if($this->system->delete(logged_id, $id, "devices")):
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();

					$this->fcm->send(md5(logged_id . $device["did"]), [
				    	"type" => "unlink",
				    	"device_unique" => $device["did"]
				    ]);

					$vars = [
						"message" => __("lang_response_deleted_device"),
						"table" => "devices.registered"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "keys":
				if($this->system->delete(logged_id, $id, "keys")):
					$this->cache->container("user." . logged_hash);
					$this->cache->clear();

					$vars = [
						"message" => __("lang_response_deleted_key"),
						"table" => "tools.keys"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "webhooks":
				if($this->system->delete(logged_id, $id, "webhooks")):
					$vars = [
						"message" => __("lang_response_deleted_hook"),
						"table" => "tools.webhooks"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "actions":
				if($this->system->delete(logged_id, $id, "actions")):
					$vars = [
						"message" => __("lang_response_action_deleted"),
						"table" => "tools.actions"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "users":
				if(!permission("manage_users"))
					response(500, __("lang_response_no_permission"));

				if($id < 2)
					response(500, __("lang_response_deleted_defaultuserfalse"));

				if($this->system->delete(false, $id, "users")):
					$this->cache->container("system.users");
        			$this->cache->clear();

					$vars = [
						"message" => __("lang_response_deleted_user"),
						"table" => "administration.users"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "roles":
				if(!permission("manage_roles"))
					response(500, __("lang_response_no_permission"));

				if($id < 2)
					response(500, __("lang_role_default_delete"));

				if($this->system->delete(false, $id, "roles")):
					$vars = [
						"message" => __("lang_role_deleted"),
						"table" => "administration.roles"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "packages":
				if(!permission("manage_packages"))
					response(500, __("lang_response_no_permission"));

				if($id < 2)
					response(500, __("lang_response_deleted_defaultpackagefalse"));

				if($this->system->delete(false, $id, "packages")):
					foreach($this->system->getUsers() as $user):
    					$this->cache->container("user.{$user["hash"]}");
						$this->cache->clear();
        			endforeach;

					$this->cache->container("system.packages");
            		$this->cache->clear();

					$vars = [
						"message" => __("lang_response_deleted_package"),
						"table" => "administration.packages"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "vouchers":
				if(!permission("manage_vouchers"))
					response(500, __("lang_response_no_permission"));

				if($this->system->delete(false, $id, "vouchers")):
					$vars = [
						"message" => __("lang_voucher_deleted"),
						"table" => "administration.vouchers"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "subscriptions":
				if(!permission("manage_subscriptions"))
					response(500, __("lang_response_no_permission"));

				if($this->system->delete(false, $id, "subscriptions")):
					$vars = [
						"message" => __("lang_response_deleted_subscription"),
						"table" => "administration.subscriptions"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "widgets":
				if(!permission("manage_widgets"))
					response(500, __("lang_response_no_permission"));

				if($this->system->delete(false, $id, "widgets")):
					$this->cache->container("system.blocks");
					$this->cache->clear();
					$this->cache->container("system.modals");
					$this->cache->clear();

					$vars = [
						"message" => __("lang_response_deleted_widget"),
						"table" => "administration.widgets"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "pages":
				if(!permission("manage_pages"))
					response(500, __("lang_response_no_permission"));

				if($this->system->delete(false, $id, "pages")):
					$this->cache->container("system.pages");
            		$this->cache->clear();

					$vars = [
						"message" => __("lang_response_page_deleted"),
						"table" => "administration.pages"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "marketing":
				if(!permission("manage_marketing"))
					response(500, __("lang_response_no_permission"));

				if($this->system->delete(false, $id, "marketing")):
					$vars = [
						"message" => __("lang_response_marketing_logdeletedsuccess"),
						"table" => "administration.marketing"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "languages":
				if(!permission("manage_languages"))
					response(500, __("lang_response_no_permission"));

				if($id < 2)
					response(500, __("lang_response_deleted_defaultlangfalse"));
				
				if($this->system->delete(false, $id, "languages")):
					$this->cache->container("system.languages");
            		$this->cache->clear();
            		
					$this->file->delete("system/languages/" . md5($id) . ".lang");

					$vars = [
						"message" => __("lang_response_deleted_language"),
						"table" => "administration.languages"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "gateways":
				if(!permission("manage_gateways"))
					response(500, __("lang_response_no_permission"));

				if($this->system->delete(false, $id, "gateways")):
					$vars = [
						"message" => __("lang_response_gateway_controllerdeletedsuccess"),
						"table" => "administration.gateways"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "shorteners":
				if(!permission("manage_shorteners"))
					response(500, __("lang_response_no_permission"));

				if($this->system->delete(false, $id, "shorteners")):
					$vars = [
						"message" => __("lang_response_shortener_controllerdeletedsuccess"),
						"table" => "administration.shorteners"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			case "plugins":
				if(!permission("manage_plugins"))
					response(500, __("lang_response_no_permission"));

				if($this->system->delete(false, $id, "plugins")):
					$vars = [
						"message" => __("lang_requests_delete_pluginssuccess"),
						"table" => "administration.plugins"
					];
				else:
					response(500, __("lang_response_went_wrong"));
				endif;

				break;
			default:
				response(500, __("lang_response_invalid"));
		endswitch;

		response(200, $vars["message"], [
			"vars" => [
				"table" => $vars["table"]
			]
		]);
	}
}