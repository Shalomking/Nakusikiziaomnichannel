<?php

class Table_Controller extends MVC_Controller
{
	public function index()
	{
		$this->header->allow();

		if(!$this->session->has("logged"))
            response(302);

		$this->cache->container("system.settings");

        if($this->cache->empty()):
            $this->cache->setArray($this->system->getSettings());
        endif;

        set_system($this->cache->getAll());

        set_logged($this->session->get("logged"));

        set_language(logged_language);

        $request = $this->sanitize->array($_POST);
		$table = $this->sanitize->string($this->url->segment(3));

		switch($table){
			case "sms.sent":
				if(isset($request["structure"])):
					$structure = [
						"limit" => system_sent_limit,
						"history" => [
							"column" => "create_date"
						],
						"export" => [
							"export_columns" => [0, 1, 2, 3],
							"copy_title" => ___(__("lang_table_androidsent_clipcopy"), [date("d-m-Y (g:s A)")]),
							"excel_filename" => ___(__("lang_table_androidsent_excel"), [date("d-m-Y (g:s A)")]),
							"pdf_filename" => ___(__("lang_table_androidsent_pdf"), [date("d-m-Y (g:s A)")])
						],
						"columns" => [
							[
								"title" => __("lang_dashboard_messages_tablesentcreated"),
								"data" => "create_date",
								"width" => "20%"
							],
							[
								"title" => __("lang_dashboard_messages_tablesentrecipient"),
								"data" => "phone"
							],
							[
								"title" => __("lang_dashboard_messages_tablesentmessage"),
								"data" => "message",
								"width" => "40%",
								"sortable" => false
							],
							[
								"title" => __("lang_table_androidsend_report"),
								"data" => "report",
								"width" => "30%",
								"searchable" => false,
								"sortable" => false
							],
							[
								"title" => __("lang_dashboard_messages_tablesentoptions"),
								"data" => "options",
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "sent", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "id",
							"dt" => "id",
							"formatter" => function($value){
								return "#{$value}";
							}
						],
						[
							"db" => "phone",
							"dt" => "phone",
							"formatter" => function($value){
								$this->cache->container("contacts." . logged_hash);

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getContacts(logged_id));
								endif;

								$contacts = $this->cache->getAll();

								$contactName = isset($contacts[$value]) ? $contacts[$value]["name"] : __("lang_table_unknown_data");
								$contactPhone = $value;

								return <<<HTML
								{$contactName}<br>
								{$contactPhone}
								HTML;
							}
						],
						[
							"db" => "message",
							"dt" => "message",
							"formatter" => function($value, $row){
								$messageStr = mb_strlen($value) > 50 ? mb_substr($value, 0, 30) . "...<br><button class=\"btn btn-sm btn-primary\" title=\"" . __("lang_dashboard_messages_tablefullmessage") . "\" zender-toggle=\"zender.view/sent-{$row["id"]}\">" . __("lang_dashboard_messages_tablefullmessage") . "</button>" : $value;
								$exportData = $this->sanitize->string($value, true);

								return <<<HTML
								<p export-data="{$exportData}">{$messageStr}</p>
								HTML;
							}
						],
						[
							"dt" => "report",
							"formatter" => function($row){
								switch($row["status"]):
									case 1:
										$sentStatus = "<span class=\"badge badge-info\">{$GLOBALS["__"]("lang_table_androidsent_statuspending")}</span>";
										break;
									case 2:
										$sentStatus = "<span class=\"badge badge-warning\">{$GLOBALS["__"]("lang_table_androidsent_statusqueued")}</span>";
										break;
									case 3:
										$sentStatus = "<span class=\"badge badge-success\">{$GLOBALS["__"]("lang_table_androidsent_statussent")}</span>";
										break;
									case 4:
										$sentStatus = "<span class=\"badge badge-danger\">{$GLOBALS["__"]("lang_table_androidsent_statusfailed")}</span>";
										break;
									default:
										$sentStatus = "<span class=\"badge badge-danger\">{$GLOBALS["__"]("lang_table_unknown_data")}</span>";
								endswitch;

								if($row["mode"] < 2):
									$this->cache->container("user." . logged_hash);

									if(!$this->cache->has("devices")):
										$this->cache->set("devices", $this->table->getDevices(logged_id));
									endif;

									$devices = $this->cache->get("devices");

									$devicesGlobal = $this->widget->getGlobalDevices(logged_id);

									$mode = __("lang_table_androidsent_modenamedevice");
									$sentCode = "<span class=\"badge badge-primary\">" . (empty($row["status_code"]) ? __("lang_table_none_data") : $row["status_code"]) . "</span>";
									$sentPriority = $row["priority"] < 1 ? __("lang_table_yes_data") : __("lang_table_no_data");
									$sentApi = $row["api"] < 2 ? __("lang_table_yes_data") : __("lang_table_no_data");
									$simSlot = $row["sim"] < 2 ? 1 : 2;
									$device = isset($devices[$row["did"]]) ? $devices[$row["did"]]["name"] : __("lang_table_removed_data");

									switch(isset($devices[$row["did"]]) ? round((int) $devices[$row["did"]]["version"]) : false):
										case 4:
											$android = __("lang_table_androidver_kitkat");
											break;
										case 5:
											$android = __("lang_table_androidver_lollipop");
											break;
										case 6:
											$android = __("lang_table_androidver_marshmallow");
											break;
										case 7:
											$android = __("lang_table_androidver_nougat");
											break;
										case 8:
											$android = __("lang_table_androidver_oreo");
											break;
										case 9:
											$android = __("lang_table_androidver_pie");
											break;
										case 10:
											$android = __("table_androidver_10");
											break;
										case 11:
											$android = __("lang_table_androidver_11");
											break;
										case 12:
											$android = __("lang_table_androidver_12");
											break;
										case 13:
											$android = __("lang_table_androidver_13");
											break;
										default:
											$android = __("lang_table_unknown_data");
									endswitch;

									$exportData = $this->sanitize->string(___(__("lang_table_androidsent_exportone"), [$sentStatus, $mode, $sentCode, $sentPriority, $sentApi, $simSlot, $device, $android]), true);

									return <<<HTML
									<ul class="text-left" export-data="{$exportData}">
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline1"), [$sentStatus])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline2"), [$mode])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline3"), [$sentCode])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline4"), [$sentPriority])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline5"), [$sentApi])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline6"), [$simSlot])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline7"), [$device])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline8"), [$android])}</li>
									</ul>
									HTML;
								else:
									$mode = __("lang_table_androidsent_modenamecredits");

									if($row["gateway"] > 0):
										$this->cache->container("system.gateways");

										if(!$this->cache->has("gateways")):
											$this->cache->set("gateways", $this->system->getGateways());
										endif;

										$gateways = $this->cache->get("gateways");

										$gateway = isset($gateways[$row["gateway"]]) ? $gateways[$row["gateway"]]["name"] : __("lang_table_removed_data");

										$exportData = $this->sanitize->string(___(__("lang_table_androidsent_exporttwo"), [$sentStatus, $mode, $gateway]), true);

										return <<<HTML
										<ul class="text-left" export-data="{$exportData}">
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline9"), [$sentStatus])}</li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline10"), [$mode])}</li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline11"), [$gateway])}</li>
										</ul>
										HTML;
									else:
										$devicesGlobal = $this->system->getGlobalDevices(logged_id);

										$sentCode = "<span class=\"badge badge-primary\">" . (empty($row["status_code"]) ? __("lang_table_unknown_data") : $row["status_code"]) . "</span>";
										$sentApi = $row["api"] < 1 ? __("lang_table_yes_data") : __("lang_table_no_data");
										$simSlot = $row["sim"] < 2 ? 1 : 2;
										$device = isset($devicesGlobal[$row["did"]]) ? $devicesGlobal[$row["did"]]["name"] : __("lang_table_removed_data");

										$exportData = $this->sanitize->string(___(__("lang_table_androidsent_exportthree"), [$sentStatus, $mode, $sentCode, $sentApi, $simSlot, $device]), true);

										return <<<HTML
										<ul class="text-left" export-data="{$exportData}">
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline12"), [$sentStatus])}</li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline13"), [$mode])}</li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline14"), [$sentCode])}</li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline15"), [$sentApi])}</li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline16"), [$simSlot])}</li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline17"), [$device])}</li>
										</ul>
										HTML;
									endif;
								endif;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								$resendState = $row["mode"] > 1 ? "disabled" : "zender-action=\"resend\" resend-id=\"{$row["id"]}\"";

								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								    	<button class="btn btn-sm btn-success" title="{$GLOBALS["__"]("lang_table_androidsent_titleresend")}" {$resendState}>
								            <i class="la la-retweet"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="sent/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"gateway",
						"sim",
						"mode",
						"did",
						"api",
						"priority",
						"status",
						"status_code"
					],
					[
						"uid = " . logged_id . " AND status > 2"
					]);
				endif;

				break;
			case "sms.queue":
				if(isset($request["structure"])):
					$structure = [
						"limit" => system_sent_limit,
						"search" => [
							"disable" => true
						],
						"columns" => [
							[
								"title" => "Status",
								"data" => "status",
								"visible" => false,
								"searchable" => false
							],
							[
								"title" => __("lang_dashboard_messages_tablesentcreated"),
								"data" => "create_date",
								"width" => "20%"
							],
							[
								"title" => __("lang_dashboard_messages_tablesentrecipient"),
								"data" => "phone"
							],
							[
								"title" => __("lang_dashboard_messages_tablesentmessage"),
								"data" => "message",
								"width" => "40%",
								"sortable" => false
							],
							[
								"title" => __("lang_table_androidsend_report"),
								"data" => "report",
								"width" => "30%",
								"searchable" => false,
								"sortable" => false
							],
							[
								"title" => __("lang_dashboard_messages_tablesentoptions"),
								"data" => "options",
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "sent", [
						[
							"db" => "status",
							"dt" => "status"
						],
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "id",
							"dt" => "id",
							"formatter" => function($value){
								return "#{$value}";
							}
						],
						[
							"db" => "phone",
							"dt" => "phone",
							"formatter" => function($value){
								$this->cache->container("contacts." . logged_hash);

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getContacts(logged_id));
								endif;

								$contacts = $this->cache->getAll();

								$contactName = isset($contacts[$value]) ? $contacts[$value]["name"] : __("lang_table_unknown_data");
								$contactPhone = $value;

								return <<<HTML
								{$contactName}<br>
								{$contactPhone}
								HTML;
							}
						],
						[
							"db" => "message",
							"dt" => "message",
							"formatter" => function($value, $row){
								$messageStr = mb_strlen($value) > 50 ? mb_substr($value, 0, 30) . "...<br><button class=\"btn btn-sm btn-primary\" title=\"" . __("lang_dashboard_messages_tablefullmessage") . "\" zender-toggle=\"zender.view/sent-{$row["id"]}\">" . __("lang_dashboard_messages_tablefullmessage") . "</button>" : $value;

								return $messageStr;
							}
						],
						[
							"dt" => "report",
							"formatter" => function($row){
								switch($row["status"]):
									case 1:
										$sentStatus = "<span class=\"badge badge-info\">{$GLOBALS["__"]("lang_table_androidsent_statuspending")}</span>";
										break;
									case 2:
										$sentStatus = "<span class=\"badge badge-warning\">{$GLOBALS["__"]("lang_table_androidsent_statusqueued")}</span>";
										break;
									case 3:
										$sentStatus = "<span class=\"badge badge-success\">{$GLOBALS["__"]("lang_table_androidsent_statussent")}</span>";
										break;
									case 4:
										$sentStatus = "<span class=\"badge badge-danger\">{$GLOBALS["__"]("lang_table_androidsent_statusfailed")}</span>";
										break;
									default:
										$sentStatus = "<span class=\"badge badge-danger\">{$GLOBALS["__"]("lang_table_unknown_data")}</span>";
								endswitch;

								if($row["mode"] < 2):
									$this->cache->container("user." . logged_hash);

									if(!$this->cache->has("devices")):
										$this->cache->set("devices", $this->table->getDevices(logged_id));
									endif;

									$devices = $this->cache->get("devices");

									$devicesGlobal = $this->widget->getGlobalDevices(logged_id);

									$mode = __("lang_table_androidsent_modenamedevice");
									$sentPriority = $row["priority"] < 1 ? __("lang_table_yes_data") : __("lang_table_no_data");
									$sentApi = $row["api"] < 2 ? __("lang_table_yes_data") : __("lang_table_no_data");
									$simSlot = $row["sim"] < 2 ? 1 : 2;
									$device = isset($devices[$row["did"]]) ? $devices[$row["did"]]["name"] : __("lang_table_removed_data");

									switch(isset($devices[$row["did"]]) ? round((int) $devices[$row["did"]]["version"]) : false):
										case 4:
											$android = __("lang_table_androidver_kitkat");
											break;
										case 5:
											$android = __("lang_table_androidver_lollipop");
											break;
										case 6:
											$android = __("lang_table_androidver_marshmallow");
											break;
										case 7:
											$android = __("lang_table_androidver_nougat");
											break;
										case 8:
											$android = __("lang_table_androidver_oreo");
											break;
										case 9:
											$android = __("lang_table_androidver_pie");
											break;
										case 10:
											$android = __("table_androidver_10");
											break;
										case 11:
											$android = __("lang_table_androidver_11");
											break;
										case 12:
											$android = __("lang_table_androidver_12");
											break;
										case 13:
											$android = __("lang_table_androidver_13");
											break;
										default:
											$android = __("lang_table_unknown_data");
									endswitch;

									return <<<HTML
									<ul class="text-left">
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline1"), [$sentStatus])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline2"), [$mode])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline4"), [$sentPriority])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline5"), [$sentApi])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline6"), [$simSlot])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline7"), [$device])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline8"), [$android])}</li>
									</ul>
									HTML;
								else:
									$mode = __("lang_table_androidsent_modenamecredits");

									if($row["gateway"] > 0):
										$this->cache->container("system.gateways");

										if(!$this->cache->has("gateways")):
											$this->cache->set("gateways", $this->system->getGateways());
										endif;

										$gateways = $this->cache->get("gateways");

										$gateway = isset($gateways[$row["gateway"]]) ? $gateways[$row["gateway"]]["name"] : __("lang_table_removed_data");

										return <<<HTML
										<ul class="text-left">
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline9"), [$sentStatus])}</li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline10"), [$mode])}</li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline11"), [$gateway])}</li>
										</ul>
										HTML;
									else:
										$devicesGlobal = $this->system->getGlobalDevices(logged_id);

										$sentApi = $row["api"] < 1 ? __("lang_table_yes_data") : __("lang_table_no_data");
										$simSlot = $row["sim"] < 2 ? 1 : 2;
										$device = isset($devicesGlobal[$row["did"]]) ? $devicesGlobal[$row["did"]]["name"] : __("lang_table_removed_data");

										return <<<HTML
										<ul class="text-left">
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline12"), [$sentStatus])}</li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline13"), [$mode])}</li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline15"), [$sentApi])}</li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline16"), [$simSlot])}</li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidsent_reportline17"), [$device])}</li>
										</ul>
										HTML;
									endif;
								endif;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="sent/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"gateway",
						"sim",
						"mode",
						"did",
						"api",
						"priority",
						"status_code"
					],
					[
						"uid = " . logged_id . " AND status < 3"
					]);
				endif;

				break;
			case "sms.campaigns":
				if(isset($request["structure"])):
					$structure = [
						"search" => [
							"disable" => true
						],
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_smscampaign_createdtitle"),
								"data" => "create_date",
								"width" => "15%"
							],
							[
								"title" => __("lang_table_smscampaign_nametitle"),
								"data" => "name"
							],
							[
								"title" => __("lang_table_smscampaign_gatewaytitle"),
								"data" => "sending_gateway"
							],
							[
								"title" => __("lang_table_smscampaign_contactstitle"),
								"data" => "contacts"
							],
							[
								"title" => __("lang_table_smscampaign_statustitle"),
								"data" => "status"
							],
							[
								"title" => __("lang_table_smscampaign_optionstitle"),
								"data" => "options",
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "campaigns", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[	
							"db" => "name",
							"dt" => "name",
							"formatter" => function($value){
								return ucfirst($value);
							}
						],
						[	
							"db" => "did",
							"dt" => "sending_gateway",
							"formatter" => function($value, $row){
								if($row["mode"] < 2):
									$this->cache->container("user." . logged_hash);

									if(!$this->cache->has("devices")):
										$this->cache->set("devices", $this->table->getDevices(logged_id));
									endif;

									$devices = $this->cache->get("devices");

									$devicesGlobal = $this->widget->getGlobalDevices(logged_id);

									return isset($devices[$value]) ? $devices[$value]["name"] : __("lang_table_removed_data");
								else:
									if($row["gateway"] > 0):
										$this->cache->container("system.gateways");

										if(!$this->cache->has("gateways")):
											$this->cache->set("gateways", $this->system->getGateways());
										endif;

										$gateways = $this->cache->get("gateways");

										return isset($gateways[$row["gateway"]]) ? $gateways[$row["gateway"]]["name"] : __("lang_table_removed_data");
									else:
										$devicesGlobal = $this->system->getGlobalDevices(logged_id);

										return isset($devicesGlobal[$value]) ? $devicesGlobal[$value]["name"] : __("lang_table_removed_data");
									endif;
								endif;
							}
						],
						[
							"db" => "contacts",
							"dt" => "contacts",
							"formatter" => function($value){
								return number_format($value);
							}
						],
						[
							"db" => "status",
							"dt" => "status",
							"formatter" => function($value, $row){
								$pendingSms = $this->table->checkSmsCampaignPending($row["uid"], $row["id"]);

								return $pendingSms < 1 ? __("lang_table_campaign_completedstatus") : ___(__("lang_table_smscagmpaign_processingstatus"), [number_format(abs($row["contacts"] - $pendingSms)), number_format($row["contacts"])]);
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								$pendingSms = $this->table->checkSmsCampaignPending($row["uid"], $row["id"]);

								$btnStartState = "disabled";
								$btnPauseState = "disabled";

								if($pendingSms > 0):
									if($row["status"] < 2):
										$btnPauseState = false;
									else:
										$btnStartState = false;
									endif;
								endif;

								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								    	<button class="btn btn-sm btn-success" title="{$GLOBALS["__"]("lang_table_campaign_resumecampaign")}" zender-sms-start="{$row["id"]}/{$row["did"]}/{$row["name"]}" {$btnStartState}>
								            <i class="la la-play"></i>
								        </button>
								    	<button class="btn btn-sm btn-warning" title="{$GLOBALS["__"]("lang_table_campaign_pausecampaign")}" zender-sms-stop="{$row["id"]}/{$row["did"]}/{$row["name"]}" {$btnPauseState}>
								            <i class="la la-pause"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="sms.campaign/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"uid",
						"gateway",
						"mode"
					],
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "sms.received":
				if(isset($request["structure"])):
					$structure = [
						"limit" => system_received_limit,
						"history" => [
							"column" => "receive_date"
						],
						"export" => [
							"export_columns" => [0, 1, 2, 3],
							"copy_title" => ___(__("lang_table_androidreceive_exportcopy"), [date("d-m-Y (g:s A)")]),
							"excel_filename" => ___(__("lang_table_androidreceive_exportexcel"), [date("d-m-Y (g:s A)")]),
							"pdf_filename" => ___(__("lang_table_androidreceive_exportpdf"), [date("d-m-Y (g:s A)")])
						],
						"columns" => [
							[
								"title" => __("lang_dashboard_messages_tablereceivedreceived"),
								"data" => "receive_date",
								"width" => "20%"
							],
							[
								"title" => __("lang_dashboard_messages_tablereceivedsender"),
								"data" => "phone"
							],
							[
								"title" => __("lang_dashboard_messages_tablereceivedmessage"),
								"data" => "message",
								"width" => "40%",
								"sortable" => false
							],
							[
								"title" => __("lang_table_androidreceive_reporttitle"),
								"data" => "report",
								"width" => "25%",
								"searchable" => false,
								"sortable" => false
							],
							[
								"title" => __("lang_dashboard_messages_tablereceivedoptions"),
								"data" => "options",
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "received", [
						[
							"db" => "receive_date",
							"dt" => "receive_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "id",
							"dt" => "id",
							"formatter" => function($value, $row){
								return "#{$value}";
							}
						],
						[
							"db" => "phone",
							"dt" => "phone",
							"formatter" => function($value){
								$this->cache->container("contacts." . logged_hash);

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getContacts(logged_id));
								endif;

								$contacts = $this->cache->getAll();

								$contactName = isset($contacts[$value]) ? $contacts[$value]["name"] : __("lang_table_unknown_data");
								$contactPhone = $value;

								return <<<HTML
								{$contactName}<br>
								{$contactPhone}
								HTML;
							}
						],
						[
							"db" => "message",
							"dt" => "message",
							"formatter" => function($value, $row){
								$messageStr = mb_strlen($value) > 50 ? mb_substr($value, 0, 30) . "...<br><button class=\"btn btn-sm btn-primary\" title=\"" . __("lang_dashboard_messages_tablefullmessage") . "\" zender-toggle=\"zender.view/received-{$row["id"]}\">" . __("lang_dashboard_messages_tablefullmessage") . "</button>" : $value;
								$exportData = $this->sanitize->string($value, true);

								return <<<HTML
								<p export-data="{$exportData}">{$messageStr}</p>
								HTML;
							}
						],
						[
							"dt" => "report",
							"formatter" => function($row){
								$this->cache->container("user." . logged_hash);

								if(!$this->cache->has("devices")):
									$this->cache->set("devices", $this->table->getDevices(logged_id));
								endif;

								$devices = $this->cache->get("devices");

								$slot = $row["slot"] < 2 ? 1 : 2;
								$device = isset($devices[$row["did"]]) ? $devices[$row["did"]]["name"] : __("lang_table_removed_data");

								switch(isset($devices[$row["did"]]) ? $devices[$row["did"]]["version"] : false):
									case 4:
										$android = __("lang_table_androidver_kitkat");
										break;
									case 5:
										$android = __("lang_table_androidver_lollipop");
										break;
									case 6:
										$android = __("lang_table_androidver_marshmallow");
										break;
									case 7:
										$android = __("lang_table_androidver_nougat");
										break;
									case 8:
										$android = __("lang_table_androidver_oreo");
										break;
									case 9:
										$android = __("lang_table_androidver_pie");
										break;
									case 10:
										$android = __("lang_table_androidver_10");
										break;
									case 11:
										$android = __("lang_table_androidver_11");
										break;
									case 12:
										$android = __("lang_table_androidver_12");
										break;
									case 13:
										$android = __("lang_table_androidver_13");
										break;
									default:
										$android = __("lang_table_unknown_data");
								endswitch;

								$exportData = $this->sanitize->string(___(__("lang_table_androidreceive_exportdata"), [$slot, $device, $android]), true);

								return <<<HTML
								<ul class="text-left" export-data="{$exportData}">
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidreceive_reportline1"), [$slot])}</li>
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidreceive_reportline2"), [$device])}</li>
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidreceive_reportline3"), [$android])}</li>
								</ul>
								HTML;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								try {
								    $number = $this->phone->parse($row["phone"], logged_country);
								    $replyDisabled = "title=\"{$GLOBALS["__"]("lang_table_androidreceive_titlereply")}\" zender-toggle=\"zender.sms.quick\" zender-reply=\"{$row["phone"]}\"";
								} catch(Brick\PhoneNumber\PhoneNumberParseException $e) {
									$replyDisabled = "disabled";
								}

								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								    	<button class="btn btn-sm btn-success" {$replyDisabled}>
								            <i class="la la-reply"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="received/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"did",
						"slot"
					],
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "sms.scheduled":
				if(isset($request["structure"])):
					$structure = [
						"columns" => [
							[
								"title" => __("lang_table_androidscheduled_titleschedule"),
								"data" => "send_date"
							],
							[
								"title" => __("lang_table_scheduled_name"),
								"data" => "name"
							],
							[
								"title" => __("lang_dashboard_messages_tablesentmessage"),
								"data" => "message",
								"width" => "25%"
							],
							[
								"title" => __("lang_table_androidscheduled_titledetails"),
								"data" => "details",
								"width" => "25%",
								"sortable" => false
							],
							[
								"title" => __("lang_dashboard_messages_tablesentoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "scheduled", [
						[
							"db" => "send_date",
							"dt" => "send_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, $value);
								$createTime = date(logged_clock_format, $value);
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"db" => "message",
							"dt" => "message"
						],
						[
							"dt" => "details",
							"formatter" => function($row){
								$this->cache->container("user." . logged_hash);

								if(!$this->cache->has("devices")):
									$this->cache->set("devices", $this->table->getDevices(logged_id));
								endif;

								$devices = $this->cache->get("devices");

								if(!$this->cache->has("groups")):
									$this->cache->set("groups", $this->table->getGroups(logged_id));
								endif;

								$groups = $this->cache->get("groups");

								$scheduledMode = $row["mode"] < 2 ? __("lang_table_androidsent_modenamedevice") : __("lang_table_androidsent_modenamecredits"); 
								$scheduledRepeat = $row["repeat"] > 0 ? ($row["repeat"] == 1 ? "1 day" : "{$row["repeat"]} days") : __("lang_table_no_data");
								$lastSend = empty($row["last_send"]) ? __("lang_table_campaign_pendingstatus") : date(logged_date_format . " " . logged_clock_format, $row["last_send"]);
								$groupsHandle = $row["groups"] == "0" ? __("lang_table_none_data") : explode(",", $row["groups"]);

								if(is_array($groupsHandle)):
									foreach($groupsHandle as $group):
										$scheduledGroupsHolder[] = $groups[$group]["name"];
									endforeach;

									$scheduledGroups = implode(", ", $scheduledGroupsHolder);
								else:
									$scheduledGroups = $groupsHandle;
								endif;

								$scheduledNumbers = !empty($row["numbers"]) ? implode(", ", explode(",", $row["numbers"])) : __("lang_table_none_data");	

								if($row["mode"] < 2):
									$scheduledSim = $row["sim"] < 2 ? __("lang_table_androidscheduled_sim1") : __("lang_table_androidscheduled_sim2");
									$scheduledDevice = isset($devices[$row["did"]]) ? $devices[$row["did"]]["name"] : __("lang_table_removed_data");

									return <<<HTML
									<ul class="text-left">
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidscheduled_detailsline1"), [$scheduledMode])}</li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidscheduled_detailsline2"), [$scheduledSim])}</li>
									  <li>{$GLOBALS["__"]("lang_table_details_schedulednewrepeat")}<br>
									  	<code>{$scheduledRepeat}</code>
									  </li>
									  <li>{$GLOBALS["__"]("lang_table_smsscheduled_lastsendinfo")}<br>
									  	<code>{$lastSend}</code>
									  </li>
									  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidscheduled_detailsline4"), [$scheduledDevice])}</li>
									  <li>{$GLOBALS["__"]("lang_table_androidscheduled_detailsgroupslabel")}<br>
									  	<code>{$scheduledGroups}</code>
									  </li>
									  <li>{$GLOBALS["__"]("lang_table_androidscheduled_detailsnumberslabel")}<br>
									  	<code>{$scheduledNumbers}</code>
									  </li>
									</ul>
									HTML;
								else:
									if($row["gateway"] > 0):
										$this->cache->container("system.gateways");

										if(!$this->cache->has("gateways")):
											$this->cache->set("gateways", $this->system->getGateways());
										endif;

										$gateways = $this->cache->get("gateways");

										$gateway = isset($gateways[$row["gateway"]]) ? $gateways[$row["gateway"]]["name"] : __("lang_table_removed_data");

										return <<<HTML
										<ul class="text-left">
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidscheduled_detailsline5"), [$scheduledMode])}</li>
										  <li>{$GLOBALS["__"]("lang_table_details_schedulednewrepeat")}<br>
										  	<code>{$scheduledRepeat}</code>
										  </li>
										  <li>{$GLOBALS["__"]("lang_table_smsscheduled_lastsendinfo")}<br>
										  	<code>{$lastSend}</code>
										  </li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidscheduled_detailsline7"), [$gateway])}</li>
										  <li>{$GLOBALS["__"]("lang_table_androidscheduled_detailsgroupslabel")}<br><code>{$scheduledGroups}</code></li>
										  <li>{$GLOBALS["__"]("lang_table_androidscheduled_detailsnumberslabel")}<br><code>{$scheduledNumbers}</code></li>
										</ul>
										HTML;
									else:
										$scheduledDevice = isset($devices[$row["did"]]) ? $devices[$row["did"]]["name"] : __("lang_table_removed_data");

										return <<<HTML
										<ul class="text-left">
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidscheduled_detailsline8"), [$scheduledMode])}</li>
										  <li>{$GLOBALS["__"]("lang_table_details_schedulednewrepeat")}<br>
										  	<code>{$scheduledRepeat}</code>
										  </li>
										  <li>{$GLOBALS["__"]("lang_table_smsscheduled_lastsendinfo")}<br>
										  	<code>{$lastSend}</code>
										  </li>
										  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_androidscheduled_detailsline10"), [$scheduledDevice])}</li>
										  <li>{$GLOBALS["__"]("lang_table_androidscheduled_detailsgroupslabel")}<br>
										  	<code>{$scheduledGroups}</code>
										  </li>
										  <li>{$GLOBALS["__"]("lang_table_androidscheduled_detailsnumberslabel")}<br>
										  	<code>{$scheduledNumbers}</code>
										  </li>
										</ul>
										HTML;
									endif;
								endif;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.sms.scheduled/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="scheduled/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"did",
						"sim",
						"mode",
						"gateway",
						"groups",
						"numbers",
						"repeat",
						"last_send"
					],
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "sms.transactions":
				$partner = $this->system->getPartnership(logged_id);

				if($partner && $partner > 1)
					response(500, __("lang_response_invalid"));

				if(isset($request["structure"])):
					$structure = [
						"search" => [
							"disable" => true
						],
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_smstransac_createdtitle"),
								"data" => "create_date"
							],
							[
								"title" => __("lang_table_smstransac_sendertitle"),
								"data" => "sender",
								"sortable" => false
							],
							[
								"title" => __("lang_table_smstransac_devicetitle"),
								"data" => "device",
								"sortable" => false
							],
							[
								"title" => __("lang_table_smstransac_messagetitle"),
								"data" => "message",
								"sortable" => false,
								"width" => "30%"
							],
							[
								"title" => __("lang_table_smstransac_earningstitle"),
								"data" => "earnings",
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "commissions", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"dt" => "sender",
							"formatter" => function($row){
								try {
									$user = $this->system->getUser($row["sid"]);

									return $user["email"];
								} catch(Exception $e){
									return __("lang_table_unknown_data");
								}
								
							}
						],
						[	
							"db" => "did",
							"dt" => "device",
							"formatter" => function($value, $row){
								$this->cache->container("user." . logged_hash);

								if(!$this->cache->has("devices")):
									$this->cache->set("devices", $this->table->getDevices(logged_id));
								endif;

								$devices = $this->cache->get("devices");

								return isset($devices[$value]) ? $devices[$value]["name"] : __("lang_table_removed_data");
							}
						],
						[
							"dt" => "message",
							"formatter" => function($row){
								$sent = $this->system->getSent($row["mid"]);

								if($sent):
									$messageStr = mb_strlen($sent["message"]) > 50 ? mb_substr($sent["message"], 0, 30) . "...<br><button class=\"btn btn-sm btn-primary\" title=\"" . __("lang_dashboard_messages_tablefullmessage") . "\" zender-toggle=\"zender.view/sent-{$row["mid"]}\">" . __("lang_dashboard_messages_tablefullmessage") . "</button>" : $sent["message"];
								else:
									$messageStr = __("lang_table_smstransac_messageremoved");
								endif;

								return $messageStr;
							}
						],
						[
							"dt" => "earnings",
							"formatter" => function($row){
								return round($row["original_amount"] - ((system_partner_commission / 100) * $row["original_amount"]), 2) . " " . system_currency;
							}
						]
					], 
					[
						"id",
						"pid",
						"sid",
						"mid",
						"original_amount",
						"commission_amount"
					],
					[
						"pid = " . logged_id . " AND pid != sid"
					]);
				endif;

				break;
			case "whatsapp.sent":
				if(isset($request["structure"])):
					$structure = [
						"limit" => system_sent_limit,
						"history" => [
							"column" => "create_date"
						],
						"export" => [
							"export_columns" => [0, 1, 2, 3, 4, 5],
							"copy_title" => ___(__("lang_table_whatsappsent_exportcopy"), [date("d-m-Y (g:s A)")]),
							"excel_filename" => ___(__("lang_table_whatsappsent_exportexcel"), [date("d-m-Y (g:s A)")]),
							"pdf_filename" => ___(__("lang_table_whatsappsent_exportpdf"), [date("d-m-Y (g:s A)")])
						],
						"columns" => [
							[
								"title" => __("lang_dashboard_messages_tablesentcreated"),
								"data" => "create_date",
								"width" => "15%"
							],
							[
								"title" => __("lang_table_whatsappsent_accounttitle"),
								"data" => "account"
							],
							[
								"title" => __("lang_table_whatsappsent_recipienttitle"),
								"data" => "phone"
							],
							[
								"title" => __("lang_table_whatsappsent_msgtitle"),
								"data" => "message",
								"width" => "20%",
								"sortable" => false
							],
							[
								"title" => __("lang_table_whatsappsent_apititle"),
								"data" => "api",
								"sortable" => false
							],
							[
								"title" => __("lang_table_whatsappsent_statustitle"),
								"data" => "status",
								"sortable" => false
							],
							[
								"title" => __("lang_dashboard_messages_tablesentoptions"),
								"data" => "options",
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "wa_sent", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[	
							"db" => "wid",
							"dt" => "account",
							"formatter" => function($value){
								$wid = explode(":", $value);

								return "+{$wid[0]}";
							}
						],
						[
							"db" => "phone",
							"dt" => "phone",
							"formatter" => function($value){
								$this->cache->container("contacts." . logged_hash);

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getContacts(logged_id));
								endif;

								$contacts = $this->cache->getAll();

								$this->cache->container("wa.contacts." . logged_hash);

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getWaGroups(logged_id));
								endif;

								$waGroups = $this->cache->getAll();

								$contactName = isset($contacts[$value]) ? "<i class=\"la la-phone\"></i> {$contacts[$value]["name"]}" : (isset($waGroups[$value]) ? "<i class=\"la la-layer-group\"></i> {$waGroups[$value]["name"]}" : __("lang_table_unknown_data"));
								$contactPhone = mb_strlen($value) > 13 ? mb_substr($value, 0, 10) . "..." : $value;

								return <<<HTML
								{$contactName}<br>
								{$contactPhone}
								HTML;
							}
						],
						[
							"db" => "message",
							"dt" => "message",
							"formatter" => function($value, $row){
								try {
									$msgDecode = json_decode($value, true, JSON_THROW_ON_ERROR);
									$waMessage = isset($msgDecode["text"]) ? $msgDecode["text"] : $msgDecode["caption"];
								} catch(Exception $e){
									$waMessage = $value;
								}

								$messageStr = mb_strlen($waMessage) > 25 ? mb_substr($waMessage, 0, 20) . "...<br><button class=\"btn btn-sm btn-primary\" title=\"" . __("lang_dashboard_messages_tablefullmessage") . "\" zender-toggle=\"zender.view/wa.sent-{$row["id"]}\">" . __("lang_dashboard_messages_tablefullmessage") . "</button>" : $waMessage;
								$exportData = $this->sanitize->string($waMessage, true);

								return <<<HTML
								<p export-data="{$exportData}">{$messageStr}</p>
								HTML;
							}
						],
						[
							"db" => "api",
							"dt" => "api",
							"formatter" => function($value){
								return $value < 2 ? __("lang_table_yes_data") : __("lang_table_no_data");
							}
						],
						[
							"db" => "status",
							"dt" => "status",
							"formatter" => function($value){
								switch($value):
									case 1:
										$sentStatus = "<span class=\"badge badge-info\" export-data=\"{$GLOBALS["__"]("lang_table_whatsapp_pendinglabel")}\">{$GLOBALS["__"]("lang_table_whatsapp_pendinglabel")}</span>";
										break;
									case 2:
										$sentStatus = "<span class=\"badge badge-warning\" export-data=\"{$GLOBALS["__"]("lang_table_whatsapp_queuelabel")}\">{$GLOBALS["__"]("lang_table_whatsapp_queuelabel")}</span>";
										break;
									case 3:
										$sentStatus = "<span class=\"badge badge-success\" export-data=\"{$GLOBALS["__"]("lang_table_whatsapp_sentlabel")}\">{$GLOBALS["__"]("lang_table_whatsapp_sentlabel")}</span>";
										break;
									default:
										$sentStatus = "<span class=\"badge badge-danger\" export-data=\"{$GLOBALS["__"]("lang_table_whatsapp_failedlabel")}\">{$GLOBALS["__"]("lang_table_whatsapp_failedlabel")}</span>";
								endswitch;

								return $sentStatus;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								$downloadBtn = false;
								$downloadLink = "href=\"javascript:void(0)\"";

								try {
									$msgDecode = json_decode($row["message"], true, JSON_THROW_ON_ERROR);

									if(isset($msgDecode["image"])):
										$downloadLink = "href=\"{$msgDecode["image"]["url"]}\" target=\"_blank\"";
									elseif(isset($msgDecode["video"])):
										$downloadLink = "href=\"{$msgDecode["video"]["url"]}\" target=\"_blank\"";
									elseif(isset($msgDecode["document"])):
										$downloadLink = "href=\"{$msgDecode["document"]["url"]}\" target=\"_blank\"";
									else:
										$downloadBtn = " disabled";
									endif;
								} catch(Exception $e){
									$downloadBtn = " disabled";
								}

								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								    	<a {$downloadLink}><button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_tables_wabtn_downloadattach")}" {$downloadBtn}>
								            <i class="la la-download"></i>
								        </button></a>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="wa.sent/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
					],
					[
						"uid = " . logged_id . " AND status > 2"
					]);
				endif;

				break;
			case "whatsapp.queue":
				if(isset($request["structure"])):
					$structure = [
						"limit" => system_sent_limit,
						"search" => [
							"disable" => true
						],
						"columns" => [
							[
								"title" => "Status",
								"data" => "status",
								"visible" => false,
								"searchable" => false
							],
							[
								"title" => __("lang_dashboard_messages_tablesentcreated"),
								"data" => "create_date",
								"width" => "15%"
							],
							[
								"title" => __("lang_table_whatsappsent_accounttitle"),
								"data" => "account"
							],
							[
								"title" => __("lang_table_whatsappsent_recipienttitle"),
								"data" => "phone"
							],
							[
								"title" => __("lang_table_whatsappsent_msgtitle"),
								"data" => "message",
								"width" => "20%",
								"sortable" => false
							],
							[
								"title" => __("lang_table_whatsappsent_apititle"),
								"data" => "api",
								"sortable" => false
							],
							[
								"title" => __("lang_table_whatsappsent_statustitle"),
								"data" => "status",
								"sortable" => false,
								"searchable" => false
							],
							[
								"title" => __("lang_dashboard_messages_tablesentoptions"),
								"data" => "options",
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "wa_sent", [
						[	
							"db" => "status",
							"dt" => "status"
						],
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[	
							"db" => "wid",
							"dt" => "account",
							"formatter" => function($value){
								$wid = explode(":", $value);

								return "+{$wid[0]}";
							}
						],
						[
							"db" => "phone",
							"dt" => "phone",
							"formatter" => function($value){
								$this->cache->container("contacts." . logged_hash);

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getContacts(logged_id));
								endif;

								$contacts = $this->cache->getAll();

								$this->cache->container("wa.contacts." . logged_hash);

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getWaGroups(logged_id));
								endif;

								$waGroups = $this->cache->getAll();

								$contactName = isset($contacts[$value]) ? "<i class=\"la la-phone\"></i> {$contacts[$value]["name"]}" : (isset($waGroups[$value]) ? "<i class=\"la la-layer-group\"></i> {$waGroups[$value]["name"]}" : __("lang_table_unknown_data"));
								$contactPhone = mb_strlen($value) > 13 ? mb_substr($value, 0, 10) . "..." : $value;

								return <<<HTML
								{$contactName}<br>
								{$contactPhone}
								HTML;
							}
						],
						[
							"db" => "message",
							"dt" => "message",
							"formatter" => function($value, $row){
								try {
									$msgDecode = json_decode($value, true, JSON_THROW_ON_ERROR);
									$waMessage = isset($msgDecode["text"]) ? $msgDecode["text"] : $msgDecode["caption"];
								} catch(Exception $e){
									$waMessage = $value;
								}

								$messageStr = mb_strlen($waMessage) > 25 ? mb_substr($waMessage, 0, 20) . "...<br><button class=\"btn btn-sm btn-primary\" title=\"" . __("lang_dashboard_messages_tablefullmessage") . "\" zender-toggle=\"zender.view/wa.sent-{$row["id"]}\">" . __("lang_dashboard_messages_tablefullmessage") . "</button>" : $waMessage;

								return $messageStr;
							}
						],
						[
							"db" => "api",
							"dt" => "api",
							"formatter" => function($value){
								return $value < 2 ? __("lang_table_yes_data") : __("lang_table_no_data");
							}
						],
						[
							"dt" => "status",
							"formatter" => function($row){
								switch($row["status"]):
									case 1:
										$sentStatus = "<span class=\"badge badge-info\">{$GLOBALS["__"]("lang_table_whatsapp_pendinglabel")}</span>";
										break;
									case 2:
										$sentStatus = "<span class=\"badge badge-warning\">{$GLOBALS["__"]("lang_table_whatsapp_queuelabel")}</span>";
										break;
									case 3:
										$sentStatus = "<span class=\"badge badge-success\">{$GLOBALS["__"]("lang_table_whatsapp_sentlabel")}</span>";
										break;
									default:
										$sentStatus = "<span class=\"badge badge-danger\">{$GLOBALS["__"]("lang_table_whatsapp_failedlabel")}</span>";
								endswitch;

								return $sentStatus;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								$downloadBtn = false;
								$downloadLink = "href=\"javascript:void(0)\"";

								try {
									$msgDecode = json_decode($row["message"], true, JSON_THROW_ON_ERROR);

									if(isset($msgDecode["image"])):
										$downloadLink = "href=\"{$msgDecode["image"]["url"]}\" target=\"_blank\"";
									elseif(isset($msgDecode["video"])):
										$downloadLink = "href=\"{$msgDecode["video"]["url"]}\" target=\"_blank\"";
									elseif(isset($msgDecode["document"])):
										$downloadLink = "href=\"{$msgDecode["document"]["url"]}\" target=\"_blank\"";
									else:
										$downloadBtn = " disabled";
									endif;
								} catch(Exception $e){
									$downloadBtn = " disabled";
								}

								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								    	<a {$downloadLink}><button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_tables_wabtn_downloadattach")}" {$downloadBtn}>
								            <i class="la la-download"></i>
								        </button></a>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="wa.sent/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
					],
					[
						"uid = " . logged_id . " AND status < 3"
					]);
				endif;

				break;
			case "whatsapp.campaigns":
				if(isset($request["structure"])):
					$structure = [
						"search" => [
							"disable" => true
						],
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_wacampaign_createdtitle"),
								"data" => "create_date",
								"width" => "15%"
							],
							[
								"title" => __("lang_table_wacampaign_nametitle"),
								"data" => "name"
							],
							[
								"title" => __("lang_table_wacampaign_accounttitle"),
								"data" => "account"
							],
							[
								"title" => __("lang_table_wacampaign_typetitle"),
								"data" => "type"
							],
							[
								"title" => __("lang_table_wacampaign_contactstitle"),
								"data" => "contacts"
							],
							[
								"title" => __("lang_table_wacampaign_statustitle"),
								"data" => "status"
							],
							[
								"title" => __("lang_table_wacampaign_optionstitle"),
								"data" => "options",
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "wa_campaigns", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[	
							"db" => "name",
							"dt" => "name",
							"formatter" => function($value){
								return ucfirst($value);
							}
						],
						[	
							"db" => "wid",
							"dt" => "account",
							"formatter" => function($value){
								$wid = explode(":", $value);

								return "+{$wid[0]}";
							}
						],
						[
							"db" => "type",
							"dt" => "type",
							"formatter" => function($value){
								return ucfirst($value);
							}
						],
						[
							"db" => "contacts",
							"dt" => "contacts",
							"formatter" => function($value){
								return number_format($value);
							}
						],
						[
							"db" => "status",
							"dt" => "status",
							"formatter" => function($value, $row){
								$pendingChats = $this->table->checkWaCampaignPending($row["uid"], $row["id"]);

								return $pendingChats < 1 ? __("lang_table_campaign_completedstatus") : ___(__("lang_table_wacampaign_processingstatus"), [number_format(abs($row["contacts"] - $pendingChats)), number_format($row["contacts"])]);
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								$pendingChats = $this->table->checkWaCampaignPending($row["uid"], $row["id"]);

								$btnStartState = "disabled";
								$btnPauseState = "disabled";

								if($pendingChats > 0):
									if($row["status"] < 2):
										$btnPauseState = false;
									else:
										$btnStartState = false;
									endif;
								endif;

								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								    	<button class="btn btn-sm btn-success" title="{$GLOBALS["__"]("lang_table_campaign_resumecampaign")}" zender-whatsapp-start="{$row["id"]}" {$btnStartState}>
								            <i class="la la-play"></i>
								        </button>
								    	<button class="btn btn-sm btn-warning" title="{$GLOBALS["__"]("lang_table_campaign_pausecampaign")}" zender-whatsapp-stop="{$row["id"]}" {$btnPauseState}>
								            <i class="la la-pause"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="wa.campaign/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"uid"
					],
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "whatsapp.received":
				if(isset($request["structure"])):
					$structure = [
						"limit" => system_received_limit,
						"history" => [
							"column" => "receive_date"
						],
						"export" => [
							"export_columns" => [0, 1, 2, 3],
							"copy_title" => ___(__("lang_table_whatsappreceive_exportcopy"), [date("d-m-Y (g:s A)")]),
							"excel_filename" => ___(__("lang_table_whatsappreceive_exportexcel"), [date("d-m-Y (g:s A)")]),
							"pdf_filename" => ___(__("lang_table_whatsappreceive_exportpdf"), [date("d-m-Y (g:s A)")])
						],
						"columns" => [
							[
								"title" => __("lang_dashboard_messages_tablereceivedreceived"),
								"data" => "receive_date",
								"width" => "20%"
							],
							[
								"title" => __("lang_dashboard_messages_tablereceivedsender"),
								"data" => "phone"
							],
							[
								"title" => __("lang_dashboard_messages_tablereceivedmessage"),
								"data" => "message",
								"width" => "30%",
								"sortable" => false
							],
							[
								"title" => __("lang_table_whatsappreceived_accounttitle"),
								"data" => "account",
								"width" => "25%",
								"searchable" => false,
								"sortable" => false
							],
							[
								"title" => __("lang_dashboard_messages_tablereceivedoptions"),
								"data" => "options",
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "wa_received", [
						[
							"db" => "receive_date",
							"dt" => "receive_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "id",
							"dt" => "id",
							"formatter" => function($value, $row){
								return "#{$value}";
							}
						],
						[
							"db" => "phone",
							"dt" => "phone",
							"formatter" => function($value){
								$this->cache->container("contacts." . logged_hash);

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getContacts(logged_id));
								endif;

								$contacts = $this->cache->getAll();

								$contactName = isset($contacts[$value]) ? $contacts[$value]["name"] : __("lang_table_unknown_data");
								$contactPhone = $value;

								return <<<HTML
								{$contactName}<br>
								{$contactPhone}
								HTML;
							}
						],
						[
							"db" => "message",
							"dt" => "message",
							"formatter" => function($value, $row){
								$messageStr = mb_strlen($value) > 50 ? mb_substr($value, 0, 20) . "...<br><button class=\"btn btn-sm btn-primary\" title=\"" . __("lang_dashboard_messages_tablefullmessage") . "\" zender-toggle=\"zender.view/wa.received-{$row["id"]}\">" . __("lang_dashboard_messages_tablefullmessage") . "</button>" : $value;
								$exportData = $this->sanitize->string($value, true);

								return <<<HTML
								<p export-data="{$exportData}">{$messageStr}</p>
								HTML;
							}
						],
						[
							"db" => "wid",
							"dt" => "account",
							"formatter" => function($value){
								$account = explode(":", $value);

								return "+{$account[0]}";
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								    	<button class="btn btn-sm btn-success" title="{$GLOBALS["__"]("lang_table_whatsappreceive_replytitle")}" zender-toggle="zender.whatsapp.quick" zender-reply="{$row["phone"]}">
								            <i class="la la-reply"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="wa.received/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					false,
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "whatsapp.scheduled":
				if(isset($request["structure"])):
					$structure = [
						"columns" => [
							[
								"title" => __("lang_table_waschedule_scheduletitle"),
								"data" => "send_date",
								"sortable" => false
							],
							[
								"title" => __("lang_table_scheduled_name"),
								"data" => "name"
							],
							[
								"title" => __("lang_dashboard_messages_tablesentmessage"),
								"data" => "message",
								"width" => "25%"
							],
							[
								"title" => __("lang_table_waschedule_detailstitle"),
								"data" => "details",
								"width" => "25%",
								"sortable" => false
							],
							[
								"title" => __("lang_dashboard_messages_tablesentoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "wa_scheduled", [
						[
							"db" => "send_date",
							"dt" => "send_date",
							"formatter" => function($value, $row){
								$createDate = date(logged_date_format, $value);
								$createTime = date(logged_clock_format, $value);
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"db" => "message",
							"dt" => "message",
							"formatter" => function($value){
								try {
									$msgDecode = json_decode($value, true, JSON_THROW_ON_ERROR);
									$waMessage = isset($msgDecode["text"]) ? $msgDecode["text"] : $msgDecode["caption"];
								} catch(Exception $e){
									$waMessage = $value;
								}

								return decodeBraces($waMessage);
							}
						],
						[
							"dt" => "details",
							"formatter" => function($row){
								$this->cache->container("groups." . logged_hash);

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getGroups(logged_id));
								endif;

								$groups = $this->cache->getAll();

								$scheduledRepeat = $row["repeat"] > 0 ? ($row["repeat"] == 1 ? "1 day" : "{$row["repeat"]} days") : __("lang_table_no_data");

								$lastSend = empty($row["last_send"]) ? __("lang_table_campaign_pendingstatus") : date(logged_date_format . " " . logged_clock_format, $row["last_send"]);

								$scheduledAccount = explode(":", $row["wid"]);

								$groupsHandle = $row["groups"] == "0" ? __("lang_table_none_data") : explode(",", $row["groups"]);

								if(is_array($groupsHandle)):
									foreach($groupsHandle as $group):
										$scheduledGroupsHolder[] = $groups[$group]["name"];
									endforeach;

									$scheduledGroups = implode(", ", $scheduledGroupsHolder);
								else:
									$scheduledGroups = $groupsHandle;
								endif;

								$scheduledNumbers = !empty($row["numbers"]) ? implode(", ", explode(",", $row["numbers"])) : __("lang_table_none_data");

								return <<<HTML
								<ul class="text-left">
								  <li>{$GLOBALS["__"]("lang_table_details_schedulednewrepeat")}<br>
								  	<code>{$scheduledRepeat}</code>
								  </li>
								  <li>{$GLOBALS["__"]("lang_table_wascheduled_lastsendinfo")}<br>
								  	<code>{$lastSend}</code>
								  </li>
								  <li>{$GLOBALS["__"]("lang_table_waschedule_detailsline2")}<br>
								  	<code>+{$scheduledAccount[0]}</code>
								  </li>
								  <li>{$GLOBALS["__"]("lang_table_waschedule_detailsline3")}<br>
								  	<code>{$scheduledGroups}</code>
								  </li>
								  <li>{$GLOBALS["__"]("lang_table_waschedule_detailsline4")}<br>
								  	<code>{$scheduledNumbers}</code>
								  </li>
								</ul>
								HTML;	
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.whatsapp.scheduled/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="wa.scheduled/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"wid",
						"groups",
						"numbers",
						"repeat",
						"last_send"
					],
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "whatsapp.groups":
				if(isset($request["structure"])):
					$structure = [
						"columns" => [
							[
								"title" => "ID",
								"data" => "id",
								"visible" => false
							],
							[
								"title" => __("lang_table_wagroups_nametitle"),
								"data" => "name"
							],
							[
								"title" => __("lang_table_wagroups_accounttitle"),
								"data" => "account",
								"width" => "25%"
							],
							[
								"title" => __("lang_table_wagroups_addresstitle"),
								"data" => "gid"
							],
							[
								"title" => __("lang_table_wagroups_optionstitle"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "wa_groups", [
						[
							"db" => "id",
							"dt" => "id"
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"db" => "wid",
							"dt" => "account",
							"formatter" => function($value){
								$wid = explode(":", $value);

								return "+{$wid[0]}";
							}
						],
						[	
							"db" => "gid",
							"dt" => "gid",
							"formatter" => function($value){
								return $value;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="wa.group/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					false,
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "whatsapp.accounts":
				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_waaccounts_loggedtitle"),
								"data" => "create_date"
							],	
							[
								"title" => __("lang_table_waaccounts_waidtitle"),
								"data" => "wid"
							],				
							[
								"title" => __("lang_table_waaccounts_wauniquetitle"),
								"data" => "unique"
							],
							[
								"title" => __("lang_table_waaccounts_statustitle"),
								"data" => "status",
								"searchable" => false,
								"sortable" => false,
								"width" => "20%"
							],
							[
								"title" => __("lang_dashboard_devices_tableregisteredoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "wa_accounts", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "wid",
							"dt" => "wid",
							"formatter" => function($value){
								$wid = explode(":", $value);
								return "+{$wid[0]}";
							}
						],
						[
							"db" => "unique",
							"dt" => "unique",
							"formatter" => function($value){
								return mb_strlen($value) > 30 ? mb_substr($value, 0, 30) . "..." : $value;
							}
						],
						[
							"dt" => "status",
							"formatter" => function($row){
								$this->wa->_guzzle = $this->guzzle;

								if($this->wa->check()):
									try {
										$status = $this->wa->status($row["unique"]);

										if($status):
							            	switch($status):
							            		case "connected":
							            			return "<i class=\"la la-signal text-success\"></i> {$GLOBALS["__"]("lang_table_waaccounts_statusconnected")}";
							            		case "connecting":
							            			return "<i class=\"la la-signal text-warning\"></i> {$GLOBALS["__"]("lang_table_waaccounts_statusconnecting")}";
							            		case "disconnecting":
							            			return "<i class=\"la la-signal text-warning\"></i> {$GLOBALS["__"]("lang_table_waaccounts_statusdisconnecting")}";
							            		default:
							            			return "<i class=\"la la-signal text-danger\"></i> {$GLOBALS["__"]("lang_table_waaccounts_statusdisconnected")}";
							            	endswitch;
								        else:
								        	return "<i class=\"la la-signal text-danger\"></i> {$GLOBALS["__"]("lang_table_waaccounts_statusdisconnected")}";
								        endif;
									} catch(Exception $e){
										return "<i class=\"la la-bug text-danger\"></i> {$GLOBALS["__"]("lang_table_waaccounts_statuserror")}";
									}
								else:	
									return "<i class=\"la la-bug text-danger\"></i> {$GLOBALS["__"]("lang_table_waaccounts_statuserror")}";
								endif;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								    	<button class="btn btn-sm btn-secondary" title="{$GLOBALS["__"]("lang_table_waaccounts_copyaccountid")}" data-clipboard-text="{$row["id"]}" zender-clipboard>
								            <i class="la la-copy"></i>
								        </button>
								    	<button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.whatsapp/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="wa.account/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id"
					],
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "android.ussd":
				if(isset($request["structure"])):
					$structure = [
						"history" => [
							"column" => "create_date"
						],
						"export" => [
							"export_columns" => [0, 1, 2, 3, 4, 5],
							"copy_title" => ___(__("lang_table_ussd_exportcopy"), [date("d-m-Y (g:s A)")]),
							"excel_filename" => ___(__("lang_table_ussd_exportexcel"), [date("d-m-Y (g:s A)")]),
							"pdf_filename" => ___(__("lang_table_ussd_exportpdf"), [date("d-m-Y (g:s A)")])
						],
						"columns" => [
							[
								"title" => __("lang_table_ussd_createdtitle"),
								"data" => "create_date",
								"width" => "15%"
							],
							[
								"title" => __("lang_table_ussd_codetitle"),
								"data" => "code"
							],
							[
								"title" => __("lang_table_ussd_responsetitle"),
								"data" => "response",
								"width" => "30%"
							],
							[
								"title" => __("lang_table_ussd_simtitle"),
								"data" => "sim",
								"width" => "10%"
							],
							[
								"title" => __("lang_table_ussd_statustitle"),
								"data" => "status",
								"sortable" => false
							],
							[
								"title" => __("lang_table_ussd_devicetitle"),
								"data" => "did",
								"sortable" => false
							],
							[
								"title" => __("lang_table_ussd_optionstitle"),
								"data" => "options",
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "ussd", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "code",
							"dt" => "code",
							"formatter" => function($value){
								return empty($value) ? __("lang_table_ussd_pendingresponsetext") : $value;
							}
						],
						[
							"db" => "response",
							"dt" => "response",
							"formatter" => function($value){
								return empty($value) ? __("lang_table_empty_data") : $value;
							}
						],
						[
							"db" => "sim",
							"dt" => "sim",
							"formatter" => function($value){
								return $value < 2 ? __("lang_table_ussd_sim1") : __("lang_table_ussd_sim2"); 
							}
						],
						[
							"db" => "status",
							"dt" => "status",
							"formatter" => function($value){
								switch($value):
									case 1:
										$ussdStatus = "<span class=\"badge badge-info\" export-data=\"{$GLOBALS["__"]("lang_table_ussd_pendinglabel")}\">{$GLOBALS["__"]("lang_table_ussd_pendinglabel")}</span>";
										break;
									case 2:
										$ussdStatus = "<span class=\"badge badge-warning\" export-data=\"{$GLOBALS["__"]("lang_table_ussd_queuelabel")}\">{$GLOBALS["__"]("lang_table_ussd_queuelabel")}</span>";
										break;
									case 3:
										$ussdStatus = "<span class=\"badge badge-success\" export-data=\"{$GLOBALS["__"]("lang_table_ussd_completelabel")}\">{$GLOBALS["__"]("lang_table_ussd_completelabel")}</span>";
										break;
									default:
										$ussdStatus = "<span class=\"badge badge-danger\" export-data=\"{$GLOBALS["__"]("lang_table_unknown_data")}\">{$GLOBALS["__"]("lang_table_unknown_data")}</span>";
								endswitch;

								return $ussdStatus;
							}
						],
						[
							"db" => "did",
							"dt" => "did",
							"formatter" => function($value){
								$this->cache->container("user." . logged_hash);

								if(!$this->cache->has("devices")):
									$this->cache->set("devices", $this->table->getDevices(logged_id));
								endif;

								$devices = $this->cache->get("devices");
								$device = isset($devices[$value]) ? $devices[$value]["name"] : __("lang_table_removed_data");

								switch(isset($devices[$value]) ? $devices[$value]["version"] : false):
									case 4:
										$android = __("lang_table_androidver_kitkat");
										break;
									case 5:
										$android = __("lang_table_androidver_lollipop");
										break;
									case 6:
										$android = __("lang_table_androidver_marshmallow");
										break;
									case 7:
										$android = __("lang_table_androidver_nougat");
										break;
									case 8:
										$android = __("lang_table_androidver_oreo");
										break;
									case 9:
										$android = __("lang_table_androidver_pie");
										break;
									case 10:
										$android = __("lang_table_androidver_10");
										break;
									case 11:
										$android = __("lang_table_androidver_11");
										break;
									case 12:
										$android = __("lang_table_androidver_12");
										break;
									case 13:
										$android = __("lang_table_androidver_13");
										break;
									default:
										$android = __("lang_table_unknown_data");
								endswitch;

								return <<<HTML
								<ul class="text-left">
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_ussd_detailsline1"), [$device])}</li>
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_ussd_detailsline2"), [$android])}</li>
								</ul>
								HTML;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="ussd/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"uid"
					],
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "android.notifications":
				if(isset($request["structure"])):
					$structure = [
						"history" => [
							"column" => "create_date"
						],
						"export" => [
							"export_columns" => [0, 1, 2, 3],
							"copy_title" => ___(__("lang_table_notifications_exportcopy"), [date("d-m-Y (g:s A)")]),
							"excel_filename" => ___(__("lang_table_notifications_exportexcel"), [date("d-m-Y (g:s A)")]),
							"pdf_filename" => ___(__("lang_table_notifications_exportpdf"), [date("d-m-Y (g:s A)")])
						],
						"columns" => [
							[
								"title" => __("lang_table_notifications_createdtitle"),
								"data" => "create_date",
								"width" => "20%"
							],
							[
								"title" => __("lang_table_notifications_packagetitle"),
								"data" => "package"
							],
							[
								"title" => __("lang_table_notifications_title"),
								"data" => "title",
								"width" => "20%"
							],
							[
								"title" => __("lang_table_notification_texttitle"),
								"data" => "text",
								"width" => "40%",
								"sortable" => false
							],
							[
								"title" => __("lang_table_notification_devicetitle"),
								"data" => "device",
								"width" => "25%"
							],
							[
								"title" => __("lang_table_notification_optiontitle"),
								"data" => "options",
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "notifications", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "package",
							"dt" => "package"
						],
						[
							"db" => "title",
							"dt" => "title",
							"formatter" => function($value){
								return empty($value) ? __("lang_table_empty_data") : $value;
							}
						],
						[
							"db" => "text",
							"dt" => "text",
							"formatter" => function($value){
								return empty($value) ? __("lang_table_empty_data") : $value;
							}
						],
						[
							"db" => "did",
							"dt" => "device",
							"formatter" => function($value){
								$this->cache->container("user." . logged_hash);

								if(!$this->cache->has("devices")):
									$this->cache->set("devices", $this->table->getDevices(logged_id));
								endif;

								$devices = $this->cache->get("devices");
								$device = isset($devices[$value]) ? $devices[$value]["name"] : __("lang_table_removed_data");

								switch(isset($devices[$value]) ? $devices[$value]["version"] : false):
									case 4:
										$android = __("lang_table_androidver_kitkat");
										break;
									case 5:
										$android = __("lang_table_androidver_lollipop");
										break;
									case 6:
										$android = __("lang_table_androidver_marshmallow");
										break;
									case 7:
										$android = __("lang_table_androidver_nougat");
										break;
									case 8:
										$android = __("lang_table_androidver_oreo");
										break;
									case 9:
										$android = __("lang_table_androidver_pie");
										break;
									case 10:
										$android = __("table_androidver_10");
										break;
									case 11:
										$android = __("lang_table_androidver_11");
										break;
									case 12:
										$android = __("lang_table_androidver_12");
										break;
									case 13:
										$android = __("lang_table_androidver_13");
										break;
									default:
										$android = __("lang_table_unknown_data");
								endswitch;

								$exportData = $this->sanitize->string(___(__("lang_table_notifications_exportone"), [$device, $android]), true);

								return <<<HTML
								<ul class="text-left" export-data="{$exportData}">
								  <li>
								  	{$GLOBALS["__"]("lang_table_notification_devicelabel")}<br>
								  	<code>{$device}</code>
								  </li>
								  <li>
								  	{$GLOBALS["__"]("lang_table_notification_oslabel")}<br>
								  	<code>{$android}</code>
								  </li>
								</ul>
								HTML;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="notification/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id"
					],
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "android.devices":
				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_dashboard_devices_tableregisteredadded"),
								"data" => "create_date"
							],					
							[
								"title" => __("lang_table_devices_nametitle"),
								"data" => "name"
							],
							[
								"title" => __("lang_table_android_brandtitle"),
								"data" => "manufacturer"
							],
							[
								"title" => __("lang_dashboard_devices_tableregisteredversion"),
								"data" => "version"
							],
							[
								"title" => __("lang_table_android_partnertitle"),
								"data" => "global"
							],
							[
								"title" => __("lang_table_android_statustitle"),
								"data" => "status"
							],
							[
								"title" => __("lang_dashboard_devices_tableregisteredoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "devices", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"db" => "manufacturer",
							"dt" => "manufacturer"
						],
						[
							"db" => "version",
							"dt" => "version",
							"formatter" => function($value){
								switch(round((int) $value)):
									case 4:
										$android = __("lang_table_androidver_kitkat");
										break;
									case 5:
										$android = __("lang_table_androidver_lollipop");
										break;
									case 6:
										$android = __("lang_table_androidver_marshmallow");
										break;
									case 7:
										$android = __("lang_table_androidver_nougat");
										break;
									case 8:
										$android = __("lang_table_androidver_oreo");
										break;
									case 9:
										$android = __("lang_table_androidver_pie");
										break;
									case 10:
										$android = __("table_androidver_10");
										break;
									case 11:
										$android = __("lang_table_androidver_11");
										break;
									case 12:
										$android = __("lang_table_androidver_12");
										break;
									case 13:
										$android = __("lang_table_androidver_13");
										break;
									default:
										$android = __("lang_table_unknown_data");
								endswitch;

								return $android;
							}
						],
						[
							"db" => "global_device",
							"dt" => "global",
							"formatter" => function($value){
								return $value < 2 ? __("lang_table_enabled_data") : __("lang_table_disabled_data"); 
							}
						],
						[
							"db" => "online_id",
							"dt" => "status",
							"formatter" => function($value){
								try {
				                    $this->echo->_cache = $this->cache;
				                    $this->echo->_guzzle = $this->guzzle;

				                    $echoToken = $this->echo->token();
				                } catch(Exception $e){
				                    return "<i class=\"la la-signal text-danger\"></i> {$GLOBALS["__"]("lang_table_android_statusoffline")}";
				                }

								return $echoToken ? ($this->echo->status($value) ? "<i class=\"la la-signal text-success\"></i> {$GLOBALS["__"]("lang_table_android_statusonline")}" : "<i class=\"la la-signal text-danger\"></i> {$GLOBALS["__"]("lang_table_android_statusoffline")}") : "<i class=\"la la-signal text-danger\"></i> {$GLOBALS["__"]("lang_table_android_statusoffline")}"; 
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								    	<button class="btn btn-sm btn-secondary" title="{$GLOBALS["__"]("lang_table_android_copydeviceid")}" data-clipboard-text="{$row["did"]}" zender-clipboard>
								            <i class="la la-copy"></i>
								        </button>
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.device/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="devices/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"did"
					],
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "contacts.saved":
				if(isset($request["structure"])):
					$structure = [
						"export" => [
							"export_columns" => [0, 1, 2, 3],
							"copy_title" => ___(__("lang_table_contactssaved_exportcopy"), [date("d-m-Y (g:s A)")]),
							"excel_filename" => ___(__("lang_table_table_contactssaved_exportexcel"), [date("d-m-Y (g:s A)")]),
							"pdf_filename" => ___(__("lang_table_table_contactssaved_exportpdf"), [date("d-m-Y (g:s A)")])
						],
						"columns" => [
							[
								"title" => __("lang_table_contactssaved_idtitle"),
								"data" => "id"
							],
							[
								"title" => __("lang_dashboard_contacts_tablesavedname"),
								"data" => "name"
							],
							[
								"title" => __("lang_dashboard_contacts_tablesavednumber"),
								"data" => "phone"
							],
							[
								"title" => __("lang_table_contactssaved_grouptitle"),
								"data" => "groups"
							],
							[
								"title" => __("lang_dashboard_contacts_tablesavedoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "contacts", [
						[
							"db" => "id",
							"dt" => "id",
							"formatter" => function($value){
								return "#{$value}";
							}
						],
						[
							"db" => "name",
							"dt" => "name",
							"formatter" => function($value){
								return ucwords($value);
							}
						],
						[
							"db" => "phone",
							"dt" => "phone"
						],
						[
							"db" => "groups",
							"dt" => "groups",
							"formatter" => function($value){
								$this->cache->container("groups." . logged_hash);

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getGroups(logged_id));
								endif;

								$groups = $this->cache->getAll();

								$groupsArray = explode(",", $value);
								$groupsContainer = [];

								foreach($groupsArray as $group):
									try {
										$groupsContainer[] = $groups[$group]["name"];
									} catch(Exception $e){
										// Ignore
									}
								endforeach;

								if(empty($groupsContainer)):
									return "<code>{$GLOBALS["__"]("lang_table_none_data")}</code>";
								else:
									return "<code>" . implode(", ", $groupsContainer) . "</code>";
								endif;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.contact/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="contacts/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					false,
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "contacts.groups":
				if(isset($request["structure"])):
					$structure = [
						"export" => [
							"export_columns" => [0, 1],
							"copy_title" => ___(__("lang_table_contactssaved_exportcopy"), [date("d-m-Y (g:s A)")]),
							"excel_filename" => ___(__("lang_table_contactssaved_exportexcel"), [date("d-m-Y (g:s A)")]),
							"pdf_filename" => ___(__("lang_table_contactssaved_exportpdf"), [date("d-m-Y (g:s A)")])
						],
						"columns" => [
							[
								"title" => __("lang_table_contactsgroups_gidtitle"),
								"data" => "id"
							],
							[
								"title" => __("lang_dashboard_contacts_tablegroupsname"),
								"data" => "name"
							],
							[
								"title" => __("lang_dashboard_contacts_tablegroupsoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "groups", [
						[
							"db" => "id",
							"dt" => "id",
							"formatter" => function($value){
								return "#{$value}";
							}
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.group/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="groups/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					false,
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "contacts.unsubscribed":
				if(isset($request["structure"])):
					$structure = [
						"export" => [
							"export_columns" => [0, 1],
							"copy_title" => ___(__("lang_table_contactsunsub_exportcopy"), [date("d-m-Y (g:s A)")]),
							"excel_filename" => ___(__("lang_table_contactsunsub_exportexcel"), [date("d-m-Y (g:s A)")]),
							"pdf_filename" => ___(__("lang_table_contactsunsub_exportpdf"), [date("d-m-Y (g:s A)")])
						],
						"columns" => [
							[
								"title" => __("lang_table_contactsunsubscribed_idtitle"),
								"data" => "id"
							],
							[
								"title" => __("lang_table_contactsunsubscribed_phonetitle"),
								"data" => "phone"
							],
							[
								"title" => __("lang_dashboard_contacts_tablegroupsoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "unsubscribed", [
						[
							"db" => "id",
							"dt" => "id",
							"formatter" => function($value){
								return "#{$value}";
							}
						],
						[
							"db" => "phone",
							"dt" => "phone",
							"formatter" => function($value, $row){
								$this->cache->container("contacts." . logged_hash);

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getContacts(logged_id));
								endif;

								$contacts = $this->cache->getAll();

								$contactName = isset($contacts[$row["phone"]]) ? $contacts[$row["phone"]]["name"] : __("lang_table_unknown_data");

								return "{$contactName}<br>{$value}";
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="unsubscribed/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					false,
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "tools.keys":
				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_toolskeys_createdtitle"),
								"data" => "create_date"
							],	
							[
								"title" => __("lang_dashboard_tools_tablekeysname"),
								"data" => "name"
							],	
							[
								"title" => __("lang_dashboard_tools_tablekeyspermissions"),
								"data" => "permissions",
								"width" => "30%"
							],
							[
								"title" => __("lang_dashboard_tools_tablekeysoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "keys", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"db" => "permissions",
							"dt" => "permissions",
							"formatter" => function($value){
								$permissions = explode(",", $value);
								return "<code>" . implode(", ", $permissions) . "</code>";
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-secondary" title="{$GLOBALS["__"]("lang_table_toolskeys_copyapisecret")}" data-clipboard-text="{$row["secret"]}" zender-clipboard>
								            <i class="la la-copy"></i>
								        </button>
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.apikey/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="keys/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"secret"
					],
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "tools.webhooks":
				if(isset($request["structure"])):
					$structure = [
						"columns" => [
							[
								"title" => __("lang_table_toolswebhooks_createtitle"),
								"data" => "create_date"
							],					
							[
								"title" => __("lang_dashboard_tools_tablehooksname"),
								"data" => "name"
							],
							[
								"title" => __("lang_dashboard_tools_tablehooksurl"),
								"data" => "url"
							],
							[
								"title" => __("lang_table_tootlswebhooks_eventstitle"),
								"data" => "events"
							],
							[
								"title" => __("lang_dashboard_tools_tablehooksoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "webhooks", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"db" => "url",
							"dt" => "url",
							"formatter" => function($value, $row){
								return <<<HTML
								{$value}<br>
								<button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_toolswebhooks_simulatedesc")}" zender-action="trigger" webhook-link="{$value}" webhook-secret="{$row["secret"]}">{$GLOBALS["__"]("lang_table_toolswebhooks_simulatebtn")}</button>
								HTML;
							}
						],
						[
							"db" => "events",
							"dt" => "events",
							"formatter" => function($value){
								$events = explode(",", $value);
								return "<code>" . implode(", ", $events) . "</code>";
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-secondary" title="{$GLOBALS["__"]("lang_table_toolswebhooks_copywebhooksecret")}" data-clipboard-text="{$row["secret"]}" zender-clipboard>
								            <i class="la la-copy"></i>
								        </button>
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.webhook/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="webhooks/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"secret"
					],
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "tools.actions":
				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_toolsactions_createtitle"),
								"data" => "create_date"
							],					
							[
								"title" => __("lang_table_action_name"),
								"data" => "name"
							],
							[
								"title" => __("lang_table_toolsactions_detailstitle"),
								"data" => "details",
								"width" => "30%",
								"searchable" => false,
								"sortable" => false
							],
							[
								"title" => __("lang_dashboard_tools_tablehooksoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "actions", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"dt" => "details",
							"formatter" => function($row){
								switch($row["type"]):
									case 1:
										$actionType = __("lang_table_toolsactions_typehook");
										break;
									case 2:
										$actionType = __("lang_table_toolsactions_typeautoreply");
										break;
									default:
										$actionType = __("lang_table_unknown_data");
								endswitch;	

								if(!empty($row["source"])):
									$actionSource = "<li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_toolsactions_source"), [$row["source"] < 2 ? __("lang_table_toolsactions_sourcesms") : __("lang_table_toolsactions_sourcewa")])}</li>";
								else:
									$actionSource = false;
								endif;

								if(!empty($row["event"])):
									$actionEvent = "<li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_toolsactions_event"), [$row["event"] < 2 ? __("lang_table_toolsactions_eventonsend") : __("lang_table_toolsactions_eventonreceive")])}</li>";
								else:
									$actionEvent = false;
								endif;

								if(!empty($row["link"])):
									$actionLink = "<li>{$GLOBALS["__"]("lang_table_toolsactions_link")}<br><code>{$row["link"]}</code></li>";
								else:
									$actionLink = false;
								endif;

								if(!empty($row["keywords"])):
									$actionKeywords = "<li>{$GLOBALS["__"]("lang_table_toolsactions_keywords")}<br><code>{$row["keywords"]}</code></li>";
								else:
									$actionKeywords = false;
								endif;

								if(!empty($row["message"])):
									$actionMessage = "<li>{$GLOBALS["__"]("lang_table_toolsactions_message")}<br><code>{$row["message"]}</code></li>";
								else:
									$actionMessage = false;
								endif;

								return <<<HTML
								<ul class="text-left">
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_toolsactions_type"), [$actionType])}</li>
								  {$actionSource}
								  {$actionEvent}
								  {$actionLink}
								  {$actionKeywords}
								  {$actionMessage}
								</ul>
								HTML;	
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								$type = $row["type"] < 2 ? "hook" : "autoreply";
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.{$type}/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="actions/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"type",
						"source",
						"event",
						"link",
						"keywords",
						"message"
					],
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "tools.templates":
				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_toolstemplates_idtitle"),
								"data" => "id",
								"visible" => false
							],
							[
								"title" => __("lang_dashboard_messages_tabletemplatesname"),
								"data" => "name",
							],
							[
								"title" => __("lang_dashboard_messages_tabletemplatesformat"),
								"data" => "format",
								"width" => "35%"
							],
							[
								"title" => __("lang_dashboard_messages_tabletemplatesoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "templates", [
						[
							"db" => "id",
							"dt" => "id"
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"db" => "format",
							"dt" => "format",
							"formatter" => function($value){
								return "<p>{$value}</p>";
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.template/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="templates/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					false,
					[
						"uid = " . logged_id
					]);
				endif;

				break;
			case "rates.partners":
				if(isset($request["structure"])):
					$structure = [
						"search" => [
							"disable" => true
						],
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_ratespartners_createdtitle"),
								"data" => "create_date",
								"visible" => false
							],
							[
								"title" => __("lang_table_ratespartners_countrytitle"),
								"data" => "country"
							],		
							[
								"title" => __("lang_table_partnerrates_devicename"),
								"data" => "name"
							],			
							[
								"title" => __("lang_table_ratespartners_detailstitle"),
								"data" => "details",
								"sortable" => false
							],
							[
								"title" => __("lang_table_ratespartners_ratetitle"),
								"data" => "rate"
							],
							[
								"title" => __("lang_table_ratespartners_prioritytitle"),
								"data" => "priority"
							],
							[
								"title" => __("lang_table_ratespartners_optionstitle"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "devices", [
						[
							"db" => "create_date",
							"dt" => "create_date"
						],
						[
							"db" => "country",
							"dt" => "country",
							"formatter" => function($value){
								$countries = \CountryCodes::get("alpha2", "country");
								$code = strtolower($value);

								return <<<HTML
								<i class="flag-icon flag-icon-{$code}"></i> $countries[$value]
								HTML;
							}
						],
						[
							"db" => "name",
							"dt" => "name",
							"formatter" => function($value){
								return $value;
							}
						],
						[
							"dt" => "details",
							"formatter" => function($row){
								$getEmail = $this->system->getEmail($row["uid"]);
								$userEmail = $getEmail ? $getEmail : __("lang_table_unknown_data");

								return <<<HTML
								<ul class="text-left">
								  <li>
								  	{$GLOBALS["__"]("lang_table_ratespartners_owner")}<br>
								  	<code>{$userEmail}</code>
								  </li>
								</ul>
								HTML; 
							}
						],
						[
							"db" => "rate",
							"dt" => "rate",
							"formatter" => function($value, $row){
								$currency = country($row["country"])->getCurrency()["iso_4217_code"];
								$systemCurrency = strtoupper(system_currency);

								$this->cache->container("system.payments", true);

								if(!$this->cache->has("exchange")):
									try {
							            $exchange = json_decode($this->guzzle->get(titansys_api . "/currency?code=" . system_purchase_code, [
							                "allow_redirects" => true,
							                "http_errors" => false
							            ])->getBody()->getContents(), true);


							            if($exchange["status"] == 200):
							            	$this->cache->set("exchange", $exchange, 43200);
							            else:
							            	return __("lang_table_unknown_data");
							            endif;
							        } catch(Exception $e){
							            return __("lang_table_unknown_data");
							        }
							    endif;

							    $rates = $this->cache->get("exchange");

							    $base_rate = $rates["data"]["USD"] / $rates["data"][strtoupper($currency)];
						        $usd_price = ($base_rate * $value) * $rates["data"]["USD"];
						        $final_price = round(abs($usd_price * $rates["data"][strtoupper(system_currency)]), 2);

								return <<<HTML
								<ul class="text-left">
								  <li>
								  	{$GLOBALS["__"]("lang_table_ratespartners_localrate")}<br>
								  	<code>{$value} {$currency}</code>
								  </li>
								  <li>
								  	{$GLOBALS["__"]("lang_table_ratespartners_convertedrate")}<br>
								  	<code>{$final_price} {$systemCurrency}</code>
								  </li>
								</ul>
								HTML; 
							}
						],
						[
							"db" => "global_priority",
							"dt" => "priority",
							"formatter" => function($value){
								return $value < 2 ? __("lang_table_yes_data") : __("lang_table_no_data");
							}
						],
						[
							"db" => "global_slots",
							"dt" => "slots",
							"formatter" => function($value){
								$slots = explode(",", $value);

								return count($slots) > 1 ? __("lang_table_ratespartners_simdual") : ___(__("lang_table_ratespartners_simsingle"), [$slots[0]]);
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								    	<button class="btn btn-sm btn-secondary" title="{$GLOBALS["__"]("lang_table_android_copydeviceid")}" data-clipboard-text="{$row["did"]}" zender-clipboard>
								            <i class="la la-copy"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"uid",
						"did",
						"name"
					],
					[
						"global_device < 2"
					]);
				endif;

				break;
			case "administration.users":
				if(!permission("manage_users"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"history" => [
							"column" => "create_date"
						],
						"export" => [
							"export_columns" => [0, 1, 2, 3],
							"copy_title" =>  ___(__("lang_table_adminusers_exportcopy"), [date("d-m-Y (g:s A)")]),
							"excel_filename" => ___(__("lang_table_adminusers_exportexcel"), [date("d-m-Y (g:s A)")]),
							"pdf_filename" => ___(__("lang_table_adminusers_exportpdf"), [date("d-m-Y (g:s A)")])
						],
						"columns" => [
							[
								"title" => __("lang_tables_users_registered"),
								"data" => "create_date"
							],					
							[
								"title" => __("lang_dashboard_admin_tableusersname"),
								"data" => "name"
							],
							[
								"title" => __("lang_dashboard_admin_tableusersemail"),
								"data" => "email"
							],
							[
								"title" => __("lang_tables_users_details"),
								"data" => "details",
								"width" => "20%",
								"sortable" => false
							],
							[
								"title" => __("lang_dashboard_admin_tableusersoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "users", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"db" => "email",
							"dt" => "email"
						],
						[
							"dt" => "details",
							"formatter" => function($row){
								if($row["country"])
									$userCountry = $row["country"];
								else
									$userCountry = __("lang_table_unknown_data");

								$userRole = $this->system->getRole($row["role"]);
								if($userRole)
									$userRole = $userRole["name"];
								else
									$userRole = __("lang_table_unknown_data");

								$userLanguage = $this->system->getLanguage($row["language"]);
								if($userLanguage)
									$userLanguage = $userLanguage["name"];
								else
									$userLanguage = __("lang_table_unknown_data");

								$userTimezone = strtoupper($row["timezone"]);
								$userEarnings = number_format($row["earnings"]);
								$userCredits = number_format($row["credits"]);
								$soundState = $row["alertsound"] < 2 ? __("lang_table_enabled_data") : __("lang_table_disabled_data");
								$suspendState = $row["suspended"] > 0 ? __("lang_table_yes_data") : __("lang_table_no_data");

								$subscription = set_subscription(
				                    $this->system->checkSubscription($row["id"]), 
				                    $this->system->getSubscription(false, $row["id"]), 
				                    $this->system->getSubscription(false, false, true)
				                );

				                if($subscription)
				                	$userSubscription = $subscription["name"];
				                else
				                	$userSubscription = __("lang_table_none_data");

				                $exportData = $this->sanitize->string(___(__("lang_table_adminusersexportdata"), [$userRole, $userCountry, $userLanguage, $userTimezone, $userEarnings, $userCredits, $userSubscription, $soundState, $suspendState]), true);

								return <<<HTML
								<ul class="text-left" export-data="{$exportData}">
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_adminusers_exportdata1"), [$userRole])}</li>
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_adminusers_exportdata2"), [$userCountry])}</li>
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_adminusers_exportdata3"), [$userLanguage])}</li>
								  <li>{$GLOBALS["__"]("lang_table_adminusers_exportdata4")}<br>
								  	<code>{$userTimezone}</code>
								  </li>
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_adminusers_exportdata5"), [$userEarnings])}</li>
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_adminusers_exportdata6"), [$userCredits])}</li>
								  <li>{$GLOBALS["__"]("lang_table_adminusers_exportdata7")}<br>
								  	<code>{$userSubscription}</code>
								  </li>
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_adminusers_exportdata8"), [$soundState])}</li>
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_adminusers_exportdata9"), [$suspendState])}</li>
								</ul>
								HTML;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								$suspendButton = $row["suspended"] > 0 ? "success" : "danger";
								$suspendAction = $row["suspended"] > 0 ? "unsuspend" : "suspend";
								$suspendTitle = $row["suspended"] > 0 ? __("lang_table_adminusers_unsuspendaccount") : __("lang_table_adminusers_suspendaccount");
								$suspendState = $row["id"] < 2 ? "disabled" : "zender-action=\"{$suspendAction}\"";
								$deleteState = $row["id"] < 2 ? "disabled" : "zender-delete=\"users/{$row["id"]}\"";

								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-{$suspendButton}" title="{$suspendTitle}" user-id="{$row["id"]}" {$suspendState}>
								        	<i class="la la-ban"></i>
								        </button>
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.user/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" {$deleteState}>
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"role",
						"country",
						"earnings",
						"credits",
						"language",
						"alertsound",
						"suspended",
						"timezone"
					]);
				endif;

				break;
			case "administration.roles":
				if(!permission("manage_roles"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_adminroles_idtitle"),
								"data" => "id",
								"visible" => false
							],					
							[
								"title" => __("lang_table_role_name"),
								"data" => "name",
								"width" => "20%"
							],
							[
								"title" => __("lang_table_role_permissions"),
								"data" => "permissions"
							],
							[
								"title" => __("lang_dashboard_admin_tableusersoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "roles", [
						[
							"db" => "id",
							"dt" => "id"
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"db" => "permissions",
							"dt" => "permissions",
							"formatter" => function($value){
								return strtoupper(empty($value) ? "default_permissions" : implode(", ", explode(",", $value)));
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								$editRole = $row["id"] < 2 ? "disabled" : "zender-toggle=\"zender.edit.role/{$row["id"]}\"";
								$deleteRole = $row["id"] < 2 ? "disabled" : "zender-delete=\"roles/{$row["id"]}\"";

								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" {$editRole}>
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" {$deleteRole}>
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					false);
				endif;

				break;
			case "administration.packages":
				if(!permission("manage_packages"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_adminpackages_createdtitle"),
								"data" => "create_date"
							],					
							[
								"title" => __("lang_dashboard_admin_tablepackagesname"),
								"data" => "name",
								"width" => "20%"
							],
							[
								"title" => __("lang_dashboard_admin_tablepackagesprice"),
								"data" => "price"
							],
							[
								"title" => __("lang_table_adminpackages_detailstitle"),
								"data" => "details",
								"sortable" => false
							],
							[
								"title" => __("lang_dashboard_admin_tablepackagesoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "packages", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"db" => "price",
							"dt" => "price",
							"formatter" => function($value, $row){
								return $row["id"] < 2 ? __("lang_table_adminpackages_freelabel") : "{$value} " . system_currency;
							}
						],
						[
							"dt" => "details",
							"formatter" => function($row){
								$packagesHidden = $row["hidden"] < 2 ? __("lang_table_yes_data") : __("lang_table_no_data");
								$packagesFootermark = $row["footermark"] < 2 ? __("lang_table_enabled_data") : __("lang_table_disabled_data");
								$packagesSend = $row["send_limit"] < 1 ? __("lang_table_unlimited_data") : number_format($row["send_limit"]);
								$packagesReceive = $row["receive_limit"] < 1 ? __("lang_table_unlimited_data") : number_format($row["receive_limit"]);
								$packagesContact = $row["contact_limit"] < 1 ? __("lang_table_unlimited_data") : number_format($row["contact_limit"]);
								$packagesDevice = $row["device_limit"] < 1 ? __("lang_table_unlimited_data") : number_format($row["device_limit"]);
								$packagesKey = $row["key_limit"] < 1 ? __("lang_table_unlimited_data") : number_format($row["key_limit"]);
								$packagesWebhook = $row["webhook_limit"] < 1 ? __("lang_table_unlimited_data") : number_format($row["webhook_limit"]);
								$packagesAction = $row["action_limit"] < 1 ? __("lang_table_unlimited_data") : number_format($row["action_limit"]);
								$packagesScheduled = $row["scheduled_limit"] < 1 ? __("lang_table_unlimited_data") : number_format($row["scheduled_limit"]);
								$packagesWASend = $row["wa_send_limit"] < 1 ? __("lang_table_unlimited_data") : number_format($row["wa_send_limit"]);
								$packagesWAReceive = $row["wa_receive_limit"] < 1 ? __("lang_table_unlimited_data") : number_format($row["wa_receive_limit"]);
								$packagesWAAccount = $row["wa_account_limit"] < 1 ? __("lang_table_unlimited_data") : number_format($row["wa_account_limit"]);
								$packagesUSSD = $row["ussd_limit"] < 1 ? __("lang_table_unlimited_data") : number_format($row["ussd_limit"]);
								$packagesNotification = $row["notification_limit"] < 1 ? __("lang_table_unlimited_data") : number_format($row["notification_limit"]);

								return <<<HTML
								<ul class="text-left">
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails1")}<br><code>{$packagesHidden}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails2")}<br><code>{$packagesFootermark}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails3")}<br><code>{$packagesSend}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails4")}<br><code>{$packagesReceive}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails5")}<br><code>{$packagesWASend}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails6")}<br><code>{$packagesWAReceive}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails7")}<br><code>{$packagesWAAccount}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails8")}<br><code>{$packagesUSSD}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails9")}<br><code>{$packagesNotification}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails10")}<br><code>{$packagesDevice}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails11")}<br><code>{$packagesContact}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails12")}<br><code>{$packagesKey}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails13")}<br><code>{$packagesWebhook}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails14")}<br><code>{$packagesAction}</code></li>
								  <li>{$GLOBALS["__"]("lang_table_adminpackages_labeldetails15")}<br><code>{$packagesScheduled}</code></li>
								</ul>
								HTML;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								if($row["id"] < 2):
									$packageDelete = "disabled";
								else:
									$packageDelete = "title=\"{$GLOBALS["__"]("lang_table_title_deletethisitem")}\" zender-delete=\"packages/{$row["id"]}\"";
								endif;

								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.package/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" {$packageDelete}>
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"send_limit",
						"receive_limit",
						"ussd_limit",
						"notification_limit",
						"contact_limit",
						"device_limit",
						"key_limit",
						"webhook_limit",
						"action_limit",
						"scheduled_limit",
						"wa_send_limit",
						"wa_receive_limit",
						"wa_account_limit",
						"footermark",
						"hidden"
					]);
				endif;

				break;
			case "administration.vouchers":
				if(!permission("manage_vouchers"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"export" => [
							"export_columns" => [0, 1, 2, 3, 4],
							"copy_title" => ___(__("lang_table_adminvouchers_exportcopy"), [date("d-m-Y (g:s A)")]),
							"excel_filename" => ___(__("lang_table_adminvouchers_exportexcel"), [date("d-m-Y (g:s A)")]),
							"pdf_filename" => ___(__("lang_table_adminvouchers_exportpdf"), [date("d-m-Y (g:s A)")])
						],
						"columns" => [
							[
								"title" => __("lang_table_adminvouchers_createdtitle"),
								"data" => "create_date"
							],		
							[
								"title" => __("lang_table_adminvouchers_codetitle"),
								"data" => "code",
								"visible" => false
							],			
							[
								"title" => __("lang_table_voucher_name"),
								"data" => "name"
							],
							[
								"title" => __("lang_table_vouchersduration"),
								"data" => "duration"
							],
							[
								"title" => __("lang_table_voucher_package"),
								"data" => "package"
							],
							[
								"title" => __("lang_dashboard_admin_tableusersoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "vouchers", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "code",
							"dt" => "code"
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"db" => "duration",
							"dt" => "duration",
							"formatter" => function($value){
								return $value;
							}
						],
						[
							"db" => "package",
							"dt" => "package",
							"formatter" => function($value){
								$this->cache->container("system.packages");

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getPackages());
								endif;

								$packages = $this->cache->getAll();

								return isset($packages[$value]) ? $packages[$value]["name"] : __("lang_table_removed_data");
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								    	<button class="btn btn-sm btn-secondary" title="{$GLOBALS["__"]("lang_table_adminvouchers_copycode")}" data-clipboard-text="{$row["code"]}" zender-clipboard>
								            <i class="la la-wallet"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="vouchers/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id"
					]);
				endif;

				break;
			case "administration.subscriptions":
				if(!permission("manage_subscriptions"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_adminsubscriptions_createdtitle"),
								"data" => "create_date"
							],					
							[
								"title" => __("lang_dashboard_admin_tablesubscriptionsuser"),
								"data" => "email"
							],
							[
								"title" => __("lang_dashboard_admin_tablesubscriptionspackage"),
								"data" => "package"
							],
							[
								"title" => __("lang_dashboard_admin_tablesubscriptionsprice"),
								"data" => "price"
							],
							[
								"title" => __("lang_dashboard_admin_tablesubscriptionsexpire"),
								"data" => "expire_date"
							],
							[
								"title" => __("lang_dashboard_admin_tablesubscriptionsoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "subscriptions", [
						[
							"db" => "date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"dt" => "email",
							"formatter" => function($row){
								$this->cache->container("system.users");

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getUsers());
								endif;

								$users = $this->cache->getAll();

								return isset($users[$row["uid"]]) ? $users[$row["uid"]]["email"] : __("lang_table_removed_data");
							}
						],
						[
							"dt" => "package",
							"formatter" => function($row){
								$this->cache->container("system.packages");

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getPackages());
								endif;

								$packages = $this->cache->getAll();

								return isset($packages[$row["pid"]]) ? $packages[$row["pid"]]["name"] : __("lang_table_removed_data");
							}
						],
						[
							"dt" => "price",
							"formatter" => function($row){
								$this->cache->container("system.transactions");

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getTransactions());
								endif;

								$transactions = $this->cache->getAll();

								return isset($transactions[$row["tid"]]) ? "{$transactions[$row["tid"]]["price"]} " . strtoupper($transactions[$row["tid"]]["currency"]) : __("lang_table_unknown_data");
							}
						],
						[
							"dt" => "expire_date",
							"formatter" => function($row){
								try {
									$transaction = $this->system->getTransaction($row["tid"]);
									$expireDuration = 30 * $transaction["duration"];
									$expireDate = date(logged_date_format, strtotime("{$row["date"]} +{$expireDuration} days"));
								} catch(Exception $e){
									$expireDate = __("lang_table_unknown_data");
								}

								return <<<HTML
								{$expireDate}
								HTML;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="subscriptions/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"uid",
						"pid",
						"tid"
					]);
				endif;

				break;
			case "administration.transactions":
				if(!permission("manage_transactions"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_admintransactions_createdtitle"),
								"data" => "create_date"
							],					
							[
								"title" => __("lang_dashboard_admin_tabletransactionscustomer"),
								"data" => "email",
								"sortable" => false
							],
							[
								"title" => __("lang_table_admintransactions_itemtitle"),
								"data" => "item",
								"sortable" => false
							],
							[
								"title" => __("lang_table_admintransactions_amounttitle"),
								"data" => "price"
							],
							[
								"title" => __("lang_table_admintransactions_durationtitle"),
								"data" => "duration"
							],
							[
								"title" => __("lang_dashboard_admin_tabletransactionsprovider"),
								"data" => "provider"
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "transactions", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"dt" => "email",
							"formatter" => function($row){
								$this->cache->container("system.users");

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getUsers());
								endif;

								$users = $this->cache->getAll();

								return isset($users[$row["uid"]]) ? $users[$row["uid"]]["email"] : __("lang_table_removed_data");
							}
						],
						[
							"dt" => "item",
							"formatter" => function($row){
								$this->cache->container("system.packages");

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getPackages());
								endif;

								$packages = $this->cache->getAll();

								return $row["type"] == 2 ? __("lang_table_admintransactions_creditslabel") : (isset($packages[$row["pid"]]) ? $packages[$row["pid"]]["name"] : __("lang_table_removed_data"));
							}
						],
						[
							"db" => "price",
							"dt" => "price",
							"formatter" => function($value, $row){
								return "{$value} " . strtoupper($row["currency"]);
							}
						],
						[
							"db" => "duration",
							"dt" => "duration",
							"formatter" => function($value){
								return $value < 1 ? __("lang_table_none_data") : ___(__("lang_table_admintransactions_monthslabel"), [$value]);
							}
						],
						[
							"db" => "provider",
							"dt" => "provider",
							"formatter" => function($value){
								return strtoupper($value);
							}
						]
					], 
					[
						"id",
						"uid",
						"type",
						"pid",
						"currency"
					]);
				endif;

				break;
			case "administration.payouts":
				if(!permission("manage_payouts"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_adminpayouts_createdtitle"),
								"data" => "create_date"
							],					
							[
								"title" => __("lang_table_adminpayouts_partnertitle"),
								"data" => "email",
								"sortable" => false
							],
							[
								"title" => __("lang_table_adminpayouts_amounttitle"),
								"data" => "amount"
							],
							[
								"title" => __("lang_table_adminpayouts_addresstitle"),
								"data" => "address"
							],
							[
								"title" => __("lang_dashboard_admin_tabletransactionsprovider"),
								"data" => "provider"
							],
							[
								"title" => __("lang_table_adminpayouts_optionstitle"),
								"data" => "options",
								"sortable" => false,
								"searchable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "payouts", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"dt" => "email",
							"formatter" => function($row){
								$this->cache->container("system.users");

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getUsers());
								endif;

								$users = $this->cache->getAll();

								return isset($users[$row["uid"]]) ? $users[$row["uid"]]["email"] : __("lang_table_removed_data");
							}
						],
						[
							"db" => "amount",
							"dt" => "amount",
							"formatter" => function($value, $row){
								return round($value) . " " . strtoupper($row["currency"]);
							}
						],
						[
							"db" => "address",
							"dt" => "address",
							"formatter" => function($value){
								return $value;
							}
						],
						[
							"db" => "provider",
							"dt" => "provider",
							"formatter" => function($value){
								return strtoupper($value);
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-success" title="{$GLOBALS["__"]("lang_table_adminpayouts_approve")}" zender-action="payout_confirm" payout-id="{$row["id"]}">
								            <i class="la la-check-circle"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_adminpayouts_reject")}" zender-action="payout_reject" payout-id="{$row["id"]}">
								            <i class="la la-times-circle"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"uid",
						"currency"
					]);
				endif;

				break;
			case "administration.widgets":
				if(!permission("manage_widgets"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_adminwidgets_createdtitle"),
								"data" => "create_date"
							],						
							[
								"title" => __("lang_dashboard_admin_tablewidgetsname"),
								"data" => "name"
							],
							[
								"title" => __("lang_dashboard_admin_tablewidgetstype"),
								"data" => "type"
							],
							[
								"title" => __("lang_dashboard_admin_tablewidgetssize"),
								"data" => "size"
							],
							[
								"title" => __("lang_dashboard_admin_tablewidgetsposition"),
								"data" => "position"
							],
							[
								"title" => __("lang_dashboard_admin_tablewidgetsoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "widgets", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "name",
							"dt" => "name",
						],
						[
							"db" => "type",
							"dt" => "type",
							"formatter" => function($value){
								return $value < 2 ? __("lang_table_adminwidgets_typeblock") : __("lang_table_adminwidgets_typemodal");
							}
						],
						[
							"db" => "size",
							"dt" => "size",
							"formatter" => function($value){
								return $value;
							}
						],
						[
							"db" => "position",
							"dt" => "position",
							"formatter" => function($value){
								return $value;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								$widgetClipboard = $row["type"] < 2 ? "{_block(\"" . md5($row["id"]) . "\")}" : "zender-toggle=\"" . md5($row["id"]) . "\"";

								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-secondary" title="{$GLOBALS["__"]("lang_table_adminwidgets_copywidgethook")}" data-clipboard-text='{$widgetClipboard}' zender-clipboard>
								            <i class="la la-clipboard"></i>
								        </button>
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.widget/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="widgets/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id"
					]);
				endif;

				break;
			case "administration.pages":
				if(!permission("manage_pages"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_adminpages_createdtitle"),
								"data" => "create_date"
							],					
							[
								"title" => __("lang_table_page_name"),
								"data" => "name"
							],
							[
								"title" => __("lang_table_page_require"),
								"data" => "logged"
							],
							[
								"title" => __("lang_table_page_roles"),
								"data" => "roles",
								"sortable" => false
							],
							[
								"title" => __("lang_table_adminpages_optionstitle"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "pages", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"db" => "slug",
							"dt" => "slug",
							"formatter" => function($value){
								return $value;
							}
						],
						[
							"db" => "logged",
							"dt" => "logged",
							"formatter" => function($value){
								return $value < 2 ? __("lang_table_yes_data") : __("lang_table_no_data");
							}
						],
						[
							"db" => "roles",
							"dt" => "roles",
							"formatter" => function($value){
								$this->cache->container("system.roles");

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getRoles());
								endif;

								$roles = $this->cache->getAll();

								foreach(explode(",", $value) as $role):
									if(isset($roles[$role])):
										$rolesNames[] = $roles[$role]["name"];
									endif;
								endforeach;

								return isset($rolesNames) ? implode(", ", $rolesNames) : __("lang_table_removed_data");
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-secondary" title="{$GLOBALS["__"]("lang_table_adminpages_copypagehook")}" data-clipboard-text='zender-page="{$row["id"]}/{$row["slug"]}"' zender-clipboard>
								            <i class="la la-clipboard"></i>
								        </button>
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.page/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="pages/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id"
					]);
				endif;

				break;
			case "administration.marketing":
				if(!permission("manage_marketing"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"columns" => [
							[
								"title" => __("lang_table_adminmarketing_createdtitle"),
								"data" => "create_date"
							],					
							[
								"title" => __("lang_table_adminmarketing_typetitle"),
								"data" => "type"
							],
							[
								"title" => __("lang_table_adminmarketing_titletitle"),
								"data" => "title"
							],
							[
								"title" => __("lang_table_adminmarketing_contenttitle"),
								"data" => "content",
								"width" => "25%",
								"sortable" => false
							],
							[
								"title" => __("lang_table_adminmarketing_recipientstitle"),
								"data" => "recipient",
								"width" => "20%",
								"sortable" => false
							],
							[
								"title" => __("lang_table_adminmarketing_optionstitle"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "marketing", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "type",
							"dt" => "type",
							"formatter" => function($value){
								switch($value):
									case 2:
										$type = __("lang_table_adminmarketing_notifylabel");

										break;
									case 3:
										$type = __("lang_table_adminmarketing_mailerlabel");

										break;
									default:
										$type = __("lang_table_adminmarketing_pushlabel");
								endswitch;

								return $type;
							}
						],
						[
							"db" => "title",
							"dt" => "title"
						],
						[
							"db" => "content",
							"dt" => "content"
						],
						[
							"dt" => "recipient",
							"formatter" => function($row){
								$this->cache->container("system.roles");

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getRoles());
								endif;

								$roles = $this->cache->getAll();

								$this->cache->container("system.users");

								if($this->cache->empty()):
									$this->cache->setArray($this->table->getUsers());
								endif;

								$users = $this->cache->getAll();

								$usersArray = [];
								$rolesArray = [];
								$usersView = __("lang_table_none_data");
								$rolesView = __("lang_table_none_data");

								if(!in_array("0", explode(",", $row["users"]))):
									foreach(explode(",", $row["users"]) as $user):
										try {
											$usersArray[] = $users[$user]["email"];
										} catch(Exception $e){
											// Ignore
										}
									endforeach;

									$usersView = implode(", ", $usersArray);
								endif;

								if(!in_array("0", explode(",", $row["roles"]))):
									foreach(explode(",", $row["roles"]) as $role):
										$rolesArray[] = $roles[$role]["name"];
									endforeach;

									$rolesView = implode(", ", $rolesArray);
								endif;

								return <<<HTML
								<ul class="text-left">
								  <li>{$GLOBALS["__"]("lang_table_adminmarketing_roleslabel")}<br>
								  	<code>{$rolesView}</code>
								  </li>
								  <li>{$GLOBALS["__"]("lang_table_adminmarketing_userslabel")}<br>
								  	<code>{$usersView}</code>
								  </li>
								</ul>
								HTML;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="marketing/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id",
						"users",
						"roles"
					]);
				endif;

				break;	
			case "administration.languages":
				if(!permission("manage_languages"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"columns" => [
							[
								"title" => __("lang_dashboard_admin_tablelanguagescreated"),
								"data" => "create_date"
							],					
							[
								"title" => __("lang_dashboard_admin_tablelanguagesiso"),
								"data" => "iso",
								"width" => "15%"
							],
							[
								"title" => __("lang_dashboard_admin_tablelanguagesname"),
								"data" => "name"
							],
							[
								"title" => __("lang_dashboard_admin_tablelanguagesoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "languages", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "iso",
							"dt" => "iso"
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.language/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="languages/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id"
					]);
				endif;

				break;
			case "administration.gateways":
				if(!permission("manage_gateways"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_admingateways_createdtitle"),
								"data" => "create_date"
							],					
							[
								"title" => __("lang_table_admingateways_nametitle"),
								"data" => "name"
							],
							[
								"title" => __("lang_table_admingateways_pricingtitle"),
								"data" => "pricing"
							],
							[
								"title" => __("lang_table_admingateways_optionstitle"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "gateways", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"db" => "pricing",
							"dt" => "pricing",
							"formatter" => function($value){
								if(!is_object(json_decode($value)))
									return __("lang_table_admingateways_invalidjson");

								$pricing = json_decode($value, true);
								
								$perCountry = [];
								$perCountryView = __("lang_table_none_data");

								if(!empty($pricing["countries"])):
									foreach($pricing["countries"] as $key => $value):
										$perCountry[] = strtoupper($key) . ": {$value}";
									endforeach;

									$perCountryView = implode(", ", $perCountry);
								endif;

								return <<<HTML
								<ul class="text-left">
								  <li>{$GLOBALS["___"]($GLOBALS["__"]("lang_table_admingateways_defaultprice"), [$pricing["default"]])}</li>
								  <li>
								  	{$GLOBALS["__"]("lang_table_admingateways_percountryprcie")}<br>
								  	<code>{$perCountryView}</code>
								  </li>
								</ul>
								HTML;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.gateway/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="gateways/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id"
					]);
				endif;

				break;
			case "administration.shorteners":
				if(!permission("manage_shorteners"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_adminshorteners_createdtitle"),
								"data" => "create_date"
							],					
							[
								"title" => __("lang_table_adminshorteners_nametitle"),
								"data" => "name"
							],
							[
								"title" => __("lang_table_adminshorteners_optionstitle"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "shorteners", [
						[
							"db" => "create_date",
							"dt" => "create_date",
							"formatter" => function($value){
								$createDate = date(logged_date_format, strtotime($value));
								$createTime = date(logged_clock_format, strtotime($value));
								return <<<HTML
								{$createDate}<br>
								({$createTime})
								HTML;
							}
						],
						[
							"db" => "name",
							"dt" => "name"
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								        <button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_title_editthisitem")}" zender-toggle="zender.edit.shortener/{$row["id"]}">
								            <i class="la la-edit"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_title_deletethisitem")}" zender-delete="shorteners/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"id"
					]);
				endif;

				break;
			case "administration.plugins":
				if(!permission("manage_plugins"))
					response(500, __("lang_response_no_permission"));

				if(isset($request["structure"])):
					$structure = [
						"multiselect" => false,
						"columns" => [
							[
								"title" => __("lang_table_adminplugins_idtitle"),
								"data" => "id",
								"visible" => false
							],					
							[
								"title" => __("lang_table_pluginsname"),
								"data" => "name",
								"sortable" => false
							],
							[
								"title" => __("lang_table_adminplugins_versiontitle"),
								"data" => "version",
								"searchable" => false,
								"sortable" => false
							],
							[
								"title" => __("lang_table_adminplugins_actionstitle"),
								"data" => "actions",
								"searchable" => false,
								"sortable" => false
							],
							[
								"title" => __("lang_table_pluginsoptions"),
								"data" => "options",
								"searchable" => false,
								"sortable" => false
							]
						]
					];
				else:
					$datatable = $this->datatable->complex($request, "plugins", [
						[
							"db" => "id",
							"dt" => "id"
						],
						[
							"db" => "name",
							"dt" => "name",
							"formatter" => function($value, $row){
								$pluginsDecoded = json_decode($row["data"], true);

								if(array_key_exists("name", $pluginsDecoded)):
									$pluginName = $pluginsDecoded["name"];
								else:
									$pluginName = __("lang_table_unknown_data");
								endif;

								return $pluginName;
							}
						],
						[
							"dt" => "version",
							"formatter" => function($row){
								$pluginsDecoded = json_decode($row["data"], true);

								if(array_key_exists("version", $pluginsDecoded)):
									$pluginVersion = "v{$pluginsDecoded["version"]}";
								else:
									$pluginVersion = __("lang_table_unknown_data");
								endif;

								return $pluginVersion;
							}
						],
						[
							"dt" => "actions",
							"formatter" => function($row){
								$pluginsDecoded = json_decode($row["data"], true);

								if(array_key_exists("actions", $pluginsDecoded)):
									$pluginsHTML = "";

									foreach($pluginsDecoded["actions"] as $action):
										$pluginsHTML .= "<button class=\"btn btn-sm btn-{$action["color"]}\" zender-plugin-action=\"{$action["action"]}\">
											<i class=\"{$action["icon"]}\"></i>
										</button>";
									endforeach;
								else:
									$pluginsHTML = "";
								endif;

								if(empty($pluginsHTML)):
									return <<<HTML
									<p>{$GLOBALS["__"]("lang_tables_plugins_noactionsline")}</p>
									HTML;
								else:
									return <<<HTML
									<div class="table-buttons">
									    <div class="btn-group">
									    	{$pluginsHTML}
									    </div>
									</div>
									HTML;
								endif;
							}
						],
						[
							"dt" => "options",
							"formatter" => function($row){
								return <<<HTML
								<div class="table-buttons">
								    <div class="btn-group">
								    	<button class="btn btn-sm btn-primary" title="{$GLOBALS["__"]("lang_table_adminplugins_updatedesc")}" zender-toggle="zender.update.plugin/{$row["id"]}">
								            <i class="la la-upload"></i>
								        </button>
								        <button class="btn btn-sm btn-warning" title="{$GLOBALS["__"]("lang_table_adminplugins_editdesc")}" zender-toggle="zender.edit.plugin/{$row["id"]}">
								            <i class="la la-cog"></i>
								        </button>
								        <button class="btn btn-sm btn-danger" title="{$GLOBALS["__"]("lang_table_adminplugins_removedesc")}" zender-delete="plugins/{$row["id"]}">
								            <i class="la la-trash"></i>
								        </button>
								    </div>
								</div>
								HTML;
							}
						]
					], 
					[
						"data"
					]);
				endif;

				break;
			default:
				response(500, __("lang_response_invalid"));
		}

		if(isset($structure)):
			response(200, false, [
				"search" => [
					"status" => !isset($structure["search"]["disable"]) ? true : false,
					"text" => __("lang_table_search_text"),
					"placeholder" => __("lang_table_search_placeholder")
				],
				"multiselect" => !isset($structure["multiselect"]) ? true : false,
				"export" => isset($structure["export"]["export_columns"]) ? $structure["export"] : false,
				"limit" => isset($structure["limit"]) ? $structure["limit"] : 10,
				"history" => isset($structure["history"]["column"]) ? $structure["history"] : false,
				"select" => isset($structure["select"]) ? true : false,
				"columns" => $structure["columns"]
			]);
		else:
			responseTable($datatable);
		endif;
	}
}