<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

global $CACHE_MANAGER, $DB, $USER;
$module_id = "pweb.tenderix";

if (!CModule::IncludeModule("pweb.tenderix")) {
    $this->AbortResultCache();
    ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
    return;
}

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
$arResult["T_RIGHT"] = $T_RIGHT;

if ($T_RIGHT < "S") {
    ShowError(GetMessage("ACCESS_DENIED"));
    return;
}

if ($arParams["JQUERY"] == "Y") {
    $APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/pweb.tenderix/jquery.js"></script>', true);
}

$ID = intval($_REQUEST["ID"]);
$TYPE_ID = $_REQUEST["TYPE_ID"];
$PRODUCTS_ID = $_REQUEST["PRODUCTS_ID"];

if ($ID <= 0) {
    $arResult["LOT"]["ACTIVE"] = "Y";
    $arResult["LOT"]["TIME_EXTENSION"] = 0;
    $arResult["LOT"]["TIME_UPDATE"] = 600;
    $arResult["SPEC_NEW_PROP"] = 0;
}

$rsUser = CTenderixUserBuyer::GetByID($USER->GetID());
$arUser = $rsUser->Fetch();
$arResult["LOT"]["COMPANY_ID"] = $arUser["COMPANY_ID"];
$arResult["LOT"]["RESPONSIBLE_FIO"] = $arUser["LAST_NAME"] . " " . $arUser["NAME"] . " " . $arUser["SECOND_NAME"];

