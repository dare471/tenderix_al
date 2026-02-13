<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix"))
    return;

$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "JQUERY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PW_JQUERY_ACTIVE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "VISUAL_EDITOR" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PW_VISUAL_EDITOR"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ),
        "COMPANY_ONLY" => array(
            "PARENT" => "ADDITIONAL_SETTINGS",
            "NAME" => GetMessage("PW_COMPANY_ONLY"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
    ),
);
?>
