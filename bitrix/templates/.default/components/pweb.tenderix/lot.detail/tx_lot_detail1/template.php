<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>

<? if(isset($_GET['lot_arch']) && $_GET['lot_arch'] == 'Y') { ?>
<script>
	UI.message({
		text: 'Лот перенесён в архив',
		timer: 6000,
		veil: true
	});
</script>
<?} ?>
<? if(isset($_GET['lot_fail']) && $_GET['lot_fail'] == 'Y') { ?>
	<script>
		UI.message({
			text: 'Лот признан несостоявшимся',
			timer: 6000,
			veil: true
		});
	</script>
<?} ?>


<?
/* Отправка уведомлений с помощью ajax */
CUtil::InitJSCore(array('ajax', 'jquery', 'popup'));?>
<script>
   
</script>
<style>
	input[type="checkbox"] {
		cursor: pointer;
	}
	
	.label-email-send {
		display: inline-block;
		padding-left: 10px;
		color: green;
	}
	#ajax-add-schema {
		display:none; 
		width:1024px; 
		max-height:578px;
		overflow: scroll;
	}
</style>

<? if ($USER->IsAuthorized() && $arResult["T_RIGHT"] == "W"): ?>

<div id="ajax-history-schema" class="popup-window-content"></div>

<script type="text/javascript">
	BX.ready(function(){
	   var historyForm = new BX.PopupWindow("schema", null, {
		  content: BX('ajax-history-schema'), //Контейнер
		  className: 't_popup-window-layout',
		  closeIcon: {right: "20px", top: "10px"}, //Иконка закрытия
		  titleBar: {
			  content: BX.create("span", {
				  html: '<b>Лот №<?= $arResult["LOT"]['ID']?></b>', 
					'props': {'className': 't_popup-title-bar'}})
		  }, //Название окна 
			zIndex: 0,
			offsetLeft: 0,
			offsetTop: 0,
			draggable: {restrict: true}, //Окно можно перетаскивать на странице     
			overlay: {backgroundColor: 'black', opacity: '80' },  /* затемнение фона */
			buttons: [
			
		
			 new BX.PopupWindowButton({
				text: "Закрыть",
				className: "t_popup-window-button-cancel",
				events: {click: function(){
				   this.popupWindow.close();// закрытие окна
				}}
				})
			 ]
	   }); 
	   	   
	   $('#history').click(function(){
		  BX.ajax.insertToNode('/event_send.php?action=proposal_list&LOT_ID=<?= $arResult["LOT"]["ID"]?>', BX('ajax-history-schema'));
		  historyForm.show(); //отображение окна
		  //document.getElementById('check-all').removeEventListener('change');
		  
	   });

	});
</script>


<? endif;?>


<? if ($USER->IsAuthorized() && $arResult["T_RIGHT"] >= "S"): ?>

