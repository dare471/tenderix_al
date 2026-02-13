<?
function LotExpired() {
CModule::IncludeModule("pweb.tenderix");
$arFilter["ACTIVE"] = "Y";
//$arFilter["ACTIVE_DATE"] = "Y";
$yesterday = date("d.m.Y H:i:s", mktime(date("H"), date("i")-10, date("s"), date("m"), date("d"), date("Y")));
$arFilter["DATE_END2"] = $yesterday;
$arFilter["DATE_END"] = date("d.m.Y H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
$by = "ID";
$order = "asc";
$res = CTenderixLot::GetList2($by, $order, $arFilter);
$text = "";

while ($arLot = $res->GetNext()) {
    if (($arLot["SEND_SPEC"] == "Y") && ($arLot["TYPE_ID"] != "S")) {
        $arResult["LOT"] = $arLot;

        $arFilter3 = array(
            "LOT_ID" => $arLot["ID"]
        );

        $arResult["TYPE_ID"] = $arLot["TYPE_ID"];
        //$arResult["OWNER"] = ($arLot["BUYER_ID"] == $USER->GetID()) ? "Y" : "N";
        $arResult["OWNER"] = "N";
        $time_end = strtotime($arLot["DATE_END"]) + intval($arLot["TIME_EXTENSION"]);
        $arResult["LOT_END"] = "N";

        //dop property 
        $rsPropList = CTenderixProposalProperty::GetList($by = "SORT", $order = "desc");
        $arPropList = array();
        while ($arPropList = $rsPropList->GetNext()) {
            if ($arPropList["ACTIVE"] == "N")
                continue;
            $arResult["PROP_LIST"][] = $arPropList;
        }
        //dop property

        $rsProposal = CTenderixProposal::GetList($arFilter3);
        $arProposal = array();
        while ($arProposal = $rsProposal->GetNext()) {
            $arResult["PROPOSAL"][$arProposal["ID"]] = $arProposal;

            //dop property 
            $arResult["PROP_PROPOSAL"][$arProposal["ID"]] = CTenderixProposal::GetProperty($arProposal["ID"]);
            //dop property

            $rsUser = CTenderixUserSupplier::GetByID($arProposal["USER_ID"]);
            $arUser = $rsUser->Fetch();
            $arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"] = $arUser;

            $rsPropList = CTenderixUserSupplierProperty::GetList($by = "SORT", $order = "asc");
            if ($arProposal["USER_ID"] > 0) {
                $arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP_SUPPLIER"] = CTenderixUserSupplier::GetProperty($arProposal["USER_ID"]);
            }
            $arPropList = array();
            while ($arPropList = $rsPropList->GetNext()) {
                if ($arPropList["ACTIVE"] == "N")
                    continue;
                $arPropList["IS_REQUIRED"] = "N";
                $arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP"][$arPropList["ID"]] = $arPropList;
            }


            if ($arLot["TYPE_ID"] != "S") {
                $rsProposalSpec = CTenderixProposal::GetListSpec(array("PROPOSAL_ID" => $arProposal["ID"]));
                $arProposalSpec = array();
                while ($arProposalSpec = $rsProposalSpec->GetNext()) {
                    $arResult["PROPOSAL"][$arProposal["ID"]]["SPEC"][$arProposalSpec["PROPERTY_BUYER_ID"]] = $arProposalSpec;
                }

            }
            if ($arLot["TYPE_ID"] == "S") {
                $rsProduct = CTenderixProposal::GetListProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
                $arProduct = $rsProduct->Fetch();
                $arResult["PROPOSAL"][$arProposal["ID"]]["PRODUCT"] = $arProduct;

                $rsProductProp = CTenderixProposal::GetListPropertyProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
                $arProductProp = array();
                while ($arProductProp = $rsProductProp->Fetch()) {
                    $arResult["PROPOSAL"][$arProductProp["PROPOSAL_ID"]]["PROP"][$arProductProp["PRODUCTS_PROPERTY_BUYER_ID"]] = $arProductProp;
                }
            }
        }

		
		
        $arParams["NDS_TYPE"] = $arLot["WITH_NDS"];

        $res->arResult = Array();
        //unset($arLot);

        $arCurr = array();
        if (CModule::IncludeModule("currency")) {
            $lcur = CCurrency::GetList(($b = "sort"), ($order1 = "asc"), LANGUAGE_ID);
            while ($lcur_res = $lcur->Fetch()) {
                $rsCur = CCurrencyRates::GetList($by = "DATE_RATE", $order = "desc", $arFilter = Array("CURRENCY" => $lcur_res["CURRENCY"]));
                $arCur = $rsCur->Fetch();
                $arCurr[$lcur_res["CURRENCY"]] = $arCur["RATE"] > 0 ? $arCur["RATE"] : 1;
            }
        }
        $history = array();
        $itogg = array();
        $itogg_n = array();
        foreach ($arResult["PROPOSAL"] as $idProp => $vProp) {
            $itogo = 0;
            $itogo_n = 0;
            if ($arResult["TYPE_ID"] != "S") {
                foreach ($vProp["SPEC"] as $idPropBuyer => $proposals) {
                    $proposals["PRICE_NDS"] = $proposals["PRICE_NDS"] / floatval($arCurr[$arResult["LOT"]["CURRENCY"]]);
                    $itogo += $proposals["PRICE_NDS"] * $proposals["COUNT"];
                    if ($arParams["NDS_TYPE"] == "N") {
                        $itogo_n += CTenderix::PriceNDSy($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
                    } else {
                        $itogo_n += CTenderix::PriceNDSn($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
                    }
                    $history[$idPropBuyer] = $proposals;
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

        arsort($itogg, SORT_NUMERIC);
        arsort($itogg_n, SORT_NUMERIC);
        // }
        foreach ($itogg as $idProp => $itogo) {
            $arResult["PROPOSAL"][$idProp] = $arr_proposal[$idProp];
            $arResult["PROPOSAL"][$idProp]["ITOGO"] = $itogo;
        }
        foreach ($itogg_n as $idProp => $itogo_n) {
            $arResult["PROPOSAL"][$idProp]["ITOGO_N"] = $itogo_n;
        }
		
        $r = array();
        $t = array();
        $p = array();
		
		$r2 = array();
		$t2 = array();
        $p2 = array();
		
		
        foreach ($arResult["PROPOSAL"] as $id_prop => $prop) {
            foreach ($prop["HISTORY"] as $id_spec => $spec) {
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
		
        ?>

		<?
		$uz_prop = array();
		$uz_prop["PROPOSAL"] = $arResult["PROPOSAL"];
		?>
        <? if ($arLot["TYPE_ID"] != "S"): ?>
		  <? //foreach ($uz_prop["PROPOSAL"] as $id_prop3 => $prop3): ?>
			
			<? $spec_mail= ""; ?>
            <!--<div style="overflow-x:auto !important; overflow-y:hidden !important;">
                <div class="t_prov">	-->
					<? ////// ?>
					<? foreach ($arResult["PROPOSAL"] as $id_prop => $prop): ?>
					<!--<table class="t_lot_table" style="border-spacing: 2px;border-color: gray;border-collapse: collapse;">
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>-->
                            <? $spec_mail .= "<table class='t_lot_table' style='border-spacing: 2px;border-color: gray;border-collapse: collapse;'>\r\n"; ?>
                            <? $spec_mail .= "<tr>\r\n"; ?>
                            <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'></td>\r\n"; ?>
                            <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'></td>\r\n"; ?>
                            <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'></td>\r\n"; ?>
                            <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'></td>\r\n"; ?>
                            <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'></td>\r\n"; ?>
                            <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'></td>\r\n"; ?>



                                <!--<td colspan="2"><?= $prop["USER_INFO"]["NAME_COMPANY"] ?></td>-->
                                <? $spec_mail .= "<td colspan='2' style='border: 1px solid #d9d9d9;'>" . $prop["USER_INFO"]["NAME_COMPANY"] . "</td>\r\n"; ?>

                       <!-- </tr>
                        <tr>-->
                            <? $spec_mail .= "</tr>\r\n"; ?>
                            <? $spec_mail .= "<tr>\r\n"; ?>
                            <!--<td>№</td>
                            <td>Товар</td>
                            <td>Кол-во</td>
                            <td>Доп. хар-ки</td>
                            <td>Начальная цена с НДС</td>
                            <td>Лучшая цена</td>-->
                            <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>№</td>\r\n"; ?>
                            <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>Товар</td>\r\n"; ?>
                            <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>Кол-во</td>\r\n"; ?>
                            <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>Доп. хар-ки</td>\r\n"; ?>
                            <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>Начальная цена с НДС</td>\r\n"; ?>
                            <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>Лучшая цена</td>\r\n"; ?>

                                <!--<td>Цена за ед, с НДС</td>
                                <td>Сумма</td>-->
                                <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>Цена за ед, с НДС</td>\r\n"; ?>
                                <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>Сумма</td>\r\n"; ?>


                        <!--</tr>-->
                        <? $spec_mail .= "</tr>\r\n"; ?>
                        <? $i = 1; ?>
                        <? foreach ($arResult["SPEC2"]["TOVAR"] as $id_tov => $tov): ?>
                            <?

							$arPP = array();
							foreach($arResult["SPEC2"]["PRICE"][$id_tov] as $pp) {
								if($pp > 0)
								$arPP[] = $pp;
							}
							$arResult["SPEC2"]["PRICE"][$id_tov] = $arPP;
							
							//$best_price = $arResult["TYPE_ID"]!="P" ? min($arResult["SPEC2"]["PRICE"][$id_tov]) : max($arResult["SPEC2"]["PRICE"][$id_tov]);
							//$min_price = min($arResult["SPEC2"]["PRICE"][$id_tov]); 
							$min_price = $arResult["TYPE_ID"]!="P" ? min($arResult["SPEC2"]["PRICE"][$id_tov]) : max($arResult["SPEC2"]["PRICE"][$id_tov]);
							?>
                            <!--<tr>-->
                                <? $spec_mail .= "<tr>\r\n"; ?>
                                <!--<td><?= $i ?></td>
                                <td><?= $tov["TITLE"] ?></td>
                                <td><?= $tov["COUNT"] ?></td>
                                <td><?= $tov["ADD_INFO"] ?></td>
                                <td><?= $tov["START_PRICE"] ?></td>
                                <td><?= $min_price ?></td>-->
                                <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>" . $i . "</td>\r\n"; ?>
                                <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>" . $tov["TITLE"] . "</td>\r\n"; ?>
                                <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>" . $tov["COUNT"] . "</td>\r\n"; ?>
                                <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>" . $tov["ADD_INFO"] . "</td>\r\n"; ?>
                                <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>" . $tov["START_PRICE"] . "</td>\r\n"; ?>
                                <? $spec_mail .= "<td style='border: 1px solid #d9d9d9;'>" . $min_price . "</td>\r\n"; ?>
								
								<?
								/*echo "<pre>";
								print_r($arResult["SPEC2"][$id_tov]["80"]);
								echo "</pre>";*/
								?>
								
                                <? //foreach ($arResult["SPEC2"][$id_tov] as $spec): ?>
                                    <?
                                    if ($arResult["SPEC2"][$id_tov][$id_prop]["PRICE_NDS"] == $min_price)
                                        $cls = " style='background-color:#CCFFCC;border: 1px solid #d9d9d9;'";
                                    else
                                        $cls = " style='border: 1px solid #d9d9d9;'";
                                    ?>
                                    <!--<td<?= $cls ?>><?= $arResult["SPEC2"][$id_tov][$id_prop]["PRICE_NDS"] ?></td>
                                    <td<?= $cls ?>><?= $arResult["SPEC2"][$id_tov][$id_prop]["PRICE_NDS"] * $arResult["SPEC2"][$id_tov][$id_prop]["COUNT"] ?></td>-->
                                    <? $spec_mail .= "<td" . $cls . ">" . $arResult["SPEC2"][$id_tov][$id_prop]["PRICE_NDS"] . "</td>\r\n"; ?>
                                    <? $spec_mail .= "<td" . $cls . ">" . $arResult["SPEC2"][$id_tov][$id_prop]["PRICE_NDS"] * $arResult["SPEC2"][$id_tov][$id_prop]["COUNT"] . "</td>\r\n"; ?>
                                <? //endforeach; ?>
                            </tr>
                            <? $spec_mail .= "</tr>\r\n"; ?>
                            <? $i++; ?>
                        <? endforeach; ?>
                    <!--</table>-->
                    <? $spec_mail .= "</table>\r\n"; ?>
					
					<?
					
						$text = "Лот №" . $arLot["ID"] . " " . $arLot["TITLE"] . " завершен " . $arLot["DATE_END"] . ".";
						$text .= "<br /><br />Cпецификация: <br />";
						$text .= $spec_mail;
						
						$text .= $prop["USER_INFO"]["EMAIL"]; 
						$arEventFields = array(
							"email" => $prop["USER_INFO"]["EMAIL"],
							//"email" => "enj@pweb.ru",
							"mess" => $text
						);
						CEvent::Send("LOT_EXPIRED", "s1", $arEventFields);
						
						$text = "";
						$spec_mail = "";
					
					?>
					<? endforeach; ?>
					<? ////// ?>
					
					
                </div>
            </div>
		  
		  <?// endforeach; ?>
        <? endif; ?>

        <?
       /* $text = "Лот №" . $arLot["ID"] . " " . $arLot["TITLE"] . " завершен " . $arLot["DATE_END"] . ".";
        $text .= "<br /><br />Cпецификация: <br />";
        $text .= $spec_mail;

        foreach ($arResult["PROPOSAL"] as $id_prop => $prop):

            $arEventFields = array(
                "email" => $prop["USER_INFO"]["EMAIL"],
                "mess" => $text
            );
        CEvent::Send("LOT_EXPIRED", "s1", $arEventFields);
        endforeach;*/
    }
    $text = "";
    $spec_mail = "";
    $arResult = array();
    $arProposal = array();
}
return "LotExpired();";
}
?>