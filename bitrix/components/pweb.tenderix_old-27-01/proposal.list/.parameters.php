<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix"))
    return;

$arSorts = Array(
    "asc" => GetMessage("PW_TD_DESC_ASC"),
    "desc" => GetMessage("PW_TD_DESC_DESC"),
);

$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "LOT_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PW_TD_LOT_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => '={$_REQUEST["LOT_ID"]}',
        ),
        "SORT_ITOGO" => Array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("PW_TD_SORT_ITOGO"),
            "TYPE" => "LIST",
            "DEFAULT" => "asc",
            "VALUES" => $arSorts,
        ),
        "CACHE_TIME" => Array("DEFAULT" => 3600000),
        "JQUERY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PW_JQUERY_ACTIVE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
    ),
);
?>
