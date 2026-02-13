<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

//__($arResult["LOT"]);

$date_end = strtotime($arResult["LOT"]["DATE_END"]);
$date_tek = time();

//__($arResult);
?>
	<div id="block_best">
		<?
		if (isset($arResult["PROPOSAL"])) {

			foreach ($arResult["PROPOSAL"] as $v => $m) {
				if ($m["ITOGO"] > 0) {
					$min_pr[$m["USER_INFO"]["USER_ID"]] = $m["ITOGO"];
				}
			}
			$min_pr2 = $min_pr;
			if ($arResult["LOT"]["TYPE_ID"] == "P") {
				rsort($min_pr);
			} else {
				sort($min_pr);
			}
			$bestpr = $min_pr[0]; //лучшая цена
			$bestpr_usid = "";

			foreach ($min_pr2 as $key => $val) {
				if ($val == $bestpr) {
					$bestpr_usid = $key;
				}
			}

			//echo "Лучшая цена " . $bestpr . " руб.<br />";
			//echo "id поставщика " . $bestpr_usid;

		}
		?>
	</div>
<? $date_end = strtotime($arResult["LOT"]["DATE_END"]);
$date_tek = time();

//if (($date_tek > $date_end)  && ($date_end != 0))

?>
<div class="row">
	<div class="col-md-12">
		<h2>
			<i class="fa fa-check-circle" style="font-size:30px;color:#5aab00;" title="активный лот" data-toggle="tooltip" data-placement="left"></i>&nbsp;
			<?if ($arResult["LOT"]["PRIVATE"] == "Y"):?>
				<i class="fa fa-users" style="font-size:28px;color:#5aab00;" title="Закрытый лот" data-toggle="tooltip" data-placement="left"></i>
			<?endif;?>
			<?= GetMessage("PW_TD_LOT_NUM") ?>&nbsp;
			<?= $arResult["LOT"]["ID"] ?>&nbsp;
			<?= $arResult["LOT"]["TITLE"] ?>
		</h2>
		<? if ($arResult["TIME"] == ""): ?>
			<div class="alert alert-info" style="font-size:18px;text-align: center;">
				<b><?= GetMessage("PW_TD_BALANCE_TIME") ?>:</b> <span id="time"></span> <span id="time2"></span>
				<?if (($bestpr_usid != $USER->GetID()) && ($bestpr_usid != 0) && $arResult['LOT']['OPEN_PRICE'] == 'Y') {
					echo "<br /><b>Лучшее предложение сделано другим поставщиком.</b><br />";
				}?>
			</div>
		<? endif; ?>
	</div>
</div>

