<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-code-branch"></i> {__("lang_dashboard_tools_tabhookstitle")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-primary" title="{__("lang_and_tool_webhook_6")}" zender-gototab="dashboard/docs" gototab-action="zender.docs.webhooks">
                <i class="la la-book la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_and_tool_webhook_8")}</span>
            </button>
            
            <button class="btn btn-lg btn-primary" zender-toggle="zender.add.webhook" >
                <i class="la la-code-branch la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_dashboard_btn_addhook")}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>