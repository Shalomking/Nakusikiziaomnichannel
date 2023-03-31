<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-telegram la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <div class="form-group">
                        <label>
                            {__("lang_forms_campagin_formname")} <i class="la la-info-circle" title="{__("lang_forms_campagin_formhelp")}"></i>
                        </label>
                        <input type="text" name="campaign" class="form-control" placeholder="{__("lang_forms_campaign_formplaceholder")}">
                    </div>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_form_excelfile")} <i class="la la-info-circle" title="{__("lang_and_whatex17")}"></i>
                    </label>
                    <small class="text-danger">
                        {__("lang_and_whatex20")} <a href="{site_url("uploads/system/wa_sample.xlsx")}" target="_blank">{__("lang_and_whatex20_1")}</a>
                    </small>
                    <input type="file" name="excel" class="form-control pb-5">
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_and_whatex27")} <i class="la la-info-circle" title="{__("lang_and_whatex27_1")}"></i>
                    </label>
                    <select name="shortener" class="form-control">
                        <option value="0" selected>{__("lang_and_whatex30")}</option>
                        {foreach $data.shorteners as $shortener}
                        <option value="{$shortener@key}">{$shortener.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        {__("lang_and_whatex39")} <i class="la la-info-circle" title="{__("lang_and_whatex39_1")}"></i>
                    </label>
                    <select name="account" class="form-control" data-live-search="true">
                        {foreach $data.accounts as $account}
                        <option value="{$account@key}" data-tokens="{$account.token}">{$account.name}</option>
                        {/foreach}
                    </select>

                    <label>
                        {__("lang_form_template")} <i class="la la-info-circle" title="{__("lang_and_whatex48")}"></i>
                    </label>
                    <select class="form-control" data-live-search="true" zender-select-template>
                        <option value="none" data-tokens="no none 0" selected>{__("lang_form_none")}</option>
                        {foreach $data.templates as $template}
                        <option value="{$template@key}" data-tokens="{$template.token}" data-format="{$template.format}">{$template.name}</option>
                        {/foreach}
                    </select>

                    <label>
                        {__("lang_forms_whatsapp_messagetype")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_messagetypehelp")}"></i>
                    </label>
                    <select name="message_type" class="form-control" zender-wa-type>
                        <option value="text" selected>{__("lang_forms_whatsapp_typetext")}</option>
                        <option value="media">{__("lang_forms_whatsapp_typemedia")}</option>
                        <option value="document">{__("lang_forms_whatsapp_typedoc")}</option>
                        <option value="button">{__("lang_forms_whatsapp_typebutton")}</option>
                        <option value="list">{__("lang_forms_whatsapp_typelist")}</option>
                        <option value="template">{__("lang_forms_whatsapp_typetemplate")}</option>
                    </select>

                    <div zender-wa-type-media>
                        <label>
                            {__("lang_forms_whatsapp_mediafile")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_mediafilehelp")}"></i>
                        </label>
                        <input type="file" name="media_file" class="form-control pb-5">
                    </div>

                    <div zender-wa-type-document>
                        <label>
                            {__("lang_forms_whatsapp_docfile")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_docfilehelp")}"></i>
                        </label>
                        <input type="file" name="doc_file" class="form-control pb-5">
                    </div>

                    <div zender-wa-type-button>
                        <label>
                            {__("lang_forms_whatsapp_imagefile")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_imagefilehelp")}"></i>
                        </label>
                        <input type="file" name="button_image" class="form-control pb-5">
                    </div>

                    <div class="mt-3" zender-wa-type-button>
                        <label>
                            {__("lang_forms_whatsapp_buttons")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_buttonshelp")}"></i>
                        </label>
                        <input type="text" name="button_1" class="form-control" placeholder="{__("lang_forms_whatsapp_buttonsplace1")}">
                        <input type="text" name="button_2" class="form-control mt-2" placeholder="{__("lang_forms_whatsapp_buttonsplace2")}">
                        <input type="text" name="button_3" class="form-control mt-2" placeholder="{__("lang_forms_whatsapp_buttonsplace3")}">
                    </div>

                    <div zender-wa-type-list>
                        <label>
                            {__("lang_forms_whatsapp_listtitle")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_listtitlehelp")}"></i>
                        </label>
                        <input type="text" name="list_title" class="form-control" placeholder="{__("lang_forms_whatsapp_listplacetitle")}">
                    </div>

                    <div class="mt-3" zender-wa-type-list>
                        <label>
                            {__("lang_forms_whatsapp_listmenutitle")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_listmenutitlehelp")}"></i>
                        </label>
                        <input type="text" name="list_menu" class="form-control" placeholder="{__("lang_forms_whatsapp_listmenutitleplace")}">
                    </div>

                    <div class="mt-3" zender-wa-type-list>
                        <label>
                            {__("lang_forms_whatsapp_footermessage")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_footermessagehelp")}"></i>
                        </label>
                        <input type="text" name="list_footer" class="form-control" placeholder="{__("lang_forms_whatsapp_footermessageplace")}">
                    </div>

                    <div class="mt-1" zender-wa-type-template>
                        <label>
                            {__("lang_form_wa_templatetype_knownissue")}
                        </label>
                        <p>{__("lang_form_wa_templatetype_knownissuedesc")}</p>

                        <label>
                            {__("lang_forms_whatsapp_footermessage")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_footermessagehelp")}"></i>
                        </label>
                        <input type="text" name="template_footer" class="form-control" placeholder="{__("lang_forms_whatsapp_footermessageplace")}">
                    </div>
                </div>

                <div class="form-group col-md-8">
                    <label>
                        {__("lang_form_message")} <small class="text-muted">(<span zender-counter-view></span>{if system_message_max < 1} {__("lang_form_messagecounterchars")}{/if})</small>
                    </label>
                    <button class="btn btn-primary btn-sm" zender-action="translate">
                        <i class="la la-language" title="{__("lang_and_whatex63")}"></i>
                    </button>
                    <textarea name="message" class="form-control mb-3" rows="7" placeholder="{__("lang_form_message_placeholder")}" zender-counter></textarea>

                    <label>
                        {__("lang_and_whatex68")} <i class="la la-info-circle" title="{__("lang_and_whatex68_1")}"></i>
                    </label>
                    <p>
                        <small>{__("lang_and_sms_bulk_135")}</small> <code>{$data.spintax_sample.main}</code>
                    </p>
                    <p>
                        <small>{___(__("lang_form_literal_spintaxdesc2"), ["<strong>{$data.spintax_sample.good}</strong>", "<strong>{$data.spintax_sample.bad}</strong>"])}</small>
                    </p>

                    <label>
                        {__("lang_form_shortcodes")} <i class="la la-info-circle" title="{__("lang_and_whatex80")}"></i>
                    </label>
                    {literal}
                    <p>
                        <code><strong>{{phone}}</strong>, <strong>{{date.now}}</strong>, <strong>{{date.time}}</strong>, <strong>{{unsubscribe.command}}</strong>, <strong>{{unsubscribe.link}}</strong>, <strong>{{custom.YourVariable}}</strong></code>
                    </p>
                    {/literal}
                </div>

                <div class="form-group col-md-12">
                    <div zender-wa-type-list>
                        <label>
                            {__("lang_forms_whatsapp_listformat")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_listformathelp")}"></i>
                        </label>
                        <div id="zender-jsoneditor-list" style="width: 100%; height: 450px;"></div>
                    </div>

                    <div zender-wa-type-template>
                        <label>
                            {__("lang_forms_whatsapp_templateformat")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_templateformathelp")}"></i>
                        </label>
                        <div id="zender-jsoneditor-template" style="width: 100%; height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-telegram la-lg"></i> {__("lang_btn_send")}
            </button>
        </div>
    </div>
</form>