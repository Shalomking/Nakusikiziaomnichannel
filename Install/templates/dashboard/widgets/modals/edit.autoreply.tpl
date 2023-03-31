<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-reply la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>
                        {__("lang_form_name")} <i class="la la-info-circle" title="{__("lang_and_edit_auto_line17")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="{__("lang_and_edit_auto_line19")}" value="{$data.autoreply.name}">
                </div>

                <div class="form-group col-md-6">
                    <label>
                        {__("lang_and_edit_auto_line24")} <i class="la la-info-circle" title="{__("lang_and_edit_auto_line24_1")}"></i>
                    </label>
                    <select name="source" class="form-control">
                        <option value="1" {if $data.autoreply.source < 2}selected{/if}>SMS</option>
                        <option value="2" {if $data.autoreply.source > 1}selected{/if}>WhatsApp</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_autoreply_keywords")} <i class="la la-info-circle" title="{__("lang_and_edit_auto_line34")}"></i>
                    </label>
                    <textarea name="keywords" class="form-control" placeholder="{__("lang_and_edit_auto_line36")}">{$data.autoreply.keywords}</textarea>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_autoreply_message")} <i class="la la-info-circle" title="{__("lang_and_edit_auto_line41")}"></i>
                    </label>
                    <textarea name="message" class="form-control" rows="5" placeholder="{__("lang_and_edit_auto_line43")}">{$data.autoreply.message}</textarea>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_shortcodes")} <i class="la la-info-circle" title="{__("lang_and_edit_auto_line48")}"></i>
                    </label>
                    {literal}
                    <p>
                        <code>
                            <strong>{{phone}}</strong>, <strong>{{message}}</strong>, <strong>{{date.now}}</strong>, <strong>{{date.time}}</strong>
                        </code>
                    </p>
                    {/literal}
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-lg btn-primary btn-block">
                <i class="la la-check-circle la-lg"></i> {__("lang_btn_submit")}
            </button>
        </div>
    </div>
</form>