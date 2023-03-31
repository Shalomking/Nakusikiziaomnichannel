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
        window.template = "{template}";
        window.site_url = "{site_url}";
        window.tawk_id = "{system_tawk_id}";
        window.titansys_echo = "{titansys_echo}";
        window.alertsound = {if logged_alertsound < 2}true{else}false{/if};
        window.message_max = {system_message_max};
        window.color_primary = "{system_theme_background}";
        window.alert_position = "{if language_rtl}topRight{else}topLeft{/if}";
        window.overlap_alert_position = "{if language_rtl}bottomRight{else}bottomLeft{/if}";
        window.consent_position = "{if language_rtl}bottom-right{else}bottom-left{/if}";
        window.recaptcha_status = {if system_recaptcha < 2}{if empty(system_recaptcha_key) || empty(system_recaptcha_secret)}false{else}true{/if}{else}false{/if};

        var lang_datatable_processing = "{__("lang_datatable_processing")}",
            lang_datatable_length = "{__("lang_datatable_length")}",
            lang_datatable_info = "{__("lang_datatable_info")}",
            lang_datatable_empty = "{__("lang_datatable_empty")}",
            lang_datatable_filtered = "{__("lang_datatable_filtered")}",
            lang_datatable_loading = "{__("lang_datatable_loading")}",
            lang_datatable_zero = "{__("lang_datatable_zero")}",
            lang_datatable_null = "{__("lang_datatable_null")}",
            lang_datatable_first = "{__("lang_datatable_first")}",
            lang_datatable_prev = "{__("lang_datatable_prev")}",
            lang_datatable_next = "{__("lang_datatable_next")}",
            lang_datatable_last = "{__("lang_datatable_last")}",
            lang_response_went_wrong = "{__("lang_response_went_wrong")}",
            lang_delete_title = "{__("lang_delete_title")}",
            lang_delete_tagline = "{__("lang_delete_tagline")}",
            lang_validate_cannotemp = "{__("lang_validate_cannotemp")}",
            lang_alert_attention = "{__("lang_alert_attention")}",
            lang_date_today = "{__("lang_date_today")}",
            lang_date_yesterday = "{__("lang_date_yesterday")}",
            lang_date_7days = "{__("lang_date_7days")}",
            lang_date_30days = "{__("lang_date_30days")}",
            lang_date_month = "{__("lang_date_month")}",
            lang_date_lmonth = "{__("lang_date_lmonth")}",
            lang_date_custom = "{__("lang_date_custom")}",
            lang_copy_data = "{__("lang_copy_data")}",
            lang_unknown_action_method = "{__("lang_unknown_action_method")}",
            lang_btn_confirm = "{__("lang_btn_confirm")}",
            lang_btn_cancel = "{__("lang_btn_cancel")}",
            lang_suspend_user_title = "{__("lang_suspend_user_title")}",
            lang_suspend_user_desc = "{__("lang_suspend_user_desc")}",
            lang_unsuspend_user_title = "{__("lang_unsuspend_user_title")}",
            lang_unsuspend_user_desc = "{__("lang_unsuspend_user_desc")}",
            lang_response_session_false = "{__("lang_response_session_false")}",
            lang_require_name = "{__("lang_require_name")}",
            lang_require_email = "{__("lang_require_email")}",
            lang_require_password = "{__("lang_require_password")}",
            lang_require_cpassword = "{__("lang_require_cpassword")}",
            lang_validate_cannotemp = "{__("lang_validate_cannotemp")}",
            lang_alert_attention = "{__("lang_alert_attention")}",
            lang_cookieconsent_message = "{__("lang_cookieconsent_message")}",
            lang_cookieconsent_link = "{__("lang_cookieconsent_link")}",
            lang_cookieconsent_dismiss = "{__("lang_cookieconsent_dismiss")}",
            lang_pendingdelete_title = "{__("lang_pendingdelete_title")}",
            lang_pendingdelete_desc = "{__("lang_pendingdelete_desc")}",
            lang_js_loader_pleasewait = "{__("lang_js_loader_pleasewait")}",
            lang_js_userstatus_online = "{__("lang_js_userstatus_online")}",
            lang_js_userstatus_offline = "{__("lang_js_userstatus_offline")}",
            lang_js_whatsapp_linksuccess = "{__("lang_js_whatsapp_linksuccess")}",
            lang_js_whatsapp_linkfailed = "{__("lang_js_whatsapp_linkfailed")}",
            lang_js_whatsapp_linkexist = "{__("lang_js_whatsapp_linkexist")}",
            lang_js_whatsapp_loaderprocessing = "{__("lang_js_whatsapp_loaderprocessing")}",
            lang_js_clearpending_title = "{__("lang_js_clearpending_title")}",
            lang_js_clearpending_desc = "{__("lang_js_clearpending_desc")}",
            lang_js_clearpending_processing = "{__("lang_js_clearpending_processing")}",
            lang_js_authenticate_processing = "{__("lang_js_authenticate_processing")}",
            lang_js_actiontrigger_title = "{__("lang_js_actiontrigger_title")}",
            lang_js_actiontrigger_typetitle = "{__("lang_js_actiontrigger_typetitle")}",
            lang_js_actiontrigger_typesms = "{__("lang_js_actiontrigger_typesms")}",
            lang_js_actiontrigger_typewa = "{__("lang_js_actiontrigger_typewa")}",
            lang_js_actiontrigger_typeussd = "{__("lang_js_actiontrigger_typeussd")}",
            lang_js_actiontrigger_typenoti = "{__("lang_js_actiontrigger_typenoti")}",
            lang_js_actiontrigger_loader = "{__("lang_js_actiontrigger_loader")}",
            lang_js_actiontranslator_title = "{__("lang_js_actiontranslator_title")}",
            lang_js_actiontranslator_from = "{__("lang_js_actiontranslator_from")}",
            lang_js_actiontranslator_to = "{__("lang_js_actiontranslator_to")}",
            lang_js_actiontranslator_loader = "{__("lang_js_actiontranslator_loader")}",
            lang_js_actionregen_title = "{__("lang_js_actionregen_title")}",
            lang_js_actionregen_desc = "{__("lang_js_actionregen_desc")}",
            lang_js_actionregen_loader = "{__("lang_js_actionregen_loader")}",
            lang_js_actionclear_title = "{__("lang_js_actionclear_title")}",
            lang_js_actionclear_desc = "{__("lang_js_actionclear_desc")}",
            lang_js_processing_dataloader = "{__("lang_js_processing_dataloader")}",
            lang_js_build_title = "{__("lang_js_build_title")}",
            lang_js_build_desc = "{__("lang_js_build_desc")}",
            lang_js_build_btnbuild = "{__("lang_js_build_btnbuild")}",
            lang_js_reorder_title = "{__("lang_js_reorder_title")}",
            lang_js_reorder_desc = "{__("lang_js_reorder_desc")}",
            lang_js_deletetrash_title = "{__("lang_js_deletetrash_title")}",
            lang_js_deletetrash_desc = "{__("lang_js_deletetrash_desc")}",
            lang_js_tablesexport_copy = "{__("lang_js_tablesexport_copy")}",
            lang_js_tablesexport_excel = "{__("lang_js_tablesexport_excel")}",
            lang_js_tablesexport_pdf = "{__("lang_js_tablesexport_pdf")}",
            lang_requests_build_submitrequest = "{__("lang_requests_build_submitrequest")}",
            lang_requests_reorder_loader = "{__("lang_requests_reorder_loader")}",
            lang_requests_deleteitem_loader = "{__("lang_requests_deleteitem_loader")}",
            lang_js_calendarall_option = "{__("lang_js_calendarall_option")}",
            lang_js_calendarall_apply = "{__("lang_js_calendarall_apply")}",
            lang_js_calendarall_cancel = "{__("lang_js_calendarall_cancel")}",
            lang_js_calendarall_from = "{__("lang_js_calendarall_from")}",
            lang_js_calendarall_to = "{__("lang_js_calendarall_to")}",
            lang_js_calendarall_week = "{__("lang_js_calendarall_week")}",
            lang_js_calendarall_daysu = "{__("lang_js_calendarall_daysu")}",
            lang_js_calendarall_daymo = "{__("lang_js_calendarall_daymo")}",
            lang_js_calendarall_daytu = "{__("lang_js_calendarall_daytu")}",
            lang_js_calendarall_daywe = "{__("lang_js_calendarall_daywe")}",
            lang_js_calendarall_dayth = "{__("lang_js_calendarall_dayth")}",
            lang_js_calendarall_dayfr = "{__("lang_js_calendarall_dayfr")}",
            lang_js_calendarall_daysa = "{__("lang_js_calendarall_daysa")}",
            lang_js_calendarall_monjan = "{__("lang_js_calendarall_monjan")}",
            lang_js_calendarall_monfeb = "{__("lang_js_calendarall_monfeb")}",
            lang_js_calendarall_monmar = "{__("lang_js_calendarall_monmar")}",
            lang_js_calendarall_monapr = "{__("lang_js_calendarall_monapr")}",
            lang_js_calendarall_monmay = "{__("lang_js_calendarall_monmay")}",
            lang_js_calendarall_monjun = "{__("lang_js_calendarall_monjun")}",
            lang_js_calendarall_monjul = "{__("lang_js_calendarall_monjul")}",
            lang_js_calendarall_monaug = "{__("lang_js_calendarall_monaug")}",
            lang_js_calendarall_monsep = "{__("lang_js_calendarall_monsep")}",
            lang_js_calendarall_monoct = "{__("lang_js_calendarall_monoct")}",
            lang_js_calendarall_monnov = "{__("lang_js_calendarall_monnov")}",
            lang_js_calendarall_mondec = "{__("lang_js_calendarall_mondec")}",
            lang_response_whatsapp_creatingqr = "{__("lang_response_whatsapp_creatingqr")}",
            lang_datatable_addedtoclipboard = "{__("lang_datatable_addedtoclipboard")}",
            lang_datatable_addedtoclipboardkeys = "{__("lang_datatable_addedtoclipboardkeys")}",
            lang_datatable_addedtoclipboardmulti = "{__("lang_datatable_addedtoclipboardmulti")}",
            lang_datatable_addedtoclipboardsingle = "{__("lang_datatable_addedtoclipboardsingle")}",
            lang_js_response_titanechorefreshdesc = "{__("lang_js_response_titanechorefreshdesc")}",
            lang_campaign_popup_titleresume = "{__("lang_campaign_popup_titleresume")}",
            lang_campaign_popup_titlestop = "{__("lang_campaign_popup_titlestop")}",
            lang_campaign_popup_descsmsresume = "{__("lang_campaign_popup_descsmsresume")}",
            lang_campaign_popup_descsmsstop = "{__("lang_campaign_popup_descsmsstop")}",
            lang_campaign_popup_descwaresume = "{__("lang_campaign_popup_descwaresume")}",
            lang_campaign_popup_descwastop = "{__("lang_campaign_popup_descwastop")}",
            lang_plugin_mpg_popuppayumoneymobilenumber = "{__("lang_plugin_mpg_popuppayumoneymobilenumber")}";

        fetchInject([
            "{_assets("js/template.dashboard.js")}", {plugin_scripts}
        ], fetchInject([
            "{_assets("js/custom.run.js", true)}", {plugin_styles}
        ], fetchInject([
            "{_assets("js/functions.js")}",
            "{_assets("js/libs/he.min.js")}",
            "{_assets("js/libs/mfb.min.js")}",
            "{_assets("js/libs/card.min.js")}",
            "{_assets("js/libs/daterangepicker.min.js")}"
        ], fetchInject([
            "{_assets("js/libs/pjax.min.js")}",
            "{_assets("js/libs/moment.min.js")}",
            "{_assets("js/libs/waves.min.js")}",
            "{_assets("js/libs/nprogress.min.js")}",
            "{_assets("js/libs/jquery.loading.min.js")}",
            "{_assets("js/libs/qrcode.min.js")}",
            "{_assets("js/libs/scrollto.min.js")}",
            "{_assets("js/libs/izitoast.min.js")}",
            "{_assets("js/libs/codeflask.min.js")}",
            "{_assets("js/libs/clipboard.min.js")}",
            "{_assets("js/libs/autocomplete.min.js")}",
            "{_assets("js/libs/iframeResizer.min.js")}",
            "{_assets("js/libs/bootstrap-select.min.js")}",
            "{_assets("js/libs/jquery.word-and-character-counter.min.js")}",
            "{_assets("js/libs/cookieconsent.min.js")}",
            "{_assets("js/libs/datatables.min.js")}",
            "{_assets("js/libs/tooltipster.bundle.min.js")}",
            "{_assets("js/libs/alertsound.min.js")}",
            "{_assets("js/libs/jsoneditor.min.js")}",
            "{_assets("js/libs/echo.min.js")}"
        ], fetchInject([
            "{_assets("js/libs/bootstrap.min.js")}"
        ], fetchInject([
            "{_assets("js/libs/jquery.min.js")}",
            "{_assets("css/custom.css", true)}",
            "{if language_rtl}{_assets("css/libs/mfb.rtl.min.css")}{else}{_assets("css/libs/mfb.min.css")}{/if}",
            "{_assets("css/libs/waves.min.css")}",
            "{_assets("css/libs/animate.min.css")}",
            "{_assets("css/libs/izitoast.min.css")}",
            "{_assets("css/libs/datatables.min.css")}",
            "{_assets("css/libs/daterangepicker.min.css")}",
            "{_assets("css/libs/bootstrap-select.min.css")}",
            "{_assets("css/libs/cookieconsent.min.css")}",
            "{_assets("css/libs/tooltipster.bundle.min.css")}",
            "{_assets("css/libs/jsoneditor.min.css")}"
        ]))))));
    </script>
</body>

</html>