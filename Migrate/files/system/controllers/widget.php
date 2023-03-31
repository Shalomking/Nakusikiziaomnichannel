<?php
/**
 * @controller Widget
 */

class Widget_Controller extends MVC_Controller
{
	public function index()
	{
		$this->header->allow(site_url);
        
        $type = $this->sanitize->string($this->url->segment(3));
        set_template($this->sanitize->string($this->url->segment(4)));

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

        $this->cache->container("system.blocks");

        if($this->cache->empty()):
            $blocks = [];
            
            foreach($this->system->getBlocks() as $key => $value):
                $blocks[$key] = $this->smarty->fetch("string: {$this->sanitize->htmlDecode($value)}");
            endforeach;

            $this->cache->setArray($blocks);
        endif;

        set_blocks($this->cache->getAll());

		/**
		 * @type Modals
		 * @desc Modal template processor
		 */

		if($type == "modal"):
			$tpl = $this->sanitize->string($this->url->segment(5));
			$id = $this->sanitize->string($this->url->segment(6));

			if(find("zender.", $tpl)):
				if(!in_array($tpl, ["zender.languages"])):
					if(!$this->session->has("logged")):
	            		response(302);
	            	endif;
				endif;

				$tpl = (string) Stringy\create($tpl)->removeLeft("zender.");

				if(!$this->smarty->templateExists(template . "/widgets/modals/{$tpl}.tpl")):
		        	response(500, __("lang_response_invalid"));
				endif;
			else:
	            $this->cache->container("system.modals");

	            if(!$this->cache->has($tpl)):
	            	if($this->widget->checkModal($tpl) > 0)
	            		$modal = $this->widget->getModal($tpl);
	            	else
	            		response(500, __("lang_response_invalid"));

	            	$this->cache->set($tpl, $modal);
	            else:
	            	$modal = $this->cache->get($tpl);
	            endif;
			endif;

			switch($tpl):
				case "languages":
					$this->cache->container("system.languages");

			        if($this->cache->empty()):
			            $this->cache->setArray($this->system->getLanguages());
			        endif;

					$vars = [
						"template" => [
							"title" => __("lang_widget_alllang_title"),
							"data" => [
								"languages" => $this->cache->getAll()
							]
						],
						"handler" => [
							"size" => "md"
						]
					];
					
					break;
				case "system.update":
					$vars = [
						"template" => [
							"title" => __("lang_widget_systemupdate_title")
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"size" => "md",
							"loader" => __("lang_widget_systemupdate_loader"),
							"require" => "update|{$GLOBALS["__"]("lang_widget_systemupdate_requireupdate")}"
						]
					];
					
					break;
				case "user.settings":
					try {
						$user = $this->widget->getUser(logged_id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$formatting = isset($user["formatting"]) && !empty($user["formatting"]) ? json_decode($user["formatting"], true) : [
				        "clock" => "g:i A",
				        "date" => "n/j/Y",
				        "container" => [
        					"clock_format" => 1,
        					"date_format" => 1,
        					"date_separator" => 2,
        					"separator_selected" => "/" 
        				]
				    ]; 

					$vars = [
						"template" => [
							"title" => __("lang_modal_usersettings_title"),
							"data" => [
								"user" => $user,
								"formatting" => $formatting,
								"avatar" => $this->file->exists("uploads/avatars/" . logged_hash . ".jpg"),
								"timezones" => $this->timezones->generate(),
								"countries" => \CountryCodes::get("alpha2", "country")
							]
						],
						"handler" => [
							"id" => logged_id,
							"tpl" => $tpl,
							"type" => "update",
							"size" => "md",
							"position" => "right",
							"loader" => __("lang_widget_usersettings_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_name")}<=>email|{$GLOBALS["__"]("lang_require_email")}"
						]
					];
					
					break;
				case "user.subscription":
					$vars = [
						"template" => [
							"title" => __("lang_modal_subscription_title"),
							"data" => [
								"usage" => [
		                            "quota" => [
		                            	"sms_send" => number_format($this->system->countQuota(logged_id, "sent")),
		                            	"sms_receive" => number_format($this->system->countQuota(logged_id, "received")),
		                            	"wa_send" => number_format($this->system->countQuota(logged_id, "wa_sent")),
		                            	"wa_receive" => number_format($this->system->countQuota(logged_id, "wa_received")),
		                            	"ussd" => number_format($this->system->countQuota(logged_id, "ussd")),
		                            	"notifications" => number_format($this->system->countQuota(logged_id, "notifications"))
		                            ],
		                            "scheduled" => number_format($this->system->countScheduled(logged_id)),
		                            "contacts" => number_format($this->system->countContacts(logged_id)),
		                            "keys" => number_format($this->system->countKeys(logged_id)),
		                            "webhooks" => number_format($this->system->countWebhooks(logged_id)),
		                            "actions" => number_format($this->system->countActions(logged_id)),
		                            "devices" => number_format($this->system->countDevices(logged_id)),
		                            "wa_accounts" => number_format($this->system->countWaAccounts(logged_id))
		                        ],
		                        "subscription" => set_subscription(
				                    $this->system->checkSubscription(logged_id), 
				                    $this->system->getSubscription(false, logged_id), 
				                    $this->system->getSubscription(false, false, true)
				                )
							]
						],
						"handler" => [
							"size" => "lg"
						]
					];
					
					break;
				case "payment":
					if($this->sanitize->isInt($id)):
						$duration = $this->sanitize->string($this->url->segment(7));

						if(!$this->sanitize->isInt($duration))
							response(500, __("lang_response_invalid"));

						if($duration < 1)
							response(500, __("lang_response_invalid"));

						foreach(["paypal", "mollie", "bank"] as $provider):
							if(in_array($provider, explode(",", system_providers)))
								$providers[$provider] = true;
							else
								$providers[$provider] = false;
						endforeach;

						if($this->widget->checkPackage($id) < 1)
							response(500, __("lang_response_invalid"));

						$package = $this->widget->getPackage($id);

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

				        $base_rate = $rates["data"]["USD"] / $rates["data"][strtoupper(system_currency)];

				        $price = (int) round(($base_rate * $package["price"]) * $rates["data"]["USD"]);

				        $item = [
				        	"type" => 1,
				        	"data" => [
								"base_currency" => strtoupper(system_currency),
								"price" => $price,
								"package" => $package,
								"duration" => $duration,
								"user" => [
									"id" => logged_id,
									"hash" => logged_hash,
									"name" => logged_name,
									"email" => logged_email
								]
							]
				        ];

				        $itemPlugin = [
				        	"type" => 1,
				        	"data" => [
								"base_currency" => strtoupper(system_currency),
								"price" => (int) round($package["price"]),
								"package" => $package,
								"duration" => $duration,
								"user" => [
									"id" => logged_id,
									"hash" => logged_hash,
									"name" => logged_name,
									"email" => logged_email
								]
							]
				        ];

				        $this->cache->set("order." . logged_hash, $item);
				        $this->cache->set("order.plugin." . logged_hash, $itemPlugin);

						$vars = [
							"template" => [
								"title" => __("lang_widget_payment_title"),
								"data" => [
									"original_price" => $package["price"] * $duration,
									"paypal_itemname" => $package["name"],
									"paypal_amount" => $price * $duration,
									"paypal_url" => system_paypal_test < 2 ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr",
									"paypal_itemid" => $this->hash->encode(logged_id, system_token),
									"paypal_return_url" => site_url("payment/success/paypal", true),
									"paypal_cancel_url" => site_url("payment/cancel", true),
									"paypal_notify_url" => site_url("payment/webhook/paypal", true),
									"providers" => $providers
								]
							],
							"handler" => [
								"tpl" => $tpl
							]
						];
					else:
						$credits = $this->sanitize->string($this->url->segment(7));

						if(!$this->sanitize->isInt($credits))
							response(500, __("lang_response_invalid"));

						if($credits < 10)
							response(500, __("lang_widget_payment_creditamountnotless"));

						foreach(["paypal", "mollie", "bank"] as $provider):
							if(in_array($provider, explode(",", system_providers)))
								$providers[$provider] = true;
							else
								$providers[$provider] = false;
						endforeach;

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

				        $base_rate = $rates["data"]["USD"] / $rates["data"][strtoupper(system_currency)];

				        $price = (int) round(($base_rate * $credits) * $rates["data"]["USD"]);

						$item = [
				        	"type" => 2,
				        	"data" => [
								"base_currency" => strtoupper(system_currency),
								"price" => $price,
								"credits" => $credits,
								"user" => [
									"id" => logged_id,
									"hash" => logged_hash,
									"name" => logged_name,
									"email" => logged_email
								]
							]
				        ];

				        $itemPlugin = [
				        	"type" => 2,
				        	"data" => [
								"base_currency" => strtoupper(system_currency),
								"price" => (int) round($credits),
								"credits" => $credits,
								"user" => [
									"id" => logged_id,
									"hash" => logged_hash,
									"name" => logged_name,
									"email" => logged_email
								]
							]
				        ];

						$this->cache->set("order." . logged_hash, $item);
						$this->cache->set("order.plugin." . logged_hash, $itemPlugin);

						$vars = [
							"template" => [
								"title" => __("lang_widget_payment_title"),
								"data" => [
									"original_price" => $credits,
									"paypal_itemname" => __("lang_widget_payment_creditslabel"),
									"paypal_amount" => $price,
									"paypal_url" => system_paypal_test < 2 ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr",
									"paypal_itemid" => $this->hash->encode(logged_id, system_token),
									"paypal_return_url" => site_url("/payment/success/paypal", true),
									"paypal_cancel_url" => site_url("/payment/cancel", true),
									"paypal_notify_url" => site_url("/payment/webhook/paypal", true),
									"providers" => $providers
								]
							],
							"handler" => [
								"tpl" => $tpl
							]
						];
					endif;
					
					break;
				case "view":
					$type = explode("-", $id);

					if(count($type) < 2)
						response(500, __("lang_response_invalid"));

					if(!$this->sanitize->isInt($type[1]))
						response(500, __("lang_response_invalid"));

					switch($type[0]):
						case "sent":
							$title = __("lang_and_view20");
							$icon = "book-reader";
							$content = $this->widget->getContent($type[1], "sent", "message");

							break;
						case "received":
							$title = __("lang_and_view20");
							$icon = "book-reader";
							$content = $this->widget->getContent($type[1], "received", "message");

							break;
						case "wa.sent":
							$title = __("lang_and_view20");
							$icon = "book-reader";
							try {
								$msgDecode = json_decode($this->widget->getContent($type[1], "wa_sent", "message"), true, JSON_THROW_ON_ERROR);
								$waMessage = isset($msgDecode["text"]) ? $msgDecode["text"] : $msgDecode["caption"];
							} catch(Exception $e){
								$waMessage = $this->widget->getContent($type[1], "wa_sent", "message");
							}
							$content = $waMessage;
							
							break;
						case "wa.received":
							$title = __("lang_and_view20");
							$icon = "book-reader";
							$content = $this->widget->getContent($type[1], "wa_received", "message");

							break;
						case "bank":
							$title = __("lang_widgets_viewbank_title");
							$icon = "money-check";
							$content = $this->lex->parse(system_bank_template, [
		        				"user" => [
		        					"name" => logged_name,
		        					"email" => logged_email,
		        					"country" => strtoupper(logged_country)
		        				],
		        				"order" => [
		        					"price" => number_format($type[1]) . " " . strtoupper(system_currency)
		        				]
		        			]);

							break;
						default:
							response(500, __("lang_response_invalid"));
					endswitch;

					$vars = [
						"template" => [
							"data" => [
								"title" => $title,
								"icon" => $icon,
								"content" => $content ? $content : __("lang_widget_view_contentremoved")
							]
						],
						"handler" => [
							"size" => "md"
						]
					];

					break;
				case "packages":
					$vars = [
						"template" => [
							"title" => __("lang_modal_packages_title"),
							"data" => [
								"packages" => $this->widget->getPackages(true, system_freemodel < 2 ? true : false)
							]
						],
						"handler" => [
							"size" => "xl"
						]
					];
					
					break;
				case "redeem":
					$vars = [
						"template" => [
							"title" => __("lang_widget_redeem_title")
						],
						"handler" => [
							"id" => $id,
							"tpl" => "redeem",
							"type" => "create",
							"loader" => __("lang_widget_redeem_loader"),
							"require" => "code|{$GLOBALS["__"]("lang_require_vouchercode")}"
						]
					];
					
					break;
				case "add.duration":
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					if($this->widget->checkPackage($id) < 1)
						response(500, __("lang_response_invalid"));

					$vars = [
						"template" => [
							"title" => __("lang_form_durationtitle"),
							"data" => [
								"package" => $this->widget->getPackage($id)
							]
						],
						"handler" => [
							"tpl" => $tpl
						]
					];
					
					break;
				case "add.credits":
					$vars = [
						"template" => [
							"title" => __("lang_widget_addcredits_title")
						],
						"handler" => [
							"size" => "sm"
						]
					];
					
					break;
				case "add.payout":
					$vars = [
						"template" => [
							"title" => __("lang_widget_addpayout_title")
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"size" => "md",
							"loader" => __("lang_widget_addpayout_loader"),
							"require" => "amount|{$GLOBALS["__"]("lang_widget_addpayout_requireamount")}<=>provider|{$GLOBALS["__"]("lang_widget_addpayout_requireprovider")}<=>address|{$GLOBALS["__"]("lang_widget_addpayout_requireaddress")}"
						]
					];
					
					break;
				case "sms.quick":
					try {
						$phoneSample = $this->phone->getExampleNumber(logged_country, Brick\PhoneNumber\PhoneNumberType::MOBILE);
					} catch(Exception $e){
						$phoneSample = "+639123456789";
					}

					$vars = [
						"template" => [
							"title" => __("lang_modal_smsquick_title"),
							"data" => [
								"phone" => $phoneSample,
								"devices" => $this->widget->getDevices(logged_id),
								"devicesGlobal" => $this->widget->getGlobalDevices(logged_id),
								"gateways" => $this->widget->getGateways(),
								"shorteners" => $this->widget->getShorteners(),
								"spintax_sample" => [
									"main" => ___(__("lang_form_spintaxsample_main"), [
										"<strong>{" . __("lang_form_spintaxsample_good") . "|" . __("lang_form_spintaxsample_bad") . "}</strong>"
									]),
									"good" => __("lang_form_spintaxsample_good"),
									"bad" => __("lang_form_spintaxsample_bad")
								]
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"size" => "md",
							"position" => "right",
							"table" => "android.sent",
							"loader" => __("lang_widget_smsquick_loader"),
							"require" => "phone|{$GLOBALS["__"]("lang_require_phone")}<=>sim|{$GLOBALS["__"]("lang_require_sim")}<=>priority|{$GLOBALS["__"]("lang_require_priority")}<=>message|{$GLOBALS["__"]("lang_require_message")}"
						]
					];
					
					break;
				case "sms.bulk":
					try {
						$phoneSample = $this->phone->getExampleNumber(logged_country, Brick\PhoneNumber\PhoneNumberType::MOBILE);
					} catch(Exception $e){
						$phoneSample = "+639123456789";
					}

					$vars = [
						"template" => [
							"title" => __("lang_modal_smsbulk_title"),
							"data" => [
								"number" => $phoneSample,
								"groups" => $this->widget->getGroups(logged_id),
								"devices" => $this->widget->getDevices(logged_id),
								"devicesGlobal" => $this->widget->getGlobalDevices(logged_id),
								"gateways" => $this->widget->getGateways(),
								"shorteners" => $this->widget->getShorteners(),
								"templates" => $this->widget->getTemplates(logged_id),
								"spintax_sample" => [
									"main" => ___(__("lang_form_spintaxsample_main"), [
										"<strong>{" . __("lang_form_spintaxsample_good") . "|" . __("lang_form_spintaxsample_bad") . "}</strong>"
									]),
									"good" => __("lang_form_spintaxsample_good"),
									"bad" => __("lang_form_spintaxsample_bad")
								]
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "android.sent",
							"loader" => __("lang_widget_smsbulk_loader"),
							"require" => "message|{$GLOBALS["__"]("lang_require_message")}"
						]
					];
					
					break;
				case "sms.excel":
					$vars = [
						"template" => [
							"title" => __("lang_widget_smsexcel_title"),
							"data" => [
								"devices" => $this->widget->getDevices(logged_id),
								"devicesGlobal" => $this->widget->getGlobalDevices(logged_id),
								"gateways" => $this->widget->getGateways(),
								"shorteners" => $this->widget->getShorteners(),
								"templates" => $this->widget->getTemplates(logged_id),
								"spintax_sample" => [
									"main" => ___(__("lang_form_spintaxsample_main"), [
										"<strong>{" . __("lang_form_spintaxsample_good") . "|" . __("lang_form_spintaxsample_bad") . "}</strong>"
									]),
									"good" => __("lang_form_spintaxsample_good"),
									"bad" => __("lang_form_spintaxsample_bad")
								]
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "android.sent",
							"loader" => __("lang_widget_smsexcel_loader")
						]
					];
					
					break;
				case "add.sms.scheduled":
					try {
						$phoneSample = $this->phone->getExampleNumber(logged_country, Brick\PhoneNumber\PhoneNumberType::MOBILE);
					} catch(Exception $e){
						$phoneSample = "+639123456789";
					}

					$vars = [
						"template" => [
							"title" => __("lang_form_scheduled_title"),
							"data" => [
								"number" => $phoneSample,
								"groups" => $this->widget->getGroups(logged_id),
								"devices" => $this->widget->getDevices(logged_id),
								"devicesGlobal" => $this->widget->getGlobalDevices(logged_id),
								"gateways" => $this->widget->getGateways(),
								"shorteners" => $this->widget->getShorteners(),
								"templates" => $this->widget->getTemplates(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "android.scheduled",
							"loader" => __("lang_widget_addsmsscheduled_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_scheduled_name")}<=>groups|{$GLOBALS["__"]("lang_require_groups")}<=>device|{$GLOBALS["__"]("lang_require_device")}<=>sim|{$GLOBALS["__"]("lang_require_sim")}<=>schedule|{$GLOBALS["__"]("lang_require_scheduled_date")}<=>message|{$GLOBALS["__"]("lang_require_message")}"
						]
					];
					
					break;
				case "edit.sms.scheduled":
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$scheduled = $this->widget->getScheduled(logged_id, $id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					try {
						$phoneSample = $this->phone->getExampleNumber(logged_country, Brick\PhoneNumber\PhoneNumberType::MOBILE);
					} catch(Exception $e){
						$phoneSample = "+639123456789";
					}

					$vars = [
						"template" => [
							"title" => __("lang_widget_editscheduled_title"),
							"data" => [
								"number" => $phoneSample,
								"groups" => $this->widget->getGroups(logged_id),
								"devices" => $this->widget->getDevices(logged_id),
								"devicesGlobal" => $this->widget->getGlobalDevices(logged_id),
								"gateways" => $this->widget->getGateways(),
								"shorteners" => $this->widget->getShorteners(),
								"scheduled" => $scheduled
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"table" => "android.scheduled",
							"loader" => __("lang_widget_editscheduled_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_scheduled_name")}<=>groups|{$GLOBALS["__"]("lang_require_groups")}<=>device|{$GLOBALS["__"]("lang_require_device")}<=>sim|{$GLOBALS["__"]("lang_require_sim")}<=>schedule|{$GLOBALS["__"]("lang_require_scheduled_date")}<=>message|{$GLOBALS["__"]("lang_require_message")}"
						]
					];
					
					break;
				case "add.ussd":
					$vars = [
						"template" => [
							"title" => __("lang_widget_addussd_title"),
							"data" => [
								"devices" => $this->widget->getDevices(logged_id, true)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"size" => "md",
							"table" => "android.ussd",
							"loader" => __("lang_widget_addussd_loader"),
							"require" => "code|{$GLOBALS["__"]("lang_require_phone")}<=>sim|{$GLOBALS["__"]("lang_require_sim")}"
						]
					];
					
					break;
				case "add.device":
					$vars = [
						"template" => [
							"title" => __("lang_modal_adddevice_title"),
							"data" => [
								"hash" => $this->hash->encode(logged_id, system_token),
								"site_url" => site_url(false, true)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"position" => "right"
						]
					];
					
					break;
				case "edit.device":
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$device = $this->widget->getDevice($id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$vars = [
						"template" => [
							"title" => __("lang_widget_editdevice_title"),
							"data" => [
								"device" => $device,
								"partner" => $this->system->getPartnership(logged_id),
								"countries" => \CountryCodes::get("alpha2", "country")
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"type" => "update",
							"size" => "lg",
							"table" => "devices.registered",
							"loader" => __("lang_widget_editdevice_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_devicename")}<=>random_min|{$GLOBALS["__"]("lang_require_randommin")}<=>random_max|{$GLOBALS["__"]("lang_require_randommax")}"
						]
					];
					
					break;
				case "whatsapp.quick":
					try {
						$phoneSample = $this->phone->getExampleNumber(logged_country, Brick\PhoneNumber\PhoneNumberType::MOBILE);
					} catch(Exception $e){
						$phoneSample = "+639123456789";
					}

					$vars = [
						"template" => [
							"title" => __("lang_widget_waquick_title"),
							"data" => [
								"phone" => $phoneSample,
								"accounts" => $this->widget->getWaAccounts(logged_id),
								"shorteners" => $this->widget->getShorteners(),
								"spintax_sample" => [
									"main" => ___(__("lang_form_spintaxsample_main"), [
										"<strong>{" . __("lang_form_spintaxsample_good") . "|" . __("lang_form_spintaxsample_bad") . "}</strong>"
									]),
									"good" => __("lang_form_spintaxsample_good"),
									"bad" => __("lang_form_spintaxsample_bad")
								]
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"size" => "lg",
							"table" => "whatsapp.sent",
							"loader" => __("lang_widget_waquick_loader"),
							"require" => "phone|{$GLOBALS["__"]("lang_require_phone")}<=>account|{$GLOBALS["__"]("lang_widget_waquick_requireaccountnew1")}"
						]
					];
					
					break;
				case "whatsapp.bulk":
					try {
						$phoneSample = $this->phone->getExampleNumber(logged_country, Brick\PhoneNumber\PhoneNumberType::MOBILE);
					} catch(Exception $e){
						$phoneSample = "+639123456789";
					}

					$vars = [
						"template" => [
							"title" => __("lang_widget_wabulk_title"),
							"data" => [
								"number" => $phoneSample,
								"accounts" => $this->widget->getWaAccounts(logged_id),
								"groups" => $this->widget->getGroups(logged_id),
								"shorteners" => $this->widget->getShorteners(),
								"templates" => $this->widget->getTemplates(logged_id),
								"spintax_sample" => [
									"main" => ___(__("lang_form_spintaxsample_main"), [
										"<strong>{" . __("lang_form_spintaxsample_good") . "|" . __("lang_form_spintaxsample_bad") . "}</strong>"
									]),
									"good" => __("lang_form_spintaxsample_good"),
									"bad" => __("lang_form_spintaxsample_bad")
								]
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"size" => "lg",
							"table" => "whatsapp.sent",
							"loader" => __("lang_widget_wabulk_loader"),
							"require" => "account|{$GLOBALS["__"]("lang_widget_waquick_requireaccountnew1")}"
						]
					];
					
					break;
				case "whatsapp.excel":
					$vars = [
						"template" => [
							"title" => __("lang_widget_waexcel_title"),
							"data" => [
								"accounts" => $this->widget->getWaAccounts(logged_id),
								"groups" => $this->widget->getGroups(logged_id),
								"shorteners" => $this->widget->getShorteners(),
								"templates" => $this->widget->getTemplates(logged_id),
								"spintax_sample" => [
									"main" => ___(__("lang_form_spintaxsample_main"), [
										"<strong>{" . __("lang_form_spintaxsample_good") . "|" . __("lang_form_spintaxsample_bad") . "}</strong>"
									]),
									"good" => __("lang_form_spintaxsample_good"),
									"bad" => __("lang_form_spintaxsample_bad")
								]
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"size" => "lg",
							"table" => "whatsapp.sent",
							"loader" => __("lang_widget_waexcel_loader"),
							"require" => "account|{$GLOBALS["__"]("lang_widget_waquick_requireaccountnew1")}"
						]
					];
					
					break;
				case "add.whatsapp.scheduled":
					try {
						$phoneSample = $this->phone->getExampleNumber(logged_country, Brick\PhoneNumber\PhoneNumberType::MOBILE);
					} catch(Exception $e){
						$phoneSample = "+639123456789";
					}

					$vars = [
						"template" => [
							"title" => __("lang_widget_wascheduled_title"),
							"data" => [
								"number" => $phoneSample,
								"accounts" => $this->widget->getWaAccounts(logged_id),
								"groups" => $this->widget->getGroups(logged_id),
								"shorteners" => $this->widget->getShorteners(),
								"templates" => $this->widget->getTemplates(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"size" => "lg",
							"table" => "whatsapp.scheduled",
							"loader" => __("lang_widget_wascheduled_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_scheduled_name")}<=>groups|{$GLOBALS["__"]("lang_require_groups")}<=>account|{$GLOBALS["__"]("lang_widget_wascheduled_requireaccount")}<=>schedule|{$GLOBALS["__"]("lang_require_scheduled_date")}<=>message|{$GLOBALS["__"]("lang_require_message")}"
						]
					];
					
					break;
				case "edit.whatsapp.scheduled":
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$scheduled = $this->widget->getScheduled(logged_id, $id, true);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					try {
						$phoneSample = $this->phone->getExampleNumber(logged_country, Brick\PhoneNumber\PhoneNumberType::MOBILE);
					} catch(Exception $e){
						$phoneSample = "+639123456789";
					}

					$decodeMessage = json_decode($scheduled["message"], true);

					$scheduled["message"] = decodeBraces(isset($decodeMessage["text"]) ? $decodeMessage["text"] : $decodeMessage["caption"]);

					$vars = [
						"template" => [
							"title" => __("lang_widget_waeditscheduled_title"),
							"data" => [
								"number" => $phoneSample,
								"accounts" => $this->widget->getWaAccounts(logged_id),
								"groups" => $this->widget->getGroups(logged_id),
								"shorteners" => $this->widget->getShorteners(),
								"templates" => $this->widget->getTemplates(logged_id),
								"scheduled" => $scheduled
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"type" => "update",
							"size" => "lg",
							"table" => "whatsapp.scheduled",
							"loader" => __("lang_widget_waeditscheduled_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_scheduled_name")}<=>groups|{$GLOBALS["__"]("lang_require_groups")}<=>account|{$GLOBALS["__"]("lang_widget_waeditscheduled_requireaccount")}<=>schedule|{$GLOBALS["__"]("lang_require_scheduled_date")}<=>message|{$GLOBALS["__"]("lang_require_message")}"
						]
					];
					
					break;
				case "whatsapp.groups":
					$vars = [
						"template" => [
							"title" => __("lang_widget_fetchgroups_titlefetch"),
							"data" => [
								"accounts" => $this->widget->getWaAccounts(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"table" => "whatsapp.groups",
							"loader" => __("lang_widget_fetchgroups_loaderfetch"),
							"require" => "account|{$GLOBALS["__"]("lang_widget_waquick_requireaccountnew1")}"
						]
					];
					
					break;
				case "add.whatsapp":
					$vars = [
						"template" => [
							"title" => __("lang_widget_waaddaccount_title"),
							"data" => [
								"linkbtn" => "<strong class=\"text-uppercase\">{$GLOBALS["__"]("lang_whatsapp_accountlink_linkadevicebtn")}</strong>"
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md"
						]
					];
					
					break;
				case "edit.whatsapp":
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$account = $this->widget->getWhatsapp($id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$vars = [
						"template" => [
							"title" => __("lang_widgets_editwhatsapp_title"),
							"data" => [
								"account" => $account
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"type" => "update",
							"size" => "md",
							"table" => "whatsapp.accounts",
							"loader" => __("lang_widgets_editwhatsapp_loader"),
							"require" => "random_min|{$GLOBALS["__"]("lang_require_randommin")}<=>random_max|{$GLOBALS["__"]("lang_require_randommax")}"
						]
					];
					
					break;
				case "import.contacts":
					$vars = [
						"template" => [
							"title" => __("lang_widget_importcontacts_title")
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "contacts.saved",
							"loader" => __("lang_widget_importcontacts_loaded")
						]
					];
					
					break;
				case "add.contact":
					try {
						$phoneSample = $this->phone->getExampleNumber(logged_country, Brick\PhoneNumber\PhoneNumberType::MOBILE);
					} catch(Exception $e){
						$phoneSample = "+639123456789";
					}

					$vars = [
						"template" => [
							"title" => __("lang_modal_addcontact_title"),
							"data" => [
								"number" => $phoneSample,
								"groups" => $this->widget->getGroups(logged_id)
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"table" => "contacts.saved",
							"loader" => __("lang_widget_addcontact_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_contactname")}<=>phone|{$GLOBALS["__"]("lang_require_phone")}<=>groups|{$GLOBALS["__"]("lang_require_group")}"
						]
					];
					
					break;
				case "edit.contact":
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$contact = $this->widget->getContact(logged_id, $id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					try {
						$phoneSample = $this->phone->getExampleNumber(logged_country, Brick\PhoneNumber\PhoneNumberType::MOBILE);
					} catch(Exception $e){
						$phoneSample = "+639123456789";
					}

					$vars = [
						"template" => [
							"title" => __("lang_modal_editcontact_title"),
							"data" => [
								"contact" => $contact,
								"number" => $phoneSample,
								"groups" => $this->widget->getGroups(logged_id)
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"type" => "update",
							"table" => "contacts.saved",
							"loader" => __("lang_widgets_editcontact_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_lang_require_contactname")}<=>phone|{$GLOBALS["__"]("lang_require_phone")}<=>groups|{$GLOBALS["__"]("lang_require_group")}"
						]
					];
					
					break;
				case "add.group":
					$vars = [
						"template" => [
							"title" => __("lang_modal_addgroup_title")
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"table" => "contacts.groups",
							"loader" => __("lang_widgets_addgroup_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_groupname")}"
						]
					];
					
					break;
				case "edit.group":
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$group = $this->widget->getGroup(logged_id, $id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$vars = [
						"template" => [
							"title" => __("lang_modal_editgroup_title"),
							"data" => [
								"group" => $group
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"type" => "update",
							"table" => "contacts.groups",
							"loader" => __("lang_widgets_editgroup_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_groupname")}"
						]
					];
					
					break;
				case "add.apikey":
					$vars = [
						"template" => [
							"title" => __("lang_modal_addkey_title")
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "tools.keys",
							"loader" => __("lang_widgets_addapikey_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_apiname")}<=>permissions|{$GLOBALS["__"]("lang_require_permissions")}"
						]
					];
					
					break;
				case "edit.apikey":
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$key = $this->widget->getKey(logged_id, $id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$permissions = [
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
        				"create_whatsapp",
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
        			];

					$vars = [
						"template" => [
							"title" => __("lang_modal_editkey_title"),
							"data" => [
								"key" => $key,
								"permissions" => $permissions
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "md",
							"type" => "update",
							"table" => "tools.keys",
							"loader" => __("lang_widgets_editapikey_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_apiname")}<=>permissions|{$GLOBALS["__"]("lang_require_permissions")}"
						]
					];
					
					break;
				case "add.webhook":
					$vars = [
						"template" => [
							"title" => __("lang_widgets_addwebhook_title")
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"size" => "md",
							"table" => "tools.webhooks",
							"loader" => __("lang_widgets_addwebhook_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_hookname")}<=>url|{$GLOBALS["__"]("lang_require_hookurl")}<=>events|{$GLOBALS["__"]("lang_widgets_addwebhook_requireevents")}"
						]
					];
					
					break;
				case "edit.webhook":
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$webhook = $this->widget->getWebhook($id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$vars = [
						"template" => [
							"title" => __("lang_modal_edithook_title"),
							"data" => [
								"webhook" => $webhook
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "md",
							"type" => "update",
							"table" => "tools.webhooks",
							"loader" => __("lang_widgets_editwebhook_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_hookname")}<=>url|{$GLOBALS["__"]("lang_require_hookurl")}<=>events|{$GLOBALS["__"]("lang_widgets_addwebhook_requireevents")}"
						]
					];
					
					break;
				case "add.hook":
					$vars = [
						"template" => [
							"title" => __("lang_form_hook_addtitle")
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "tools.actions",
							"loader" => __("lang_widgets_addhook_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_action_name")}<=>event|{$GLOBALS["__"]("lang_require_action_event")}<=>link|{$GLOBALS["__"]("lang_require_action_link")}"
						]
					];
					
					break;
				case "edit.hook":
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$action = $this->widget->getAction(logged_id, $id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$vars = [
						"template" => [
							"title" => __("lang_form_hook_edittitle"),
							"data" => [
								"hook" => $action
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "md",
							"type" => "update",
							"table" => "tools.actions",
							"loader" => __("lang_widgets_edithook_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_action_name")}<=>event|{$GLOBALS["__"]("lang_require_action_event")}<=>link|{$GLOBALS["__"]("lang_require_action_link")}"
						]
					];
					
					break;
				case "add.autoreply":
					$vars = [
						"template" => [
							"title" => __("lang_form_autoreply_addtitle")
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "tools.actions",
							"loader" => __("lang_widgets_addautoreply_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_action_name")}<=>keywords|{$GLOBALS["__"]("lang_require_action_keywords")}<=>message|{$GLOBALS["__"]("lang_require_action_message")}"
						]
					];
					
					break;
				case "edit.autoreply":
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$action = $this->widget->getAction(logged_id, $id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$vars = [
						"template" => [
							"title" => __("lang_form_autoreply_edittitle"),
							"data" => [
								"autoreply" => $action
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "md",
							"type" => "update",
							"table" => "tools.actions",
							"loader" => __("lang_widgets_editautoreply_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_action_name")}<=>keywords|{$GLOBALS["__"]("lang_require_action_keywords")}<=>message|{$GLOBALS["__"]("lang_require_action_message")}"
						]
					];
					
					break;
				case "add.template":
					$vars = [
						"template" => [
							"title" => __("lang_modal_addtemplate_title"),
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "messages.templates",
							"loader" => __("lang_widgets_addtemplate_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_templatename")}<=>format|{$GLOBALS["__"]("lang_require_templateformat")}"
						]
					];
					
					break;
				case "edit.template":
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$template_ = $this->widget->getTemplate($id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$vars = [
						"template" => [
							"title" => __("lang_modal_edittemplate_title"),
							"data" => [
								"template" => $template_
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"table" => "messages.templates",
							"loader" => __("lang_widgets_edittemplate_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_templatename")}<=>format|{$GLOBALS["__"]("lang_require_templateformat")}"
						]
					];
					
					break;
				case "admin.builder":
					if(!super_admin)
						response(500, __("lang_response_no_permission"));

					$vars = [
						"template" => [
							"title" => __("lang_modal_buildersettings_title"),
							"data" => [
								"builder" => $this->system->getSettings(),
								"assets" => [
									"logo" => $this->file->exists("uploads/builder/logo.png"),
									"logo_login" => $this->file->exists("uploads/builder/logo-login.png"),
									"icon" => $this->file->exists("uploads/builder/icon.png"),
									"splash" => $this->file->exists("uploads/builder/splash.png"),
									"google" => $this->file->exists("system/storage/temporary/google.json"),
									"firebase" => $this->file->exists("system/storage/temporary/firebase.json")
								],
								"layout" => empty(system_app_layout) ? $this->file->get("system/storage/temporary/device.html") : system_app_layout
							]
						],
						"handler" => [
							"id" => 1,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"position" => "right",
							"loader" => __("lang_widgets_adminbuilder_loader"),
							"require" => "package_name|{$GLOBALS["__"]("lang_require_packagename")}<=>app_name|{$GLOBALS["__"]("lang_require_appname")}<=>app_color|{$GLOBALS["__"]("lang_require_appcolor")}"
						]
					];
					
					break;
				case "admin.settings":
					if(!super_admin)
						response(500, __("lang_response_no_permission"));

					foreach(["paypal", "mollie", "bank"] as $provider):
						if(in_array($provider, explode(",", system_providers)))
							$providers[$provider] = true;
						else
							$providers[$provider] = false;
					endforeach;

					foreach(["facebook", "google", "vk"] as $social):
						if(in_array($social, explode(",", system_social_platforms)))
							$social_platforms[$social] = true;
						else
							$social_platforms[$social] = false;
					endforeach;

					$currencies = ["AED", "AFN", "ALL", "AMD", "ANG", "AOA", "ARS", "AUD", "AWG", "AZN", "BAM", "BBD", "BDT", "BGN", "BHD", "BIF", "BMD", "BND", "BOB", "BRL", "BSD", "BTC", "BTN", "BWP", "BYN", "BZD", "CAD", "CDF", "CHF", "CLF", "CLP", "CNH", "CNY", "COP", "CRC", "CUC", "CUP", "CVE", "CZK", "DJF", "DKK", "DOP", "DZD", "EGP", "ERN", "ETB", "EUR", "FJD", "FKP", "GBP", "GEL", "GGP", "GHS", "GIP", "GMD", "GNF", "GTQ", "GYD", "HKD", "HNL", "HRK", "HTG", "HUF", "IDR", "ILS", "IMP", "INR", "IQD", "IRR", "ISK", "JEP", "JMD", "JOD", "JPY", "KES", "KGS", "KHR", "KMF", "KPW", "KRW", "KWD", "KYD", "KZT", "LAK", "LBP", "LKR", "LRD", "LSL", "LYD", "MAD", "MDL", "MGA", "MKD", "MMK", "MNT", "MOP", "MRO", "MRU", "MUR", "MVR", "MWK", "MXN", "MYR", "MZN", "NAD", "NGN", "NIO", "NOK", "NPR", "NZD", "OMR", "PAB", "PEN", "PGK", "PHP", "PKR", "PLN", "PYG", "QAR", "RON", "RSD", "RUB", "RWF", "SAR", "SBD", "SCR", "SDG", "SEK", "SGD", "SHP", "SLL", "SOS", "SRD", "SSP", "STD", "STN", "SVC", "SYP", "SZL", "THB", "TJS", "TMT", "TND", "TOP", "TRY", "TTD", "TWD", "TZS", "UAH", "UGX", "USD", "UYU", "UZS", "VES", "VND", "VUV", "WST", "XAF", "XAG", "XAU", "XCD", "XDR", "XOF", "XPD", "XPF", "XPT", "YER", "ZAR", "ZMW", "ZWL"];

					$vars = [
						"template" => [
							"title" => __("lang_modal_systemsettings_title"),
							"data" => [
								"system" => $this->system->getSettings(),
								"languages" => $this->widget->getLanguages(),
								"timezones" => $this->timezones->generate(),
								"countries" => \CountryCodes::get("alpha2", "country"),
								"providers" => $providers,
								"platforms" => $social_platforms,
								"currencies" => $currencies
							]
						],
						"handler" => [
							"id" => 1,
							"tpl" => $tpl,
							"size" => "xl",
							"type" => "update",
							"loader" => __("lang_widgets_adminsettings_loader"),
							"position" => "right"
						]
					];
					
					break;
				case "admin.theme":
					if(!super_admin)
						response(500, __("lang_response_no_permission"));
					
					$vars = [
						"template" => [
							"title" => __("lang_modal_themesettings_title"),
							"data" => [
								"system" => $this->system->getSettings(),
								"script" => $this->file->get("templates/_assets/js/custom.js"),
								"css" => $this->file->get("templates/_assets/css/custom.css"),
								"assets" => [
									"logo_light" => $this->file->exists("uploads/theme/logo-light.png"),
									"logo_dark" => $this->file->exists("uploads/theme/logo-dark.png"),
									"background" => $this->file->exists("uploads/theme/bg.png"),
									"favicon" => $this->file->exists("uploads/theme/favicon.png")
								]
							]
						],
						"handler" => [
							"id" => 1,
							"tpl" => $tpl,
							"size" => "xl",
							"type" => "update",
							"loader" => __("lang_widgets_admintheme_loader")
						]
					];
					
					break;
				case "add.user":
					if(!permission("manage_users"))
						response(500, __("lang_response_no_permission"));

					$vars = [
						"template" => [
							"title" => __("lang_modal_adduser_title"),
							"data" => [
								"timezones" => $this->timezones->generate(),
								"countries" => \CountryCodes::get("alpha2", "country"),
								"roles" => $this->widget->getRoles(),
								"languages" => $this->widget->getLanguages()
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"type" => "create",
							"size" => "md",
							"position" => "right",
							"table" => "administration.users",
							"loader" => __("lang_widgets_adduser_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_name")}<=>email|{$GLOBALS["__"]("lang_require_email")}<=>password|{$GLOBALS["__"]("lang_require_password")}"
						]
					];
					
					break;
				case "edit.user":
					if(!permission("manage_users"))
						response(500, __("lang_response_no_permission"));
					
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$user = $this->widget->getUser($id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$formatting = isset($user["formatting"]) && !empty($user["formatting"]) ? json_decode($user["formatting"], true) : [
				        "clock" => "g:i A",
				        "date" => "n/j/Y",
				        "container" => [
        					"clock_format" => 1,
        					"date_format" => 1,
        					"date_separator" => 2,
        					"separator_selected" => "/" 
        				]
				    ]; 

					$vars = [
						"template" => [
							"title" => __("lang_modal_edituser_title"),
							"data" => [
								"user" => $user,
								"formatting" => $formatting,
								"timezones" => $this->timezones->generate(),
								"countries" => \CountryCodes::get("alpha2", "country"),
								"roles" => $this->widget->getRoles(),
								"languages" => $this->widget->getLanguages()
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"type" => "update",
							"size" => "md",
							"position" => "right",
							"table" => "administration.users",
							"loader" => __("lang_widgets_edituser_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_name")}<=>email|{$GLOBALS["__"]("lang_require_email")}"
						]
					];
					
					break;
				case "add.role":
					if(!permission("manage_roles"))
						response(500, __("lang_response_no_permission"));
					
					$vars = [
						"template" => [
							"title" => __("lang_widgets_addrole_title")
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "administration.roles",
							"loader" => __("lang_widgets_addrole_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_addrole_name")}<=>permissions|{$GLOBALS["__"]("lang_require_addrole_permissions")}"
						]
					];
					
					break;
				case "edit.role":
					if(!permission("manage_roles"))
						response(500, __("lang_response_no_permission"));

					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$role = $this->widget->getRole($id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$permissions = [
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
					];

					$vars = [
						"template" => [
							"title" => __("lang_widget_editrole_title"),
							"data" => [
								"role" => $role,
								"permissions" => $permissions
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "md",
							"type" => "update",
							"table" => "administration.roles",
							"loader" => __("lang_widgets_editrole_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_addrole_name")}<=>permissions|{$GLOBALS["__"]("lang_require_addrole_permissions")}"
						]
					];
					
					break;
				case "add.package":
					if(!permission("manage_packages"))
						response(500, __("lang_response_no_permission"));
					
					$vars = [
						"template" => [
							"title" => __("lang_modal_addpackage_title")
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "administration.packages",
							"loader" => __("lang_widgets_addpackage_loader")
						]
					];
					
					break;
				case "edit.package":
					if(!permission("manage_packages"))
						response(500, __("lang_response_no_permission"));
					
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$package = $this->widget->getPackage($id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$vars = [
						"template" => [
							"title" => __("lang_modal_editpackage_title"),
							"data" => [
								"package" => $package
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"table" => "administration.packages",
							"loader" => __("lang_widgets_editpackage_loader")
						]
					];
					
					break;
				case "add.voucher":
					if(!permission("manage_vouchers"))
						response(500, __("lang_response_no_permission"));
					
					$vars = [
						"template" => [
							"title" => __("lang_form_title_addvoucher"),
							"data" => [
								"packages" => $this->widget->getPackages()
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "administration.vouchers",
							"loader" => __("lang_widgets_addvoucher_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_voucher_name")}<=>count|{$GLOBALS["__"]("lang_require_voucher_count")}<=>package|{$GLOBALS["__"]("lang_require_voucher_package")}"
						]
					];
					
					break;
				case "add.subscription":
					if(!permission("manage_subscriptions"))
						response(500, __("lang_response_no_permission"));
					
					$vars = [
						"template" => [
							"title" => __("lang_form_title_addsubscription"),
							"data" => [
								"users" => $this->widget->getUsers(),
								"packages" => $this->widget->getPackages()
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "administration.subscriptions",
							"loader" => __("lang_widgets_addsubscription_loader"),
							"require" => "user|{$GLOBALS["__"]("lang_require_subscription_user")}<=>package|{$GLOBALS["__"]("lang_require_subscription_user")}"
						]
					];
					
					break;
				case "add.widget":
					if(!permission("manage_widgets"))
						response(500, __("lang_response_no_permission"));
					
					$vars = [
						"template" => [
							"title" => __("lang_modal_addwidget_title")
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "administration.widgets",
							"loader" => __("lang_widgets_addwidgets_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_widgetname")}<=>size|{$GLOBALS["__"]("lang_require_widgetsize")}<=>position|{$GLOBALS["__"]("lang_require_widgetposition")}<=>type|{$GLOBALS["__"]("lang_require_widgettype")}" 
						]
					];
					
					break;
				case "edit.widget":
					if(!permission("manage_widgets"))
						response(500, __("lang_response_no_permission"));
					
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$widget = $this->widget->getWidget($id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$vars = [
						"template" => [
							"title" => __("lang_modal_editwidget_title"),
							"data" => [
								"widget" => $widget,
								"content" => $widget["content"]
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"table" => "administration.widgets",
							"loader" => __("lang_widgets_editwidget_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_widgetname")}<=>size|{$GLOBALS["__"]("lang_require_widgetsize")}<=>position|{$GLOBALS["__"]("lang_require_widgetposition")}<=>type|{$GLOBALS["__"]("lang_require_widgettype")}"
						]
					];
					
					break;
				case "add.page":
					if(!permission("manage_pages"))
						response(500, __("lang_response_no_permission"));
					
					$vars = [
						"template" => [
							"title" => __("lang_widget_addpage_title"),
							"data" => [
								"roles" => $this->widget->getRoles()
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "administration.pages",
							"loader" => __("lang_widgets_addpage_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_pagename")}<=>roles|{$GLOBALS["__"]("lang_require_pageroles")}" 
						]
					];
					
					break;
				case "edit.page":
					if(!permission("manage_pages"))
						response(500, __("lang_response_no_permission"));

					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$page = $this->widget->getPage($id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$vars = [
						"template" => [
							"title" => __("lang_widget_editpage_title"),
							"data" => [
								"page" => $page,
								"roles" => $this->widget->getRoles()
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"table" => "administration.pages",
							"loader" => __("lang_widgets_editpage_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_pagename")}<=>roles|{$GLOBALS["__"]("lang_require_pageroles")}"
						]
					];
					
					break;
				case "add.push":
					if(!permission("manage_marketing"))
						response(500, __("lang_response_no_permission"));
					
					$vars = [
						"template" => [
							"title" => __("lang_widgets_addpush_title"),
							"data" => [
								"users" => $this->widget->getUsers(),
								"roles" => $this->widget->getRoles()
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "administration.marketing",
							"loader" => __("lang_widgets_addpush_loader"),
							"require" => "title|{$GLOBALS["__"]("lang_widgets_addpush_requiretitle")}<=>message|{$GLOBALS["__"]("lang_widgets_addpush_requiremessage")}"
						]
					];
					
					break;
				case "add.notify":
					if(!permission("manage_marketing"))
						response(500, __("lang_response_no_permission"));
					
					$vars = [
						"template" => [
							"title" => __("lang_widgets_addnotify_title"),
							"data" => [
								"users" => $this->widget->getUsers(),
								"roles" => $this->widget->getRoles()
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "administration.marketing",
							"loader" => __("lang_widgets_addnotify_loader"),
							"require" => "title|{$GLOBALS["__"]("lang_widgets_addnotify_requiretitle")}<=>message|{$GLOBALS["__"]("lang_widgets_addnotify_requiremessage")}"
						]
					];
					
					break;
				case "add.mailer":
					if(!permission("manage_marketing"))
						response(500, __("lang_response_no_permission"));
					
					$vars = [
						"template" => [
							"title" => __("lang_widgets_addmailer_title"),
							"data" => [
								"users" => $this->widget->getUsers(),
								"roles" => $this->widget->getRoles()
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "administration.marketing",
							"loader" => __("lang_widgets_addmailer_loader"),
							"require" => "title|{$GLOBALS["__"]("lang_widgets_addmailer_requiretitle")}"
						]
					];
					
					break;
				case "add.language":
					if(!permission("manage_languages"))
						response(500, __("lang_response_no_permission"));

					$vars = [
						"template" => [
							"title" => __("lang_modal_addlanguage_title"),
							"data" => [
								"countries" => \CountryCodes::get("alpha2", "country"),
								"strings" => $this->file->exists("system/storage/temporary/default.lang") ? $this->file->get("system/storage/temporary/default.lang") : false
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "administration.languages",
							"loader" => __("lang_widgets_addlanguage_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_languagename")}<=>iso|{$GLOBALS["__"]("lang_require_languageiso")}<=>translations|{$GLOBALS["__"]("lang_require_languagestr")}"
						]
					];
					
					break;
				case "edit.language":
					if(!permission("manage_languages"))
						response(500, __("lang_response_no_permission"));
					
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$language = $this->widget->getLanguage($id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$vars = [
						"template" => [
							"title" => __("lang_modal_editlanguage_title"),
							"data" => [
								"language" => $language,
								"countries" => \CountryCodes::get("alpha2", "country"),
								"strings" => $this->file->exists("system/languages/" . md5($language["id"]) . ".lang") ? $this->file->get("system/languages/" . md5($language["id"]) . ".lang") : false
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"table" => "administration.languages",
							"loader" => __("lang_widgets_editlanguage_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_require_languagename")}<=>iso|{$GLOBALS["__"]("lang_require_languageiso")}<=>translations|{$GLOBALS["__"]("lang_require_languagestr")}"
						]
					];
					
					break;
				case "add.gateway":
					if(!permission("manage_gateways"))
						response(500, __("lang_response_no_permission"));

					$vars = [
						"template" => [
							"title" => __("lang_widgets_addgateway_title"),
							"data" => [
								"pricing" => $this->file->get("uploads/system/gateway.json")
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "create",
							"table" => "administration.gateways",
							"loader" => __("lang_widgets_addgateway_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_widgets_addgateway_requirename")}"
						]
					];
					
					break;
				case "edit.gateway":
					if(!permission("manage_gateways"))
						response(500, __("lang_response_no_permission"));

					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$gateway = $this->widget->getGateway($id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$vars = [
						"template" => [
							"title" => __("lang_widgets_editgateway_title"),
							"data" => [
								"gateway" => $gateway
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"table" => "administration.gateways",
							"loader" => __("lang_widgets_editgateway_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_widgets_editgateway_requirename")}"
						]
					];
					
					break;
				case "add.shortener":
					if(!permission("manage_shorteners"))
						response(500, __("lang_response_no_permission"));

					$vars = [
						"template" => [
							"title" => __("lang_widgets_addshortener_title"),
							"data" => [
								"packages" => $this->widget->getPackages()
							]
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "md",
							"type" => "create",
							"table" => "administration.shorteners",
							"loader" => __("lang_widgets_addshortener_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_widgets_addshortener_requirename")}"
						]
					];
					
					break;
				case "edit.shortener":
					if(!permission("manage_shorteners"))
						response(500, __("lang_response_no_permission"));

					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$shortener = $this->widget->getShortener($id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$vars = [
						"template" => [
							"title" => __("lang_widgets_editshortener_title"),
							"data" => [
								"shortener" => $shortener,
								"packages" => $this->widget->getPackages()
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "md",
							"type" => "update",
							"table" => "administration.shorteners",
							"loader" => __("lang_widgets_editshortener_loader"),
							"require" => "name|{$GLOBALS["__"]("lang_widgets_editshortener_requirename")}"
						]
					];
					
					break;
				case "add.plugin":
					if(!permission("manage_plugins"))
						response(500, __("lang_response_no_permission"));

					$vars = [
						"template" => [
							"title" => __("lang_modal_addplugin_title")
						],
						"handler" => [
							"tpl" => $tpl,
							"size" => "sm",
							"type" => "create",
							"table" => "administration.plugins",
							"loader" => __("lang_widgets_addplugin_loader"),
							"require" => "plugin|{$GLOBALS["__"]("lang_require_pluginfile")}"
						]
					];
					
					break;
				case "edit.plugin":
					if(!permission("manage_plugins"))
						response(500, __("lang_response_no_permission"));
					
					if(!$this->sanitize->isInt($id))
						response(500, __("lang_response_invalid"));

					try {
						$plugin = $this->widget->getPlugin($id);
					} catch(Exception $e){
						response(500, __("lang_response_invalid"));
					}

					$decodedData = json_decode($plugin["data"], true);

					$pluginName = $decodedData["name"];

					foreach($decodedData["data"] as $key => $value):
						$fields[$key] = $value;
						if(!find("hr", $key))
							$require[] = "{$key}|" . ucfirst($key);
					endforeach;

					$vars = [
						"template" => [
							"title" => __("lang_model_title_editplugin"),
							"data" => [
								"plugin" => [
									"name" => $pluginName,
									"fields" => $fields
								]
							]
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "lg",
							"type" => "update",
							"table" => "administration.plugins",
							"loader" => __("lang_widgets_editplugin_loader"),
							"require" => implode("<=>", $require)
						]
					];
					
					break;
				case "update.plugin":
					if(!permission("manage_plugins"))
						response(500, __("lang_response_no_permission"));

					$vars = [
						"template" => [
							"title" => __("lang_widgets_updateplugin_title")
						],
						"handler" => [
							"id" => $id,
							"tpl" => $tpl,
							"size" => "sm",
							"type" => "update",
							"table" => "administration.plugins",
							"loader" => __("lang_widgets_updateplugin_loader"),
							"require" => "plugin|{$GLOBALS["__"]("lang_require_pluginfile")}"
						]
					];
					
					break;
				default:
					if(!isset($modal))
						response(500, __("lang_response_invalid"));

					$tpl = "default";

					$vars = [
						"template" => [
							"title" => $modal["name"],
							"data" => [
								"modal" => $modal,
								"content" => $this->smarty->fetch("string:" . $this->sanitize->htmlDecode($modal["content"]))
							]
						],
						"handler" => [
							"size" => $modal["size"],
							"position" => ($modal["position"] == "center" ? false : $modal["position"])
						]
					];

			endswitch;

	        $this->smarty->assign($vars["template"]);

	    	response(200, false, [
	    		"vars" => (isset($vars["handler"]) ? $vars["handler"] : false),
	    		"tpl" => $this->smarty->fetch(template . "/widgets/modals/{$tpl}.tpl")
	    	]);
		endif;

		/**
		 * Tabs
		 */

		if($type == "tab"):
			if(!$this->session->has("logged"))
        		response(302);

			$tpl = $this->sanitize->string($this->url->segment(5));

			if(find("zender.", $tpl)):
				$tpl = (string) Stringy\create($tpl)->removeLeft("zender.");
		    endif;

		    $feature = explode(".", $tpl);

		    if(count($feature) < 2)
		    	response(500, __("lang_response_invalid"));

		    switch($feature[0]):
		    	case "rates":
		    		if($feature[1] != "partners"):
		    			$gateway = $this->widget->getGateway($feature[1]);

			    		if(!$gateway)
		    				response(500, __("lang_response_invalid"));

			    		$tpl = "rates.gateway";

			    		$pricing = json_decode($gateway["pricing"], true);

			    		$vars = [
							"handler" => [
								"table" => false,
							],
							"data" => [
								"gateway" => $gateway,
								"pricing" => $pricing
							] 
						];
			    	else:
			    		if(!$this->smarty->templateExists(template . "/widgets/tabs/{$tpl}.tpl"))
		        			response(500, __("lang_response_invalid"));

			    		$vars = [
							"handler" => [
								"table" => $tpl
							]
						];
			    	endif;
		    		

		    		break;
		    	case "docs":
		    		$vars = [
						"handler" => [
							"table" => false
						]
					];

		    		break;
		    	case "administration":
		    		if(in_array($feature[1], ["filemanager", "api"])):
			    		$vars = [
							"handler" => [
								"table" => false,
							]
						];
			    	else:
			    		if(!$this->smarty->templateExists(template . "/widgets/tabs/{$tpl}.tpl"))
		        			response(500, __("lang_response_invalid"));

			    		$vars = [
							"handler" => [
								"table" => $tpl
							]
						];
			    	endif;

		    		break;
		    	default:
		    		if(!$this->smarty->templateExists(template . "/widgets/tabs/{$tpl}.tpl"))
		        		response(500, __("lang_response_invalid"));

		    		$vars = [
						"handler" => [
							"table" => $tpl
						]
					];
		    endswitch;

	        $this->smarty->assign($vars);

	        $vars["handler"]["loader"] = isset($vars["handler"]["loader"]) ? $vars["handler"]["loader"] : false;

	    	response(200, false, [
	    		"vars" => $vars["handler"],
	    		"tpl" => $this->smarty->fetch(template . "/widgets/tabs/{$tpl}.tpl")
	    	]);
		endif;

		response(500, __("lang_response_invalid"));
	}

	public function chart()
	{
		$this->header->allow(site_url);

		if(!$this->session->has("logged"))
            response(401);

        set_template("dashboard");

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

        switch($type):
        	case "dashboard.messages":
        		$vars = [
        			"chart" => $type
        		];

        		break;
        	case "dashboard.events":
        		$vars = [
        			"chart" => $type
        		];

        		break;
        	case "dashboard.utilities":
        		$vars = [
        			"chart" => $type
        		];

        		break;
    		case "admin.countries":
        		if(!is_admin)
					response(500, __("lang_response_invalid"));
					
        		$vars = [
        			"chart" => $type
        		];

        		break;
        	case "admin.browsers":
        		if(!is_admin)
					response(500, __("lang_response_invalid"));
					
        		$vars = [
        			"chart" => $type
        		];

        		break;
        	case "admin.os":
        		if(!is_admin)
					response(500, __("lang_response_invalid"));
					
        		$vars = [
        			"chart" => $type
        		];

        		break;
        	case "admin.messages":
        		if(!is_admin)
					response(500, __("lang_response_invalid"));
					
        		$vars = [
        			"chart" => $type
        		];

        		break;
        	case "admin.utilities":
        		if(!is_admin)
					response(500, __("lang_response_invalid"));
					
        		$vars = [
        			"chart" => $type
        		];

        		break;
        	case "admin.subscriptions":
        		if(!permission("manage_transactions"))
					response(500, __("lang_response_no_permission"));
					
        		$vars = [
        			"chart" => $type
        		];

        		break;
        	case "admin.credits":
        		if(!permission("manage_transactions"))
					response(500, __("lang_response_no_permission"));
					
        		$vars = [
        			"chart" => $type
        		];

        		break;

        	case "admin.commissions":
        		if(!permission("manage_transactions"))
					response(500, __("lang_response_no_permission"));
					
        		$vars = [
        			"chart" => $type
        		];

        		break;
        	default:
        		response(500, __("lang_response_invalid"));
        endswitch;

        $this->smarty->assign($vars);
		$this->smarty->display(template . "/widgets/charts/default.tpl");
	}
}