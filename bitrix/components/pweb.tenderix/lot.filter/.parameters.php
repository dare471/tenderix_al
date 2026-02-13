<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix"))
    return;

$arFieldsFilter = Array(
    "SECTION_ID" => GetMessage("PW_TD_SECTION"),
    "COMPANY_ID" => GetMessage("PW_TD_COMPANY"),
    "TYPE" => GetMessage("PW_TD_TYPE"),
    "TITLE" => GetMessage("PW_TD_TITLE"),
    "ID" => GetMessage("PW_TD_ID"),
    "DATE_START" => GetMessage("PW_TD_DATE_START"),
    "DATE_END" => GetMessage("PW_TD_DATE_END"),
    "ARCHIVE_LOT" => GetMessage("PW_TD_ARCHIVE_LOT"),
    "USER" => GetMessage("PW_TD_USER"),
);
$arFieldsFilterDefault = Array("SECTION_ID", "COMPANY_ID", "TYPE", "TITLE", "ID", "DATE_START", "DATE_END", "ARCHIVE_LOT", "USER");

$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "CACHE_TIME" => Array("DEFAULT" => 3600000),
        "FILTER_NAME" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PW_TD_FILTER_NAME"),
            "TYPE" => "STRING",
            "DEFAULT" => "arrFilterLot",
        ),
        "FILTER_FIELDS" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PW_TD_FILTER_FIELDS"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arFieldsFilter,
            "DEFAULT" => $arFieldsFilterDefault,
        ),
    ),
);
?>
