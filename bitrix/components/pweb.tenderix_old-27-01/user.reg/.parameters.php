<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix"))
    return;

$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "SET_TITLE" => Array(),
        "AJAX_MODE" => array(),
    ),
);
?>
