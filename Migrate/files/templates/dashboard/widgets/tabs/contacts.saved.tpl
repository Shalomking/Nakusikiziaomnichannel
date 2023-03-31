<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-address-book"></i> {__("lang_dashboard_contacts_tabsavedtitle")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-danger" title="{__("lang_and_con_sav_6")}" zender-trash="contacts.saved">
                <i class="la la-stream la-lg"></i>
            </button>

            <button class="btn btn-lg btn-primary" title="{__("lang_and_con_sav_10")}" zender-toggle="zender.add.contact">
                <i class="la la-address-book la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_dashboard_btn_addcontact")}</span>
            </button>

            <button class="btn btn-lg btn-primary" title="{__("lang_and_con_sav_15")}" zender-toggle="zender.import.contacts">
                <i class="la la-upload la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_import_btn")}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>