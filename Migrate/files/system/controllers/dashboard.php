<?php

class Dashboard_Controller extends MVC_Controller
{
    public function index()
    {
        if(!$this->session->has("logged"))
            $this->header->redirect(site_url("dashboard/authenticate/login"));
        else
            set_template("dashboard");

        $page = ($this->sanitize->string($this->url->segment(3)) ?: "default");

        if(!$this->smarty->templateExists(template . "/pages/{$page}.tpl"))
            $this->header->redirect(site_url("dashboard"));

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

        set_language(logged_language, logged_rtl);

        $this->cache->container("system.blocks");

        if($this->cache->empty()):
            $blocks = [];
            
            foreach($this->system->getBlocks() as $key => $value):
                $blocks[$key] = $this->smarty->fetch("string: {$this->sanitize->htmlDecode($value)}");
            endforeach;

            $this->cache->setArray($blocks);
        endif;

        set_blocks($this->cache->getAll());

        switch($page){
             case "pages":
                $id = $this->url->segment(4);
                $slug = $this->url->segment(5);

                if(!$this->sanitize->isInt($id))
                    $this->header->redirect(site_url);

                if(empty($slug))
                    $this->header->redirect(site_url);

                if($this->system->checkPage($id) < 1)
                    $this->header->redirect(site_url);

                $this->cache->container("system.pages");

                if(!$this->cache->has($id)):
                    $this->cache->set($id, $this->system->getPage($id));
                endif;

                $content = $this->cache->get($id);

                if($content["slug"] != $slug)
                    $this->header->redirect(site_url);

                $roles = explode(",", $content["roles"]);

                if(!in_array(1, $roles)):
                    if(!in_array(logged_role, $roles))
                        $this->header->redirect(site_url("dashboard"));
                endif;

                $vars = [
                    "title" => $content["name"],
                    "data" => [
                        "page" => $content,
                        "content" => $this->smarty->fetch("string: {$this->sanitize->htmlDecode($content["content"])}")
                    ]
                ];

                break;
            case "sms":
                $partner = $this->system->getPartnership(logged_id);

                $vars = [
                    "title" => __("lang_dashpages_sms_headertitle"),
                    "partner" => $partner ? ($partner < 2 ? true : false) : false
                ];

                break;
            case "whatsapp":
                $vars = [
                    "title" => __("lang_dashboard_title_whatsapp")
                ];

                break;
            case "android":
                $vars = [
                    "title" => __("lang_dashboard_title_android")
                ];

                break;
            case "contacts":
                $vars = [
                    "title" => __("lang_dashboard_title_contacts")
                ];

                break;
            case "tools": 
                $vars = [
                    "title" => __("lang_dashboard_title_tools")
                ];
                
                break;
            case "rates": 
                $vars = [
                    "title" => __("lang_dashboard_title_gatewayrates"),
                    "data" => [
                        "gateways" => $this->system->getGateways()
                    ]
                ];
                
                break;
            case "docs": 
                $vars = [
                    "title" => __("lang_dashboard_title_docs")
                ];
                
                break;
            case "administration": 
                if(!is_admin)
                    $this->header->redirect(site_url("dashboard"));

                $this->wa->_guzzle = $this->guzzle;

                $vars = [
                    "title" => __("lang_dashboard_title_admin"),
                    "data" => [
                        "whatsapp" => $this->wa->check(),
                        "gateway" => $this->file->exists("uploads/builder/" . strtolower(system_package_name . ".apk"))
                    ]
                ];

                break;
            default:
                $this->cache->container("user." . logged_hash, true);

                if(!$this->cache->has("ratio")):
                    $success = $this->system->getSentSuccessCount(logged_id);
                    $failed = $this->system->getSentFailedCount(logged_id);

                    $this->cache->set("ratio", [
                        "success" => $success > 0 ? abs(round(($success / ($success + $failed)) * 100, 2)) : 0,
                        "failed" => $failed > 0 ? abs(round(($failed / ($success + $failed)) * 100, 2)) : 0
                    ], 86400);
                endif;

                $subscription = set_subscription(
                    $this->system->checkSubscription(logged_id), 
                    $this->system->getSubscription(false, logged_id), 
                    $this->system->getSubscription(false, false, true)
                );

                $vars = [
                    "title" => __("lang_dashboard_title_default"),
                    "data" => [
                        "package" => $subscription,
                        "ratio" => $this->cache->get("ratio"),
                        "count" => [
                            "devices" => $this->system->countDevices(logged_id),
                            "accounts" => $this->system->countWaAccounts(logged_id)
                        ],
                        "balance" => $this->system->getBalance(logged_id),
                        "partner" => $this->system->getPartnership(logged_id)
                    ]
                ];
        }

        $vars["page"] = $page;

        $this->smarty->assign($vars);
        $this->smarty->display(template . "/header.tpl");
        $this->smarty->display(template . "/pages/{$page}.tpl");
        $this->smarty->display(template . "/footer.tpl");
    }

