<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

//Цены поставщика
	// print_r('xfgdsgdfgdfssssssssssssssssssssssssssssg');
?>

	
<?if($arResult['RIGHT'] == "W"):?>

	<div style="overflow-x:auto !important; overflow-y:hidden !important;">
	<div class="t_prov">
	<a name="proposal_table"></a>

	<h3><?= GetMessage("PW_TD_LIST_PROPOSAL") ?></h3> [<?= GetMessage("PW_TD_CURRENCY") ?>
	: <?= $arResult["LOT"]["CURRENCY"] ?>] <br/>

	<form name="win_add" action="<?= POST_FORM_ACTION_URI ?>#proposal_table" method="post" enctype="multipart/form-data">
		<table class="table t_lot_table">
			<tr>
				<th><?= GetMessage("PW_TD_NUM") ?></th>
				<th><?= GetMessage("PW_TD_DATE_START") ?></th>
				<th><?= GetMessage("PW_TD_SUPPLIER") ?></th>
				<?if(($arResult['LOT_END'] == 'N' && $arResult['RIGHT'] == "W") || $arResult['LOT_END'] == 'Y'): ?>
					<? if ($arParams["NDS_TYPE"] != "N"): ?>
						<th><?= GetMessage("PW_TD_ITOGO_N") ?></th>
						<th><?= GetMessage("PW_TD_ITOGO") ?></th>
					<? else: ?>
						<th><?= GetMessage("PW_TD_ITOGO_N") ?></th>
						<th><?= GetMessage("PW_TD_ITOGO") ?></th>
					<? endif; ?>
					
					
				<?endif;?>
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
			
			$arHistoryResult = array();
			foreach($arResult["PROPOSAL"] as $id => $val) {
				$res = CTenderixProposal::GetSpecHistory(array("PROPOSAL_ID" => $id));
				
				while($arHistory = $res->Fetch()) {
					//echo '<pre>'; print_r($arHistory);
					// $arResult["PROPOSAL"][$arHistory['PROPOSAL_ID']]["HISTORY"][$arHistory['DATE_START']][$arHistory['PROPERTY_BUYER_ID']] = $arHistory;
					$arHistoryResult[$arHistory['DATE_START']][$arHistory['PROPOSAL_ID']][$arHistory['PROPERTY_BUYER_ID']] = $arHistory;
					
				}
					
			}
			
			krsort($arHistoryResult);
			
			?>
			
			<? 
			$total = 0; 
			foreach ($arHistoryResult as $idStart => $vProposal): ?>
				<? foreach ($vProposal as $idProp => $vSpec):?>
					<??>
					<?++$total;?>
				<? endforeach;?>
			<? endforeach;?>
			
	<? foreach ($arHistoryResult as $idStart => $vProposal): ?>
	
			<? foreach ($vProposal as $idProp => $vSpec):?>
			
		<?

		
		
		
		$itogo = 0;
		$itogo_n = 0;
		if ($arResult["TYPE_ID"] != "S" && $arResult["TYPE_ID"] != "R") {
			foreach ($vSpec as $idPropBuyer => $proposals) {
				
				$proposals["PRICE_NDS"] = $proposals["PRICE_NDS"]; // floatval($arResult['arCurr'][$arResult["LOT"]["CURRENCY"]]);
				$itogo += $proposals["PRICE_NDS"] * $proposals["COUNT"];
			/* 	echo '<pre>';
				print_r($arResult['arCurr']); */
				//__($arParams["NDS_TYPE"]);
				// Тут разобраться в параметре NDS
				if ($arParams["NDS_TYPE"] == "N") {
					$itogo_n += CTenderix::PriceNDSy($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
				} else {
					$itogo_n += CTenderix::PriceNDSn($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
				}
				//$history[$idPropBuyer] = $proposals;
			}
			//$arResult["PROPOSAL"][$idProp]["HISTORY"] = $history;
		}
		//Пока только для закупок !!!!!!!
		/* if ($arResult["TYPE_ID"] == "S" || $arResult["TYPE_ID"] == "R") {
			foreach ($vProp["PRODUCT"] as $prodArr) {
				$prodArr["PRICE_NDS"] = $prodArr["PRICE_NDS"] / floatval($arCurr[$arResult["LOT"]["CURRENCY"]]);
				$itogo += $prodArr["PRICE_NDS"] * $prodArr["COUNT"];
				if ($arParams["NDS_TYPE"] == "N") {
					$itogo_n += CTenderix::PriceNDSy($prodArr["PRICE_NDS"], $prodArr["NDS"]) * $prodArr["COUNT"];
				} else {
					$itogo_n += CTenderix::PriceNDSn($prodArr["PRICE_NDS"], $prodArr["NDS"]) * $prodArr["COUNT"];
				}
			}
		}  */
		//$arResult["PROPOSAL"][$idProp]["ITOGO"] = $itogo;
		//$itogg[$idProp] = $itogo;
		//$itogg_n[$idProp] = $itogo_n;
		
		
		
		
		
		
		/* if ($arResult["TYPE_ID"] != "S" && $arResult["TYPE_ID"] != "R") {
			$history = $arResult["PROPOSAL"][$idProp]["HISTORY"];
		}  */
		$vProp = $arResult["PROPOSAL"][$idProp];
		?>
		<tr>
		<td><?= $total-- ?></td>
		<td><?= $idStart?></td>
		<td>
			
			<? if (is_file($vProp["USER_INFO"]["LOGO_SMALL"])): ?>
				<img src="<?= $vProp["USER_INFO"]["LOGO_SMALL"] ?>" alt="<?= $vProp["USER_INFO"]["STATUS_NAME"] ?>"/>
			<? endif; ?>
			<a class="user_view" href="#"
			   onclick="userView(<?= $vProp["ID"] ?>)"><?= strlen($vProp["USER_INFO"]["NAME_COMPANY"]) > 0 ? $vProp["USER_INFO"]["NAME_COMPANY"] : $vProp["USER_INFO"]["FIO"] ?></a>
			<div style="display:none">
				<div id="user_<?= $vProp["ID"] ?>">
					<table class="table t_lot_table">
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
		<?if(($arResult['LOT_END'] == 'N' && $arResult['RIGHT'] == "W") || $arResult['LOT_END'] == 'Y'): ?>
		<td >

			<nobr><?= ($itogo > 0) ? number_format($itogo_n, 2, '.', ' ') : '--' ?></nobr>
		</td>
		<td>
			<nobr><?= ($itogo > 0) ? number_format($itogo, 2, '.', ' ') : '--' ?></nobr>
		</td>
		
		<?endif; ?>
		
		</tr>
	<?endforeach; ?>
	<?endforeach; ?>
	</table>

	</form>
	</div>
	</div>
	<? endif; ?>