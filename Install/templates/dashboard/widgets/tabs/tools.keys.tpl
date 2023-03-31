<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-key"></i> {__("lang_dashboard_tools_tabkeystitle")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-primary" title="{__("lang_and_tool_key_6")}" zender-gototab="dashboard/docs" gototab-action="zender.docs.api">
                <i class="la la-book la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_and_tool_key_8")}</span>
            </button>

            <button class="btn btn-lg btn-primary" title="{__("lang_and_tool_key_11")}" zender-toggle="zender.add.apikey">
                <i class="la la-key la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_dashboard_btn_addkey")}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>