if (isset($_REQUEST["lotadd_submit"]) && $T_RIGHT >= "S") {
    $arFields = array(
        "ACTIVE" => ($_REQUEST["ACTIVE"] == "Y" ? "Y" : "N"),
        "SECTION_ID" => intval($_REQUEST["SECTION_ID"]),
        "TYPE_ID" => $_REQUEST["TYPE_ID"],
        "TIMESTAMP_X" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), time() + CTimeZone::GetOffset()),
        "DATE_START" => $_REQUEST["DATE_START"],
        "DATE_END" => $_REQUEST["DATE_END"],
        "NOTE" => $_REQUEST["NOTE"],
        "OPEN_PRICE" => ($_REQUEST["OPEN_PRICE"] == "Y" ? "Y" : "N"),
        "TIME_UPDATE" => (intval($_REQUEST["TIME_UPDATE"]) > 0 ? $_REQUEST["TIME_UPDATE"] : "600"),
        "DATE_DELIVERY" => $_REQUEST["DATE_DELIVERY"],
        "RESPONSIBLE_FIO" => $_REQUEST["RESPONSIBLE_FIO"],
        "TIME_EXTENSION" => $_REQUEST["TIME_EXTENSION"],
        "RESPONSIBLE_PHONE" => $_REQUEST["RESPONSIBLE_PHONE"],
        "TERM_PAYMENT_ID" => $_REQUEST["TERM_PAYMENT_ID"],
        "TERM_PAYMENT_VAL" => ($_REQUEST["TERM_PAYMENT_ID"] == 0 ? "" : $_REQUEST["TERM_PAYMENT_VAL"]),
        "TERM_PAYMENT_REQUIRED" => ($_REQUEST["TERM_PAYMENT_ID"] == 0 ? "N" : ($_REQUEST["TERM_PAYMENT_REQUIRED"] == "Y" ? "Y" : "N")),
        "TERM_PAYMENT_EDIT" => ($_REQUEST["TERM_PAYMENT_ID"] == 0 ? "N" : ($_REQUEST["TERM_PAYMENT_EDIT"] == "Y" ? "Y" : "N")),
        "TERM_DELIVERY_ID" => $_REQUEST["TERM_DELIVERY_ID"],
        "TERM_DELIVERY_VAL" => ($_REQUEST["TERM_DELIVERY_ID"] == 0 ? "" : $_REQUEST["TERM_DELIVERY_VAL"]),
        "TERM_DELIVERY_REQUIRED" => ($_REQUEST["TERM_DELIVERY_ID"] == 0 ? "N" : ($_REQUEST["TERM_DELIVERY_REQUIRED"] == "Y" ? "Y" : "N")),
        "TERM_DELIVERY_EDIT" => ($_REQUEST["TERM_DELIVERY_ID"] == 0 ? "N" : ($_REQUEST["TERM_DELIVERY_EDIT"] == "Y" ? "Y" : "N")),
        "PRIVATE" => $_REQUEST["PRIVATE"] == "Y" ? "Y" : "N",
        "NOTVISIBLE_PROPOSAL" => $_REQUEST["NOTVISIBLE_PROPOSAL"] == "Y" ? "Y" : "N",
        "PRIVATE_LIST" => $_REQUEST["PRIVATE_LIST"],
        "WITH_NDS" => $_REQUEST["WITH_NDS"],
        "CURRENCY" => $_REQUEST["CURRENCY"],
    );

    if ($arParams["COMPANY_ONLY"] == "Y" && $ID <= 0) {
        $arFields["COMPANY_ID"] = $arUser["COMPANY_ID"];
    } elseif ($arParams["COMPANY_ONLY"] == "Y" && $ID > 0) {
        $res = CTenderixLot::GetByIDa($ID);
        $arLot = $res->Fetch();
        $arFields["COMPANY_ID"] = $arLot["COMPANY_ID"];
    } elseif (isset($_REQUEST["COMPANY_ID"])) {
        $arFields["COMPANY_ID"] = intval($_REQUEST["COMPANY_ID"]);
    } 

    if ($TYPE_ID != 'S') {
        $arFields["TITLE"] = $_REQUEST["TITLE"];
    } else {
        if ($PRODUCTS_ID > 0) {
            $rsTovar = CTenderixProducts::GetByID($_REQUEST["PRODUCTS_ID"]);
            $arTovar = $rsTovar->Fetch();
            $arFields["TITLE"] = $arTovar["TITLE"];
            $arFields["SECTION_ID"] = $arTovar["SECTION_ID"];
        } else {
            $arFields["TITLE"] = "notitle";
        }
    }

    if ($TYPE_ID != "S") {
        if ($ID > 0) {
            $rsSpecProp = CTenderixLotSpec::GetListProp($ID);
            while ($arSpecProp = $rsSpecProp->GetNext()) {
                if ($_REQUEST["PROP_" . $arSpecProp["ID"] . "_DEL"] == "Y") {
                    if (!CTenderixLotSpec::DeletePropID($arSpecProp["ID"])) {
                        $message = Array("MESSAGE" => GetMessage("PROP_DELETE_ERROR"));
                        $bInitVars = true;
                    }
                }
                $arFieldsDop[] = array(
                    "TITLE" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_TITLE"],
                    "ADD_INFO" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_ADD_INFO"],
                    "COUNT" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_COUNT"],
                    "UNIT_ID" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_UNIT_ID"],
                    "START_PRICE" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_START_PRICE"],
                    "STEP_PRICE" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_STEP_PRICE"],
                );
                $arFieldsDopUpdate[$arSpecProp["ID"]] = array(
                    "TITLE" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_TITLE"],
                    "ADD_INFO" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_ADD_INFO"],
                    "COUNT" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_COUNT"],
                    "UNIT_ID" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_UNIT_ID"],
                    "START_PRICE" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_START_PRICE"],
                    "STEP_PRICE" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_STEP_PRICE"],
                );
            }
        }

        for ($i = 0; $i < $_REQUEST["newProp"] + 1; $i++) {
            if (strlen($_REQUEST["PROP_n" . $i . "_TITLE"]) <= 0 ||
                    strlen($_REQUEST["PROP_n" . $i . "_COUNT"]) <= 0 ||
                    strlen($_REQUEST["PROP_n" . $i . "_UNIT_ID"]) <= 0)
                continue;
            $arFieldsDop[] = array(
                "TITLE" => $_REQUEST["PROP_n" . $i . "_TITLE"],
                "ADD_INFO" => $_REQUEST["PROP_n" . $i . "_ADD_INFO"],
                "COUNT" => $_REQUEST["PROP_n" . $i . "_COUNT"],
                "UNIT_ID" => $_REQUEST["PROP_n" . $i . "_UNIT_ID"],
                "START_PRICE" => $_REQUEST["PROP_n" . $i . "_START_PRICE"],
                "STEP_PRICE" => $_REQUEST["PROP_n" . $i . "_STEP_PRICE"],
            );
            $arFieldsDopNew[] = array(
                "TITLE" => $_REQUEST["PROP_n" . $i . "_TITLE"],
                "ADD_INFO" => $_REQUEST["PROP_n" . $i . "_ADD_INFO"],
                "COUNT" => $_REQUEST["PROP_n" . $i . "_COUNT"],
                "UNIT_ID" => $_REQUEST["PROP_n" . $i . "_UNIT_ID"],
                "START_PRICE" => $_REQUEST["PROP_n" . $i . "_START_PRICE"],
                "STEP_PRICE" => $_REQUEST["PROP_n" . $i . "_STEP_PRICE"],
            );
        }
    }

    if ($TYPE_ID == "S") {
        $arFieldsDop["PRODUCTS"] = array(
            "PRODUCTS_ID" => $_REQUEST["PRODUCTS_ID"],
            "START_PRICE" => $_REQUEST["START_PRICE"],
            "STEP_PRICE" => $_REQUEST["STEP_PRICE"],
            "COUNT" => $_REQUEST["COUNT"],
            "COUNT_EDIT" => ($_REQUEST["COUNT_EDIT"] == "Y" ? "Y" : "N")
        );
        if ($ID > 0) {
            $rsProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $PRODUCTS_ID), $is_filtered);
            $rsProdBuyer = CTenderixProducts::GetListBuyer($arFilter = Array("LOT_ID" => $ID));
            $arProdBuyer = $rsProdBuyer->Fetch();
            while ($arProps = $rsProps->Fetch()) {
                $rsPropsBuyer = CTenderixProductsProperty::GetListBuyer(array("PRODUCTS_ID" => $arProdBuyer["ID"], "PRODUCTS_PROPERTY_ID" => $arProps["ID"]));
                $arPropsBuyer = $rsPropsBuyer->Fetch();
                $arFieldsDop["PRODUCTS_PROPERTY"][] = array(
                    "PRODUCTS_PROPERTY_BUYER" => $arPropsBuyer["ID"],
                    "VALUE" => $_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_VALUE"],
                    "REQUIRED" => ($_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_REQUIRED"] == "Y" ? "Y" : "N"),
                    "EDIT" => ($_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_EDIT"] == "Y" ? "Y" : "N"),
                    "VISIBLE" => ($_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_VISIBLE"] == "Y" ? "Y" : "N"),
                );
            }
        } else {
            $rsProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $PRODUCTS_ID), $is_filtered);
            while ($arProps = $rsProps->Fetch()) {
                $arFieldsDop["PRODUCTS_PROPERTY"][] = array(
                    "PRODUCTS_PROPERTY_ID" => $arProps["ID"],
                    "VALUE" => $_REQUEST["PROP_PROD_" . $arProps["ID"] . "_VALUE"],
                    "REQUIRED" => ($_REQUEST["PROP_PROD_" . $arProps["ID"] . "_REQUIRED"] == "Y" ? "Y" : "N"),
                    "EDIT" => ($_REQUEST["PROP_PROD_" . $arProps["ID"] . "_EDIT"] == "Y" ? "Y" : "N"),
                    "VISIBLE" => ($_REQUEST["PROP_PROD_" . $arProps["ID"] . "_VISIBLE"] == "Y" ? "Y" : "N"),
                );
            }
        }
    }

    $res_lot = 0;
    if ($ID > 0) {
        if (CTenderixLot::CheckFields("UPDATE", $arFields, $arFieldsDop)) {
            $res_lot = CTenderixLot::Update($ID, $arFields);
            if ($TYPE_ID != 'S' && intval($res_lot) > 0) {
                //Update NS lot
                $arFieldsSpec = array(
                    "FULL_SPEC" => ($_REQUEST["FULL_SPEC"] == "Y" ? "Y" : "N"),
                    "NOT_ANALOG" => ($_REQUEST["NOT_ANALOG"] == "Y" ? "Y" : "N")
                );
                $res = CTenderixLotSpec::Update($ID, $arFieldsSpec);

                $SPEC_ID = intval($res);
                if ($SPEC_ID > 0) {
                    foreach ($arFieldsDopUpdate as $arSpecPropId => $arFieldsProp) {
                        $res = CTenderixLotSpec::UpdateProp($arSpecPropId, $arFieldsProp);
                    }

                    foreach ($arFieldsDopNew as $fieldPropNew) {
                        $fieldPropNew["SPEC_ID"] = $SPEC_ID;
                        $res = CTenderixLotSpec::AddProp($fieldPropNew);
                    }
                }
            } elseif ($TYPE_ID == 'S' && intval($res_lot) > 0) {
                //Update S lot
                $arFieldsProduct = $arFieldsDop["PRODUCTS"];
                $res = CTenderixLotProduct::Update($ID, $arFieldsProduct);

                $PRODUCT_ID_BUYER = intval($res);
                if ($PRODUCT_ID_BUYER > 0) {
                    foreach ($arFieldsDop["PRODUCTS_PROPERTY"] as $arFieldsProductProps) {
                        $arFieldsProductProps["PRODUCTS_ID"] = $PRODUCT_ID_BUYER;
                        $res = CTenderixLotProduct::UpdateProp($arFieldsProductProps["PRODUCTS_PROPERTY_BUYER"], $arFieldsProductProps);
                    }
                }
            }
        }
    } else {
        $arFields["BUYER_ID"] = $USER->GetID();
        if (CTenderixLot::CheckFields("ADD", $arFields, $arFieldsDop)) {
            $res_lot = CTenderixLot::Add($arFields);
            $ID = intval($res_lot);
            $CACHE_MANAGER->ClearByTag('pweb.tenderix_user.info_' . $USER->GetID());
            if ($TYPE_ID != 'S' && intval($res_lot) > 0) {
                $arFieldsSpec = array(
                    "FULL_SPEC" => ($_REQUEST["FULL_SPEC"] == "Y" ? "Y" : "N"),
                    "NOT_ANALOG" => ($_REQUEST["NOT_ANALOG"] == "Y" ? "Y" : "N"),
                    "LOT_ID" => intval($res_lot)
                );
                $res = CTenderixLotSpec::Add($arFieldsSpec);

                $SPEC_ID = intval($res);
                if ($SPEC_ID > 0) {
                    foreach ($arFieldsDop as $fieldPropNew) {
                        $fieldPropNew["SPEC_ID"] = $SPEC_ID;
                        $res = CTenderixLotSpec::AddProp($fieldPropNew);
                    }
                }
            } elseif ($TYPE_ID == 'S' && intval($res_lot) > 0) {
                $arFieldsProduct = $arFieldsDop["PRODUCTS"];
                $arFieldsProduct["LOT_ID"] = intval($res_lot);

                $res = CTenderixLotProduct::Add($arFieldsProduct);
                $PRODUCT_ID_BUYER = intval($res);
                if ($PRODUCT_ID_BUYER > 0) {
                    foreach ($arFieldsDop["PRODUCTS_PROPERTY"] as $arFieldsProductProps) {
                        $arFieldsProductProps["PRODUCTS_ID"] = $PRODUCT_ID_BUYER;
                        $res = CTenderixLotProduct::AddProp($arFieldsProductProps);
                    }
                }
            }
        }
    }

    //Add Files
    if (intval($res_lot) > 0) {
        //Delete
        if (is_array($_REQUEST["FILE_ID"]))
            foreach ($_REQUEST["FILE_ID"] as $file)
                CTenderixLot::DeleteFile($ID, $file);

        //New files
        $arFiles = array();

        //Brandnew
        if (is_array($_FILES["NEW_FILE"]))
            foreach ($_FILES["NEW_FILE"] as $attribute => $files)
                if (is_array($files))
                    foreach ($files as $index => $value)
                        $arFiles[$index][$attribute] = $value;


        foreach ($arFiles as $file) {

            if (strlen($file["name"]) > 0 && intval($file["size"]) > 0) {
                $res = CTenderixLot::SaveFile($ID, $file);
                if (!$res)
                    break;
            }
        }
    }

    $sRedirectUrl = $APPLICATION->GetCurPage() . "?ID=" . $ID;
    if (!$ex = $APPLICATION->GetException()) {
        LocalRedirect($sRedirectUrl);
        exit();
    }
}

