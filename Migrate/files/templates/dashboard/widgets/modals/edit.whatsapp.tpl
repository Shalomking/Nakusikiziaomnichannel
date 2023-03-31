<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-android la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_editwhatsapp_receiveoptiontitle")} <i class="la la-info-circle la-lg" title="{__("lang_form_editwhatsapp_receiveoptiondesc")}"></i>
                    </label>
                    <select name="receive_chats" class="form-control">
                        <option value="1" {if $data.account.receive_chats < 2}selected{/if}>{__("lang_form_enable")}</option>
                        <option value="2" {if $data.account.receive_chats > 1}selected{/if}>{__("lang_form_disable")}</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_editwhatsapp_randominterval")} <i class="la la-info-circle la-lg" title="{__("lang_form_editwhatsapp_randomintervalhelp")}"></i>
                    </label>
                    <select name="random_send" class="form-control">
                        <option value="1" {if $data.account.random_send < 2}selected{/if}>{__("lang_form_enable")}</option>
                        <option value="2" {if $data.account.random_send > 1}selected{/if}>{__("lang_form_disable")}</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_editwhatsapp_randommin")} <i class="la la-info-circle la-lg" title="{__("lang_form_editwhatsapp_randomminhelp")}"></i>
                    </label>

                    <input type="number" name="random_min" class="form-control" placeholder="{__("lang_and_edit_dev_line41")}" value="{$data.account.random_min}">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_editwhatsapp_randommax")} <i class="la la-info-circle la-lg" title="{__("lang_form_editwhatsapp_randommaxhelp")}"></i>
                    </label>

                    <input type="number" name="random_max" class="form-control" placeholder="{__("lang_and_edit_dev_line49")}" value="{$data.account.random_max}">
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