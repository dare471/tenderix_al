<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);



global $USER;

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
$arResult["T_RIGHT"] = $T_RIGHT;
if($T_RIGHT == 'D') {
	$T_RIGHT = 'S';
}
//echo "<pre>";print_r($arResult);echo "</pre>";

if ($T_RIGHT < "W") {
	LocalRedirect("/");
}

?>
<script>
	function userView(id) {
      $(".user_view").colorbox({
          inline:true, 
          href:"#user_"+id,
          opacity: 0.5,
          maxWidth:"80%", 
          maxHeight:"80%",
          top: "10%"
      });
      return false;
}
</script>

<div class="space-bottom"></div>
<div class="col">

<? if(!isset($_REQUEST["LOT_ID"])) { ?>
	<div class="supplier_status_filter btn-group">
		<button type="button" class="btn btn-default btn_status <? echo ($_REQUEST['status'] == 0) ? 'active' : '' ?>" id="btn_0" rel="0">Все</button>
		<? foreach ($arResult["STATUS_ARRAY"] as $status_arr) { ?>
			<button type="button" class="btn btn-default btn_status <? echo ($_REQUEST['status'] == $status_arr["ID"]) ? 'active' : '' ?>" id="btn_<?=$status_arr["ID"]; ?>" rel="<?=$status_arr["ID"]; ?>"><?=$status_arr["TITLE"]; ?></button>
		<? } ?>
	</div>
<? } ?>

<script>
	$('.btn_status').click( function() {
		var status_id = $(this).attr("rel");

		document.location.href="?status="+status_id;

		//var tbl_content = $("#supplier_table").html();

		/*$.ajax({
		 url: "<?= $templateFolder ?>/ajax.php",
		 type: "POST",
		 data: "statusId=" + status_id + "&type=filter_status",
		 success: function (data) {

		 }
		 });*/
	});

</script>

<div class="debug"></div>

<? if(isset($_REQUEST["LOT_ID"]) && ($_REQUEST["LOT_ID"] != '')) { ?>
	<h3>Запросы на доступ к подаче предложений</h3>
<? } ?>

