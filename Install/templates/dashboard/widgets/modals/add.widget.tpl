<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-puzzle-piece la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-4">
                    <label>
                        {__("lang_form_name")} <i class="la la-info-circle" title="{__("lang_and_add_widget_line17")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="eg. {__("lang_form_widgetname_placeholder")}">
                </div>

                <div class="form-group col-4">
                    <label>
                        {__("lang_form_widgeticon")} <i class="la la-info-circle" title="{__("lang_and_add_widget_line24")}"></i>
                    </label>
                    <input type="text" name="icon" class="form-control" placeholder="{__("lang_and_add_widget_line26")}">
                </div>

                <div class="form-group col-4">
                    <label>
                        {__("lang_form_widgettype")} <i class="la la-info-circle" title="{__("lang_and_add_widget_line31")}"></i>
                    </label>
                    <select name="type" class="form-control">
                        <option value="1" selected>{__("lang_and_add_widget_line34")}</option>
                        <option value="2">{__("lang_and_add_widget_line35")}</option>
                    </select>
                </div>

                <div class="form-group col-6">
                    <label>
                        {__("lang_form_widgetsize")} <i class="la la-info-circle" title="{__("lang_and_add_widget_line41")}"></i>
                    </label>
                    <select name="size" class="form-control">
                        <option value="sm" selected>{__("lang_form_widgetsmall")}</option>
                        <option value="md">{__("lang_form_widgetmedium")}</option>
                        <option value="lg">{__("lang_form_widgetlarge")}</option>
                        <option value="xl">{__("lang_form_widgetxlarge")}</option>
                    </select>
                </div>

                <div class="form-group col-6">
                    <label>
                        {__("lang_form_widgetposition")} <i class="la la-info-circle" title="{__("lang_and_add_widget_line53")}"></i>
                    </label>
                    <select name="position" class="form-control">
                        <option value="center" selected>{__("lang_form_widgetcenter")}</option>
                        <option value="left">{__("lang_form_widgetleft")}</option>
                        <option value="right">{__("lang_form_widgetright")}</option>
                    </select>
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_form_widgetcontent")} <i class="la la-info-circle" title="{__("lang_and_add_widget_line64")}"></i>
                    </label>                    
                    <div zender-codeflask><p>{__("lang_and_add_widget_line66")}</p></div>
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