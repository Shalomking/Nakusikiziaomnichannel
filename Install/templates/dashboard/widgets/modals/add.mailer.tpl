<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-chrome la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>
                        {__("lang_and_dash_mail_line17")} <i class="la la-info-circle" title="{__("lang_and_dash_mail_line17_1")}"></i>
                    </label>
                    <input type="text" name="title" class="form-control" placeholder="{__("lang_and_dash_mail_line19")}">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_dash_mail_line24")} <i class="la la-info-circle" title="{__("lang_and_dash_mail_line24_1")}"></i>
                    </label>
                    <select name="users[]" class="form-control" data-live-search="true" zender-select-users multiple>
                        <option value="0" data-tokens="None {__("lang_form_select_multinone")}" selected>{__("lang_form_select_multinone")}</option>
                        {foreach $data.users as $user}
                        <option value="{$user.id}" data-tokens="{$user.name} {$user.email}">{$user.name} ({$user.email})</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_dash_mail_line36")} <i class="la la-info-circle" title="{__("lang_and_dash_mail_line36_1")}"></i>
                    </label>
                    <select name="roles[]" class="form-control" data-live-search="true" zender-select-roles multiple>
                        <option value="0" data-tokens="None {__("lang_form_select_multinone")}" selected>{__("lang_form_select_multinone")}</option>
                        {foreach $data.roles as $role}
                        <option value="{$role.id}" data-tokens="{$role.name}">{$role.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_and_dash_mail_line48")} <i class="la la-info-circle" title="{__("lang_and_dash_mail_line48_1")}"></i>
                    </label>
                    <div zender-codeflask><p>{__("lang_and_dash_mail_line50")}</p></div>
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