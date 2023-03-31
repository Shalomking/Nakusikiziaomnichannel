<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-stream la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-6">
                    <label>
                        {__("lang_form_name")} <i class="la la-info-circle" title="{__("lang_and_edit_page_line17")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="eg. {__("lang_form_widgetname_placeholder")}" value="{$data.page.name}">
                </div>

                <div class="form-group col-3">
                    <label>
                        {__("lang_form_pageroles")} <i class="la la-info-circle" title="{__("lang_and_edit_page_line24")}"></i>
                    </label>
                    <select name="roles[]" class="form-control" multiple>
                        {foreach $data.roles as $role}
                        <option value="{$role@key}" {if in_array($role@key, explode(",", $data.page.roles))}selected{/if}>{$role.name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-group col-3">
                    <label>
                        {__("lang_form_require_login")} <i class="la la-info-circle" title="{__("lang_and_edit_page_line35")}"></i>
                    </label>
                    <select name="logged" class="form-control">
                        <option value="1" {if $data.page.logged < 2}selected{/if}>{__("lang_form_yes")}</option>
                        <option value="2" {if $data.page.logged > 1}selected{/if}>{__("lang_form_no")}</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_widgetcontent")} <i class="la la-info-circle" title="{__("langa_and_edit_page_line45")}"></i>
                    </label>
                    <div zender-codeflask>{$data.page.content}</div>
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