    public function authenticate()
    {
        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        if($this->session->has("logged"))
            $this->header->redirect(system_auth_redirect < 2 ? site_url : site_url("dashboard"));
        else
            set_template("dashboard");

        $page = ($this->sanitize->string($this->url->segment(4)) ?: "login");

        if(!$this->smarty->templateExists(template . "/pages/{$page}.tpl"))
            $this->header->redirect(site_url("dashboard"));

        set_logged($this->session->get("logged"));

        set_language(logged_language, logged_rtl);

        $this->cache->container("system.plugins");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getPlugins());
        endif;

        set_plugins($this->cache->getAll());

        $this->cache->container("system.blocks");

        if($this->cache->empty()):
            foreach($this->system->getBlocks() as $key => $value):
                $blocks[$key] = $this->smarty->fetch("string: {$this->sanitize->htmlDecode($value)}");
            endforeach;
            $this->cache->setArray($blocks);
        endif;

        set_blocks($this->cache->getAll());

        switch($page){
            case "register": 
                if(system_registrations > 1)
                    $this->header->redirect(site_url("dashboard/authenticate/login"));

                $confirmId = $this->sanitize->string($this->url->segment(5));

                if(!empty($confirmId)):
                    $this->cache->container("register.confirm", true);

                    if($this->cache->has($confirmId)):
                        if($this->system->update($this->cache->get($confirmId), false, "users", [
                            "confirmed" => 1
                        ])):
                            $this->cache->delete($confirmId);
                            
                            $confirmBool = true;
                        else:
                            $confirmBool = false;
                        endif;
                    else:
                        $confirmBool = false;
                    endif;
                else:
                    $confirmBool = false;
                endif;

                $vars = [
                    "title" => __("lang_dashboard_title_register"),
                    "data" => [
                        "timezones" => $this->timezones->generate(),
                        "countries" => \CountryCodes::get("alpha2", "country"),
                        "confirm" => $confirmBool
                    ]
                ];

                break;
            case "forgot": 
                $vars = [
                    "title" => __("lang_dashboard_title_forgot")
                ];

                break;
            default:
                $vars = [
                    "title" => __("lang_dashboard_title_login")
                ];

                break;
        }

        $vars["page"] = $page;

        $this->smarty->assign($vars);
        $this->smarty->display(template . "/header.tpl");
        $this->smarty->display(template . "/pages/{$page}.tpl");
        $this->smarty->display(template . "/footer.tpl");
    }

    public function filemanager()
    {
        if(!$this->session->has("logged"))
            response(401);

        $this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        if(!is_admin)
            response(401);

        $this->session->set("filemanager", [
            "logged" => "admin"
        ]);

        if($this->file->exists("system/plugins/filemanager/manager.php")):
            require "system/plugins/filemanager/manager.php";
        else:
            response(401);
        endif;
    }
}