<form name="proposal_add" action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data">
	<?// Если лот открыт и можно делать предложния?>
	<?if ($arResult["TIME"] == ""): ?>
		<div class="row">
			<div class="col-md-12">
				<?  /*if(!empty($arResult["LOT"]["ACCESS"])) {
					if($arResult["LOT"]["ACCESS"] == "Y") {*/ ?>
						<? if ($arResult["LOT"]["TYPE_ID"] == "R" || $arResult["LOT"]["TYPE_ID"] == "T"): ?>
							<input class="btn btn-primary" type="submit" name="proposal_submit" value="Сделать предложение"/><br/><br/>
						<?else:?>
							<input class="btn btn-primary" type="submit" name="proposal_submit" value="<?= GetMessage("PROPOSAL_FORM_SAVE") ?>"/><br/><br/>
						<?endif;?>
					<? /*} else { ?>
						<input class="btn btn-primary proposal_request" type="button" name="proposal_request_yet" value="<?= GetMessage("PW_TD_REQUEST_YET") ?>" disabled /><br/><br/>
					<? }
				} else {?>
					<input class="btn btn-primary proposal_request" type="button" name="proposal_request" value="<?= GetMessage("PW_TD_REQUEST") ?>"/><br/><br/>
				<? }*/ ?>

				<? if (strlen($arResult["ERRORS"]) > 0): ?>
					<div class="errors-tender"><?= $arResult["ERRORS"] ?></div>
				<? endif; ?>

				<? if (strlen($arResult["ERRORS2"]) > 0): ?>
					<div class="errors-tender"><?= $arResult["ERRORS2"] ?></div>
				<? endif; ?>

				<? if ($arResult["SEND_OK"] == "Y"): ?>
					<div class="send-ok-tender"><?= GetMessage("PW_TD_SEND_OK") ?></div>
				<? endif; ?>
			</div>
		</div>
	<? endif; ?>


	<div class="row">
		<div class="col-md-7">
			<b><?= GetMessage("PW_TD_CURR") ?></b><br/>
			<select name="CURRENCY_PROPOSAL" >
				<? foreach ($arResult["CURRENCY"] as $nameCurrency => $arCurrency): ?>
					<option<?
					if ($arResult["CURRENCY_PROPOSAL"] == $nameCurrency || $nameCurrency == $_REQUEST["CURRENCY_PROPOSAL"])
						echo " selected";
					?> value="<?= $nameCurrency ?>"><?=
						$nameCurrency ?><?
						if (strlen($arCurrency["RATE"]) > 0)
							echo " [" . $arCurrency["RATE"] . "]";
						?></option>
				<? endforeach; ?>
			</select>
			<? foreach ($arResult["CURRENCY"] as $nameCurrency => $arCurrency): ?>
				<? if ($arResult["CURRENCY_PROPOSAL"] == $nameCurrency || $nameCurrency == $_REQUEST["CURRENCY_PROPOSAL"]):?>
					<input type="hidden" name="CURRENCY_PROPOSAL" value="<?= $nameCurrency ?>">
				<? endif;?>
			<? endforeach; ?>

			<? foreach ($arResult["CURRENCY"] as $nameCurrency => $arCurrency): ?>
				<input type="hidden" name="CURR[<?= $nameCurrency ?>]" value="<?= $arCurrency["RATE_NUM"] > 0 ? $arCurrency["RATE_NUM"] : 1 ?>"/>
			<? endforeach; ?>

			<input type="hidden" name="CURR_USER" value="<?= strlen($arResult["CURRENCY_PROPOSAL"]) > 0 ? $arResult["CURRENCY_PROPOSAL"] : $arParams["CURR"] ?>"/>

			<? if ($arResult["LOT"]["OPEN_PRICE"] == "Y"): ?>
				<br/><b class="t_open"><?= GetMessage("PW_TD_LOT_OPEN_PRICE") ?></b>
			<? endif; ?>

			<? if ($arResult["LOT"]["TYPE_ID"] == "R" || $arResult["LOT"]["TYPE_ID"] == "T"): ?>
				<br/><b class="t_open">Тип лота: <span class="label label-info">КОНКУРС<?if($arResult['LOT']['QUOTES'] == 'Y'):?>(Запрос котировок)<?endif;?></span></b>
			<?else:?>
				<? if ($arResult["LOT"]["TYPE_ID"] == "P"): ?>
					<br/><b class="t_open">Тип лота: продажа (повышение цены)</b>
					<br/><b class="t_open">Установка ставок ниже начальной запрещена.</b>
				<?else:?>
					<br/><b class="t_open">Тип лота: покупка (понижение цены)</b>
					<br/><b class="t_open">Установка ставок выше начальной запрещена.</b>
				<?endif;?>

				<? if ($arResult["LOT"]["NOSAME"] == "Y"): ?>
					<br/><b class="t_open">Установка равных ставок в лоте запрещена.</b>
				<? endif; ?>

				<? if ($arResult["LOT"]["NOBAD"] == "Y"): ?>
					<br/><b class="t_open">Установка ставок хуже лидирующей запрещена.</b>
				<? endif; ?>

				<? if ($arResult["LOT"]["ONLY_BEST"] == "Y"): ?>
					<br/><b class="t_open">Установка ставок хуже начальной запрещена.</b>
				<? endif; ?>

				<? if ($arResult["LOT"]["PRE_PROPOSAL"] == "Y"): ?>
					<br/><b class="t_open">Возможность делать ставки до начала торгов.</b>
				<? endif; ?>
			<?endif;?>
		</div>
		<div class="col-md-5" style="text-align:right;">
			<b>Стартовая цена: </b><span id="total_start_price"><?=number_format($arResult['START_PRICE'], 2, '.', ' ');?></span><br/>	
			<input type="hidden" id="total_start_price_full" value="<?=number_format($arResult['START_PRICE'], 10, '.', ' ');?>" />
			<? $summ = 0;
			foreach ($arResult["PROPERTY_SPEC"] as $specProp) {
				if (isset($arResult["PROPOSAL_SPEC"]) && $arResult["PROPOSAL_ID"] > 0) {
					$priceNDS = /*isset($_REQUEST["proposal_submit"]) ? floatval($_REQUEST["PROP_" . $specProp["ID"] . "_PRICE_NDS"]) :*/ $arResult["PROPOSAL_SPEC"][$specProp["ID"]]["PRICE_NDS"];
				} else {
					$priceNDS = /*isset($_REQUEST["proposal_submit"]) ? floatval($_REQUEST["PROP_" . $specProp["ID"] . "_PRICE_NDS"]) :*/ $specProp['START_PRICE'];
				}
				$count = $specProp['COUNT'];
				$summ += $priceNDS*$count;
			}?>
			<b>Минимальная цена: </b>
			
			<? 
			if ( $summ == $arResult['MIN_PRICE']) {
				
				?> <span id="total_min_price" style="color:green"> <? echo number_format($arResult['MIN_PRICE'], 2, '.', ' ');?> </span><br/>
				   <input type="hidden" id="total_min_price_full" value="<? echo number_format($arResult['MIN_PRICE'], 10, '.', ' ');?>" />
			<? } else { ?>
				<span id="total_min_price">
				<? echo number_format($arResult['MIN_PRICE'], 2, '.', ' '); ?>
				</span>
				<input type="hidden" id="total_min_price_full" value="<? echo number_format($arResult['MIN_PRICE'], 10, '.', ' ');?>" />
			<? }?>  
			</div>
			<?// if($arResult['LOT']['DOPPROP'][6]['VALUE']):?>
			<!--<strong>Курс EUR: </strong>--><? //=$arResult['LOT']['DOPPROP'][6]['VALUE'];?><br>
			<? //endif;?>
			<?// if($arResult['LOT']['DOPPROP'][5]['VALUE']):?>
			<!--<strong><strong>Курс USD: </strong>--><? //=$arResult['LOT']['DOPPROP'][5]['VALUE'];?>
			<? //endif;?>
			<div class="pull-right" style="margin: 15px 0px;">
			<?$APPLICATION->IncludeComponent(
				"bitrix:currency.rates",
				"",
				Array(
					"CACHE_TIME" => "86400",
					"CACHE_TYPE" => "N",
					"CURRENCY_BASE" => "KZT",
					"RATE_DAY" => "",
					"SHOW_CB" => "N",
					"arrCURRENCY_FROM" => array("RUB","USD","EUR","UAH")
				)
			);?>
			</div>
		</div>
	</div>

	<? //Информация о лоте?>
	<table class="t_lot_table">
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
				<? if ($arResult["LOT"]["TIME_EXTENSION"] > 0) : ?>
					<span
						class="time_ext">(+ <?= round($arResult["LOT"]["TIME_EXTENSION"] / 60, 1); ?> <?= GetMessage("PW_TD_MINUTES") ?>
						)</span>
				<? endif; ?></td>
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
						<a href="/tx_files/lot_file.php?LOT_ID=<?= $arResult["LOT"]["ID"] ?>&FILE_ID=<?= $arFile["ID"] ?>"><?= $arFile["ORIGINAL_NAME"] ?></a>
						<br/>
						<?
					}
					?></td>
			</tr>
		<? endif; ?>
	</table>

	<?if ($arResult["TIME"] == ""): ?>
		<? if($arResult["LOT"]["TYPE_ID"] != "N") { ?>
		<div style="margin-top:20px;position:relative;" class="alert alert-warning message-dopprop">
			<h4>Спецификация СЭТ заполняется только в рублях.</h4>
			<a class="button-hide" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
				<i class="fa fa-arrow-up"></i><i class="fa fa-arrow-down" style="display:none;"></i>
			</a>
			<div class="collapse in" id="collapseExample">
				<p>В случае подачи предложения в иной валюте - для заполнения Спецификации СЭТ, необходимо сконвертировать все цены в рубли по курсу, указанному в Лоте. <br>
					В случае, если для данного Лота также предусмотрено обязательное заполнение Спецификации в формате Excel – итоговые суммы в спецификации СЭТ должны соответствовать итоговым суммам в Спецификации Excel, прикрепляемой к предложению. <br>
					В случае выявлении расхождений предложение может быть признано несоответствующим требованиям лота.
				</p>
			</div>
		</div>
		<? } ?>
	<?endif;?>

	<? if ($arResult["LOT"]["TYPE_ID"] != "S" && $arResult["LOT"]["TYPE_ID"] != "R"): ?>
		<div style="overflow-x:auto !important; overflow-y:hidden !important;">
		<?
		//__($arResult["SPEC"]["FULL_SPEC"]);
		//echo "кол-во товаров " . count($arResult["PROPERTY_SPEC"]);
		// if ((count($arResult["PROPERTY_SPEC"]) > 1) && ($arResult["SPEC"]["FULL_SPEC"] == "N"))
		?>
		<table class="t_lot_table">
			<? $numProp = 1; ?>
			<? foreach ($arResult["PROPERTY_SPEC"] as $specProp): ?>
				<?
				$mypr = 0;
				if (isset($arResult["PROPOSAL_SPEC"]) && $arResult["PROPOSAL_ID"] > 0) {
					$mypr = 1;
				}
				?>
				<? if ($numProp == 1): ?>
					<tr>
						<th><?= GetMessage("PW_TD_NUM") ?></th>
						<th><?= GetMessage("PW_TD_TOVAR") ?></th>
						<? if ($specProp["NOT_ANALOG"] == "N"): ?>
							<th><?= GetMessage("PW_TD_ANALOG") ?></th>
						<? endif; ?>
						<th><?= GetMessage("PW_TD_NDS") ?></th>
						<? if ($arParams["NDS_TYPE"] == "N"): ?>
							<th><?= GetMessage("PW_TD_PRICE_NDS_N") ?></th>
						<? else: ?>
							<th><?= GetMessage("PW_TD_PRICE_NDS") ?></th>
						<? endif; ?>
						<? $gr_style = ""; ?>
						<? if ((count($arResult["PROPERTY_SPEC"]) > 1) && ($arResult["SPEC"]["FULL_SPEC"] == "N") && ($date_tek < $date_end) && ($date_end != 0)): ?>
							<th>Предложение</th>
						<? endif; ?>
						<? //if ((count($arResult["PROPERTY_SPEC"]) > 1) && ($arResult["SPEC"]["FULL_SPEC"] == "N") && ($mypr == 0)): ?>
						<?
						//$gr_style = " class='gr_style'";
						?>
						<? //endif; ?>
						<? if ($mypr == 1): ?>
							<th width="100px">Мое<br/>предложение</th>
						<? endif; ?>
						<? if ($arResult["LOT"]["OPEN_PRICE"] == "Y"): ?>
							<th><?= GetMessage("PW_TD_BEST_PROPOSAL") ?></th>
						<? endif; ?>
						<th>Стартовая цена</th>
						<th>Минимальная цена</th>
					</tr>
				<? endif; ?>
				<tr<?= $gr_style; ?>>
					<td align="center"><? echo $numProp ?></td>
					<td>
						<b><?= GetMessage("PW_TD_SPEC_NAME_PROD") ?>:</b> <?= $specProp["TITLE"] ?><br/>
						<b><?= GetMessage("PW_TD_SPEC_COUNT") ?>
							:</b> <?= $specProp["COUNT"] ?>  <?= $specProp["UNIT_NAME"] ?><br/>
						<? if (strlen($specProp["ADD_INFO"]) > 0): ?>
							<b><?= GetMessage("PW_TD_SPEC_ADD_INFO") ?>:</b> <?= $specProp["ADD_INFO"] ?>
						<? endif; ?>
					</td>
					<? if ($specProp["NOT_ANALOG"] == "N"): ?>
						<?
						//если есть пердложение
						if (isset($arResult["PROPOSAL_SPEC"]) && $arResult["PROPOSAL_ID"] > 0) {
							$ANALOG = isset($_REQUEST["proposal_submit"]) ? htmlspecialcharsEx($_REQUEST["PROP_" . $specProp["ID"] . "_ANALOG"]) : $arResult["PROPOSAL_SPEC"][$specProp["ID"]]["ANALOG"];
						} else {
							$ANALOG = isset($_REQUEST["proposal_submit"]) ? htmlspecialcharsEx($_REQUEST["PROP_" . $specProp["ID"] . "_ANALOG"]) : "";
						}
						?>
						<td>
							<? if ($arResult["TIME"] == ""): ?>
								<textarea name="PROP_<?= $specProp["ID"] ?>_ANALOG" rows="4"
										  cols="15"><?= htmlspecialchars($ANALOG) ?></textarea>
							<? endif; ?>
						</td>
					<? endif; ?>
					<td>
						<?
						//если есть пердложение
						if (isset($arResult["PROPOSAL_SPEC"]) && $arResult["PROPOSAL_ID"] > 0) {
							$NDS = isset($_REQUEST["proposal_submit"]) ? intval($_REQUEST["PROP_" . $specProp["ID"] . "_NDS"]) : $arResult["PROPOSAL_SPEC"][$specProp["ID"]]["NDS"];
						} else {
							$NDS = isset($_REQUEST["proposal_submit"]) ? intval($_REQUEST["PROP_" . $specProp["ID"] . "_NDS"]) : 0;
						}
						?>
						<? if ($arResult["TIME"] == ""): ?>
							<select name="PROP_<?= $specProp["ID"] ?>_NDS" id="PROP_<?= $specProp["ID"] ?>_NDS">
								<option value="0">0</option>
								<option<?
								if ($NDS == 10)
									echo " selected"
								?> value="10">10
								</option>
								<option<?
								if ($NDS == 18)
									echo " selected"
								?> value="18">18
								</option>
							</select>
						<? else: ?>
							<?= $NDS ?>
						<?endif; ?>
					</td>
					<td class="item-proposal-price">
						<?
						//если есть предложение
						if (isset($arResult["PROPOSAL_SPEC"]) && $arResult["PROPOSAL_ID"] > 0) {
							$priceNDS = /*isset($_REQUEST["proposal_submit"]) ? floatval($_REQUEST["PROP_" . $specProp["ID"] . "_PRICE_NDS"]) :*/ $arResult["PROPOSAL_SPEC"][$specProp["ID"]]["PRICE_NDS"];
						} else {
							$priceNDS = /*isset($_REQUEST["proposal_submit"]) ? floatval($_REQUEST["PROP_" . $specProp["ID"] . "_PRICE_NDS"]) :*/ $specProp['START_PRICE'];
						}
						
						// $priceNDS *= 100; 
						?>
						<!--<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.3.1/jquery.maskedinput.min.js"></script>-->
						<script src="<?= $templateFolder?>/jquery.mask.min.js"></script>
						
						<script>
						$(function(){
							//Использование параметра completed
							  $(".item_proposal_price").mask('# ##0.00', {reverse: true});
						});
						</script>
						<? if (intval($specProp['START_PRICE']) > 0): ?>
							<? if ($specProp['STEP_PRICE'] > 0) { ?>
								<? if ($arResult["TIME"] == ""): ?>
									<?if($arResult['TYPE_ID'] == 'S' || $arResult['TYPE_ID'] == 'T'):?>
										<? if ((count($arResult["PROPERTY_SPEC"]) > 1) && ($arResult["SPEC"]["FULL_SPEC"] == "N") && ($mypr == 0)) { ?>
											<input type="hidden" class="item_proposal_price w80"
												   name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS"
												   value="<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>"/>
											<input type="text" size="10" class="item_proposal_price w80"
												   name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS_dis"
												   value="<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>" disabled/>
										<?} else {?>
											<input type="text" style="width:100%;" size="10" class="form-control"
												   name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS"
												   value="<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>"/>
										<? } ?>
									<?else:?>
										<!--input type="text" size="10" class="item_proposal_price w80" name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS" readonly value="<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>" /-->

										<? if ((count($arResult["PROPERTY_SPEC"]) >= 1) /*&& ($arResult["SPEC"]["FULL_SPEC"] == "N") && ($mypr == 0)*/) { ?>
											<?//__($mypr);?>
											<input type="hidden" class="item_proposal_price w80" name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS" value="<?= number_format($priceNDS, 2, '.', ' '); ?>" />
											<input type="text" size="10" class="item_proposal_price w80" name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS_dis" value="<?= number_format($priceNDS, 2, '.', ' '); ?>" disabled />
												<? /*if ((count($arResult["PROPERTY_SPEC"]) > 1) && ($arResult["SPEC"]["FULL_SPEC"] == "N") && ($mypr == 0)) { ?>
												<input type="hidden" class="item_proposal_price w80"
												   name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS"
												   value="<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>"/>
												<input type="text" size="10" class="item_proposal_price w80"
												   name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS_dis"
												   value="<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>" disabled/>
												<?
												} elseif ((count($arResult["PROPERTY_SPEC"]) > 1) && ($arResult["SPEC"]["FULL_SPEC"] == "N") && ($mypr == 1)) { ?>
													<input type="hidden" class="item_proposal_price w80" name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS" value="<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>" />
													<input type="text" size="10" class="item_proposal_price w80" name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS_dis" value="<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>" disabled />
										   		<? } else {?>
													<input type="text" size="10" class="item_proposal_price w80"
												   name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS" readonly
												   value="<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>"/>
												<? }*/ ?>
											<a onclick="stepDown(<?= $specProp["ID"] ?>,<?= $specProp['STEP_PRICE'] ?>,<?= $numProp; ?>,<?= $specProp['START_PRICE']; ?>); return false;"
										   	href="#"><i class="fa fa-minus-circle" style="color:red;font-size: 17px;" ></i></a>
											<a onclick="stepUp(<?= $specProp["ID"] ?>,<?= $specProp['STEP_PRICE'] ?>,<?= $numProp; ?>,<?= $specProp['START_PRICE']; ?>); return false;"
										   	href="#"><i class="fa fa-plus-circle" style="color:green;font-size: 17px;"></i></a>
											<br/>
											<div id="PROP_<?= $specProp["ID"] ?>_err_span" class="error_span"></div>
										   <?}?>
										<? endif; ?>
									<?// else: ?>
										<?//= number_format(round($priceNDS, 2), 2, '.', ' '); ?>
									<?endif; ?>
							<? } else { ?>
								<? if ($arResult["TIME"] == ""): ?>
									<? if ((count($arResult["PROPERTY_SPEC"]) > 1) && ($arResult["SPEC"]["FULL_SPEC"] == "N") && ($mypr == 0)) { ?>
										<input type="text" size="10" class="item_proposal_price w80"
											   name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS"
											   value="<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>"
											   onchange="step2(<?= $numProp; ?>);"/>
									<? } elseif ((count($arResult["PROPERTY_SPEC"]) > 1) && ($arResult["SPEC"]["FULL_SPEC"] == "N") && ($mypr == 1)) { ?>
										<input type="text" size="10" class="item_proposal_price w80"
											   name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS"
											   value="<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>"
											   onchange="step2(<?= $numProp; ?>);"/>
									<? } else { ?>
										<input type="text" size="10" class="item_proposal_price w80"
											   name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS"
											   value="<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>"/>
									<? } ?>
								<? else: ?>
									<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>
								<?endif; ?>
							<? } ?>
						<? else: ?>
							<? if ($arResult["TIME"] == ""): ?>
								<input type="text" size="10" class="item_proposal_price"
									   name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS"
									   value="<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>"/>
							<? else: ?>
								<?if(round($priceNDS, 2) == '0'):?>
									Не установлена
								<?else:?>
									<?= number_format(round($priceNDS, 2), 2, '.', ' '); ?>
								<?endif?>
							<?endif; ?>
						<? endif; ?>
						<input type="hidden" class="id_item_proposal" value="<?= $specProp["ID"]; ?>"/>
						<input type="hidden" id="price_nds_full_<?= $specProp["ID"]; ?>" value="<?= $priceNDS; ?>"/>
					</td>
					<?
					// $checked = "";
					// if ((count($arResult["PROPERTY_SPEC"]) > 1) && ($arResult["SPEC"]["FULL_SPEC"] == "N") && ($mypr == 1)) {
					// 	$checked = " checked";
					// }

					$checked = "";
					if (((count($arResult["PROPERTY_SPEC"]) > 1) && ($arResult["SPEC"]["FULL_SPEC"] == "N") && ($mypr == 1) && (($arResult["PROPOSAL_ID"] > 0) && ($priceNDS > 0))) || ((count($arResult["PROPERTY_SPEC"]) > 1) && ($arResult["SPEC"]["FULL_SPEC"] == "N") && ($mypr == 0))) {
						$checked = " checked";
					}

					?>
					<? if ((count($arResult["PROPERTY_SPEC"]) > 1) && ($arResult["SPEC"]["FULL_SPEC"] == "N") && ($date_tek < $date_end) && ($date_end != 0)): ?>
						<td><? echo "<a href='javascript:void(0);' onclick='step($numProp,$priceNDS,$specProp[ID],$specProp[START_PRICE]);'><input id='zero_$numProp' type='checkbox' $checked /></a>"; ?></td>
					<? endif; ?>

					<? if ($mypr == 1): ?>
						<td id="my_price_<?= $specProp["ID"] ?>" width="100px"><?= number_format($priceNDS, 2, '.', ' '); ?></td>
						<input type="hidden" id ="my_price_full_<?= $specProp["ID"] ?>" value="<?= number_format($priceNDS, 10, '.', ' ');?>"/>
					<? endif; ?>
					<? if ($arResult["LOT"]["OPEN_PRICE"] == "Y"): ?>
						</td>
						<td align="center" class="best_proposal_n" id="best_proposal_<?= $specProp["ID"] ?>"><?if(isset($specProp["ID"])):?><strong>Предложений еще нет</strong><?endif;?></td>
						<input type="hidden" id="best_proposal_full_<?= $specProp["ID"] ?>" value=""/>
					<? endif; ?>
					<td id="start_price_<?= $specProp["ID"] ?>"><?=number_format($specProp['START_PRICE'], 2, '.', ' '); ?></td>
					<input type="hidden" id ="start_price_full_<?= $specProp["ID"] ?>" value="<?=number_format($specProp['START_PRICE'], 10, '.', ' '); ?>"/>
					<td id="min_price_<?= $specProp["ID"] ?>"><?=number_format($arResult['PRODUCT_MIN_SUM'][$specProp['PROP_ID']]['PRICE_NDS'], 2, '.', ' ');?></td>
					<input type="hidden" id ="min_price_full_<?= $specProp["ID"] ?>" value="<?=number_format($arResult['PRODUCT_MIN_SUM'][$specProp['PROP_ID']]['PRICE_NDS'], 10, '.', ' ');?>"/>
				</tr>
				<? $numProp++; ?>
			<? endforeach; ?>
		</table>
		</div>
	<? endif; ?>


	<? //Закончилась информация о лоте?>

	<? if ($arResult["LOT"]["TERM_PAYMENT_ID"] > 0): ?>
		<p><b><?= GetMessage("PW_TD_TERM_PAYMENT") ?>:</b> <?= $arResult["PAYMENT"] ?><br/>
			<? if ($arResult["LOT"]["TERM_PAYMENT_EDIT"] == "Y"): ?>
				<? if ($arResult["TIME"] == ""): ?>
					<textarea name="TERM_PAYMENT_VAL" cols="80"
							  rows="5"><?= isset($_REQUEST["proposal_submit"]) ? htmlspecialcharsEx($_REQUEST["TERM_PAYMENT_VAL"]) : $arResult["LOT"]["TERM_PAYMENT_VAL"] ?></textarea>
				<? else: ?>
					<?= $arResult["LOT"]["TERM_PAYMENT_VAL"]; ?>
				<?endif; ?>
			<? else: ?>
				<?= $arResult["LOT"]["TERM_PAYMENT_VAL"] ?>
			<? endif; ?>
		</p>
	<? endif; ?>

	<? if ($arResult["LOT"]["TERM_DELIVERY_ID"] > 0): ?>
		<p><b><?= GetMessage("PW_TD_TERM_DELIVERY") ?>:</b> <?= $arResult["DELIVERY"] ?><br/>
			<? if ($arResult["LOT"]["TERM_DELIVERY_EDIT"] == "Y"): ?>
				<? if ($arResult["TIME"] == ""): ?>
					<b>Выберите условия поставки:</b>
					<select name="TERM_DELIVERY_VAL">
						<option value=""></option>
						<? foreach ($arResult["DELIVERY_ARRAY"] as $k => $v) { ?>
							<option value="<? echo $v?>"><? echo $v?></option>
						<?} ?>
					</select>
					<!--<textarea name="TERM_DELIVERY_VAL" cols="80"
							  rows="5">
					</textarea-->
				<? else: ?>
					<?= $arResult["LOT"]["TERM_DELIVERY_VAL"]; ?>
				<?endif; ?>
			<? else: ?>
				<?= $arResult["LOT"]["TERM_DELIVERY_VAL"] ?>
			<? endif; ?>
		</p>
	<? endif; ?>

	<? if (strlen($arResult["LOT"]["NOTE"]) > 0): ?>
		<p><b><?= GetMessage("PW_TD_NOTE") ?>:</b><br/>
			<?= html_entity_decode($arResult["LOT"]["NOTE"]) ?>
		</p>
	<? endif; ?>
	<? if ($arResult["TIME"] == ""): ?>
		<?
		/*****************
		 * PROPERTY
		 *****************/
		?>
		<script type="text/javascript">
			function addNewElem(id, cnt) {
				var idProp = parseInt($("#id-prop-" + id).val());
				var str = $("#prop-" + id + "-" + (idProp - 1)).html();
				var nidProp = idProp - parseInt(cnt);
				str = str.replace(/\[n\d+\]/g, "[n" + nidProp + "]");
				$("#prop-" + id).append('<div id="prop-' + id + '-' + idProp + '">' + str + '</div>');
				idProp += 1;
				$("#id-prop-" + id).val(idProp);
			}
		</script>

	<table class="table table-bordered">

		<?
		$arPropProposal = $arResult["PROP_PROPOSAL"];
		foreach ($arResult["PROP_LIST"] as $arPropList) {
			if($arPropList['ID'] == '1'){?>
				<tr>
					<td><?= $arPropList["TITLE"] ?><br>
						<?= $arPropList["DESCRIPTION"] ?> <span style="color:red;" class="required small">* (обязательно к заполнению)</span>
					</td>
					<td>
						<?
						$result = "";
						$arrList = unserialize(base64_decode($arPropList["DEFAULT_VALUE"]));
						if ($arResult["PROPOSAL_ID"] > 0) {
							foreach ($arPropProposal[$arPropList["ID"]] as $arrListSupplier) {
								$arrListValue = $arrListSupplier["VALUE"];
							}
						} else {
							$arrListValue[] = $arrList["DEFAULT_VALUE_SELECT"];
						}
						if (isset($_REQUEST["PROP"][$arPropList["ID"]])) {
							unset($arrListValue);
							$arrListValue = $_REQUEST["PROP"][$arPropList["ID"]];
						}
						$result .= '<select class="prop_1_sel form-control" name="PROP[' . $arPropList["ID"] . '][]"' . ($arPropList["MULTI"] == "Y" ? " multiple" : "") . ' size="' . $arPropList["ROW_COUNT"] . '">';
						foreach ($arrList["DEFAULT_VALUE"] as $idRow => $listVal) {
							$result .= '<option class="'.(($idRow == 2) ? "prop_sel" : "").'" '.(/*in_array*/($idRow == $arrListValue) ? " selected" : "") . ' value="' . $idRow . '">' . $listVal . '</option>';
						}
						$result .= '</select>';

						echo $result;
						?>
						<div style="display:none;" id="prop_1_more" >
							<br>
							<?
							foreach ($arResult["PROP_LIST"] as $arPropList) {
								if ($arPropList['ID'] == '2') {
									$cntProp = 0;
									for ($i = 0; $i < $arPropList["MULTI_CNT"]; $i++) {
										$result = "";
										if ($arResult["PROPOSAL_ID"] > 0) {
											$propName = "PROP[" . $arPropList["ID"] . "][" . $arPropProposal[$arPropList["ID"]][$i]["ID"] . "]";
											$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"]);
										} else {
											$propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
											$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : htmlspecialcharsEx($arPropList["DEFAULT_VALUE"]);
										}
										//$req = ($arPropList["IS_REQUIRED"] == "Y" ? "required" : "");
										if ($arPropList["ROW_COUNT"] <= 1) {
											$result .= '<input class="form-control prop_2_input" name="' . $propName . '" type="text" value="' . $propValue . '" size="' . $arPropList["COL_COUNT"] . '" placeholder="'.$arPropList["TITLE"].'"/>';
										} else {
											$result .= '<textarea class="form-control prop_2_input" name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] .'>' . $propValue . '</textarea>';
										}
									}
									echo $result;
								}
							}
							?>
						</div>
						<script>
							$('.prop_1_sel').on('change', function() {
								if(this.value == 2) {
									$('#prop_1_more').show(1000);
									$('.prop_2_input').attr("required", "required");
								}else {
									$('#prop_1_more').hide(1000);
									$('.prop_2_input').removeAttr("required");
								}
							});
							$(document).ready(function(){
								if ($('.prop_1_sel .prop_sel').attr('selected')) {
									$('#prop_1_more').show();
								}
								if($('#prop_1_more').is(":visible")) {
									$('.prop_2_input').attr("required", "required");
								}
								else {
									$('.prop_2_input').removeAttr("required");
								}
							});
						</script>
					</td>
				</tr>
			<?}

			if($arPropList['ID'] == '3'){?>
				<tr>
					<td width="40%"><?= $arPropList["TITLE"] ?><br>
						<?= $arPropList["DESCRIPTION"] ?> <span style="color:red;" class="required small">* (обязательно к заполнению)</span>
					</td>
					<td>
						<?
						$result = "";
						$arrList = unserialize(base64_decode($arPropList["DEFAULT_VALUE"]));
						if ($arResult["PROPOSAL_ID"] > 0) {
							foreach ($arPropProposal[$arPropList["ID"]] as $arrListSupplier) {
								$arrListValue = $arrListSupplier["VALUE"];
							}
						} else {
							$arrListValue = $arrList["DEFAULT_VALUE_SELECT"];
						}
						if (isset($_REQUEST["PROP"][$arPropList["ID"]])) {
							//unset($arrListValue);
							$arrListValue = $_REQUEST["PROP"][$arPropList["ID"]];
						}
						$result .= '<select class="prop_3_sel form-control" name="PROP[' . $arPropList["ID"] . '][]"' . ($arPropList["MULTI"] == "Y" ? " multiple" : "") . ' size="' . $arPropList["ROW_COUNT"] . '">';
						foreach ($arrList["DEFAULT_VALUE"] as $idRow => $listVal) {
							$result .= '<option '.(($idRow == 1 || $idRow == 2 || $idRow == 3) ? " class='prop_sel'" : "").' '.(/*in_array*/($idRow == $arrListValue) ? " selected" : "") . ' value="' . $idRow . '">' . $listVal . '</option>';
						}
						$result .= '</select>';
						echo $result;
						?>
						<div style="display:none;" id="prop_3_more">
							<br>
							<?
							foreach ($arResult["PROP_LIST"] as $arPropList) {
								if ($arPropList['ID'] == '4') {
									$cntProp = 0;
									for ($i = 0; $i < $arPropList["MULTI_CNT"]; $i++) {
										$result = "";
										if ($arResult["PROPOSAL_ID"] > 0) {
											$propName = "PROP[" . $arPropList["ID"] . "][" . $arPropProposal[$arPropList["ID"]][$i]["ID"] . "]";
											$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"]);
										} else {
											$propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
											$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : htmlspecialcharsEx($arPropList["DEFAULT_VALUE"]);
										}
										if ($arPropList["ROW_COUNT"] <= 1) {
											$result .= '<input class="form-control more-text1 prop_4_input" name="' . $propName . '" type="text" value="' . $propValue . '" size="' . $arPropList["COL_COUNT"] . '" placeholder="'.$arPropList["TITLE"].'" />';
										} else {
											$result .= '<textarea class="form-control" name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $propValue . '</textarea>';
										}
									}
									echo $result;
								}
								if ($arPropList['ID'] == '5') {
									$cntProp = 0;
									for ($i = 0; $i < $arPropList["MULTI_CNT"]; $i++) {
										$result = "";
										if ($arResult["PROPOSAL_ID"] > 0) {
											$propName = "PROP[" . $arPropList["ID"] . "][" . $arPropProposal[$arPropList["ID"]][$i]["ID"] . "]";
											$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"]);
										} else {
											$propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
											$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : htmlspecialcharsEx($arPropList["DEFAULT_VALUE"]);
										}
										//$req = ($arPropList["IS_REQUIRED"] == "Y" ? "required" : "");
										if ($arPropList["ROW_COUNT"] <= 1) {
											$result .= '<input class="form-control more-text2 prop_5_input"  name="' . $propName . '" type="text" value="' . $propValue . '" size="' . $arPropList["COL_COUNT"] . '" placeholder="'.$arPropList["TITLE"].'" />';
										} else {
											$result .= '<textarea class="form-control" name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $propValue . '</textarea>';
										}
									}
									echo $result;
								}
							}
							?>
							<div id="contenInput"></div>
						</div>
						<script>
							$('.prop_3_sel').on('change', function() {
								if(this.value == 1 || this.value == 2 ) {
									$('#prop_3_more').show(1000);
									if(this.value == 2){
										$('#prop_3_more input').attr('maxlength', '300');
										$('#prop_3_more input.more-text1').hide();
										$('#prop_3_more input.more-text2').attr('placeholder', 'Внесите свое предложение');
										$('#prop_3_more input').unbind("change keyup click");
										$('.prop_5_input').attr("required", "required");
										$('.prop_4_input').removeAttr("required");
										/* TODO */
									}else {
										$('#prop_3_more input.more-text2').attr('maxlength', '3');
										$('#prop_3_more input.more-text1').show().attr('placeholder', 'Этапы и варианты оплаты');
										$('#prop_3_more input.more-text2').show().attr('placeholder', 'Укажите % первого платежа');
										$('.prop_4_input').attr("required", "required");
										$('.prop_5_input').attr("required", "required");
										$('#prop_3_more input.more-text2').bind("change keyup click", function() {
											if (this.value.match(/[^0-9]/g)) {
												this.value = this.value.replace(/[^0-9]/g, '');
											}
										});
									}
								}else {
									$('#prop_3_more').hide(1000);
									$('.prop_4_input').removeAttr("required");
									$('.prop_5_input').removeAttr("required");
								}
							});
							$(document).ready(function(){
								if ($('.prop_3_sel .prop_sel').attr('selected')) {
									$('#prop_3_more').show();
								}
								$('.prop_3_sel').trigger('change');
								if($('#prop_3_more').is(":visible")) {
									if($('.prop_5_input').is(":visible")) {
										$('.prop_5_input').attr("required", "required");
									}
									else {
										$('.prop_5_input').removeAttr("required");
									}
									if($('.prop_4_input').is(":visible")) {
										$('.prop_4_input').attr("required", "required");
									}
									else {
										$('.prop_4_input').removeAttr("required");
									}
								}
								else {
									$('.prop_5_input').removeAttr("required");
									$('.prop_4_input').removeAttr("required");
								}
							});
						</script>
					</td>
				</tr>
			<?}

			if($arPropList['ID'] == '8'){ ?>
				<tr>
					<td width="40%"><?= $arPropList["TITLE"] ?>
							<span style="color:red;" class="required small">* (обязательно к заполнению)</span>
						<br />
						<?= $arPropList["DESCRIPTION"] ?>
					</td>
					<td>

						<? $is_file_prop = false; ?>
						<? if ($arPropList["PROPERTY_TYPE"] == "F" && ($arResult["PROPOSAL_ID"] > 0) && ($rsFiles = CTenderixProposal::GetFileListProperty($arResult["PROPOSAL_ID"], $arPropList["ID"])) && ($arFile = $rsFiles->GetNext())) { ?>
							<? $is_file_prop = true; ?>
							<strong><?= GetMessage("PW_TD_FILE_ATTACH_LIST") ?>:</strong>
							<table>
								<tr>
									<td>
										<table class="t_lot_table">
											<tr>
												<th><? echo GetMessage("PW_TD_FILE_NAME") ?></th>
												<th><? echo GetMessage("PW_TD_FILE_SIZE") ?></th>
												<th><? echo GetMessage("PW_TD_FILE_DELETE") ?></th>
											</tr>
											<?
											do {
												?>
												<tr>
													<td>
														<a href="/tx_files/property_file.php?PROPOSAL_ID=<?= $arResult["PROPOSAL_ID"] ?>&amp;FILE_ID=<?= $arFile["ID"] ?>&amp;PROPERTY=<?= $arPropList["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a>
													</td>
													<td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
													<td align="center">
														<input type="checkbox" name="FILE_ID_PROP[<? echo $arFile["ID"] ?>]"
															   value="<? echo $arFile["ID"] ?>">
														<input type="hidden"
															   name="PROP[<?= $arPropList["ID"] ?>][<?= $arFile["ID"] ?>]"/>
														<input type="hidden" name="FILE_PROP" value="<?= $arPropList["ID"] ?>"/>
													</td>
												</tr>
											<? } while ($arFile = $rsFiles->GetNext()); ?>
										</table>
									</td>
								</tr>
							</table>
						<? } ?>
						<?
						$result = "";
						if (strlen($arPropList["DEFAULT_VALUE"]) > 0 && $arPropList["MULTI"] == "Y") {
							$arPropList["MULTI_CNT"]++;
						}
						$cntProp = 0;
						if ($arResult["PROPOSAL_ID"] > 0 && $arPropList["PROPERTY_TYPE"] != "L" && $arPropList["PROPERTY_TYPE"] != "F") {
							$cntProp = count($arPropProposal[$arPropList["ID"]]);
							$arPropList["MULTI_CNT"] += $cntProp;
						}
						if (isset($_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]]) &&
							$_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]] >= $arPropList["MULTI_CNT"] &&
							$arPropList["PROPERTY_TYPE"] != "L" &&
							$arPropList["PROPERTY_TYPE"] != "F"
						) {
							if (strlen($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]] - $cntProp - 1)]) > 0) {
								$arPropList["MULTI_CNT"] = $_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]] + 1;
							} else {
								$arPropList["MULTI_CNT"] = $_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]];
							}
						}
						if ($arPropList["PROPERTY_TYPE"] == "L" || $arPropList["MULTI"] == "N") {
							$arPropList["MULTI_CNT"] = 1;
						}
						$result .= '<br /><span id="prop-' . $arPropList["ID"] . '">';
						for ($i = 0; $i < $arPropList["MULTI_CNT"]; $i++) {
							$result .= '<span id="prop-' . $arPropList["ID"] . '-' . $i . '">';
							switch ($arPropList["PROPERTY_TYPE"]) {
								case "S":
								case "N":
									if ($i > 0 || $arResult["PROPOSAL_ID"] > 0) {
										$arPropList["DEFAULT_VALUE"] = "";
									}
									if ($arResult["PROPOSAL_ID"] > 0 && $i < $cntProp) {
										$propName = "PROP[" . $arPropList["ID"] . "][" . $arPropProposal[$arPropList["ID"]][$i]["ID"] . "]";
										$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"]);
									} else {
										$propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
										$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : htmlspecialcharsEx($arPropList["DEFAULT_VALUE"]);
									}
									$req = ($arPropList["IS_REQUIRED"] == "Y" ? "required" : "");
									if ($arPropList["ROW_COUNT"] <= 1) {
										$result .= '<input class="form-control" name="' . $propName . '" type="text" value="' . $propValue . '" size="' . $arPropList["COL_COUNT"] . '" ' . $req . ' />';
									} else {
										$result .= '<textarea class="form-control" ' . $prop . ' name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $propValue . '</textarea>';
									}
									break;
								case "F":
									if (!$is_file_prop || $arPropList["MULTI"] == "Y")
										$result .= '<input type="file" name="PROP[' . $arPropList["ID"] . '][n' . ($i - $cntProp) . ']" size="' . $arPropList["COL_COUNT"] . '" />';
									break;
								case "L":
									$arrList = unserialize(base64_decode($arPropList["DEFAULT_VALUE"]));
									if ($arResult["PROPOSAL_ID"] > 0) {
										//foreach ($arPropProposal[$arPropList["ID"]] as $arrListSupplier) {
										$arrListValue = $arPropProposal[$arPropList["ID"]][0]["VALUE"];
										//}
									} else {
										$arrListValue = $arrList["DEFAULT_VALUE_SELECT"];
									}
									if (isset($_REQUEST["PROP"][$arPropList["ID"]])) {
										unset($arrListValue);
										$arrListValue = $_REQUEST["PROP"][$arPropList["ID"]];
									}
									$result .= '<select class="form-control" name="PROP[' . $arPropList["ID"] . '][]"' . ($arPropList["MULTI"] == "Y" ? " multiple" : "") . ' size="' . $arPropList["ROW_COUNT"] . '">';
									foreach ($arrList["DEFAULT_VALUE"] as $idRow => $listVal) {
										$result .= '<option' . (($idRow == $arrListValue) ? " selected" : "") . ' value="' . $idRow . '">' . $listVal . '</option>';
									}
									$result .= '</select>';
									break;
								case "T":
									if ($i > 0 || $arResult["PROPOSAL_ID"] > 0) {
										$arPropList["DEFAULT_VALUE"] = "";
									}
									if ($arResult["PROPOSAL_ID"] > 0 && $i < $cntProp) {
										$propName = "PROP[" . $arPropList["ID"] . "][" . $arPropProposal[$arPropList["ID"]][$i]["ID"] . "]";
										$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"]);
									} else {
										$propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
										$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : htmlspecialcharsEx($arPropList["DEFAULT_VALUE"]);
									}
									$req = ($arPropList["IS_REQUIRED"] == "Y" ? "required" : "");
									$result .= '<textarea class="form-control" '.$req.' rows="3" name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $propValue . '</textarea>';
									break;
								case "D":
									if ($i > 0 || $arResult["PROPOSAL_ID"] > 0) {
										$arPropList["DEFAULT_VALUE"] = "";
									}
									if ($arResult["PROPOSAL_ID"] > 0 && $i < $cntProp) {
										$propName = "PROP[" . $arPropList["ID"] . "][" . $arPropProposal[$arPropList["ID"]][$i]["ID"] . "]";
										$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) : (strlen($arPropProposal[$arPropList["ID"]][$i]["VALUE"]) > 0 ? ConvertTimeStamp(strtotime($arPropProposal[$arPropList["ID"]][$i]["VALUE"]), "FULL") : "");
									} else {
										$propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
										$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : (strlen($arPropList["DEFAULT_VALUE"]) > 0 ? ConvertTimeStamp(strtotime($arPropList["DEFAULT_VALUE"]), "FULL") : "");
									}
									$req = ($arPropList["IS_REQUIRED"] == "Y" ? "required" : "");
									$result .= '<input type="text" name="' . $propName . '" value="' . $propValue . '" size="20" '.$req.' />';
									ob_start();
									$APPLICATION->IncludeComponent(
										'bitrix:main.calendar', '', array(
										'SHOW_INPUT' => 'N',
										'FORM_NAME' => 'proposal_add',
										'INPUT_NAME' => $propName,
										'INPUT_VALUE' => (strlen($propValue) > 0 ? $propValue : date("d.m.Y H:i:s")),
										'SHOW_TIME' => 'N',
										'HIDE_TIMEBAR' => 'N'
									), null, array('HIDE_ICONS' => 'Y')
									);
									$result .= ob_get_clean();
									break;
							}
							$result .= '</span><br />';
						}
						$result .= '</span>';
						$result .= '<input type="hidden" name="PROP_ID_MULTI[' . $arPropList["ID"] . ']" id="id-prop-' . $arPropList["ID"] . '" value="' . $i . '" />';
						if ($arPropList["MULTI"] == "Y" && $arPropList["PROPERTY_TYPE"] != "L") {
							$result .= '<input type="button" value="' . GetMessage("PW_TD_PROP_ADD") . '" onclick="addNewElem(' . $arPropList["ID"] . ', ' . $cntProp . ');" />';
						}
						echo $result;
						?>
					</td>
				</tr>
			<? }
		}
		?>
	</table>

		<?
		//$arPropProposal = $arResult["PROP_PROPOSAL"];
		/*foreach ($arResult["PROP_LIST"] as $arPropList) :?>
			<?if ($arPropList['START_LOT'] == 'Y' && $arPropList['S_RIGHT'] == 'W'):?>

				<div style="margin-top:20px;" class="alert alert-warning" >
					<h4><?= $arPropList["TITLE"] ?>
						<? if ($arPropList["IS_REQUIRED"] == "Y"):?>
						<span style="color:red;" class="required small">* (обязательно к заполнению)</span>
						<? endif; ?>
					</h4>
					<div><?= $arPropList["DESCRIPTION"] ?><br />

					<? $is_file_prop = false; ?>
					<? if ($arPropList["PROPERTY_TYPE"] == "F" && ($rsFiles = CTenderixProposal::GetFileListProperty($arResult["PROPOSAL_ID"], $arPropList["ID"])) && ($arFile = $rsFiles->GetNext())) { ?>
						<? $is_file_prop = true; ?>
						<strong><?= GetMessage("PW_TD_FILE_ATTACH_LIST") ?>:</strong>
						<table>
							<tr>
								<td>
									<table class="t_lot_table">
										<tr>
											<th><? echo GetMessage("PW_TD_FILE_NAME") ?></th>
											<th><? echo GetMessage("PW_TD_FILE_SIZE") ?></th>
											<th><? echo GetMessage("PW_TD_FILE_DELETE") ?></th>
										</tr>
										<?
										do {
											?>
											<tr>
												<td>
													<a href="/bitrix/components/pweb.tenderix/proposal.add/property_file.php?PROPOSAL_ID=<?= $arResult["PROPOSAL_ID"] ?>&amp;FILE_ID=<?= $arFile["ID"] ?>&amp;PROPERTY=<?= $arPropList["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a>
												</td>
												<td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
												<td align="center">
													<input type="checkbox" name="FILE_ID_PROP[<? echo $arFile["ID"] ?>]"
														   value="<? echo $arFile["ID"] ?>">
													<input type="hidden"
														   name="PROP[<?= $arPropList["ID"] ?>][<?= $arFile["ID"] ?>]"/>
													<input type="hidden" name="FILE_PROP" value="<?= $arPropList["ID"] ?>"/>
												</td>
											</tr>
										<? } while ($arFile = $rsFiles->GetNext()); ?>
									</table>
								</td>
							</tr>
						</table>
					<? } ?>
			<?
			$result = "";
			if (strlen($arPropList["DEFAULT_VALUE"]) > 0 && $arPropList["MULTI"] == "Y") {
				$arPropList["MULTI_CNT"]++;
			}
			$cntProp = 0;
			if ($arResult["PROPOSAL_ID"] > 0 && $arPropList["PROPERTY_TYPE"] != "L" && $arPropList["PROPERTY_TYPE"] != "F") {
				$cntProp = count($arPropProposal[$arPropList["ID"]]);
				$arPropList["MULTI_CNT"] += $cntProp;
			}
			if (isset($_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]]) &&
				$_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]] >= $arPropList["MULTI_CNT"] &&
				$arPropList["PROPERTY_TYPE"] != "L" &&
				$arPropList["PROPERTY_TYPE"] != "F"
			) {
				if (strlen($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]] - $cntProp - 1)]) > 0) {
					$arPropList["MULTI_CNT"] = $_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]] + 1;
				} else {
					$arPropList["MULTI_CNT"] = $_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]];
				}
			}
			if ($arPropList["PROPERTY_TYPE"] == "L" || $arPropList["MULTI"] == "N") {
				$arPropList["MULTI_CNT"] = 1;
			}
			$result .= '<br /><span id="prop-' . $arPropList["ID"] . '">';
			for ($i = 0; $i < $arPropList["MULTI_CNT"]; $i++) {
				$result .= '<span id="prop-' . $arPropList["ID"] . '-' . $i . '">';
				switch ($arPropList["PROPERTY_TYPE"]) {
					case "S":
					case "N":
						if ($i > 0 || $arResult["PROPOSAL_ID"] > 0) {
							$arPropList["DEFAULT_VALUE"] = "";
						}
						if ($arResult["PROPOSAL_ID"] > 0 && $i < $cntProp) {
							$propName = "PROP[" . $arPropList["ID"] . "][" . $arPropProposal[$arPropList["ID"]][$i]["ID"] . "]";
							$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"]);
						} else {
							$propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
							$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : htmlspecialcharsEx($arPropList["DEFAULT_VALUE"]);
						}
						if ($arPropList["ROW_COUNT"] <= 1) {
							$result .= '<input name="' . $propName . '" type="text" value="' . $propValue . '" size="' . $arPropList["COL_COUNT"] . '" />';
						} else {
							$result .= '<textarea name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $propValue . '</textarea>';
						}
						break;
					case "F":
						if (!$is_file_prop || $arPropList["MULTI"] == "Y")
							$result .= '<input type="file" name="PROP[' . $arPropList["ID"] . '][n' . ($i - $cntProp) . ']" size="' . $arPropList["COL_COUNT"] . '" />';
						break;
					case "L":
						$arrList = unserialize(base64_decode($arPropList["DEFAULT_VALUE"]));
						if ($arResult["PROPOSAL_ID"] > 0) {
							foreach ($arPropProposal[$arPropList["ID"]] as $arrListSupplier) {
								$arrListValue[] = $arrListSupplier["VALUE"];
							}
						} else {
							$arrListValue[] = $arrList["DEFAULT_VALUE_SELECT"];
						}
						if (isset($_REQUEST["PROP"][$arPropList["ID"]])) {
							unset($arrListValue);
							$arrListValue = $_REQUEST["PROP"][$arPropList["ID"]];
						}
						$result .= '<select name="PROP[' . $arPropList["ID"] . '][]"' . ($arPropList["MULTI"] == "Y" ? " multiple" : "") . ' size="' . $arPropList["ROW_COUNT"] . '">';
						foreach ($arrList["DEFAULT_VALUE"] as $idRow => $listVal) {
							$result .= '<option' . (in_array($idRow, $arrListValue) ? " selected" : "") . ' value="' . $idRow . '">' . $listVal . '</option>';
						}
						$result .= '</select>';
						break;
					case "T":
						if ($i > 0 || $arResult["PROPOSAL_ID"] > 0) {
							$arPropList["DEFAULT_VALUE"] = "";
						}
						if ($arResult["PROPOSAL_ID"] > 0 && $i < $cntProp) {
							$propName = "PROP[" . $arPropList["ID"] . "][" . $arPropProposal[$arPropList["ID"]][$i]["ID"] . "]";
							$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"]);
						} else {
							$propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
							$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : htmlspecialcharsEx($arPropList["DEFAULT_VALUE"]);
						}
						$result .= '<textarea name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $propValue . '</textarea>';
						break;
					case "D":
						if ($i > 0 || $arResult["PROPOSAL_ID"] > 0) {
							$arPropList["DEFAULT_VALUE"] = "";
						}
						if ($arResult["PROPOSAL_ID"] > 0 && $i < $cntProp) {
							$propName = "PROP[" . $arPropList["ID"] . "][" . $arPropProposal[$arPropList["ID"]][$i]["ID"] . "]";
							$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) : (strlen($arPropProposal[$arPropList["ID"]][$i]["VALUE"]) > 0 ? ConvertTimeStamp(strtotime($arPropProposal[$arPropList["ID"]][$i]["VALUE"]), "FULL") : "");
						} else {
							$propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
							$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : (strlen($arPropList["DEFAULT_VALUE"]) > 0 ? ConvertTimeStamp(strtotime($arPropList["DEFAULT_VALUE"]), "FULL") : "");
						}
						$result .= '<input type="text" name="' . $propName . '" value="' . $propValue . '" size="20" />';
						ob_start();
						$APPLICATION->IncludeComponent(
							'bitrix:main.calendar', '', array(
								'SHOW_INPUT' => 'N',
								'FORM_NAME' => 'proposal_add',
								'INPUT_NAME' => $propName,
								'INPUT_VALUE' => (strlen($propValue) > 0 ? $propValue : date("d.m.Y H:i:s")),
								'SHOW_TIME' => 'N',
								'HIDE_TIMEBAR' => 'N'
							), null, array('HIDE_ICONS' => 'Y')
						);
						$result .= ob_get_clean();
						break;
				}
				$result .= '</span><br />';
			}
			$result .= '</span>';
			$result .= '<input type="hidden" name="PROP_ID_MULTI[' . $arPropList["ID"] . ']" id="id-prop-' . $arPropList["ID"] . '" value="' . $i . '" />';
			if ($arPropList["MULTI"] == "Y" && $arPropList["PROPERTY_TYPE"] != "L") {
				$result .= '<input type="button" value="' . GetMessage("PW_TD_PROP_ADD") . '" onclick="addNewElem(' . $arPropList["ID"] . ', ' . $cntProp . ');" />';
			}
			echo $result;
			?>
		</div> 	</div>

		<?endif;?>
		<?endforeach;*/?>
		<?
		/****************
		 * PROPERTY
		 ****************/
		?>

	<? if ($arResult["TIME"] == ""): ?>
		<div style="margin-top:20px;position:relative;" class="alert alert-warning message-dopprop">
			<h4>В поле «Сообщение» запрещено</h4>
			<a class="button-hide" data-toggle="collapse" href="#collapseMassage" aria-expanded="false" aria-controls="collapseMassage">
				<i class="fa fa-arrow-up"></i><i class="fa fa-arrow-down" style="display:none;"></i>
			</a>
			<div class="collapse in" id="collapseMassage">
				<p>указывать какую-либо информацию о финансовых аспектах предложения (цена, условия финансирования, скидки и пр.).
				</p>
				<p style="color:red;">В случае Вашего нежелания принимать участие в данном Лоте необходимо указать причину отказа в поле "Сообщение".</p>
			</div>
		</div>
		<b><?= GetMessage("PW_TD_MESSAGE") ?>: </b><br>
		<textarea class="form-control" name="MESSAGE" rows="5"><?= isset($_REQUEST["proposal_submit"]) ? htmlspecialcharsEx($_REQUEST["MESSAGE"]) : $arResult["MESSAGE"] ?></textarea>
	<? endif; ?>

		<div>
		<? if (count($arResult["INFO"]["FILE"]) > 0): ?>
			<br/><?= GetMessage("PW_TD_FILE_ATTACH_LIST") ?>:
			<table>
				<tr>
					<td>
						<table class="t_lot_table">
							<tr>
								<th><? echo GetMessage("PW_TD_FILE_NAME") ?></th>
								<th><? echo GetMessage("PW_TD_FILE_SIZE") ?></th>
								<th><? echo GetMessage("PW_TD_FILE_DELETE") ?></th>
							</tr>
							<? foreach ($arResult["INFO"]["FILE"] as $arFile) : ?>
								<tr>
									<td>
										<a href="/tx_files/proposal_file.php?PROPOSAL_ID=<?= $arResult["PROPOSAL_ID"] ?>&FILE_ID=<?= $arFile["ID"] ?>"><?= $arFile["ORIGINAL_NAME"] ?></a>
									</td>
									<td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
									<td align="center">
										<input type="checkbox" name="FILE_ID[<? echo $arFile["ID"] ?>]"
											   value="<? echo $arFile["ID"] ?>">
									</td>
								</tr>
							<? endforeach; ?>
						</table>
					</td>
				</tr>
			</table>
		<? endif; ?>

			<div style="margin-top:20px;" class="alert alert-warning">
				<h4>Загрузка документов</h4>
				<p>Перед подачей предложения необходимо прикрепить все запрашиваемые в соответствии с условиями Лота документы. <br>
					Прикрепляемые копии должны быть хорошего качества, содержать подпись уполномоченного лица и печать организации. <br>
					В случае отсутствия полного комплекта документов предложение может быть признано несоответствующим требованиям лота.
				</p>
			</div>
		<b><?= GetMessage("PW_TD_DOCUMENT") ?>:</b><br/>
		<? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br/>
		<? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br/>
		<? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br/>
		<? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br/>
		<? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br/>

		</div>

		<div class="t_right">
			<? if (isset($arResult["PROPOSAL_ID"]) && $arResult["PROPOSAL_ID"] > 0): ?>
				<input type="hidden" name="PROPOSAL_ID" value="<?= $arResult["PROPOSAL_ID"] ?>"/>
			<? endif; ?>
			<?  /*if(!empty($arResult["LOT"]["ACCESS"])) {
					if($arResult["LOT"]["ACCESS"] == "Y") {*/ ?>
						<? if ($arResult["LOT"]["TYPE_ID"] == "R" || $arResult["LOT"]["TYPE_ID"] == "T"): ?>
							<input class="btn btn-primary" type="submit" name="proposal_submit" value="Сделать предложение"/><br/><br/>
						<?else:?>
							<input class="btn btn-primary" type="submit" name="proposal_submit" value="<?= GetMessage("PROPOSAL_FORM_SAVE") ?>"/><br/><br/>
						<?endif;?>
					<? /*} else { ?>
							<input class="btn btn-primary proposal_request" type="button" name="proposal_request_yet" value="<?= GetMessage("PW_TD_REQUEST_YET") ?>" disabled /><br/><br/>
					<? }
					} else {?>
						<input class="btn btn-primary proposal_request" type="button" name="proposal_request" value="<?= GetMessage("PW_TD_REQUEST") ?>"/><br/><br/>
					<? }*/ ?>
		</div>
	<? endif; ?>
