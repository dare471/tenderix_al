<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix")) {
    ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
    return;
}

function PriceFormat($price) {
    $price = str_replace(",", ".", $price);
    $price = floatval($price);
    if ($price < 0)
        return 0;
    return $price;
}

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
$S_RIGHT = CTenderixUserSupplierStatus::GetStatusRight();

if ($T_RIGHT == "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

if ($S_RIGHT == "D" && $T_RIGHT == "P") {
    ShowError(GetMessage("ACCESS_DENIED"));
    return;
}

$timeZone = time() + CTimeZone::GetOffset();

$arResult["PROPOSAL_ID"] = $_REQUEST["PROPOSAL_ID"] ? $_REQUEST["PROPOSAL_ID"] : 0;

if (CModule::IncludeModule("currency")) {
    $lcur = CCurrency::GetList(($b = "sort"), ($order1 = "asc"), LANGUAGE_ID);
    while ($lcur_res = $lcur->Fetch()) {
        $rsCur = CCurrencyRates::GetList($by = "DATE_RATE", $order = "desc", $arFilter = Array("CURRENCY" => $lcur_res["CURRENCY"]));
        $arCur = $rsCur->Fetch();
        $arResult["CURRENCY"][$lcur_res["CURRENCY"]] = array(
            "RATE" => CurrencyFormat($arCur["RATE"], $lcur_res["CURRENCY"]),
            "RATE_NUM" => $arCur["RATE"],
            "DATE_RATE" => $arCur["DATE_RATE"],
            "ID" => $arCur["ID"]
        );
        $curr_name[$lcur_res["CURRENCY"]] = $arCur["RATE"] > 0 ? $arCur["RATE"] : 1;
    }
}

//if ($T_RIGHT == "W" || ($S_RIGHT == "W" && $T_RIGHT == "P")) {
if ($T_RIGHT == "W" || $T_RIGHT == "P") {
    $new_proposal = false;
    $LOT_ID = intval($_REQUEST["LOT_ID"]);
    if ($LOT_ID <= 0) {
        ShowError(GetMessage("PW_TD_LOT_NOTFOUND"));
        return;
    }

    $rsLot = CTenderixLot::GetByIDa($LOT_ID);
    $arLot = $rsLot->Fetch();
    $arParams["NDS_TYPE"] = $arLot["WITH_NDS"];

//�����������
    if (isset($_REQUEST["proposal_submit"])) {
        $arFields["TERM_PAYMENT_VAL"] = $_REQUEST["TERM_PAYMENT_VAL"];
        $arFields["TERM_DELIVERY_VAL"] = $_REQUEST["TERM_DELIVERY_VAL"];
        $arFields["LOT_ID"] = $LOT_ID;
        if (!isset($_REQUEST["PROPOSAL_ID"])) {
            $arFields["USER_ID"] = $USER->GetID();
        }
        $arFields["CURRENCY"] = $_REQUEST["CURRENCY_PROPOSAL"];
        $arFields["MESSAGE"] = $_REQUEST["MESSAGE"];
        $arFields["DATE_START"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), $timeZone);

        //property
        if (is_array($_REQUEST["FILE_ID_PROP"])) {
            foreach ($_REQUEST["FILE_ID_PROP"] as $file)
                CTenderixProposal::DeleteFileProperty($arResult["PROPOSAL_ID"], $file);
        }

        $arPropDop = array("PROPERTY" => $_REQUEST["PROP"], "FILES" => $_FILES["PROP"]);
        $rsPropS = CTenderixProposalProperty::GetList();
        $arParams["PROPERTY"] = $arLot["TYPE_ID"] == "P" ? $arParams["PROPERTY2"] : $arParams["PROPERTY"];
        $arParams["PROPERTY_REQUIRED"] = $arLot["TYPE_ID"] == "P" ? $arParams["PROPERTY_REQUIRED2"] : $arParams["PROPERTY_REQUIRED"];
        while ($arPropS = $rsPropS->Fetch()) {
            if (in_array("PROP_" . $arPropS["ID"], $arParams["PROPERTY"]) || !isset($arParams["PROPERTY"])) {
                if (isset($arParams["PROPERTY"])) {
                    $arPropS["IS_REQUIRED"] = in_array("PROP_" . $arPropS["ID"], $arParams["PROPERTY_REQUIRED"]) ? "Y" : "N";
                }
                $arrPropS[$arPropS["ID"]] = $arPropS;
            }
        }
        $arPropDop["PROPERTY_S"] = $arrPropS;
        $arFields["PROPERTY"] = $arPropDop;

        //property
        if ($arLot["TYPE_ID"] == "S") {
            $arFieldsPropertyProducts = array();
            //print_r($_REQUEST["PROPS"]);
            foreach ($_REQUEST["PROPS"] as $idProp => $valueProp) {
                $arFieldsPropertyProducts[$idProp]["PRODUCTS_PROPERTY_BUYER_ID"] = $idProp;
                $arFieldsPropertyProducts[$idProp]["VALUE"] = $valueProp;
            }
            $check_fields = CTenderixProposal::CheckFields($arFields, $arFieldsPropertyProducts);
        }
        if ($arLot["TYPE_ID"] != "S") {
            $arFieldsSpec = array();
            $rsProp = CTenderixLotSpec::GetListProp($LOT_ID);
            while ($arProp = $rsProp->Fetch()) {
                $arFieldsBuyer["FULL_SPEC"] = $arProp["FULL_SPEC"];
                $arFieldsSpec[$arProp["ID"]]["PROPERTY_BUYER_ID"] = $arProp["ID"];
                $arFieldsSpec[$arProp["ID"]]["NDS"] = $_REQUEST["PROP_" . $arProp["ID"] . "_NDS"];
                $arFieldsSpec[$arProp["ID"]]["PRICE_NDS"] = PriceFormat($_REQUEST["PROP_" . $arProp["ID"] . "_PRICE_NDS"]) * floatval($curr_name[$_REQUEST["CURRENCY_PROPOSAL"]]);
                $arFieldsSpec[$arProp["ID"]]["ANALOG"] = trim($_REQUEST["PROP_" . $arProp["ID"] . "_ANALOG"]);
                $arFieldsSpec[$arProp["ID"]]["DATE_START"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), $timeZone);
            }
            $check_fields = CTenderixProposal::CheckFields($arFields, $arFieldsSpec, $arFieldsBuyer);
        }

        if ($check_fields) {
            if (isset($_REQUEST["PROPOSAL_ID"]) && $_REQUEST["PROPOSAL_ID"] > 0) {
                $ID = $_REQUEST["PROPOSAL_ID"];
                $ID = CTenderixProposal::Update($ID, $arFields);
            } else {
                $ID = CTenderixProposal::Add($arFields);
                if (intval($ID) > 0)
                    $new_proposal = true;
            }

            if (intval($ID) > 0) {
                $arResult["PROPOSAL_ID"] = $ID;
                CTenderixProposal::SetProperty($ID, $arPropDop);

                //File add
                if (is_array($_REQUEST["FILE_ID"]))
                    foreach ($_REQUEST["FILE_ID"] as $file)
                        CTenderixProposal::DeleteFile($ID, $file);
                if (is_array($_FILES["NEW_FILE"]))
                    foreach ($_FILES["NEW_FILE"] as $attribute => $files)
                        if (is_array($files))
                            foreach ($files as $index => $value)
                                $arFiles[$index][$attribute] = $value;

                foreach ($arFiles as $file) {
                    if (strlen($file["name"]) > 0 && intval($file["size"]) > 0) {
                        $res_file = CTenderixProposal::SaveFile($ID, $file);
                        if (!$res_file)
                            break;
                    }
                }

                if ($arLot["TYPE_ID"] == "S") {
                    $arFieldsPropertyProducts = array();
                    foreach ($_REQUEST["PROPS"] as $idProp => $valueProp) {
                        $arFieldsPropertyProducts[$idProp]["PRODUCTS_PROPERTY_BUYER_ID"] = $idProp;
                        $arFieldsPropertyProducts[$idProp]["PROPOSAL_ID"] = $ID;
                        $arFieldsPropertyProducts[$idProp]["VALUE"] = $valueProp;
                    }
                    if (!isset($_REQUEST["PROPOSAL_ID"])) {
                        CTenderixProposal::AddPropertyProducts($arFieldsPropertyProducts);
                    } else {
                        CTenderixProposal::UpdatePropertyProducts($arFieldsPropertyProducts);
                    }

                    $arFieldsProducts = array();
                    $arFieldsProducts["PROPOSAL_ID"] = $ID;
                    $arFieldsProducts["NDS"] = $_REQUEST["NDS"];
                    $arFieldsProducts["PRICE_NDS"] = PriceFormat($_REQUEST["PRICE_NDS"]) * floatval($curr_name[$_REQUEST["CURRENCY_PROPOSAL"]]);
                    $arFieldsProducts["COUNT"] = $_REQUEST["COUNT"];
                    $arFieldsProducts["DATE_START"] = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), $timeZone);

                    CTenderixProposal::AddProductsHistory($arFieldsProducts, $LOT_ID);

                    if (!isset($_REQUEST["PROPOSAL_ID"])) {
                        CTenderixProposal::AddProducts($arFieldsProducts);
                        CTenderixLog::Log("PROPOSAL_ADD", array("ID" => $ID, "FIELDS" => $arFields, "PRODUCTS" => $arFieldsProducts));
                    } else {
                        CTenderixProposal::UpdateProducts($arFieldsProducts);
                        CTenderixLog::Log("PROPOSAL_UPDATE", array("ID" => $ID, "FIELDS" => $arFields, "PRODUCTS" => $arFieldsProducts));
                    }
                    CTenderixStatistic::UpdatePriceForS($LOT_ID);

                    //event TENDERIX_BEST_PROPOSAL -->
                    if ($arLot["OPEN_PRICE"] == "Y") {
                        $rsBestProposal = CTenderixProposal::GetListProductsPriceAll($LOT_ID);
                        while ($arBestProposal = $rsBestProposal->Fetch()) {
                            $arrBestProposal[$arBestProposal["PROPOSAL_ID"]] += $arBestProposal["PRICE_NDS"];
                        }
                    }
                    //event TENDERIX_BEST_PROPOSAL <--
                }
                if ($arLot["TYPE_ID"] != "S") {
                    foreach ($arFieldsSpec as $key => $val) {
                        $arFieldsSpec[$key]["PROPOSAL_ID"] = $ID;
                    }

                    CTenderixProposal::AddSpecHistory($arFieldsSpec, $arParams);

                    if (!isset($_REQUEST["PROPOSAL_ID"])) {
                        if (!CTenderixProposal::AddSpec($arFieldsSpec)) {
                            ShowError(GetMessage("PW_TD_EDIT_PROPOSAL"));
                            return;
                        }
                        CTenderixLog::Log("PROPOSAL_ADD", array("ID" => $ID, "FIELDS" => $arFields, "SPEC" => $arFieldsSpec));
                    } else {
                        if (!CTenderixProposal::UpdateSpec($arFieldsSpec)) {
                            ShowError(GetMessage("PW_TD_EDIT_PROPOSAL"));
                            return;
                        }
                        CTenderixLog::Log("PROPOSAL_UPDATE", array("ID" => $ID, "FIELDS" => $arFields, "SPEC" => $arFieldsSpec));
                    }
                    CTenderixStatistic::UpdatePriceForN($LOT_ID);

                    //event TENDERIX_BEST_PROPOSAL -->
                    if ($arLot["OPEN_PRICE"] == "Y") {
                        $rsBestProposal = CTenderixProposal::GetListSpecPriceAll($LOT_ID);
                        while ($arBestProposal = $rsBestProposal->Fetch()) {
                            $arrBestProposal[$arBestProposal["PROPOSAL_ID"]] += $arBestProposal["PRICE_NDS"];
                        }
                    }
                    //event TENDERIX_BEST_PROPOSAL <--
                }

                //event TENDERIX_BEST_PROPOSAL -->
                if ($arLot["OPEN_PRICE"] == "Y") {
                    $arrBestProposalCurr = $arrBestProposal[$ID];
                    unset($arrBestProposal[$ID]);
                    $arrSITE = CTenderixLot::GetSite();
                    $COMPANY = CTenderixCompany::GetByIdName($arLot["COMPANY_ID"]);
                    foreach ($arrBestProposal as $arrBestProposalID => $arrBestProposalPrice) {
                        if ($arrBestProposalPrice > $arrBestProposalCurr) {
                            $rsProposalID = CTenderixProposal::GetList(array("ID" => $arrBestProposalID));
                            if ($arProposalID = $rsProposalID->Fetch()) {
                                $arEventFields = array(
                                    "LOT_NUM" => $arLot["ID"],
                                    "LOT_NAME" => $arLot["TITLE"],
                                    "SUPPLIER" => $arProposalID["LAST_NAME"] . " " . $arProposalID["NAME"] . " " . $arProposalID["SECOND_NAME"],
                                    "COMPANY" => $COMPANY,
                                    "RESPONSIBLE_FIO" => $arLot["RESPONSIBLE_FIO"],
                                    "RESPONSIBLE_PHONE" => $arLot["RESPONSIBLE_PHONE"],
                                    "DATE_START" => $arLot["DATE_START"],
                                    "DATE_END" => $arLot["DATE_END"],
                                    "EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
                                    "EMAIL_TO" => $arProposalID["EMAIL"],
                                );
                                CEvent::Send("TENDERIX_BEST_PROPOSAL", $arrSITE, $arEventFields, "N");
                            }
                        }
                    }
                }
                //event TENDERIX_BEST_PROPOSAL <--
                //event TENDERIX_NEW_PROPOSAL -->
                if ($new_proposal) {
                    $rsBuyer = CTenderixUserBuyer::GetByID($arLot["BUYER_ID"]);
                    $arBuyer = $rsBuyer->Fetch();

                    $rsSupplier = CTenderixUserSupplier::GetByID($USER->GetID());
                    if ($arSupplier = $rsSupplier->Fetch()) {
                        $arEventFields = array(
                            "LOT_NUM" => $arLot["ID"],
                            "LOT_NAME" => $arLot["TITLE"],
                            "BUYER" => $arBuyer["FIO"],
                            "COMPANY" => $arSupplier["NAME_COMPANY"],
                            "DATE_START" => $arFields["DATE_START"],
                            "EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
                            "EMAIL_TO" => $arBuyer["EMAIL"],
                        );
                        $arrSITE = CTenderixLot::GetSite();
                        CEvent::Send("TENDERIX_NEW_PROPOSAL", $arrSITE, $arEventFields, "N");
                    }
                }
                //event TENDERIX_NEW_PROPOSAL <--
            }

            $sRedirectUrl = $APPLICATION->GetCurPage() . "?LOT_ID=" . $LOT_ID;

            if (!$ex = $APPLICATION->GetException()) {
                $_SESSION["SEND_OK"] = "Y";
                LocalRedirect($sRedirectUrl);
                exit();
            }
        }
    }


    if ($ex = $APPLICATION->GetException()) {
        $e = new CAdminException();
        $arResult["ERRORS_ARRAY"] = $ex->GetMessages();
        $arResult["ERRORS"] = $ex->GetString();
    } elseif ($_SESSION["SEND_OK"] == "Y") {
        unset($_SESSION["SEND_OK"]);
        $arResult["SEND_OK"] = "Y";
    }


    $arParams["LOT_ID"] = intval($arParams["~LOT_ID"]);

    $arFilter = array(
        "ID" => $arParams["LOT_ID"]
    );
    $res = CTenderixLot::GetList($by = "", $order = "", $arFilter);

    if ($arLots = $res->GetNext()) {
        if ($S_RIGHT == "A" && $arLots["TYPE_ID"] != "P" && $T_RIGHT != "W") {
            ShowError(GetMessage("ACCESS_DENIED"));
            return;
        }

        $time_start = strtotime($arLots["DATE_START"]);
        if ($time_start > $timeZone) {
            ShowError(GetMessage("PW_TD_DATE_START_LOT") . ": " . $arLots["DATE_START"]);
            return;
        }

        //$time_end = strtotime($arLots["DATE_END"]) + intval($arLots["TIME_EXTENSION"]);
        $time_end = strtotime($arLots["DATE_END"]);
        $time_diff = $time_end - $timeZone; //echo $time_diff." ".$timeZone." ".CTimeZone::GetOffset();
        if ($time_end < $timeZone) {
            ShowError(GetMessage("PW_TD_DATE_END_LOT"));
            return;
        }

        if ($arParams["JQUERY"] == "Y") {
            $APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/pweb.tenderix/jquery.js"></script>', true);
        }
        //$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/components/pweb.tenderix/proposal.add/ajax.php"></script>', true);
        //$APPLICATION->AddHeadScript(substr(dirname(__FILE__), strpos(__FILE__, "/bitrix/")) . "/ajax.php");

        echo '<input type="hidden" id="lot_id" value="' . $arLots["ID"] . '" />';
        echo '<input type="hidden" id="time_diff" value="' . $time_diff . '" />';

        $arResult["LOT"] = $arLots;

        $rsFile = CTenderixLot::GetFileList($arLots["ID"]);
        $arrFile = array();
        while ($arFile = $rsFile->Fetch()) {
            $arrFile[] = $arFile;
        }
        $arResult["LOT"]["FILE"] = $arrFile;

        $rsPayment = CTenderixSprDetails::GetList($by = "", $order = "", $arFilter = Array("ID" => $arLots["TERM_PAYMENT_ID"]), $is_filtered);
        $arPayment = $rsPayment->Fetch();
        $arResult["PAYMENT"] = $arPayment["TITLE"];

        $rsDelivery = CTenderixSprDetails::GetList($by = "", $order = "", $arFilter = Array("ID" => $arLots["TERM_DELIVERY_ID"]), $is_filtered);
        $arDelivery = $rsDelivery->Fetch();
        $arResult["DELIVERY"] = $arDelivery["TITLE"];

        //���� ���� �����������
        $proposalFlag = false;
        $rsProposal = CTenderixProposal::GetList(array("LOT_ID" => $arParams["LOT_ID"], "USER_ID" => $USER->GetID()));
        if ($arProposal = $rsProposal->Fetch()) {
            $proposalFlag = true;
            $arResult["PROPOSAL_ID"] = $arProposal["ID"];
            $arResult["CURRENCY_PROPOSAL"] = $arProposal["CURRENCY"];
            $arResult["MESSAGE"] = $arProposal["MESSAGE"];
            $curr_list = CTenderixCurrency::GetListProposal(strtotime($arProposal["DATE_START"]));

            if (strlen($arProposal["TERM_PAYMENT_VAL"]) > 0) {
                $arResult["LOT"]["TERM_PAYMENT_VAL"] = $arProposal["TERM_PAYMENT_VAL"];
            }
            if (strlen($arProposal["TERM_DELIVERY_VAL"]) > 0) {
                $arResult["LOT"]["TERM_DELIVERY_VAL"] = $arProposal["TERM_DELIVERY_VAL"];
            }

            if ($arLots["TYPE_ID"] == "S") {
                $arFieldsPropertyProducts = array();
                $rsPropertyProducts = CTenderixProposal::GetListPropertyProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
                while ($arPropertyProducts = $rsPropertyProducts->Fetch()) {
                    $arFieldsPropertyProducts[$arPropertyProducts["PRODUCTS_PROPERTY_BUYER_ID"]] = $arPropertyProducts["VALUE"];
                }
                $arResult["PROPOSAL_PROPERTY_PRODUCTS"] = $arFieldsPropertyProducts;

                $arFieldsProducts = array();
                $rsProducts = CTenderixProposal::GetListProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
                $arProducts = $rsProducts->Fetch();
                $arProducts["PRICE_NDS"] = floatval($arProducts["PRICE_NDS"]) / (floatval($curr_list[$arProposal["CURRENCY"]]) <= 0 ? 1 : floatval($curr_list[$arProposal["CURRENCY"]]));
                $arResult["PROPOSAL_PRODUCTS"] = $arProducts;
            }
            if ($arLots["TYPE_ID"] != "S") {
                $arFieldsSpec = array();
                $rsProposalSpec = CTenderixProposal::GetListSpec(array("PROPOSAL_ID" => $arProposal["ID"]));
                while ($arProposalSpec = $rsProposalSpec->Fetch()) {
                    $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["PROPERTY_BUYER_ID"] = $arProposalSpec["PROPERTY_BUYER_ID"];
                    $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["NDS"] = $arProposalSpec["NDS"];
                    $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["PRICE_NDS"] = floatval($arProposalSpec["PRICE_NDS"]) / (floatval($curr_list[$arProposal["CURRENCY"]]) <= 0 ? 1 : floatval($curr_list[$arProposal["CURRENCY"]]));
                    $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["ANALOG"] = $arProposalSpec["ANALOG"];
                }
                $arResult["PROPOSAL_SPEC"] = $arFieldsSpec;
            }
        } else {
            $arResult["CURRENCY_PROPOSAL"] = $arLots["CURRENCY"];
            //$arResult["CURRENCY_PROPOSAL"] = $arParams["CURR"];
        }

        //File result
        $rsFiles = CTenderixProposal::GetFileList($arProposal["ID"]);
        while ($arFile = $rsFiles->GetNext()) {
            $arResult["INFO"]["FILE"][] = $arFile;
        }

        if ($arLots["TYPE_ID"] == "S") {
            $rsProdBuyer = CTenderixProducts::GetListBuyer(array("LOT_ID" => $arLots["ID"]));
            $arProdBuyer = $rsProdBuyer->Fetch();

            $rsProd = CTenderixProducts::GetList($by, $order, array("ID" => $arProdBuyer["PRODUCTS_ID"]), $is_filtered);
            $arProd = $rsProd->Fetch();

            $rsProdProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $arProdBuyer["PRODUCTS_ID"]), $is_filtered);
            while ($arProdProps = $rsProdProps->GetNext()) {
                $rsProps2 = CTenderixProductsProperty::GetListBuyer(Array("PRODUCTS_ID" => $arProdBuyer["ID"], "PRODUCTS_PROPERTY_ID" => $arProdProps["ID"]));
                $arProps2 = $rsProps2->Fetch();
                $arrPropProduct[$arProdProps["ID"]] = $arProdProps;
                $arrPropProductBuyer[$arProps2["PRODUCTS_PROPERTY_ID"]] = $arProps2;
            }
            $arResult["PROPERTY_PRODUCT"] = $arrPropProduct;
            $arResult["PROPERTY_PRODUCT_BUYER"] = $arrPropProductBuyer;
            if ($proposalFlag)
                $arProdBuyer["START_PRICE"] = floatval($arProdBuyer["START_PRICE"]) / floatval($curr_name[$arResult["CURRENCY_PROPOSAL"]]);
            else
                $arProdBuyer["START_PRICE"] = floatval($arProdBuyer["START_PRICE"]);
            $arProdBuyer["STEP_PRICE"] = floatval($arProdBuyer["STEP_PRICE"]) * floatval($curr_name[$arResult["LOT"]["CURRENCY"]]);
            $arResult["PRODUCT_BUYER"] = $arProdBuyer;
            $arResult["PRODUCT"] = $arProd;
        }
        if ($arLots["TYPE_ID"] != "S") {
            $arResult["SPEC"] = CTenderixLotSpec::GetByLotId($arLots["ID"]);

            $rsProp = CTenderixLotSpec::GetListProp($arLots["ID"]);
            while ($arProp = $rsProp->Fetch()) {
                if ($proposalFlag) {
                    $arProp["START_PRICE"] = floatval($arProp["START_PRICE"]) / floatval($curr_name[$arResult["CURRENCY_PROPOSAL"]]);
                } else {
                    $arProp["START_PRICE"] = floatval($arProp["START_PRICE"]);
                }
                $arProp["STEP_PRICE"] = floatval($arProp["STEP_PRICE"]) * floatval($curr_name[$arResult["LOT"]["CURRENCY"]]);
                $arResult["PROPERTY_SPEC"][] = $arProp;
            }
        }

        //dop property 
        $rsPropList = CTenderixProposalProperty::GetList($by = "SORT", $order = "desc");
        if ($arResult["PROPOSAL_ID"] > 0) {
            $arResult["PROP_PROPOSAL"] = CTenderixProposal::GetProperty($arResult["PROPOSAL_ID"]);
        }
        $arParams["PROPERTY"] = $arLots["TYPE_ID"] == "P" ? $arParams["PROPERTY2"] : $arParams["PROPERTY"];
        $arParams["PROPERTY_REQUIRED"] = $arLots["TYPE_ID"] == "P" ? $arParams["PROPERTY_REQUIRED2"] : $arParams["PROPERTY_REQUIRED"];
        while ($arPropList = $rsPropList->GetNext()) {
            //if ($arPropList["ACTIVE"] == "N")
            //   continue;
            if (in_array("PROP_" . $arPropList["ID"], $arParams["PROPERTY"]) || !isset($arParams["PROPERTY"])) {
                if (isset($arParams["PROPERTY"])) {
                    $arPropList["IS_REQUIRED"] = in_array("PROP_" . $arPropList["ID"], $arParams["PROPERTY_REQUIRED"]) ? "Y" : "N";
                }
                $arResult["PROP_LIST"][] = $arPropList;
            }
        }
    } else {
        ShowError(GetMessage("PW_TD_LOT_NOTFOUND"));
        return;
    }

    $res->arResult = Array();
    unset($arLots);

    $this->IncludeComponentTemplate();
} else {
    ShowError(GetMessage("ACCESS_DENIED"));
    return;
}
?>
