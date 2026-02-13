<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix"))
    return;

$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "CACHE_TIME" => Array("DEFAULT" => 3600),
        "LOT_LIST_URL" => array(
            "PARENT" => "URL_TEMPLATES",
            "NAME" => GetMessage("PW_TD_LOT_LIST"),
            "TYPE" => "STRING",
            "DEFAULT" => "index.php",
        ),
        "LOT_ADD_URL" => array(
            "PARENT" => "URL_TEMPLATES",
            "NAME" => GetMessage("PW_TD_LOT_ADD"),
            "TYPE" => "STRING",
            "DEFAULT" => "lot.php",
        ),
        "PROFILE_URL" => array(
            "PARENT" => "URL_TEMPLATES",
            "NAME" => GetMessage("PW_TD_PROFILE"),
            "TYPE" => "STRING",
            "DEFAULT" => "profile.php",
        ),
        "PROFILE_SUPPLIER_URL" => array(
            "PARENT" => "URL_TEMPLATES",
            "NAME" => GetMessage("PW_TD_PROFILE_SUPPLIER"),
            "TYPE" => "STRING",
            "DEFAULT" => "profile_supplier.php",
        ),
        "STATISTIC_URL" => array(
            "PARENT" => "URL_TEMPLATES",
            "NAME" => GetMessage("PW_TD_PSTATISTIC"),
            "TYPE" => "STRING",
            "DEFAULT" => "statistic.php",
        ),
    ),
);
?>
