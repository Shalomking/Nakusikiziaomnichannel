<?php

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

class Api_Controller extends MVC_Controller
{
	public function index()
	{
        $this->header->allow();

        if(!$this->session->has("logged"))
            response(401);

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        try {
            $phoneSample = $this->phone->getExampleNumber(logged_country, Brick\PhoneNumber\PhoneNumberType::MOBILE);
        } catch(Exception $e){
            $phoneSample = "+63123456789";
        }

        $vars = [
            "site_url" => site_url(false, true),
            "data" => [
                "number" => $phoneSample,
                 "type" => "default"
            ]
        ];
        
        $this->smarty->display("_apidoc/layout.tpl", $vars);
	}

    public function send()
    {
        $this->header->allow();

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        $request = $this->sanitize->array($_REQUEST);
        $service = $this->sanitize->string($this->url->segment(4));

        if(!isset($request["secret"]))
            response(400, "Invalid Parameters!");

        if($this->api->checkApikey($request["secret"]) < 1)
            response(401, "Invalid API secret supplied!");

        $api = $this->api->getApikey($request["secret"]);

        switch($service):
            case "otp":
                /**
                 * @api {post} /send/otp Send OTP
                 * @apiName Send OTP
                 * @apiDescription Send a one-time-password to specified mobile number. Requires "<strong>otp</strong>" API permission.
                 * @apiGroup OTP
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {String="sms","whatsapp"} type Type of message, it can be SMS or WhatsApp.
                 * @apiParam {String} message OTP message to send, you can use <strong>{{otp}}</strong> shortcode to include the otp anywhere in the message.
                 * @apiParam {String} phone Recipient mobile number, it will accept E.164 formatted numbers<br>
                 * <strong>Example for Philippines</strong><br>
                 * E.164: +639184661533
                 * @apiParam {Number} expire OTP expiration time in seconds. This is optional, default value is 300 seconds or 5 minutes.
                 * @apiParam {Number} [account] This is only for <strong>whatsapp</strong> type. WhatsApp account ID you want to use for sending, you can get account ID's from <strong>/get/wa.accounts</strong> (Your WhatsApp accounts).
                 * @apiParam {String="devices","credits"} [mode] This is only required for <strong>sms</strong> type. This is the mode of sending the message, it can be "devices" which will allow you to use your linked android devices or "credits" which will allow you to use gateways and partner devices. "credits" requires you to have enough credit balance to send messages.
                 * @apiParam {String} [device] This is only for <strong>sms</strong> type. Linked device unique ID, this is required if you will send with "devices" mode. You can get linked device unique ID from <strong>/get/devices</strong> (Your devices).
                 * @apiParam {String|Number} [gateway] This is only for <strong>sms</strong> type. Partner device unique ID or gateway ID, this is required if you will send with "credits" mode. You can get a partner device unique ID and gateway ID from <strong>/get/rates</strong> 
                 * @apiParam {Number=1,2} [sim] This is only for <strong>sms</strong> type. Sim slot number you want to use. For "devices" mode only.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $message = [
                        "secret" => "API_SECRET", // your API secret from (Tools -> API Keys) page
                        "mode" => "sms",
                        "mode" => "devices",
                        "device" => "00000000-0000-0000-d57d-f30cb6a89289",
                        "sim" => 1,
                        "phone" => "+639123456789",
                        "message" => "Your OTP is {{otp}}"
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/api/send/otp");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $message);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    message = {
                        "secret": apiSecret,
                        "type": "sms",
                        "mode": "devices",
                        "device": "00000000-0000-0000-d57d-f30cb6a89289",
                        "sim": 1,
                        "phone": "+639123456789",
                        "message": "Your OTP is {{otp}}"
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/api/send/otp", params = message)
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "OTP has been sent!",
                   "data": {
                        phone: "+639123456789",
                        message: "Your OTP is 345678",
                        otp: 345678
                   }
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 404 = Device doesn't exist<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("otp", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["message"]))
                    response(400, "Invalid Parameters!");

                if(isset($request["expire"])):
                    if(!$this->sanitize->isInt($request["expire"])):
                        response(400, "Invalid Parameters!");
                    endif;
                endif;

                $this->cache->container("api.otp.{$request["secret"]}", true);

                $unique = false;

                while(!$unique):
                    $otp = rand(100000, 999999);

                    if(!$this->cache->has($otp)):
                        $this->cache->set($otp, true, isset($request["expire"]) ? ($request["expire"] < 1 ? 1 : $request["expire"]) : 300);

                        $unique = true;
                    endif;
                endwhile;

                $request["message"] = $this->lex->parse($request["message"], [
                    "otp" => $otp
                ]);

                if($request["type"] == "sms"):
                    if(!isset($request["mode"], $request["phone"]))
                        response(400, "Invalid Parameters!");

                    if(!$this->file->exists("system/storage/temporary/firebase.json")):
                        response(500, "System configuration error!");
                    endif;

                    try {
                        $number = $this->phone->parse($request["phone"], $api["country"]);

                        $number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

                        if(!$number->isValidNumber())
                            response(400, "Invalid phone number!");

                        if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
                            response(400, "Invalid phone number!");

                        $request["phone"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
                        $country = $number->getRegionCode();
                    } catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
                        response(400, "Invalid phone number!");
                    }

                    if(!$this->sanitize->length($request["message"], system_message_min))
                        response(400, "OTP message is too short!");

                    if(system_message_max > 0):
                        if($this->sanitize->length($request["message"], system_message_max, 2))
                            response(400, "OTP message is too long!");
                    endif;

                    if($request["mode"] == "devices"):
                        if(!isset($request["device"]))
                            response(400, "Invalid Parameters!");

                        if(isset($request["sim"]))
                            $request["sim"] = $request["sim"] < 2 ? 1 : 2;

                        $subscription = set_subscription(
                            $this->system->checkSubscription($api["uid"]), 
                            $this->system->getSubscription(false, $api["uid"]), 
                            $this->system->getSubscription(false, false, true)
                        );

                        if(empty($subscription))
                            response(403, "Account is not subscribed to any premium package!");

                        if(!$this->sanitize->isInt($request["sim"]))
                            response(400, "Invalid Parameters!");

                        if(limitation($subscription["send_limit"], $this->system->countQuota($api["uid"], "sent")))
                            response(403, "Maximum allowed number of sent messages has been reached!");

                        if($this->system->checkDevice($api["uid"], $request["device"], "did") < 1)
                            response(404, "Device doesn't exist!");

                        $device = $this->system->getDevice($api["uid"], $request["device"], "did");
                    else:
                        if($request["mode"] != "credits")
                            response(400, "Invalid Parameters!");

                        if(!isset($request["gateway"]))
                            response(400, "Invalid Parameters!");

                        if($this->sanitize->isInt($request["gateway"])):
                            $gateways = $this->system->getGateways();

                            if(!array_key_exists($request["gateway"], $gateways)):
                                response(404, "Specified gateway doesn't exist!");
                            endif;

                            if(!$this->file->exists("system/gateways/" . md5($request["gateway"]) . ".php"))
                                response(404, "Specified gateway doesn't exist!");

                            require "system/gateways/" . md5($request["gateway"]) . ".php";
                        else:
                            $device = $this->system->getDevice(false, $request["gateway"], "global");

                            if($device):
                                if($device["global_device"] > 1):
                                    response(403, "Device is not available!");
                                endif;
                            else:
                                response(404, "Device doesn't exist!");
                            endif;
                        endif;
                    endif;

                    if($request["mode"] == "devices"):
                        $this->system->create("sent", [
                            "cid" => 0,
                            "uid" => $api["uid"],
                            "did" => $request["device"],
                            "gateway" => 0,
                            "sim" => $request["sim"] < 2 ? 1 : 2,
                            "mode" => 1,
                            "phone" => $request["phone"],
                            "message" => bjoernffm\Spintax\Parser::parse(footermark($subscription["footermark"], $request["message"], system_message_mark))->generate(),
                            "status" => 1,
                            "status_code" => false,
                            "priority" => 1,
                            "api" => 1,
                            "create_date" => date("Y-m-d H:i:s", time())
                        ]);
                    else:
                        $credits = $this->system->getCredits($api["uid"]);

                        if($this->sanitize->isInt($request["gateway"])):
                            $pricing = json_decode($gateways[$request["gateway"]]["pricing"], true);

                            if(array_key_exists(strtolower($country), $pricing["countries"])):
                                $price = $pricing["countries"][strtolower($country)];
                            else:
                                $price = $pricing["default"];
                            endif;

                            if($credits < $price)
                                response(403, "Not enough credits to send this message!");

                            $gateway = $gateways[$request["gateway"]];

                            $message = bjoernffm\Spintax\Parser::parse($request["message"])->generate();

                            $send = gatewaySend($request["phone"], $message, $this);

                            if($send):
                                $create = $this->system->create("sent", [
                                    "cid" => 0,
                                    "uid" => $api["uid"],
                                    "did" => false,
                                    "gateway" => $request["gateway"],
                                    "api" => 1,
                                    "sim" => 0,
                                    "mode" => 2,
                                    "priority" => 0,
                                    "phone" => $request["phone"],
                                    "message" => $message,
                                    "status" => $gateway["callback"] < 2 ? 2 : 3,
                                    "status_code" => false
                                ]);

                                if($gateway["callback"] < 2):
                                    $this->cache->container("system.gateways");

                                    $this->cache->set("{$gateway["callback_id"]}.{$send}", $create);
                                endif;
                            else:
                                response(500, "Gateway was unable to send your message!");
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
                                        response(500, "System configuration error!");
                                    endif;
                                } catch(Exception $e){
                                    response(500, "System configuration error!");
                                }
                            endif;

                            $rates = $this->cache->get("exchange");

                            $base_rate = $rates["data"]["USD"] / $rates["data"][strtoupper($currency)];
                            $usd_price = ($base_rate * $device["rate"]) * $rates["data"]["USD"];
                            $final_price = (float) abs($usd_price * $rates["data"][strtoupper(system_currency)]);

                            if($credits < $final_price)
                                response(403, "Not enough credits to send this message!");

                            $slots = explode(",", $device["global_slots"]);

                            $this->system->create("sent", [
                                "cid" => 0,
                                "uid" => $api["uid"],
                                "did" => $request["gateway"],
                                "gateway" => 0,
                                "sim" => count($slots) > 1 ? rand(1, 2) : ($slots[0] < 2 ? 1 : 2),
                                "mode" => 2,
                                "phone" => $request["phone"],
                                "message" => bjoernffm\Spintax\Parser::parse($request["message"])->generate(),
                                "status" => 1,
                                "status_code" => false,
                                "priority" => $device["global_priority"],
                                "api" => 1,
                                "create_date" => date("Y-m-d H:i:s", time())
                            ]);
                        endif;
                    endif;

                    if($request["mode"] == "devices"):
                        $this->fcm->send(md5($api["uid"] . $request["device"]), [
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
                else:
                    if($request["type"] != "whatsapp")
                        response(400, "Invalid Parameters!");

                    if(!isset($request["phone"], $request["account"]))
                        response(400, "Invalid Parameters!");

                    $subscription = set_subscription(
                        $this->system->checkSubscription($api["uid"]), 
                        $this->system->getSubscription(false, $api["uid"]), 
                        $this->system->getSubscription(false, false, true)
                    );

                    if(empty($subscription))
                        response(403, "Account is not subscribed to any premium package!");

                    if($this->system->checkWaAccount($api["uid"], $request["account"], "id") < 1)
                        response(404, "WhatsApp account doesn't exist!");

                    if($this->system->checkQuota($api["uid"]) < 1):
                        $this->system->create("quota", [
                            "uid" => $api["uid"],
                            "sent" => 0,
                            "received" => 0,
                            "wa_sent" => 0,
                            "wa_received" => 0,
                            "ussd" => 0,
                            "notifications" => 0
                        ]);
                    endif;

                    if(limitation($subscription["wa_send_limit"], $this->system->countQuota($api["uid"], "wa_sent")))
                        response(403, "You have reached the maximum number of allowed chats!");

                    try {
                        $number = $this->phone->parse($request["phone"], $api["country"]);

                        $number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

                        if(!$number->isValidNumber())
                            response(400, "Invalid phone number!");

                        if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
                            response(400, "Invalid phone number!");

                        $request["phone"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
                    } catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
                        response(400, "Invalid phone number!");
                    }

                    $account = $this->system->getWaAccount($api["uid"], $request["account"], "id");

                    $filtered = [
                        "cid" => 0,
                        "uid" => $api["uid"],
                        "wid" => $account["wid"],
                        "unique" => $account["unique"],
                        "phone" => $request["phone"],
                        "message" => json_encode([
                            "text" => bjoernffm\Spintax\Parser::parse(footermark($subscription["footermark"], $request["message"], system_message_mark))->generate()
                        ]),
                        "status" => 1,
                        "api" => 1,
                        "create_date" => date("Y-m-d H:i:s", time())
                    ];

                    if($this->system->create("wa_sent", $filtered)):
                        $this->wa->_guzzle = $this->guzzle;

                        $addQueue = $this->wa->send($account["unique"]);

                        if($addQueue):
                            if($addQueue != 200):
                                response(500, "Failed adding chat to WhatsApp queue!");
                            endif;
                        else:
                            response(500, "Unable to connect to WhatsApp servers!");
                        endif;
                    else:
                        response(500, "Something went wrong!");
                    endif;
                endif;

                response(200, "OTP has been sent!", [
                    "phone" => $request["phone"],
                    "message" => $request["message"],
                    "otp" => (int) $otp
                ]);

                break;
            case "sms":
                /**
                 * @api {post} /send/sms Send Single Message
                 * @apiName Send Single Message
                 * @apiDescription Send a single sms message. Requires "<strong>sms_send</strong>" API permission.
                 * @apiGroup SMS
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {String="devices","credits"} mode Mode of sending the message, it can be "devices" which will allow you to use your linked android devices or "credits" which will allow you to use gateways and partner devices. "credits" requires you to have enough credit balance to send messages.
                 * @apiParam {String} phone Recipient mobile number, it will accept E.164 formatted number or locally formatted numbers using the country code from your profile settings.<br>
                 * <strong>Example for Philippines</strong><br>
                 * E.164: +639184661533<br>
                 * Local: 09184661533
                 * @apiParam {String} message Message you want to send, spintax is also supported.
                 * @apiParam {String} [device] Linked device unique ID, this is required if you will send with "devices" mode. You can get linked device unique ID from <strong>/get/devices</strong> (Your devices).
                 * @apiParam {String|Number} [gateway] Partner device unique ID or gateway ID, this is required if you will send with "credits" mode. You can get a partner device unique ID and gateway ID from <strong>/get/rates</strong> 
                 * @apiParam {Number=1,2} sim Sim slot number you want to use. For "devices" mode only.
                 * @apiParam {Number=1,2} [priority=1] If you want to send the messages as priority, 1 for yes and 2 for no. For "devices" mode only.
                 * @apiParam {Number} [shortener=none] Shortener ID, specify the shortener you want to use if you want to shorten the links in your message. You can get the list of available shorteners from <strong>/get/shorteners</strong>
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $message = [
                        "secret" => "API_SECRET", // your API secret from (Tools -> API Keys) page
                        "mode" => "devices",
                        "device" => "00000000-0000-0000-d57d-f30cb6a89289",
                        "sim" => 1,
                        "priority" => 1,
                        "phone" => "+639123456789",
                        "message" => "Hello World!"
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/api/send/sms");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $message);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    message = {
                        "secret": apiSecret,
                        "mode": "devices",
                        "device": "00000000-0000-0000-d57d-f30cb6a89289",
                        "sim": 1,
                        "priority": 1,
                        "phone": "+639123456789",
                        "message": "Hello World!"
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/api/send/sms", params = message)
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Message has been queued for sending!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 404 = Device doesn't exist<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("sms_send", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["mode"], $request["phone"], $request["message"]))
                    response(400, "Invalid Parameters!");

                if(!$this->file->exists("system/storage/temporary/firebase.json")):
                    response(500, "System configuration error!");
                endif;

                try {
                    $number = $this->phone->parse($request["phone"], $api["country"]);

                    $number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

                    if(!$number->isValidNumber())
                        response(400, "Invalid phone number!");

                    if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
                        response(400, "Invalid phone number!");

                    $phone = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
                    $country = $number->getRegionCode();
                } catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
                    response(400, "Invalid phone number!");
                }

                if(!$this->sanitize->length($request["message"], system_message_min))
                    response(400, "Message is too short!");

                if(system_message_max > 0):
                    if($this->sanitize->length($request["message"], system_message_max, 2))
                        response(400, "Message is too long!");
                endif;

                if($request["mode"] == "devices"):
                    if(!isset($request["device"]))
                        response(400, "Invalid Parameters!");

                    if(isset($request["sim"]))
                        $request["sim"] = $request["sim"] < 2 ? 1 : 2;

                    if(!isset($request["priority"]))
                        $request["priority"] = 1;

                    $subscription = set_subscription(
                        $this->system->checkSubscription($api["uid"]), 
                        $this->system->getSubscription(false, $api["uid"]), 
                        $this->system->getSubscription(false, false, true)
                    );

                    if(empty($subscription))
                        response(403, "Account is not subscribed to any premium package!");

                    if(!$this->sanitize->isInt($request["sim"]))
                        response(400, "Invalid Parameters!");

                    if(!$this->sanitize->isInt($request["priority"]))
                        response(400, "Invalid Parameters!");

                    if(limitation($subscription["send_limit"], $this->system->countQuota($api["uid"], "sent")))
                        response(403, "Maximum allowed number of sent messages has been reached!");

                    if($this->system->checkDevice($api["uid"], $request["device"], "did") < 1)
                        response(404, "Device doesn't exist!");

                    $device = $this->system->getDevice($api["uid"], $request["device"], "did");

                    if($device["limit_status"] < 2 && $this->system->checkSmsLimit($api["uid"], $request["device"], $device["limit_interval"], $device["limit_number"])):
                        $intervalType = $device["limit_interval"] < 2 ? "daily" : "monthly";
                        response(403, "The {$intervalType} allowed messages has been reached for this device, please try again later!");
                    endif;
                else:
                    if($request["mode"] != "credits")
                        response(400, "Invalid Parameters!");

                    if(!isset($request["gateway"]))
                        response(400, "Invalid Parameters!");

                    if($this->sanitize->isInt($request["gateway"])):
                        $gateways = $this->system->getGateways();

                        if(!array_key_exists($request["gateway"], $gateways)):
                            response(404, "Specified gateway doesn't exist!");
                        endif;

                        if(!$this->file->exists("system/gateways/" . md5($request["gateway"]) . ".php"))
                            response(404, "Specified gateway doesn't exist!");

                        try {
                            require "system/gateways/" . md5($request["gateway"]) . ".php";
                        } catch(Exception $e){
                            response(500, "We encountered a gateway error!");
                        }
                    else:
                        $device = $this->system->getDevice(false, $request["gateway"], "global");

                        if($device):
                            if($device["uid"] == $api["uid"]):
                                response(403, "You cannot send messages with credits using your own devices!");
                            endif;

                            if($device["limit_status"] < 2 && $this->system->checkSmsLimit($api["uid"], $request["gateway"], $device["limit_interval"], $device["limit_number"])):
                                $intervalType = $device["limit_interval"] < 2 ? "daily" : "monthly";
                                response(403, "The {$intervalType} allowed messages has been reached for this device, please try again later!");
                            endif;

                            if($device["global_device"] > 1):
                                response(403, "Device is not available!");
                            endif;
                        else:
                            response(404, "Device doesn't exist!");
                        endif;
                    endif;
                endif;

                if(isset($request["shortener"])):
                    if(!$this->sanitize->isInt($request["shortener"]))
                        response(400, "Invalid Parameters!");

                    if($request["shortener"] > 0):
                        if(!$this->file->exists("system/shorteners/" . md5($request["shortener"]) . ".php"))
                            response(404, "Specified shortener doesn't exist!");

                        $messageLinks = (new VStelmakh\UrlHighlight\UrlHighlight)->getUrls($request["message"]);

                        if(!empty($messageLinks)):
                            try {
                                require "system/shorteners/" . md5($request["shortener"]) . ".php";
                            } catch(Exception $e){
                                response(500, "We encountered a shortener error!");
                            }

                            foreach($messageLinks as $key => $value):
                                $shortLink = shortenUrl($value, $this);

                                if($shortLink):
                                    $request["message"] = str_replace($value, $shortLink, $request["message"]);
                                endif;
                            endforeach;
                        endif;
                    endif;
                endif;

                if($request["mode"] == "devices"):
                    $this->system->create("sent", [
                        "cid" => 0,
                        "uid" => $api["uid"],
                        "did" => $request["device"],
                        "gateway" => 0,
                        "sim" => $request["sim"] < 2 ? 1 : 2,
                        "mode" => 1,
                        "phone" => $phone,
                        "message" => bjoernffm\Spintax\Parser::parse(footermark($subscription["footermark"], $request["message"], system_message_mark))->generate(),
                        "status" => 1,
                        "status_code" => false,
                        "priority" => $request["priority"] < 2 ? 1 : 2,
                        "api" => 1,
                        "create_date" => date("Y-m-d H:i:s", time())
                    ]);
                else:
                    $credits = $this->system->getCredits($api["uid"]);

                    if($this->sanitize->isInt($request["gateway"])):
                        $pricing = json_decode($gateways[$request["gateway"]]["pricing"], true);

                        if(array_key_exists(strtolower($country), $pricing["countries"])):
                            $price = $pricing["countries"][strtolower($country)];
                        else:
                            $price = $pricing["default"];
                        endif;

                        if($credits < $price)
                            response(403, "Not enough credits to send this message!");

                        $gateway = $gateways[$request["gateway"]];

                        $message = bjoernffm\Spintax\Parser::parse($request["message"])->generate();

                        $send = gatewaySend($request["phone"], $message, $this);

                        if($send):
                            $create = $this->system->create("sent", [
                                "cid" => 0,
                                "uid" => $api["uid"],
                                "did" => false,
                                "gateway" => $request["gateway"],
                                "api" => 1,
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

                                    response(200, "Message has been queued for sending!");
                                else:
                                    $this->process->_sanitize = $this->sanitize;
                                    $this->process->_guzzle = $this->guzzle;
                                    $this->process->_lex = $this->lex;
                
                                    $hooks = $this->process->actionHooks($api["uid"], 1, 1, $phone, $message, $this->device->getActions($api["uid"], 1));

                                    if(!empty($hooks)):
                                        foreach($hooks as $hook):
                                            $this->system->create("events", [
                                                "uid" => $api["uid"],
                                                "type" => 2,
                                                "create_date" => date("Y-m-d H:i:s", time())
                                            ]);
                                        endforeach;
                                    endif;

                                    $this->system->credits($api["uid"], "decrease", $price);

                                    response(200, "Message has been sent!");
                                endif;
                            endif;
                        else:
                            response(500, "Gateway was unable to send your message!");
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
                                    response(500, "System configuration error!");
                                endif;
                            } catch(Exception $e){
                                response(500, "System configuration error!");
                            }
                        endif;

                        $rates = $this->cache->get("exchange");

                        $base_rate = $rates["data"]["USD"] / $rates["data"][strtoupper($currency)];
                        $usd_price = ($base_rate * $device["rate"]) * $rates["data"]["USD"];
                        $final_price = (float) abs($usd_price * $rates["data"][strtoupper(system_currency)]);

                        if($credits < $final_price)
                            response(403, "Not enough credits to send this message!");

                        $slots = explode(",", $device["global_slots"]);

                        $this->system->create("sent", [
                            "cid" => 0,
                            "uid" => $api["uid"],
                            "did" => $request["gateway"],
                            "gateway" => 0,
                            "sim" => count($slots) > 1 ? rand(1, 2) : ($slots[0] < 2 ? 1 : 2),
                            "mode" => 2,
                            "phone" => $phone,
                            "message" => bjoernffm\Spintax\Parser::parse($request["message"])->generate(),
                            "status" => 1,
                            "status_code" => false,
                            "priority" => $device["global_priority"],
                            "api" => 1,
                            "create_date" => date("Y-m-d H:i:s", time())
                        ]);
                    endif;
                endif;

                if($request["mode"] == "devices"):
                    $this->fcm->send(md5($api["uid"] . $request["device"]), [
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

                response(200, "Message has been queued for sending!");

                break;
            case "sms.bulk":
                /**
                 * @api {post} /send/sms.bulk Send Bulk Messages
                 * @apiName Send Bulk Message
                 * @apiDescription Send bulk sms messages. Requires "<strong>sms_send_bulk</strong>" API permission.
                 * @apiGroup SMS
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {String="devices","credits"} mode Mode of sending the message, it can be "devices" which will allow you to use your linked android devices or "credits" which will allow you to use gateways and partner devices. "credits" requires you to have enough credit balance to send messages.
                 * @apiParam {String} campaign Name of the campaign, you will see this in the sms campaign manager.
                 * @apiParam {String} [numbers] List of phone numbers separated by commas. It can be optional if "groups" parameter is not empty. It will accept E.164 formatted number or locally formatted numbers using the country code from your profile settings.<br>
                 * <strong>Example for Philippines</strong><br>
                 * E.164: +639184661533<br>
                 * Local: 09184661533
                 * @apiParam {String} [groups] List of contact group ID's separated by commas. It can be optional if "numbers" parameter is not empty. You can get group ID's from <strong>/get/groups</strong> (Your contact groups).
                 * @apiParam {String} message Message you want to send, spintax and shortcodes are supported.
                 * @apiParam {String} [device] Linked device unique ID, this is required if you will send with "devices" mode. You can get linked device unique ID from <strong>/get/devices</strong> (Your devices).
                 * @apiParam {String|Number} [gateway] Partner device unique ID or gateway ID, this is required if you will send with "credits" mode. You can get a partner device unique ID and gateway ID from <strong>/get/rates</strong> 
                 * @apiParam {Number=1,2} sim Sim slot number you want to use. For "devices" mode only.
                 * @apiParam {Number=1,2} [priority=1] If you want to send the messages as priority, 1 for yes and 2 for no. For "devices" mode only.
                 * @apiParam {Number} [shortener=none] Shortener ID, specify the shortener you want to use if you want to shorten the links in your message. You can get the list of available shorteners from <strong>/get/shorteners</strong>
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $message = [
                        "secret" => "API_SECRET", // your API secret from (Tools -> API Keys) page
                        "mode" => "devices",
                        "campaign" => "bulk test",
                        "numbers" => "+639123456789,+639123456789,+639123456789",
                        "groups" => "1,2,3,4",
                        "device" => "00000000-0000-0000-d57d-f30cb6a89289",
                        "sim" => 1,
                        "priority" => 1,
                        "message" => "Hello World!"
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/api/send/sms.bulk");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $message);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    message = {
                        "secret": apiSecret,
                        "mode": "devices",
                        "campaign": "bulk test",
                        "numbers": "+639123456789,+639123456789,+639123456789",
                        "groups": "1,2,3,4",
                        "device": "00000000-0000-0000-d57d-f30cb6a89289",
                        "sim": 1,
                        "priority": 1,
                        "message": "Hello World!"
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/api/send/sms.bulk", params = message)
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Message has been queued for sending!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 404 = Device doesn't exist<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("sms_send_bulk", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["mode"], $request["campaign"], $request["message"]))
                    response(400, "Invalid Parameters!");

                if(!isset($request["numbers"]) && !isset($request["groups"]))
                    response(400, "Invalid Parameters!");

                if(!$this->file->exists("system/storage/temporary/firebase.json")):
                    response(500, "System configuration error!");
                endif;

                if(!$this->sanitize->length($request["message"], system_message_min))
                    response(400, "Message is too short!");

                if(system_message_max > 0):
                    if($this->sanitize->length($request["message"], system_message_max, 2))
                        response(400, "Message is too long!");
                endif;

                if($request["mode"] == "devices"):
                    if(!isset($request["device"]))
                        response(400, "Invalid Parameters!");

                    if(isset($request["sim"]))
                        $request["sim"] = $request["sim"] < 2 ? 1 : 2;

                    if(!isset($request["priority"]))
                        $request["priority"] = 2;

                    $subscription = set_subscription(
                        $this->system->checkSubscription($api["uid"]), 
                        $this->system->getSubscription(false, $api["uid"]), 
                        $this->system->getSubscription(false, false, true)
                    );

                    if(empty($subscription))
                        response(403, "You are not subscribed to any premium package!");

                    if(!$this->sanitize->isInt($request["sim"]))
                        response(400, "Invalid Parameters!");

                    if(!$this->sanitize->isInt($request["priority"]))
                        response(400, "Invalid Parameters!");

                    if($this->system->checkDevice($api["uid"], $request["device"], "did") < 1)
                        response(404, "Device doesn't exist!");

                    $device = $this->system->getDevice($api["uid"], $request["device"], "did");
                else:
                    if($request["mode"] != "credits")
                        response(400, "Invalid Parameters!");

                    if(!isset($request["gateway"]))
                        response(400, "Invalid Parameters!");

                    if($this->sanitize->isInt($request["gateway"])):
                        $gateways = $this->system->getGateways();

                        if(!array_key_exists($request["gateway"], $gateways)):
                            response(404, "Specified gateway doesn't exist!");
                        endif;

                        if(!$this->file->exists("system/gateways/" . md5($request["gateway"]) . ".php"))
                            response(404, "Specified gateway doesn't exist!");

                        try {
                            require "system/gateways/" . md5($request["gateway"]) . ".php";
                        } catch(Exception $e){
                            response(500, "We encountered a gateway error!");
                        }
                    else:
                        $device = $this->system->getDevice(false, $request["gateway"], "global");

                        if($device):
                            if($device["uid"] == $api["uid"]):
                                response(500, "You cannot send messages with credits using your own devices!");
                            endif;

                            if($device["global_device"] > 1):
                                response(403, "Device is not available!");
                            endif;
                        else:
                            response(404, "Device doesn't exist!");
                        endif;
                    endif;
                endif;

                $contactBook = [];

                if(isset($request["numbers"])):
                    $numbers = explode(",", trim($request["numbers"]));

                    if(is_array($numbers) && !empty($numbers) && !empty($numbers[0])):
                        foreach($numbers as $number):
                            $rejected = false;

                            try {
                                $phone = $this->phone->parse($number, $api["country"]);

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

                            if(!$rejected):
                                $contactBook[] = [
                                    "name" => $phoneNumber,
                                    "phone" => $phoneNumber,
                                    "group" => "Unknown",
                                    "country" => $country
                                ];
                            endif;
                        endforeach;
                    endif;
                endif;

                if(isset($request["groups"])):
                    $groups = explode(",", trim($request["groups"]));

                    if(is_array($groups) && !empty($groups) && !empty($groups[0])):
                        foreach($groups as $group):
                            if($this->system->checkGroup($api["uid"], $group) > 0):
                                $contacts = $this->system->getContactsByGroup($api["uid"], $group);

                                if(!empty($contacts)):
                                    foreach($contacts as $contact):
                                        try {
                                            $phone = $this->phone->parse($contact["phone"], $api["country"]);
                                            $country = $phone->getRegionCode();
                                        } catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
                                            // ignore
                                        }

                                        $contactBook[] = [
                                            "name" => $contact["name"],
                                            "phone" => $contact["phone"],
                                            "group" => $contact["group"],
                                            "country" => $country
                                        ];
                                    endforeach;
                                endif;
                            endif;
                        endforeach;
                    endif;
                endif;

                if(empty($contactBook))
                    response(400, "Invalid Parameters!");

                if(isset($request["shortener"])):
                    if(!$this->sanitize->isInt($request["shortener"]))
                        response(400, "Invalid Parameters!");

                    if($request["shortener"] > 0):
                        if(!$this->file->exists("system/shorteners/" . md5($request["shortener"]) . ".php"))
                            response(404, "Specified shortener doesn't exist!");

                        $messageLinks = (new VStelmakh\UrlHighlight\UrlHighlight)->getUrls($request["message"]);

                        if(!empty($messageLinks)):
                            try {
                                require "system/shorteners/" . md5($request["shortener"]) . ".php";
                            } catch(Exception $e){
                                response(500, "We encountered a shortener error!");
                            }

                            foreach($messageLinks as $key => $value):
                                $shortLink = shortenUrl($value, $this);

                                if($shortLink):
                                    $request["message"] = str_replace($value, $shortLink, $request["message"]);
                                endif;
                            endforeach;
                        endif;
                    endif;
                endif;

                $smsCampaign = $this->system->create("campaigns", [
                    "uid" => $api["uid"],
                    "did" => $request["mode"] == "devices" ? $request["device"] : ($this->sanitize->isInt($request["gateway"]) ? false : $request["gateway"]),
                    "gateway" => $request["mode"] == "credits" && $this->sanitize->isInt($request["gateway"]) ? $request["gateway"] : 0,
                    "mode" => $request["mode"] == "devices" ? 1 : 2,
                    "status" => 1,
                    "name" => $request["campaign"],
                    "contacts" => count($contactBook),
                    "create_date" => date("Y-m-d H:i:s", time())
                ]);

                $sendCounter = 0;

                foreach($contactBook as $contact):
                    if($request["mode"] == "devices"):
                        if(!limitation($subscription["send_limit"], $this->system->countQuota($api["uid"], "sent"))):
                            $rejectLimit = false;

                            if($device["limit_status"] < 2 && $this->system->checkSmsLimit($api["uid"], $request["device"], $device["limit_interval"], $device["limit_number"])):
                                $rejectLimit = true;
                            endif;

                            if(!$rejectLimit):
                                $this->system->create("sent", [
                                    "cid" => $smsCampaign,
                                    "uid" => $api["uid"],
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
                                        "date" => [
                                            "now" => date("F j, Y"),
                                            "time" => date("h:i A") 
                                        ]
                                    ]))->generate(),
                                    "status" => 1,
                                    "status_code" => false,
                                    "priority" => $request["priority"] < 1 ? 1 : 2,
                                    "api" => 1,
                                    "create_date" => date("Y-m-d H:i:s", time())
                                ]);

                                $sendCounter++;
                            endif;
                        endif;
                    else:
                        $credits = $this->system->getCredits($api["uid"]);

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
                                        "uid" => $api["uid"],
                                        "did" => false,
                                        "gateway" => $request["gateway"],
                                        "api" => 1,
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
                        
                                            $hooks = $this->process->actionHooks($api["uid"], 1, 1, $contact["phone"], $message, $this->device->getActions($api["uid"], 1));

                                            if(!empty($hooks)):
                                                foreach($hooks as $hook):
                                                    $this->system->create("events", [
                                                        "uid" => $api["uid"],
                                                        "type" => 2,
                                                        "create_date" => date("Y-m-d H:i:s", time())
                                                    ]);
                                                endforeach;
                                            endif;

                                            $this->system->credits($api["uid"], "decrease", ($price * count($contactBook)));
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
                                        response(500, "System configuration error!");
                                    endif;
                                } catch(Exception $e){
                                    response(500, "System configuration error!");
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

                                if($device["limit_status"] < 2 && $this->system->checkSmsLimit($api["uid"], $request["gateway"], $device["limit_interval"], $device["limit_number"])):
                                    $rejectLimit = true;
                                endif;

                                if(!$rejectLimit):
                                    $this->system->create("sent", [
                                        "cid" => $smsCampaign,
                                        "uid" => $api["uid"],
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
                                            "date" => [
                                                "now" => date("F j, Y"),
                                                "time" => date("h:i A") 
                                            ]
                                        ]))->generate(),
                                        "status" => 1,
                                        "status_code" => false,
                                        "priority" => $request["priority"] < 1 ? 1 : 2,
                                        "api" => 1,
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

                if($request["mode"] == "devices" || !$this->sanitize->isInt($request["gateway"])):
                    if($device["limit_status"] < 2 && $this->system->checkSmsLimit($api["uid"], $request["mode"] == "devices" ? $request["device"] : $request["gateway"], $device["limit_interval"], $device["limit_number"])):
                        if($sendCounter < 1):
                            $this->system->delete($api["uid"], $smsCampaign, "campaigns");
                            $intervalType = $device["limit_interval"] < 2 ? "daily" : "monthly";
                            response(403, "The {$intervalType} allowed messages has been reached for this device, please try again later!");
                        endif;
                    else:
                        if($sendCounter < 1):
                            $this->system->delete($api["uid"], $smsCampaign, "campaigns");
                            response(403, "You have reached the maximum sent messages for your current package!");
                        endif;
                    endif;

                    if($sendCounter < count($contactBook)):
                        $this->system->update($smsCampaign, $api["uid"], "campaigns", [
                            "contacts" => $sendCounter
                        ]);
                    endif;
                endif;

                if($request["mode"] == "devices"):
                    $this->fcm->send(md5($api["uid"] . $request["device"]), [
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

                response(200, "Bulk messages has been queued for sending!");

                break;
            case "whatsapp":
                /**
                 * @api {post} /send/whatsapp Send Single Chat
                 * @apiName Send Single Chat
                 * @apiDescription Send a single chat message. Requires "<strong>wa_send</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * 
                 * @apiParam {Number} account WhatsApp account ID you want to use for sending, you can get account ID's from <strong>/get/wa.accounts</strong> (Your WhatsApp accounts).
                 * 
                 * @apiParam {String} recipient Recipient mobile number or group address, it will accept whatsapp group address or E.164 formatted number and locally formatted numbers using the country code from your profile settings.<br>
                 * <strong>Example for Philippines</strong><br>
                 * E.164: +639184661533<br>
                 * Local: 09184661533
                 * 
                 * @apiParam {String="text","media","document","button","list","template"} type Type of WhatsApp message.
                 * 
                 * @apiParam {String} message Message or caption you want to send, spintax is also supported.
                 * 
                 * @apiParam {File} [media_file] This is for "media" and "button" type message only. The media file you want to attach in the WhatsApp message, it supports jpg, png, gif and mp4 files. Please use POST method if you are using this parameter. "mp4" files are not supported in "button" type messages.
                 * 
                 * @apiParam {String} [media_url] This is for "media" and "button" type message only. The media file url, please use direct link to the media file. It will be downloaded and be attached in the WhatsApp message, it also supports jpg, png, gif and mp4 files. You can use GET method for this, "mp4" files are not supported on button messages.
                 * 
                 * @apiParam {String="image","mp4"} [media_type] This is for "media" type message only. You only need to enter this parameter if you are using "media_url" instead of "media_file". You need to declare the file type of the media in the url you provided.
                 * 
                 * @apiParam {File} [document_file] This is for "document" type message only. The document file you want to attach in the WhatsApp message, it supports pdf, xls, xlsx, doc and docx files. Please use POST method if you are using this parameter.
                 * 
                 * @apiParam {String} [document_url] This is for "document" type message only. The document file url, please use direct link to the document file. It will be downloaded and be attached in the WhatsApp message, it also supports pdf, xls, xlsx, doc, and docx files. You can use GET method for this.
                 * 
                 * @apiParam {String="pdf","xls","xlsx","doc","docx"} [document_type] This is for "document" type message only. You only need to enter this parameter if you are using "document_url" instead of "document_file". You need to declare the file type of the document in the url you provided.
                 * 
                 * @apiParam {String} button_1 This is for "button" type message only. The name for the first button.
                 * 
                 * @apiParam {String} [button_2] This is for "button" type message only. The name for the second button.
                 * 
                 * @apiParam {String} [button_3] This is for "button" type message only. The name for the third button.
                 * 
                 * @apiParam {String} list_title This is for "list" type message only. The title of the list. Please use POST method for "list" and "template" formats.
                 * 
                 * @apiParam {String} menu_title This is for "list" type message only. The title of the menu. Please use POST method for "list" and "template" formats.
                 * 
                 * @apiParam {String} footer This is for "list" and "template" type message only. The footer message. Please use POST method for "list" and "template" formats.
                 * 
                 * @apiParam {String} format This is for "list" and "template" type message only. The list or template format you want to send. This must be a valid JSON string, please check the example JSON in the quick message modal. Please use POST method for "list" and "template" formats.
                 * 
                 * @apiParam {Number} [shortener=none] Shortener ID, specify the shortener you want to use if you want to shorten the links in your message. You can get the list of available shorteners from <strong>/get/shorteners</strong>
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $chat = [
                        "secret" => "API_SECRET", // your API secret from (Tools -> API Keys) page
                        "account" => 1,
                        "recipient" => "+639123456789",
                        "type" => "text",
                        "message" => "Hello World!"
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/api/send/whatsapp");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $chat);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    chat = {
                        "secret": apiSecret,
                        "account": 1,
                        "recipient": "+639123456789",
                        "type": "text",
                        "message": "Hello World!"
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/api/send/whatsapp", params = chat)
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "WhatsApp message has been queued for sending!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 404 = WhatsApp account doesn't exist<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("wa_send", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(empty(system_wa_server) || empty(system_wa_port))
                    response(403, "WhatsApp server is not yet configured!");

                if(!isset($request["recipient"], $request["account"], $request["type"], $request["message"]))
                    response(400, "Invalid Parameters!");

                if(!in_array($request["type"], ["text", "media", "document", "button", "list", "template"]))
                    response(400, "Invalid message type!");

                $subscription = set_subscription(
                    $this->system->checkSubscription($api["uid"]), 
                    $this->system->getSubscription(false, $api["uid"]), 
                    $this->system->getSubscription(false, false, true)
                );

                if(empty($subscription))
                    response(403, "Account is not subscribed to any premium package!");

                if($this->system->checkWaAccount($api["uid"], $request["account"], "id") < 1)
                    response(404, "WhatsApp account doesn't exist!");

                if($this->system->checkQuota($api["uid"]) < 1):
                    $this->system->create("quota", [
                        "uid" => $api["uid"],
                        "sent" => 0,
                        "received" => 0,
                        "wa_sent" => 0,
                        "wa_received" => 0,
                        "ussd" => 0,
                        "notifications" => 0
                    ]);
                endif;

                if(limitation($subscription["wa_send_limit"], $this->system->countQuota($api["uid"], "wa_sent")))
                    response(403, "You have reached the maximum number of allowed chats!");

                if(!find("@g.us", $request["recipient"])):
                    try {
                        $number = $this->phone->parse($request["recipient"], $api["country"]);

                        $number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

                        if(!$number->isValidNumber())
                            response(400, "Invalid phone number!");

                        if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
                            response(400, "Invalid phone number!");

                        $request["recipient"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);

                        if($number->getRegionCode() == "MX"):
                            $request["recipient"] = formatMexicoNumWa($request["recipient"]);
                        endif;
                    } catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
                        response(400, "Invalid phone number!");
                    }
                endif;

                if(!$this->sanitize->length($request["message"], system_message_min))
                    response(400, "Chat message is too short!");

                if(system_message_max > 0):
                    if($this->sanitize->length($request["message"], system_message_max, 2))
                        response(400, "Chat message is too long!");
                endif;

                if(isset($request["shortener"])):
                    if(!$this->sanitize->isInt($request["shortener"]))
                        response(400, "Invalid Parameters!");

                    if($request["shortener"] > 0):
                        if(!$this->file->exists("system/shorteners/" . md5($request["shortener"]) . ".php"))
                            response(404, "Specified shortener doesn't exist!");

                        $messageLinks = (new VStelmakh\UrlHighlight\UrlHighlight)->getUrls($request["message"]);

                        if(!empty($messageLinks)):
                            try {
                                require "system/shorteners/" . md5($request["shortener"]) . ".php";
                            } catch(Exception $e){
                                response(500, "We encountered a shortener error!");
                            }

                            foreach($messageLinks as $key => $value):
                                $shortLink = shortenUrl($value, $this);

                                if($shortLink):
                                    $request["message"] = str_replace($value, $shortLink, $request["message"]);
                                endif;
                            endforeach;
                        endif;
                    endif;
                endif;

                $request["message"] = bjoernffm\Spintax\Parser::parse(footermark($subscription["footermark"], $request["message"], system_message_mark))->generate();

                switch($request["type"]):
                    case "media":
                        if(isset($request["media_url"])):
                            if(!$this->sanitize->isUrl($request["media_url"]))
                                response(400, "You provided an invalid media url!");

                            if(!isset($request["media_type"]))
                                response(400, "Please declare the media type!");

                            if($request["media_type"] == "image"):
                                // image
                                $message = [
                                    "image" => [
                                        "url" => $request["media_url"]
                                    ],
                                    "caption" => $request["message"]
                                ];
                            else:
                                // video
                                $message = [
                                    "video" => [
                                        "url" => $request["media_url"]
                                    ],
                                    "caption" => $request["message"]
                                ];
                            endif;
                        else:
                            try {
                                $this->upload->upload($_FILES["media_file"]);
                                if($this->upload->uploaded):
                                    if(!in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif", "mp4"]))
                                        response(400, "Invalid media file!");

                                    $mediaName = "{$api["hash"]}_" . uniqid($api["hash"], true);

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
                                        response(400, "Invalid media file!");
                                    endif;
                                endif;
                            } catch(Exception $e){
                                response(400, "Invalid media file!");
                            }
                        endif;

                        break;
                    case "document":
                        if(isset($request["document_url"])):
                            if(!$this->sanitize->isUrl($request["document_url"]))
                                response(400, "You provided an invalid document url!");

                            if(!isset($request["document_type"]))
                                response(400, "Please declare the document type!");

                            switch($request["document_type"]):
                                case "pdf":
                                    $docMimetype = "application/pdf";

                                    break;
                                case "xls":
                                    $docMimetype = "application/excel";
                                    
                                    break;
                                case "xlsx":
                                    $docMimetype = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                                    
                                    break;
                                case "doc":
                                    $docMimetype = "application/msword";
                                    
                                    break;
                                case "docx":
                                    $docMimetype = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
                                    
                                    break;
                                default:
                                    response(400, "Invalid Document Type!");
                            endswitch;

                            $message = [
                                "document" => [
                                    "url" => $request["document_url"]
                                ],
                                "mimetype" => $docMimetype,
                                "caption" => $request["message"]
                            ];
                        else:
                            try {
                                $this->upload->upload($_FILES["document_file"]);
                                if($this->upload->uploaded):
                                    switch($this->upload->file_src_name_ext):
                                        case "pdf":
                                            $docMimetype = "application/pdf";

                                            break;
                                        case "xls":
                                            $docMimetype = "application/excel";
                                            
                                            break;
                                        case "xlsx":
                                            $docMimetype = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                                            
                                            break;
                                        case "doc":
                                            $docMimetype = "application/msword";
                                            
                                            break;
                                        case "docx":
                                            $docMimetype = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
                                            
                                            break;
                                        default:
                                            response(400, "Invalid Document File!");
                                    endswitch;

                                    $docName = "{$api["hash"]}_" . uniqid($api["hash"], true);

                                    $this->upload->mime_check = false;
                                    $this->upload->file_new_name_body = $docName;
                                    $this->upload->file_overwrite = true;
                                    $this->upload->process("uploads/whatsapp/");

                                    if($this->upload->processed):
                                        $message = [
                                            "document" => [
                                                "url" => site_url("uploads/whatsapp/{$docName}.{$this->upload->file_src_name_ext}", true)
                                            ],
                                            "mimetype" => $docMimetype,
                                            "caption" => $request["message"]
                                        ];

                                        $this->upload->clean();
                                    else:
                                        response(400, "Invalid Document File!");
                                    endif;
                                endif;
                            } catch(Exception $e){
                                response(400, "Invalid Document File!");
                            }
                        endif;

                        break;
                    case "button":
                        if(!isset($request["button_1"]))
                            response(400, "First button is required!");

                        if(empty($request["button_1"]))
                            response(400, "Please enter a button name!");

                        $mediaUrl = false;

                        if(isset($request["media_url"])):
                            if(!$this->sanitize->isUrl($request["media_url"]))
                                response(400, "You provided an invalid media url!");

                            $mediaUrl = $request["media_url"];
                        else:
                            if(isset($_FILES["media_file"])):
                                try {
                                    $this->upload->upload($_FILES["media_file"]);
                                    if($this->upload->uploaded):
                                        if(!in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif"]))
                                            response(400, "Invalid image file!");

                                        $mediaName = "{$api["hash"]}_" . uniqid($api["hash"], true);

                                        $this->upload->mime_check = false;
                                        $this->upload->file_new_name_body = $mediaName;
                                        $this->upload->file_overwrite = true;
                                        $this->upload->process("uploads/whatsapp/");

                                        if($this->upload->processed):
                                            $mediaUrl = site_url("uploads/whatsapp/{$mediaName}.{$this->upload->file_src_name_ext}", true);

                                            $this->upload->clean();
                                        else:
                                            response(400, "Invalid image file!");
                                        endif;
                                    endif;
                                } catch(Exception $e){
                                    response(400, "Invalid image file!");
                                }
                            endif;
                        endif;

                        $waButtons = [
                            [
                                "buttonId" => "btn1",
                                "buttonText" => [
                                    "displayText" => $request["button_1"]
                                ],
                                "type" => 1
                            ]
                        ];

                        if(isset($request["button_2"]) && !empty($request["button_2"])):
                            $waButtons[] = [
                                "buttonId" => "btn2",
                                "buttonText" => [
                                    "displayText" => $request["button_2"]
                                ],
                                "type" => 1
                            ];
                        endif;

                        if(isset($request["button_2"]) && !empty($request["button_3"])):
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
                        if(!isset($request["list_title"], $request["menu_title"], $request["format"]))
                            response(400, "Invalid list parameters!");

                        if(empty($request["list_title"]))
                            response(400, "List title is required!");

                        if(empty($request["menu_title"]))
                            response(400, "List menu title is required!");

                        if(empty($request["format"]))
                            response(400, "List format is required!");

                        try {
                            $listFormat = json_decode(htmlspecialchars_decode($request["format"]), true, JSON_THROW_ON_ERROR);
                        } catch(Exception $e){
                            response(400, "List format is invalid!");
                        }

                        $message = [
                            "text" => $request["message"],
                            "footer" => isset($request["footer"]) ? $request["footer"] : "",
                            "title" => $request["list_title"],
                            "buttonText" => $request["menu_title"],
                            "sections" => $listFormat
                        ];

                        break;
                    case "template":
                        if(!isset($request["format"]))
                            response(400, "Invalid Parameters!");

                        if(empty($request["format"]))
                            response(400, "Template format is required!");

                        try {
                            $templateFormat = json_decode(htmlspecialchars_decode($request["format"]), true, JSON_THROW_ON_ERROR);
                        } catch(Exception $e){
                            response(400, "Template format is invalid!");
                        }

                        $message = [
                            "text" => $request["message"],
                            "footer" => isset($request["footer"]) ? $request["footer"] : "",
                            "templateButtons" => $templateFormat
                        ];

                        break;
                    default:
                        $message = [
                            "text" => $request["message"]
                        ];
                endswitch;

                $account = $this->system->getWaAccount($api["uid"], $request["account"], "id");

                $filtered = [
                    "cid" => 0,
                    "uid" => $api["uid"],
                    "wid" => $account["wid"],
                    "unique" => $account["unique"],
                    "phone" => $request["recipient"],
                    "message" => json_encode($message),
                    "status" => 1,
                    "api" => 1,
                    "create_date" => date("Y-m-d H:i:s", time())
                ];

                if($this->system->create("wa_sent", $filtered)):
                    $this->wa->_guzzle = $this->guzzle;

                    $addQueue = $this->wa->send($account["unique"]);

                    if($addQueue):
                        if($addQueue == 200):
                            response(200, "WhatsApp chat has been queued for sending!");
                        else:
                            response(500, "Failed adding chat to WhatsApp queue!");
                        endif;
                    else:
                        response(500, "Unable to connect to WhatsApp servers!");
                    endif;
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "whatsapp.bulk":
                /**
                 * @api {post} /send/whatsapp.bulk Send Bulk Chats
                 * @apiName Send Bulk Chats
                 * @apiDescription Send bulk chat messages. Requires "<strong>wa_send_bulk</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * 
                 * @apiParam {Number} account WhatsApp account ID you want to use for sending, you can get account ID's from <strong>/get/wa.accounts</strong> (Your WhatsApp accounts).
                 * 
                 * @apiParam {String} campaign Name of the campaign, you will see this in the WhatsApp campaign manager.
                 * 
                 * @apiParam {String} [recipients] List of phone numbers or group addresses separated by commas. It can be optional if "groups" parameter is not empty. It will accept whatsapp group address E.164 formatted number or locally formatted numbers using the country code from your profile settings.<br>
                 * <strong>Example for Philippines</strong><br>
                 * E.164: +639184661533<br>
                 * Local: 09184661533
                 * 
                 * @apiParam {String} [groups] List of contact group ID's separated by commas. It can be optional if "numbers" parameter is not empty. You can get group ID's from <strong>/get/groups</strong> (Your contact groups).
                 * 
                 * @apiParam {String="text","media","button","list","template"} type Type of WhatsApp message.
                 * 
                 * @apiParam {String} message Message or caption you want to send, spintax and shortcodes are supported.
                 * 
                 * @apiParam {File} [media_file] This is for "media" and "button" type message only. The media file you want to attach in the WhatsApp message, it supports jpg, png, gif and mp4 files. Please use POST method if you are using this parameter. "mp4" files are not supported in "button" type messages.
                 * 
                 * @apiParam {String} [media_url] This is for "media" and "button" type message only. The media file url, please use direct link to the media file. It will be downloaded and be attached in the WhatsApp message, it also supports jpg, png, gif and mp4 files. You can use GET method for this, "mp4" files are not supported on button messages.
                 * 
                 * @apiParam {String="image","mp4"} [media_type] This is for "media" type message only. You only need to enter this parameter if you are using "media_url" instead of "media_file". You need to declare the file type of the media in the url you provided.
                 * 
                 * @apiParam {File} [document_file] This is for "document" type message only. The document file you want to attach in the WhatsApp message, it supports pdf, xls, xlsx, doc and docx files. Please use POST method if you are using this parameter.
                 * 
                 * @apiParam {String} [document_url] This is for "document" type message only. The document file url, please use direct link to the document file. It will be downloaded and be attached in the WhatsApp message, it also supports pdf, xls, xlsx, doc, and docx files. You can use GET method for this.
                 * 
                 * @apiParam {String="pdf","xls","xlsx","doc","docx"} [document_type] This is for "document" type message only. You only need to enter this parameter if you are using "document_url" instead of "document_file". You need to declare the file type of the document in the url you provided.
                 * 
                 * @apiParam {String} button_1 This is for "button" type message only. The name for the first button.
                 * 
                 * @apiParam {String} [button_2] This is for "button" type message only. The name for the second button.
                 * 
                 * @apiParam {String} [button_3] This is for "button" type message only. The name for the third button.
                 * 
                 * @apiParam {String} list_title This is for "list" type message only. The title of the list. Please use POST method for "list" and "template" formats. 
                 * 
                 * @apiParam {String} menu_title This is for "list" type message only. The title of the menu. Please use POST method for "list" and "template" formats. 
                 * 
                 * @apiParam {String} footer This is for "list" and "template" type message only. The footer message. Please use POST method for "list" and "template" formats.
                 * 
                 * @apiParam {String} format This is for "list" and "template" type message only. The list or template format you want to send. This must be a valid JSON string, please check the example JSON in the quick message modal. Please use POST method for "list" and "template" formats.
                 * 
                 * @apiParam {Number} [shortener=none] Shortener ID, specify the shortener you want to use if you want to shorten the links in your message. You can get the list of available shorteners from <strong>/get/shorteners</strong>
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $chat = [
                        "secret" => "API_SECRET", // your API secret from (Tools -> API Keys) page
                        "account" => 1,
                        "campaign" => "bulk test",
                        "numbers" => "+639123456789,+639123456789,+639123456789",
                        "groups" => "1,2,3,4",
                        "phone" => "+639123456789",
                        "message" => "Hello World!"
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/api/send/whatsapp.bulk");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $chat);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    chat = {
                        "secret": apiSecret,
                        "account": 1,
                        "campaign": "bulk test",
                        "numbers": "+639123456789,+639123456789,+639123456789",
                        "groups": "1,2,3,4",
                        "phone": "+639123456789",
                        "message": "Hello World!"
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/api/send/whatsapp.bulk", params = chat)
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "WhatsApp bulk chats has been queued!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 404 = WhatsApp account doesn't exist<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("wa_send_bulk", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(empty(system_wa_server) || empty(system_wa_port))
                    response(403, "WhatsApp server is not yet configured!");

                if(!isset($request["account"], $request["campaign"], $request["type"], $request["message"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->length($request["campaign"]))
                    response(500, "Campaign name is too short!");

                if(!in_array($request["type"], ["text", "media", "document", "button", "list", "template"]))
                    response(400, "Invalid message type!");

                if(!isset($request["recipients"]) && !isset($request["groups"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["account"]))
                    response(400, "Invalid Parameters!");

                if(!$this->file->exists("system/storage/temporary/firebase.json")):
                    response(500, "System configuration error!");
                endif;

                if(!$this->sanitize->length($request["message"], system_message_min))
                    response(400, "Message is too short!");

                if(system_message_max > 0):
                    if($this->sanitize->length($request["message"], system_message_max, 2))
                        response(400, "Message is too long!");
                endif;

                $subscription = set_subscription(
                    $this->system->checkSubscription($api["uid"]), 
                    $this->system->getSubscription(false, $api["uid"]), 
                    $this->system->getSubscription(false, false, true)
                );

                if(empty($subscription))
                    response(403, "You are not subscribed to any premium package!");

                if($this->system->checkWaAccount($api["uid"], $request["account"], "id") < 1)
                    response(404, "WhatsApp account doesn't exist!");

                if($this->system->checkQuota($api["uid"]) < 1):
                    $this->system->create("quota", [
                        "uid" => $api["uid"],
                        "sent" => 0,
                        "received" => 0,
                        "wa_sent" => 0,
                        "wa_received" => 0,
                        "ussd" => 0,
                        "notifications" => 0
                    ]);
                endif;

                $contactBook = [];

                if(isset($request["recipients"])):
                    $numbers = explode(",", trim($request["recipients"]));

                    if(is_array($numbers) && !empty($numbers) && !empty($numbers[0])):
                        foreach($numbers as $number):
                            $rejected = false;

                            if(!find("@g.us", $number)):
                                try {
                                    $phone = $this->phone->parse($number, $api["country"]);

                                    $phone->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

                                    if(!$phone->isValidNumber())
                                        $rejected = true;

                                    if(!$phone->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
                                        $rejected = true;

                                    $phoneNumber = $phone->format(Brick\PhoneNumber\PhoneNumberFormat::E164);

                                    if($phone->getRegionCode() == "MX"):
                                        $phoneNumber = formatMexicoNumWa($phoneNumber);
                                    endif;
                                } catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
                                    $rejected = true;
                                }
                            else:
                                $phoneNumber = $number;
                            endif;

                            if(!$rejected):
                                $contactBook[] = [
                                    "name" => $phoneNumber,
                                    "phone" => $phoneNumber,
                                    "group" => "Unknown"
                                ];
                            endif;
                        endforeach;
                    endif;
                endif;

                if(isset($request["groups"])):
                    $groups = explode(",", trim($request["groups"]));

                    if(is_array($groups) && !empty($groups) && !empty($groups[0])):
                        foreach($request["groups"] as $group):
                            if($this->system->checkGroup($api["uid"], $group) > 0):
                                $contacts = $this->system->getContactsByGroup($api["uid"], $group);
                                
                                if(!empty($contacts)):
                                    foreach($contacts as $contact):
                                        try {
                                            $phone = $this->phone->parse($contact["phone"], $api["country"]);

                                            $phoneNumber = $phone->format(Brick\PhoneNumber\PhoneNumberFormat::E164);

                                            if($phone->getRegionCode() == "MX"):
                                                $phoneNumber = formatMexicoNumWa($phoneNumber);
                                            endif;
                                        } catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
                                            // ignore
                                        }

                                        $contactBook[] = [
                                            "name" => $contact["name"],
                                            "phone" => $phoneNumber,
                                            "group" => $contact["group"]
                                        ];
                                    endforeach;
                                endif;
                            endif;
                        endforeach;
                    endif;
                endif;

                if(empty($contactBook))
                    response(400, "Invalid Parameters!");

                if(isset($request["shortener"])):
                    if(!$this->sanitize->isInt($request["shortener"]))
                        response(400, "Invalid Parameters!");

                    if($request["shortener"] > 0):
                        if(!$this->file->exists("system/shorteners/" . md5($request["shortener"]) . ".php"))
                            response(404, "Specified shortener doesn't exist!");

                        $messageLinks = (new VStelmakh\UrlHighlight\UrlHighlight)->getUrls($request["message"]);

                        if(!empty($messageLinks)):
                            try {
                                require "system/shorteners/" . md5($request["shortener"]) . ".php";
                            } catch(Exception $e){
                                response(500, "We encountered a shortener error!");
                            }

                            foreach($messageLinks as $key => $value):
                                $shortLink = shortenUrl($value, $this);

                                if($shortLink):
                                    $request["message"] = str_replace($value, $shortLink, $request["message"]);
                                endif;
                            endforeach;
                        endif;
                    endif;
                endif;

                switch($request["type"]):
                    case "media":
                        if(isset($request["media_url"])):
                            if(!$this->sanitize->isUrl($request["media_url"]))
                                response(400, "You provided an invalid media url!");

                            if(!isset($request["media_type"]))
                                response(400, "Please declare the media type!");

                            if($request["media_type"] == "image"):
                                // image
                                $message = [
                                    "image" => [
                                        "url" => $request["media_url"]
                                    ],
                                    "caption" => $request["message"]
                                ];
                            else:
                                // video
                                $message = [
                                    "video" => [
                                        "url" => $request["media_url"]
                                    ],
                                    "caption" => $request["message"]
                                ];
                            endif;
                        else:
                            try {
                                $this->upload->upload($_FILES["media_file"]);
                                if($this->upload->uploaded):
                                    if(!in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif", "mp4"]))
                                        response(500, "Invalid media file!");

                                    $mediaName = "{$api["hash"]}_" . uniqid($api["hash"], true);

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
                        endif;

                        break;
                    case "document":
                        if(isset($request["document_url"])):
                            if(!$this->sanitize->isUrl($request["document_url"]))
                                response(400, "You provided an invalid document url!");

                            if(!isset($request["document_type"]))
                                response(400, "Please declare the document type!");

                            switch($request["document_type"]):
                                case "pdf":
                                    $docMimetype = "application/pdf";

                                    break;
                                case "xls":
                                    $docMimetype = "application/excel";
                                    
                                    break;
                                case "xlsx":
                                    $docMimetype = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                                    
                                    break;
                                case "doc":
                                    $docMimetype = "application/msword";
                                    
                                    break;
                                case "docx":
                                    $docMimetype = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
                                    
                                    break;
                                default:
                                    response(400, "Invalid Document Type!");
                            endswitch;

                            $message = [
                                "document" => [
                                    "url" => $request["document_url"]
                                ],
                                "mimetype" => $docMimetype,
                                "caption" => $request["message"]
                            ];
                        else:
                            try {
                                $this->upload->upload($_FILES["document_file"]);
                                if($this->upload->uploaded):
                                    switch($this->upload->file_src_name_ext):
                                        case "pdf":
                                            $docMimetype = "application/pdf";

                                            break;
                                        case "xls":
                                            $docMimetype = "application/excel";
                                            
                                            break;
                                        case "xlsx":
                                            $docMimetype = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                                            
                                            break;
                                        case "doc":
                                            $docMimetype = "application/msword";
                                            
                                            break;
                                        case "docx":
                                            $docMimetype = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
                                            
                                            break;
                                        default:
                                            response(400, "Invalid Document File!");
                                    endswitch;

                                    $docName = "{$api["hash"]}_" . uniqid($api["hash"], true);

                                    $this->upload->mime_check = false;
                                    $this->upload->file_new_name_body = $docName;
                                    $this->upload->file_overwrite = true;
                                    $this->upload->process("uploads/whatsapp/");

                                    if($this->upload->processed):
                                        $message = [
                                            "document" => [
                                                "url" => site_url("uploads/whatsapp/{$docName}.{$this->upload->file_src_name_ext}", true)
                                            ],
                                            "mimetype" => $docMimetype,
                                            "caption" => $request["message"]
                                        ];

                                        $this->upload->clean();
                                    else:
                                        response(400, "Invalid Document File!");
                                    endif;
                                endif;
                            } catch(Exception $e){
                                response(400, "Invalid Document File!");
                            }
                        endif;

                        break;
                    case "button":
                        if(!isset($request["button_1"]))
                            response(400, "First button is required!");

                        if(empty($request["button_1"]))
                            response(500, "Please enter a button name!");

                        $mediaUrl = false;

                        if(isset($request["media_url"])):
                            if(!$this->sanitize->isUrl($request["media_url"]))
                                response(400, "You provided an invalid media url!");

                            $mediaUrl = $request["media_url"];
                        else:
                            if(isset($_FILES["media_file"])):
                                try {
                                    $this->upload->upload($_FILES["media_file"]);
                                    if($this->upload->uploaded):
                                        if(!in_array($this->upload->file_src_name_ext, ["jpg", "jpeg", "png", "gif"]))
                                            response(500, "Invalid image file!");

                                        $mediaName = "{$api["hash"]}_" . uniqid($api["hash"], true);

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
                            endif;
                        endif;

                        $waButtons = [
                            [
                                "buttonId" => "btn1",
                                "buttonText" => [
                                    "displayText" => $request["button_1"]
                                ],
                                "type" => 1
                            ]
                        ];

                        if(isset($request["button_2"]) && !empty($request["button_2"])):
                            $waButtons[] = [
                                "buttonId" => "btn2",
                                "buttonText" => [
                                    "displayText" => $request["button_2"]
                                ],
                                "type" => 1
                            ];
                        endif;

                        if(isset($request["button_2"]) && !empty($request["button_3"])):
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
                        if(!isset($request["list_title"], $request["menu_title"], $request["format"]))
                            response(400, "Invalid list parameters!");

                        if(empty($request["list_title"]))
                            response(400, "List title is required!");

                        if(empty($request["menu_title"]))
                            response(400, "List menu title is required!");

                        if(empty($request["format"]))
                            response(400, "List format is required!");

                        try {
                            $listFormat = json_decode(htmlspecialchars_decode($request["format"]), true, JSON_THROW_ON_ERROR);
                        } catch(Exception $e){
                            response(500, "List format is invalid!");
                        }

                        $message = [
                            "text" => $request["message"],
                            "footer" => isset($request["footer"]) ? $request["footer"] : "",
                            "title" => $request["list_title"],
                            "buttonText" => $request["menu_title"],
                            "sections" => $listFormat
                        ];

                        break;
                    case "template":
                        if(!isset($request["format"]))
                            response(400, "Invalid Parameters!");

                        if(empty($request["format"]))
                            response(500, "Template format is required!");

                        try {
                            $templateFormat = json_decode(htmlspecialchars_decode($request["format"]), true, JSON_THROW_ON_ERROR);
                        } catch(Exception $e){
                            response(500, "Template format is invalid!");
                        }

                        $message = [
                            "text" => $request["message"],
                            "footer" => isset($request["footer"]) ? $request["footer"] : "",
                            "templateButtons" => $templateFormat
                        ];

                        break;
                    default:
                        $message = [
                            "text" => $request["message"]
                        ];
                endswitch;

                $account = $this->system->getWaAccount($api["uid"], $request["account"], "id");

                $waCampaign = $this->system->create("wa_campaigns", [
                    "uid" => $api["uid"],
                    "wid" => $account["wid"],
                    "type" => $request["type"],
                    "status" => 1,
                    "name" => $request["campaign"],
                    "contacts" => count($contactBook),
                    "create_date" => date("Y-m-d H:i:s", time())
                ]);

                $sendCounter = 0;

                $msgText = isset($message["text"]) ? $message["text"] : $message["caption"];

                foreach($contactBook as $contact):
                    if(!limitation($subscription["wa_send_limit"], $this->system->countQuota($api["uid"], "wa_sent"))):
                        if(isset($message["text"]) || isset($message["caption"])):
                            $formatMessage = bjoernffm\Spintax\Parser::parse($this->lex->parse(footermark($subscription["footermark"], $msgText, system_message_mark), [
                                "contact" => [
                                    "name" => $contact["name"],
                                    "number" => $contact["phone"]
                                ],
                                "group" => [
                                    "name" => $contact["group"]
                                ],
                                "unsubscribe" => [
                                    "command" => "STOP",
                                    "link" => site_url("unsubscribe/{$api["uid"]}/{$contact["phone"]}", true)
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
                            "uid" => $api["uid"],
                            "wid" => $account["wid"],
                            "unique" => $account["unique"],
                            "phone" => $contact["phone"],
                            "message" => json_encode($message),
                            "status" => 1,
                            "api" => 1,
                            "create_date" => date("Y-m-d H:i:s", time())
                        ]);

                        $sendCounter++;
                    endif;
                endforeach;

                if($sendCounter > 0):
                    $this->wa->_guzzle = $this->guzzle;

                    $addQueue = $this->wa->send($account["unique"]);

                    if($addQueue):
                        if($addQueue == 200):
                            response(200, "{$sendCounter} chats has been queued for sending!");
                        else:
                            response(500, "Failed adding chats to WhatsApp queue!");
                        endif;
                    else:
                        response(500, "Unable to connect to WhatsApp servers!");
                    endif;
                else:
                    response(500, "No chats were queued. Please check the sending limit of your package!");
                endif;

                break;
            case "ussd":
                /**
                 * @api {post} /send/ussd Send USSD Request
                 * @apiName Send USSD Request
                 * @apiDescription Send USSD Request. Requires "<strong>ussd</strong>" API permission.
                 * @apiGroup USSD
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {String} code MMI request code. Please make sure that you are using a valid MMI code, if not, it will fail.
                 * @apiParam {Number} sim Sim slot number you want to use.
                 * @apiParam {String} device Linked device unique ID. You can get linked device unique ID from <strong>/get/devices</strong> (Your devices).
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $ussd = [
                        "secret" => "API_SECRET", // your API secret from (Tools -> API Keys) page
                        "code" => "*121#",
                        "sim" => 1,
                        "device" => "00000000-0000-0000-d57d-f30cb6a89289"
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/api/send/ussd");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $ussd);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    ussd = {
                        "secret": apiSecret,
                        "code": "*121#",
                        "sim": 1,
                        "device": "00000000-0000-0000-d57d-f30cb6a89289"
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/api/send/ussd", params = ussd)
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "WhatsApp message has been queued for sending!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br> 
                 * 404 = Device doesn't exist<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("ussd", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["code"], $request["sim"], $request["device"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["sim"]))
                    response(400, "Invalid Parameters!");

                $subscription = set_subscription(
                    $this->system->checkSubscription($api["uid"]), 
                    $this->system->getSubscription(false, $api["uid"]), 
                    $this->system->getSubscription(false, false, true)
                );

                if(empty($subscription))
                    response(403, "You are not subscribed to any premium package!");

                if(limitation($subscription["ussd_limit"], $this->system->countQuota($api["uid"], "ussd")))
                    response(403, "Maximum number of USSD requests has been reached!");

                if($this->system->checkDevice($api["uid"], $request["device"], "did") < 1)
                    response(404, "Device doesn't exist!");

                $device = $this->system->getDevice($api["uid"], $request["device"], "did");

                if(!$device)
                    response(404, "Device doesn't exist!");

                $filtered = [
                    "uid" => $api["uid"],
                    "code" => $request["code"],
                    "did" => $request["device"],
                    "sim" => $request["sim"] < 2 ? 1 : 2,
                    "response" => false,
                    "status" => 1,
                    "create_date" => date("Y-m-d H:i:s", time())
                ];

                if($this->system->create("ussd", $filtered)):
                    $this->fcm->send(md5($api["uid"] . $request["device"]), [
                        "type" => "ussd"
                    ]);

                    response(200, "USSD request has been queued!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            default:
                response(500, "Invalid API Endpoint!");
        endswitch;
    }

    public function remote()
    {
        $this->header->allow();

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        $request = $this->sanitize->array($_REQUEST);
        $type = $this->sanitize->string($this->url->segment(4));

        if(!isset($request["secret"]))
            response(400, "Invalid Parameters!");

        if($this->api->checkApikey($request["secret"]) < 1)
            response(401, "Invalid API secret supplied!");

        $api = $this->api->getApikey($request["secret"]);

        $permissions = explode(",", $api["permissions"]);

        if(!is_array($permissions))
            response(403, "Insufficient Permissions!");

        switch($type):
            case "start.sms":
                /**
                 * @api {get} /remote/start.sms Start SMS Campaign
                 * @apiName Start SMS Campaign
                 * @apiDescription Start SMS Campaign. Requires "<strong>start_sms_campaign</strong>" API permission.
                 * @apiGroup SMS
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} campaign Campaign ID
                 * 
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $campaignId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/remote/start.sms?secret={$apiSecret}&campaign={$campaignId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    campaignId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/remote/start.sms", params = {
                        "secret": apiSecret,
                        "campaign": campaignId
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "SMS campaign has been resumed!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br> 
                 * 404 = Device doesn't exist<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("start_sms_campaign", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["campaign"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["campaign"]))
                    response(400, "Invalid Parameters!");

                if($this->api->checkSmsCampaign($request["campaign"], $api["uid"]) < 1)
                    response(400, "Invalid Parameters!");

                $campaign = $this->api->getSmsCampaign($request["campaign"], $api["uid"]);

                if($campaign["mode"] > 1 && $this->sanitize->isInt($campaign["gateway"]))
                    response(200, "Action is not supported in this campaign!");

                if($this->system->update($request["campaign"], $api["uid"], "campaigns", [
                    "status" => 1
                ])):
                    $this->fcm->send(md5($campaign["device_uid"] . $campaign["did"]), [
                        "type" => "start_sms",
                        "cid" => (int) $campaign["id"],
                        "name" => $campaign["name"]
                    ]);
                endif;

                response(200, "SMS campaign has been resumed!");

                break;
            case "stop.sms":
                /**
                 * @api {get} /remote/stop.sms Stop SMS Campaign
                 * @apiName Stop SMS Campaign
                 * @apiDescription Stop SMS Campaign. Requires "<strong>stop_sms_campaign</strong>" API permission.
                 * @apiGroup SMS
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} campaign Campaign ID
                 * 
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $campaignId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/remote/stop.sms?secret={$apiSecret}&campaign={$campaignId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    campaignId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/remote/stop.sms", params = {
                        "secret": apiSecret,
                        "campaign": campaignId
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "SMS campaign has been paused!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br> 
                 * 404 = Device doesn't exist<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("stop_sms_campaign", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["campaign"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["campaign"]))
                    response(400, "Invalid Parameters!");

                if($this->api->checkSmsCampaign($request["campaign"], $api["uid"]) < 1)
                    response(400, "Invalid Parameters!");

                $campaign = $this->api->getSmsCampaign($request["campaign"], $api["uid"]);

                if($campaign["mode"] > 1 && $this->sanitize->isInt($campaign["gateway"]))
                    response(200, "Action is not supported in this campaign!");
                
                if($this->system->update($request["campaign"], $api["uid"], "campaigns", [
                    "status" => 2
                ])):
                    $this->fcm->send(md5($campaign["device_uid"] . $campaign["did"]), [
                        "type" => "stop_sms",
                        "cid" => (int) $campaign["id"],
                        "name" => $campaign["name"]
                    ]);
                endif;

                response(200, "SMS campaign has been paused!");

                break;
            case "start.chats":
                /**
                 * @api {get} /remote/start.chats Start WhatsApp Campaign
                 * @apiName Start WhatsApp Campaign
                 * @apiDescription Start WhatsApp Campaign. Requires "<strong>start_wa_campaign</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} campaign Campaign ID
                 * 
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $campaignId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/remote/start.chats?secret={$apiSecret}&campaign={$campaignId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    campaignId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/remote/start.chats", params = {
                        "secret": apiSecret,
                        "campaign": campaignId
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "WhatsApp campaign has been resumed!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br> 
                 * 404 = Device doesn't exist<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("start_wa_campaign", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["campaign"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["campaign"]))
                    response(400, "Invalid Parameters!");

                if($this->api->checkWaCampaign($request["campaign"], $api["uid"]) < 1)
                    response(400, "Invalid Parameters!");

                $campaign = $this->api->getWaCampaign($request["campaign"], $api["uid"]);

                if($this->system->update($request["campaign"], $api["uid"], "wa_campaigns", [
                    "status" => 1
                ])):
                    try {
                        $this->wa->_guzzle = $this->guzzle;

                        $account = $this->system->getWaAccount($api["uid"], $campaign["wid"], "wid");

                        $this->wa->start_campaign($account["unique"], $api["uid"], $request["campaign"]);
                    } catch(Exception $e){
                        // Ignore
                    }
                endif;

                response(200, "WhatsApp campaign has been resumed!");

                break;
            case "stop.chats":
                /**
                 * @api {get} /remote/stop.chats Stop WhatsApp Campaign
                 * @apiName Stop WhatsApp Campaign
                 * @apiDescription Stop WhatsApp Campaign. Requires "<strong>stop_wa_campaign</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} campaign Campaign ID
                 * 
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $campaignId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/remote/stop.chats?secret={$apiSecret}&campaign={$campaignId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    campaignId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/remote/stop.chats", params = {
                        "secret": apiSecret,
                        "campaign": campaignId
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "WhatsApp campaign has been resumed!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br> 
                 * 404 = Device doesn't exist<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("stop_wa_campaign", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["campaign"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["campaign"]))
                    response(400, "Invalid Parameters!");

                if($this->api->checkWaCampaign($request["campaign"], $api["uid"]) < 1)
                    response(400, "Invalid Parameters!");

                $campaign = $this->api->getWaCampaign($request["campaign"], $api["uid"]);
                
                if($this->system->update($request["campaign"], $api["uid"], "wa_campaigns", [
                    "status" => 2
                ])):
                    try {
                        $this->wa->_guzzle = $this->guzzle;

                        $account = $this->system->getWaAccount($api["uid"], $campaign["wid"], "wid");

                        $this->wa->stop_campaign($account["unique"], $api["uid"], $request["campaign"]);
                    } catch(Exception $e){
                        // Ignore
                    }
                endif;

                response(200, "WhatsApp campaign has been paused!");

                break;
            default:
                response(500, "Invalid API Endpoint!");
        endswitch;
    }

    public function clear()
    {
        $this->header->allow();

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        $request = $this->sanitize->array($_REQUEST);
        $type = $this->sanitize->string($this->url->segment(4));

        if(!isset($request["secret"]))
            response(400, "Invalid Parameters!");

        if($this->api->checkApikey($request["secret"]) < 1)
            response(401, "Invalid API secret supplied!");

        $api = $this->api->getApikey($request["secret"]);

        $permissions = explode(",", $api["permissions"]);

        if(!is_array($permissions))
            response(403, "Insufficient Permissions!");

        switch($type):
            case "ussd":
                /**
                 * @api {get} /clear/ussd Clear Pending USSD
                 * @apiName Clear Pending USSD
                 * @apiDescription Clear Pending USSD. Requires "<strong>clear_ussd_pending</strong>" API permission.
                 * @apiGroup USSD
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 *
                 * @apiExample {php} PHP Example 
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/clear/ussd?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/clear/ussd", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Pending USSD requests has been cleared!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br> 
                 * 404 = Device doesn't exist<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("clear_ussd_pending", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if($this->system->clearUssd($api["uid"])):
                    $this->fcm->send($this->hash->encode($api["uid"], system_token), [
                        "type" => "clear_ussd",
                        "uid" => (int) $api["uid"]
                    ]);
                endif;

                response(200, "Pending USSD requests has been cleared!");

                break;
            default:
                response(500, "Invalid API Endpoint!");
        endswitch;
    }

	public function get()
	{
		$this->header->allow();

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        $request = $this->sanitize->array($_REQUEST);
        $type = $this->sanitize->string($this->url->segment(4));

        if(!in_array($type, ["wa.qr"])):
            if(!isset($request["secret"])):
                response(400, "Invalid Parameters!");
            endif;
        else:
            if(!isset($request["token"])):
                response(400, "Invalid Parameters!");
            endif;

            $this->cache->container("system.whatsapp", true);

            if(!$this->cache->has($request["token"])):
                response(401, "Invalid token supplied!");
            endif;

            $request["secret"] = $this->cache->get($request["token"]);
        endif;

        if($this->api->checkApikey($request["secret"]) < 1)
            response(401, "Invalid API secret supplied!");

        $api = $this->api->getApikey($request["secret"]);

        $permissions = explode(",", $api["permissions"]);

        if(!is_array($permissions))
            response(403, "Insufficient Permissions!");

        try {
            $this->echo->_cache = $this->cache;
            $this->echo->_guzzle = $this->guzzle;

            $echoToken = $this->echo->token();
        } catch(Exception $e){
            response(500, "System configuration error!");
        }

        switch($type):
            case "otp":
                /**
                 * @api {get} /get/otp Verify OTP
                 * @apiName Verify OTP
                 * @apiDescription Verify one-time-password from user supplied data. Requires "<strong>otp</strong>" API permission.
                 * @apiGroup OTP
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {String} otp The otp you got from a user supplied input or data
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $otp = "123456"; // otp from a user supplied input or data

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/otp?secret={$apiSecret}&otp={$otp}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    # otp from a user supplied input or data
                    otpCode = "123456"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/otp", params = {
                        "secret": apiSecret,
                        "otp": otpCode
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "OTP has been verified!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 403,
                   "message": "OTP is invalid or expired!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("otp", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["otp"]))
                    response(400, "Invalid Parameters!");

                $this->cache->container("api.otp.{$request["secret"]}", true);

                if($this->cache->has($request["otp"])):
                    $this->cache->delete($request["otp"]);
                    response(200, "OTP has been verified!");
                endif;

                response(403, "OTP is invalid or expired!");

                break;
            case "credits":
                /**
                 * @api {get} /get/credits Get Remaining Credits
                 * @apiName Get Remaining Credits
                 * @apiDescription Get Remaining Credits. Requires "<strong>get_credits</strong>" API permission.
                 * @apiGroup Account
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/credits?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/credits", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Remaining Credits",
                   "data": {
                        "credits": "798.634",
                        "currency": "GBP"
                    }
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_credits", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                response(200, "Remaining Credits", [
                    "credits" => $api["credits"],
                    "currency" => system_currency
                ]);

                break;
            case "earnings":
                /**
                 * @api {get} /get/earnings Get Partner Earnings
                 * @apiName Get Partner Earnings
                 * @apiDescription Get Partner Earnings. Requires "<strong>get_earnings</strong>" API permission.
                 * @apiGroup Account
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/earnings?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/earnings", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Partner Credits",
                   "data": {
                        "earnings": "1.43638",
                        "currency": "GBP"
                    }
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_earnings", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                response(200, "Partner Earnings", [
                    "earnings" => $api["earnings"],
                    "currency" => system_currency
                ]);

                break;
            case "subscription":
                /**
                 * @api {get} /get/subscription Get Subscription Package
                 * @apiName Get Subscription Package
                 * @apiDescription Get Subscription Package. Requires "<strong>get_subscription</strong>" API permission.
                 * @apiGroup Account
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/subscription?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/subscription", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Subscription Package",
                   "data": "data": {
                    "name": "Starter",
                    "usage": {
                            "sms_send": {
                                "used": 262,
                                "limit": 1000
                            },
                            "sms_receive": {
                                "used": 139,
                                "limit": 250
                            },
                            "ussd": {
                                "used": 16,
                                "limit": 0
                            },
                            "notifications": {
                                "used": 55,
                                "limit": 0
                            },
                            "contacts": {
                                "used": 7,
                                "limit": 50
                            },
                            "devices": {
                                "used": 3,
                                "limit": 3
                            },
                            "apikeys": {
                                "used": 4,
                                "limit": 5
                            },
                            "webhooks": {
                                "used": 1,
                                "limit": 5
                            },
                            "actions": {
                                "used": 3,
                                "limit": 0
                            },
                            "scheduled": {
                                "used": 0,
                                "limit": 0
                            },
                            "wa_send": {
                                "used": 3,
                                "limit": 0
                            },
                            "wa_receive": {
                                "used": 19,
                                "limit": 0
                            },
                            "wa_accounts": {
                                "used": 1,
                                "limit": 0
                            }
                        }
                    }
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_subscription", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                $subscription = set_subscription(
                    $this->system->checkSubscription($api["uid"]), 
                    $this->system->getSubscription(false, $api["uid"]), 
                    $this->system->getSubscription(false, false, true)
                );

                if(empty($subscription))
                    response(403, "Account is not subscribed to any premium package!");

                $package = [
                    "name" => $subscription["name"],
                    "usage" => [
                        "sms_send" => [
                            "used" => (int) $this->system->countQuota($api["uid"], "sent"),
                            "limit" => (int) $subscription["send_limit"]
                        ],
                        "sms_receive" => [
                            "used" => (int) $this->system->countQuota($api["uid"], "received"),
                            "limit" => (int) $subscription["receive_limit"]
                        ],
                        "ussd" => [
                            "used" => (int) $this->system->countQuota($api["uid"], "ussd"),
                            "limit" => (int) $subscription["ussd_limit"]
                        ],
                        "notifications" => [
                            "used" => (int) $this->system->countQuota($api["uid"], "notifications"),
                            "limit" => (int) $subscription["notification_limit"]
                        ],
                        "contacts" => [
                            "used" => (int) $this->system->countContacts($api["uid"]),
                            "limit" => (int) $subscription["contact_limit"]
                        ],
                        "devices" => [
                            "used" => (int) $this->system->countDevices($api["uid"]),
                            "limit" => (int) $subscription["device_limit"]
                        ],
                        "apikeys" => [
                            "used" => (int) $this->system->countKeys($api["uid"]),
                            "limit" => (int) $subscription["key_limit"]
                        ],
                        "webhooks" => [
                            "used" => (int) $this->system->countWebhooks($api["uid"]),
                            "limit" => (int) $subscription["webhook_limit"]
                        ],
                        "actions" => [
                            "used" => (int) $this->system->countActions($api["uid"]),
                            "limit" => (int) $subscription["action_limit"]
                        ],
                        "scheduled" => [
                            "used" => (int) $this->system->countScheduled($api["uid"]),
                            "limit" => (int) $subscription["scheduled_limit"]
                        ],
                        "wa_send" => [
                            "used" => (int) $this->system->countQuota($api["uid"], "wa_sent"),
                            "limit" => (int) $subscription["wa_send_limit"]
                        ],
                        "wa_receive" => [
                            "used" => (int) $this->system->countQuota($api["uid"], "wa_received"),
                            "limit" => (int) $subscription["wa_receive_limit"]
                        ],
                        "wa_accounts" => [
                            "used" => (int) $this->system->countWaAccounts($api["uid"]),
                            "limit" => (int) $subscription["wa_account_limit"]
                        ]
                    ]
                ];

                response(200, "Subscription Package", $package);

                break;
            case "sms.sent":
                /**
                 * @api {get} /get/sms.sent Get Sent Messages
                 * @apiName Get Sent Messages
                 * @apiDescription Get Sent Messages. Requires "<strong>get_sms_sent</strong>" API permission.
                 * @apiGroup SMS
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/sms.sent?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/sms.sent", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Sent SMS Messages",
                   "data": [
                        {
                            "id": 1,
                            "mode": "Devices",
                            "sender": "00000000-0000-0000-d57d-f30cb6a89289",
                            "sender_type": "device",
                            "sim": 2,
                            "priority": false,
                            "api": false,
                            "status": "sent",
                            "status_code": "SMS_SENT",
                            "recipient": "+639206150513",
                            "message": "Hello World!",
                            "created": 1644382599
                        },
                        {
                            "id": 2,
                            "mode": "Devices",
                            "sender": "00000000-0000-0000-d57d-f30cb6a89289",
                            "sender_type": "device",
                            "sim": 2,
                            "priority": false,
                            "api": false,
                            "status": "sent",
                            "status_code": "SMS_SENT",
                            "recipient": "+639184661533",
                            "message": "Hello World!",
                            "created": 1644382597
                        },
                        {
                            "id": 3,
                            "mode": "Credits",
                            "sender": "Twilio",
                            "sender_type": "gateway",
                            "sim": 0,
                            "priority": false,
                            "api": false,
                            "status": "sent",
                            "status_code": "",
                            "recipient": "+639206150513",
                            "message": "Hello World!",
                            "created": 1644382807
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_sms_sent", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $messages = $this->api->getSms($api["uid"], "sent", "sent", abs($page), abs($limit));

                $messageArray = [];

                if(!empty($messages)):
                    foreach($messages as $message):
                        $messageArray[] = [
                            "id" => (int) $message["id"],
                            "mode" => $message["mode"] < 2 ? "devices" : "credits",
                            "sender" => $message["mode"] < 2 ? $message["did"] : ($message["gateway"] > 0 ? $message["gateway_name"] : $message["did"]),
                            "sender_type" => $message["mode"] < 2 ? "device" : ($message["gateway"] > 0 ? "gateway" : "partner_device"),
                            "sim" => (int) ($message["mode"] < 2 ? $message["sim"] : ($message["gateway"] > 0 ? 0 : $message["sim"])),
                            "priority" => $message["mode"] < 2 ? ($message["priority"] < 2 ? false : true) : ($message["gateway"] > 0 ? false : ($message["priority"] < 2 ? false : true)),
                            "api" => $message["api"] < 2 ? true : false,
                            "status" => $message["status"] < 4 ? "sent" : "failed",
                            "status_code" => $message["status_code"],
                            "recipient" => $message["phone"],
                            "message" => $message["message"],
                            "created" => strtotime($message["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "Sent SMS Messages", $messageArray);

                break;
            case "sms.campaigns":
                /**
                 * @api {get} /get/sms.campaigns Get SMS Campaigns
                 * @apiName Get SMS Campaigns
                 * @apiDescription Get SMS Campaigns. Requires "<strong>get_sms_campaigns</strong>" API permission.
                 * @apiGroup SMS
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/sms.campaigns?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/sms.campaigns", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "SMS Campaigns",
                   "data": [
                        {
                            "id": 1,
                            "sender": "f1939aec-0d08-e221-3582-400511111108",
                            "sender_type": "device",
                            "status": "completed",
                            "name": "Campaign Test",
                            "contacts": 32,
                            "created": 1662763259
                        },
                        {
                            "id": 6,
                            "sender": "f1939aec-0d08-e221-3582-400511111108",
                            "sender_type": "device",
                            "status": "running",
                            "name": "World Test",
                            "contacts": 2,
                            "created": 1662799690
                        },
                        {
                            "id": 14,
                            "sender": "da6fcb98-3c6c-4554-3582-400511111108",
                            "sender_type": "device",
                            "status": "paused",
                            "name": "Marketing",
                            "contacts": 8,
                            "created": 1662828578
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_sms_campaigns", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $campaigns = $this->api->getSmsCampaigns($api["uid"], abs($page), abs($limit));

                $campaignArray = [];

                if(!empty($campaigns)):
                    foreach($campaigns as $campaign):
                        $pendingSms = $this->api->checkSmsCampaignPending($api["uid"], $campaign["id"]);

                        $campaignArray[] = [
                            "id" => (int) $campaign["id"],
                            "sender" => $campaign["mode"] < 2 ? $campaign["did"] : ($campaign["gateway"] > 0 ? $campaign["gateway"] : $campaign["did"]),
                            "sender_type" => $campaign["mode"] < 2 ? "device" : ($campaign["gateway"] > 0 ? "gateway" : "partner_device"),
                            "status" => $campaign["gateway"] > 0 ? "none" : ($pendingSms < 1 ? "completed" : ($campaign["status"] < 2 ? "running" : "paused")),
                            "name" => $campaign["name"],
                            "contacts" => (int) $campaign["contacts"],
                            "created" => strtotime($campaign["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "SMS Campaigns", $campaignArray);

                break;
            case "sms.pending":
                /**
                 * @api {get} /get/sms.pending Get Pending Messages
                 * @apiName Get Pending Messages
                 * @apiDescription Get Pending Messages. Requires "<strong>get_sms_pending</strong>" API permission.
                 * @apiGroup SMS
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/sms.pending?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/sms.pending", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Pending SMS Messages",
                   "data": [
                        {
                            "id": 1,
                            "mode": "Devices",
                            "sender": "00000000-0000-0000-d57d-f30cb6a89289",
                            "sender_type": "device",
                            "sim": 2,
                            "priority": false,
                            "api": false,
                            "recipient": "+639184661533",
                            "message": "Hello World!",
                            "created": 1645520349
                        },
                        {
                            "id": 2,
                            "mode": "Devices",
                            "sender": "00000000-0000-0000-d57d-f30cb6a89289",
                            "sender_type": "device",
                            "sim": 2,
                            "priority": false,
                            "api": false,
                            "recipient": "+639206150513",
                            "message": "Hello World!",
                            "created": 1645520349
                        },
                        {
                            "id": 3,
                            "mode": "Credits",
                            "sender": "00000000-0000-0000-d57d-f30cb6a89289",
                            "sender_type": "partner_device",
                            "sim": 2,
                            "priority": false,
                            "api": false,
                            "recipient": "+639184661532",
                            "message": "Hello World!",
                            "created": 1645520349
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_sms_pending", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $messages = $this->api->getSms($api["uid"], "sent", "pending", abs($page), abs($limit));

                $messageArray = [];

                if(!empty($messages)):
                    foreach($messages as $message):
                        $messageArray[] = [
                            "id" => (int) $message["id"],
                            "mode" => $message["mode"] < 2 ? "devices" : "credits",
                            "sender" => $message["mode"] < 2 ? $message["did"] : ($message["gateway"] > 0 ? $message["gateway_name"] : $message["did"]),
                            "sender_type" => $message["mode"] < 2 ? "device" : ($message["gateway"] > 0 ? "gateway" : "partner_device"),
                            "sim" => (int) ($message["mode"] < 2 ? $message["sim"] : ($message["gateway"] > 0 ? 0 : $message["sim"])),
                            "priority" => $message["mode"] < 2 ? ($message["priority"] < 2 ? false : true) : ($message["gateway"] > 0 ? false : ($message["priority"] < 2 ? false : true)),
                            "api" => $message["api"] < 2 ? true : false,
                            "recipient" => $message["phone"],
                            "message" => $message["message"],
                            "created" => strtotime($message["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "Pending SMS Messages", $messageArray);

                break;
            case "sms.received":
                /**
                 * @api {get} /get/sms.received Get Received Messages
                 * @apiName Get Received Messages
                 * @apiDescription Get Received Messages. Requires "<strong>get_sms_received</strong>" API permission.
                 * @apiGroup SMS
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/sms.received?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/sms.received", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Received SMS Messages",
                   "data": [
                        {
                            "id": 1,
                            "device": "00000000-0000-0000-d57d-f30cb6a89289",
                            "sender": "+639760713666",
                            "message": "Hello World!",
                            "created": 1644405663
                        },
                        {
                            "id": 33,
                            "device": "00000000-0000-0000-d57d-f30cb6a89289",
                            "sender": "GCash",
                            "message": "Hello World!",
                            "created": 1644417283
                        },
                        {
                            "id": 22,
                            "device": "00000000-0000-0000-d57d-f30cb6a89289",
                            "sender": "TWILIO",
                            "message": "Hello World!",
                            "created": 1644421353
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_sms_received", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $messages = $this->api->getSms($api["uid"], "received", false, abs($page), abs($limit));

                $messageArray = [];

                if(!empty($messages)):
                    foreach($messages as $message):
                        $messageArray[] = [
                            "id" => (int) $message["id"],
                            "device" => $message["did"],
                            "sender" => $message["phone"],
                            "message" => $message["message"],
                            "created" => strtotime($message["receive_date"])
                        ];
                    endforeach;
                endif;

                response(200, "Received SMS Messages", $messageArray);

                break;
            case "wa.qr":
                /**
                 * @api {get} /get/wa.qr Get WhatsApp QR Image
                 * @apiName Get WhatsApp QR Image
                 * @apiDescription Get WhatsApp QR Image. Requires "<strong>create_whatsapp</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token The token string you got from
                 * @apiParam {Number} qrstring The qr code string you got from <strong>/create/whatsapp</strong>
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("create_whatsapp", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["qrstring"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->length($request["qrstring"], 200))
                    response(400, "QR string is too short to be valid!");

                try {
                    $writer = new PngWriter();

                    $generate = QrCode::create($request["qrstring"])
                        ->setEncoding(new Encoding("UTF-8"))
                        ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                        ->setSize(500)
                        ->setMargin(0)
                        ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
                        ->setForegroundColor(new Color(0, 0, 0))
                        ->setBackgroundColor(new Color(255, 255, 255));

                    $qrcode = $writer->write($generate);

                    header("Content-Type: {$qrcode->getMimeType()}");

                    echo $qrcode->getString();

                    die;
                } catch(Exception $e){
                    response(500, "Something went wrong while generating the image!");
                }

                break;
            case "wa.sent":
                /**
                 * @api {get} /get/wa.sent Get Sent Chats
                 * @apiName Get Sent Chats
                 * @apiDescription Get Sent Chats. Requires "<strong>get_wa_sent</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/wa.sent?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/wa.sent", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Sent WhatsApp Chats",
                   "data": [
                        {
                            "id": 1,
                            "account": "+639760713666",
                            "status": "sent",
                            "api": true,
                            "recipient": "+639206150513",
                            "message": "Hello World!",
                            "attachment": false,
                            "created": 1645129261
                        },
                        {
                            "id": 2,
                            "account": "+639760713666",
                            "status": "sent",
                            "api": false,
                            "recipient": "+639206150513",
                            "message": "Hello World!",
                            "attachment": false,
                            "created": 1645129261
                        },
                        {
                            "id": 3,
                            "account": "+639760713666",
                            "status": "failed",
                            "api": true,
                            "recipient": "+639206150513",
                            "message": "Hello World!",
                            "attachment": "http://127.0.0.1/zender/uploads/whatsapp/c4ca4238a0b923820dcc509a6f75849b_c4ca4238a0b923820dcc509a6f75849b6352420c0654f1.46673324.pdf",
                            "created": 1645129720
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_wa_sent", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $chats = $this->api->getChats($api["uid"], "sent", "sent", abs($page), abs($limit));

                $chatArray = [];

                if(!empty($chats)):
                    foreach($chats as $chat):
                        $account = explode(":", $chat["wid"]);

                        try {
                            $msgDecode = json_decode($chat["message"], true, JSON_THROW_ON_ERROR);
                            $waMessage = isset($msgDecode["text"]) ? $msgDecode["text"] : $msgDecode["caption"];
                        } catch(Exception $e){
                            $waMessage = $chat["message"];
                        }

                        try {
                            $msgDecode = json_decode($chat["message"], true, JSON_THROW_ON_ERROR);

                            if(isset($msgDecode["image"])):
                                $downloadLink = $msgDecode["image"]["url"];
                            elseif(isset($msgDecode["video"])):
                                $downloadLink = $msgDecode["video"]["url"];
                            elseif(isset($msgDecode["document"])):
                                $downloadLink = $msgDecode["document"]["url"];
                            else:
                                $downloadLink = false;
                            endif;
                        } catch(Exception $e){
                            $downloadLink = false;
                        }

                        $chatArray[] = [
                            "id" => (int) $chat["id"],
                            "account" => "+{$account[0]}",
                            "status" => $chat["status"] < 4 ? "sent" : "failed",
                            "api" => $chat["api"] < 2 ? true : false,
                            "recipient" => $chat["phone"],
                            "message" => $waMessage,
                            "attachment" => $downloadLink,
                            "created" => strtotime($chat["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "Sent WhatsApp Chats", $chatArray);

                break;
            case "wa.campaigns":
                /**
                 * @api {get} /get/wa.campaigns Get WhatsApp Campaigns
                 * @apiName Get WhatsApp Campaigns
                 * @apiDescription Get WhatsApp Campaigns. Requires "<strong>get_wa_campaigns</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/wa.campaigns?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/wa.campaigns", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "WhatsApp Campaigns",
                   "data": [
                        {
                            "id": 1,
                            "account": "+639123456789",
                            "type": "text",
                            "status": "completed",
                            "name": "Sample Campaign",
                            "contacts": 2,
                            "created": 1661446601
                        },
                        {
                            "id": 2,
                            "account": "+639123456789",
                            "type": "button",
                            "status": "running",
                            "name": "Sample Campaign 2",
                            "contacts": 70,
                            "created": 1661447664
                        },
                        {
                            "id": 3,
                            "account": "+639123456789",
                            "type": "media",
                            "status": "paused",
                            "name": "Demo",
                            "contacts": 5,
                            "created": 1663185427
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_wa_campaigns", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $campaigns = $this->api->getWaCampaigns($api["uid"], abs($page), abs($limit));

                $campaignArray = [];

                if(!empty($campaigns)):
                    foreach($campaigns as $campaign):
                        $account = explode(":", $campaign["wid"]);
                        $pendingChats = $this->api->checkWaCampaignPending($api["uid"], $campaign["id"]);

                        $campaignArray[] = [
                            "id" => (int) $campaign["id"],
                            "account" => "+{$account[0]}",
                            "type" => $campaign["type"],
                            "status" => $pendingChats < 1 ? "completed" : ($campaign["status"] < 2 ? "running" : "paused"),
                            "name" => $campaign["name"],
                            "contacts" => (int) $campaign["contacts"],
                            "created" => strtotime($campaign["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "WhatsApp Campaigns", $campaignArray);

                break;
            case "wa.pending":
                /**
                 * @api {get} /get/wa.pending Get Pending Chats
                 * @apiName Get Pending Chats
                 * @apiDescription Get Pending Chats. Requires "<strong>get_wa_pending</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/wa.pending?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/wa.pending", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Pending WhatsApp Chats",
                   "data": [
                        {
                            "id": 1,
                            "account": "+639760713666",
                            "api": false,
                            "recipient": "+639184661533",
                            "message": "Hello World!",
                            "attachment": false,
                            "created": 1645521446
                        },
                        {
                            "id": 2,
                            "account": "+639760713666",
                            "api": true,
                            "recipient": "+639206150513",
                            "message": "Hello World!",
                            "attachment": false,
                            "created": 1645521446
                        },
                        {
                            "id": 3,
                            "account": "+639760713666",
                            "api": false,
                            "recipient": "+639184661532",
                            "message": "Hello World!",
                            "attachment": "http://127.0.0.1/zender/uploads/whatsapp/c4ca4238a0b923820dcc509a6f75849b_c4ca4238a0b923820dcc509a6f75849b6352420c0654f1.46673324.pdf",
                            "created": 1645521446
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_wa_pending", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $chats = $this->api->getChats($api["uid"], "sent", "pending", abs($page), abs($limit));

                $chatArray = [];

                if(!empty($chats)):
                    foreach($chats as $chat):
                        $account = explode(":", $chat["wid"]);

                        try {
                            $msgDecode = json_decode($chat["message"], true, JSON_THROW_ON_ERROR);
                            $waMessage = isset($msgDecode["text"]) ? $msgDecode["text"] : $msgDecode["caption"];
                        } catch(Exception $e){
                            $waMessage = $chat["message"];
                        }

                        try {
                            $msgDecode = json_decode($chat["message"], true, JSON_THROW_ON_ERROR);
                            
                            if(isset($msgDecode["image"])):
                                $downloadLink = $msgDecode["image"]["url"];
                            elseif(isset($msgDecode["video"])):
                                $downloadLink = $msgDecode["video"]["url"];
                            elseif(isset($msgDecode["document"])):
                                $downloadLink = $msgDecode["document"]["url"];
                            else:
                                $downloadLink = false;
                            endif;
                        } catch(Exception $e){
                            $downloadLink = false;
                        }

                        $chatArray[] = [
                            "id" => (int) $chat["id"],
                            "account" => "+{$account[0]}",
                            "api" => $chat["api"] < 2 ? true : false,
                            "recipient" => $chat["phone"],
                            "message" => $waMessage,
                            "attachment" => $downloadLink,
                            "created" => strtotime($chat["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "Pending WhatsApp Chats", $chatArray);

                break;
            case "wa.received":
                /**
                 * @api {get} /get/wa.received Get Received Chats
                 * @apiName Get Received Chats
                 * @apiDescription Get Received Chats. Requires "<strong>get_wa_received</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/wa.received?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/wa.received", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Pending WhatsApp Chats",
                   "data": [
                        {
                            "id": 1,
                            "account": "+639760713666",
                            "recipient": "+639184661533",
                            "message": "Hello world!",
                            "created": 1645232578
                        },
                        {
                            "id": 2,
                            "account": "+639760713666",
                            "recipient": "+639184661533",
                            "message": "How are you?",
                            "created": 1645232635
                        },
                        {
                            "id": 3,
                            "account": "+639760713666",
                            "recipient": "+639184661533",
                            "message": "hahaha",
                            "created": 1645232650
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_wa_received", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $chats = $this->api->getChats($api["uid"], "received", false, abs($page), abs($limit));

                $chatArray = [];

                if(!empty($chats)):
                    foreach($chats as $chat):
                        $account = explode(":", $chat["wid"]);
                        $chatArray[] = [
                            "id" => (int) $chat["id"],
                            "account" => "+{$account[0]}",
                            "recipient" => $chat["phone"],
                            "message" => $chat["message"],
                            "created" => strtotime($chat["receive_date"])
                        ];
                    endforeach;
                endif;

                response(200, "Received WhatsApp Chats", $chatArray);

                break;
            case "contacts":
                /**
                 * @api {get} /get/contacts Get Contacts
                 * @apiName Get Contacts
                 * @apiDescription Get Contacts. Requires "<strong>get_contacts</strong>" API permission.
                 * @apiGroup Contacts
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/contacts?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/contacts", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Saved Contacts",
                   "data": [
                        {
                            "id": 2,
                            "groups": [
                                "1"
                            ],
                            "phone": "+639184661538",
                            "name": "Shane"
                        },
                        {
                            "id": 3,
                            "groups": [
                                "1",
                                "9",
                                "10",
                                "11"
                            ],
                            "phone": "+639206150514",
                            "name": "Terry Bom"
                        },
                        {
                            "id": 4,
                            "groups": [
                                "1",
                                "9"
                            ],
                            "phone": "+639184661532",
                            "name": "Jake Thrower"
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_contacts", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $contacts = $this->api->getContacts($api["uid"], abs($page), abs($limit));

                $contactArray = [];

                if(!empty($contacts)):
                    foreach($contacts as $contact):
                        $groups = explode(",", $contact["groups"]);
                        $contactArray[] = [
                            "id" => (int) $contact["id"],
                            "groups" => is_array($groups) ? $groups : [],
                            "phone" => $contact["phone"],
                            "name" => $contact["name"]
                        ];
                    endforeach;
                endif;

                response(200, "Saved Contacts", $contactArray);

                break;
            case "groups":
                /**
                 * @api {get} /get/groups Get Groups
                 * @apiName Get Groups
                 * @apiDescription Get Groups. Requires "<strong>get_groups</strong>" API permission.
                 * @apiGroup Contacts
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/groups?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/groups", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Contact Groups",
                   "data": [
                        {
                            "id": 1,
                            "name": "Default"
                        },
                        {
                            "id": 9,
                            "name": "Happy Group"
                        },
                        {
                            "id": 10,
                            "name": "Riders"
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_groups", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $groups = $this->api->getGroups($api["uid"], abs($page), abs($limit));

                $groupArray = [];

                if(!empty($groups)):
                    foreach($groups as $group):
                        $groupArray[] = [
                            "id" => (int) $group["id"],
                            "name" => $group["name"]
                        ];
                    endforeach;
                endif;

                response(200, "Contact Groups", $groupArray);

                break;
            case "unsubscribed":
                /**
                 * @api {get} /get/unsubscribed Get Unsubscribed
                 * @apiName Get Unsubscribed
                 * @apiDescription Get Unsubscribed Contacts. Requires "<strong>get_unsubscribed</strong>" API permission.
                 * @apiGroup Contacts
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/unsubscribed?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/unsubscribed", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Unsubscribed Contacts",
                   "data": [
                        {
                            "id": 2,
                            "phone": "+639694967617",
                            "created": 1645755138
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_unsubscribed", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $unsubscribed = $this->api->getUnsubscribed($api["uid"], abs($page), abs($limit));

                $unsubscribeArray = [];

                if(!empty($unsubscribed)):
                    foreach($unsubscribed as $unsubscribe):
                        $unsubscribeArray[] = [
                            "id" => (int) $unsubscribe["id"],
                            "phone" => $unsubscribe["phone"],
                            "created" => strtotime($unsubscribe["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "Unsubscribed Contacts", $unsubscribeArray);

                break;
            case "ussd":
                /**
                 * @api {get} /get/ussd Get USSD Requests
                 * @apiName Get USSD Requests
                 * @apiDescription Get USSD Requests. Requires "<strong>get_ussd</strong>" API permission.
                 * @apiGroup USSD
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/ussd?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/ussd", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "USSD Requests",
                   "data": [
                        {
                            "id": 5,
                            "device": "00000000-0000-0000-d57d-f30cb6a89289",
                            "sim": 1,
                            "code": "*143#",
                            "response": "Sorry! You are not allowed to use this service.",
                            "status": "completed",
                            "created": 1645043019
                        },
                        {
                            "id": 6,
                            "device": "00000000-0000-0000-d57d-f30cb6a89289",
                            "sim": 1,
                            "code": "*145#",
                            "response": "Your balance is 14.60",
                            "status": "completed",
                            "created": 1645043024
                        },
                        {
                            "id": 13,
                            "device": "00000000-0000-0000-d57d-f30cb6a89289",
                            "sim": 2,
                            "code": "*121#",
                            "response": "Sorry! Invalid MMI Code.",
                            "status": "completed",
                            "created": 1645413608
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_ussd", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $ussds = $this->api->getUssd($api["uid"], abs($page), abs($limit));

                $ussdArray = [];

                if(!empty($ussds)):
                    foreach($ussds as $ussd):
                        $ussdArray[] = [
                            "id" => (int) $ussd["id"],
                            "device" => $ussd["did"],
                            "sim" => $ussd["sim"] < 1 ? 1 : 2,
                            "code" => $ussd["code"],
                            "response" => $ussd["response"],
                            "status" => $ussd["status"] < 2 ? "pending" : ($ussd["status"] == 2 ? "queued" : "completed"),
                            "created" => strtotime($ussd["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "USSD Requests", $ussdArray);

                break;
            case "notifications":
                /**
                 * @api {get} /get/notifications Get Notifications
                 * @apiName Get Notifications
                 * @apiDescription Get Notifications. Requires "<strong>get_notifications</strong>" API permission.
                 * @apiGroup Notifications
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/notifications?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/notifications", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Received Notifications",
                   "data": [
                        {
                            "id": 1,
                            "device": "00000000-0000-0000-d57d-f30cb6a89289",
                            "package_name": "com.facebook.orca",
                            "title": "Darren Shmuck",
                            "content": "Hello World!",
                            "timestamp": 1645052535
                        },
                        {
                            "id": 2,
                            "device": "00000000-0000-0000-d57d-f30cb6a89289",
                            "package_name": "com.facebook.katana",
                            "title": "Michael shared your post",
                            "content": "Michael shared your post",
                            "timestamp": 1645052541
                        },
                        {
                            "id": 3,
                            "device": "00000000-0000-0000-d57d-f30cb6a89289",
                            "package_name": "com.facebook.orca",
                            "title": "Shane Blake",
                            "content": "Hello World!",
                            "timestamp": 1645052543
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_notifications", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $notifications = $this->api->getNotifications($api["uid"], abs($page), abs($limit));

                $notificationArray = [];

                if(!empty($notifications)):
                    foreach($notifications as $notification):
                        $notificationArray[] = [
                            "id" => (int) $notification["id"],
                            "device" => $notification["did"],
                            "package_name" => $notification["package"],
                            "title" => $notification["title"],
                            "content" => $notification["text"],
                            "timestamp" => strtotime($notification["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "Received Notifications", $notificationArray);

                break;
            case "wa.accounts":
                /**
                 * @api {get} /get/wa.accounts Get Accounts
                 * @apiName Get Accounts
                 * @apiDescription Get Accounts. Requires "<strong>get_wa_accounts</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/wa.accounts?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/wa.accounts", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "WhatsApp Accounts",
                   "data": [
                        {
                            "id": 1,
                            "phone": "+639760713666",
                            "unique": "90cf7d40a467d5f40a39fca222c216449cb9abee73e5e2b0b321060c2ae06a8fa9e45486",
                            "created": 1645128758
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_wa_accounts", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $accounts = $this->api->getWaAccounts($api["uid"], abs($page), abs($limit));

                $accountArray = [];

                if(!empty($accounts)):
                    foreach($accounts as $account):
                        $phone = explode(":", $account["wid"]);
                        $accountArray[] = [
                            "id" => (int) $account["id"],
                            "phone" => "+{$phone[0]}",
                            "unique" => $account["unique"],
                            "created" => strtotime($account["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "WhatsApp Accounts", $accountArray);

                break;
            case "devices":
                /**
                 * @api {get} /get/devices Get Devices
                 * @apiName Get Devices
                 * @apiDescription Get Devices. Requires "<strong>get_devices</strong>" API permission.
                 * @apiGroup SMS
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/devices?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/devices", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Android Devices",
                   "data": [
                        {
                            "id": "49",
                            "unique": "00000000-0000-0000-d57d-f30cb6a89289",
                            "name": "F11 Phone",
                            "version": "Android 11",
                            "manufacturer": "OPPO",
                            "random_send": false,
                            "random_min": 5,
                            "random_max": 10,
                            "limit_status": true,
                            "limit_interval": "daily",
                            "limit_number": 100,
                            "notification_packages": [
                                "com.google.android.apps.messaging",
                                "com.facebook.orca"
                            ],
                            "partner": false,
                            "partner_sim": [
                                "2"
                            ],
                            "partner_priority": false,
                            "partner_country": "PH",
                            "partner_rate": 5,
                            "partner_currency": "PHP",
                            "created": 1636462504
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_devices", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(isset($request["page"])):
                    if(!$this->sanitize->isInt($request["page"]))
                        response(400, "Invalid Parameters!");

                    $page = $request["page"];
                else:
                    $page = 1;
                endif;

                if(isset($request["limit"])):
                    if(!$this->sanitize->isInt($request["limit"]))
                        response(400, "Invalid Parameters!");

                    $limit = $request["limit"];
                else:
                    $limit = 10;
                endif;

                $devices = $this->api->getDevices($api["uid"], abs($page), abs($limit));

                $deviceArray = [];

                if(!empty($devices)):
                    foreach($devices as $device):
                        switch(round((int) $device["version"])):
                            case 4:
                                $android = "Android KitKat";
                                break;
                            case 5:
                                $android = "Android Lollipop";
                                break;
                            case 6:
                                $android = "Android Marshmallow";
                                break;
                            case 7:
                                $android = "Android Nougat";
                                break;
                            case 8:
                                $android = "Android Oreo";
                                break;
                            case 9:
                                $android = "Android Pie";
                                break;
                            case 10:
                                $android = "Android 10";
                                break;
                            case 11:
                                $android = "Android 11";
                                break;
                            case 12:
                                $android = "Android 12";
                                break;
                            case 13:
                                $android = "Android 13";
                                break;
                            default:
                                $android = "Unknown";
                        endswitch;

                        $slots = explode(",", $device["global_slots"]);
                        $currency = country($device["country"])->getCurrency()["iso_4217_code"];

                        $deviceArray[] = [
                            "id" => (int) $device["id"],
                            "unique" => $device["did"],
                            "online" => $echoToken ? $this->echo->status($device["online_id"]) : false,
                            "name" => $device["name"],
                            "version" => $android,
                            "manufacturer" => $device["manufacturer"],
                            "random_send" => $device["random_send"] < 2 ? true : false,
                            "random_min" => (int) $device["random_min"],
                            "random_max" => (int) $device["random_max"],
                            "limit_status" => $device["limit_status"] < 2 ? true : false,
                            "limit_interval" => $device["limit_interval"] < 2 ? "daily" : "monthly",
                            "limit_number" => (int) $device["limit_number"],
                            "notification_packages" => empty($device["packages"]) ? [] : array_filter(array_map("trim", explode("\n", $device["packages"]))),
                            "partner" => $device["global_device"] < 2 ? true : false,
                            "partner_sim" => is_array($slots) ? $slots : [],
                            "partner_priority" => $device["global_priority"] < 2 ? true : false,
                            "partner_country" => strtoupper($device["country"]),
                            "partner_rate" => (float) $device["rate"],
                            "partner_currency" => strtoupper($currency),
                            "created" => strtotime($device["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "Android Devices", $deviceArray);

                break;
            case "rates":
                /**
                 * @api {get} /get/rates Get Gateway Rates
                 * @apiName Get Gateway Rates
                 * @apiDescription Get Gateway Rates. Requires "<strong>get_rates</strong>" API permission.
                 * @apiGroup System
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/rates?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/rates", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Gateway Rates",
                   "data": {
                        "gateways": [
                            {
                                "id": 1,
                                "name": "Twilio",
                                "currency": "GBP",
                                "pricing": {
                                    "default": "0.01",
                                    "countries": {
                                        "us": "0.01",
                                        "ph": "10",
                                        "gb": "0.02"
                                    }
                                }
                            }
                        ],
                        "partners": [
                            {
                                "unique": "00000000-0000-0000-d57d-f30cb6a89289",
                                "name": "F11 Phone",
                                "version": "Android 11",
                                "priority": false,
                                "sim": [
                                    "2"
                                ],
                                "country": "PH",
                                "currency": "PHP",
                                "rate": 5,
                                "owner": "mail@owneremail.com",
                                "status": "online"
                            }
                        ]
                    }
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_rates", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                $gateways = $this->api->getGateways();

                $gatewayArray = [];

                if(!empty($gateways)):
                    foreach($gateways as $gateway):
                        $gatewayArray[] = [
                            "id" => (int) $gateway["id"],
                            "name" => $gateway["name"],
                            "currency" => strtoupper(system_currency),
                            "pricing" => json_decode($gateway["pricing"], true)
                        ];
                    endforeach;
                endif;

                $partners = $this->api->getPartners($api["uid"]);

                $partnerArray = [];

                if(!empty($partners)):
                    foreach($partners as $partner):
                        switch(round((int) $partner["version"])):
                            case 4:
                                $android = "Android KitKat";
                                break;
                            case 5:
                                $android = "Android Lollipop";
                                break;
                            case 6:
                                $android = "Android Marshmallow";
                                break;
                            case 7:
                                $android = "Android Nougat";
                                break;
                            case 8:
                                $android = "Android Oreo";
                                break;
                            case 9:
                                $android = "Android Pie";
                                break;
                            case 10:
                                $android = "Android 10";
                                break;
                            case 11:
                                $android = "Android 11";
                                break;
                            case 12:
                                $android = "Android 12";
                                break;
                            case 13:
                                $android = "Android 13";
                                break;
                            default:
                                $android = "Unknown";
                        endswitch;

                        $slots = explode(",", $partner["slots"]);
                        $currency = country($partner["country"])->getCurrency()["iso_4217_code"];

                        $partnerArray[] = [
                            "unique" => $partner["unique"],
                            "name" => $partner["name"],
                            "version" => $android,
                            "priority" => $partner["priority"] < 2 ? true : false,
                            "sim" => $slots,
                            "country" => strtoupper($partner["country"]),
                            "currency" => strtoupper($currency),
                            "rate" => (int) $partner["rate"],
                            "owner" => $partner["owner"],
                            "status" => $partner["status"] < 2 ? "online" : "offline"
                        ];
                    endforeach;
                endif;

                response(200, "Gateway Rates", [
                    "gateways" => $gatewayArray,
                    "partners" => $partnerArray
                ]);

                break;
            case "shorteners":
                /**
                 * @api {get} /get/shorteners Get Shorteners
                 * @apiName Get Shorteners
                 * @apiDescription Get Shorteners. Requires "<strong>get_shorteners</strong>" API permission.
                 * @apiGroup System
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/get/shorteners?secret={$apiSecret}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    r = requests.get(url = "http://127.0.0.1/zender/api/get/shorteners", params = {
                        "secret": apiSecret
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Available Shorteners",
                   "data": [
                        {
                            "id": 1,
                            "name": "Bitly"
                        }
                    ]
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("get_shorteners", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                $shorteners = $this->api->getShorteners($api["uid"]);

                $shortenerArray = [];

                if(!empty($shorteners)):
                    foreach($shorteners as $shortener):
                        $shortenerArray[] = [
                            "id" => (int) $shortener["id"],
                            "name" => $shortener["name"],
                        ];
                    endforeach;
                endif;

                response(200, "Available Shorteners", $shortenerArray);

                break;
            default:
                response(400, "Invalid API Endpoint!");
        endswitch;
	}

	public function create()
	{
        $this->header->allow();

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        $request = $this->sanitize->array($_REQUEST);
        $type = $this->sanitize->string($this->url->segment(4));

        if(!isset($request["secret"]))
            response(400, "Invalid Parameters!");

        if($this->api->checkApikey($request["secret"]) < 1)
            response(401, "Invalid API secret supplied!");

        $api = $this->api->getApikey($request["secret"]);

        $permissions = explode(",", $api["permissions"]);

        if(!is_array($permissions))
            response(403, "Insufficient Permissions!");

        switch($type):
            case "whatsapp":
                /**
                 * @api {get} /create/whatsapp Create WhatsApp QRCode
                 * @apiName Create WhatsApp QRCode
                 * @apiDescription Create WhatsApp QRCode. Requires "<strong>create_whatsapp</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $contact = [
                        "secret" => "API_SECRET", // your API secret from (Tools -> API Keys) page
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/api/create/whatsapp");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $contact);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    contact = {
                        "secret": apiSecret
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/api/create/whatsapp", params = contact)
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "WhatsApp QRCode has been created!",
                   "data": {
                        qrstring: "2@MwggDzdZqWfC4vYBJQsExNnSuE6+fyGYVo+/RfMyWUxJBW2Q0yDKykpqRi+pSoHquonRk5P6CaVOsg==,BpVhDS5yHBbN9k/xCiQIWwOduYcyo/1tMhoWaNpwJC8=,7F75UfkJzXY6GbLy+3evLc9aCkyN40K2ORR0dZ84eSk=,7nQ0NTR4eaXRZOwIbv9FKoFpFTSNu6fHzKGaICsyDzc=",
                        qrimagelink: "http://127.0.0.1/zender/api/get/wq.qr?token=e10adc3949ba59abbe56e057f20f883e&qrstring=2@MwggDzdZqWfC4vYBJQsExNnSuE6+fyGYVo+/RfMyWUxJBW2Q0yDKykpqRi+pSoHquonRk5P6CaVOsg==,BpVhDS5yHBbN9k/xCiQIWwOduYcyo/1tMhoWaNpwJC8=,7F75UfkJzXY6GbLy+3evLc9aCkyN40K2ORR0dZ84eSk=,7nQ0NTR4eaXRZOwIbv9FKoFpFTSNu6fHzKGaICsyDzc=",
                   }
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("create_whatsapp", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                $subscription = set_subscription(
                    $this->system->checkSubscription($api["uid"]), 
                    $this->system->getSubscription(false, $api["uid"]), 
                    $this->system->getSubscription(false, false, true)
                );

                if(empty($subscription))
                    response(400, "Account is not subscribed to any premium package!");

                if(limitation($subscription["wa_account_limit"], $this->system->countWaAccounts($api["uid"])))
                    response(400, "You have reached the maximum allowed WhatsApp accounts for your package!");

                if(empty(system_wa_server) || empty(system_wa_port))
                    response(400, "WhatsApp server is unavailable!");

                $this->wa->_guzzle = $this->guzzle;

                if($this->wa->check()):
                    $qrString = $this->wa->create($api["uid"], $api["hash"]);

                    if($qrString):
                        $token = sha1(uniqid(time(), true));

                        $this->cache->container("system.whatsapp", true);
                        $this->cache->set($token, $request["secret"], 120);

                        response(200, "WhatsApp QRCode has been created!", [
                            "qrstring" => $qrString,
                            "qrimagelink" => site_url("api/get/wa.qr?token={$token}&qrstring={$qrString}", true)
                        ]);
                    else:
                        response(500, "Unable to generate WhatsApp QRCode!");
                    endif;
                else:
                    response(500, "Unable to connect to WhatsApp servers!");
                endif;

                break;
            case "contact":
                /**
                 * @api {post} /create/contact Create Contact
                 * @apiName Create Contact
                 * @apiDescription Create Contact. Requires "<strong>create_contact</strong>" API permission.
                 * @apiGroup Contacts
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {String} phone phone Recipient mobile number, it will accept E.164 formatted number or locally formatted numbers using the country code from your profile settings.<br>
                 * <strong>Example for Philippines</strong><br>
                 * E.164: +639184661533<br>
                 * Local: 09184661533
                 * @apiParam {String} name Name of contact
                 * @apiParam {String} groups List of contact group ID's separated by commas. You can get group ID's from <strong>/get/groups</strong> (Your contact groups).
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $contact = [
                        "secret" => "API_SECRET", // your API secret from (Tools -> API Keys) page
                        "groups" => "1,2,3,4",
                        "phone" => "+639123456789",
                        "name" => "Martin Crater"
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/api/create/contact");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $contact);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    contact = {
                        "secret": apiSecret,
                        "groups": "1,2,3,4",
                        "phone": "+639123456789",
                        "name": "Martin Crater"
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/api/create/contact", params = contact)
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Contact has been created!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("create_contact", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["name"], $request["phone"], $request["groups"]))
                    response(400, "Invalid Parameters!");

                $subscription = set_subscription(
                    $this->system->checkSubscription($api["uid"]), 
                    $this->system->getSubscription(false, $api["uid"]), 
                    $this->system->getSubscription(false, false, true)
                );

                if(empty($subscription))
                    response(400, "Account is not subscribed to any premium package!");

                if(limitation($subscription["contact_limit"], $this->system->countContacts($api["uid"])))
                    response(400, "You have reached the maximum allowed contacts!");

                if(!$this->sanitize->length($request["name"]))
                    response(400, "Contact name is too short!");

                try {
                    $number = $this->phone->parse($request["phone"], $api["country"]);

                    $number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

                    if (!$number->isValidNumber())
                        response(400, "Invalid phone number!");

                    if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
                        response(400, "Invalid phone number!");

                    $request["phone"] = $number->format(Brick\PhoneNumber\PhoneNumberFormat::E164);
                } catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
                    response(400, "Invalid phone number!");
                }

                $groups = explode(",", $request["groups"]);

                if(!is_array($groups))
                    response(400, "Invalid Parameters!");

                foreach($groups as $group):
                    if($this->system->checkGroup($api["uid"], $group) < 1)
                        response(400, "Group #{$group} is invalid!");
                endforeach;

                if($this->system->checkNumber($api["uid"], $request["phone"]) > 0)
                    response(400, "Phone number already exist in your contact book!");

                $filtered = [
                    "uid" => $api["uid"],
                    "groups" => implode(",", $groups),
                    "phone" => $request["phone"],
                    "name" => $request["name"]
                ];

                if($this->system->create("contacts", $filtered)):
                    $this->cache->container("autocomplete.contacts.{$api["hash"]}");
                    $this->cache->clear();
                    $this->cache->container("contacts.{$api["hash"]}");
                    $this->cache->clear();
                    $this->cache->container("user.{$api["hash"]}");
                    $this->cache->clear();

                    response(200, "Contact has been created!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "group":
                /**
                 * @api {post} /create/group Create Group
                 * @apiName Create Group
                 * @apiDescription Create Group. Requires "<strong>create_group</strong>" API permission.
                 * @apiGroup Contacts
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {String} name Name of group
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $group = [
                        "secret" => "API_SECRET", // your API secret from (Tools -> API Keys) page
                        "name" => "Friends"
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/api/create/group");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $group);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"

                    group = {
                        "secret": apiSecret,
                        "name": "Friends"
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/api/create/group", params = group)
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Contact group has been created!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("create_group", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["name"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->length($request["name"]))
                    response(400, "Group name is too short!");

                $filtered = [
                    "uid" => $api["uid"],
                    "name" => $request["name"]
                ];

                if($this->system->create("groups", $filtered)):
                    response(200, "Contact group has been created!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            default:
                response(400, "Invalid API Endpoint!");
        endswitch;
	}

	public function delete()
	{
		$this->header->allow();

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        $request = $this->sanitize->array($_REQUEST);
        $type = $this->sanitize->string($this->url->segment(4));

        if(!isset($request["secret"]))
            response(400, "Invalid Parameters!");

        if($this->api->checkApikey($request["secret"]) < 1)
            response(401, "Invalid API secret supplied!");

        $api = $this->api->getApikey($request["secret"]);

        $permissions = explode(",", $api["permissions"]);

        if(!is_array($permissions))
            response(403, "Insufficient Permissions!");

        switch($type):
            case "contact":
                /**
                 * @api {get} /delete/contact Delete Contact
                 * @apiName Delete Contact
                 * @apiDescription Delete Contact. Requires "<strong>delete_contact</strong>" API permission.
                 * @apiGroup Contacts
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} id Contact ID
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $contactId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/delete/contact?secret={$apiSecret}&id={$contactId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    contactId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/delete/contact", params = {
                        "secret": apiSecret
                        "id": contactId
                    })
                      
                    # do something with response object
                    result = r.json()
                 * 
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Contact has been deleted!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("delete_contact", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($this->system->delete($api["uid"], $request["id"], "contacts")):
                    $this->cache->container("autocomplete.contacts.{$api["hash"]}");
                    $this->cache->clear();
                    $this->cache->container("contacts.{$api["hash"]}");
                    $this->cache->clear();
                    $this->cache->container("user.{$api["hash"]}");
                    $this->cache->clear();

                    response(200, "Contact has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "group":
                /**
                 * @api {get} /delete/group Delete Group
                 * @apiName Delete Group
                 * @apiDescription Delete Group. Requires "<strong>delete_group</strong>" API permission.
                 * @apiGroup Contacts
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} id Contact group ID
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $groupId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/delete/group?secret={$apiSecret}&id={$groupId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    groupId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/delete/group", params = {
                        "secret": apiSecret
                        "id": groupId
                    })
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Contact group has been deleted!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("delete_group", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($this->system->delete($api["uid"], $request["id"], "groups")):
                    response(200, "Contact group has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "unsubscribed":
                /**
                 * @api {get} /delete/unsubscribed Delete Unsubscribed
                 * @apiName Delete Unsubscribed
                 * @apiDescription Delete Unsubscribed Contact. Requires "<strong>delete_unsubscribed</strong>" API permission.
                 * @apiGroup Contacts
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} id Contact ID
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $unsubscribedId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/delete/unsubscribed?secret={$apiSecret}&id={$unsubscribedId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    unsubscribedId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/delete/unsubscribed", params = {
                        "secret": apiSecret
                        "id": unsubscribedId
                    })
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Unsubscribed contact has been deleted!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("delete_unsubscribed", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($this->system->delete($api["uid"], $request["id"], "unsubscribed")):
                    response(200, "Unsubscribed contact has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "sms.sent":
                /**
                 * @api {get} /delete/sms.sent Delete Sent Message
                 * @apiName Delete Sent Message
                 * @apiDescription Delete Sent Message. Requires "<strong>delete_sms_sent</strong>" API permission.
                 * @apiGroup SMS
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} id Sent message ID
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $smsId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/delete/sms.sent?secret={$apiSecret}&id={$smsId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    smsId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/delete/sms.sent", params = {
                        "secret": apiSecret
                        "id": smsId
                    })
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Sent SMS has been deleted!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("delete_sms_sent", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                $sent = $this->system->getSent($request["id"]);

                if($this->system->delete($api["uid"], $request["id"], "sent")):
                    try {
                        $this->fcm->send(md5($api["uid"] . $sent["did"]), [
                            "type" => "sms_delete",
                            "id" => $request["id"]
                        ]);
                    } catch(Exception $e) {
                        // Ignore
                    }
                    
                    response(200, "Sent SMS has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "sms.campaign":
                /**
                 * @api {get} /delete/sms.campaign Delete SMS Campaign
                 * @apiName Delete SMS Campaign
                 * @apiDescription Delete SMS Campaign. Requires "<strong>delete_sms_campaign</strong>" API permission.
                 * @apiGroup SMS
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} id Campaign ID
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $campaignId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/delete/sms.campaign?secret={$apiSecret}&id={$campaignId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    campaignId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/delete/sms.campaign", params = {
                        "secret": apiSecret
                        "id": campaignId
                    })
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "SMS campaign has been deleted!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("delete_sms_campaign", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($this->api->checkSmsCampaign($request["campaign"], $api["uid"]) < 1)
                    response(400, "Invalid Parameters!");

                if($this->system->delete($api["uid"], $request["id"], "campaigns")):
                    if($this->system->clearCampaignSms($api["uid"], $request["id"])):
                        $this->fcm->send($this->hash->encode($api["uid"], system_token), [
                            "type" => "sms_campaign_delete",
                            "cid" => $request["id"]
                        ]);
                    endif;

                    response(200, "SMS campaign has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "sms.received":
                /**
                 * @api {get} /delete/sms.received Delete Received Message
                 * @apiName Delete Received Message
                 * @apiDescription Delete Received Message. Requires "<strong>delete_sms_received</strong>" API permission.
                 * @apiGroup SMS
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} id Received message ID
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $smsId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/delete/sms.received?secret={$apiSecret}&id={$smsId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    smsId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/delete/sms.received", params = {
                        "secret": apiSecret
                        "id": smsId
                    })
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Received SMS has been deleted!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("delete_sms_received", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($this->system->delete($api["uid"], $request["id"], "received")):
                    response(200, "Received SMS has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "wa.sent":
                /**
                 * @api {get} /delete/wa.sent Delete Sent Chat
                 * @apiName Delete Sent Chat
                 * @apiDescription Delete Sent Chat. Requires "<strong>delete_wa_sent</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} id Sent chat ID
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $chatId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/delete/wa.sent?secret={$apiSecret}&id={$chatId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    chatId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/delete/wa.sent", params = {
                        "secret": apiSecret
                        "id": chatId
                    })
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Sent WhatsApp chat has been deleted!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("delete_wa_sent", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                $sent = $this->system->getWaSent($request["id"]);

                if($this->system->delete($api["uid"], $request["id"], "wa_sent")):
                    try {
                        $this->wa->_guzzle = $this->guzzle;

                        $account = $this->system->getWaAccount($api["uid"], $sent["wid"], "wid");

                        $this->wa->delete_chat($account["unique"], $api["hash"], $request["id"]);
                    } catch(Exception $e){
                        // Ignore
                    }

                    response(200, "Sent WhatsApp chat has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "wa.campaign":
                /**
                 * @api {get} /delete/wa.campaign Delete WhatsApp Campaign
                 * @apiName Delete WhatsApp Campaign
                 * @apiDescription Delete WhatsApp Campaign. Requires "<strong>delete_wa_campaign</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} id Campaign ID
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $campaignId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/delete/wa.campaign?secret={$apiSecret}&id={$campaignId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    campaignId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/delete/wa.campaign", params = {
                        "secret": apiSecret
                        "id": campaignId
                    })
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "WhatsApp campaign has been deleted!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("delete_wa_sent", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($this->api->checkWaCampaign($request["id"], $api["uid"]) < 1)
                    response(400, "Invalid Parameters!");

                $campaign = $this->system->getWaCampaign($api["uid"], $request["id"], "id");

                if($this->system->delete($api["uid"], $request["id"], "wa_campaigns")):
                    if($this->system->clearCampaignChats($api["uid"], $request["id"])):
                        try {
                            $this->wa->_guzzle = $this->guzzle;

                            $account = $this->system->getWaAccount($api["uid"], $campaign["wid"], "wid");

                            $this->wa->delete_campaign($account["unique"], $api["hash"], $request["id"]);
                        } catch(Exception $e){
                            // Ignore
                        }
                    endif;

                    response(200, "WhatsApp campaign has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "wa.received":
                /**
                 * @api {get} /delete/wa.received Delete Received Chat
                 * @apiName Delete Received Chat
                 * @apiDescription Delete Received Chat. Requires "<strong>delete_wa_received</strong>" API permission.
                 * @apiGroup WhatsApp
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} id Received chat ID
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $chatId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/delete/wa.received?secret={$apiSecret}&id={$chatId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    chatId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/delete/wa.received", params = {
                        "secret": apiSecret
                        "id": chatId
                    })
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Received WhatsApp chat has been deleted!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("delete_wa_received", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($this->system->delete($api["uid"], $request["id"], "wa_received")):
                    response(200, "Received WhatsApp chat has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "ussd":
                /**
                 * @api {get} /delete/ussd Delete USSD Request
                 * @apiName Delete USSD Request
                 * @apiDescription Delete USSD Request. Requires "<strong>delete_ussd</strong>" API permission.
                 * @apiGroup USSD
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} id USSD request ID
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $ussdId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/delete/ussd?secret={$apiSecret}&id={$ussdId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    ussdId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/delete/ussd", params = {
                        "secret": apiSecret
                        "id": ussdId
                    })
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "USSD request has been deleted!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("delete_ussd", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($this->system->delete($api["uid"], $request["id"], "ussd")):
                    response(200, "USSD request has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "notification":
                /**
                 * @api {get} /delete/notification Delete Notification
                 * @apiName Delete Notification
                 * @apiDescription Delete Notification. Requires "<strong>delete_notification</strong>" API permission.
                 * @apiGroup Notifications
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} secret The API secret you copied from (Tools -> API Keys) page
                 * @apiParam {Number} id Notification ID
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $apiSecret = "API_SECRET"; // your API secret from (Tools -> API Keys) page
                    $notificationId = 1;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/api/delete/notification?secret={$apiSecret}&id={$notificationId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # your API secret from (Tools -> API Keys) page
                    apiSecret = "API_SECRET"
                    notificationId = 1

                    r = requests.get(url = "http://127.0.0.1/zender/api/delete/notification", params = {
                        "secret": apiSecret
                        "id": notificationId
                    })
                      
                    # do something with response object
                    result = r.json()
                 *
                 * @apiSuccess (Success Response Format) {Number} status List of Codes
                 * <br> 200 = Success
                 * @apiSuccess (Success Response Format) {String} message Response message
                 * @apiSuccess (Success Response Format) {Array} data Array of data
                 *
                 * @apiSuccessExample {json} Success Response
                 {
                   "status": 200,
                   "message": "Notification has been deleted!",
                   "data": false
                 }
                 * 
                 * @apiError (Error Response Format) {Number} status List of Codes<br>
                 * 400 = Invalid parameters<br>
                 * 401 = Invalid API secret<br>
                 * 403 = Access denied<br>
                 * 500 = Something went wrong
                 * @apiError (Error Response Format) {String} message Response message
                 * @apiError (Error Response Format) {Array} data Array of data
                 * 
                 * @apiErrorExample {json} Error Response
                 {
                   "status": 400,
                   "message": "Invalid Parameters!",
                   "data": false
                 }
                 *
                 */

                if(!in_array("delete_notification", explode(",", $api["permissions"])))
                    response(403, "This API key doesn't have permission to use this endpoint!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($this->system->delete($api["uid"], $request["id"], "notifications")):
                    response(200, "Notification has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            default:
                response(400, "Invalid API Endpoint!");
        endswitch;
	}
}