<div id="ajax-add-schema"></div>
<div id="ajax-add-time-schema">
	<form id="add-time" name="add-time" method="POST" action="/event_send.php?action=add_time&LOT_ID=<?= $arResult["LOT"]["ID"]?>">
		<div class="row">
			<div class="col-md-5">
				<label><?= GetMessage('PW_TD_ADD_TIME_SEC')?></label>
			</div>
			<div class="col-md-7">
				<input name="time" type="text" style="width:100%" value="600"/>
			</div>
			
		</div>
		<div class="row">
			<div class="col-md-12">
				<div id="add-time-alert"></div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	BX.ready(function(){
	   var schema = new BX.PopupWindow("schema", null, {
		  content: BX('ajax-add-schema'), //Контейнер
		  className: 't_popup-window-layout',
		  closeIcon: {right: "20px", top: "10px"}, //Иконка закрытия
		  titleBar: {
			  content: BX.create("span", {html: '<b>Оправить приглашение</b>', 'props': {'className': 't_popup-title-bar'}})
		  }, //Название окна 
			zIndex: 0,
			offsetLeft: 0,
			offsetTop: 0,
			draggable: {restrict: true}, //Окно можно перетаскивать на странице     
			overlay: {backgroundColor: 'black', opacity: '80' },  /* затемнение фона */
			buttons: [
			
			new BX.PopupWindowButton({
				text: "Отправить",
				className: "t_popup-window-button-accept",
				events: {click: function(){
				   BX.ajax.submit(BX("email-send"), function(data){ // отправка данных из формы с id="myForm" в файл из action="..."
					  BX('ajax-add-schema').innerHTML = data;
					  
					});
				}}
			 }),			
			 new BX.PopupWindowButton({
				text: "Закрыть",
				className: "t_popup-window-button-cancel",
				events: {click: function(){
				   this.popupWindow.close();// закрытие окна
				}}
				})
			 ]
	   }); 
	   
	   var addTimeForm = new BX.PopupWindow("add_time", null, {
		  content: BX('ajax-add-time-schema'), //Контейнер
		  className: 't_popup-window-layout',
		  closeIcon: {right: "20px", top: "10px"}, //Иконка закрытия
		  titleBar: {
			  content: BX.create("span", {html: '<b>Продлить лот</b>', 'props': {'className': 't_popup-title-bar'}})
		  }, //Название окна 
			zIndex: 0,
			offsetLeft: 0,
			offsetTop: 0,
			draggable: {restrict: true}, //Окно можно перетаскивать на странице     
			overlay: {backgroundColor: 'black', opacity: '80' },  /* затемнение фона */
			buttons: [
			
			new BX.PopupWindowButton({
				text: "Продлить",
				className: "t_popup-window-button-accept",
				events: {click: function(){
				   BX.ajax.submit(BX("add-time"), function(data){ // отправка данных из формы с id="myForm" в файл из action="..."
					  BX('add-time-alert').innerHTML = data;
					  setTimeout(function() {
						  // BX('add-time-alert').innerHTML = '';
						  var url = window.location.href;
						   location.href = url;
					  }, 1000);					  
					});
				}}
			 }),			
			 new BX.PopupWindowButton({
				text: "Закрыть",
				className: "t_popup-window-button-cancel",
				events: {click: function(){
				   this.popupWindow.close();// закрытие окна
				   
				}}
				})
			 ]
	   }); 
	   
	   $('#loading').click(function(){
		  BX.ajax.insertToNode('/event_send.php?LOT_ID=<?= $arResult["LOT"]["ID"]?>', BX('ajax-add-schema'));//ajax-загрузка контента из url, у меня он помещён в "Короткие ссылки" /bitrix/admin/short_uri_admin.php?lang=ru
		  //Можно использовать такой адрес /include/schema.php      
		  schema.show(); //отображение окна
		  //document.getElementById('check-all').removeEventListener('change');
		  
	   });
	    $('#add_time').click(function(){
		   
		   BX('add-time-alert').innerHTML = '';
		  addTimeForm.show(); //отображение окна
		  
	   });
	});
</script>

