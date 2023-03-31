<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-robot"></i> {__("lang_and_tool_act_3")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-primary" title="{__("lang_and_tool_act_6")}" zender-gototab="dashboard/docs" gototab-action="zender.docs.actions">
                <i class="la la-book la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_and_tool_act_8")}</span>
            </button>
            
            <button class="btn btn-lg btn-primary" title="{__("lang_and_tool_act_11")}" zender-toggle="zender.add.hook">
                <i class="la la-wave-square la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_and_tool_act_13")}</span>
            </button>

            <button class="btn btn-lg btn-primary" title="{__("lang_and_tool_act_16")}" zender-toggle="zender.add.autoreply">
                <i class="la la-reply la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_and_tool_act_18")}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>