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
                <div class="form-group col-md-12">
                    <label>
                        {__("lang_forms_whatsapp_groupsimportname")} <i class="la la-info-circle" title="{__("lang_forms_whatsapp_groupsimportnamehelp")}"></i>
                    </label>
                    <select name="account" class="form-control" data-live-search="true">
                        {foreach $data.accounts as $account}
                        <option value="{$account@key}" data-tokens="{$account.token}">{$account.name}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-layer-group la-lg"></i> {__("lang_forms_whatsapp_groupsimportbutton")}
            </button>
        </div>
    </div>
</form>