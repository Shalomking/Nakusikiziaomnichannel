<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-link la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group col-12">
                    <label>
                        {__("lang_and_short_line17")} <i class="la la-info-circle" title="{__("lang_and_short_line17_1")}"></i>
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="{__("lang_and_short_line19")}">
                </div>

                <div class="form-group col-12">
                    <label>
                        {__("lang_and_short_line24")} <i class="la la-info-circle" title="{__("lang_and_short_line24_1")}"></i>
                    </label>
                    <small class="text-danger">
                        {__("lang_and_short_line27")} <a href="https://github.com/titansys/zender-shorteners" target="_blank">{__("lang_form_btnall_visitlink")}</a> 
                    </small>
                    <input type="file" name="controller" class="form-control pb-5">
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