if ($ex = $APPLICATION->GetException()) {
    $e = new CAdminException();
    $arResult["ERRORS_ARRAY"] = $ex->GetMessages();
    $arResult["ERRORS"] = $ex->GetString();
}

if ($ID > 0) {
    $arFilterLot["ID"] = $ID;
    /* if ($T_RIGHT != "W") {
      $arFilterLot["BUYER_ID"] = $USER->GetID();
      } */

    $db_lot = CTenderixLot::GetList($by = "", $order = "", $arFilterLot);
    if ($arLot = $db_lot->Fetch()) {
        $arResult["LOT"] = $arLot;
    }

    if ($arResult["LOT"]["BUYER_ID"] != $USER->GetID() && $T_RIGHT != "W" && !in_array($arResult["LOT"]["BUYER_ID"], unserialize($arUser["USER_BIND"]))) {
        ShowError(GetMessage("ACCESS_DENIED"));
        return;
    }

    //private lot -->
    if ($arLot["PRIVATE"] == "Y") {
        $rsRes = CTenderixLot::GetUserPrivateLot($ID);
        while ($arRes = $rsRes->Fetch()) {
            $arSupplierSelect[] = $arRes["USER_ID"];
        }
        $rsSupplier = CTenderixUserSupplier::GetListUser(array("NAME_COMPANY" => "ASC"), array());
        while ($arSupplier = $rsSupplier->Fetch()) {
            $arResult["LOT"]["PRIVATE_USER"][] = array(
                "company" => $arSupplier["NAME_COMPANY"],
                "id" => $arSupplier["USER_ID"]
            );
            if (in_array($arSupplier["USER_ID"], $arSupplierSelect)) {
                $arResult["LOT"]["PRIVATE_LIST"][] = array(
                    "company" => $arSupplier["NAME_COMPANY"],
                    "id" => $arSupplier["USER_ID"]
                );
            }
        }
    }
    //private lot <--

    $TYPE_ID = $arResult["LOT"]["TYPE_ID"];
    if ($TYPE_ID != 'S') {
        $db_lot_spec = CTenderixLotSpec::GetListSpec($by = "", $order = "", array("LOT_ID" => $ID), $is_filtered);
        if ($arLotSpec = $db_lot_spec->Fetch()) {
            $arResult["LOT"] = array_merge($arLotSpec, $arResult["LOT"]);
        }
        $db_lot_spec_prop = CTenderixLotSpec::GetListProp($ID);
        while ($arLotSpecProp = $db_lot_spec_prop->Fetch()) {
            $arResult["LOT"]["SPEC"][] = $arLotSpecProp;
        }
    }
    if ($TYPE_ID == 'S') {
        $db_lot_prod = CTenderixProducts::GetListBuyer(array("LOT_ID" => $ID));
        if ($arLotProd = $db_lot_prod->Fetch()) {
            $arResult["LOT"]["TOVAR_BUYER"] = $arLotProd;
            $arResult["PRODUCTS_ID"] = $arResult["LOT"]["TOVAR_BUYER"]["PRODUCTS_ID"];
        }
        $db_lot_prod_prop = CTenderixProductsProperty::GetListBuyer(array("ACTIVE" => "Y", "PRODUCTS_ID" => $arResult["LOT"]["TOVAR_BUYER"]["ID"]));
        while ($arLotProdProps = $db_lot_prod_prop->Fetch()) {
            $arResult["LOT"]["TOVAR_BUYER"]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]] = $arLotProdProps;
        }
    }

    $rsFiles = CTenderixLot::GetFileList($ID);
    while ($arFile = $rsFiles->GetNext()) {
        $arResult["LOT"]["FILE"][] = $arFile;
    }
}