</form>

	<br clear="all"/>

<? if ($arResult["TIME"] == ""): ?>
	<script type="text/javascript">
	function stepUp(id, step, num, price) {
		var price_nds = $("input[name='PROP_" + id + "_PRICE_NDS']").val();
		var curr_name = $("[name='CURRENCY_PROPOSAL']").val();
		var curr_val = parseFloat($("[name='CURR[" + curr_name + "]']").val());

		if (($("#zero_" + num).attr("checked") == 'checked')) {

		} else {
			$("#zero_" + num).attr("checked", true);
		}

		if (price_nds == 0) {
			price_nds = price;
		}

		price_nds = parseFloat(price_nds) + (parseFloat(step) / curr_val);

		//$("input[name='PROP_" + id + "_PRICE_NDS']").val(price_nds.toFixed(2));
		<? if($arResult["LOT"]["TYPE_ID"] == "N") { ?>
			if(price_nds > price) {
				$("input[name='PROP_" + id + "_PRICE_NDS_dis']").css("border", "1px solid red");
				$("#PROP_"+ id +"_err_span").html("нельзя устанавливать<br /> цену больше начальной");
				$("input[name='PROP_" + id + "_PRICE_NDS']").val(price.toFixed(2));
				$("input[name='PROP_" + id + "_PRICE_NDS_dis']").val(price.toFixed(2));
			}
			else {
				$("input[name='PROP_" + id + "_PRICE_NDS_dis']").css("border", "");
				$("#PROP_"+ id +"_err_span").html("");
				$("input[name='PROP_" + id + "_PRICE_NDS']").val(price_nds.toFixed(2));
				$("input[name='PROP_" + id + "_PRICE_NDS_dis']").val(price_nds.toFixed(2));
			}
		<? } elseif($arResult["LOT"]["TYPE_ID"] == "P") { ?>
			if(price_nds >= price) {
				$("input[name='PROP_" + id + "_PRICE_NDS_dis']").css("border", "");
				$("#PROP_"+ id +"_err_span").html("");
				$("input[name='PROP_" + id + "_PRICE_NDS']").val(price_nds.toFixed(2));
				$("input[name='PROP_" + id + "_PRICE_NDS_dis']").val(price_nds.toFixed(2));

			} else {
				$("input[name='PROP_" + id + "_PRICE_NDS_dis']").css("border", "1px solid red");
				$("#PROP_"+ id +"_err_span").html("нельзя устанавливать<br /> цену меньше начальной");
				$("input[name='PROP_" + id + "_PRICE_NDS']").val(price.toFixed(2));
				$("input[name='PROP_" + id + "_PRICE_NDS_dis']").val(price.toFixed(2));
			}
		<? } ?>
		//$("input[name='PROP_" + id + "_PRICE_NDS_dis']").val(price_nds.toFixed(2));

		//if($("#zero_"+id).attr("checked") == 'checked') {
		$("#zero_" + num).parent('a').parent('td').parent('tr').removeClass('gr_style');
		//$("input[name='PROP_"+id+"_PRICE_NDS']").val(cur_price.toFixed(2));
		//$("input[name='PROP_"+id+"_PRICE_NDS_dis']").val(cur_price.toFixed(2));

		return false;
	}
	function stepDown(id, step, num, price) {
		var price_nds = $("input[name='PROP_" + id + "_PRICE_NDS']").val();
		var curr_name = $("[name='CURRENCY_PROPOSAL']").val();
		var curr_val = parseFloat($("[name='CURR[" + curr_name + "]']").val());

		if (($("#zero_" + num).attr("checked") == 'checked')) {

		} else {
			$("#zero_" + num).attr("checked", true);
		}

		if (price_nds == 0) {
			price_nds = price;
		}

		price_nds = parseFloat(price_nds) - (parseFloat(step) / curr_val);
		//$("input[name='PROP_" + id + "_PRICE_NDS']").val(price_nds.toFixed(2));
		<? if($arResult["LOT"]["TYPE_ID"] == "P") { ?>
		if(price_nds < price) {
			$("input[name='PROP_" + id + "_PRICE_NDS_dis']").css("border", "1px solid red");
			$("#PROP_"+ id +"_err_span").html("нельзя устанавливать<br /> цену меньше начальной");
			$("input[name='PROP_" + id + "_PRICE_NDS']").val(price.toFixed(2));
			$("input[name='PROP_" + id + "_PRICE_NDS_dis']").val(price.toFixed(2));
		}
		else {
			$("input[name='PROP_" + id + "_PRICE_NDS_dis']").css("border", "");
			$("#PROP_"+ id +"_err_span").html("");
			$("input[name='PROP_" + id + "_PRICE_NDS']").val(price_nds.toFixed(2));
			$("input[name='PROP_" + id + "_PRICE_NDS_dis']").val(price_nds.toFixed(2));
		}
		<? } elseif($arResult["LOT"]["TYPE_ID"] == "N") { ?>
		if(price_nds <= price) {
			$("input[name='PROP_" + id + "_PRICE_NDS_dis']").css("border", "");
			$("#PROP_"+ id +"_err_span").html("");
			$("input[name='PROP_" + id + "_PRICE_NDS']").val(price_nds.toFixed(2));
			$("input[name='PROP_" + id + "_PRICE_NDS_dis']").val(price_nds.toFixed(2));
		} else {
			$("input[name='PROP_" + id + "_PRICE_NDS_dis']").css("border", "1px solid red");
			$("#PROP_"+ id +"_err_span").html("нельзя устанавливать<br /> цену больше начальной");
			$("input[name='PROP_" + id + "_PRICE_NDS']").val(price.toFixed(2));
			$("input[name='PROP_" + id + "_PRICE_NDS_dis']").val(price.toFixed(2));
		}

		<? } ?>
		//$("input[name='PROP_" + id + "_PRICE_NDS_dis']").val(price_nds.toFixed(2));

		$("#zero_" + num).parent('a').parent('td').parent('tr').removeClass('gr_style');

		return false;
	}

	function stepUpS(step, id) {
		var price_nds = $("#item_proposal_price_full" + id).val();
		var curr_name = $("[name='CURRENCY_PROPOSAL']").val();
		var curr_val = parseFloat($("[name='CURR[" + curr_name + "]']").val());

		price_nds = parseFloat(price_nds) + (parseFloat(step) / curr_val);
		$("#item_proposal_price_full" + id).val(price_nds);
		$("input[name='PRICE_NDS[" + id + "]']").val(price_nds.toFixed(2));
		return false;
	}
	function stepDownS(step, id) {
		var price_nds = $("#item_proposal_price_full" + id).val();
		var curr_name = $("[name='CURRENCY_PROPOSAL']").val();
		var curr_val = parseFloat($("[name='CURR[" + curr_name + "]']").val());

		price_nds = parseFloat(price_nds) - (parseFloat(step) / curr_val);
		$("#item_proposal_price_full" + id).val(price_nds);
		$("input[name='PRICE_NDS[" + id + "]']").val(price_nds.toFixed(2));
		return false;
	}

	function dateWrite(CountSec) {
		var days = " <?= GetMessage("DAYS") ?> ";
		var CountFullDays = (parseInt(CountSec / (24 * 60 * 60)));
		if (
			CountFullDays == 2 ||
				CountFullDays == 3 ||
				CountFullDays == 4 ||
				CountFullDays == 22 ||
				CountFullDays == 23 ||
				CountFullDays == 24 ||
				CountFullDays == 32 ||
				CountFullDays == 33 ||
				CountFullDays == 34
			) {
			days = " <?= GetMessage("DAYS2") ?> "
		}
		if (
			CountFullDays == 1 ||
				CountFullDays == 21 ||
				CountFullDays == 31
			) {
			days = " <?= GetMessage("DAYS3") ?> "
		}
		var secInLastDay = CountSec - CountFullDays * 24 * 3600;
		var CountFullHours = (parseInt(secInLastDay / 3600));
		var hours = " <?= GetMessage("HOURS") ?> ";
		if (
			CountFullHours == 2 ||
				CountFullHours == 3 ||
				CountFullHours == 4 ||
				CountFullHours == 22 ||
				CountFullHours == 23 
			) {
				hours = " <?= GetMessage("HOURS2") ?> "
		}
		if (
			CountFullHours == 1 ||
				CountFullHours == 21 
			) {
			hours = " <?= GetMessage("HOURS3") ?> "
		}
		
		/* if (CountFullHours < 10) {
			CountFullHours = "0" + CountFullHours
		}
		; */
		var secInLastHour = secInLastDay - CountFullHours * 3600;
		//
		var CountMinutes = (parseInt(secInLastHour / 60));
		/* if (CountMinutes < 10) {
			CountMinutes = "0" + CountMinutes
		}
		; */
		
		
		var minutes = " <?= GetMessage("MINUTE") ?> ";
		if (
			CountMinutes == 2 ||
				CountMinutes == 3 ||
				CountMinutes == 4 ||
				CountMinutes == 22 ||
				CountMinutes == 23 ||
				CountMinutes == 24 ||
				CountMinutes == 32 ||
				CountMinutes == 33 ||
				CountMinutes == 34 ||
				CountMinutes == 42 ||
				CountMinutes == 43 ||
				CountMinutes == 44 ||
				CountMinutes == 52 ||
				CountMinutes == 53 ||
				CountMinutes == 54
				
			) {
				minutes = " <?= GetMessage("MINUTE2") ?> "
		}
		if (
			CountMinutes == 1 ||
				CountMinutes == 21 ||
				CountMinutes == 31 ||
				CountMinutes == 41 ||
				CountMinutes == 51
				
			) {
			minutes = " <?= GetMessage("MINUTE3") ?> "
		}
		
		var lastSec = secInLastHour - CountMinutes * 60;
		/* if (lastSec < 10) {
			lastSec = "0" + lastSec
		}
		; */
		
		var sec = " <?= GetMessage("SECOND") ?> ";
		if (
			lastSec == 2 ||
				lastSec == 3 ||
				lastSec == 4 ||
				lastSec == 22 ||
				lastSec == 23 ||
				lastSec == 24 ||
				lastSec == 32 ||
				lastSec == 33 ||
				lastSec == 34 ||
				lastSec == 42 ||
				lastSec == 43 ||
				lastSec == 44 ||
				lastSec == 52 ||
				lastSec == 53 ||
				lastSec == 54 
			) {
				sec = " <?= GetMessage("SECOND2") ?> "
		}
		if (
			lastSec == 1 ||
				lastSec == 21 ||
				lastSec == 31 ||
				lastSec == 41 ||
				lastSec == 51
				
			) {
			sec = " <?= GetMessage("SECOND3") ?> "
		}
		
		var tdays = CountFullDays > 0 ? CountFullDays + days : '';
		var thours = CountFullHours > 0 ? CountFullHours + hours : '';
		var tminutes = CountMinutes > 0 ? CountMinutes + minutes : '';
		var tsec = lastSec > 0 ? lastSec + sec : '';

		return tdays + thours + tminutes + tsec;
	}

	var update_flag = true;
	var count_refresh = 60;
	var count_time = 1;

	function updateLot() {
		var lot_id = $("#lot_id").val();
		var curr = $("[name='CURR_USER']").val();
		//alert(curr);
		var time_diff = $("#time_diff").val();

		$("#time").html(dateWrite(time_diff));

		if (update_flag) {
			update_flag = false;
			$.ajax({
				type: "POST",
				url: "/bitrix/components/pweb.tenderix/proposal.add/update.php",
				data: "LOT_ID=" + lot_id + "&CURR=" + curr,
				beforeSend: function () {
					$("#time2").html("<?= GetMessage("START_UPDATE") ?>");
					$(".best_proposal_n").html("<?= GetMessage("START_UPDATE") ?>");
					$("#best_proposal").html("<?= GetMessage("START_UPDATE") ?>");
				},
				success: function (data) {
					var json = eval("(" + data + ")");
					if (json.time_diff <= 0) {
						window.location.href = window.location.href;
					} else {
						count_time = 0;
						$("#time_diff").val(json.time_diff);
						$("#time2").html("");
						$(".best_proposal_n").html("-");
						$("#best_proposal").html("-");

						if (json.type != "S") {
							var proposal_min = eval("(" + json.proposal_min + ")");
							for (var key in proposal_min) {
								$("#best_proposal_" + key).html(proposal_min[key].toFixed(2));
								$("#best_proposal_full_" + key).val(proposal_min[key]);
								$("#best_proposal2_" + key).html(proposal_min[key].toFixed(2));
								$("#best_proposal2_full_" + key).val(proposal_min[key]);
								if (proposal_min[key] > 0) {
									$("#block_best").show();
								}
							}
						}
						if (json.type == "S") {
							var proposal_min = eval("(" + json.proposal_min + ")");
							for (var key in proposal_min) {
								$("#best_proposal" + key).html(proposal_min[key].toFixed(2));
								$("#best_proposal2_"+key).html(proposal_min[key].toFixed(2));
								$("#best_proposal_full" + key).val(proposal_min[key]);
							}
						}
					}
					if (json.date_st > 0) {
						$(".best_proposal_n").html("--");
						$(".best_proposal2_n").html("--");
						$("#best_proposal").html("--");
						$("#best_proposal2").html("--");
					}
				}
			});
		}
		if (time_diff > 0) {
			time_diff--;
			count_time++;
			$("#time_diff").val(time_diff);
		}
		if (count_time == count_refresh || time_diff <= 0) {
			update_flag = true;
		}

		setTimeout("updateLot()", 1000);
	}
