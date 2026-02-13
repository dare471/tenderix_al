<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

//Цены поставщика
	// print_r('xfgdsgdfgdfssssssssssssssssssssssssssssg');
?>



<?if($arResult['RIGHT'] == "W" || $arResult['RIGHT'] == "P"):?>
	<?if($arResult['LOT']['PRIVATE'] == 'Y'):?>
		<?if($arResult['PRIVATE_USER']):?>
			<h4>Список приглашенных поставщиков:</h4>
			<ol>
			<?php foreach ($arResult['PRIVATE_USER'] as $key => $value): ?>
			<li>
			<?php echo $value['NAME_COMPANY'];?> - ИНН - <?php echo $value['CODE_INN'];?> (email: <a href="mailto:<?php echo $value['EMAIL'];?>"><?php echo $value['EMAIL'];?>)</a>
			<?if(array_key_exists($value['USER_ID'], $arResult['PROPOSAL_USER'])){echo '<i class="fa fa-check" style="color:green;"></i>';}?>
			</li>

			<?php endforeach ?>
			</ol>
			<br><br>
		<?else:?>
			<div class="alert alert-warning">Лот приватный, но не выбранно не одного поставщика!</div>
		<?endif;?>
	<?endif;?>
<?endif;?>

	<?if($arResult['LOT']['NOTVISIBLE_PROPOSAL'] == 'Y' && $arResult['RIGHT'] != "P" && $arResult['LOT_END'] == 'N') {
		echo '<div class="alert alert-info"><h5> Лот еще не завершен!</h5></div>';
		return false;
	} ?>
	
	
	<?/*if($arResult['SECURITY'] != 'N' && !CSite::InGroup(array(7))):?>
		<h3>Ожидаем ответа службы безопасности</h3>
	<?else:*/?>
	<?// Если безопасники проверили?>

	
