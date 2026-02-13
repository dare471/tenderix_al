<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix"))
    return;

$arUnit = array(
    "r" => GetMessage("PW_TD_R"),
    "tr" => GetMessage("PW_TD_TR"),
    "mr" => GetMessage("PW_TD_MR"),
);
$arFormat = array(
    "0" => "1 726 458.00",
    "1" => "1726458.00",
    "2" => "1 726 458,00",
    "3" => "1726458,00",
);

$arLevel = array(
    "COMPANY_ID" => GetMessage("PW_TD_COMPANY"),
    "SECTION_ID" => GetMessage("PW_TD_SECTION"),
    "DATE_YEAR" => GetMessage("PW_TD_YEAR"),
    "DATE_MONTH" => GetMessage("PW_TD_MONTH"),
);

$arLevelCol = array(
    "2" => "2",
    "3" => "3",
    "4" => "4",
);

$arComponentParameters = array(
    "GROUPS" => array(
        "LEVEL" => array(
            "NAME" => GetMessage("PW_TD_LEVEL")
        ),
    ),
    "PARAMETERS" => array(
        "SET_TITLE" => Array(),
        "CACHE_TIME" => Array("DEFAULT" => 86400),
        "UNIT" => Array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("PW_TD_UNIT"),
            "TYPE" => "LIST",
            "DEFAULT" => "r",
            "VALUES" => $arUnit,
        ),
        "FORMAT" => Array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("PW_TD_FORMAT"),
            "TYPE" => "LIST",
            "DEFAULT" => "0",
            "VALUES" => $arFormat,
        ),
        "COMPANY_ONLY" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("PW_COMPANY_ONLY"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
        "JQUERY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PW_JQUERY_ACTIVE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "LEVEL_COL" => Array(
            "PARENT" => "LEVEL",
            "NAME" => GetMessage("PW_TD_LEVEL_COL"),
            "TYPE" => "LIST",
            "DEFAULT" => "4",
            "VALUES" => $arLevelCol,
        ),
        "TYPE_L1" => Array(
            "PARENT" => "LEVEL",
            "NAME" => GetMessage("PW_TD_TYPE_L1"),
            "TYPE" => "LIST",
            "DEFAULT" => "COMPANY_ID",
            "VALUES" => $arLevel,
        ),
        "TYPE_L2" => Array(
            "PARENT" => "LEVEL",
            "NAME" => GetMessage("PW_TD_TYPE_L2"),
            "TYPE" => "LIST",
            "DEFAULT" => "SECTION_ID",
            "VALUES" => $arLevel,
        ),
        "TYPE_L3" => Array(
            "PARENT" => "LEVEL",
            "NAME" => GetMessage("PW_TD_TYPE_L3"),
            "TYPE" => "LIST",
            "DEFAULT" => "DATE_YEAR",
            "VALUES" => $arLevel,
        ),
        "TYPE_L4" => Array(
            "PARENT" => "LEVEL",
            "NAME" => GetMessage("PW_TD_TYPE_L4"),
            "TYPE" => "LIST",
            "DEFAULT" => "DATE_MONTH",
            "VALUES" => $arLevel,
        ),
    ),
);
?>
