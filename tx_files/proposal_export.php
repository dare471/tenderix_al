<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); 

if(!isset($_REQUEST["LOT_ID"]))
	die();

if (!CModule::IncludeModule("pweb.tenderix")) {
    ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
    return;
}

global $USER;
$timeZone = time() + CTimeZone::GetOffset();

$rsLot = CTenderixLot::GetByIDa($_REQUEST["LOT_ID"]);
if ($arLot = $rsLot->GetNext()) {
    $arResult["LOT"] = $arLot;
}

$arLotAnalog = CTenderixLotSpec::GetByLotId($_REQUEST["LOT_ID"]);
$arResult["LOT"]["NOT_ANALOG"] = $arLotAnalog["NOT_ANALOG"];

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
$userBind = array();
$rsUserBind = CTenderixUserBuyer::GetList($by = "", $order = "", array("ID" => $USER->GetID()), $filtered);
if ($arUserBind = $rsUserBind->Fetch()) {
    $userBind = unserialize($arUserBind["USER_BIND"]);
}
$contracts = array();
foreach ($arResult["PROPOSAL"] as $pid => $usid) {
	$contracts[] = $usid["USER_ID"];
}

if ($arLot["BUYER_ID"] == $USER->GetID() || $T_RIGHT == "W" || in_array($arLot["BUYER_ID"], $userBind) || (in_array($USER->GetID(), $contracts))) {


$arFilter = array(
    "LOT_ID" => $_REQUEST["LOT_ID"]
);

if (count($arLot) > 0) {
    $arResult["TYPE_ID"] = $arLot["TYPE_ID"];
    $arResult["OWNER"] = ($arLot["BUYER_ID"] == $USER->GetID() || $T_RIGHT == "W") ? "Y" : "N";
    $arResult["RIGHT"] = $T_RIGHT;
    $time_end = strtotime($arLot["DATE_END"]) + intval($arLot["TIME_EXTENSION"]);
    $arResult["LOT_END"] = "N";
    if ($time_end < $timeZone) {
        $arResult["LOT_END"] = "Y";
        $rsWin = CTenderixLot::GetListWinLot(array(), array("LOT_ID" => $_REQUEST["LOT_ID"]));
        while ($arWin = $rsWin->Fetch()) {
            $arResult["WIN"][] = $arWin["USER_ID"];
            $arResult["WIN_COMMENT"][$arWin["USER_ID"]] = $arWin["COMMENT"];
        }
    }

	$rsCompany = CTenderixCompany::GetList($by="", $order="", array("ID" => $arResult["LOT"]["COMPANY_ID"]), $filtered);
	if($arCompany = $rsCompany->GetNext())
		$arResult["COMPANY"] = 	$arCompany;

    if ($arLot["NOTVISIBLE_PROPOSAL"] == "N" || ($arLot["NOTVISIBLE_PROPOSAL"] == "Y" && $arResult["LOT_END"] == "Y") || $T_RIGHT == "W" || $arResult["OWNER"] == "Y") {

        //dop property 
        $rsPropList = CTenderixProposalProperty::GetList($by = "SORT", $order = "desc");
        while ($arPropList = $rsPropList->GetNext()) {
            if ($arPropList["ACTIVE"] == "N")
                continue;
            $arResult["PROP_LIST"][] = $arPropList;
        }
        //dop property
        $rsProposal = CTenderixProposal::GetList($arFilter);
        while ($arProposal = $rsProposal->GetNext()) {
            $arResult["PROPOSAL"][$arProposal["ID"]] = $arProposal;

            //dop property 
            $arResult["PROP_PROPOSAL"][$arProposal["ID"]] = CTenderixProposal::GetProperty($arProposal["ID"]);
            //dop property

            $rsFile = CTenderixProposal::GetFileList($arProposal["ID"]);
            $arrFile = array();
            while ($arFile = $rsFile->Fetch()) {
                $arrFile[] = $arFile;
            }

            $rsUser = CTenderixUserSupplier::GetByID($arProposal["USER_ID"]);
            $arUser = $rsUser->Fetch();
            $arUser["LOGO_SMALL"] = CFile::GetPath($arUser["LOGO_SMALL"]);
            $arUser["LOGO_BIG"] = CFile::GetPath($arUser["LOGO_BIG"]);
            $arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"] = $arUser;

            $rsPropList = CTenderixUserSupplierProperty::GetList($by = "SORT", $order = "asc");
            if ($arProposal["USER_ID"] > 0) {
                $arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP_SUPPLIER"] = CTenderixUserSupplier::GetProperty($arProposal["USER_ID"]);
            }
            while ($arPropList = $rsPropList->GetNext()) {
                if ($arPropList["ACTIVE"] == "N")
                    continue;

                $arPropList["IS_REQUIRED"] = "N";
                $arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP"][$arPropList["ID"]] = $arPropList;
                $arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP"][$arPropList["ID"]] = $arPropList;
                if ($arPropList["PROPERTY_TYPE"] == "F" && $arProposal["USER_ID"] > 0) {
                    $rsFiles = CTenderixUserSupplier::GetFileListProperty($arProposal["USER_ID"], $arPropList["ID"]);
                    while ($arFile = $rsFiles->GetNext()) {
                        $arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP"][$arPropList["ID"]]["FILE"][] = $arFile;
                    }
                }
            }


            $arResult["PROPOSAL"][$arProposal["ID"]]["FILE"] = $arrFile;
            if ($arLot["TYPE_ID"] != "S") {
                $rsProposalSpec = CTenderixProposal::GetListSpec(array("PROPOSAL_ID" => $arProposal["ID"]));
                while ($arProposalSpec = $rsProposalSpec->GetNext()) {
                    $arResult["PROPOSAL"][$arProposal["ID"]]["SPEC"][$arProposalSpec["PROPERTY_BUYER_ID"]] = $arProposalSpec;
                }
                //natsort($arResult["PROPOSAL"][$arProposal["ID"]]["SPEC"]);
            }
            if ($arLot["TYPE_ID"] == "S") {
                $rsProduct = CTenderixProposal::GetListProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
                $arProduct = $rsProduct->Fetch();
                $arResult["PROPOSAL"][$arProposal["ID"]]["PRODUCT"] = $arProduct;

                $rsProductProp = CTenderixProposal::GetListPropertyProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
                while ($arProductProp = $rsProductProp->Fetch()) {
                    $arResult["PROPOSAL"][$arProductProp["PROPOSAL_ID"]]["PROP"][$arProductProp["PRODUCTS_PROPERTY_BUYER_ID"]] = $arProductProp;
                }
            }
        }
        
        //echo "<pre>";print_r($arResult["PROPOSAL"]);echo "</pre>";

        $arParams["NDS_TYPE"] = $arLot["WITH_NDS"];

        $res->arResult = Array();
        unset($arLot);

        $arCurr = array();
        if (CModule::IncludeModule("currency")) {
            $lcur = CCurrency::GetList(($b = "sort"), ($order1 = "asc"), LANGUAGE_ID);
            while ($lcur_res = $lcur->Fetch()) {
                $rsCur = CCurrencyRates::GetList($by = "DATE_RATE", $order = "desc", $arFilter = Array("CURRENCY" => $lcur_res["CURRENCY"]));
                $arCur = $rsCur->Fetch();
                $arCurr[$lcur_res["CURRENCY"]] = $arCur["RATE"] > 0 ? $arCur["RATE"] : 1;
            }
        }
		$arrHistory = array();
        foreach ($arResult["PROPOSAL"] as $idProp => $vProp) {
            $itogo = 0;
            $itogo_n = 0;
            if ($arResult["TYPE_ID"] != "S") {
                foreach ($vProp["SPEC"] as $idPropBuyer => $proposals) {
                    $proposals["PRICE_NDS"]= $proposals["PRICE_NDS"] / floatval($arCurr[$arResult["LOT"]["CURRENCY"]]);
                    $itogo += $proposals["PRICE_NDS"] * $proposals["COUNT"];
                    if ($arParams["NDS_TYPE"] == "N") {
                        $itogo_n += CTenderix::PriceNDSy($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
                    } else {
                        $itogo_n += CTenderix::PriceNDSn($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
                    }
                    $history[$idPropBuyer] = $proposals;
					$arrHistory[$idPropBuyer][] = $proposals["PRICE_NDS"];
                }
                $arResult["PROPOSAL"][$idProp]["HISTORY"] = $history;
            }
            if ($arResult["TYPE_ID"] == "S") {
                $vProp["PRODUCT"]["PRICE_NDS"] = $vProp["PRODUCT"]["PRICE_NDS"] / floatval($arCurr[$arResult["LOT"]["CURRENCY"]]);
                $itogo = $vProp["PRODUCT"]["PRICE_NDS"] * $vProp["PRODUCT"]["COUNT"];
                if ($arParams["NDS_TYPE"] == "N") {
                    $itogo_n = CTenderix::PriceNDSy($vProp["PRODUCT"]["PRICE_NDS"], $vProp["PRODUCT"]["NDS"]) * $vProp["PRODUCT"]["COUNT"];
                } else {
                    $itogo_n = CTenderix::PriceNDSn($vProp["PRODUCT"]["PRICE_NDS"], $vProp["PRODUCT"]["NDS"]) * $vProp["PRODUCT"]["COUNT"];
                }
            }
            //$arResult["PROPOSAL"][$idProp]["ITOGO"] = $itogo;
            $itogg[$idProp] = $itogo;
            $itogg_n[$idProp] = $itogo_n;
        }
        $arr_proposal = $arResult["PROPOSAL"];
        unset($arResult["PROPOSAL"]);
        unset($itogo);
        unset($itogo_n);
		asort($itogg, SORT_NUMERIC);
        asort($itogg_n, SORT_NUMERIC);
        /*if ($arParams["SORT_ITOGO"] == "asc") {
            asort($itogg, SORT_NUMERIC);
            asort($itogg_n, SORT_NUMERIC);
        } elseif ($arParams["SORT_ITOGO"] == "desc") {
            arsort($itogg, SORT_NUMERIC);
            arsort($itogg_n, SORT_NUMERIC);
        }*/
        foreach ($itogg as $idProp => $itogo) {
            $arResult["PROPOSAL"][$idProp] = $arr_proposal[$idProp];
            $arResult["PROPOSAL"][$idProp]["ITOGO"] = $itogo;
        }
        foreach ($itogg_n as $idProp => $itogo_n) {
            $arResult["PROPOSAL"][$idProp]["ITOGO_N"] = $itogo_n;
        }
    }
	
	if ($arLot["TYPE_ID"] != "S" && $arLot["TYPE_ID"] != "R") {
		foreach($arResult["PROPOSAL"] as $propARR) {
			$arResult["SPEC"] = $propARR["SPEC"];
			continue;
		}
	}
	
	$r = array();
	$t = array();
    $p = array();
	foreach($arResult["PROPOSAL"] as $id_prop => $prop) {
		foreach($prop["HISTORY"] as $id_spec => $spec) {
			$r[$id_spec][$spec["PROPOSAL_ID"]] = $spec;
			$t[$id_spec] = array(
                  "TITLE" => $spec["TITLE"],
                  "START_PRICE" => $spec["START_PRICE"],
                  "ADD_INFO" => $spec["ADD_INFO"],
                  "COUNT" => $spec["COUNT"],
                );
            $p[$id_spec][] = $spec["PRICE_NDS"];
		}
	}
	$r["TOVAR"] = $t;
	$r["PRICE"] = $p;
    $arResult["SPEC2"] = $r;

	//echo "<pre>";print_r($arResult);echo "</pre>";
	//die();	
	$file='Lot_proposal_'.$arResult['LOT']['ID'].'.xls';

	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$file");

	
	?>
	<html xmlns:x="urn:schemas-microsoft-com:office:excel">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<table class="learn-gradebook-table data-table" border="1">
				<tr>
					<td border="1"></td>
					<td border="1"></td>
					<td border="1"></td>
					<td border="1"></td>
					<td border="1"></td>
					<? foreach ($arResult["PROPOSAL"] as $id_prop => $prop): ?>
                        <td border="1" colspan="<?if($arResult['LOT']['WITH_NDS'] != 'Y'):?>4<?else:?>4<?endif;?>"><?= $prop["USER_INFO"]["NAME_COMPANY"] ?></td>
					<? endforeach; ?>
				</tr>
				<tr>
					<td border="1">№</td>
					<td border="1">Наименование позиции</td>
					<td border="1">Кол-во</td>
					<td border="1">Описание</td>
                    <?if($arResult['LOT']['WITH_NDS'] != 'Y' /*|| $arResult['LOT']['WITH_NDS'] == 'Y'*/):?>
                        <td border="1">Минимальная цена по позиции без НДС</td>
                        <? foreach ($arResult["PROPOSAL"] as $id_prop => $prop): ?>
                            <td border="1">Цена за ед, без НДС</td>
                            <td border="1">Сумма без НДС</td>
                            <td>НДС</td>
                            <td>Сумма с НДС</td>
                        <? endforeach; ?>
                    <?else:?>
                        <td border="1">Минимальная цена по позиции c НДС</td>
                        <? foreach ($arResult["PROPOSAL"] as $id_prop => $prop): ?>
                            <td border="1">Цена за ед, c НДС</td>
                            <td border="1">Сумма без НДС</td>
                            <td>НДС</td>
                            <td border="1">Сумма c НДС</td>
                        <? endforeach; ?>
                    <?endif;?>


				</tr>
				<? $i = 1;
                    $idtovar = array();
                ?>
				<? foreach ($arResult["SPEC2"]["TOVAR"] as $id_tov => $tov): ?>
					<?
                    $idtovar[] = $id_tov;
					$arPP = array();
					foreach ($arResult["SPEC2"]["PRICE"][$id_tov] as $pp) {
						if ($pp > 0)
							$arPP[] = $pp;
					}
					$arResult["SPEC2"]["PRICE"][$id_tov] = $arPP;
					$best_price = $arResult["TYPE_ID"] != "P" ? min($arResult["SPEC2"]["PRICE"][$id_tov]) : max($arResult["SPEC2"]["PRICE"][$id_tov]); ?>
					<tr>
						<td border="1"><?= $i ?></td>
						<td border="1"><?= $tov["TITLE"] ?></td>
						<td border="1"><?= $tov["COUNT"] ?></td>
						<td border="1"><?= $tov["ADD_INFO"] ?></td>
						<td border="1"><?= $best_price ?></td>
						<? foreach ($arResult["SPEC2"][$id_tov] as $spec): ?>
							<?
							if ($spec["PRICE_NDS"] == $best_price) $cls = " style='background-color:#CCFFCC;'";
							else $cls = "";
							?>
							<td border="1" <?= $cls ?>>
								<?
								//=$spec["PRICE_NDS"];
								if ($spec["PRICE_NDS"] == 0) {
									echo "--";
								} else {
									echo $spec["PRICE_NDS"];
								}
								?>
							</td>
							<td border="1" <?= $cls ?>>
								<?
								//=$spec["PRICE_NDS"]*$spec["COUNT"];
//								if ($spec["PRICE_NDS"] == 0) {
//									echo "--";
//								} else {
                                    $withnds = $spec["PRICE_NDS"] * $spec["COUNT"];
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
                            <td>
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
					<!--td>
						<? echo number_format($best_price_itogo, 2, '.', ' ');?>
					</td-->
					<?//echo "<pre>";print_r($arResult["PROPOSAL"]);echo "</pre>"?>
					<?foreach ($arResult["PROPOSAL"] as $proposal):?>
						<td>
							<?$spec_itogo = 0;?>
							<?foreach ($proposal["SPEC"]as $spec): ?>
								<?$spec_itogo = $spec_itogo + $spec['PRICE_NDS'];?>
							<?endforeach;?>
							<? //echo number_format(($spec_itogo / floatval($arResult["ARRCUR"][$arResult["LOT"]["CURRENCY"]])), 2, '.', ' ');?>
						</td>
						<td>
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
                        <td>
                            <? if ($arResult['LOT']['WITH_NDS'] == 'Y') { ?>
                                <? echo number_format($proposal["ITOGO"], 2, '.', ' ');?>
                            <? } else {
                                echo number_format($proposal["ITOGO"]*$nds, 2, '.', ' ');
                            } ?>
                        </td>
                        <?endif;?>
					<?endforeach;?>
				</tr>
                <? if($arResult["LOT"]["NOT_ANALOG"] == "N") { ?>
                    <? $i = 0; ?>
                    <? $j = 0; ?>
                    <? foreach($idtovar as $idtovar_id => $idtovar_val) { ?>
                        <tr>
                            <td colspan="5" width="40%">
                                <b><?=$arResult["SPEC2"][$idtovar_val][$propos_id[$j]][$idtovar[$i]]["TITLE"]; ?></b>
                                Аналог
                            </td>

                            <?foreach($arResult["SPEC2"][$idtovar_val] as $idSpec => $valSpec):?>
                                <td colspan="4">
                                    <br/>
                                    <?=(empty($valSpec["ANALOG"]) ? "--" : $valSpec["ANALOG"]); ?>
                                </td>
                            <?endforeach;?>
                        </tr>
                        <? $i++; ?>
                    <? } ?>
                <? } ?>
		</table>
	</body>
</html>	
	<?
	}
}
?>