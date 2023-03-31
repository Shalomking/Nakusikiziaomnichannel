<?php

class Admin_Controller extends MVC_Controller
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

        if(!is_admin)
        	response(403);

        try {
            $phoneSample = $this->phone->getExampleNumber(system_default_country, Brick\PhoneNumber\PhoneNumberType::MOBILE);
        } catch(Exception $e){
            $phoneSample = "+63123456789";
        }

        $vars = [
            "site_url" => site_url(false, true),
            "data" => [
                "number" => $this->phone->getExampleNumber(system_default_country, Brick\PhoneNumber\PhoneNumberType::MOBILE),
                "type" => "admin"
            ]
        ];
        
        $this->smarty->display("_apidoc/layout.tpl", $vars);
	}

	public function get()
	{
        $this->header->allow();

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        if(in_array("0", explode(",", system_admin_api)))
            response(403, "Admin API is disabled!");

        $request = $this->sanitize->array($_REQUEST);
        $type = $this->sanitize->string($this->url->segment(4));

        if(!isset($request["token"]))
            response(400, "Invalid Parameters!");

        if($request["token"] != system_token)
            response(401, "Invalid system token supplied!");

        switch($type):
            case "users":
                /**
                 * @api {get} /get/users Get Accounts
                 * @apiName Get Accounts
                 * @apiDescription Get the list of user accounts.
                 * @apiGroup Users
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/get/users?token={$systemToken}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    r = requests.get(url = "http://127.0.0.1/zender/system/get/users", params = {
                        "token": systemToken
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
                   "message": "System Users",
                   "data": [
                        {
                            "id": 1,
                            "role": 1,
                            "credits": 798.132,
                            "earnings": 1.9386,
                            "partner": true,
                            "email": "mail@domain.com",
                            "name": "Shane Mondez",
                            "country": "PH",
                            "timezone": "asia/manila",
                            "language": 5,
                            "notification_sounds": true,
                            "suspended": true,
                            "registered": 1585646141
                        },
                        {
                            "id": 4,
                            "role": 3,
                            "credits": 50,
                            "earnings": 0,
                            "partner": true,
                            "email": "test@domain.net",
                            "name": "Test User",
                            "country": "KW",
                            "timezone": "asia/kuwait",
                            "language": 1,
                            "notification_sounds": false,
                            "suspended": false,
                            "registered": 1587452434
                        },
                        {
                            "id": 10,
                            "role": 1,
                            "credits": 0,
                            "earnings": 0,
                            "partner": false,
                            "email": "test@domain.org",
                            "name": "Bobby Matthews",
                            "country": "US",
                            "timezone": "america/chicago",
                            "language": 3,
                            "notification_sounds": true,
                            "suspended": false,
                            "registered": 1615488072
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

                if(!in_array("get_users", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

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

                $users = $this->admin->getUsers(abs($page), abs($limit));

                $userArray = [];

                if(!empty($users)):
                    foreach($users as $user):
                        $userArray[] = [
                            "id" => (int) $user["id"],
                            "role" => (int) $user["role"],
                            "credits" => (float) $user["credits"],
                            "earnings" => (float) $user["earnings"],
                            "partner" => (int) $user["partner"] < 2 ? true : false,
                            "email" => $user["email"],
                            "name" => $user["name"],
                            "country" => $user["country"],
                            "timezone" => $user["timezone"],
                            "language" => (int) $user["language"],
                            "notification_sounds" => $user["alertsound"] < 2 ? true : false,
                            "suspended" => $user["suspended"] < 1 ? true : false,
                            "registered" => strtotime($user["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "System Users", $userArray);

                break;
            case "roles":
                /**
                 * @api {get} /get/roles Get Roles
                 * @apiName Get Roles
                 * @apiDescription Get the list of roles.
                 * @apiGroup Roles
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/get/roles?token={$systemToken}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    r = requests.get(url = "http://127.0.0.1/zender/system/get/roles", params = {
                        "token": systemToken
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
                   "message": "System Roles",
                   "data": [
                        {
                            "id": 1,
                            "name": "Default",
                            "permissions": [
                                "default_permissions"
                            ]
                        },
                        {
                            "id": 4,
                            "name": "Rangers",
                            "permissions": [
                                "manage_users",
                                "manage_roles"
                            ]
                        },
                        {
                            "id": 5,
                            "name": "Developer",
                            "permissions": [
                                "manage_api"
                            ]
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

                if(!in_array("get_roles", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

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

                $roles = $this->admin->getRoles(abs($page), abs($limit));

                $roleArray = [];

                if(!empty($roles)):
                    foreach($roles as $role):
                        $permissions = explode(",", $role["permissions"]);
                        $roleArray[] = [
                            "id" => (int) $role["id"],
                            "name" => $role["name"],
                            "permissions" => $role["id"] < 2 ? ["default_permissions"] : (is_array($permissions) ? $permissions : [])
                        ];
                    endforeach;
                endif;

                response(200, "System Roles", $roleArray);

                break;
            case "packages":
                /**
                 * @api {get} /get/packages Get Packages
                 * @apiName Get Packages
                 * @apiDescription Get the list of packages.
                 * @apiGroup Packages
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/get/packages?token={$systemToken}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    r = requests.get(url = "http://127.0.0.1/zender/system/get/packages", params = {
                        "token": systemToken
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
                   "message": "System Packages",
                   "data": [
                        {
                            "id": 1,
                            "price": 0,
                            "hidden": false,
                            "footermark": true,
                            "name": "Starter",
                            "sms_send_limit": 1000,
                            "sms_receive_limit": 250,
                            "ussd_limit": 0,
                            "notification_limit": 0,
                            "device_limit": 3,
                            "wa_send_limit": 0,
                            "wa_receive_limit": 0,
                            "wa_account_limit": 0,
                            "scheduled_limit": 0,
                            "key_limit": 5,
                            "webhook_limit": 5,
                            "action_limit": 0,
                            "created": 1586370407
                        },
                        {
                            "id": 2,
                            "price": 12,
                            "hidden": true,
                            "footermark": false,
                            "name": "Professional",
                            "sms_send_limit": 3000,
                            "sms_receive_limit": 1500,
                            "ussd_limit": 0,
                            "notification_limit": 0,
                            "device_limit": 30,
                            "wa_send_limit": 0,
                            "wa_receive_limit": 0,
                            "wa_account_limit": 0,
                            "scheduled_limit": 0,
                            "key_limit": 10,
                            "webhook_limit": 5,
                            "action_limit": 0,
                            "created": 1587393358
                        },
                        {
                            "id": 3,
                            "price": 30,
                            "hidden": false,
                            "footermark": false,
                            "name": "Enterprise",
                            "sms_send_limit": 10000,
                            "sms_receive_limit": 7000,
                            "ussd_limit": 0,
                            "notification_limit": 0,
                            "device_limit": 50,
                            "wa_send_limit": 0,
                            "wa_receive_limit": 0,
                            "wa_account_limit": 0,
                            "scheduled_limit": 0,
                            "key_limit": 25,
                            "webhook_limit": 15,
                            "action_limit": 0,
                            "created": 1587393393
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

                if(!in_array("get_packages", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

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

                $packages = $this->admin->getPackages(abs($page), abs($limit));

                $packageArray = [];

                if(!empty($packages)):
                    foreach($packages as $package):
                        $packageArray[] = [
                            "id" => (int) $package["id"],
                            "price" => (int) $package["price"],
                            "hidden" => (int) $package["hidden"] < 2 ? true : false,
                            "footermark" => (int) $package["footermark"] < 2 ? true : false,
                            "name" => $package["name"],
                            "sms_send_limit" => (int) $package["send_limit"],
                            "sms_receive_limit" => (int) $package["receive_limit"],
                            "ussd_limit" => (int) $package["ussd_limit"],
                            "notification_limit" => (int) $package["notification_limit"],
                            "device_limit" => (int) $package["device_limit"],
                            "wa_send_limit" => (int) $package["wa_send_limit"],
                            "wa_receive_limit" => (int) $package["wa_receive_limit"],
                            "wa_account_limit" => (int) $package["wa_account_limit"],
                            "scheduled_limit" => (int) $package["scheduled_limit"],
                            "key_limit" => (int) $package["key_limit"],
                            "webhook_limit" => (int) $package["webhook_limit"],
                            "action_limit" => (int) $package["action_limit"],
                            "created" => strtotime($package["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "System Packages", $packageArray);

                break;
            case "vouchers":
                /**
                 * @api {get} /get/vouchers Get Vouchers
                 * @apiName Get Vouchers
                 * @apiDescription Get the list of unused vouchers.
                 * @apiGroup Vouchers
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/get/vouchers?token={$systemToken}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    r = requests.get(url = "http://127.0.0.1/zender/system/get/vouchers", params = {
                        "token": systemToken
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
                   "message": "System Vouchers",
                   "data": [
                        {
                            "id": 27,
                            "package": 4,
                            "duration": 1,
                            "name": "1k Voucher #1",
                            "code": "caf1fbc8191d3bdfe736f5b6a36011cf",
                            "created": 1605869831
                        },
                        {
                            "id": 28,
                            "package": 4,
                            "duration": 1,
                            "name": "1k Voucher #2",
                            "code": "b7c289d2b0e69c5ee0791c966650e8ca",
                            "created": 1605869831
                        },
                        {
                            "id": 29,
                            "package": 4,
                            "duration": 1,
                            "name": "1k Voucher #3",
                            "code": "062a0cdc6faf8a3fa91aa572495c861f",
                            "created": 1605869831
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

                if(!in_array("get_vouchers", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

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

                $vouchers = $this->admin->getVouchers(abs($page), abs($limit));

                $voucherArray = [];

                if(!empty($vouchers)):
                    foreach($vouchers as $voucher):
                        $voucherArray[] = [
                            "id" => (int) $voucher["id"],
                            "package" => (int) $voucher["package"],
                            "duration" => (int) $voucher["duration"],
                            "name" => $voucher["name"],
                            "code" => $voucher["code"],
                            "created" => strtotime($voucher["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "System Vouchers", $voucherArray);

                break;
            case "subscriptions":
                /**
                 * @api {get} /get/subscriptions Get Subscriptions
                 * @apiName Get Subscriptions
                 * @apiDescription Get the list of active subscriptions.
                 * @apiGroup Subscriptions
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/get/subscriptions?token={$systemToken}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    r = requests.get(url = "http://127.0.0.1/zender/system/get/subscriptions", params = {
                        "token": systemToken
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
                   "message": "System Subscriptions",
                   "data": [
                        {
                            "id": 2,
                            "user": 16,
                            "package": 2,
                            "transaction": 4,
                            "created": 1644202610
                        },
                        {
                            "id": 3,
                            "user": 6,
                            "package": 3,
                            "transaction": 6,
                            "created": 1645635227
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

                if(!in_array("get_subscriptions", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

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

                $subscriptions = $this->admin->getSubscriptions(abs($page), abs($limit));

                $subscriptionArray = [];

                if(!empty($subscriptions)):
                    foreach($subscriptions as $subscription):
                        $subscriptionArray[] = [
                            "id" => (int) $subscription["id"],
                            "user" => (int) $subscription["uid"],
                            "package" => (int) $subscription["pid"],
                            "transaction" => (int) $subscription["tid"],
                            "created" => strtotime($subscription["date"])
                        ];
                    endforeach;
                endif;

                response(200, "System Subscriptions", $subscriptionArray);

                break;
            case "transactions":
                /**
                 * @api {get} /get/transactions Get Transactions
                 * @apiName Get Transactions
                 * @apiDescription Get the list of transactions.
                 * @apiGroup Transactions
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/get/transactions?token={$systemToken}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    r = requests.get(url = "http://127.0.0.1/zender/system/get/transactions", params = {
                        "token": systemToken
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
                   "message": "System Transactions",
                   "data": [
                        {
                            "id": 1,
                            "user": 3,
                            "package": 0,
                            "type": "credits",
                            "price": 10,
                            "currency": "GBP",
                            "duration": 1,
                            "provider": "paypal",
                            "created": 1644030231
                        },
                        {
                            "id": 2,
                            "user": 7,
                            "package": 4,
                            "type": "package",
                            "price": 100,
                            "currency": "GBP",
                            "duration": 1,
                            "provider": "mollie",
                            "created": 1644030301
                        },
                        {
                            "id": 3,
                            "user": 9,
                            "package": 2,
                            "type": "package",
                            "price": 2400,
                            "currency": "GBP",
                            "duration": 24,
                            "provider": "voucher",
                            "created": 1644059151
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

                if(!in_array("get_transactions", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

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

                $transactions = $this->admin->getTransactions(abs($page), abs($limit));

                $transactionArray = [];

                if(!empty($transactions)):
                    foreach($transactions as $transaction):
                        $transactionArray[] = [
                            "id" => (int) $transaction["id"],
                            "user" => (int) $transaction["uid"],
                            "package" => (int) $transaction["pid"],
                            "type" => $transaction["type"] == 2 ? "credits" : "package",
                            "price" => (float) $transaction["price"],
                            "currency" => $transaction["currency"],
                            "duration" => (int) $transaction["duration"],
                            "provider" => $transaction["provider"],
                            "created" => strtotime($transaction["create_date"])
                        ];
                    endforeach;
                endif;

                response(200, "System Transactions", $transactionArray);

                break;
            case "languages":
                /**
                 * @api {get} /get/languages Get Languages
                 * @apiName Get Languages
                 * @apiDescription Get the list of languages.
                 * @apiGroup Languages
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} [limit=10] Limit the number of results per page.
                 * @apiParam {Number} [page=1] Pagination of results.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/get/languages?token={$systemToken}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    r = requests.get(url = "http://127.0.0.1/zender/system/get/languages", params = {
                        "token": systemToken
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
                   "message": "System Languages",
                   "data": [
                        {
                            "id": 1,
                            "order": 2,
                            "rtl": false,
                            "iso": "US",
                            "name": "English"
                        },
                        {
                            "id": 2,
                            "order": 1,
                            "rtl": false,
                            "iso": "PH",
                            "name": "Filipino"
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

                if(!in_array("get_languages", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

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

                $languages = $this->admin->getLanguages(abs($page), abs($limit));

                $languageArray = [];

                if(!empty($languages)):
                    foreach($languages as $language):
                        $languageArray[] = [
                            "id" => (int) $language["id"],
                            "order" => (int) $language["order"],
                            "rtl" => $language["rtl"] < 2 ? true : false,
                            "iso" => $language["iso"],
                            "name" => $language["name"]
                        ];
                    endforeach;
                endif;

                response(200, "System Languages", $languageArray);

                break;
            default:
                response(500, "Invalid API Endpoint!");
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

        if(in_array("0", explode(",", system_admin_api)))
            response(403, "Admin API is disabled!");

        $request = $this->sanitize->array($_REQUEST);
        $type = $this->sanitize->string($this->url->segment(4));

        if(!isset($request["token"]))
            response(400, "Invalid Parameters!");

        if($request["token"] != system_token)
            response(401, "Invalid system token supplied!");

        switch($type):
            case "user":
                /**
                 * @api {post} /create/user Create Account
                 * @apiName Create Account
                 * @apiDescription Create a new user account.
                 * @apiGroup Users
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {String} name Full name of the user.
                 * @apiParam {String} email Email address for the user account.
                 * @apiParam {String} password Password for the user account.
                 * @apiParam {Number} [credits] Credits for the user account. Whole number only.
                 * @apiParam {String} timezone Timezone for the user account, here's the list of valid timezones you can use: <a href="https://www.w3schools.com/php/php_ref_timezones.asp">Click Here</a>.
                 * @apiParam {String} country Country code, it should satisfy ISO Alpha-2 format.
                 * @apiParam {Number} language Language ID, you can get the a language ID from <strong>/get/languages</strong>
                 * @apiParam {Number} [role=1] Role ID, you can get a role ID from <strong>/get/roles</strong>
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $user = [
                        "token" => "SYSTEM_TOKEN", // system token, can be acquired from system settings.
                        "name" => "Ryan Huggins",
                        "email" => "mail@domain.com",
                        "password" => "123456",
                        "credits" => 100,
                        "timezone" => "Asia/Manila",
                        "country" => "PH",
                        "language" => 1
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/system/create/user");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $user);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    user = {
                        "token": systemToken,
                        "name": "Ryan Huggins",
                        "email": "mail@domain.com",
                        "password": "123456",
                        "credits": 100,
                        "timezone": "Asia/Manila",
                        "country": "PH",
                        "language": 1
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/system/create/user", params = user)
                      
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
                   "message": "User has been created!",
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

                if(!in_array("create_user", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

                if(!isset($request["name"], $request["email"], $request["password"], $request["timezone"], $request["country"], $request["language"]))
                    response(400, "Invalid Parameters!");

                if(isset($request["role"])):
                    if(!$this->sanitize->isInt($request["role"])):
                        response(400, "Invalid Parameters!");
                    endif;
                else:
                    $request["role"] = 1;
                endif;

                if(!$this->sanitize->isInt($request["language"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->length($request["name"]))
                    response(400, "Name is too short!");

                if(!$this->sanitize->isEmail($request["email"]))
                    response(400, "Invalid email address!");

                if(!$this->sanitize->length($request["password"], 5))
                    response(400, "Password is too short!");

                if(!in_array(strtolower($request["timezone"]), $this->timezones->generate()))
                    response(400, "Invalid Parameters!");

                if(!array_key_exists(strtoupper($request["country"]), \CountryCodes::get("alpha2", "country")))
                    response(400, "Invalid Parameters!");

                if($this->system->checkRole($request["role"]) < 1)
                    response(400, "Invalid Parameters!");

                if($this->system->checkLanguage($request["language"]) < 1)
                    response(400, "Invalid Parameters!");

                $filtered = [
                    "timezone" => strtolower($request["timezone"]),
                    "formatting" => false,
                    "country" => strtoupper($request["country"]),
                    "role" => $request["role"],
                    "name" => $request["name"],
                    "language" => $request["language"],
                    "email" => $this->sanitize->email($request["email"]),
                    "credits" => isset($request["credits"]) && $this->sanitize->isInt($request["credits"]) ? $request["credits"] : 0,
                    "earnings" => 0,
                    "suspended" => 0,
                    "providers" => false,
                    "alertsound" => 1,
                    "partner" => 2,
                    "confirmed" => 1,
                    "password" => password_hash($request["password"], PASSWORD_DEFAULT)
                ];

                if($this->system->checkEmail($filtered["email"]) < 1):
                    $create = $this->system->create("users", $filtered);
                    if($create):
                        response(200, "User has been created!");
                    else:
                        response(500, "Something went wrong!");
                    endif;
                else:
                    response(400, "Email address was already used!");
                endif;

                break;
            case "role":
                /**
                 * @api {post} /create/role Create Role
                 * @apiName Create Role
                 * @apiDescription Create a new user role.
                 * @apiGroup Roles
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token
                 * @apiParam {String} name Name of the user role.
                 * @apiParam {String="disallow_sms", "disallow_ussd", "disallow_notifications", "disallow_devices", "disallow_wa_chats", "disallow_wa_accounts", "disallow_contacts", "disallow_groups", "disallow_keys", "disallow_webhooks", "disallow_actions", "disallow_templates", "disallow_extensions", "disallow_redeem", "disallow_subscribe", "disallow_topup", "disallow_withdraw", "disallow_convert", "disallow_api", "manage_users", "manage_roles", "manage_packages", "manage_vouchers", "manage_subscriptions", "manage_transactions", "manage_widgets", "manage_pages", "manage_marketing", "manage_languages", "manage_gateways", "manage_shorteners", "manage_plugins", "manage_templates", "manage_api"} permissions List of permissions separrated by commas.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $role = [
                        "token" => "SYSTEM_TOKEN", // system token, can be acquired from system settings.
                        "name" => "New Role",
                        "permissions" => "disallow_api,manage_users,manage_roles"
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/system/create/role");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $role);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    role = {
                        "token": systemToken,
                        "name": "New Role",
                        "permissions": "disallow_api,manage_users,manage_roles"
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/system/create/role", params = role)
                      
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
                   "message": "Role has been created!",
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

                if(!in_array("create_role", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

                if(!isset($request["name"], $request["permissions"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->length($request["name"]))
                    response(400, "Role name is too short!");

                $permissions = explode(",", $request["permissions"]);

                if(!is_array($permissions))
                    response(400, "Invalid Parameters!");

                if(empty($permissions))
                    response(400, "Invalid Parameters!");

                foreach($permissions as $permission):
                    if(!in_array($permission, [
                        "disallow_sms",
                        "disallow_ussd",
                        "disallow_notifications",
                        "disallow_devices",
                        "disallow_wa_chats",
                        "disallow_wa_accounts",
                        "disallow_contacts",
                        "disallow_groups",
                        "disallow_keys",
                        "disallow_webhooks",
                        "disallow_actions",
                        "disallow_templates",
                        "disallow_extensions",
                        "disallow_redeem",
                        "disallow_subscribe",
                        "disallow_topup",
                        "disallow_withdraw",
                        "disallow_convert",
                        "disallow_api",
                        "manage_users",
                        "manage_roles",
                        "manage_packages",
                        "manage_vouchers",
                        "manage_subscriptions",
                        "manage_transactions",
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
                        response(400, "Invalid Parameters!");
                    endif;
                endforeach;

                $filtered = [
                    "name" => $request["name"],
                    "permissions" => implode(",", $permissions)
                ];

                if($this->system->create("roles", $filtered)):
                    response(200, "Role has been created!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "package":
                /**
                 * @api {post} /create/package Create Package
                 * @apiName Create Package
                 * @apiDescription Create a new package.
                 * @apiGroup Packages
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {String} name Name of the package.
                 * @apiParam {Number} price Price of the package, cannot be less than 1. This is based on system currency.
                 * @apiParam {Number=1,2} hidden Hide the package from homepage and modal. 1 for yes and 2 for no.
                 * @apiParam {Number=1,2} footermark Include the footer mark in sms/chats. 1 for yes and 2 for no.
                 * @apiParam {Number} send_limit SMS send limit every cycle. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} receive_limit SMS receive limit every cycle. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} ussd_limit USSD limit every cycle. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} notification_limit Notification limit every cycle. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} contact_limit Maximum number of contacts that can be saved. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} device_limit Maximum number of devices that can be linked. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} key_limit Maximum number of API keys that can be created. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} webhook_limit Maximum number of webhooks that can be created. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} action_limit Maximum number of actions that can be created. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} scheduled_limit Maximum number of scheduled sms/chats that can be created. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} wa_send_limit WhatsApp send limit every cycle. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} wa_receive_limit WhatsAPp send limit every cycle. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} wa_account_limit Maximum number of WhatsApp accounts that can be linked. Use <strong>0</strong> for unlimited.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $package = [
                        "token" => "SYSTEM_TOKEN", // system token, can be acquired from system settings.
                        "name" => "Unlimited",
                        "price" => 30,
                        "hidden" => 2,
                        "footermark" => 1,
                        "send_limit" => 0,
                        "receive_limit" => 0,
                        "ussd_limit" => 100,
                        "notification_limit" => 0,
                        "contact_limit" => 100,
                        "device_limit" => 5,
                        "key_limit" => 5,
                        "webhook_limit" => 10,
                        "action_limit" => 5,
                        "scheduled_limit" => 100,
                        "wa_send_limit" => 0,
                        "wa_receive_limit" => 0,
                        "wa_account_limit" => 5
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/system/create/package");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $package);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    package = {
                        "token": systemToken,
                        "name": "Unlimited",
                        "price": 30,
                        "hidden": 2,
                        "footermark": 1,
                        "send_limit": 0,
                        "receive_limit": 0,
                        "ussd_limit": 100,
                        "notification_limit": 0,
                        "contact_limit": 100,
                        "device_limit": 5,
                        "key_limit": 5,
                        "webhook_limit": 10,
                        "action_limit": 5,
                        "scheduled_limit": 100,
                        "wa_send_limit": 0,
                        "wa_receive_limit": 0,
                        "wa_account_limit": 5
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/system/create/package", params = package)
                      
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
                   "message": "Package has been created!",
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

                if(!in_array("create_package", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

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
                        response(400, "Invalid Parameters!");
                    endif;

                    if(!in_array($column, ["name"])):
                        if(!$this->sanitize->isInt($request[$column])):
                            response(400, "Invalid Parameters!");
                        endif;
                    endif;
                endforeach;

                if(!$this->sanitize->length($request["name"]))
                    response(400, "Name is too short!");

                if($request["price"] < 1)
                    response(400, "Package price cannot be less than 1!");

                $request["footermark"] = $request["footermark"] < 2 ? 1 : 2;
                $request["hidden"] = $request["hidden"] < 2 ? 1 : 2;

                $filtered = [];

                foreach($columns as $column):
                    $filtered[$column] = $request[$column];
                endforeach;

                if($this->system->create("packages", $filtered)):
                    response(200, "Package has been created!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "voucher":
                /**
                 * @api {post} /create/voucher Create Voucher
                 * @apiName Create Voucher
                 * @apiDescription Create new voucher/s.
                 * @apiGroup Vouchers
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token
                 * @apiParam {String} name Name of the voucher/s.
                 * @apiParam {Number} count Number of vouchers to create.
                 * @apiParam {Number} duration Duration of subscription in months.
                 * @apiParam {Number} package Package ID, this can be obtained in <strong>/get/packages</strong>
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $voucher = [
                        "token" => "SYSTEM_TOKEN", // system token, can be acquired from system settings.
                        "name" => "Amazing Voucher",
                        "count" => 10,
                        "duration" => 3,
                        "package" => 3
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/system/create/voucher");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $voucher);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    voucher = {
                        "token": systemToken,
                        "name": "Amazing Voucher",
                        "count": 10,
                        "duration": 3,
                        "package": 3
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/system/create/voucher", params = voucher)
                      
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
                   "message": "Voucher has been created!",
                   "data": [
                        {
                            "code": "314a0fdf80bb61247304b8b1e6f023ab",
                            "name": "Amazing Voucher",
                            "package": 3,
                            "duration": 1
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

                if(!in_array("create_voucher", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

                if(!isset($request["name"], $request["count"], $request["duration"], $request["package"]))
                    response(400, "Invalid Parameters!");

                if($request["count"] < 1 || $request["count"] > 1000)
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["count"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["duration"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["package"]))
                    response(400, "Invalid Parameters!");

                if($request["duration"] < 1)
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->length($request["name"]))
                    response(400, "Name is too short!");

                if($request["package"] < 2)
                    response(400, "Invalid Parameters!");

                if($this->system->checkPackage($request["package"]) < 1)
                    response(400, "Invalid Parameters!");

                $voucherArray = [];

                if($request["count"] < 2):
                    $filtered = [
                        "code" => md5(uniqid(time(), true)),
                        "name" => $request["name"],
                        "package" => (int) $request["package"],
                        "duration" => (int) $request["duration"]
                    ];

                    $voucherArray[] = $filtered;

                    if($this->system->create("vouchers", $filtered)):
                        response(200, "Voucher has been created!", $voucherArray);
                    else:
                        response(500, "Something went wrong!");
                    endif;
                else:
                    for($i = 1; $i <= $request["count"]; $i++):
                        $filtered = [
                            "code" => md5(uniqid(time() . "_{$i}", true)),
                            "name" => $request["name"] . " #{$i}",
                            "package" => (int) $request["package"],
                            "duration" => (int) $request["duration"]
                        ];

                        $voucherArray[] = $filtered;

                        $this->system->create("vouchers", $filtered);
                    endfor;

                    response(200, "Vouchers has been created!", $voucherArray);
                endif;

                break;
            case "subscription":
                /**
                 * @api {post} /create/voucher Create Subscription
                 * @apiName Create Subscription
                 * @apiDescription Create a new subscription.
                 * @apiGroup Subscriptions
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token
                 * @apiParam {Number} user User ID.
                 * @apiParam {Number} package Package ID, this can be obtained in <strong>/get/packages</strong>
                 * @apiParam {Number} duration Duration of subscription in months.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $subscription = [
                        "token" => "SYSTEM_TOKEN", // system token, can be acquired from system settings.
                        "user" => 3,
                        "package" => 3,
                        "duration" => 3
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/system/create/subscription");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $subscription);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    subscription = {
                        "token": systemToken,
                        "user": 3,
                        "package": 3,
                        "duration": 3
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/system/create/subscription", params = subscription)
                      
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
                   "message": "Subscription has been created!",
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

                if(!in_array("create_subscription", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

                if(!isset($request["user"], $request["package"], $request["duration"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["user"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["package"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["duration"]))
                    response(400, "Invalid Parameters!");

                if($this->system->checkUser($request["user"]) < 1)
                    response(400, "Invalid Parameters!");

                if($this->system->checkPackage($request["package"]) < 1)
                    response(400, "Invalid Parameters!");

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

                if($this->system->delete($filtered["uid"], false, "subscriptions")):
                    if($this->system->create("subscriptions", $filtered)):
                        response(200, "Subscription has been created!");
                    else:
                        response(500, "Something went wrong!");
                    endif;
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            default:
                response(500, "Invalid API Endpoint!");
        endswitch;
	}

    public function update()
    {
        $this->header->allow();

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        if(in_array("0", explode(",", system_admin_api)))
            response(403, "Admin API is disabled!");

        $request = $this->sanitize->array($_REQUEST);
        $type = $this->sanitize->string($this->url->segment(4));

        if(!isset($request["token"]))
            response(400, "Invalid Parameters!");

        if($request["token"] != system_token)
            response(401, "Invalid system token supplied!");

        switch($type):
            case "user":
                /**
                 * @api {post} /update/user Update Account
                 * @apiName Update Account
                 * @apiDescription Update a user account.
                 * @apiGroup Users
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} id User ID, you can obtain user ID from <strong>/get/users</strong>
                 * @apiParam {String} [name] Full name of the user.
                 * @apiParam {String} [email] Email address for the user account.
                 * @apiParam {String} [password] Password for the user account.
                 * @apiParam {Number} [credits] Credits for the user account. Whole number only.
                 * @apiParam {String} [timezone] Timezone for the user account, here's the list of valid timezones you can use: <a href="https://www.w3schools.com/php/php_ref_timezones.asp">Click Here</a>.
                 * @apiParam {String} [country] Country code, it should satisfy ISO Alpha-2 format.
                 * @apiParam {Number} [language] Language ID, you can get the a language ID from <strong>/get/languages</strong>
                 * @apiParam {Number} [role] Role ID, you can get a role ID from <strong>/get/roles</strong>
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $user = [
                        "token" => "SYSTEM_TOKEN", // system token, can be acquired from system settings.
                        "id" => 4,
                        "name" => "Ryan Huggins",
                        "email" => "mail@domain.com",
                        "password" => "123456",
                        "credits" => 100,
                        "timezone" => "Asia/Manila",
                        "country" => "PH",
                        "language" => 1
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/system/update/user");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $user);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    user = {
                        "token": systemToken,
                        "id": 4,
                        "name": "Ryan Huggins",
                        "email": "mail@domain.com",
                        "password": "123456",
                        "credits": 100,
                        "timezone": "Asia/Manila",
                        "country": "PH",
                        "language": 1
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/system/update/user", params = user)
                      
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
                   "message": "User has been updated!",
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

                if(!in_array("edit_user", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                $filtered = [];

                if(isset($request["role"])):
                    if(!$this->sanitize->isInt($request["role"]))
                        response(400, "Invalid Parameters!");

                    if($this->system->checkRole($request["role"]) < 1)
                        response(400, "Invalid Parameters!");

                    $filtered["role"] = $request["role"];
                endif;

                if(isset($request["language"])):
                    if(!$this->sanitize->isInt($request["language"]))
                        response(400, "Invalid Parameters!");

                    if($this->system->checkLanguage($request["language"]) < 1)
                        response(400, "Invalid Parameters!");

                    $filtered["language"] = $request["language"];
                endif;

                if(isset($request["name"])):
                    if(!$this->sanitize->length($request["name"]))
                        response(400, "Name is too short!");

                    $filtered["name"] = $request["name"];
                endif;

                if(isset($request["email"])):
                    if(!$this->sanitize->isEmail($request["email"]))
                        response(400, "Invalid email address!");

                    if($this->system->checkEmail($request["email"]) > 0)
                        response(403, "Email was already used!");

                    $filtered["email"] = $this->sanitize->email($request["email"]);
                endif;

                if(isset($request["credits"]) && $this->sanitize->isInt($request["credits"])):
                    $filtered["credits"] = $request["credits"];
                endif;

                if(isset($request["timezone"])):
                    if(!in_array(strtolower($request["timezone"]), $this->timezones->generate()))
                        response(400, "Invalid Parameters!");

                    $filtered["timezone"] = strtolower($request["timezone"]);
                endif;

                if(isset($request["country"])):
                    if(!array_key_exists(strtoupper($request["country"]), \CountryCodes::get("alpha2", "country")))
                        response(400, "Invalid Parameters!");

                    $filtered["country"] = strtoupper($request["country"]);
                endif;

                if(isset($request["password"])):
                    if(!$this->sanitize->length($request["password"], 5))
                        response(400, "Password is too short!");
                    else
                        $filtered["password"] = password_hash($request["password"], PASSWORD_DEFAULT);
                endif;

                if(!empty($filtered)):
                    if($this->system->update($request["id"], false, "users", $filtered)):
                        response(200, "User has been updated!");
                    else:
                        response(500, "Something went wrong!");
                    endif;
                else:
                    response(400, "Invalid Parameters!");
                endif;

                break;
            case "role":
                /**
                 * @api {post} /update/role Update Role
                 * @apiName Update Role
                 * @apiDescription Update a user role.
                 * @apiGroup Roles
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token
                 * @apiParam {Number} id Role ID, you can obtain role ID from <strong>/get/roles</strong>
                 * @apiParam {String} [name] Name of the user role.
                 * @apiParam {String="disallow_sms", "disallow_ussd", "disallow_notifications", "disallow_devices", "disallow_wa_chats", "disallow_wa_accounts", "disallow_contacts", "disallow_groups", "disallow_keys", "disallow_webhooks", "disallow_actions", "disallow_templates", "disallow_extensions", "disallow_redeem", "disallow_subscribe", "disallow_topup", "disallow_withdraw", "disallow_convert", "disallow_api", "manage_users", "manage_roles", "manage_packages", "manage_vouchers", "manage_subscriptions", "manage_transactions", "manage_widgets", "manage_pages", "manage_marketing", "manage_languages", "manage_gateways", "manage_shorteners", "manage_plugins", "manage_templates", "manage_api"} [permissions] List of permissions separrated by commas.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $role = [
                        "token" => "SYSTEM_TOKEN", // system token, can be acquired from system settings.
                        "id" => 2,
                        "name" => "New Role Name",
                        "permissions" => "disallow_api,manage_users"
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/system/update/role");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $role);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    role = {
                        "token": systemToken,
                        "id": 2,
                        "name": "New Role Name",
                        "permissions": "disallow_api,manage_users"
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/system/update/role", params = role)
                      
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
                   "message": "Role has been updated!",
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

                if(!in_array("edit_role", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if($request["id"] < 2)
                    response(500, "Default role cannot be edited!");

                if($this->system->checkRole($request["id"]) < 1)
                    response(400, "Invalid Parameters!");

                $filtered = [];

                if(isset($request["permissions"])):
                    $permissions = explode(",", $request["permissions"]);

                    if(!is_array($permissions))
                        response(400, "Invalid Parameters!");

                    if(empty($permissions))
                        response(400, "Invalid Parameters!");

                     foreach($permissions as $permission):
                        if(!in_array($permission, [
                            "disallow_sms",
                            "disallow_ussd",
                            "disallow_notifications",
                            "disallow_devices",
                            "disallow_wa_chats",
                            "disallow_wa_accounts",
                            "disallow_contacts",
                            "disallow_groups",
                            "disallow_keys",
                            "disallow_webhooks",
                            "disallow_actions",
                            "disallow_templates",
                            "disallow_extensions",
                            "disallow_redeem",
                            "disallow_subscribe",
                            "disallow_topup",
                            "disallow_withdraw",
                            "disallow_convert",
                            "disallow_api",
                            "manage_users",
                            "manage_roles",
                            "manage_packages",
                            "manage_vouchers",
                            "manage_subscriptions",
                            "manage_transactions",
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
                            response(400, "Invalid Parameters!");
                        endif;
                    endforeach;

                    $filtered["permissions"] = implode(",", $permissions);
                endif;

                if(isset($request["name"])):
                    if(!$this->sanitize->length($request["name"]))
                        response(400, "Name is too short!");

                    $filtered["name"] = $request["name"];
                endif;

                if($this->system->update($request["id"], false, "roles", $filtered)):
                    try {
                        $this->echo->_cache = $this->cache;
                        $this->echo->_guzzle = $this->guzzle;

                        $echoToken = $this->echo->token();
                    } catch(Exception $e){
                        response(500, "System configuration error!");
                    }

                    if($echoToken):
                        $this->echo->notify("role", $request["id"]);
                    endif;

                    response(200, "Role has been updated!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "package":
                /**
                 * @api {post} /update/package Update Package
                 * @apiName Update Package
                 * @apiDescription Update an existing package.
                 * @apiGroup Packages
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} id Package ID, you can obtain package ID from <strong>/get/packages</strong>
                 * @apiParam {String} [name] Name of the package.
                 * @apiParam {Number} [price] Price of the package, cannot be less than 1. This is based on system currency.
                 * @apiParam {Number=1,2} [hidden] Hide the package from homepage and modal. 1 for yes and 2 for no.
                 * @apiParam {Number=1,2} [footermark] Include the footer mark in sms/chats. 1 for yes and 2 for no.
                 * @apiParam {Number} [send_limit] SMS send limit every cycle. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} [receive_limit] SMS receive limit every cycle. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} [ussd_limit] USSD limit every cycle. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} [notification_limit] Notification limit every cycle. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} [contact_limit] Maximum number of contacts that can be saved. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} [device_limit] Maximum number of devices that can be linked. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} [key_limit] Maximum number of API keys that can be created. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} [webhook_limit] Maximum number of webhooks that can be created. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} [action_limit] Maximum number of actions that can be created. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} [scheduled_limit] Maximum number of scheduled sms/chats that can be created. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} [wa_send_limit] WhatsApp send limit every cycle. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} [wa_receive_limit] WhatsAPp send limit every cycle. Use <strong>0</strong> for unlimited.
                 * @apiParam {Number} [wa_account_limit] Maximum number of WhatsApp accounts that can be linked. Use <strong>0</strong> for unlimited.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $package = [
                        "token" => "SYSTEM_TOKEN", // system token, can be acquired from system settings.
                        "id" => 7,
                        "name" => "Unlimited",
                        "price" => 30,
                        "hidden" => 2,
                        "footermark" => 1,
                        "send_limit" => 0,
                        "receive_limit" => 0,
                        "ussd_limit" => 100,
                        "notification_limit" => 0,
                        "contact_limit" => 100,
                        "device_limit" => 5,
                        "key_limit" => 5,
                        "webhook_limit" => 10,
                        "action_limit" => 5,
                        "scheduled_limit" => 100,
                        "wa_send_limit" => 0,
                        "wa_receive_limit" => 0,
                        "wa_account_limit" => 5
                    ];

                    $cURL = curl_init("http://127.0.0.1/zender/system/update/package");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($cURL, CURLOPT_POSTFIELDS, $package);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    package = {
                        "token": systemToken,
                        "id": 5,
                        "name": "Unlimited",
                        "price": 30,
                        "hidden": 2,
                        "footermark": 1,
                        "send_limit": 0,
                        "receive_limit": 0,
                        "ussd_limit": 100,
                        "notification_limit": 0,
                        "contact_limit": 100,
                        "device_limit": 5,
                        "key_limit": 5,
                        "webhook_limit": 10,
                        "action_limit": 5,
                        "scheduled_limit": 100,
                        "wa_send_limit": 0,
                        "wa_receive_limit": 0,
                        "wa_account_limit": 5
                    }

                    r = requests.post(url = "http://127.0.0.1/zender/system/update/package", params = package)
                      
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
                   "message": "Package has been updated!",
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

                if(!in_array("edit_package", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                $filtered = [];
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
                    if(isset($request[$column])):
                        if(!in_array($column, ["name"])):
                            if(!$this->sanitize->isInt($request[$column])):
                                response(400, "Invalid Parameters!");
                            endif;

                            if($column == "price"):
                                if($request["price"] < 1)
                                    response(400, "Package price cannot be less than 1!");
                            endif;
                        else:
                            if(!$this->sanitize->length($request["name"]))
                                response(400, "Name is too short!");
                        endif;

                        $filtered[$column] = $request[$column];
                    endif;
                endforeach;

                if(isset($request["footermark"]))
                    $request["footermark"] = $request["footermark"] < 2 ? 1 : 2;

                if(isset($request["hidden"])):
                    if($request["id"] < 2):
                        $request["hidden"] = 2;
                    else:
                        $request["hidden"] = $request["hidden"] < 2 ? 1 : 2;
                    endif;
                endif;

                if(!empty($filtered)):
                    if($this->system->update($request["id"], false, "packages", $filtered)):
                        response(200, "Package has been updated!");
                    else:
                        response(500, "Something went wrong!");
                    endif;
                else:
                    response(400, "Invalid Parameters!");
                endif;

                break;
            default:
                response(500, "Invalid API Endpoint!");
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

        if(in_array("0", explode(",", system_admin_api)))
            response(403, "Admin API is disabled!");

        $request = $this->sanitize->array($_REQUEST);
        $type = $this->sanitize->string($this->url->segment(4));

        if(!isset($request["token"]))
            response(400, "Invalid Parameters!");

        if($request["token"] != system_token)
            response(401, "Invalid system token supplied!");

        switch($type):
            case "user":
                /**
                 * @api {get} /delete/user Delete Account
                 * @apiName Delete Account
                 * @apiDescription Delete a user account.
                 * @apiGroup Users
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} id User ID, you can obtain a user ID from <strong>/get/users</strong>
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.
                    $userId = 4;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/delete/user?token={$systemToken}&id={$userId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"
                    userId = 6

                    r = requests.get(url = "http://127.0.0.1/zender/system/delete/user", params = {
                        "token": systemToken,
                        "id": userId
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
                   "message": "User has been deleted!",
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

                if(!in_array("delete_user", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($request["id"] < 2)
                    response(400, "Default admin account cannot be deleted!");

                if($this->system->delete(false, $request["id"], "users")):
                    response(200, "User has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "role":
                /**
                 * @api {get} /delete/role Delete Role
                 * @apiName Delete Role
                 * @apiDescription Delete a user role.
                 * @apiGroup Roles
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} id Role ID, you can obtain a role ID from <strong>/get/roles</strong>
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.
                    $roleId = 4;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/delete/role?token={$systemToken}&id={$roleId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"
                    roleId = 6

                    r = requests.get(url = "http://127.0.0.1/zender/system/delete/role", params = {
                        "token": systemToken,
                        "id": roleId
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
                   "message": "Role has been deleted!",
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

                if(!in_array("delete_role", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($request["id"] < 2)
                    response(400, "Default role cannot be deleted!");

                if($this->system->delete(false, $request["id"], "roles")):
                    response(200, "Role has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "package":
                /**
                 * @api {get} /delete/package Delete Package
                 * @apiName Delete Package
                 * @apiDescription Delete an existing package.
                 * @apiGroup Packages
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} id Package ID, you can obtain a package ID from <strong>/get/packages</strong>
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.
                    $packageId = 4;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/delete/package?token={$systemToken}&id={$packageId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"
                    packageId = 6

                    r = requests.get(url = "http://127.0.0.1/zender/system/delete/package", params = {
                        "token": systemToken,
                        "id": packageId
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
                   "message": "Package has been deleted!",
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

                if(!in_array("delete_package", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($request["id"] < 2)
                    response(400, "Default package cannot be deleted!");

                if($this->system->delete(false, $request["id"], "packages")):
                    response(200, "Package has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "voucher":
                /**
                 * @api {get} /delete/voucher Delete Voucher
                 * @apiName Delete Voucher
                 * @apiDescription Delete an unused voucher.
                 * @apiGroup Vouchers
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} id Voucher ID, you can obtain a voucher ID from <strong>/get/vouchers</strong>
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.
                    $voucherId = 4;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/delete/voucher?token={$systemToken}&id={$voucherId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"
                    voucherId = 6

                    r = requests.get(url = "http://127.0.0.1/zender/system/delete/voucher", params = {
                        "token": systemToken,
                        "id": voucherId
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
                   "message": "Voucher has been deleted!",
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

                if(!in_array("delete_voucher", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($this->system->delete(false, $request["id"], "vouchers")):
                    response(200, "Voucher has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "subscription":
                /**
                 * @api {get} /delete/subscription Delete Subscription
                 * @apiName Delete Subscription
                 * @apiDescription Delete an active subscription.
                 * @apiGroup Subscriptions
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} id Subscription ID, you can obtain a subscription ID from <strong>/get/subscriptions</strong>
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.
                    $subscriptionId = 4;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/delete/subscription?token={$systemToken}&id={$subscriptionId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"
                    subscriptionId = 6

                    r = requests.get(url = "http://127.0.0.1/zender/system/delete/subscription", params = {
                        "token": systemToken,
                        "id": subscriptionId
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
                   "message": "Subscription has been deleted!",
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

                if(!in_array("delete_subscription", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($this->system->delete(false, $request["id"], "subscriptions")):
                    response(200, "Subscription has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            case "transaction":
                /**
                 * @api {get} /delete/transaction Delete Transaction
                 * @apiName Delete Transaction
                 * @apiDescription Delete a transaction record.
                 * @apiGroup Transactions
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 * @apiParam {Number} id Transaction ID, you can obtain a transaction ID from <strong>/get/transactions</strong>
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.
                    $transactionId = 4;

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/delete/transaction?token={$systemToken}&id={$transactionId}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests
    
                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"
                    transactionId = 6

                    r = requests.get(url = "http://127.0.0.1/zender/system/delete/transaction", params = {
                        "token": systemToken,
                        "id": transactionId
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
                   "message": "Transaction has been deleted!",
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

                if(!in_array("delete_transaction", explode(",", system_admin_api)))
                    response(403, "This API endpoint is disabled!");

                if(!isset($request["id"]))
                    response(400, "Invalid Parameters!");

                if(!$this->sanitize->isInt($request["id"]))
                    response(400, "Invalid Parameters!");

                if($this->system->delete(false, $request["id"], "transactions")):
                    response(200, "Transaction has been deleted!");
                else:
                    response(500, "Something went wrong!");
                endif;

                break;
            default:
                response(500, "Invalid API Endpoint!");
        endswitch;
	}

    public function redeem()
    {
        $this->header->allow();

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        if(in_array("0", explode(",", system_admin_api)))
            response(403, "Admin API is disabled!");

        if(!in_array("redeem_voucher", explode(",", system_admin_api)))
            response(403, "This API endpoint is disabled!");

        $request = $this->sanitize->array($_REQUEST);

        /**
         * @api {get} /redeem Redeem Voucher
         * @apiName Redeem Voucher
         * @apiDescription Redeem an unused voucher.
         * @apiGroup Vouchers
         * @apiVersion 1.0.0
         *
         * @apiParam {String} token System token, can be acquired from system settings.
         * @apiParam {Number} user User ID, you can obtain a user ID from <strong>/get/users</strong>
         * @apiParam {String} code Voucher Code
         *
         * @apiExample {php} PHP Example
          <?php

            $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.
            $userId = 4;
            $voucherCode = "caf1fbc8191d3bdfe736f5b6a36011cf";

            $cURL = curl_init();
            curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/redeem?token={$systemToken}&user={$userId}&code={$voucherCode}");
            curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($cURL);
            curl_close($cURL);

            $result = json_decode($response, true);

            // do something with response
            print_r($result);
         * 
         * @apiExample {python} Python Example
            import requests

            # system token, can be acquired from system settings.
            systemToken = "SYSTEM_TOKEN"
            userId = 6
            voucherCode = "caf1fbc8191d3bdfe736f5b6a36011cf"

            r = requests.get(url = "http://127.0.0.1/zender/system/redeem", params = {
                "token": systemToken,
                "user": userId,
                "code": voucherCode
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
           "message": "Voucher has been redeemed!",
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

        if(!isset($request["token"], $request["user"], $request["code"]))
            response(400, "Invalid Parameters!");

        if($request["token"] != system_token)
            response(401, "Invalid system token supplied!");

        if(!$this->sanitize->isInt($request["user"]))
            response(400, "Invalid Parameters!");

        if($this->system->checkUser($request["user"]) < 1)
            response(400, "Invalid Parameters!");

        if($this->system->checkVoucher($request["code"]) > 0):
            $voucher = $this->system->getVoucher($request["code"]);
            $package = $this->system->getPackage($voucher["package"]);

            if($this->system->checkSubscription($request["user"]) > 0):
                $transaction = $this->system->create("transactions", [
                    "uid" => $request["user"],
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

                $subscription = $this->system->getSubscription(false, $request["user"]);

                $this->system->update($subscription["sid"], $request["user"], "subscriptions", $filtered);
            else:
                $transaction = $this->system->create("transactions", [
                    "uid" => $request["user"],
                    "pid" => $package["id"],
                    "type" => 3,
                    "price" => $package["price"],
                    "currency" => system_currency,
                    "duration" => $voucher["duration"],
                    "provider" => "Voucher"
                ]);

                $filtered = [
                    "uid" => $request["user"],
                    "pid" => $package["id"],
                    "tid" => $transaction
                ];

                $this->system->create("subscriptions", $filtered);
            endif;

            if($this->system->delete(false, $voucher["id"], "vouchers")):
                response(200, "Voucher has been redeemed!");
            else:
                response(500, "Something went wrong!");
            endif;
        else:
            response(403, "Invalid voucher code!");
        endif;
    }

    public function clear()
    {
        $this->header->allow();

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        if(in_array("0", explode(",", system_admin_api)))
            response(403, "Admin API is disabled!");

        if(!in_array("clear_cache", explode(",", system_admin_api)))
            response(403, "This API endpoint is disabled!");

        $request = $this->sanitize->array($_REQUEST);
        $type = $this->sanitize->string($this->url->segment(4));

        if(!isset($request["token"]))
            response(400, "Invalid Parameters!");

        if($request["token"] != system_token)
            response(401, "Invalid system token supplied!");

        switch($type):
            case "cache":
                /**
                 * @api {get} /clear/cache Clear Cache
                 * @apiName Clear Cache
                 * @apiDescription Clear system cache files.
                 * @apiGroup System
                 * @apiVersion 1.0.0
                 *
                 * @apiParam {String} token System token, can be acquired from system settings.
                 *
                 * @apiExample {php} PHP Example
                  <?php

                    $systemToken = "SYSTEM_TOKEN"; // system token, can be acquired from system settings.

                    $cURL = curl_init();
                    curl_setopt($cURL, CURLOPT_URL, "http://127.0.0.1/zender/system/clear/cache?token={$systemToken}");
                    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($cURL);
                    curl_close($cURL);

                    $result = json_decode($response, true);

                    // do something with response
                    print_r($result);
                 * 
                 * @apiExample {python} Python Example
                    import requests

                    # system token, can be acquired from system settings.
                    systemToken = "SYSTEM_TOKEN"

                    r = requests.get(url = "http://127.0.0.1/zender/clear/cache", params = {
                        "token": systemToken
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
                   "message": "System cache files has been cleared!",
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
                    response(500);
                }

                response(200, "System cache files has been cleared!");

                break;
            default:
                response(500, "Invalid API Endpoint!");
        endswitch;
    }
}