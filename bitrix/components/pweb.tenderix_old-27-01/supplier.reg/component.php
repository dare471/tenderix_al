<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

global $CACHE_MANAGER;

if (!CModule::IncludeModule("pweb.tenderix")) {
    ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
    return;
}

// if ($arParams["JQUERY"] == "Y") {
//     $APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/pweb.tenderix/jquery.js"></script>', true);
// }
if ($arParams["SET_TITLE"] == "Y")
    $APPLICATION->SetTitle(GetMessage("PW_TD_SUPPLIER_REG_TITLE"));

if (!$USER->IsAuthorized()) {
    $APPLICATION->AuthForm("", $show_prolog=true, $show_epilog=true, $not_show_links="N", $do_die=true);
}

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
$S_RIGHT = CTenderixUserSupplierStatus::GetStatusRight();

$arResult["T_RIGHT"] = $T_RIGHT;
$arResult["S_RIGHT"] = $S_RIGHT;


//__($S_RIGHT);
/* if ($T_RIGHT == "D")
  $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED")); */
if ($S_RIGHT == "D" && $T_RIGHT == "P" /*|| $S_RIGHT == "A"*/) {
    ShowError(GetMessage("ACCESS_DENIED"));
    return;
}

/* echo '<pre>';
print_r($arParams); */

$arResult["TYPE"] = isset($_REQUEST["TYPE"]) ? intval($_REQUEST["TYPE"]) : 0;

$ID = $USER->GetID();

$rsUserInfo = CTenderixUserSupplier::GetListUser(array(), array("USER_ID" => $ID));
$arUserInfo = $rsUserInfo->Fetch();

$rsUserInfo = CTenderixUserSupplier::GetList($by = "", $order = "", array("ID" => $ID), $is_filtered = false);

if ($arUserInfo = $rsUserInfo->Fetch()) {
	
    foreach ($arUserInfo as $name => $val) {
        $arResult["INFO"][$name] = $val;
    }

    $rsFiles = CTenderixUserSupplier::GetFileList($ID);
    while ($arFile = $rsFiles->GetNext()) {
        $arResult["INFO"]["FILE"][] = $arFile;
    }

    $arResult["INFO"]["DIRECTION"] = CTenderixUserSupplier::DirectionListArr($ID);
    $arResult["INFO"]["SUBSCRIBE"] = CTenderixUserSupplier::SubscribeListArr($ID);

    $status = CTenderixUserSupplierStatus::GetStatusRight();
    if (!isset($_REQUEST["TYPE"])) {
        $arResult["TYPE"] = $status == "W" ? 0 : 1;
    }
} else {
    $USER_INFO = CUser::GetByID($ID);
    $USER_INFO = $USER_INFO->Fetch();
    foreach ($USER_INFO as $name => $val) {
        $arResult["INFO"][$name] = $val;
    }
}

$arParams["BASE_FIELDS"] = $arParams["FIELDS"];
$arParams["DOP_FIELDS"] = $arParams["FIELDS"];
$arParams["DOP_FIELDS_CODE"] = $arParams["FIELDS"];
$arParams["DOP_FIELDS_LEGALADDRESS"] = $arParams["FIELDS"];
$arParams["DOP_FIELDS_POSTALADDRESS"] = $arParams["FIELDS"];
$arParams["DOP_FIELDS_STATEREG"] = $arParams["FIELDS"];
$arParams["DOP_FIELDS_BANK"] = $arParams["FIELDS"];

if ($arResult["TYPE"] != 0) {
    $arParams["FIELDS"] = $arParams["FIELDS2"];
    $arParams["REG_FIELDS_REQUIRED"] = $arParams["REG_FIELDS_REQUIRED2"];
}
$arParams["DOP_FIELDS_CODE_ACTIVE"] = array_intersect(array("CODE_INN", "CODE_KPP", "CODE_OKVED", "CODE_OKPO"), $arParams["FIELDS"]) ? "Y" : "N";
$arParams["DOP_FIELDS_LEGALADDRESS_ACTIVE"] = array_intersect(array("LEGALADDRESS_REGION", "LEGALADDRESS_CITY", "LEGALADDRESS_INDEX", "LEGALADDRESS_STREET", "LEGALADDRESS_POST"), $arParams["FIELDS"]) ? "Y" : "N";
$arParams["DOP_FIELDS_POSTALADDRESS_ACTIVE"] = array_intersect(array("POSTALADDRESS_REGION", "POSTALADDRESS_CITY", "POSTALADDRESS_INDEX", "POSTALADDRESS_STREET", "POSTALADDRESS_POST", "PHONE", "FAX"), $arParams["FIELDS"]) ? "Y" : "N";
$arParams["DOP_FIELDS_STATEREG_ACTIVE"] = array_intersect(array("STATEREG_PLACE", "STATEREG_DATE", "STATEREG_OGRN", "STATEREG_NDS"), $arParams["FIELDS"]) ? "Y" : "N";
$arParams["DOP_FIELDS_BANK_ACTIVE"] = array_intersect(array("BANKING_NAME", "BANKING_ACCOUNT", "BANKING_ACCOUNTCORR", "BANKING_BIK"), $arParams["FIELDS"]) ? "Y" : "N";
$arParams["DOP_FIELDS_DIRECTION_ACTIVE"] = array_intersect(array("DOP_FIELDS_DIRECTION_ACTIVE"), $arParams["FIELDS"]) ? "Y" : "N";
$arParams["DOP_FIELDS_SUBSCRIBE_ACTIVE"] = array_intersect(array("DOP_FIELDS_SUBSCRIBE_ACTIVE"), $arParams["FIELDS"]) ? "Y" : "N";
$arParams["DOP_FIELDS_DOCUMENT_ACTIVE"] = array_intersect(array("DOP_FIELDS_DOCUMENT_ACTIVE"), $arParams["FIELDS"]) ? "Y" : "N";



