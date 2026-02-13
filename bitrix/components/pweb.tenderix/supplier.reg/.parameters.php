<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix"))
    return;

$rsStatus = CTenderixUserSupplierStatus::GetList($by = "C_SORT", $order = "asc");
//$arFieldsStatus[] = "";
while ($arStatus = $rsStatus->Fetch()) {
    $arFieldsStatus[$arStatus["ID"]] = $arStatus["TITLE"];
}

$arFieldsBase = Array(
    "LAST_NAME" => GetMessage("PW_TD_SUPPLIER_LAST_NAME"),
    "NAME" => GetMessage("PW_TD_SUPPLIER_NAME"),
    "SECOND_NAME" => GetMessage("PW_TD_SUPPLIER_SECOND_NAME"),
);
$arFieldsDop = Array(
    "NAME_COMPANY" => GetMessage("PW_TD_SUPPLIER_NAME_COMPANY"),
    "NAME_DIRECTOR" => GetMessage("PW_TD_SUPPLIER_NAME_DIRECTOR"),
    "NAME_ACCOUNTANT" => GetMessage("PW_TD_SUPPLIER_NAME_ACCOUNTANT"),
);
$arFieldsDopCode = Array(
    "CODE_INN" => GetMessage("PW_TD_SUPPLIER_CODE_INN"),
    "CODE_KPP" => GetMessage("PW_TD_SUPPLIER_CODE_KPP"),
    "CODE_OKVED" => GetMessage("PW_TD_SUPPLIER_CODE_OKVED"),
    "CODE_OKPO" => GetMessage("PW_TD_SUPPLIER_CODE_OKPO"),
);
$arFieldsDopLegaladdress = Array(
    "LEGALADDRESS_REGION" => GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_REGION"),
    "LEGALADDRESS_CITY" => GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_CITY"),
    "LEGALADDRESS_INDEX" => GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_INDEX"),
    "LEGALADDRESS_STREET" => GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_STREET"),
    "LEGALADDRESS_POST" => GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_POST"),
);
$arFieldsDopPostaladdress = Array(
    "POSTALADDRESS_REGION" => GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_REGION"),
    "POSTALADDRESS_CITY" => GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_CITY"),
    "POSTALADDRESS_INDEX" => GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_INDEX"),
    "POSTALADDRESS_STREET" => GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_STREET"),
    "POSTALADDRESS_POST" => GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_POST"),
    "PHONE" => GetMessage("PW_TD_SUPPLIER_PHONE"),
    "FAX" => GetMessage("PW_TD_SUPPLIER_FAX"),
);
$arFieldsDopStatereg = Array(
    "STATEREG_PLACE" => GetMessage("PW_TD_SUPPLIER_STATEREG_PLACE"),
    "STATEREG_DATE" => GetMessage("PW_TD_SUPPLIER_STATEREG_DATE"),
    "STATEREG_OGRN" => GetMessage("PW_TD_SUPPLIER_STATEREG_OGRN"),
);
$arFieldsDopBank = Array(
    "BANKING_NAME" => GetMessage("PW_TD_SUPPLIER_BANKING_NAME"),
    "BANKING_ACCOUNT" => GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNT"),
    "BANKING_ACCOUNTCORR" => GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNTCORR"),
    "BANKING_BIK" => GetMessage("PW_TD_SUPPLIER_BANKING_BIK"),
);
$arFieldsDopActive = array(
    "DOP_FIELDS_DIRECTION_ACTIVE" => GetMessage("PW_TD_DOP_FIELDS_DIRECTION_ACTIVE"),
    "DOP_FIELDS_SUBSCRIBE_ACTIVE" => GetMessage("PW_TD_DOP_FIELDS_SUBSCRIBE_ACTIVE"),
    "DOP_FIELDS_DOCUMENT_ACTIVE" => GetMessage("PW_TD_DOP_FIELDS_DOCUMENT_ACTIVE")
);