//!!!
	$(function () {
		updateLot();
		$("[name='CURRENCY_PROPOSAL']").change(function () {
			var curr_name = $(this).val();
			var curr_val = parseFloat($("[name='CURR[" + curr_name + "]']").val());
			var curr_name_user = $("[name='CURR_USER']").val();
			//alert(curr_name_user);
			var curr_val_user = parseFloat($("[name='CURR[" + curr_name_user + "]']").val());
			$("[name='CURR_USER']").val(curr_name);

			//if($(".best_proposal_n").length) {
			$(".id_item_proposal").each(function () {
				var id = $(this).val();
				
				/* Лучшая цена*/ 
				var best_proposal_full = $("#best_proposal_full_" + id).val().replace(/\s/g, '');
				best_proposal_full = Number(best_proposal_full) ? parseFloat(best_proposal_full) : 0;
				if (best_proposal_full > 0) {
					var num = (best_proposal_full * curr_val_user / curr_val);
					var gnum = num.toFixed(2).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 '); 
					$("#best_proposal_"+id).text(gnum);
				}
				$("#best_proposal_full_" + id).val(best_proposal_full * curr_val_user / curr_val);
				/* --- Лучшая цена*/ 
				
				/* Стартовая цена*/ 
				var start_price_full = $("#start_price_full_" + id).val().replace(/\s/g, '');
				start_price_full = Number(start_price_full) ? parseFloat(start_price_full) : 0;
				if (start_price_full > 0) {
					var num = (start_price_full * curr_val_user / curr_val);
					var gnum = num.toFixed(2).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 '); 
					$("#start_price_"+id).text(gnum);
				}
				$("#start_price_full_" + id).val(start_price_full * curr_val_user / curr_val);
				/* --- Стартовая цена*/ 
				
				/* Моя цена*/ 
				if ($("#my_price_full_" + id).length > 0) {
					var my_price_full = $("#my_price_full_" + id).val().replace(/\s/g, '');
					my_price_full = Number(my_price_full) ? parseFloat(my_price_full) : 0;
					if (my_price_full > 0) {
						var num = (my_price_full * curr_val_user / curr_val);
						var gnum = num.toFixed(2).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 '); 
						$("#my_price_"+id).text(gnum);
					}
					$("#my_price_full_" + id).val(my_price_full * curr_val_user / curr_val);
					/* --- Моя цена*/ 
				}
				
				
				/* Минимальная цена*/ 
				var min_price_full = $("#min_price_full_" + id).val().replace(/\s/g, '');
				total_min_price_full = Number(min_price_full) ? parseFloat(min_price_full) : 0;
				if (min_price_full > 0) {
					var num = (min_price_full * curr_val_user / curr_val);
					var gnum = num.toFixed(2).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 '); 
					$("#min_price_"+id).text(gnum);
				}
				$("#min_price_full_" + id).val(min_price_full * curr_val_user / curr_val);
				/* --- Минимальная цена*/ 
				
				/* Цена с НДС */
				var price_nds_full = parseFloat($("#price_nds_full_" + id).val()) > 0 ? parseFloat($("#price_nds_full_" + id).val()) : $("[name=PROP_" + id + "_PRICE_NDS]").val();
				var num = (price_nds_full * curr_val_user / curr_val);
				var gnum = num.toFixed(2).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 '); 
				$("[name=PROP_" + id + "_PRICE_NDS]").val(gnum);
				$("[name=PROP_" + id + "_PRICE_NDS_dis]").val(gnum);
				$("#price_nds_full_" + id).val(num);
				/* --- Цена с НДС */

			});
			
			/* Минимальная сумма лота*/ 
			var total_min_price_full = $("#total_min_price_full").val().replace(/\s/g, '');
			total_min_price_full = Number(total_min_price_full) ? parseFloat(total_min_price_full) : 0;
			if (total_min_price_full > 0) {
				var num = (total_min_price_full * curr_val_user / curr_val);
				var gnum = num.toFixed(2).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 '); 
				$("#total_min_price").text(gnum);
			}
			$("#total_min_price_full").val(total_min_price_full * curr_val_user / curr_val);
			/* --- Минимальная сумма лота*/ 
			
			/* Стартовая сумма лота*/ 
			var total_start_price_full = $("#total_start_price_full").val().replace(/\s/g, '');
			total_start_price_full = Number(total_start_price_full) ? parseFloat(total_start_price_full) : 0;
			if (total_start_price_full > 0) {
				var num = (total_start_price_full * curr_val_user / curr_val);
				var gnum = num.toFixed(2).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 '); 
				$("#total_start_price").text(gnum);
			}
			$("#total_start_price_full").val(total_start_price_full * curr_val_user / curr_val);
			/* --- Стартовая сумма лота*/ 
			
			//Стартовую и минимальную цену изменяем
			// alert('1');
			
			/* $(".min_price").each(function() {
				var val = $(this).html().replace(/\s/g, '');				
				val = (val * curr_val_user / curr_val);
				val = val.toFixed(2).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 '); 
				$(this).html(val);
			});
			$(".start_price").each(function() {
				var val = $(this).html().replace(/\s/g, '');
				val = (val * curr_val_user / curr_val);
				val = val.toFixed(2).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 '); 
				$(this).html(val);
			}); */
			
			//}
			//if($("#best_proposal").length) {
			var best_proposal_full = Number($("#best_proposal_full").val()) ? parseFloat($("#best_proposal_full").val()) : 0;
			if (best_proposal_full > 0) {
				$("#best_proposal").text(((best_proposal_full * curr_val_user) / curr_val).toFixed(2));
			}
			$("#best_proposal_full").val((best_proposal_full * curr_val_user) / curr_val);
			
			$("#item_proposal_price").val(((parseFloat($("#item_proposal_price_full").val()) * curr_val_user) / curr_val).toFixed(2));
			$("#item_proposal_price_full").val((parseFloat($("#item_proposal_price_full").val()) * curr_val_user) / curr_val);
			//}

		});
	});

	function step(id, price, specid, startpr) {
		var cur_price = price;
		//$("input[name='PROP_"+id+"_PRICE_NDS_dis']").val("0");
		if (($("#zero_" + id).attr("checked") == 'checked') && ($("input[name='PROP_" + specid + "_PRICE_NDS']").val() > 0)) {
			$("#zero_" + id).removeAttr("checked");
		}

		if ($("#zero_" + id).attr("checked") == 'checked') {
			$("#zero_" + id).parent('a').parent('td').parent('tr').removeClass('gr_style');
			if (price == 0) {
				cur_price = startpr;
			}
			$("input[name='PROP_" + specid + "_PRICE_NDS']").val(cur_price.toFixed(2));
			$("input[name='PROP_" + specid + "_PRICE_NDS_dis']").val(cur_price.toFixed(2));
			//alert("вы установили флажок");

		} else {
			cur_price = 0;
			$("#zero_" + id).parent('a').parent('td').parent('tr').addClass('gr_style');
			$("input[name='PROP_" + specid + "_PRICE_NDS']").val(cur_price.toFixed(2));
			$("input[name='PROP_" + specid + "_PRICE_NDS_dis']").val(cur_price.toFixed(2));
			//alert("вы сняли флажок");
		}
	}

	function step2(id) {

		$("#zero_" + id).parent('a').parent('td').parent('tr').removeClass('gr_style');
		//if (price == 0) {cur_price = startpr;}
		 //$("input[name='PROP_"+specid+"_PRICE_NDS']").val(cur_price.toFixed(2));
		 //$("input[name='PROP_"+specid+"_PRICE_NDS_dis']").val(cur_price.toFixed(2));
		//alert("вы установили флажок");

	}

	//document.getElementByID('my_checkbox').checked = true;

	</script>
