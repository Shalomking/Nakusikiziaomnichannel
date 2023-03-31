    {include "./modules/footer.block.tpl"}

    <div zender-preloader>
        <div class="loadingio loadingio-spinner-ripple-c4xwekkbyc9">
            <div class="ldio-k6xrhuhg6o">
                <div></div>
                <div></div>
            </div>
        </div>
    </div>

    <script src="{_assets("js/libs/fetch.min.js")}"></script>
    <script defer>
        window.site_url = "{site_url}";
        window.template = "{template}";
        window.tawk_id = "{system_tawk_id}";
        window.titansys_echo = "{titansys_echo}";
        window.alertsound = {if logged_alertsound < 2}true{else}false{/if};
        window.color_primary = "{system_theme_background}";
        window.alert_position = "{if language_rtl}topRight{else}topLeft{/if}";
        window.overlap_alert_position = "{if language_rtl}bottomRight{else}bottomLeft{/if}";
        window.consent_position = "{if language_rtl}bottom-right{else}bottom-left{/if}";
        
        var lang_response_went_wrong = "{__("lang_response_went_wrong")}",
            lang_alert_attention = "{__("lang_alert_attention")}",
            lang_cookieconsent_message = "{__("lang_cookieconsent_message")}",
            lang_cookieconsent_link = "{__("lang_cookieconsent_link")}",
            lang_cookieconsent_dismiss = "{__("lang_cookieconsent_dismiss")}",
            lang_js_processing_dataloader = "{__("lang_js_processing_dataloader")}",
            lang_js_loader_pleasewait = "{__("lang_js_loader_pleasewait")}",
            lang_response_session_false = "{__("lang_response_session_false")}";

        fetchInject([
            "{_assets("js/template.default.js")}", {plugin_scripts}
        ], fetchInject([
            "{_assets("js/custom.run.js", true)}", {plugin_styles}
        ], fetchInject([
            "{_assets("js/functions.js")}",
        ], fetchInject([
            "{_assets("js/libs/mfb.min.js")}",
            "{assets("js/template.js")}"
        ], fetchInject([
            "{_assets("js/libs/pjax.min.js")}",
            "{_assets("js/libs/aos.min.js")}",
            "{_assets("js/libs/waves.min.js")}",
            "{_assets("js/libs/nprogress.min.js")}",
            "{_assets("js/libs/jquery.loading.min.js")}",
            "{_assets("js/libs/scrollto.min.js")}",
            "{_assets("js/libs/izitoast.min.js")}",
            "{_assets("js/libs/cookieconsent.min.js")}",
            "{_assets("js/libs/bootstrap-select.min.js")}",
            "{_assets("js/libs/iframeResizer.min.js")}",
            "{_assets("js/libs/tooltipster.bundle.min.js")}",
            "{_assets("js/libs/alertsound.min.js")}",
            "{_assets("js/libs/echo.min.js")}"
        ], fetchInject([
            "{assets("js/libs/bootstrap.min.js")}",
            "{assets("js/plugins.js")}"
        ], fetchInject([
            "{_assets("js/libs/jquery.min.js")}",
            "{_assets("css/custom.css", true)}",
            "{_assets("css/libs/aos.min.css")}",
            "{_assets("css/libs/waves.min.css")}",
            "{_assets("css/libs/izitoast.min.css")}",
            "{_assets("css/libs/bootstrap-select.min.css")}",
            "{_assets("css/libs/cookieconsent.min.css")}",
            "{_assets("css/libs/tooltipster.bundle.min.css")}",
            "{if language_rtl}{_assets("css/libs/mfb.rtl.min.css")}{else}{_assets("css/libs/mfb.min.css")}{/if}"
        ])))))));
    </script>
</body>

</html>