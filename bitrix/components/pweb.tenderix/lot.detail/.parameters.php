<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix"))
    return;

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
        "PROPOSAL_URL" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PW_TD_PROPOSAL_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => 'proposal.php?LOT_ID=#LOT_ID#',
        ),
        "LOT_URL" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PW_TD_LOT_URL"),
            "TYPE" => "STRING",
            "DEFAULT" => 'lot.php?ID=#ID#',
        ),
        //"SET_TITLE" => Array(),
        "CACHE_TIME" => Array("DEFAULT" => 3600000),
    ),
);
?>
