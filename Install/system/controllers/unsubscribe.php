<?php

class Unsubscribe_Controller extends MVC_Controller
{
	public function index()
    {
        set_template("dashboard");

        $uid = $this->sanitize->string($this->url->segment(3));
        $phone = $this->sanitize->string($this->url->segment(4));

        if(!$this->smarty->templateExists(template . "/pages/misc.tpl"))
            $this->header->redirect(site_url);

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

        $this->cache->container("system.blocks");

        if($this->cache->empty()):
            $blocks = [];
            
            foreach($this->system->getBlocks() as $key => $value):
                $blocks[$key] = $this->smarty->fetch("string: {$this->sanitize->htmlDecode($value)}");
            endforeach;

            $this->cache->setArray($blocks);
        endif;

        set_blocks($this->cache->getAll());

        set_logged();

        if($this->system->checkUser($uid) < 1)
            $this->header->redirect(site_url);

        $userAccount = $this->system->getUser($uid);

        set_language($userAccount["language"], $userAccount["rtl"]);

        try {
            $number = $this->phone->parse($phone);

            $number->format(Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);

            if(!$number->isValidNumber())
                $this->header->redirect(site_url());

            if(!$number->getNumberType(Brick\PhoneNumber\PhoneNumberType::MOBILE))
                $this->header->redirect(site_url());

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

        $vars = [
            "title" => __("lang_unsubscribe_header_title"),
            "page" => "misc",
            "data" => [
                "title" => __("lang_unsubscribe_content_title"),
                "content" => <<<HTML
                <p>{$GLOBALS["__"]("lang_unsubscribe_content_desc")}</p>
                HTML
            ]
        ];

        $this->smarty->assign($vars);
        $this->smarty->display(template . "/header.tpl");
        $this->smarty->display(template . "/pages/misc.tpl");
        $this->smarty->display(template . "/footer.tpl");
    }
}