<form zender-form>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="la la-language la-lg"></i> {$title}
            </h3>

            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="row">
                {foreach $data.languages as $language}
                <div class="col-3 text-center">
                    <a href="#" class="all-languages" title="{$language.name}" zender-language="{$language.id}">
                        <i class="flag-icon flag-icon-{$language.iso} p-1"></i>
                    </a>
                </div>
                {/foreach}  
            </div>
        </div>
    </div>
</form>