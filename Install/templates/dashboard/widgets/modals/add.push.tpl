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
                        {__("lang_and_push_line17")} <i class="la la-info-circle" title="{__("lang_and_push_line17_1")}"></i>
                    </label>
                    <input type="text" name="title" class="form-control" placeholder="{__("lang_and_push_line19")}">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_push_line24")} <i class="la la-info-circle" title="{__("lang_and_push_line24_1")}"></i>
                    </label>
                    <small class="text-mute">
                        {__("lang_and_push_line27")}
                    </small>
                    <input type="file" name="image" class="form-control pb-5">
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_and_push_line34")} <i class="la la-info-circle" title="{__("lang_and_push_line34_1")}"></i>
                    </label>
                    <textarea name="message" class="form-control" rows="5" placeholder="{__("lang_form_message_placeholder")}"></textarea>
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_push_line41")} <i class="la la-info-circle" title="{__("lang_and_push_line41_1")}"></i>
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
                        {__("lang_and_push_line53")} <i class="la la-info-circle" title="{__("lang_and_push_line53_1")}"></i>
                    </label>
                    <select name="roles[]" class="form-control" data-live-search="true" zender-select-roles multiple>
                        <option value="0" data-tokens="None {__("lang_form_select_multinone")}" selected>{__("lang_form_select_multinone")}</option>
                        {foreach $data.roles as $role}
                        <option value="{$role.id}" data-tokens="{$role.name}">{$role.name}</option>
                        {/foreach}
                    </select>
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