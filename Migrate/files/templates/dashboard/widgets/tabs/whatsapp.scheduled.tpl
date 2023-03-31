<div class="card">
    <div class="card-header">
        <h4 class="card-title"><i class="la la-clock"></i> {__("lang_messages_scheduled_title")}</h4>

        <div class="float-right">
            <button class="btn btn-lg btn-danger" title="{__("lang_and_what_sched_6")}" zender-trash="whatsapp.scheduled">
                <i class="la la-stream la-lg"></i>
            </button>
            
            <button class="btn btn-lg btn-primary" zender-toggle="zender.add.whatsapp.scheduled">
                <i class="la la-calendar la-lg"></i>
                <span class="d-none d-sm-inline">{__("lang_and_what_sched_12")}</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="dt-responsive table-responsive">
            <table class="table table-striped" zender-table></table>
        </div>
    </div>
</div>