$arFieldsDefault = array("LAST_NAME", "NAME", "SECOND_NAME", "NAME_COMPANY", "NAME_DIRECTOR", "NAME_ACCOUNTANT",
    "CODE_INN", "CODE_KPP", "CODE_OKVED", "CODE_OKPO", "LEGALADDRESS_REGION", "LEGALADDRESS_CITY", "LEGALADDRESS_INDEX", "LEGALADDRESS_STREET", "LEGALADDRESS_POST",
    "POSTALADDRESS_REGION", "POSTALADDRESS_CITY", "POSTALADDRESS_INDEX", "POSTALADDRESS_STREET", "POSTALADDRESS_POST", "PHONE", "FAX",
    "STATEREG_PLACE", "STATEREG_DATE", "STATEREG_OGRN", "BANKING_NAME", "BANKING_ACCOUNT", "BANKING_ACCOUNTCORR", "BANKING_BIK",
    "DOP_FIELDS_DIRECTION_ACTIVE", "DOP_FIELDS_SUBSCRIBE_ACTIVE", "DOP_FIELDS_DOCUMENT_ACTIVE");

$arFieldsDefaultRequired = array("NAME", "LAST_NAME", "SECOND_NAME");

//property
$arFieldsProp = array();
$rsPropList = CTenderixUserSupplierProperty::GetList($by = "SORT", $order = "asc");
while ($arPropList = $rsPropList->GetNext()) {
    if ($arPropList["ACTIVE"] == "N")
        continue;
    $arFieldsProp["PROP_" . $arPropList["ID"]] = $arPropList["TITLE"];
    $arFieldsDefault[] = "PROP_" . $arPropList["ID"];
    if ($arPropList["IS_REQUIRED"] == "Y") {
        $arFieldsDefaultRequired[] = "PROP_" . $arPropList["ID"];
    }
}

$arFields = array_merge(
        $arFieldsBase, $arFieldsDop, $arFieldsDopCode, $arFieldsDopLegaladdress, $arFieldsDopPostaladdress, $arFieldsDopStatereg, $arFieldsDopBank, $arFieldsDopActive, $arFieldsProp
);
$arFieldsRequired = array_merge(
        $arFieldsBase, $arFieldsDop, $arFieldsDopCode, $arFieldsDopLegaladdress, $arFieldsDopPostaladdress, $arFieldsDopStatereg, $arFieldsDopBank, $arFieldsProp
);


$arComponentParameters = array(
    "GROUPS" => array(
        "FIELDS_REG" => array(
            "NAME" => GetMessage("PW_TD_FIELDS_REG_DESCR")
        ),
        "FIELDS_REG2" => array(
            "NAME" => GetMessage("PW_TD_FIELDS_REG_DESCR2")
        ),
    ),
    "PARAMETERS" => array(
        "SET_TITLE" => Array(),
        "STATUS" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("PW_TD_FIELDS_STATUS"),
            "TYPE" => "LIST",
            "VALUES" => $arFieldsStatus,
            "DEFAULT" => "1",
        ),
        "STATUS2" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("PW_TD_FIELDS_STATUS2"),
            "TYPE" => "LIST",
            "VALUES" => $arFieldsStatus,
            "DEFAULT" => "1",
        ),
        "JQUERY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PW_JQUERY_ACTIVE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "FIELDS" => array(
            "PARENT" => "FIELDS_REG",
            "NAME" => GetMessage("PW_TD_FIELDS"),
            "TYPE" => "LIST",
            "SIZE" => "10",
            "MULTIPLE" => "Y",
            "VALUES" => $arFields,
            "DEFAULT" => $arFieldsDefault,
        ),
        "REG_FIELDS_REQUIRED" => array(
            "PARENT" => "FIELDS_REG",
            "NAME" => GetMessage("PW_TD_FIELDS_REG_REQUIRED"),
            "TYPE" => "LIST",
            "SIZE" => "10",
            "MULTIPLE" => "Y",
            "VALUES" => $arFieldsRequired,
            "DEFAULT" => $arFieldsDefaultRequired,
        ),
        "FIELDS2" => array(
            "PARENT" => "FIELDS_REG2",
            "NAME" => GetMessage("PW_TD_FIELDS"),
            "TYPE" => "LIST",
            "SIZE" => "10",
            "MULTIPLE" => "Y",
            "VALUES" => $arFields,
            "DEFAULT" => $arFieldsDefault,
        ),
        "REG_FIELDS_REQUIRED2" => array(
            "PARENT" => "FIELDS_REG2",
            "NAME" => GetMessage("PW_TD_FIELDS_REG_REQUIRED"),
            "TYPE" => "LIST",
            "SIZE" => "10",
            "MULTIPLE" => "Y",
            "VALUES" => $arFieldsRequired,
            "DEFAULT" => $arFieldsDefaultRequired,
        ),
    ),
);
?>
