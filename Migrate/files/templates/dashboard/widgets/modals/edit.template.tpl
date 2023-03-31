<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-wrench la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>
                        {__("lang_form_name")} <i class="la la-info-circle" title="{__("lang_and_edit_temp_line17")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="eg. {__("lang_form_templatename_placeholder")}" value="{$data.template.name}">
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_templateformat")} <i class="la la-info-circle" title="{__("lang_and_edit_temp_line24")}"></i>
                    </label>
                    <textarea name="format" class="form-control" rows="5" placeholder="{__("lang_form_templateformat_placeholder")}">{$data.template.format}</textarea>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_and_sms_bulk_131")} <i class="la la-info-circle" title="{__("lang_and_sms_bulk_131_1")}"></i>
                    </label>
                    <p>
                        <small>{__("lang_and_sms_bulk_135")}</small> <code>Tom is a {literal}<strong>{good|bad}</strong>{/literal} cat</code>
                    </p>
                    <p>
                        <small>{___(__("lang_form_literal_spintaxdesc2"), ["<strong>good</strong>", "<strong>bad</strong>"])}</small>
                    </p>

                    <label>
                        {__("lang_form_shortcodes")} <i class="la la-info-circle" title="{__("lang_and_edit_temp_line43")}"></i>
                    </label>
                    {literal}
                    <p>
                        <code><strong>{{contact.name}}</strong>, <strong>{{contact.number}}</strong>, <strong>{{group.name}}</strong>, <strong>{{date.now}}</strong>, <strong>{{date.time}}</strong>, <strong>{{unsubscribe.command}}</strong>, <strong>{{unsubscribe.link}}</strong></code>
                    </p>
                    {/literal}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-check-circle la-lg"></i> {__("lang_btn_submit")}
            </button>
        </div>
    </div>
</form>