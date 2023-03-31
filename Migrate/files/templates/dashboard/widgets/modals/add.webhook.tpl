<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-key la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_name")} <i class="la la-info-circle" title="{__("lang_and_web_line17")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="eg. {__("lang_form_webhookname_placeholder")}">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_web_line24")} <i class="la la-info-circle" title="{__("lang_and_web_line24_1")}"></i>
                    </label>
                    <select name="events[]" class="form-control" multiple>
                        <option value="sms" selected>{__("lang_and_web_line27")}</option>
                        <option value="whatsapp">{__("lang_and_web_line28")}</option>
                        <option value="ussd">{__("lang_and_web_line29")}</option>
                        <option value="notifications">{__("lang_and_web_line30")}</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_webhookurl")} <i class="la la-info-circle" title="{__("lang_and_web_line36")}"></i>
                    </label>
                    <input type="text" name="url" class="form-control" placeholder="{__("lang_and_web_line38")}">
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