if (isset($_REQUEST["reg_submit"])) {
    foreach ($_REQUEST["INFO"] as $name => $val) {
        $arFields[$name] = $val;
    }

    $direction = $arFields["DIRECTION"];
    $subscribe = $arFields["SUBSCRIBE"];
    unset($arFields["DIRECTION"]);
    unset($arFields["SUBSCRIBE"]);

    if ($arResult["TYPE"] != 0) {
        $arParams["FIELDS"] = $arParams["FIELDS2"];
        $arParams["REG_FIELDS_REQUIRED"] = $arParams["REG_FIELDS_REQUIRED2"];
        $arParams["STATUS"] = $arParams["STATUS2"];
    }

    if ($arFields["PASSWORD"] == "" && $arFields["PASSWORD_CONFIRM"] == "") {
        unset($arFields["PASSWORD"]);
        unset($arFields["PASSWORD_CONFIRM"]);
    }
    $arFields["PROPERTY"] = array("PROPERTY" => $_REQUEST["PROP"], "FILES" => $_FILES["PROP"]);
    $arFields["AGREE"] = $_REQUEST["INFO"]["AGREE"];
		
		
	
	
    if ($arUserInfo["USER_ID"] > 0) {
        if (isset($_REQUEST["TYPE"])) {
            $arFields["STATUS"] = $arParams["STATUS"];
        }
		
		//!!!
		if ($S_RIGHT == "W") {
			CTenderixUserSupplier::Update2($ID, $arFields, $arParams["REG_FIELDS_REQUIRED"], array("NAME_COMPANY" => "", "CODE_INN" => "") );
		}
		else {
			CTenderixUserSupplier::Update($ID, $arFields, $arParams["REG_FIELDS_REQUIRED"]);
		}
			
		
    } else {
        $arFields["STATUS"] = $arParams["STATUS"];
        $ID = CTenderixUserSupplier::Add($arFields, $ID, $arParams["REG_FIELDS_REQUIRED"]);
    }

    if ($ID > 0) {
        CTenderixUserSupplier::DirectionAdd($ID, $direction);
        CTenderixUserSupplier::SubscribeAdd($ID, $subscribe);
        //delete file
        if (is_array($_REQUEST["FILE_ID"]))
            foreach ($_REQUEST["FILE_ID"] as $file)
                CTenderixUserSupplier::DeleteFile($ID, $file);
        if (is_array($_REQUEST["FILE_ID_PROP"]))
            foreach ($_REQUEST["FILE_ID_PROP"] as $file)
                CTenderixUserSupplier::DeleteFileProperty($ID, $file);

        if (is_array($_FILES["NEW_FILE"]))
            foreach ($_FILES["NEW_FILE"] as $attribute => $files)
                if (is_array($files))
                    foreach ($files as $index => $value)
                        $arFiles[$index][$attribute] = $value;

        foreach ($arFiles as $file) {

            if (strlen($file["name"]) > 0 && intval($file["size"]) > 0) {
                $res_file = CTenderixUserSupplier::SaveFile($ID, $file);
                if (!$res_file)
                    break;
            }
        }
    }


    if (!$ex = $GLOBALS["APPLICATION"]->GetException()) {
        $CACHE_MANAGER->ClearByTag('pweb.tenderix_user.info_' . $USER->GetID());
        $USER->Authorize($USER->GetID());
        $sRedirectUrl = $APPLICATION->GetCurPage();
        $_SESSION["SEND_OK"] = "Y";
        LocalRedirect($sRedirectUrl);
    }
}

if ($ex = $APPLICATION->GetException()) {
    $arResult["ERRORS"] = $ex->GetString();
    foreach ($_REQUEST["INFO"] as $name => $val) {
        $arResult["INFO"][$name] = $val;
    }
    unset($_REQUEST["INFO"]);
} elseif ($_SESSION["SEND_OK"] == "Y") {
    unset($_SESSION["SEND_OK"]);
    $arResult["SEND_OK"] = "Y";
}

$rsSection = CTenderixSection::GetList($by="", $order = "", $arFilter = Array(), $is_filtered = false);
while ($arSection = $rsSection->GetNext()) {
    $arResult["SECTION"][$arSection["ID"]] = $arSection["TITLE"];
}

//property
$rsPropList = CTenderixUserSupplierProperty::GetList($by = "SORT", $order = "asc");
if ($ID > 0) {
    $arResult["PROP_SUPPLIER"] = CTenderixUserSupplier::GetProperty($ID);
}
while ($arPropList = $rsPropList->GetNext()) {
    if ($arPropList["ACTIVE"] == "N")
        continue;
    if (in_array("PROP_" . $arPropList["ID"], $arParams["FIELDS"])) {
        $arPropList["IS_REQUIRED"] = in_array("PROP_" . $arPropList["ID"], $arParams["REG_FIELDS_REQUIRED"]) ? "Y" : "N";
        $arResult["PROP"][$arPropList["ID"]] = $arPropList;
        if ($arPropList["PROPERTY_TYPE"] == "F" && $ID > 0) {
            $rsFiles = CTenderixUserSupplier::GetFileListProperty($ID, $arPropList["ID"]);
            while ($arFile = $rsFiles->GetNext()) {
                $arResult["PROP"][$arPropList["ID"]]["FILE"][] = $arFile;
            }
        }
    }
}


$this->IncludeComponentTemplate();
?>
