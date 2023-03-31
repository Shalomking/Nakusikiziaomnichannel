<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-list la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                {foreach $data.plugin.fields as $field}
                {if find("hr", $field@key)}
                <hr>
                <input type="hidden" name="{$field@key}" value="{$field}">
                {else}
                <div class="form-group col-12">
                    <label>{ucfirst($field@key)}</label>
                    {if in_array($field, ["true", "false"])}
                    <select name="{$field@key}" class="form-control">
                        <option value="true" {if $field eq "true"}selected{/if}>{__("lang_form_yes")}</option>
                        <option value="false" {if $field eq "false"}selected{/if}>{__("lang_form_no")}</option>
                    </select>
                    {else}
                    <input type="text" name="{$field@key}" class="form-control" placeholder="{ucfirst($field@key)}" value="{$field}">
                    {/if}
                </div>
                {/if}
                {/foreach}
            </div>
        </div>

        <div class="modal-footer">
            <input type="hidden" name="plugin_name" value="{$data.plugin.name}">
            <button type="submit" class="btn btn-lg btn-primary">
                <i class="la la-check-circle la-lg"></i> {__("lang_btn_submit")}
            </button>
        </div>
    </div>
</form>