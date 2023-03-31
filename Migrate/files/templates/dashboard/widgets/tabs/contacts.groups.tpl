<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-list"></i> {__("lang_dashboard_contacts_tabgroupstitle")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-danger" title="{__("lang_and_con_grp_6")}" zender-trash="contacts.groups">
                <i class="la la-stream la-lg"></i>
            </button>

            <button class="btn btn-lg btn-primary" title="{__("lang_and_con_grp_10")}" zender-toggle="zender.add.group">
                <i class="la la-list la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_dashboard_btn_addgroup")}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>