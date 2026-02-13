<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
	
?>

	<?if($arResult['LOT']['NOTVISIBLE_PROPOSAL'] == 'Y' && $arResult['RIGHT'] != "P" && $arResult['LOT_END'] == 'N') {
		echo '<div class="alert alert-info"><h5> Лот еще не завершен!</h5></div>';
		return false;
	} ?>
	

	<?/*if($arResult['SECURITY'] != 'N' && !CSite::InGroup(array(7))):?>
		<h3>Ожидаем ответа службы безопасности</h3>
	<?else:*/?>
	<?// Если безопасники проверили?>

	
<?if($arResult["OWNER"] == "Y" || $arResult['RIGHT'] == "P"):?>
	<?
	if (!isset($arResult["PROPOSAL"])) {
		echo '<div class="alert alert-info mt-2"><h5 style="text-align: center;"> Предложения по лоту не подавались</h5></div>';
		return false;
	}
	?>
	<? 	if($arResult["LOT"]["FAIL"] == "Y") { ?>
	<div class="row">
		<div class="col">
			<div class="t_lot_fail alert alert-danger mt-2"><i class="fa fa-ban"></i>&nbsp;Лот признан несостоявшимся</div>
		</div>
	</div>
	<? } elseif(count($arResult["WIN"])>0) { ?>
		<div class="t_lot_win alert alert-success mt-2"><i class="fa fa-flag"></i>&nbsp;Победитель выбран</div>
	<? }?>	
	<div style="overflow-x:auto !important; overflow-y:hidden !important;">
	<div class="t_prov">
	<a name="proposal_table"></a>

	[<?= GetMessage("PW_TD_CURRENCY") ?>: <?= $arResult["LOT"]["CURRENCY"] ?>] <br/>

	<form name="win_add" action="<?= POST_FORM_ACTION_URI ?>#proposal_table" method="post" enctype="multipart/form-data">
		<table class="table t_proposal_table">
			<tr>
				<? if (($arResult['LOT']['PROPERTY'][8]['VALUE']== 1 && $arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y" && count($arResult["WIN"]) == 0) || ($arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y")): ?>
					<th><?= GetMessage("PW_TD_WINNER") ?></th>
				<? endif; ?>
				<th><?= GetMessage("PW_TD_SUPPLIER") ?></th>
				<?//Цены вскрываются по закрытию лота
				// if(($arResult['LOT_END'] == 'N' && $arResult['RIGHT'] == "W") || $arResult['LOT_END'] == 'Y'): 
				// Цены вскрываются уже непосредственно во время проведения лота
				?>			
					<? if ($arParams["NDS_TYPE"] != "N"): ?>
						<th><?= GetMessage("PW_TD_ITOGO_N") ?></th>
						<th><?= GetMessage("PW_TD_ITOGO") ?></th>
					<? else: ?>
						<th><?= GetMessage("PW_TD_ITOGO_N") ?></th>
						<th><?= GetMessage("PW_TD_ITOGO") ?></th>
					<? endif; ?>
					
					<th><?= GetMessage("PW_TD_SPEC") ?></th>
				<?//endif;?>
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
			?>
		
		<? 
		$i = 0;
		foreach ($arResult["PROPOSAL"] as $idProp => $vProp): ?>
		<?
		$i++;
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
			<? 
			/* echo '<pre>';
			print_r($arResult["PROPOSAL_USER"]);   */
			$ID = $USER->getId();
			//
			if ($vProp["USER_ID"] == $ID) {?>
				<a class="user_view" href="#"><?= strlen($vProp["USER_INFO"]["NAME_COMPANY"]) > 0 ? $vProp["USER_INFO"]["NAME_COMPANY"] : $vProp["USER_INFO"]["FIO"] ?></a>
			<? } 
			else { 
				$supplier_description = GetMessage("PW_TD_SUPPLIER") . " " . $i;
			?>
				<a class="user_view" href="#"><?= $supplier_description?></a>
			<?}?>
			<?
			echo "<br />" . $vProp["DATE_START"];
			?>
		</td>

		<?//if(($arResult['LOT_END'] == 'N' && $arResult['RIGHT'] == "W") || $arResult['LOT_END'] == 'Y'): ?>
		<td <? if ($itogo == $bestpr) {
			echo " style='background-color:#CCFFCC;'";
		} ?>>

			<nobr><?= ($itogo > 0 && $arResult["LOT"]["WITH_NDS"] != "Y") ? number_format($itogo_n, 2, '.', ' ') : '--' ?></nobr>
			<? if ((strlen($checked) > 0) || $itogo == $bestpr): ?>
				<i class="fa fa-flag" style="color:red;font-size: 16px;"></i>
				<!--img src="/bitrix/components/pweb.tenderix/proposal.list/templates/.default/images/best-price.png"/-->
			<? endif; ?>
		</td>
		<td<? if ($itogo == $bestpr) {
			echo " style='background-color:#CCFFCC;'";
		} ?>>
			<nobr><?= ($itogo > 0 && $arResult["LOT"]["WITH_NDS"] == "Y") ? number_format($itogo, 2, '.', ' ') : '--' ?></nobr>
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
						<table class="table t_lot_table">
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
											// Если валюта KZT, показываем как есть (уже в нужном формате)
											
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
		<?//endif; ?>
		<? //товары?>
		<? if ($arResult["TYPE_ID"] == "S" || $arResult["TYPE_ID"] == "R"): ?>
			<td align="center">
				<a href="#" class="tovar_view" onclick="tovarView(<?= $vProp["ID"] ?>)">
					<i class="fa fa-table" style="color:#d46e37;font-size: 20px;"></i>
				</a>

				<div style="display:none">
					<div id="tovar_table_<?= $vProp["ID"] ?>">
						<table class="t_lot_table">
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
										// Логика для заголовка столбца цены в зависимости от типа НДС лота
										if ($arResult['LOT']['WITH_NDS'] == 'Y') {
											echo "Цена за ед. с НДС";
										} else {
											echo "Цена за ед. без НДС";
										}
										?>
										</th>
										<th><?= GetMessage("PW_TD_SPEC_PRICE_NDS_SUM") ?></th>
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
										//= number_format($specProp["PRICE_NDS"], 2, '.', ' ')
										if ($tovarProp["PRICE_NDS"] == 0) {
											echo "--";
										} else {
											echo number_format($tovarProp["PRICE_NDS"], 2, '.', ' ');
										}
										?>
									</td>
									<?
									$sum_item = floatval($tovarProp["PRICE_NDS"]) * floatval($tovarProp["COUNT"]);
									$itogo_sum_item += $sum_item;
									?>
									<td align="center">
										<?
										//= number_format($sum_item, 2, '.', ' ')
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
									<table class="t_lot_table">
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

	
	<? //endif; ?>




<?endif;?>
<?//окончание всего else?>