$arResult["LOT"]["CURRENCY_ARRAY"] = array();
if (CModule::IncludeModule("currency")) {
    $lcur = CCurrency::GetList(($b = "sort"), ($order1 = "asc"), LANGUAGE_ID);
    while ($lcur_res = $lcur->Fetch()) {
        $rsCur = CCurrencyRates::GetList($by = "DATE_RATE", $order = "desc", $arFilter = Array("CURRENCY" => $lcur_res["CURRENCY"]));
        $arCur = $rsCur->Fetch();
        $arResult["LOT"]["CURRENCY_ARRAY"][$lcur_res["CURRENCY"]] = $arCur["RATE"] > 0 ? CurrencyFormat($arCur["RATE"], $lcur_res["CURRENCY"]) : "";
    }
}

$arResult["TYPE_URL"]["N"] = $APPLICATION->GetCurPageParam("TYPE_ID=N", array("TYPE_ID"));
$arResult["TYPE_URL"]["S"] = $APPLICATION->GetCurPageParam("TYPE_ID=S", array("TYPE_ID"));
$arResult["TYPE_URL"]["P"] = $APPLICATION->GetCurPageParam("TYPE_ID=P", array("TYPE_ID"));
if (isset($_REQUEST["TYPE_ID"])) {
    $arResult["TYPE_ID"] = $_REQUEST["TYPE_ID"];
} else {
    $arResult["TYPE_ID"] = isset($arResult["LOT"]["TYPE_ID"]) ? $arResult["LOT"]["TYPE_ID"] : "N";
}

