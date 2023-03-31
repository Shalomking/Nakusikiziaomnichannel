<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-bullhorn"></i> {__("lang_and_admin_mark_3")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-danger" title="{__("lang_and_admin_mark_6")}" zender-trash="administration.marketing">
                <i class="la la-stream la-lg"></i>
            </button>
            
            <button class="btn btn-lg btn-primary" title="{__("lang_dashboard_adminmarketing_pushbtndesc")}" zender-toggle="zender.add.push">
                <i class="la la-android la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_and_admin_mark_12")}</span>
            </button>

            <button class="btn btn-lg btn-primary" title="{__("lang_dashboard_adminmarketing_notifybtndesc")}" zender-toggle="zender.add.notify">
                <i class="la la-chrome la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_and_admin_mark_17")}</span>
            </button>

            <button class="btn btn-lg btn-primary" title="{__("lang_dashboard_adminmarketing_mailerbtndesc")}" zender-toggle="zender.add.mailer">
                <i class="la la-envelope la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_and_admin_mark_22")}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>