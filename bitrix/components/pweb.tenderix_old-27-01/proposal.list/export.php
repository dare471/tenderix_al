<?


if (!CModule::IncludeModule("pweb.tenderix")) {
    $this->AbortResultCache();
    ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
    return;
}

if(!isset($_REQUEST["LOT_ID"]))
	die();

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
if ($T_RIGHT == "D" || $T_RIGHT == "P")
    return;

$timeZone = time() + CTimeZone::GetOffset();

$rsLot = CTenderixLot::GetByIDa($_REQUEST["LOT_ID"]);
if ($arLot = $rsLot->GetNext()) {
    $time_end = strtotime($arLot["DATE_END"]) + intval($arLot["TIME_EXTENSION"]);
    if ($time_end < $timeZone && ($arLot["BUYER_ID"] == $USER->GetID() || $T_RIGHT == "W")) {
        $CACHE_MANAGER->ClearByTag('pweb.tenderix_proposal.list_' . $arParams["LOT_ID"]);
    }
    $arResult["LOT"] = $arLot;
}

$userBind = array();
$rsUserBind = CTenderixUserBuyer::GetList($by = "", $order = "", array("ID" => $USER->GetID()));
if ($arUserBind = $rsUserBind->Fetch()) {
    $userBind = unserialize($arUserBind["USER_BIND"]);
}

if ($arLot["BUYER_ID"] == $USER->GetID() || $T_RIGHT == "W" || in_array($arLot["BUYER_ID"], $userBind)) {

    $arFilter = array(
        "LOT_ID" => $_REQUEST["LOT_ID"]
    );

    //$rsLot = CTenderixLot::GetByIDa($arParams["LOT_ID"]);
    if (count($arLot) > 0) {
        $arResult["TYPE_ID"] = $arLot["TYPE_ID"];
        $arResult["OWNER"] = ($arLot["BUYER_ID"] == $USER->GetID() || $T_RIGHT == "W") ? "Y" : "N";
        $arResult["RIGHT"] = $T_RIGHT;
        $time_end = strtotime($arLot["DATE_END"]) + intval($arLot["TIME_EXTENSION"]);
        $arResult["LOT_END"] = "N";
        if ($time_end < $timeZone) {
            $arResult["LOT_END"] = "Y";
            $rsWin = CTenderixLot::GetListWinLot(array(), array("LOT_ID" => $arParams["LOT_ID"]));
            while ($arWin = $rsWin->Fetch()) {
                $arResult["WIN"][] = $arWin["USER_ID"];
                $arResult["WIN_COMMENT"][$arWin["USER_ID"]] = $arWin["COMMENT"];
            }
        }


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

                    $arPropList["IS_REQUIRED"] = in_array("PROP_" . $arPropList["ID"], $arParams["REG_FIELDS_REQUIRED"]) ? "Y" : "N";
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
            //print_r($arResult["PROPOSAL"]);
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

            foreach ($arResult["PROPOSAL"] as $idProp => $vProp) {
                $itogo = 0;
                $itogo_n = 0;
                $hasPrices = false;
                if ($arResult["TYPE_ID"] != "S") {
                    foreach ($vProp["SPEC"] as $idPropBuyer => $proposals) {
                        if (floatval($proposals["PRICE_NDS"]) > 0 && floatval($proposals["COUNT"]) > 0) {
                            $hasPrices = true;
                            $proposals["PRICE_NDS"]= $proposals["PRICE_NDS"] / floatval($arCurr[$arResult["LOT"]["CURRENCY"]]);
                            $itogo += $proposals["PRICE_NDS"] * $proposals["COUNT"];
                            if ($arParams["NDS_TYPE"] == "N") {
                                $itogo_n += CTenderix::PriceNDSy($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
                            } else {
                                $itogo_n += CTenderix::PriceNDSn($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
                            }
                        }
                        $history[$idPropBuyer] = $proposals;
                    }
                    $arResult["PROPOSAL"][$idProp]["HISTORY"] = $history;
                }
                if ($arResult["TYPE_ID"] == "S") {
                    if (floatval($vProp["PRODUCT"]["PRICE_NDS"]) > 0 && floatval($vProp["PRODUCT"]["COUNT"]) > 0) {
                        $hasPrices = true;
                        $vProp["PRODUCT"]["PRICE_NDS"] = $vProp["PRODUCT"]["PRICE_NDS"] / floatval($arCurr[$arResult["LOT"]["CURRENCY"]]);
                        $itogo = $vProp["PRODUCT"]["PRICE_NDS"] * $vProp["PRODUCT"]["COUNT"];
                        if ($arParams["NDS_TYPE"] == "N") {
                            $itogo_n = CTenderix::PriceNDSy($vProp["PRODUCT"]["PRICE_NDS"], $vProp["PRODUCT"]["NDS"]) * $vProp["PRODUCT"]["COUNT"];
                        } else {
                            $itogo_n = CTenderix::PriceNDSn($vProp["PRODUCT"]["PRICE_NDS"], $vProp["PRODUCT"]["NDS"]) * $vProp["PRODUCT"]["COUNT"];
                        }
                    }
                }
                // Устанавливаем итоги только если есть заполненные цены
                if ($hasPrices && $itogo > 0) {
                    $itogg[$idProp] = $itogo;
                    $itogg_n[$idProp] = $itogo_n;
                } else {
                    $itogg[$idProp] = 0;
                    $itogg_n[$idProp] = 0;
                }
            }
            $arr_proposal = $arResult["PROPOSAL"];
            unset($arResult["PROPOSAL"]);
            unset($itogo);
            unset($itogo_n);
            if ($arParams["SORT_ITOGO"] == "asc") {
                asort($itogg, SORT_NUMERIC);
                asort($itogg_n, SORT_NUMERIC);
            } elseif ($arParams["SORT_ITOGO"] == "desc") {
                arsort($itogg, SORT_NUMERIC);
                arsort($itogg_n, SORT_NUMERIC);
            }
            foreach ($itogg as $idProp => $itogo) {
                $arResult["PROPOSAL"][$idProp] = $arr_proposal[$idProp];
                $arResult["PROPOSAL"][$idProp]["ITOGO"] = $itogo;
            }
            foreach ($itogg_n as $idProp => $itogo_n) {
                $arResult["PROPOSAL"][$idProp]["ITOGO_N"] = $itogo_n;
            }
        }
    }
	print_r($arResult);
}
?>