<? endif; ?>

	<br/>
	<br/>
<?
$rsLot = CTenderixLot::GetByIDa($_REQUEST["LOT_ID"]);
if ($arLot = $rsLot->GetNext()) {
	$type_lot = $arLot["TYPE_ID"];
}

$date_end = strtotime($arResult["LOT"]["DATE_END"]);
$date_tek = time();
//echo $date_end . " " . $date_end . " " . $arResult[LOT][VIZ_HIST];
if ((($date_tek > $date_end) && ($date_end != 0) && ($arResult[LOT][VIZ_HIST] == "Y")) || (($date_tek < $date_end) && ($date_end != 0))) {

	$arFilter = array(
		"LOT_ID" => $_REQUEST["LOT_ID"],
		"USER_ID" => $USER->GetID()
	);
	$rsProposal = CTenderixProposal::GetList($arFilter);
	$logi = array();
	$title = array();
	if ($type_lot != "S" && $type_lot != "R") {
		global $DB;
		$spec_lot = array();
		while ($arProposal = $rsProposal->GetNext()) {
			//подробности спецификации
			$prid = $arProposal["ID"];
			$rsProposalSpec = CTenderixProposal::GetListSpec(array("PROPOSAL_ID" => $arProposal["ID"]));
			while ($arProposalSpec = $rsProposalSpec->GetNext()) {
				$spec_lot[$arProposalSpec["PROPERTY_BUYER_ID"]] = $arProposalSpec;
				$title[$arProposalSpec["PROPERTY_BUYER_ID"]] = $arProposalSpec["TITLE"];
			}
			//подробности спецификации - конец

			//логи
			$strSql = "SELECT * FROM b_tx_proposal_spec_h WHERE PROPOSAL_ID=" . $arProposal["ID"] . " ORDER BY DATE_START DESC";
			$reslist = $DB->Query($strSql);
			while ($razbl = $reslist->fetch()) {
				$logi[$razbl["DATE_START"]][$razbl["PROPERTY_BUYER_ID"]]["ID"] = $razbl["ID"];
				$logi[$razbl["DATE_START"]][$razbl["PROPERTY_BUYER_ID"]]["ANALOG"] = $razbl["ANALOG"];
				$logi[$razbl["DATE_START"]][$razbl["PROPERTY_BUYER_ID"]]["NDS"] = $razbl["NDS"];
				$logi[$razbl["DATE_START"]][$razbl["PROPERTY_BUYER_ID"]]["PRICE_NDS"] = $razbl["PRICE_NDS"];
			}
			//логи- конец
			//__($logi);
			?>
			<h3>Ставки:</h3>
			<table class="table t_lot_table">
				<tr>
					<th>Дата</th>
					<th>Время</th>
					<th>Товар</th>
					<th>НДС</th>
					<th>Мое предложение<br/><!--(Цена за ед. с НДС)--></th>
					<? if ($arResult["LOT"]["OPEN_PRICE"] == "Y"): ?>
						<th>Лучшее предложение<br/>(Цена за ед. с НДС)</th>
					<? endif; ?>
				</tr>
				<? $ff = 0;
				$curpr = array(); ?>
				<? //__($arResult); ?>
				<? if($arResult["CURRENCY"][$arResult["LOT"]["CURRENCY"]]["RATE_NUM"] != '') {
					$rate = $arResult["CURRENCY"][$arResult["LOT"]["CURRENCY"]]["RATE_NUM"];
				} else {
					$rate = 1;
				} ?>
				<? foreach ($logi as $ldata => $lspec) { ?>
					<?$ss = 0;
					foreach ($lspec as $bid => $pspec) {
						$curpr[$ff][$bid] = $pspec["PRICE_NDS"];
						if ($curpr[$ff][$bid] != $curpr[$ff - 1][$bid]) {
							if ($ss==0) {
							?>
							<!--tr>
									<td rowspan="2"><?=$ldata; ?></td-->
							<?
							} else {
							?>
							<tr>
								<? } ?>
								<td><?= date('d.m.Y', strtotime($ldata)); ?></td>
								<td><?= date('H:i:s', strtotime($ldata)); ?></td>
								<td><?//__($title); ?><?= $title[$bid]; ?></td>
								<td align="center"><?=$pspec["NDS"];?></td>
								<td align="center"><?= number_format(($pspec["PRICE_NDS"] / $rate), 2, '.', ' '); ?></td>
								<? if ($arResult["LOT"]["OPEN_PRICE"] == "N") { ?>
									<td align="center" class="best_proposal_n" id="best_proposal_<?=$specProp["ID"];?>"></td>
									<input type="hidden" id="best_proposal_full_<?= $specProp["ID"] ?>" value="" />
								<? } else { ?>
									<td align="center" class="best_proposal2_n" id="best_proposal2_<?= $bid; ?>"></td>
									<input type="hidden" id="best_proposal2_full_<?= $specProp["ID"] ?>" value="" />
								<? } ?>
							</tr>
							<? $ss++;
						}
					} ?>
					<?
					$ff++;
				} ?>
			</table>
		<?
		}
	} else {
		global $DB;
		$logi = array();
		while ($arProposal = $rsProposal->GetNext()) {
			//подробности спецификации
			$prid = $arProposal["ID"];
		}
		$rsProduct = CTenderixProposal::GetListProducts(array("PROPOSAL_ID" => $prid));
		$arProduct = $rsProduct->Fetch();
		//echo "<pre>"; print_r($arProduct); echo "</pre>";

		//логи
		//echo $prid;
		if ($prid) {
			$strSql = "SELECT * FROM b_tx_proposal_prod_h WHERE PROPOSAL_ID=" . $prid . " ORDER BY DATE_START DESC";
			//$strSql = "SELECT * FROM b_tx_proposal_prod_h WHERE PROPOSAL_ID=".$prid;
			$reslist = $DB->Query($strSql);
			$arProdBuyer = array();
			while ($razbl = $reslist->fetch()) {
				$logi[$razbl["DATE_START"]][$razbl["PROD_BUYER_ID"]]["ID"] = $razbl["ID"];
				$logi[$razbl["DATE_START"]][$razbl["PROD_BUYER_ID"]]["NDS"] = $razbl["NDS"];
				$logi[$razbl["DATE_START"]][$razbl["PROD_BUYER_ID"]]["PRICE_NDS"] = $razbl["PRICE_NDS"];
				$arProdBuyer[$razbl["PROD_BUYER_ID"]] = $razbl["PROD_BUYER_ID"];
			}

			$arProd = array();
			foreach ($arProdBuyer as $prodBuyerId) {
				$rsB = CTenderixProducts::GetListBuyer(array("ID" => $prodBuyerId));
				$arB = $rsB->Fetch();
				$rsP = CTenderixProducts::GetListProducts(array(), array("ID" => $arB["PRODUCTS_ID"]));
				$arProd[$prodBuyerId] = $rsP->Fetch();
			}
			//логи- конец
			?>
			<h3>Ставки:</h3>
			<table class="table t_lot_table">
				<tr>
					<th>Товар</th>
					<th>Дата</th>
					<th>Время</th>
					<th>НДС</th>
					<th>Мое предложение<br/><!--(Цена за ед. с НДС)--></th>
					<? if ($arResult["LOT"]["OPEN_PRICE"] == "Y"): ?>
						<th>Лучшее предложение<br/>(Цена за ед. с НДС)</th>
					<? endif; ?>
				</tr>
				<? $ff = 0;
				$curpr = array(); ?>
				<?//__($logi);?>
				<? foreach ($logi as $ldata => $lsp) {
					foreach ($lsp as $idBuyerProd => $lspec) {
						$curpr[$ff] = $lspec["PRICE_NDS"];
						if ($curpr[$ff] != $curpr[$ff - 1]) {
							?>
							<tr>
								<td><?= $arProd[$idBuyerProd]["TITLE"] ?></td>
								<td><?= date('d.m.Y', strtotime($ldata)); ?></td>
								<td><?= date('H:i:s', strtotime($ldata)); ?></td>
								<td align="center"><?=$lspec["NDS"];?></td>
								<td align="center"><?= $lspec["PRICE_NDS"]; ?></td>
								<? if ($arResult["LOT"]["OPEN_PRICE"] == "Y"): ?>
									<td align="center">
										<span id="best_proposal2_<?=$idBuyerProd?>"></span>
									</td>
								<? endif; ?>
							</tr>
						<?
						}
						$ff++;
					}
				} ?>
			</table>
		<?
		}
	}
}
?>

<script>
	$('.proposal_request').click(function() {
		lot_id = <?=$arResult["LOT"]["ID"]; ?>;

		user_id = <?= $USER -> GetID(); ?>;
		$.ajax({
			url: "<?= $templateFolder ?>/ajax.php",
			type: "POST",
			data: "lotId=" + lot_id + "&userId=" + user_id,
			success: function (data) {
				if(data == 1) {
					$('.proposal_request').val('Доступ к лоту запрошен');
					$('.proposal_request').prop( "disabled", true );
				} else {
					UI.message({
						text: data,
						timer: 6000,
						veil: true
					});
					$('.proposal_request').val('Доступ к лоту запрошен');
					$('.proposal_request').prop( "disabled", true );
				}
			}
		});
	});
</script>