<? endif;?>
<? //__($arResult["LOT"]["ARCHIVE"]);?>
<div class="col-md-12">
	<div class="row mb-3">
		<div class="col-md-12">
			<h2>
				
				<? if ($arResult["LOT"]["ARCHIVE"] == "Y"){?>				
					<i class="fa fa-archive" style="font-size:30px;color:#aaacb2;" title="архивный лот" data-toggle="tooltip" data-placement="left"></i>
				<?}elseif ($arResult["LOT"]["END_LOT"] == "Y") {?>
					<i class="fa fa-lock" style="font-size:30px;color:#aaacb2;" title="завершенный лот" data-toggle="tooltip" data-placement="left"></i>
				<?}else {?>
					<i class="fa fa-check-circle" style="font-size:30px;color:#5aab00;" title="активный лот" data-toggle="tooltip" data-placement="left"></i>
				<?}?>
				<?= GetMessage("PW_TD_LOT_NUM") ?><?= $arResult["LOT"]["ID"] ?> <?= $arResult["LOT"]["TITLE"] ?>
			</h2>
			
			<div class="t_panel_actions">
				<ul class="t_lot_actions">
					<? if ($arResult["OWNER"] == "Y" || $arResult["T_RIGHT"] == 'W'){ ?>
						<? //if(($arResult["LOT"]["END_LOT"] != "N" || $arResult["LOT"]["ACTIVE"] != "Y") && $arResult["LOT"]["ARCHIVE"] != "Y") { ?>
						<li class="">
							<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;<a href="<?= $arResult["LOT_URL"] ?>">Редактировать</a> 
						</li>
						<? //} ?>
					<? } ?>
					<? if ($USER->IsAuthorized() && $arResult["T_RIGHT"] >= "S"): ?>
						<li class="">
							<i class="fa fa-copy"></i>&nbsp;&nbsp;&nbsp;<a href="lot.php?COPY_ID=<?= $arResult["LOT"]["ID"] ?>">Дублировать</a>
						</li>
					<? endif; ?>
					<!-- Отправка почтовых уведомлений (повторно) -->
					<? if ($USER->IsAuthorized() && $arResult["T_RIGHT"] >= "S"): ?>
						<li class="" id="loading">
							<i class="fa fa-envelope"></i>&nbsp;&nbsp;&nbsp;<a id="loading" href="javascript:void(0)">Уведомление</a>
						</li>
					<? endif; ?>
					<!-- Продление тендера -->
					<? if ($USER->IsAuthorized() && $arResult["T_RIGHT"] >= "S"): ?>
						<li class="">
							<i class="fa fa-hourglass"></i>&nbsp;&nbsp;&nbsp;<a id="add_time" href="javascript:void(0)">Продлить</a>
						</li>
					<? endif; ?>
					<!--История переторжки (для администратора тендера--> 
					<? if ($USER->IsAuthorized() && $arResult["T_RIGHT"] == "W"): ?>
						<li class="">
							<i class="fa fa-history"></i>&nbsp;&nbsp;&nbsp;<a id="history" href="javascript:void(0)">История</a>
						</li>
					<? endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div>

<!-- Запросы на доступ к лоту -->
<?/*if($arResult["LOT"]["END_LOT"] == "N") { ?>
	<? if ($USER->IsAuthorized()) { ?>
		<? //if($arResult['T_RIGHT'] == "W"){ ?>
			<div class="row" id="suppliers_div">
			</div>


			<script>
				function updateListSuppliers() {
					$.ajax({
						type: "POST",
						url: "<?= $templateFolder ?>/load_component.php",
						data: "LOT_ID="+<?=$arResult["LOT"]["ID"] ?>,
						success: function (data) {
							$("#suppliers_div").html(data);
						}
					});
					//setTimeout("updateListSuppliers()", 60000);
				}

				$(function () {
					updateListSuppliers();
				});

			</script>
		<? //} ?>
	<? } ?>
<? }*/ ?>
	<div class="col-md-12">
<div class="side-tab">
	<div class="side-tab_menu">
		<ul class="side-tab_tabs">
			<li class="side-tab_tab active" onclick="changeTab('basic', this)"><span class="label">Информация</span></li>
			<li class="side-tab_tab" onclick="changeTab('proto', this)" id="secondTab"><span class="label">Протокол</span></li>
			<li class="side-tab_tab" onclick="changeTab('auc', this)" id="thirdTab"><span class="label">Торги</span></li>
		</ul>
	</div>
	<div class="side-tab_content" id="basic" style="display:block">
		<div class="col-md-12">
			<div class="row mb-4">
				<div class="col-md-6">
					<div class="t_block">
						<div class="t_block_header">Информация о лоте</div>
						<div class="t_block_content">					
							<table class="table t_lot_table" >
									<tr>
										<td><b>Статус: </b></td>
										<td>
											<? 	if($arResult["LOT"]["ACTIVE"] == "Y") { ?>
													<?if ($arResult["LOT"]["PRIVATE"] == "Y"):?>
														<i class="t_lot_status t_lot_status_private fa fa-users"></i>&nbsp;<b>Закрытый лот</b>
													<?endif;?>
													<?if ($arResult["LOT"]["ARCHIVE"] == "Y"){?>
														<i class="t_lot_status t_lot_status_archive fa fa-archive"></i>&nbsp;<b>Архивный лот</b>
													<?}elseif ($arResult["LOT"]["END_LOT"] == "Y") {?>
														<i class="t_lot_status t_lot_status_end fa fa-lock"></i>&nbsp;<b>Завершенный лот</b>
													<?}else{?>
														<i class="t_lot_status t_lot_status_active fa fa-check-circle"></i>&nbsp;<b>Активный лот</b>
													<? }
												} else { ?>
												<?if ($arResult["LOT"]["PRIVATE"] == "Y"):?>
													<i class="t_lot_status t_lot_status_private fa fa-users"></i>&nbsp;<b>Закрытый лот</b>
												<?endif;?>
													<i class="t_lot_status t_lot_status_saved fa fa-save"></i>&nbsp;<b>Сохраненный лот</b>
											<?}?>
											
																			
										</td>
									</tr>
									<tr>
										<td><b>Тип лота: </b></td>
										<td>
										<? if ($arResult["LOT"]["TYPE_ID"] == "N") :?>
											<b><span class="label label-primary">АУКЦИОН</span></b><br/>
										<? endif;?>
										<? if ($arResult["LOT"]["TYPE_ID"] == "S") :?>
											<b>покупка (товар)</b><br/>
										<? endif;?>
										<? if ($arResult["LOT"]["TYPE_ID"] == "P") :?>
											<b>продажа</b><br/>
										<? endif;?>
										<? if ($arResult["LOT"]["TYPE_ID"] == "T") :?>
											<b><span class="label label-info">КОНКУРС<?if($arResult['LOT']['QUOTES'] == 'Y'):?>(Запрос котировок)<?endif;?></span></b><br/>
										<? endif;?>
										<? if ($arResult["LOT"]["TYPE_ID"] == "R") :?>
											<b>запрос цен (товар)</b><br/>
										<? endif;?>
										<? if ($arResult["LOT"]["OPEN_PRICE"] == "Y"): ?>
											<b><?= GetMessage("PW_TD_LOT_OPEN_PRICE") ?></b><br/>
										<? endif; ?>
										</td>
									</tr>
									<tr>
										<td><b><?= GetMessage("PW_TD_CURRENCY") ?>: </b></td>
										<td><?= $arResult["LOT"]["CURRENCY"] ?></td>
									</tr>
									<tr>
										<td><b><?= GetMessage("PW_TD_SECTION") ?>: </b></td>
										<td><?= $arResult["LOT"]["SECTION"] ?></td>
									</tr>

								<tr>
									<td><b><?= GetMessage("PW_TD_DATE_START") ?>:</b></td>
									<td><?= $arResult["LOT"]["DATE_START"] ?></td>
								</tr>
								<tr>
									<td><b><?= GetMessage("PW_TD_DATE_END") ?>: </b></td>
									<td><?= $arResult["LOT"]["DATE_END"] ?>
										<?// if ($arResult["LOT"]["TIME_EXTENSION"] > 0) : ?>
											<!--span
												class="time_ext">(+ <?= round($arResult["LOT"]["TIME_EXTENSION"] / 60, 1); ?> <?= GetMessage("PW_TD_MINUTES") ?>
												)</span-->
										<?// endif; ?></td>
								</tr>
								<tr>
									<td><b>Предложений подано: </b></td>
									<td><b>
									<?=$arResult['LOT']['PROPOSAL']; ?></b></td>
								</tr>
								<tr>
									<td><b>Объем лота: </b></td>
									<td>
										<? $sumStart = 0;
										foreach ($arResult["PROPERTY_SPEC"] as $specProp) {
											$sumStart += $specProp['START_PRICE']*$specProp['COUNT'];								
										}
										?>
										<span id="total_start_price"><b><?=number_format($sumStart, 2, '.', ' ');?></b></span>
									</td>
								</tr>
								<? if (strlen($arResult["LOT"]["DATE_DELIVERY"]) > 0): ?>
									<tr>
										<td><b><?= GetMessage("PW_TD_DATE_DELIVERY") ?>: </b></td>
										<td><?= $arResult["LOT"]["DATE_DELIVERY"] ?></td>
									</tr>
								<? endif; ?>
								<tr>
									<td><b><?= GetMessage("PW_TD_COMPANY") ?>: </b></td>
									<td><?= $arResult["LOT"]["COMPANY"] ?></td>
								</tr>
								<tr>
									<td><b><?= GetMessage("PW_TD_RESPONSIBLE") ?>:</b></td>
									<td><?= $arResult["LOT"]["RESPONSIBLE_FIO"] ?></td>
								</tr>
								<tr>
									<td><b><?= GetMessage("PW_TD_RESPONSIBLE_PHONE") ?>:</b></td>
									<td><?= $arResult["LOT"]["RESPONSIBLE_PHONE"] ?></td>
								</tr>
								<? if (count($arResult["LOT"]["FILE"]) > 0): ?>
									<tr>
										<td><b><?= GetMessage("PW_TD_DOCUMENT") ?>:</b></td>
										<td><?
											foreach ($arResult["LOT"]["FILE"] as $arFile) {
												?>
												<a href="/tx_files/lot_file.php?LOT_ID=<?=$arResult["LOT"]["ID"] ?>&FILE_ID=<?= $arFile["ID"] ?>"><?= $arFile["ORIGINAL_NAME"] ?></a>
												<br/>
											<?
											}
											?></td>
									</tr>
								<? endif; ?>
							</table>
						</div>
					</div>
				</div>
				<? if ($arResult["LOT"]["TYPE_ID"] != "S" && $arResult["LOT"]["TYPE_ID"] != "R"): ?>
				<div class="col-md-6">
					<div class="t_block">
						<div class="t_block_header">Спецификация</div>
						<div class="t_block_content">
							<table class="table t_lot_table">			
							<? $numProp = 1; ?>
							<?
								// echo '<pre>';
								// print_r($arResult["PROPERTY_SPEC"]);
								
								// START_PRICE
								// COUNT
							?>
							<? foreach ($arResult["PROPERTY_SPEC"] as $specProp): ?>
								<? if ($numProp == 1): ?>
									<tr>
										<th><?= GetMessage("PW_TD_NUM") ?></th>
										<th><?= GetMessage("PW_TD_TOVAR") ?></th>
										<? // if ($specProp["START_PRICE"] > 0 && $specProp["STEP_PRICE"] > 0):   ?>
										<th>
											<? if ($arParams["NDS_TYPE"] == "N"): ?>
												<?= GetMessage("PW_TD_SPEC_START_PRICE_NDS_N") ?>
											<? else: ?>
												<?= GetMessage("PW_TD_SPEC_START_PRICE_NDS") ?>
											<? endif; ?>
										</th>
										<!--th><?= GetMessage("PW_TD_SPEC_STEP_PRICE") ?></th-->
										<? // endif;   ?>
									</tr>
								<? endif; ?>
								<tr>
									<td align="center"><? echo $numProp ?></td>
									<td>
										<b><?= GetMessage("PW_TD_SPEC_NAME_PROD") ?>:</b> <?= $specProp["TITLE"] ?><br/>
										<b><?= GetMessage("PW_TD_SPEC_COUNT") ?>
											:</b> <?= $specProp["COUNT"] ?>  <?= $specProp["UNIT_NAME"] ?><br/>
										<? if (strlen($specProp["ADD_INFO"]) > 0): ?>
											<b><?= GetMessage("PW_TD_SPEC_ADD_INFO") ?>:</b> <?= $specProp["ADD_INFO"] ?>
										<? endif; ?>
									</td>
									<? //if ($specProp["START_PRICE"] > 0 && $specProp["STEP_PRICE"] > 0):   ?>
									<td align="center">
										<?if($specProp["START_PRICE"] == '0'):?>
										Не установлена
										<?else:?>
											<?= number_format($specProp["START_PRICE"], 2, '.', ',');?>
										<?endif;?>
									</td>
									<!--td align="center">
										<?= $specProp["STEP_PRICE"] ?>
									</td-->
									<? //endif;   ?>
								</tr>
								<? $numProp++; ?>
							<? endforeach; ?>
							</table>
						</div>
					</div>
				</div>
				<? endif; ?>
			</div>
		</div>
		<div class="col-md-12">
			<div class="row">
				<div class="col-sm-12">
					<div class="t_block">
						<div class="t_block_header">Дополнительная информация</div>
						<div class="t_block_content">
							<? if ($arResult["LOT"]["TYPE_ID"] == "S" || $arResult["LOT"]["TYPE_ID"] == "R"): ?>
							
								<? foreach ($arResult["PRODUCT"] as $arProdBuyerId => $arProduct) : ?>
									<h2><?= $arProduct["TITLE"] ?></h2>
									<table class="table t_lot_table bold">
										<tr>
											<td><b><?= GetMessage("PW_TD_SECTION") ?>: </b></td>
											<td><?=$arProduct["SECTION"]?></td>
										</tr>
										<? $odd_table = 2; ?>
										<? foreach ($arResult["PROPERTY_PRODUCT"][$arProdBuyerId] as $arPropProductId => $arPropProduct): ?>
											<? if ($arResult["PROPERTY_PRODUCT_BUYER"][$arProdBuyerId][$arPropProductId]["VISIBLE"] == "Y"): ?>
												<tr<? if ($odd_table % 2 == 0) echo ' class="odd"'; ?>>
													<td>
														<? echo $arPropProduct["TITLE"]; ?>
													</td>
													<td>
														<?= $arResult["PROPERTY_PRODUCT_BUYER"][$arProdBuyerId][$arPropProductId]["VALUE"]; ?>
													</td>
												</tr>
												<? $odd_table++; ?>
											<? endif; ?>
										<? endforeach; ?>

										<?
										$COUNT = $arResult["PRODUCT_BUYER"][$arProdBuyerId]["COUNT"];
										$NDS = 0;
										$PRICE_NDS = $arResult["PRODUCT_BUYER"][$arProdBuyerId]["START_PRICE"];
										?>
										<tr<? if ($odd_table % 2 == 0) echo ' class="odd"'; ?>>
											<td>
												<?= GetMessage("PW_TD_PRODUCT_COUNT") ?>
											</td>
											<td>
												<?= $arResult["PRODUCT_BUYER"][$arProdBuyerId]["COUNT"] ?>
											</td>
										</tr>
										<? $odd_table++; ?>
										<tr<? if ($odd_table % 2 == 0) echo ' class="odd"'; ?>>
											<td>
												<?= GetMessage("PW_TD_PRODUCT_UNIT") ?>
											</td>
											<td>
												<?= $arResult["PRODUCT"][$arProdBuyerId]["UNIT_NAME"] ?>
											</td>
										</tr>
										<? if (intval($arResult["PRODUCT_BUYER"][$arProdBuyerId]["START_PRICE"]) > 0): ?>
											<? $odd_table++; ?>
											<tr<? if ($odd_table % 2 == 0) echo ' class="odd"'; ?>>
												<td>
													<? if ($arParams["NDS_TYPE"] == "N"): ?>
														<?= GetMessage("PW_TD_PRICE_START_PRICE_NDS_N") ?>
													<? else: ?>
														<?= GetMessage("PW_TD_PRICE_START_PRICE_NDS") ?>
													<? endif; ?>
												</td>
												<td>
													<?= $arResult["PRODUCT_BUYER"][$arProdBuyerId]["START_PRICE"] ?>
												</td>
											</tr>
										<? endif; ?>
										<? //if (intval($arResult["PRODUCT_BUYER"][$arProdBuyerId]["STEP_PRICE"]) > 0): ?>
											<?// $odd_table++; ?>
											<!--tr<? if ($odd_table % 2 == 0) echo ' class="odd"'; ?>>
												<td>
													<?= GetMessage("PW_TD_PRODUCT_STEP_PRICE") ?>
												</td>
												<td>
													<?= $arResult["PRODUCT_BUYER"][$arProdBuyerId]["STEP_PRICE"] ?>
												</td>
											</tr-->
										<? //endif; ?>
									</table>
									<br/><br/>
								<? endforeach; ?>
							<? endif; ?>
							<? if ($arResult["LOT"]["TERM_PAYMENT_ID"] > 0): ?>
						<p><b><?= GetMessage("PW_TD_TERM_PAYMENT") ?>:</b> <?= $arResult["PAYMENT"] ?><br/>
							<?= $arResult["LOT"]["TERM_PAYMENT_VAL"] ?>
						</p>
					<? endif; ?>
					<? if ($arResult["LOT"]["TERM_DELIVERY_ID"] > 0): ?>
						<p><b><?= GetMessage("PW_TD_TERM_DELIVERY") ?>:</b> <?= $arResult["DELIVERY"] ?><br/>
							<?= $arResult["LOT"]["TERM_DELIVERY_VAL"] ?>
						</p>
					<? endif; ?>
					<? if (strlen($arResult["LOT"]["NOTE"]) > 0): ?>
						<p><b><?= GetMessage("PW_TD_NOTE") ?>:</b><br/>
							<?= html_entity_decode($arResult["LOT"]["NOTE"]) ?>
						</p>
					<? endif; ?>
						</div>
						
					</div>
				</div>
					
			</div>
		</div>
	</div>
	<div class="side-tab_content" id="proto">
		<? if($arResult["LOT"]["END_LOT"] == 'Y' && $USER->IsAuthorized() && CSite::InGroup(array(1,8))) { ?>
		<div class="col-md-12">
		<div class="row">
			<div class="col-md-12">
				<div class="t_block">
						<div class="t_block_header">Протокол</div>
						<div class="t_block_content">	
						<form method="POST" name="final_file_lot" action="" style="margin-top:10px; border: 1px solid #dddddd; padding:10px;" enctype="multipart/form-data">

							Прикрепить отсканированный итоговый протокол <br/>

							<div>
								<?$APPLICATION->IncludeComponent("bitrix:main.file.input", "drag_n_drop",
									array(
										"INPUT_NAME"=>"LOT_FILE_CONCURENT",
										"MULTIPLE"=>"Y",
										"MODULE_ID"=>"pweb.tenderix",
										"MAX_FILE_SIZE"=>"",
										"ALLOW_UPLOAD"=>"A",
										"ALLOW_UPLOAD_EXT"=>""
									),
									false
								);?>
							</div>
							<? if($arResult["LOT"]["PRIVATE"] == "Y") { ?>
								<? foreach($arResult["LOT"]["PRIVATE_USER"] as $val) { ?>
									<input type="hidden" name="PRIVATE_USER[]" value="<?=$val["ID"] ?>" />
								<? } ?>
								<input type="hidden" name="PRIVATE" value="Y" />
							<? } else { ?>
								<input type="hidden" name="PRIVATE" value="N" />
							<? } ?>
							<br/>
							<table class="t_lot_table">
								<tr>
									<th style="width: 170px; text-align: center;"><? echo "Название" ?></th>
									<?if(CSite::InGroup(array(1,8))):?><th style="width: 170px; text-align: center;"><? echo "Удалить" ?></th><?endif;?>
								</tr>
								<? if(!empty($arResult["LOT"]["FILE_CONCURENT"])) { ?>
									<? foreach ($arResult["LOT"]["FILE_CONCURENT"] as $arFile) { ?>
										<tr>
											<td>
												<a href="/tx_files/lot_file.php?LOT_ID=<?= $arResult["LOT"]["ID"] ?>&FILE_ID=<?= $arFile["ID"] ?>&file_type=concurent"><?= $arFile["ORIGINAL_NAME"] ?></a>
											</td>
											<?if(CSite::InGroup(array(1,8))):?>
												<td align="center">
													<input type="checkbox" name="FILE_ID[<? echo $arFile["ID"] ?>]" value="<? echo $arFile["ID"] ?>">
												</td>
											<?endif;?>
										</tr>
									<?	} ?>
								<? } else { ?>
									<tr>
										<td colspan="2" style="text-align: center;">Нет файлов</td>
									</tr>
								<? } ?>
							</table>

							<br/>
							Комментарий:
							<textarea name="COMMENT" class="form-control" rows="7"><?= html_entity_decode($arResult["LOT"]["COMMENT"]) ?></textarea>
							<br>
							<? if(empty($arResult["LOT"]["FILE_CONCURENT"])) { ?>
								<input type="submit" name="lot_file_concurent_submit" value="Сохранить">
							<? } ?>
						</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<? } ?>
	</div>
	<div class="side-tab_content" id="auc">
		<div class="col-md-12">
			<div class="t_block_header">Список предложений</div>
			 <?$APPLICATION->IncludeComponent(
				"pweb.tenderix:proposal.list",
				"tx_proposal_list",
				Array(
					"CACHE_TIME" => "3600000",
					"CACHE_TYPE" => "N",
					"JQUERY" => "N",
					"LOT_ID" => $_REQUEST["LOT_ID"],
					"SORT_ITOGO" => "asc"
				)
			);?>
			</br>
		</div>
	</div>
</div>
	</div>
<script>
	function changeTab(id, e) {
		$('.side-tab_content').hide();
		$('#'+id).fadeIn();
		$('.side-tab_tab').removeClass('active');
		$(e).addClass('active');
	}
</script>
<?if( isset($_GET['active_tab']) && intval($_GET['active_tab']) == 3 ): ?>
	<script>
		changeTab('auc', $('#thirdTab'));
	</script>	
<?endif; ?>