$rsCatalog = CTenderixSection::GetCatalogList($by = "id", $order = "asc", array("ACTIVE"=>"Y"));
while ($arCatalog = $rsCatalog->GetNext()) {
    $arCat[$arCatalog["CATALOG_ID"]][] = $arCatalog;
} 
$arResult["CATALOG"] = CTenderixSection::BuildTree($arCat, 0, 0);

$rsSection = CTenderixSection::GetList($by = "s_c_sort", $order = "asc", array("ACTIVE"=>"Y"));
while ($arSection = $rsSection->Fetch()) {
    $arResult["SECTION"][$arSection["ID"]] = $arSection["TITLE"];
    $arResult["SECTION_ARR"][$arSection["CATALOG_ID"]][] = $arSection;
}

$rsCompany = CTenderixCompany::GetList($by = "s_title", $order = "desc", array(), $is_filtered);
while ($arCompany = $rsCompany->Fetch()) {
    $arResult["COMPANY"][$arCompany["ID"]] = $arCompany["TITLE"];
}
//N tovar
if ($arResult["TYPE_ID"] != "S") {
    $rsUnit = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_UNIT")), $is_filtered);
    while ($arUnit = $rsUnit->GetNext()) {
        $arResult["UNIT"][$arUnit["ID"]] = $arUnit["TITLE"];
    }
}
//S tovar
if ($arResult["TYPE_ID"] == "S") {
    $rsProducts = CTenderixProducts::GetList();
    $arResult["PRODUCTS"][0] = "--";
    $arResult["PRODUCTS_URL"][0] = $APPLICATION->GetCurPageParam("PRODUCTS_ID=0", array("PRODUCTS_ID"));
    while ($arProducts = $rsProducts->Fetch()) {
        $arResult["PRODUCTS"][$arProducts["ID"]] = $arProducts["TITLE"];
        $arResult["PRODUCTS_URL"][$arProducts["ID"]] = $APPLICATION->GetCurPageParam("PRODUCTS_ID=" . $arProducts["ID"], array("PRODUCTS_ID"));
    }
    if (isset($_REQUEST["PRODUCTS_ID"]) && !isset($arResult["PRODUCTS_ID"])) {
        $arResult["PRODUCTS_ID"] = $_REQUEST["PRODUCTS_ID"];
    }

    $rsProd = CTenderixProducts::GetList($by = "", $order = "", array("ID" => $arResult["PRODUCTS_ID"]));
    if ($arProd = $rsProd->Fetch()) {
        $arResult["LOT"]["TOVAR"] = $arProd;
    }
    $rsProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("ACTIVE" => "Y", "PRODUCTS_ID" => $arResult["PRODUCTS_ID"]), $is_filtered);
    while ($arProps = $rsProps->Fetch()) {
        if (isset($arResult["LOT"]["TOVAR_BUYER"]) && !isset($arResult["LOT"]["TOVAR_BUYER"]["PROP"][$arProps["ID"]])) {
            continue;
        }
        $arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]] = $arProps;
        if (intval($arProps["SPR_ID"]) > 0) {
            $rsSpr = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => $arProps["SPR_ID"]), $is_filtered);
            while ($arSpr = $rsSpr->GetNext()) {
                $arrSpr[$arSpr["ID"]] = $arSpr["TITLE"];
            }
            $arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["SPR_ID"] = $arrSpr;
        }
        $arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["VISIBLE"] = "Y";
    }
    //data merge tovar base and tovar buyer -->
    if (isset($arResult["LOT"]["TOVAR_BUYER"])) {
        foreach ($arResult["LOT"]["TOVAR_BUYER"] as $k_TB => $v_TB) {
            if ($k_TB != "PROP") {
                $arResult["LOT"]["TOVAR"][$k_TB] = $v_TB;
            } else {
                foreach ($v_TB as $k_TB_PROP => $v_TB_PROP) {
                    foreach ($v_TB_PROP as $k_TB_PROP_name => $v_TB_PROP_val) {
                        $arResult["LOT"]["TOVAR"]["PROP"][$k_TB_PROP][$k_TB_PROP_name] = $v_TB_PROP_val;
                    }
                }
            }
        }
    }
    //data merge tovar base and tovar buyer <--
}

