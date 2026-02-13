<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<? if (!empty($arResult["LOTS"])):
	
	if (CSite::InGroup(array(1,6,8))):
		// $arResult["S_RIGHT"] = "W";
		// $arResult["T_RIGHT"] = "S";
	endif;?>
	<div class="row">
		<div class="col">
			<table class="t_lot_table table table-hover table-condensed">
				<tfoot>
					<tr>
						<td colspan="6">
							<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
								<?= $arResult["NAV_STRING"] ?>
							<? endif; ?>
						</td>
					</tr>
				</tfoot>
				<thead class="">
				<tr>
					<th colspan="2" class="t_lot_table_th_gray">
						Номер
						<a href="<?= $arResult["CURR_URL"] ?>SORT_BY=ID&SORT_ORDER=ASC" class="navy">&#9650;</a>
						<a href="<?= $arResult["CURR_URL"] ?>SORT_BY=ID&SORT_ORDER=DESC" class="navy">&#9660;</a>
					</th>
					<th class="t_lot_table_th_gray">
						Наименование
					</th>
					<th class="t_lot_table_th_gray">
						Регион
					</th>
					<th class="t_lot_table_th_gray">
						Компания
					</th>
					<th class="t_lot_table_th_gray"><nobr>
						Ставок
					</th>
					<th class="t_lot_table_th_navy">
					<nobr>
						Окончание
						<a href="<?= $arResult["CURR_URL"] ?>SORT_BY=DATE_END&SORT_ORDER=ASC">&#9650;</a>
						<a href="<?= $arResult["CURR_URL"] ?>SORT_BY=DATE_END&SORT_ORDER=DESC">&#9660;</a></nobr>
					</th>
					
					<th class="t_lot_table_th_navy"><nobr>
						Объем лота
					</th>
				</tr>
				</thead>
				<tbody>
					<? foreach ($arResult["LOTS"] as $arLots): ?>
					<? 
						/* echo '<pre>';
						print_r($arLots);
						echo '</pre>'; */
					?>
					<?
					$date_start = strtotime($arLots["DATE_START"]);
					$date_end = strtotime($arLots["DATE_END"]);
					$date_tek = time();
					?>
						<tr<? if ($arLots["ARCHIVE"] == "Y"): echo ' class="warning"'; endif; ?>>
							<td style="text-align:center;">
								<? 	if($arLots["ACTIVE"] == "Y") { ?>
										<?if ($arLots["PRIVATE"] == "Y"):?>
											<i class="t_lot_status t_lot_status_private fa fa-users" title="Закрытый лот" data-toggle="tooltip" data-placement="left"></i><br>
										<?endif;?>
										<?if ($arLots["ARCHIVE"] == "Y"){?>
											<i class="t_lot_status t_lot_status_archive fa fa-archive" title="Архивный лот" data-toggle="tooltip" data-placement="left"></i>
										<?}elseif ($arLots["END_LOT"] == "Y") {?>
											<i class="t_lot_status t_lot_status_end fa fa-lock" title="Завершенный лот" data-toggle="tooltip" data-placement="left"></i><br>
										<?}else{?>
											<i class="t_lot_status t_lot_status_active fa fa-check-circle" title="Активный лот" data-toggle="tooltip" data-placement="left"></i>
										<? }
									} else { ?>
									<?if ($arLots["PRIVATE"] == "Y"):?>
										<i class="t_lot_status t_lot_status_private fa fa-users" title="Закрытый лот" data-toggle="tooltip" data-placement="left"></i><br>
									<?endif;?>
										<i class="t_lot_status t_lot_status_saved fa fa-save" title="Сохраненный лот" data-toggle="tooltip" data-placement="left"></i>
								<?}?>
							</td>
							<td>
								<a class="t_lot_id">№&nbsp;<?=$arLots["ID"];?></a>
							</td>
							<td>
								<?if ((($arResult["T_RIGHT"] == "P") && (($arResult["S_RIGHT"]) == "A")) ):?>
									<a class="t_lot_title <?if($arLots["ACTIVE"] == "N") echo "mute";?>" href="/user/profile.php"><?= $arLots["TITLE"] ?></a>
								<?else:?>
									<a class="t_lot_title <?if($arLots["ACTIVE"] == "N") echo "mute";?>" href="<?if($arLots["ACTIVE"] == "N"):?>lot.php?ID=<?= $arLots["ID"] ?><?else:?><?= $arLots["DETAIL_URL"]; ?><?endif;?>"><?= $arLots["TITLE"] ?></a>								
								<?endif?>
								<? if(isset($arLots["WIN"])):?>
									<span class="t_lot_win_span">
										Победитель выбран
									</span>
								<? endif; ?>
								<br>								
								<span><?=$arLots["SECTION"]?></span>
							</td>
							<td class="">
								<span><?=$arLots["TERM_DELIVERY_VAL"]?></span></br>
								<?if ($arResult["T_RIGHT"] == "P" && $arResult["S_RIGHT"] == "A") {
									
									switch($arResult["SUPPLIER_STATUS"]["ID"]) {
										case 1: ?>
										<div class="t_lot_status_message">
											<span><a href="/user/profile.php">Для доступа к лоту необходимо заполнить профиль</a></span>
										</div>
										<?
										break;
										case 2: ?>
										<div class="t_lot_status_message">
											<span><a href="/user/profile.php">Вы не можете принимать участие в торгах</a></span>
										</div>
										<?
										break;
										case 3...4:
										break;
										case 5:
										break;
										default:
										break;
									}
								} ?>
							</td>
							<td class="">
								<span class="t_lot_company"><?= $arLots["COMPANY"] ?></span>
							</td>
							<td>
								<span class="t_lot_proprosal"><?=isset($arLots["PROPOSAL"])?$arLots["PROPOSAL"]: $arLots["~PROPOSAL"] ?></span>
							</td>
							<td class="hidden-xs t_lot_table_th_gray">
								<?
									
									$oNow = strtotime(date('Y-m-d'));
									$oNowTime = strtotime(date('Y-m-d H:i:s'));
									
									$oEnd = strtotime($arLots["DATE_END"]);
									
									$oDiff = $oEnd - $oNow;
									$oDiffTime = $oEnd - $oNowTime;
									
									$oDaysLeft = floor($oDiff/(60*60*24));
									
									if ($oDaysLeft < 0 || $oDiffTime <=0) 
										$oDaysLeftString = "Лот завершен";
									else 
										$oDaysLeftString =  "Осталось " . $oDaysLeft . " дней";
								?>
								<label class="t_lot_time_left"><?=$oDaysLeftString;?></label>
								<span class="t_lot_end_date">
									
									<nobr><?= $arLots["DATE_END"] ?></nobr>
								</span>
								
							</td>
							
							<td class="t_lot_table_th_gray">
								<span class="">
									<nobr><b><? if ($arLots["TOTAL_SUM"] == 0):?>
									Не установлен
									<?else:?>
									<?=CurrencyFormat($arLots["TOTAL_SUM"], $arLots["CURRENCY"]);?>
									<?endif;?></b>
									</nobr>
								</span>
							</td>										
						</tr>
					<? endforeach; ?>
				</tbody>
			</table>
		</div>
		<? else: ?>
			<div class="well" style="min-height: px;">
			<h4><?= GetMessage("PW_TD_LOT_NOT_FOUND") ?></h4>
			</div>
		<? endif; ?>
		<script>
			function send_ajax(lot_id, params) {
				$.ajax({
					url: "/bitrix/components/pweb.tenderix/proposal.add/templates/tx_proposal_add/ajax.php",
					type: "POST",
					data: params,
					success: function (data) {
						if(data == 1) {
							$('#proposal_request_'+lot_id).val('Доступ к лоту запрошен');
							$('#proposal_request_'+lot_id).prop( "disabled", true );
						} else {
							UI.message({
								text: data,
								timer: 6000,
								veil: true
							});
							$('#proposal_request_'+lot_id).val('Доступ к лоту запрошен');
							$('#proposal_request_'+lot_id).prop( "disabled", true );
						}
					}
				});
			}
		</script>
	</div>
