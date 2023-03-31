<div class="card">
    <div class="card-header">
        <h4 class="card-title">
        	<i class="la la-comments-dollar"></i> {___(__("lang_and_rate_gate_3new"), [$data.gateway.name])} <span class="badge badge-success">{___(__("lang_and_rate_gate_idname"), [$data.gateway.id])}</span>
        </h4>
    </div>

    <div class="card-body">
    	<table class="table">
		  <thead>
		    <tr>
		      <th>{__("lang_and_rate_gate_10")}</th>
		      <th>{__("lang_and_rate_gate_11")} <span class="badge badge-success">{system_currency}</span></th>
		    </tr>
		  </thead>
		  <tbody>
		  	{foreach $data.pricing.countries as $country}
		    <tr>
		      <td class="text-uppercase"><i class="flag-icon flag-icon-{$country@key}"></i> {$country@key}</td>
		      <td>{$country}</td>
		    </tr>
		    {/foreach}
		    <tr>
		      <td class="text-uppercase"><i class="flag-icon flag-icon-un"></i> {__("lang_and_rate_gate_22")}</td>
		      <td>{$data.pricing.default}</td>
		    </tr>
		  </tbody>
		</table>
    </div>
</div>