$rsUnit = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_TERM_DELIVERY")), $is_filtered);
while ($arUnit = $rsUnit->GetNext()) {
    $arResult["DELIVERY"][$arUnit["ID"]] = $arUnit["TITLE"];
}

$rsUnit = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_TERM_PAYMENT")), $is_filtered);
while ($arUnit = $rsUnit->GetNext()) {
    $arResult["PAYMENT"][$arUnit["ID"]] = $arUnit["TITLE"];
}

//save data if error
if ($ex = $APPLICATION->GetException()) {
    $arResult["TYPE_ID"] = $_REQUEST["TYPE_ID"];
    $arResult["LOT"]["TITLE"] = $_REQUEST["TITLE"];
    $arResult["LOT"]["ACTIVE"] = isset($_REQUEST["ACTIVE"]) ? "Y" : "N";
    $arResult["LOT"]["SECTION_ID"] = $_REQUEST["SECTION_ID"];
    $arResult["LOT"]["FULL_SPEC"] = isset($_REQUEST["FULL_SPEC"]) ? "Y" : "N";
    $arResult["LOT"]["NOT_ANALOG"] = isset($_REQUEST["NOT_ANALOG"]) ? "Y" : "N";
    $arResult["LOT"]["OPEN_PRICE"] = isset($_REQUEST["OPEN_PRICE"]) ? "Y" : "N";
    $arResult["LOT"]["COMPANY_ID"] = $_REQUEST["COMPANY_ID"];
    $arResult["LOT"]["RESPONSIBLE_FIO"] = $_REQUEST["RESPONSIBLE_FIO"];
    $arResult["LOT"]["RESPONSIBLE_PHONE"] = $_REQUEST["RESPONSIBLE_PHONE"];
    $arResult["LOT"]["DATE_START"] = $_REQUEST["DATE_START"];
    $arResult["LOT"]["DATE_END"] = $_REQUEST["DATE_END"];
    $arResult["LOT"]["TIME_EXTENSION"] = $_REQUEST["TIME_EXTENSION"];
    $arResult["LOT"]["TIME_UPDATE"] = $_REQUEST["TIME_UPDATE"];
    $arResult["LOT"]["DATE_DELIVERY"] = $_REQUEST["DATE_DELIVERY"];
    $arResult["LOT"]["NOTE"] = $_REQUEST["NOTE"];
    $arResult["LOT"]["TERM_DELIVERY_ID"] = $_REQUEST["TERM_DELIVERY_ID"];
    $arResult["LOT"]["TERM_DELIVERY_VAL"] = $_REQUEST["TERM_DELIVERY_VAL"];
    $arResult["LOT"]["TERM_DELIVERY_REQUIRED"] = isset($_REQUEST["TERM_DELIVERY_REQUIRED"]) ? "Y" : "N";
    $arResult["LOT"]["TERM_DELIVERY_EDIT"] = isset($_REQUEST["TERM_DELIVERY_EDIT"]) ? "Y" : "N";
    $arResult["LOT"]["TERM_PAYMENT_ID"] = $_REQUEST["TERM_PAYMENT_ID"];
    $arResult["LOT"]["TERM_PAYMENT_VAL"] = $_REQUEST["TERM_PAYMENT_VAL"];
    $arResult["LOT"]["TERM_PAYMENT_REQUIRED"] = isset($_REQUEST["TERM_PAYMENT_REQUIRED"]) ? "Y" : "N";
    $arResult["LOT"]["TERM_PAYMENT_EDIT"] = isset($_REQUEST["TERM_PAYMENT_EDIT"]) ? "Y" : "N";
    $arResult["LOT"]["PRIVATE"] = isset($_REQUEST["PRIVATE"]) ? "Y" : "N";
    $arResult["LOT"]["WITH_NDS"] = $_REQUEST["WITH_NDS"] == "Y" ? "Y" : "N";
    $arResult["LOT"]["CURRENCY"] = $_REQUEST["CURRENCY"];

    //private lot -->
    if ($arResult["LOT"]["PRIVATE"] == "Y") {
        unset($arResult["LOT"]["PRIVATE_USER"]);
        unset($arResult["LOT"]["PRIVATE_LIST"]);
        $rsSupplier = CTenderixUserSupplier::GetListUser(array("NAME_COMPANY" => "ASC"), array());
        while ($arSupplier = $rsSupplier->Fetch()) {
            $arResult["LOT"]["PRIVATE_USER"][] = array(
                "company" => $arSupplier["NAME_COMPANY"],
                "id" => $arSupplier["USER_ID"]
            );
            if (in_array($arSupplier["USER_ID"], $_REQUEST["PRIVATE_LIST"])) {
                $arResult["LOT"]["PRIVATE_LIST"][] = array(
                    "company" => $arSupplier["NAME_COMPANY"],
                    "id" => $arSupplier["USER_ID"]
                );
            }
        }
    }
    //private lot <--

    if ($arResult["TYPE_ID"] == "S") {
        $arResult["PRODUCTS_ID"] = $_REQUEST["PRODUCTS_ID"];

        $arResult["LOT"]["TOVAR"]["START_PRICE"] = $_REQUEST["START_PRICE"];
        $arResult["LOT"]["TOVAR"]["STEP_PRICE"] = $_REQUEST["STEP_PRICE"];
        $arResult["LOT"]["TOVAR"]["COUNT"] = $_REQUEST["COUNT"];
        $arResult["LOT"]["TOVAR"]["COUNT_EDIT"] = ($_REQUEST["COUNT_EDIT"] == "Y" ? "Y" : "N");

        if ($ID > 0) {
            $rsProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $PRODUCTS_ID), $is_filtered);
            $rsProdBuyer = CTenderixProducts::GetListBuyer($arFilter = Array("LOT_ID" => $ID));
            $arProdBuyer = $rsProdBuyer->Fetch();
            while ($arProps = $rsProps->Fetch()) {
                $rsPropsBuyer = CTenderixProductsProperty::GetListBuyer(array("PRODUCTS_ID" => $arProdBuyer["ID"], "PRODUCTS_PROPERTY_ID" => $arProps["ID"]));
                $arPropsBuyer = $rsPropsBuyer->Fetch();

                $arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["ID"] = $arPropsBuyer["ID"];
                $arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["VALUE"] = $_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_VALUE"];
                $arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["REQUIRED"] = ($_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_REQUIRED"] == "Y" ? "Y" : "N");
                $arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["EDIT"] = ($_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_EDIT"] == "Y" ? "Y" : "N");
                $arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["VISIBLE"] = ($_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_VISIBLE"] == "Y" ? "Y" : "N");
            }
        } else {
            $rsProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $PRODUCTS_ID), $is_filtered);
            while ($arProps = $rsProps->Fetch()) {
                $arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["ID"] = $arProps["ID"];
                $arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["VALUE"] = $_REQUEST["PROP_PROD_" . $arProps["ID"] . "_VALUE"];
                $arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["REQUIRED"] = ($_REQUEST["PROP_PROD_" . $arProps["ID"] . "_REQUIRED"] == "Y" ? "Y" : "N");
                $arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["EDIT"] = ($_REQUEST["PROP_PROD_" . $arProps["ID"] . "_EDIT"] == "Y" ? "Y" : "N");
                $arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["VISIBLE"] = ($_REQUEST["PROP_PROD_" . $arProps["ID"] . "_VISIBLE"] == "Y" ? "Y" : "N");
            }
        }
    }
    if ($arResult["TYPE_ID"] != "S") {
        for ($i = 0; $i < $_REQUEST["newProp"] + 1; $i++) {
            if (strlen($_REQUEST["PROP_n" . $i . "_TITLE"]) <= 0 ||
                    strlen($_REQUEST["PROP_n" . $i . "_COUNT"]) <= 0 ||
                    strlen($_REQUEST["PROP_n" . $i . "_UNIT_ID"]) <= 0)
                continue;
            $arResult["LOT"]["SPEC"]["n" . $i]["TITLE"] = $_REQUEST["PROP_n" . $i . "_TITLE"];
            $arResult["LOT"]["SPEC"]["n" . $i]["ADD_INFO"] = $_REQUEST["PROP_n" . $i . "_ADD_INFO"];
            $arResult["LOT"]["SPEC"]["n" . $i]["COUNT"] = $_REQUEST["PROP_n" . $i . "_COUNT"];
            $arResult["LOT"]["SPEC"]["n" . $i]["UNIT_ID"] = $_REQUEST["PROP_n" . $i . "_UNIT_ID"];
            $arResult["LOT"]["SPEC"]["n" . $i]["START_PRICE"] = $_REQUEST["PROP_n" . $i . "_START_PRICE"];
            $arResult["LOT"]["SPEC"]["n" . $i]["STEP_PRICE"] = $_REQUEST["PROP_n" . $i . "_STEP_PRICE"];
            $arResult["LOT"]["SPEC"]["n" . $i]["PROP_ID"] = "n" . $i;
        }
        $arResult["SPEC_NEW_PROP"] = $_REQUEST["newProp"];
    }
}
//print_r($arResult);

$this->IncludeComponentTemplate();
?>
