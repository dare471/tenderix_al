<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<? if (!empty($arResult["LOTS"])):
	
	if (CSite::InGroup(array(1,6,8))):
		// $arResult["S_RIGHT"] = "W";
		// $arResult["T_RIGHT"] = "S";
	endif;?>

<? //__($arResult); ?>
	<nobr><i class="fa fa-check-circle" style="font-size:16px;color:#5aab00;"></i>&nbsp;- <?= GetMessage("PW_TD_LEGEND_ACTIVE") ?></nobr>&nbsp;
	<nobr><i class="fa fa-lock" style="font-size:16px;color:#5aab00;"></i>&nbsp;- Завершенный лот</nobr>&nbsp;
	<nobr><i class="fa fa-users" style="font-size:16px;color:#5aab00;"></i>&nbsp;- Закрытый лот</nobr>&nbsp;
	<nobr><i class="fa fa-archive" style="font-size:16px;color:#aaacb2;"></i>&nbsp;- <?= GetMessage("PW_TD_LEGEND_ARCHIVE") ?></nobr>&nbsp;
	<nobr><i class="fa fa-save" style="font-size:16px;color:#aaacb2;"></i>&nbsp;- <?= GetMessage("PW_TD_LEGEND_SAVE") ?></nobr>&nbsp;

	<table class="table table-striped table-hover table-condensed">
		<thead>

			<tr>
				<td colspan="6">
					<? if ($arParams["DISPLAY_TOP_PAGER"]): ?>
						<?= $arResult["NAV_STRING"] ?>
					<? endif; ?>
			</td>
		</tr>
		</thead>

		<tfoot>
			<tr>
				<td colspan="6">
					<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
						<?= $arResult["NAV_STRING"] ?>
					<? endif; ?>
				</td>
			</tr>
		</tfoot>

		<tbody>
			<tr>
				<th></th>
				<th>
					<?= GetMessage("PW_TD_SORT_NUM_LOT") ?> 
					<a href="<?= $arResult["CURR_URL"] ?>SORT_BY=ID&SORT_ORDER=ASC">&#9650;</a>
					<a href="<?= $arResult["CURR_URL"] ?>SORT_BY=ID&SORT_ORDER=DESC">&#9660;</a>
				</th>
				<th class="hidden-xs">
					<?= GetMessage("PW_TD_CUSTOMER") ?>
				</th>
				<th class="hidden-xs"><nobr>
					<?= GetMessage("PW_TD_SORT_DATE_START") ?>
					<a href="<?= $arResult["CURR_URL"] ?>SORT_BY=DATE_START&SORT_ORDER=ASC">&#9650;</a>
					<a href="<?= $arResult["CURR_URL"] ?>SORT_BY=DATE_START&SORT_ORDER=DESC">&#9660;</a></nobr>
				</th>
				<th class="hidden-xs"><nobr>
					<?= GetMessage("PW_TD_SORT_TOTAL_SUM") ?>
				</th>
				<th class="hidden-xs">
				<nobr>
					<?= GetMessage("PW_TD_SORT_DATE_END") ?> 
					<a href="<?= $arResult["CURR_URL"] ?>SORT_BY=DATE_END&SORT_ORDER=ASC">&#9650;</a>
					<a href="<?= $arResult["CURR_URL"] ?>SORT_BY=DATE_END&SORT_ORDER=DESC">&#9660;</a></nobr>
				</th>
				<?if ((($USER->IsAuthorized() && ($arResult["T_RIGHT"] == "P") && ($arResult["S_RIGHT"]) == "W")) || ($USER->IsAuthorized() && (($arResult["T_RIGHT"] == "W") || ($arResult["T_RIGHT"] == "S"))) ):?>
				<th class="hidden-xs">
					<?= GetMessage("PW_TD_ACTIONS") ?>
				</th>
				<?	endif;?>
			</tr>
			<? foreach ($arResult["LOTS"] as $arLots): 
						// print_r($arLots["TOTAL_SUM"]); 
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
								<i class="fa fa-users" style="font-size:16px;color:<?if ($arLots["ARCHIVE"] == "Y"){?>#aaacb2;<?}else{?>#5aab00;<?}?>" title="Закрытый лот" data-toggle="tooltip" data-placement="left"></i><br>
							<?endif;?>
								<?if ($arLots["ARCHIVE"] == "Y"){?>
									<i class="fa fa-archive" style="font-size:16px;color:#aaacb2;" title="<?= GetMessage("PW_TD_LEGEND_ARCHIVE") ?>" data-toggle="tooltip" data-placement="left"></i>
								<?}elseif ($arLots["END_LOT"] == "Y") {?>
									<i class="fa fa-lock" style="font-size:16px;color:#5aab00;" title="Завершенный лот" data-toggle="tooltip" data-placement="left"></i><br>
								<?}else{?>
									<i class="fa fa-check-circle" style="font-size:16px;color:#5aab00;" title="<?= GetMessage("PW_TD_LEGEND_ACTIVE") ?>" data-toggle="tooltip" data-placement="left"></i>
								<? }
							} else { ?>
							<?if ($arLots["PRIVATE"] == "Y"):?>
								<i class="fa fa-users" style="font-size:16px;color:#aaacb2;" title="Закрытый лот" data-toggle="tooltip" data-placement="left"></i><br>
							<?endif;?>
								<i class="fa fa-save" style="font-size:16px;color:#aaacb2;" title="<?= GetMessage("PW_TD_LEGEND_SAVE") ?>" data-toggle="tooltip" data-placement="left"></i>
						<?}?>
					</td>
					<td>
						<?if (/*!$USER->IsAuthorized() ||*/ (($arResult["T_RIGHT"] == "P") && (($arResult["S_RIGHT"]) == "A")) ):?>
							<a class="t_lot_title <?if($arLots["ACTIVE"] == "N") echo "mute";?>" href="/profile_supplier.php"><?= $arLots["TITLE"] ?></a>
						<?else:?>
							<a class="t_lot_title <?if($arLots["ACTIVE"] == "N") echo "mute";?>" href="<?if($arLots["ACTIVE"] == "N"):?>lot.php?ID=<?= $arLots["ID"] ?><?else:?><?= $arLots["DETAIL_URL"]; ?><?endif;?>"><?= $arLots["TITLE"] ?></a>
							<?if ($arLots["END_LOT"] == "Y"):?> [Завершен]<?endif;?> <?if(count($arLots['WIN']) > 0):?><span style="color:green;">Победитель выбран</span><?endif;?>
							<?if($arLots["FAIL"] == "Y"):?> <span style="color:red">[Лот не состоялся]</span><?endif;?>
						<?endif?>
						<div class="t_lot_descr">
							<span class="t_lot_id"><b><?= GetMessage("PW_TD_LOT") ?>:</b> <?= $arLots["ID"] ?> <?if($arLots['LINKED'] > 0 && $arResult["S_RIGHT"] != "W"):?>[<a href="/tenders_detail.php?LOT_ID=<?=$arLots['LINKED']?>">Второй этап</a>]<?endif;?></span>
							<?if ($arLots["TYPE_ID"] == "N") :?>
							<span class="label label-primary">АУКЦИОН (покупка товара)</span>
							<?endif;?>
							<?if ($arLots["TYPE_ID"] == "S") :?>
							<span class="label label-primary">Покупка (товар)</span>
							<?endif;?>
							<?if ($arLots["TYPE_ID"] == "P") :?>
							<span class="label label-primary">Аукцион (продажа товара)</span>
							<?endif;?>
							<?if ($arLots["TYPE_ID"] == "T") :?>
							<span class="label label-info">КОНКУРС <?if ($arLots["QUOTES"] == "Y") :?>(Запрос котировок)<?endif;?></span>
							<?endif;?>
							<?if ($arLots["TYPE_ID"] == "R") :?>
							<span class="label label-info">Запрос цен (товар)</span>
							<?endif;?>
							<br /> 
							<span class="t_lot_section"><b><?= GetMessage("PW_TD_LOT_SECTION") ?>:</b> <?= $arLots["SECTION"] ?></span><br />
							<? if ($arLots["OPEN_PRICE"] == "Y"): ?>
								<span class="t_lot_open_price"><b><?= GetMessage("PW_TD_LOT_OPEN_PRICE") ?></b></span><br />
							<? endif; ?>
							<? if ($arLots["PROPOSAL"] > 0): ?>
								<span class="t_lot_proposal"><b><?= GetMessage("PW_TD_LOT_CNT_PROPOSAL") ?>:</b> <?= $arLots["PROPOSAL"] ?></span> <br />
							<? endif; ?>
							<? if ($arResult["T_RIGHT"] >= "S"): ?>
								Создатель: <?=$arResult['BUYERS'][$arLots['BUYER_ID']]['LOGIN']?><br />
							<? endif;?>
							<?if($arLots['PRIVATE'] == 'Y' && !isset($arLots['private_user'])):?>
							<span style="color:red;" >Не добавлены поставщики!</span>
							<?endif;?>

						</div>
						<?if (!$USER->IsAuthorized()):?>
							<div class="t_lot_meta visible-xs">
								<span>Информация о заказчике доступна только зарегистрированным поставщикам!</span><br />
								Необходимо <a data-toggle="modal" data-target="#authModal" href="#">авторизоваться</a> или
								<a href="/profile_supplier.php">зарегистрироваться</a> и заполнить профиль поставщика.
							</div>
						<?elseif($arResult["S_RIGHT"] == "A" && $arResult['T_RIGHT'] == "P"):?>
							<? //if ?>
							<div class="t_lot_meta visible-xs">
								<span>Информация о заказчике доступна при полностью заполненном профиле!</span><br />
								Необходимо <a href="/profile_supplier.php">заполнить профиль</a>.
							</div>
						<?else:?>
							<div class="t_lot_meta visible-xs">
							<span class="t_lot_company"><b><?= GetMessage("PW_TD_LOT_COMPANY") ?>:</b> <?= $arLots["COMPANY"] ?></span><br />
							<span class="t_lot_responsible_fio"><b><?= GetMessage("PW_TD_LOT_RESPONSIBLE_FIO") ?>:</b> <?= $arLots["RESPONSIBLE_FIO"] ?></span><br />
							<span class="t_lot_responsible_phone"><b><?= GetMessage("PW_TD_LOT_RESPONSIBLE_PHONE") ?>:</b> <?= $arLots["RESPONSIBLE_PHONE"] ?></span><br />
							<span class="t_lot_start_date">
								<strong>Дата начала:</strong> <?= $arLots["DATE_START"] ?><br />
							</span>
							<span class="t_lot_end_date">
								<strong>Дата завершения:</strong> <?= $arLots["DATE_END"] ?><br /><br />
							</span>
							<?if (($arResult["T_RIGHT"] == "P") && (($arResult["S_RIGHT"]) == "W")) : ?>
							<span class="t_lot_actions">
								<?  if(!empty($arLots["ACCESS"])) {
										if($arLots["ACCESS"] == "Y") { ?>
											<a class="btn btn-info btn-block btn-sm" href="<?= $arLots["DETAIL_URL"]; ?>"><?= GetMessage("PW_TD_MAKE_AN_OFFER") ?></a><br />
										<? } else { ?>
											<input class="btn btn-primary proposal_request" type="button" name="proposal_request_yet" disabled value="<?= GetMessage("PW_TD_REQUEST_YET") ?>" /><br />
										<? } ?>
								<? } else /*{ ?>
												<input class="btn btn-primary proposal_request" type="button" name="proposal_request" id="proposal_request_<?=$arLots["ID"];?>" rel="<?= $arLots["ID"];?>" value="<?= GetMessage("PW_TD_REQUEST");?>"/><br />
								<input type="hidden" class="user_id" id="userid_<?=$arLots["ID"];?>" value="<?=$USER->GetID();?>"/>
									<script>
										$('#proposal_request_<?=$arLots["ID"];?>').click(function() {
											alert(1);
											var lot_id = ('#proposal_request_<?=$arLots["ID"];?>').attr("rel");
											var user_id = $(".user_id").val();

											var params = "lotId=" + lot_id + "&userId=" + user_id;
											send_ajax(lot_id, params);
										});
									</script>
								<? }*/ ?>
							</span>
							<?endif;?>
							</div>
						<?endif?>
					</td>
					<td class="hidden-xs">
						<?if (!$USER->IsAuthorized()):?>
							<span>Информация о заказчике доступна <br />только зарегистрированным поставщикам!</span><br />
								Необходимо <a data-toggle="modal" data-target="#authModal" href="#">авторизоваться</a> или
								<a href="/profile_supplier.php?register=yes">зарегистрироваться</a><br /> и заполнить профиль поставщика.
						<?elseif($arResult["S_RIGHT"] == "A" && $arResult['T_RIGHT'] == "P") :?>
							<? if($arResult["SUPPLIER_STATUS"]["ID"] == 6) { ?>
								<span>Информация о заказчике станет доступна после того, как Вам будет предоставлен доступ к площадке. <br/>
								Ваш статус: <?=$arResult["SUPPLIER_STATUS"]["TITLE"] ?>.
								</span>
							<? } else { ?>
							<span>Информация о заказчике доступна при полностью заполненном профиле!<br />
								Необходимо <a href="/profile_supplier.php">заполнить профиль</a>.
							<? } ?>
						<? else: ?>
							<span class="t_lot_company"><b><?= GetMessage("PW_TD_LOT_COMPANY") ?>:</b> <?= $arLots["COMPANY"] ?></span><br />
							<span class="t_lot_responsible_fio"><b><?= GetMessage("PW_TD_LOT_RESPONSIBLE_FIO") ?>:</b> <?= $arLots["RESPONSIBLE_FIO"] ?></span><br />
							<span class="t_lot_responsible_phone"><b><?= GetMessage("PW_TD_LOT_RESPONSIBLE_PHONE") ?>:</b> <?= $arLots["RESPONSIBLE_PHONE"] ?></span>
						<?	endif; ?>
					</td>
					<td class="hidden-xs">
						<span class="t_lot_start_date">
							<nobr><?= $arLots["DATE_START"] ?></nobr>
						</span>
					</td>
					<td class="hidden-xs">
						<span class="t_lot_start_date">
							<nobr><b><? if ($arLots["TOTAL_SUM"] == 0):?>
							Не установлен
							<?else:?>
							<?= number_format($arLots["TOTAL_SUM"], 2, '.', ' ');?>
							<?endif;?></b>
							</nobr>
						</span>
					</td>
					<td class="hidden-xs">
						<span class="t_lot_end_date">
							<nobr><?= $arLots["DATE_END"] ?></nobr>
						</span>
					</td>
					<?if ((($USER->IsAuthorized() && ($arResult["T_RIGHT"] == "P") && ($arResult["S_RIGHT"]) == "W")) || ($USER->IsAuthorized() && (($arResult["T_RIGHT"] == "W") || ($arResult["T_RIGHT"] == "S"))) ):?>
						<td class="hidden-xs">
							<span class="t_lot_actions">
								<div>
								<? if ($arResult["T_RIGHT"] == "P"): ?>
										<?	if($arLots["ARCHIVE"] == "Y"):?>
										Лот в архиве.
										<?elseif($arLots["END_LOT"] == "Y"):?>
											Лот завершен
										<?else:?>
											<?  //if(!empty($arLots["ACCESS"])) {
												//if($arLots["ACCESS"] == "Y") { ?>
													<a class="btn btn-info btn-block btn-sm" href="<?= $arLots["DETAIL_URL"]; ?>"><?= GetMessage("PW_TD_MAKE_AN_OFFER") ?></a><br />
												<? /*} else { ?>
													<input class="btn btn-primary proposal_request" type="button" name="proposal_request_yet" disabled value="<?= GetMessage("PW_TD_REQUEST_YET"); ?>" /><br />
												<? } ?>
											<? } else { ?>
												<input class="btn btn-primary proposal_request" type="button" name="proposal_request" id="proposal_request_<?=$arLots["ID"];?>" rel="<?=$arLots["ID"];?>" value="<?= GetMessage("PW_TD_REQUEST");?>"/><br />
												<input type="hidden" class="lot_id" id="lot_id_<?=$arLots["ID"];?>" value="<?=$arLots["ID"];?>"/>
												<input type="hidden" class="user_id" id="user_id_<?=$arLots["ID"];?>" value="<?=$USER->GetID();?>"/>
												<script>
													$('#proposal_request_<?=$arLots["ID"];?>').click(function() {
														var user_id = $(".user_id").val();
														var lot_id = $("#lot_id_<?=$arLots['ID'];?>").val();

														var params = "lotId=" + lot_id + "&userId=" + user_id;
														send_ajax(lot_id, params);
													});
												</script>
											<? } */ ?>
										<?endif;?>
									<? else:?>
										<?	if($arLots["ACTIVE"] == "N"):?>
											<? if ($arResult["T_RIGHT"] == 'W'): ?>
												<a href="lot.php?ID=<?= $arLots["ID"] ?>"><nobr>Опубликовать лот</nobr></a><br />
											<?endif;?>
										<?	endif;?>
										<?if($arLots["ACTIVE"] == "Y" && $arResult["T_RIGHT"] != 'W'):?>
										<?if (( $arResult["T_RIGHT"] != 'W' && ($arLots["ACTIVE"] == "Y") && ($date_tek < $date_end) && ($date_tek > $date_start) && ($date_end != 0) && ($date_start != 0)) || ($arResult["LOT"]["NOEDIT"] == "Y")) :?>
											<span style="color:red;"><nobr>Редактировать запрещено</nobr></span><br />
										<?endif;?>
										<?else:?>
											<?	if($arLots["ARCHIVE"] != "Y"){ ?>
												<? if($arLots["LOT_END"] != "N" || $arLots["ACTIVE"] != "Y") { ?>
												<a href="lot.php?ID=<?= $arLots["ID"] ?>"><nobr>Редактировать</nobr></a><br />
													<? } ?>
											<? } ?>
										<?endif?>
										<a href="lot.php?COPY_ID=<?= $arLots["ID"] ?>"><nobr>Дублировать лот</nobr></a><br />
										<? if ($arResult["T_RIGHT"] == 'W'): ?>
										<!--a href="lot.php?ID=<?= $arLots["ID"] ?>"><nobr>Перенести в архив</nobr></a><br /-->
										<?endif;?>
									<?endif;?>
								</div>
							</span>
						</td>
					<?endif;?>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>
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
