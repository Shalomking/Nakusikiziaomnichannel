<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-language"></i> {__("lang_dashboard_admin_tablanguagestitle")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-danger" title="{__("lang_and_admin_lang_6")}" zender-reorder>
                <i class="la la-sort-alpha-up la-lg"></i>
            </button>
            
            <button class="btn btn-lg btn-primary" zender-toggle="zender.add.language">
                <i class="la la-language la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_dashboard_btn_addlanguage")}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>