<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix"))
    return;

$arSorts = Array(
    "asc" => GetMessage("PW_TD_LOT_DESC_ASC"),
    "desc" => GetMessage("PW_TD_LOT_DESC_DESC"),
);

$arSortFields = Array(
    "ID" => GetMessage("PW_TD_DESC_FID"),
    "TITLE" => GetMessage("PW_TD_DESC_FNAME"),
    "DATE_START" => GetMessage("PW_TD_DESC_FSTART"),
    "DATE_END" => GetMessage("PW_TD_DESC_FEND"),
);
$arFieldsFilter = Array(
    "SECTION" => GetMessage("PW_TD_SECTION"),
    "COMPANY" => GetMessage("PW_TD_COMPANY"),
    "ARCHIVE_LOT" => GetMessage("PW_TD_ARCHIVE_LOT"),
    "NAME_KEY" => GetMessage("PW_TD_NAME_KEY"),
    "USER" => GetMessage("PW_TD_USER"),
    "TYPE" => GetMessage("PW_TD_TYPE"),
);

$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "AJAX_MODE" => array(),
        "LOTS_COUNT" => Array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("PW_TD_DESC_LIST_CONT"),
            "TYPE" => "STRING",
            "DEFAULT" => "20",
        ),
        "SORT_BY" => Array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("PW_TD_DESC_IBORD1"),
            "TYPE" => "LIST",
            "DEFAULT" => "ID",
            "VALUES" => $arSortFields,
            "ADDITIONAL_VALUES" => "Y",
        ),
        "SORT_ORDER" => Array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("PW_TD_DESC_IBBY1"),
            "TYPE" => "LIST",
            "DEFAULT" => "DESC",
            "VALUES" => $arSorts,
        ),
        "ACTIVE_DATE" => Array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("PW_TD_ACTIVE_LOT"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y"
        ),
        "DETAIL_URL" => array(
            "PARENT" => "URL_TEMPLATES",
            "NAME" => GetMessage("PW_TD_LOT_DETAIL_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => "tenders_detail.php?LOT_ID=#LOT_ID#",
        ),
        "PROPOSAL_URL" => array(
            "PARENT" => "URL_TEMPLATES",
            "NAME" => GetMessage("PW_TD_LOT_PROPOSAL_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => "proposal.php?LOT_ID=#LOT_ID#",
        ),
        "SET_TITLE" => Array(),
        "CACHE_TIME" => Array("DEFAULT" => 3600),
        "FILTER_NAME" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("PW_TD_FILTER_NAME"),
            "TYPE" => "STRING",
            "DEFAULT" => "arrFilterLot",
        ),
    ),
);
CTenderix::AddPagerSettings($arComponentParameters, GetMessage("PW_TD_DESC_PAGER_LOT"), false, true);
?>
