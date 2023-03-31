<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-users"></i> {__("lang_pages_wagroups_header")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-danger" title="{__("lang_and_what_rev_6")}" zender-trash="whatsapp.groups">
                <i class="la la-stream la-lg"></i>
            </button>
            
            <button class="btn btn-lg btn-primary" title="{__("lang_and_what_sent_10")}" zender-toggle="zender.whatsapp.groups">
                <i class="la la-layer-group la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_pages_wagroups_fetchbtn")}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>