<div id="supplier_table">
<table style="margin-top:30px;" class="t_supplier_table table table-hover table-condensed">
	<thead>
		<tr>
			<!--th width="40">п/п</th-->
			<th>ФИО поставщика</th>
			<th>Компания</th>
			<th>Статус поставщика</th>
			<th></th>
		</tr>	
	</thead>
	<tbody>
	<? if(!empty($arResult["SUPPLIERS"])) { ?>
<?
foreach ($arResult["SUPPLIERS"] as $key => $arSupplier):?>

		<tr>
			<!--td>
				<? //= $key+1; ?>
			</td-->
			<td>
				<?= $arSupplier['NAME'];?></a><br>
				<a href="mailto:<?= $arSupplier['EMAIL'];?>"><?= $arSupplier['EMAIL'];?>
			</td>
			<td>
				<a class="user_view" href="#"
				onclick="userView(<?= $arSupplier['ID']?>)"><?= $arSupplier['NAME_COMPANY'];?></a><br>

			<div style="display:none">
				<div id="user_<?= $arSupplier['ID'] ?>">
					<table class="table t_lot_table">
						<tbody>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_FIO") ?></td>
							<td><?= $arSupplier["ALL"]["FIO"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_NAME_COMPANY") ?></td>
							<td><?= $arSupplier["ALL"]["NAME_COMPANY"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_NAME_DIRECTOR") ?></td>
							<td><?= $arSupplier["ALL"]["NAME_DIRECTOR"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_NAME_ACCOUNTANT") ?></td>
							<td><?= $arSupplier["ALL"]["NAME_ACCOUNTANT"] ?></td>
						</tr>
						<tr>
							<td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_CODE") ?></b></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_CODE_INN") ?></td>
							<td><?= $arSupplier["ALL"]["CODE_INN"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_CODE_KPP") ?></td>
							<td><?= $arSupplier["ALL"]["CODE_KPP"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_CODE_OKVED") ?></td>
							<td><?= $arSupplier["ALL"]["CODE_OKVED"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_CODE_OKPO") ?></td>
							<td><?= $arSupplier["ALL"]["CODE_OKPO"] ?></td>
						</tr>
						<tr>
							<td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_LEGALADDRESS") ?></b></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_REGION") ?></td>
							<td><?= $arSupplier["ALL"]["LEGALADDRESS_REGION"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_CITY") ?></td>
							<td><?= $arSupplier["ALL"]["LEGALADDRESS_CITY"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_INDEX") ?></td>
							<td><?= $arSupplier["ALL"]["LEGALADDRESS_INDEX"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_STREET") ?></td>
							<td><?= $arSupplier["ALL"]["LEGALADDRESS_STREET"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_POST") ?></td>
							<td><?= $arSupplier["ALL"]["LEGALADDRESS_POST"] ?></td>
						</tr>
						<tr>
							<td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_POSTALADDRESS") ?></b></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_REGION") ?></td>
							<td><?= $arSupplier["ALL"]["POSTALADDRESS_REGION"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_CITY") ?></td>
							<td><?= $arSupplier["ALL"]["POSTALADDRESS_CITY"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_INDEX") ?></td>
							<td><?= $arSupplier["ALL"]["POSTALADDRESS_INDEX"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_STREET") ?></td>
							<td><?= $arSupplier["ALL"]["POSTALADDRESS_STREET"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_POST") ?></td>
							<td><?= $arSupplier["ALL"]["POSTALADDRESS_POST"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_PHONE") ?></td>
							<td><?= $arSupplier["ALL"]["PHONE"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_FAX") ?></td>
							<td><?= $arSupplier["ALL"]["FAX"] ?></td>
						</tr>
						<tr>
							<td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_STATEREG") ?></b></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_STATEREG_PLACE") ?></td>
							<td><?= $arSupplier["ALL"]["STATEREG_PLACE"] ?></td>
						</tr>
						
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_STATEREG_DATE") ?></td>
							<td><?= $arSupplier["ALL"]["STATEREG_DATE"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_STATEREG_OGRN") ?></td>
							<td><?= $arSupplier["ALL"]["STATEREG_OGRN"] ?></td>
						</tr>
						<tr>
							<td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_BANK") ?></b></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_BANKING_NAME") ?></td>
							<td><?= $arSupplier["ALL"]["BANKING_NAME"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNT") ?></td>
							<td><?= $arSupplier["ALL"]["BANKING_ACCOUNT"] ?></td>
						</tr>
						<tr class="odd">
							<td><?= GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNTCORR") ?></td>
							<td><?= $arSupplier["ALL"]["BANKING_ACCOUNTCORR"] ?></td>
						</tr>
						<tr>
							<td><?= GetMessage("PW_TD_SUPPLIER_BANKING_BIK") ?></td>
							<td><?= $arSupplier["ALL"]["BANKING_BIK"] ?></td>
						</tr>
						<tr>
							<td>Файлы:</td>
							<td>
								<?foreach ($arSupplier['FILES'] as $files) {?>
									<a href="/tx_files/supplier_file.php?USER_ID=<? echo $arSupplier['ID'] ?>&amp;FILE_ID=<? echo $files["ID"] ?>"><? echo $files["ORIGINAL_NAME"] ?></a>
								<?}?>
							</td>
						</tr>
						
						
						<? if ($arSupplier["ALL"]["PROP"]): ?>
							<tr>
								<td colspan="2"><b>Дополнительные поля:</b></td>
							</tr>
							<? foreach ($arSupplier["PROP"] as $arProp): ?>
								<? if ($arSupplier["PROP_S"][$arProp["ID"]]): ?>
									<tr>
										<td><?= $arProp["TITLE"] ?></td>
										<?
										if ($arProp["PROPERTY_TYPE"] == "L") {
											$arPropDef = unserialize(base64_decode($arProp["DEFAULT_VALUE"]));
										}
										$rsPropSupp = array();
										$arFile = array();
										foreach ($arRes["PROP_S"][$arProp["ID"]] as $arPropSupp) {
											//__($arPropSupp);
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
							<tr>
								<td><b>Направления работы</b></td>
								<td>
									<?php foreach ($arSupplier['SUBS'] as $subscribe): ?>
										<i class="fa fa-check-square"></i>&nbsp;&nbsp;&nbsp;<?=$subscribe;?><br>
									<?php endforeach ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
				<strong>ИНН:</strong> <?= $arSupplier['ALL']['CODE_INN'];?>
			</td>
			<td>
				<? //print_r($arSupplier['STATUS_ID']); ?>

				<? if($arSupplier['T_RIGHT'] == 'W') { ?>
					<? if(empty($arSupplier['LOT_ACCESS'])) { ?>
						<?
							echo SelectBox("SUPPLIER_STATUS_".$arSupplier['ID'], CTenderixUserSupplierStatus::GetDropDownList_forSupplierList(), "", $arSupplier['STATUS_ID']);
						?>
						<input type="hidden" name="supplier_id" id="supplierId_<?=$arSupplier['ID'];?>" value="<?=$arSupplier['ID'];?>" />
						<script>
							$('select[name="SUPPLIER_STATUS_<?=$arSupplier['ID'];?>"]').change(function() {
								var status_id = $(this).val();
								var suppl_id = $("#supplierId_<?=$arSupplier['ID'];?>").val();
								var params = "statusId=" + status_id + "&supplierId=" + suppl_id;
								var type = "update_status";
								send_ajax(suppl_id, params, type);
							});
						</script>
					<? } else { ?>
							<input type="button" name="access_confirm" id="access_confirm_<?=$arSupplier['ID'];?>" value="Разрешить доступ" />
							<input type="hidden" name="supplier_id" id="supplierId_<?=$arSupplier['ID'];?>" value="<?=$arSupplier['ID'];?>" />
							<input type="hidden" name="lot_id" id="lotId_<?=$arSupplier['ID'];?>" value="<?=$_REQUEST["LOT_ID"];?>" />
							<script>
								$('#access_confirm_<?=$arSupplier['ID'];?>').click( function() {
									var lot_id = $("#lotId_<?=$arSupplier['ID'];?>").val();
									var suppl_id = $("#supplierId_<?=$arSupplier['ID'];?>").val();
									var params = "supplId=" + suppl_id + "&lotId=" + lot_id;
									var type = "confirm_access";
									send_ajax(suppl_id, params, type);
								});
							</script>
						<? } ?>
					<? } else { ?>
					<br/>
						<?= $arSupplier['STATUS_NAME'];?>
					<? } ?>

			</td>
			
		</tr>
<?
endforeach?>
	<? } else { ?>
		<tr>
			<? if(isset($_REQUEST["LOT_ID"]) && ($_REQUEST["LOT_ID"] != '')) { ?>
				<td colspan="5" align="center">Нет запросов на доступ к этому лоту.</td>
			<? } else { ?>
				<td colspan="5" align="center">Список поставщиков пуст.</td>
			<? } ?>
		</tr>
	<? } ?>
	</tbody>
</table>

	<?= $arResult["NAV_STRING"] ?>
</div>
</div>

<script>
	function send_ajax(suppl_id, params, type) {
		$.ajax({
			url: "<?= $templateFolder ?>/ajax.php",
			type: "POST",
			data: params + "&type="+type,
			success: function (data) {
				if(type == "confirm_access") {
					if (data == 1) {
						$('#access_confirm_'+suppl_id).val('Доступ разрешён');
						$('#access_confirm_'+suppl_id).prop("disabled", true);
					} else {
						UI.message({
							text: data,
							timer: 1000,
							veil: true
						});
					}
				} else if(type == "update_status") {
					UI.message({
						text: data,
						timer: 1000,
						veil: true
					});
				}
			}
		});
	}

</script>

<?//echo "<pre>";print_r($arResult);echo "</pre>";?>