<?if($arResult["OWNER"] == "Y" || $arResult['RIGHT'] == "W"):?>
	<?
	if (!isset($arResult["PROPOSAL"])) {
			echo '<div class="alert alert-info mt-2"><h5 style="text-align: center;"> Предложения по лоту не подавались</h5></div>';
	?>
	<? if($arResult["LOT"]["FAIL"] != "Y") { ?>
	<form name="fail_lot_form" action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data">
		<a data-toggle="modal" class="btn btn-fail" data-target="#failModal1" href="#" id="fail_lot1" style="color: #FFFFFF !important;">Признать несостоявшимся</a>
		<div class="modal fade" id="failModal1" tabindex="-1" role="dialog" aria-labelledby="failModalLabel1" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="failModalLabel1">Вы уверены, что хотите признать лот несостоявшимся?</h4>
					</div>
					<div class="modal-body">
						Укажите, пожалуйста, причину в поле ниже <span style="color:red;" class="required small">* (обязательно к заполнению)</span>: <br/>
						<textarea style="width:100%; height: 80px;" id="message_fail_lot1" name="message_lot"></textarea>
						<br/><br/>
						<input class="btn btn-primary" type="submit" name="fail_lot" value="Подтвердить"/>
						<button class="btn btn-primary" type="button" data-dismiss="modal" name="close_fail_lot">Отмена</button>
					</div>
				</div>
			</div>
		</div>
		<script>
			$("#fail_lot").click(function() {
				$("#message_fail_lot1").attr("required", "required");
			});
		</script>
	</form>
		<? } ?>
	<?	return false;
	}
	?>
	
	
	<div style="overflow-x:auto !important; overflow-y:hidden !important;">
	<div class="t_prov">
	<a name="proposal_table"></a>
	</br>
	[<?= GetMessage("PW_TD_CURRENCY") ?>
	: <?= $arResult["LOT"]["CURRENCY"] ?>] <br/>
	<form name="win_add" action="<?= POST_FORM_ACTION_URI ?>&active_tab=3" method="post" enctype="multipart/form-data">
		<table class="table t_proposal_table">
			<tr>
				<? if (($arResult['LOT']['PROPERTY'][8]['VALUE']== 1 && $arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y" && count($arResult["WIN"]) == 0) || ($arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y")): ?>
					<th><?= GetMessage("PW_TD_WINNER") ?></th>
				<? endif; ?>
				<th><?= GetMessage("PW_TD_SUPPLIER") ?></th>
				<th><?= GetMessage("PW_TD_MESSAGE") ?></th>
				<?if(($arResult['LOT_END'] == 'N' && $arResult['RIGHT'] == "W") || $arResult['LOT_END'] == 'Y'): ?>
					<? if ($arParams["NDS_TYPE"] != "N"): ?>
						<th><?= GetMessage("PW_TD_ITOGO_N") ?></th>
						<th><?= GetMessage("PW_TD_ITOGO") ?></th>
					<? else: ?>
						<th><?= GetMessage("PW_TD_ITOGO_N") ?></th>
						<th><?= GetMessage("PW_TD_ITOGO") ?></th>
					<? endif; ?>
					
					<th><?= GetMessage("PW_TD_SPEC") ?></th>
				<?endif;?>
				<th><?= GetMessage("PW_TD_DOP_INFO") ?></th>
				<? if (($arResult['LOT']['PROPERTY'][8]['VALUE']== 1 && $arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y" && count($arResult["WIN"]) == 0) || ($arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y")): ?>
					<th><?= GetMessage("PW_TD_COMMENTS") ?></th>
				<? endif; ?>
			</tr>
			<?
			foreach ($arResult["PROPOSAL"] as $v => $m) {
				if ($m["ITOGO"] > 0) {
					$min_pr[] = $m["ITOGO"];
				}
			}
			if ($arResult["LOT"]["TYPE_ID"] == "P") {
				rsort($min_pr);
			} else {
				sort($min_pr);
			}
			$bestpr = $min_pr[0]; //лучшая цена
			/* echo '<pre>';
			print_r($arResult["PROPOSAL"][335]); */
			
	
			?>

	<? foreach ($arResult["PROPOSAL"] as $idProp => $vProp): ?>
	
		
		<?

		
		$itogo = $arResult["PROPOSAL"][$idProp]["ITOGO"];
		$itogo_n = $arResult["PROPOSAL"][$idProp]["ITOGO_N"];
		if ($arResult["TYPE_ID"] != "S" && $arResult["TYPE_ID"] != "R") {
			$history = $arResult["PROPOSAL"][$idProp]["HISTORY"];
		}
		?>
		<tr>
		<?
		$checked = in_array($vProp["USER_ID"], $arResult["WIN"]) ? " checked" : "";
		?>
		<? if (($arResult['LOT']['PROPERTY'][8]['VALUE']== 1 && $arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y" && count($arResult["WIN"]) == 0) || ($arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y")): ?>
			<td>
				<input<?= $checked ?> type="checkbox" name="win[<?= $vProp["USER_ID"] ?>]" id="win_<?= $vProp["ID"] ?>" /> <!-- добавлен id для статистики -->
			</td>
		<? endif; ?>
		<td>
			<? if (is_file($vProp["USER_INFO"]["LOGO_SMALL"])): ?>
				<img src="<?= $vProp["USER_INFO"]["LOGO_SMALL"] ?>" alt="<?= $vProp["USER_INFO"]["STATUS_NAME"] ?>"/>
			<? endif; ?>
			<a class="user_view" href="#"
			   onclick="userView(<?= $vProp["ID"] ?>)"><?= strlen($vProp["USER_INFO"]["NAME_COMPANY"]) > 0 ? $vProp["USER_INFO"]["NAME_COMPANY"] : $vProp["USER_INFO"]["FIO"] ?></a>
			<?
			echo "<br />" . $vProp["DATE_START"];
			?>
			<div style="display:none">
				<div id="user_<?= $vProp["ID"] ?>">
					<table class="table t_proposal_table">
						<tbody>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_FIO") ?></td>
							<td><?= $vProp["USER_INFO"]["FIO"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_NAME_COMPANY") ?></td>
							<td><?= $vProp["USER_INFO"]["NAME_COMPANY"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_NAME_DIRECTOR") ?></td>
							<td><?= $vProp["USER_INFO"]["NAME_DIRECTOR"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_NAME_ACCOUNTANT") ?></td>
							<td><?= $vProp["USER_INFO"]["NAME_ACCOUNTANT"] ?></td>
						</tr>
						<tr>
							<td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_CODE") ?></b></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_CODE_INN") ?></td>
							<td><?= $vProp["USER_INFO"]["CODE_INN"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_CODE_KPP") ?></td>
							<td><?= $vProp["USER_INFO"]["CODE_KPP"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_CODE_OKVED") ?></td>
							<td><?= $vProp["USER_INFO"]["CODE_OKVED"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_CODE_OKPO") ?></td>
							<td><?= $vProp["USER_INFO"]["CODE_OKPO"] ?></td>
						</tr>
						<tr>
							<td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_LEGALADDRESS") ?></b></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_REGION") ?></td>
							<td><?= $vProp["USER_INFO"]["LEGALADDRESS_REGION"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_CITY") ?></td>
							<td><?= $vProp["USER_INFO"]["LEGALADDRESS_CITY"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_INDEX") ?></td>
							<td><?= $vProp["USER_INFO"]["LEGALADDRESS_INDEX"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_STREET") ?></td>
							<td><?= $vProp["USER_INFO"]["LEGALADDRESS_STREET"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_POST") ?></td>
							<td><?= $vProp["USER_INFO"]["LEGALADDRESS_POST"] ?></td>
						</tr>
						<tr>
							<td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_POSTALADDRESS") ?></b></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_REGION") ?></td>
							<td><?= $vProp["USER_INFO"]["POSTALADDRESS_REGION"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_CITY") ?></td>
							<td><?= $vProp["USER_INFO"]["POSTALADDRESS_CITY"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_INDEX") ?></td>
							<td><?= $vProp["USER_INFO"]["POSTALADDRESS_INDEX"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_STREET") ?></td>
							<td><?= $vProp["USER_INFO"]["POSTALADDRESS_STREET"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_POST") ?></td>
							<td><?= $vProp["USER_INFO"]["POSTALADDRESS_POST"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_PHONE") ?></td>
							<td><?= $vProp["USER_INFO"]["PHONE"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_FAX") ?></td>
							<td><?= $vProp["USER_INFO"]["FAX"] ?></td>
						</tr>
						<tr>
							<td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_STATEREG") ?></b></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_STATEREG_PLACE") ?></td>
							<td><?= $vProp["USER_INFO"]["STATEREG_PLACE"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_STATEREG_DATE") ?></td>
							<td><?= $vProp["USER_INFO"]["STATEREG_DATE"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_STATEREG_OGRN") ?></td>
							<td><?= $vProp["USER_INFO"]["STATEREG_OGRN"] ?></td>
						</tr>
						<tr>
							<td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_BANK") ?></b></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_BANKING_NAME") ?></td>
							<td><?= $vProp["USER_INFO"]["BANKING_NAME"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNT") ?></td>
							<td><?= $vProp["USER_INFO"]["BANKING_ACCOUNT"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNTCORR") ?></td>
							<td><?= $vProp["USER_INFO"]["BANKING_ACCOUNTCORR"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_BANKING_BIK") ?></td>
							<td><?= $vProp["USER_INFO"]["BANKING_BIK"] ?></td>
						</tr>
						<? if ($vProp["USER_INFO"]["PROP"]): ?>
							<tr>
								<td colspan="2"><b><?= GetMessage("PW_TD_GROUP_DOP_PROP") ?></b></td>
							</tr>
							<? foreach ($vProp["USER_INFO"]["PROP"] as $arProp): ?>
								<? if ($vProp["USER_INFO"]["PROP_SUPPLIER"][$arProp["ID"]]): ?>
									<tr>
										<td><?= $arProp["TITLE"] ?></td>
										<?
										if ($arProp["PROPERTY_TYPE"] == "L") {
											$arPropDef = unserialize(base64_decode($arProp["DEFAULT_VALUE"]));
										}
										$rsPropSupp = array();
										$arFile = array();
										foreach ($vProp["USER_INFO"]["PROP_SUPPLIER"][$arProp["ID"]] as $arPropSupp) {
											if ($arProp["PROPERTY_TYPE"] == "L") {
												$arPropSupp["VALUE"] = $arPropDef["DEFAULT_VALUE"][$arPropSupp["VALUE"]];
											}
											if ($arProp["PROPERTY_TYPE"] == "F") {
												$rsFile = CFile::GetByID($arPropSupp["VALUE"]);
												$arFile = $rsFile->Fetch();
												$arPropSupp["VALUE"] = "<a href='" . CFile::GetPath($arPropSupp["VALUE"]) . "'>" . $arFile["ORIGINAL_NAME"] . "</a>";
											}
											if ($arProp["PROPERTY_TYPE"] == "D") {
												$arPropSupp["VALUE"] = date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), strtotime($arPropSupp["VALUE"]));
											}
											$rsPropSupp[] = $arPropSupp["VALUE"];
										}
										?>
										<td><?= implode(",", $rsPropSupp) ?></td>
									</tr>
								<? endif; ?>
							<? endforeach; ?>
						<? endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</td>
		<td align="center">
			<? if (strlen($vProp["MESSAGE"]) > 0 || count($vProp["FILE"]) > 0): ?>
				<a href="#" class="mess_view" onclick="messView(<?= $vProp["ID"] ?>)">
					<i class="fa fa-comment" style="color:green;font-size:20px;"></i>
					<!--img src="/bitrix/components/pweb.tenderix/proposal.list/templates/.default/images/message_activ.png"/></a-->
				<div style="display:none">
					<div id="mess_<?= $vProp["ID"] ?>">
						<h3><?= GetMessage("PW_TD_MESSAGE_FILE_SUPPLIER") ?> <?= $vProp["USER_INFO"]["NAME_COMPANY"] ?></h3>

						<p><b><?= GetMessage("PW_TD_FILE_SUPPLIER") ?>: </b><br/>
							<?
							if (count($vProp["FILE"]) > 0) {
								foreach ($vProp["FILE"] as $arFile) {
									?>
									<a href="/bitrix/components/pweb.tenderix/proposal.list/proposal_file.php?PROPOSAL_ID=<?= $vProp["ID"] ?>&FILE_ID=<?= $arFile["ID"] ?>"><?= $arFile["ORIGINAL_NAME"] ?></a>
									<br/>
								<?
								}
							} else {
								echo "-";
							}
							?>
						</p>

						<p><b><?= GetMessage("PW_TD_MESSAGE_SUPPLIER") ?>: </b><br/>
							<?
							if (strlen($vProp["MESSAGE"]) > 0) {
								echo nl2br($vProp["MESSAGE"]);
							} else {
								echo "-";
							}
							?>
						</p>
					</div>
				</div>
			<? else: ?>
				<i class="fa fa-comment-o" style="color:#adadad;font-size:20px;"></i>
				<!--img src="/bitrix/components/pweb.tenderix/proposal.list/templates/.default/images/message_no.png"/-->
			<? endif; ?>
		</td>
		<?if(($arResult['LOT_END'] == 'N' && $arResult['RIGHT'] == "W") || $arResult['LOT_END'] == 'Y'): ?>
		<td <? if ($itogo == $bestpr) {
			echo " style='background-color:#CCFFCC;'";
		} ?>>

			<nobr><?= ($itogo > 0) ? number_format($itogo_n, 2, '.', ' ') : '--' ?></nobr>
			<? if ((strlen($checked) > 0) || $itogo == $bestpr): ?>
				<i class="fa fa-flag" style="color:red;font-size: 16px;"></i>
				<!--img src="/bitrix/components/pweb.tenderix/proposal.list/templates/.default/images/best-price.png"/-->
			<? endif; ?>
		</td>
		<td<? if ($itogo == $bestpr) {
			echo " style='background-color:#CCFFCC;'";
		} ?>>
			<nobr><?= ($itogo > 0) ? number_format($itogo, 2, '.', ' ') : '--' ?></nobr>
			<? if (strlen($checked) > 0): ?>
				<img src="/bitrix/components/pweb.tenderix/proposal.list/templates/.default/images/best-price.png"/>
			<? endif; ?>
			<? /**
			 * Добавлено для Статистика. В. Филиппов. 15.04.16
			 */ ?>
			<input type="hidden" name="best[<?= $vProp["USER_ID"] ?>]" id="best_<?= $vProp["ID"] ?>" value="<? ($checked != "" ? $itogo : 0); ?>" />
		</td>

			<?
			/**
			 * Добавлено для статистики. В. Филиппов. 14.04.16
			 */
			?>
			<script>
				$("#win_<?= $vProp["ID"] ?>").bind("change click", function () {
					if($("#win_<?= $vProp["ID"] ?>").prop('checked')) {
						$("#best_<?= $vProp["ID"] ?>").val(<?=$itogo; ?>);
					}
					else {
						$("#best_<?= $vProp["ID"] ?>").val(0);
					}
				});
			</script>
		
		<? if ($arResult["TYPE_ID"] != "S" && $arResult["TYPE_ID"] != "R"): ?>
			<td align="center">
				<a href="#" class="spec_view" onclick="specView(<?= $vProp["ID"] ?>)">
					<i class="fa fa-table" style="color:#d46e37;font-size: 20px;"></i>
					<!--img
						src="/bitrix/components/pweb.tenderix/proposal.list/templates/.default/images/specification.png"/></a-->

				<div style="display:none">
					<div id="spec_table_<?= $vProp["ID"] ?>">
						<table class="table t_proposal_table">
							<?
							$numProp = 1;
							$sum_item = 0;
							$sum_item_NDS = 0;
							$itogo_sum_item = 0;
							$itogo_sum_item_NDS = 0;
							?>
							<? foreach ($history as $idPropBuyer => $specProp) : ?>
								<? if ($numProp == 1): ?>
									<tr>
										<th style="text-align:center;"><?= GetMessage("PW_TD_SPEC_NUM") ?></th>
										<th style="text-align:center;"><?= GetMessage("PW_TD_SPEC_TOVAR") ?></th>
										<? if($arResult["LOT"]["NOT_ANALOG"] == "N") { ?>
											<th><?= GetMessage("PW_TD_SPEC_ANALOG") ?></th>
										<? } ?>
										<th style="text-align:center;"><?= GetMessage("PW_TD_SPEC_NDS") ?></th>
										<th style="text-align:center;">
										<?
										// Логика для заголовка столбца цены
										if ($arResult["LOT"]["CURRENCY"] == "KZT") {
											// Для KZT показываем в зависимости от WITH_NDS
											if ($arResult['LOT']['WITH_NDS'] == 'Y') {
												echo "Цена за ед. с НДС";
											} else {
												echo "Цена за ед. без НДС";
											}
										} else {
											// Для других валют всегда без НДС
											echo "Цена за ед. без НДС";
										}
										?>
										</th>
										<th style="width:25%; text-align: center;">
										<?
										// Логика для заголовка столбца суммы
										if ($arResult["LOT"]["CURRENCY"] == "KZT") {
											// Для KZT показываем в зависимости от WITH_NDS
											if ($arResult['LOT']['WITH_NDS'] == 'Y') {
												echo "Сумма с НДС";
											} else {
												echo GetMessage("PW_TD_SPEC_PRICE_NDS_SUM");
											}
										} else {
											// Для других валют всегда без НДС
											echo GetMessage("PW_TD_SPEC_PRICE_NDS_SUM");
										}
										?>
										</th>
									</tr>
								<? endif; ?>
								<tr>
									<td align="center"><? echo $numProp ?></td>
									<td>
										<b><?= GetMessage("PW_TD_SPEC_NAME_PROD") ?>:</b> <?= $specProp["TITLE"] ?><br/>
										<b><?= GetMessage("PW_TD_SPEC_COUNT") ?>
											:</b> <?= $specProp["COUNT"] ?>  <?= $specProp["UNIT_NAME"] ?><br/>
										<? if (strlen($specProp["ADD_INFO"]) > 0): ?>
											<b><?= GetMessage("PW_TD_SPEC_ADD_INFO") ?>
												:</b> <?= $specProp["ADD_INFO"] ?>
										<? endif; ?>
									</td>
									<? if($arResult["LOT"]["NOT_ANALOG"] == "N") { ?>
										<td><?= $specProp["ANALOG"] ?></td>
									<? } ?>
									<td align="center"><?= $specProp["NDS"] ?></td>
									<td align="center">
										<?
										// Логика отображения цены
										if ($specProp["PRICE_NDS"] == 0) {
											echo "--";
										} else {
											$displayPrice = $specProp["PRICE_NDS"];
											
											// Если валюта не KZT, всегда показываем без НДС
											if ($arResult["LOT"]["CURRENCY"] != "KZT") {
												// Рассчитываем цену без НДС
												if($specProp["NDS"] == '0'){
													$nds_multiplier = 1;
												}elseif($specProp["NDS"] =='10'){
													$nds_multiplier = 1.10;
												}elseif($specProp["NDS"] == '18'){
													$nds_multiplier = 1.18;
												} else {
													$nds_multiplier = 1;
												}
												// Если цена с НДС, убираем НДС
												if ($arResult['LOT']['WITH_NDS'] == 'Y' && $nds_multiplier > 1) {
													$displayPrice = $displayPrice / $nds_multiplier;
												}
											}
											// Если валюта KZT и цена без НДС, но нужно показать с НДС
											elseif ($arResult["LOT"]["CURRENCY"] == "KZT" && $arResult['LOT']['WITH_NDS'] == 'Y') {
												// Цена уже с НДС, показываем как есть
												$displayPrice = $specProp["PRICE_NDS"];
											}
											// Если валюта KZT и цена с НДС, но нужно показать без НДС
											elseif ($arResult["LOT"]["CURRENCY"] == "KZT" && $arResult['LOT']['WITH_NDS'] != 'Y') {
												// Цена уже без НДС, показываем как есть
												$displayPrice = $specProp["PRICE_NDS"];
											}
											
											echo number_format($displayPrice, 2, '.', ' ');
										}
										?>
									</td>
									<?
									// Расчет коэффициента НДС
									if($specProp["NDS"] == '0'){
										$nds = 1;
									}elseif($specProp["NDS"] =='10'){
										$nds = 1.10;
									}elseif($specProp["NDS"] == '18'){
										$nds = 1.18;
									}
									
									// Расчет суммы в зависимости от валюты
									$base_price = floatval($specProp["PRICE_NDS"]);
									
									// Если валюта не KZT, всегда считаем без НДС
									if ($arResult["LOT"]["CURRENCY"] != "KZT") {
										// Если цена с НДС, убираем НДС
										if ($arResult['LOT']['WITH_NDS'] == 'Y' && $nds > 1) {
											$base_price = $base_price / $nds;
										}
										$sum_item = $base_price * floatval($specProp["COUNT"]);
										$itogo_sum_item += $sum_item;
										$sum_item_NDS = $sum_item; // Для не-KZT всегда без НДС
									}
									// Если валюта KZT
									else {
										if ($arResult['LOT']['WITH_NDS'] == 'Y') {
											// Цена с НДС
											$sum_item = $base_price * floatval($specProp["COUNT"]);
											$itogo_sum_item += $sum_item;
											$sum_item_NDS = $sum_item;
										} else {
											// Цена без НДС
											$sum_item = $base_price * floatval($specProp["COUNT"]);
											$itogo_sum_item += $sum_item;
											$sum_item_NDS = $base_price * floatval($specProp["COUNT"]) * $nds;
										}
									}
									$itogo_sum_item_NDS += $sum_item_NDS;
									?>
									<td align="center" style="width:25%;">
									<?
									if ($sum_item == 0) {
										echo "--";
									} else {
										// Показываем сумму в зависимости от валюты и типа НДС
										if ($arResult["LOT"]["CURRENCY"] == "KZT" && $arResult['LOT']['WITH_NDS'] == 'Y') {
											// KZT с НДС - показываем сумму с НДС
											echo number_format($sum_item, 2, '.', ' ');
										} else {
											// KZT без НДС или другие валюты - показываем сумму без НДС
											echo number_format($sum_item, 2, '.', ' ');
										}
									}
									?>
									</td>
								</tr>
								<? $numProp++; ?>
							<? endforeach; ?>
							<tr>
								<td colspan="<?=($arResult["LOT"]["NOT_ANALOG"] == "N" ? 5 : 4); ?>" align="right"><?= GetMessage("PW_TD_ITOGO_ALL") ?>:</td>
								<td style="width:25%;">
								<?
								// Логика для итоговой суммы в зависимости от валюты
								if ($arResult["LOT"]["CURRENCY"] == "KZT" && $arResult['LOT']['WITH_NDS'] == 'Y') {
									// KZT с НДС - показываем сумму с НДС
									echo number_format($itogo_sum_item, 2, '.', ' ');
								} else {
									// KZT без НДС или другие валюты - показываем сумму без НДС
									echo number_format($itogo_sum_item, 2, '.', ' ');
								}
								?>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		<?endif;?>
		<?endif; ?>
		<? //товары?>
		<? if ($arResult["TYPE_ID"] == "S" || $arResult["TYPE_ID"] == "R"): ?>
			<td align="center">
				<a href="#" class="tovar_view" onclick="tovarView(<?= $vProp["ID"] ?>)">
					<i class="fa fa-table" style="color:#d46e37;font-size: 20px;"></i>
				</a>

				<div style="display:none">
					<div id="tovar_table_<?= $vProp["ID"] ?>">
						<table class="t_proposal_table">
							<?
							$numProp = 1;
							$sum_item = 0;
							$itogo_sum_item = 0;
							?>
							
							<? foreach ($vProp["PRODUCT"] as $idPropBuyer => $tovarProp) : ?>
								<? if ($numProp == 1): ?>
									<tr>
										<th><?= GetMessage("PW_TD_SPEC_NUM") ?></th>
										<th><?= GetMessage("PW_TD_SPEC_TOVAR") ?></th>
										<th><?= GetMessage("PW_TD_SPEC_NDS") ?></th>
										<th>
										<?
										// Логика для заголовка столбца цены
										if ($arResult["LOT"]["CURRENCY"] == "KZT") {
											// Для KZT показываем в зависимости от WITH_NDS
											if ($arResult['LOT']['WITH_NDS'] == 'Y') {
												echo "Цена за ед. с НДС";
											} else {
												echo "Цена за ед. без НДС";
											}
										} else {
											// Для других валют всегда без НДС
											echo "Цена за ед. без НДС";
										}
										?>
										</th>
										<th>
										<?
										// Логика для заголовка столбца суммы
										if ($arResult["LOT"]["CURRENCY"] == "KZT") {
											// Для KZT показываем в зависимости от WITH_NDS
											if ($arResult['LOT']['WITH_NDS'] == 'Y') {
												echo "Сумма с НДС";
											} else {
												echo GetMessage("PW_TD_SPEC_PRICE_NDS_SUM");
											}
										} else {
											// Для других валют всегда без НДС
											echo GetMessage("PW_TD_SPEC_PRICE_NDS_SUM");
										}
										?>
										</th>
									</tr>
								<? endif; ?>
								<tr>
									<td align="center"><? echo $numProp ?></td>
									<td>
										<b><?= GetMessage("PW_TD_SPEC_NAME_PROD") ?>
											:</b> <?= $tovarProp["PROD"]["TITLE"] ?>
										<br/>
										<? foreach ($vProp["PROP"][$idPropBuyer] as $propB): ?>
											<b><?=$propB["TITLE"]?>:</b> <?=$propB["VALUE"]?><br />
										<? endforeach; ?>
										<b><?= GetMessage("PW_TD_SPEC_COUNT") ?>
											:</b> <?= $tovarProp["COUNT"] ?>  <?= $tovarProp["PROD"]["UNIT_NAME"] ?>
									</td>
									<td align="center"><?= $tovarProp["NDS"] ?></td>
									<td align="center">
										<?
										// Логика отображения цены для товаров
										if ($tovarProp["PRICE_NDS"] == 0) {
											echo "--";
										} else {
											$displayPrice = $tovarProp["PRICE_NDS"];
											
											// Если валюта не KZT, всегда показываем без НДС
											if ($arResult["LOT"]["CURRENCY"] != "KZT") {
												// Рассчитываем цену без НДС
												if($tovarProp["NDS"] == '0'){
													$nds_multiplier = 1;
												}elseif($tovarProp["NDS"] =='10'){
													$nds_multiplier = 1.10;
												}elseif($tovarProp["NDS"] == '18'){
													$nds_multiplier = 1.18;
												} else {
													$nds_multiplier = 1;
												}
												// Если цена с НДС, убираем НДС
												if ($arResult['LOT']['WITH_NDS'] == 'Y' && $nds_multiplier > 1) {
													$displayPrice = $displayPrice / $nds_multiplier;
												}
											}
											// Если валюта KZT, показываем как есть (уже в нужном формате)
											
											echo number_format($displayPrice, 2, '.', ' ');
										}
										?>
									</td>
									<?
									// Расчет суммы для товаров в зависимости от валюты
									$base_price = floatval($tovarProp["PRICE_NDS"]);
									
									// Если валюта не KZT, всегда считаем без НДС
									if ($arResult["LOT"]["CURRENCY"] != "KZT") {
										// Рассчитываем коэффициент НДС
										if($tovarProp["NDS"] == '0'){
											$nds = 1;
										}elseif($tovarProp["NDS"] =='10'){
											$nds = 1.10;
										}elseif($tovarProp["NDS"] == '18'){
											$nds = 1.18;
										} else {
											$nds = 1;
										}
										// Если цена с НДС, убираем НДС
										if ($arResult['LOT']['WITH_NDS'] == 'Y' && $nds > 1) {
											$base_price = $base_price / $nds;
										}
										$sum_item = $base_price * floatval($tovarProp["COUNT"]);
									}
									// Если валюта KZT
									else {
										$sum_item = $base_price * floatval($tovarProp["COUNT"]);
									}
									$itogo_sum_item += $sum_item;
									?>
									<td align="center">
										<?
										if ($sum_item == 0) {
											echo "--";
										} else {
											echo number_format($sum_item, 2, '.', ' ');
										}
										?>
									</td>
								</tr>
								<? $numProp++; ?>
							<? endforeach; ?>
							<tr>
								<td colspan="4" align="right"><?= GetMessage("PW_TD_ITOGO_ALL") ?>:</td>
								<td><?= number_format($itogo_sum_item, 2, '.', ' ') ?></td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		<? endif; ?>
		<td>
			<? if (strlen($vProp["TERM_PAYMENT_VAL"]) > 0): ?>
				<b><?= GetMessage("PW_TD_PAYMENT") ?>:</b> <?= $vProp["TERM_PAYMENT_VAL"] ?><br/>
			<? endif; ?>
			<? if (strlen($vProp["TERM_DELIVERY_VAL"]) > 0): ?>
				<b><?= GetMessage("PW_TD_DELIVERY") ?>:</b> <?= $vProp["TERM_DELIVERY_VAL"] ?><br/>
			<? endif; ?>
			<? /*
			if ($arResult["TYPE_ID"] == "S") {
				foreach ($vProp["PROP"] as $arProductProp) {
					?>
					<b><?= $arProductProp["TITLE"] ?>:</b> <?= $arProductProp["VALUE"] ?><br/>
				<?
				}
			}*/
			?>

			<?
			/*                     * **************
			 * PROPERTY
			 * *************** */
			?>

			<?
			$arPropProposal = $arResult["PROP_PROPOSAL"][$idProp];
			foreach ($arResult["PROP_LIST"]["START_LOT"] as $arPropList) :
				?>
				<?
				$is_file_prop = false;
				$result_list = array();

				if ($arPropList["PROPERTY_TYPE"] == "F" && ($rsFiles = CTenderixProposal::GetFileListProperty($idProp, $arPropList["ID"])) && ($arFile = $rsFiles->GetNext())) {
					?>
					<?
					$is_file_prop = true;
					do {
						$result_list[] = '<a href="/tx_files/property_file.php?PROPOSAL_ID=' . $idProp . '&amp;FILE_ID=' . $arFile["ID"] . '&amp;PROPERTY=' . $arPropList["ID"] . '">' . $arFile["ORIGINAL_NAME"] . '</a>';
					} while ($arFile = $rsFiles->GetNext());
				}

				if (strlen($arPropList["DEFAULT_VALUE"]) > 0 && $arPropList["MULTI"] == "Y") {
					$arPropList["MULTI_CNT"]++;
				}
				$cntProp = 0;
				if ($arPropList["PROPERTY_TYPE"] != "L" && $arPropList["PROPERTY_TYPE"] != "F") {
					$cntProp = count($arPropProposal[$arPropList["ID"]]);
					$arPropList["MULTI_CNT"] = $cntProp;
				}
				if ($arPropList["PROPERTY_TYPE"] == "L" || $arPropList["MULTI"] == "N") {
					$arPropList["MULTI_CNT"] = 1;
				}

				for ($i = 0; $i < $arPropList["MULTI_CNT"]; $i++) {
					switch ($arPropList["PROPERTY_TYPE"]) {
						case "S":
						case "N":
						case "T":
							if (strlen(htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"])) > 0)
								$result_list[] = htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"]);
							break;
						case "L":
							$arrListValue = array();
							$arrList = unserialize(base64_decode($arPropList["DEFAULT_VALUE"]));
							foreach ($arPropProposal[$arPropList["ID"]] as $arrListSupplier) {
								$arrListValue[] = $arrListSupplier["VALUE"];
							}
							foreach ($arrList["DEFAULT_VALUE"] as $idRow => $listVal) {
								if (in_array($idRow, $arrListValue))
									$result_list[] = $listVal;
							}
							break;
						case "D":
							$result_list[] = ConvertTimeStamp(strtotime($arPropProposal[$arPropList["ID"]][$i]["VALUE"]), "FULL");
							break;
					}
				}

				if (count($result_list) > 0) {
					echo "<b>" . $arPropList["TITLE"] . ":</b> " . implode(", ", $result_list) . "<br />";
				}
				?>
			<? endforeach; ?>
			<?
			/*                     * **************
			 * PROPERTY
			 * *************** */
			?>
		</td>
		<? if (($arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y" && count($arResult["WIN"]) == 0) || ($arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y")): ?>
			<td>

				<? if(count($arResult["WIN"]) > 0){?>
					<div><?= $arResult["WIN_COMMENT"][$vProp["USER_ID"]] ?></div>
				<?}else{?>
				<textarea name="comment[<?= $vProp["USER_ID"] ?>]" rows="2"
						  cols="15"><?= $arResult["WIN_COMMENT"][$vProp["USER_ID"]] ?></textarea>
				<?}?>
			</td>
		<? endif; ?>
		</tr>
	<?endforeach; ?>
	</table>
	<br/>
	<?/*if($arResult['END_ANALIZ'] == "N"):?>
		<div class="alert alert-danger">
			Анализ еще не закончен!
		</div>
	<?endif;*/?>
	<!--br/-->

	<? if (($arResult['LOT']['PROPERTY'][8]['VALUE']== 1 && $arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y" && count($arResult["WIN"]) == 0) || ($arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y")): ?>
		<? if($arResult['LOT']['ARCHIVE'] != 'Y') {?>
			<input class="btn btn-add-submit" type="submit" name="win_add_submit" id="winner_lot" value="<?= GetMessage("PW_TD_SUBMIT_WIN") ?>"/>
			<? if(count($arResult["WIN"]) == 0) { ?>
				<a data-toggle="modal" class="btn btn-fail" data-target="#failModal" href="#" id="fail_lot" style="color: #FFFFFF !important;">Признать несостоявшимся</a>
				<div class="modal fade" id="failModal" tabindex="-1" role="dialog" aria-labelledby="failModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
								<h4 class="modal-title" id="failModalLabel">Вы уверены, что хотите признать лот несостоявшимся?</h4>
							</div>
							<div class="modal-body">
								Укажите, пожалуйста, причину в поле ниже <span style="color:red;" class="required small">* (обязательно к заполнению)</span>: <br/>
								<textarea style="width:100%; height: 80px;" id="message_fail_lot" name="message_lot"></textarea>
								<br/><br/>
								<input class="btn btn-primary" type="submit" name="fail_lot" value="Подтвердить"/>
								<button class="btn btn-primary" type="button" data-dismiss="modal" name="close_fail_lot">Отмена</button>
							</div>
						</div>
					</div>
				</div>
				<script>
					$("#fail_lot").click(function() {
						$("#message_fail_lot").attr("required", "required");
					});
					$("#winner_lot").click(function() {
						$("#message_fail_lot").removeAttr("required");
					});
				</script>
			<? } ?>
		<? } ?>
		<? if(($arResult['LOT']['ARCHIVE'] != 'Y' && count($arResult["WIN"]) > 0) || ($arResult['LOT']['ARCHIVE'] != 'Y' && $arResult['LOT']['FAIL'] == 'Y')) { ?>
			<a data-toggle="modal" class="btn btn-primary" data-target="#archiveModal" id="lot_archive" href="#" style="color: #FFFFFF !important;">Перенести в архив</a>
			<div class="modal fade" id="archiveModal" tabindex="-1" role="dialog" aria-labelledby="archiveModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
							<h4 class="modal-title" id="archiveModalLabel">Вы уверены, что хотите отправить лот в архив?</h4>
						</div>
						<div class="modal-body">
							Комментарий (необязательно): <br/>
							<textarea style="width:100%; height: 80px;" name="message_lot"></textarea>
							<br/><br/>
							<input class="btn btn-primary" type="submit" name="lotarch_submit" value="Подтвердить"/>
							<button class="btn btn-primary" type="button" data-dismiss="modal" name="close_archive_lot">Отмена</button>
						</div>
					</div>
				</div>
			</div>
			<script>
				$("#lot_archive").click(function() {
					$("#message_fail_lot").removeAttr("required");
				});
			</script>
		<? } ?>
		<!--input class="btn btn-primary" type="submit" name="second_step" value="Переход на второй этап"/-->
		<!--input class="btn btn-primary" type="submit" name="new_protocol" value="Сформировать протокол"/ -->
	<? endif; ?>

	</form>
	</div>
	</div>
	<br clear="all"/>
	<div class="row">
		<div class="col-md-12">
			<?if($arResult['LOT_END'] == 'Y' && ($arResult['RIGHT'] == "W" || $arResult['RIGHT'] == "P")): ?>
				<?foreach ($arResult['PROP_LIST']['END_LOT'] as $dopPropLotEnd): ?>
					<?if($dopPropLotEnd['ID'] == '2'):?>
						<div style="margin-top:20px;position:relative;" class="alert alert-warning message-dopprop">
							<h4><?=$dopPropLotEnd['TITLE'];?>:</h4>
							<div class="collapse in" id="collapseExample">
								<?echo nl2br($arResult['LOT']['PROPERTY'][2]['VALUE']);?>
							</div>
						</div>
					<?else:?>

						<?$arResult["PROP_LIST_END_LOT"][] = $dopPropLotEnd;?>

					<?endif;?>
				<?endforeach ?>
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
		<form name="lotadd_prop_form" action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data">
		<?
		$arPropProposal = $arResult["PROP_PROPOSAL"];
		$arPropProposal .= $arResult['LOT']['PROPERTY'];
		foreach ($arResult["PROP_LIST_END_LOT"] as $arPropList) :?>

			<?if($arResult['RIGHT'] == "P" && $arPropList['ID'] == 10):?>
				
			<?endif;?>
		<?//__($arPropList);?>
			<?if ($arPropList['END_LOT'] == 'Y' && $arPropList['T_RIGHT'] == 'W'):?>
			<?if(($arResult['END_ANALIZ'] == 'N' && $arPropList['ID'] != '10') || $arResult['END_ANALIZ'] == 'Y'):?>
				<div>
				<?if(count($arResult['WIN']) > 0 && $arPropList['ID'] == 8):?>
				<?else:?>
					<h4><?= $arPropList["TITLE"] ?>
						<? if ($arPropList["IS_REQUIRED"] == "Y"):?>
						<span style="color:red;" class="required small">* (обязательно к заполнению)</span>
						<? endif; ?>
					</h4>
					<?if($arPropList["DESCRIPTION"]):?>
					<div><?= $arPropList["DESCRIPTION"] ?></div>
					<?endif;?>
				<?endif;?>
					

					<? $is_file_prop = false; ?>
					<? if ($arPropList["PROPERTY_TYPE"] == "F" && ($rsFiles = CTenderixProposal::GetFileListPropertyLot($arResult["LOT"]["ID"], $arPropList["ID"])) && ($arFile = $rsFiles->GetNext())) { ?>
						<? $is_file_prop = true; ?>
						<strong>Загруженные файлы:</strong>
						<table>
							<tr>
								<td>
									<table class="t_proposal_table">
										<tr>
											<th>Имя файла</th>
											<th>Размер файла</th>
											<?if ($arResult['END_ANALIZ'] == 'N' || ($arResult['END_ANALIZ'] == 'Y' && $arResult['RIGHT'] == 'W')):?>
												<th>Удалить?</th>
											<?endif;?>

										</tr>
										<?
										do {
											?>
											<tr>
												<td><?if($arPropList['S_RIGHT'] == 'D'):?>
													<a href="/tx_files/property_file.php?LOT_ID=<?= $arResult["LOT"]["ID"] ?>&amp;FILE_ID=<?= $arFile["ID"] ?>&amp;PROPERTY=<?= $arPropList["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a>
												<?else:?>
													<a href="/tx_files/property_file.php?PROPOSAL_ID=<?= $arResult["PROPOSAL_ID"] ?>&amp;FILE_ID=<?= $arFile["ID"] ?>&amp;PROPERTY=<?= $arPropList["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a>
												<?endif;?>
													
												</td>
												<td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
												<?if ($arResult['END_ANALIZ'] == 'N' || ($arResult['END_ANALIZ'] == 'Y' && $arResult['RIGHT'] == 'W')):?>
												<td align="center">
													<input type="checkbox" name="FILE_ID_PROP[<? echo $arFile["ID"] ?>]"
														   value="<? echo $arFile["ID"] ?>">
													<input type="hidden"
														   name="PROP[<?= $arPropList["ID"] ?>][<?= $arFile["ID"] ?>]"/>
													<input type="hidden" name="FILE_PROP" value="<?= $arPropList["ID"] ?>"/>
												</td>
												<?endif;?>
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
			if ($arResult['LOT']["ID"] > 0 && $arPropList["PROPERTY_TYPE"] != "L" && $arPropList["PROPERTY_TYPE"] != "F") {
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
							if(count($arResult['WIN']) == 0){
							$result .= '<input type="file" name="PROP[' . $arPropList["ID"] . '][n' . ($i - $cntProp) . ']" size="' . $arPropList["COL_COUNT"] . '" />';
							}
						break;
					case "L":
						$arrList = unserialize(base64_decode($arPropList["DEFAULT_VALUE"]));
						if ($arResult["LOT"]["ID"] > 0) {
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
						if($arPropList['ID'] == 8):
							if($arResult['END_ANALIZ'] == 'N' || ($arResult['END_ANALIZ'] == 'Y' && $arResult['RIGHT'] == 'W' && $arResult['LOT']['ARCHIVE'] != 'Y')):
								$result .= '<select name="PROP[' . $arPropList["ID"] . '][]"' . ($arPropList["MULTI"] == "Y" ? " multiple" : "") . ' size="' . $arPropList["ROW_COUNT"] . '">';
								foreach ($arrList["DEFAULT_VALUE"] as $idRow => $listVal) {
									$result .= '<option' . (in_array($idRow, $arrListValue) ? " selected" : "") . ' value="' . $idRow . '">' . $listVal . '</option>';
								}
								$result .= '</select>';
							endif; 
						else:
							$result .= '<select name="PROP[' . $arPropList["ID"] . '][]"' . ($arPropList["MULTI"] == "Y" ? " multiple" : "") . ' size="' . $arPropList["ROW_COUNT"] . '">';
							foreach ($arrList["DEFAULT_VALUE"] as $idRow => $listVal) {
								$result .= '<option' . (in_array($idRow, $arrListValue) ? " selected" : "") . ' value="' . $idRow . '">' . $listVal . '</option>';
							}
							$result .= '</select>';
						endif;
						break;
					case "T":
						if ($i > 0 || $arResult["LOT"]["ID"] > 0) {
							$arPropList["DEFAULT_VALUE"] = "";
						}
						if ($arResult["PROPOSAL_ID"] > 0 && $i < $cntProp) {
							$propName = "PROP[" . $arPropList["ID"] . "][" . $arPropProposal[$arPropList["ID"]][$i]["ID"] . "]";
							$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"]);
						} else {
							//$propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
							$propName = "PROP[" . $arPropList["ID"] . "][n0]";
							$propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : htmlspecialcharsEx($arPropList["DEFAULT_VALUE"]);
						}
						if(isset($arResult['LOT']['PROPERTY'][$arPropList["ID"]])){
							if($arResult['RIGHT'] == 'S' && $arPropList["ID"] != 10 && count($arResult["WIN"]) <= 0 && $arResult['END_ANALIZ'] == "N"):
								$result .= '<textarea class="form-control" name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $arResult['LOT']['PROPERTY'][$arPropList["ID"]]['VALUE'] . '</textarea>';
							else:
								if($arResult['RIGHT'] == 'W' && $arResult['END_ANALIZ'] == "Y" && $arPropList["ID"] == 10 && $arResult['LOT']['ARCHIVE'] != 'Y'):
									$result .= '<textarea class="form-control" name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $arResult['LOT']['PROPERTY'][$arPropList["ID"]]['VALUE'] . '</textarea>';
								else:
									echo "<strong>".$arResult['LOT']['PROPERTY'][$arPropList["ID"]]['VALUE']."</strong>";
								endif;
								
							endif;
							if(count($arResult["WIN"]) > 0 && ($arResult['END_ANALIZ'] == "Y" && $arResult['RIGHT'] != 'S' && $arPropList["ID"] != 9)){
								
								echo "<strong>".$arResult['LOT']['PROPERTY'][$arPropList["ID"]]['VALUE']."</strong>";
							}else {
								
							}
							//echo $arResult['LOT']['PROPERTY'][$arPropList["ID"]]['VALUE'];
						}else {
							$result .= '<textarea class="form-control" name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $propValue . '</textarea>';
						}
						break;
					case "D":
						if ($i > 0 || $arResult["LOT"]["ID"] > 0) {
							$arPropList["DEFAULT_VALUE"] = "";
						}
						if ($arResult["LOT"]["ID"] > 0 && $i < $cntProp) {
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
				if(($arResult['END_ANALIZ'] == 'N') || ($arResult['END_ANALIZ'] == 'Y' && $arResult['RIGHT'] == 'W' && $arResult['LOT']['ARCHIVE'] != 'Y')){
					$result .= '<br /><input type="button" value="Еще файлы?" onclick="addNewElem(' . $arPropList["ID"] . ', ' . $cntProp . ');" />';
				}
			}
			echo $result;
			?>
</div>
<?endif;?>
<??>
<?if($arPropList['ID'] != 8):?>
<hr>
<?endif;?>

		<?endif;?>

		<?endforeach;?>
		<?/*if ($arResult['END_ANALIZ'] == 'N' || ($arResult['END_ANALIZ'] == 'Y' && $arResult['RIGHT'] == 'W' && $arResult['LOT']['ARCHIVE'] != 'Y')):?>
		<input style="float:right;" class="btn btn-primary" name="lotadd_prop" value="Сохранить" type="submit" />
		<?endif;*/?>
			</form>
					<?//endif;?>
				
			<?endif;?>
		</div>
	</div>
	<? 	if($arResult["LOT"]["FAIL"] == "Y") { ?>
	<div class="row">
		<div class="col">
			<div class="t_lot_fail alert alert-danger"><i class="fa fa-ban"></i>&nbsp;Лот признан несостоявшимся</div>
		</div>
	</div>
	<? } elseif(count($arResult["WIN"])>0) { ?>
		<div class="t_lot_win alert alert-success"><i class="fa fa-flag"></i>&nbsp;Победитель выбран</div>
	<? }?>	
	<?if(($arResult['LOT_END'] == 'N' && $arResult['RIGHT'] == "W") || $arResult['LOT_END'] == 'Y'): ?>
		<? if ($arResult["TYPE_ID"] != "S" && $arResult["TYPE_ID"] != "R"): ?>
	<div style="overflow-x:auto !important; overflow-y:hidden !important;">
		<div class="t_prov">
			<a href="/tx_files/proposal_export.php?LOT_ID=<?= $_REQUEST["LOT_ID"] ?>"><i class="fa fa-file-excel-o" style="font-size: 20px;"></i> Экспортировать в Excel</a><br/>
			<table class="table t_proposal_table">
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<? foreach ($arResult["PROPOSAL"] as $id_prop => $prop): ?>
						<td colspan="<?if($arResult['LOT']['WITH_NDS'] != 'Y'):?>4<?else:?>4<?endif;?>"><?= $prop["USER_INFO"]["NAME_COMPANY"] ?></td>
					<? endforeach; ?>
				</tr>
				<tr>
					<td>№</td>
					<td>Наименование позиции</td>
					<td>Кол-во</td>
					<td>Описание</td>
					<?if($arResult['LOT']['WITH_NDS'] != 'Y' /*|| $arResult['LOT']['WITH_NDS'] == 'Y'*/):?>
						<td>Минимальная цена по позиции без НДС</td>
						<? foreach ($arResult["PROPOSAL"] as $id_prop => $prop): ?>
							<td>Цена за ед. без НДС</td>
							<td>Сумма без НДС</td>
							<td>НДС</td>
							<td>Сумма с НДС</td>
						<? endforeach; ?>
					<?else:?>
						<td>Минимальная цена по позиции c НДС</td>
						<? foreach ($arResult["PROPOSAL"] as $id_prop => $prop): ?>
							<td>Цена за ед. с НДС</td>
							<td>Сумма без НДС</td>
							<td>НДС</td>
							<td>Сумма c НДС</td>
						<? endforeach; ?>
					<?endif;?>
				</tr>
				<? $i = 1; ?>
				<? $best_price_itogo = 0;?>

				<? foreach ($arResult["SPEC2"]["TOVAR"] as $id_tov => $tov): ?>
					<?
					$arPP = array();
					foreach ($arResult["SPEC2"]["PRICE"][$id_tov] as $pp) {
						if ($pp > 0)
							$arPP[] = $pp;
					}
					
					$arResult["SPEC2"]["PRICE"][$id_tov] = $arPP;
					$best_price = $arResult["TYPE_ID"] != "P" ? min($arResult["SPEC2"]["PRICE"][$id_tov]) : max($arResult["SPEC2"]["PRICE"][$id_tov]); ?>
					<tr>
						<td><?= $i ?></td>
						<td><?= $tov["TITLE"] ?></td>
						<td><?= $tov["COUNT"] ?></td>
						<td><?= $tov["ADD_INFO"] ?></td>
						<td style="white-space:nowrap;"><? echo number_format($best_price, 2, '.', ' '); ?><?$best_price_itogo = $best_price_itogo + $best_price?></td>
						<?$itogowithnds = 0;?>
						<? foreach ($arResult["SPEC2"][$id_tov] as $spec): ?>
							<?
								//print_r($spec);
							if ($spec["PRICE_NDS"] == $best_price) $cls = " style='background-color:#CCFFCC;'";
							else $cls = "";
							?>
							<td<?= $cls ?> style="white-space:nowrap;">
								<?
								//=$spec["PRICE_NDS"];
								if ($spec["PRICE_NDS"] == 0) {
									echo "--";
								} else {
									echo number_format($spec["PRICE_NDS"], 2, '.', ' ');
								}
								?>
							</td>
							<td<?= $cls ?> style="white-space:nowrap;">
								<?
								//=$spec["PRICE_NDS"]*$spec["COUNT"];
//								if ($spec["PRICE_NDS"] == 0) {
//									echo "--";
//								} else {
									$withnds = $spec["PRICE_NDS"] * $spec["COUNT"];
//									echo number_format($withnds, 2, '.', ' ');
//								}
								?>

								<? if($arResult['LOT']['WITH_NDS'] == 'Y') { ?>

								<?if($spec["NDS"] == 0):
									echo number_format($withnds, 2, '.', ' ');;
								elseif($spec["NDS"] == 18):
									$withnds = $withnds / 1.18;
									echo number_format($withnds, 2, '.', ' ');
								elseif($spec["NDS"] == 10):
									$withnds = $withnds / 1.10;
									echo number_format($withnds, 2, '.', ' ');
								endif;?>

								<? } else {
									echo number_format($withnds, 2, '.', ' ');
								} ?>

							</td>
							<td>
								<?=$spec["NDS"]?>
							</td>
							<td style="white-space:nowrap;">
								<?if($spec["NDS"] == 0):
										echo number_format($withnds, 2, '.', ' ');;
										$itogowithnds = $withnds;
										$nds = 0;
									elseif($spec["NDS"] == 18):
										$withnds = $withnds * 1.18;
										echo number_format($withnds, 2, '.', ' ');
										$itogowithnds = $withnds;
										$nds =  1.18;
									elseif($spec["NDS"] == 10):
										$withnds = $withnds * 1.10;
										echo number_format($withnds, 2, '.', ' ');
										$itogowithnds = $withnds;
										$nds = 1.10;
									endif;?>
							</td>

						<? endforeach; ?>
					</tr>
					<? $i++; ?>
				<? endforeach; ?>
				<tr>
					<td colspan="5" align="right">
						<strong>ИТОГО:</strong>
					</td>
					<!--td style="white-space:nowrap;">
						<? //echo number_format($best_price_itogo, 2, '.', ' ');?>
					</td-->
					<?foreach ($arResult["PROPOSAL"] as $proposal):?>
						<td style="white-space:nowrap;">
							<?$spec_itogo = 0;?>
							<?foreach ($proposal["SPEC"] as $spec): ?>
								<?$spec_itogo = $spec_itogo + $spec['PRICE_NDS'];?>
							<?endforeach;?>
							<? //echo number_format(($spec_itogo / floatval($arResult["ARRCUR"][$arResult["LOT"]["CURRENCY"]])), 2, '.', ' ');?>
						</td>
						<td style="white-space:nowrap;">
							<?
								if($nds == 0) { $sum_itogo = $proposal["ITOGO"]; }
								else {
									if ($arResult['LOT']['WITH_NDS'] == 'Y') {
										$sum_itogo = $proposal["ITOGO"] / $nds;
									} else {
										$sum_itogo = $proposal["ITOGO"];
									}
								}
							?>
							<? echo number_format($sum_itogo, 2, '.', ' ');?>
						</td>
						<?if($arResult['LOT']['WITH_NDS'] != 'Y' || $arResult['LOT']['WITH_NDS'] == 'Y'):?>
						<td>
							
						</td>
						<td style="white-space:nowrap;">
							<? if ($arResult['LOT']['WITH_NDS'] == 'Y') { ?>
							<? echo number_format($proposal["ITOGO"], 2, '.', ' ');?>
							<? } else {
								echo number_format(($nds > 0 ? $proposal["ITOGO"] * (($spec["NDS"]+100)/100) : $proposal["ITOGO"]), 2, '.', ' ');
							} ?>
						</td>
						<?endif;?>
					<?endforeach;?>
				</tr>
			</table>
		</div>
	</div>
	<? endif; ?>
	<? //endif; ?>


	<?endif;?>

<?endif;?>
<?//окончание всего else?>



