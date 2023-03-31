<div class="modal fade" id="lang-modal">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content text-center">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="la la-language la-lg"></i> {$title}
            </h2>

            <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
  </div>
</div>