<?php

class Default_Controller extends MVC_Controller
{
    public function index()
    {
        set_template("default");
        
        $page = ($this->sanitize->string($this->url->segment(2)) ?: "default");

        $this->cache->container("system.languages");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getLanguages());
        endif;

        foreach($this->cache->getAll() as $language):
            if($page == $language["iso"]):
                $this->session->set("language", $language["id"]);
                $this->header->redirect(site_url);
            endif;
        endforeach;

        if(!$this->smarty->templateExists(template . "/pages/{$page}.tpl"))
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

        set_logged($this->session->get("logged"));

        set_language(logged_language, $this->system->getLanguageRtl(logged_language));

        $this->cache->container("system.blocks");

        if($this->cache->empty()):
            $blocks = [];
            
            foreach($this->system->getBlocks() as $key => $value):
                $blocks[$key] = $this->smarty->fetch("string: {$this->sanitize->htmlDecode($value)}");
            endforeach;

            $this->cache->setArray($blocks);
        endif;

        set_blocks($this->cache->getAll());

        switch($page):
            case "pages":
                $id = $this->url->segment(3);
                $slug = $this->url->segment(4);

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

                if($this->session->has("logged"))
                    $this->header->redirect(site_url("dashboard/pages/{$content["id"]}/{$content["slug"]}"));

                if($content["logged"] < 2)
                    $this->header->redirect(site_url);

                if($content["slug"] != $slug)
                    $this->header->redirect(site_url);

                $vars = [
                    "title" => $content["name"],
                    "data" => [
                        "page" => $content,
                        "content" => $this->smarty->fetch("string: {$this->sanitize->htmlDecode($content["content"])}")
                    ]
                ];

                break;
            default:
                if(system_homepage > 1)
                    $this->header->redirect(site_url("dashboard"));

                $vars = [
                    "title" => __("lang_landing_title_default"),
                    "data" => [
                        "packages" => $this->widget->getPackages(true, system_freemodel < 2 ? true : false)
                    ]
                ];

        endswitch;

        $vars["page"] = $page;

        $this->smarty->assign($vars);
        $this->smarty->display(template . "/header.tpl");
        $this->smarty->display(template . "/pages/{$page}.tpl");
        $this->smarty->display(template . "/footer.tpl");
    }
}
