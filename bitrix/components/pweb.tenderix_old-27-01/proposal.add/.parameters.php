<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("pweb.tenderix"))
    return;

if (CModule::IncludeModule("currency")) {
    $rsCur = CCurrency::GetList();
    while ($arCur = $rsCur->Fetch()) {
        $arrCur[$arCur["CURRENCY"]] = $arCur["CURRENCY"];
    }
}

$rsProp = CTenderixProposalProperty::GetList($by = "SORT", $order = "asc", array("ACTIVE" => "Y"));
while ($arProp = $rsProp->Fetch()) {
    $arrProp["PROP_" . $arProp["ID"]] = $arProp["TITLE"];
    $arrPropDefault[] = "PROP_" . $arProp["ID"];
    if ($arProp["IS_REQUIRED"] == "Y")
        $arrPropRequired[] = "PROP_" . $arProp["ID"];
}

$arComponentParameters = array(
    "GROUPS" => array(
        "PROPERTY" => array(
            "NAME" => GetMessage("PW_TD_GROUPS_PROPERTY")
        ),
        "PROPERTY2" => array(
            "NAME" => GetMessage("PW_TD_GROUPS_PROPERTY2")
        ),
    ),
    "PARAMETERS" => array(
        "LOT_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PW_TD_LOT_ID"),
            "TYPE" => "STRING",
            "DEFAULT" => '={$_REQUEST["LOT_ID"]}',
        ),
        "CURR" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PW_TD_CURR"),
            "TYPE" => "LIST",
            "VALUES" => $arrCur,
            "DEFAULT" => 'RUB',
        ),
        "JQUERY" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PW_JQUERY_ACTIVE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "PROPERTY" => Array(
            "PARENT" => "PROPERTY",
            "NAME" => GetMessage("PW_TD_PROPERTY"),
            "TYPE" => "LIST",
            "SIZE" => "5",
            "MULTIPLE" => "Y",
            "DEFAULT" => $arrPropDefault,
            "VALUES" => $arrProp,
        ),
        "PROPERTY_REQUIRED" => Array(
            "PARENT" => "PROPERTY",
            "NAME" => GetMessage("PW_TD_PROPERTY_REQUIRED"),
            "TYPE" => "LIST",
            "SIZE" => "5",
            "MULTIPLE" => "Y",
            "DEFAULT" => $arrPropRequired,
            "VALUES" => $arrProp,
        ),
        "PROPERTY2" => Array(
            "PARENT" => "PROPERTY2",
            "NAME" => GetMessage("PW_TD_PROPERTY2"),
            "TYPE" => "LIST",
            "SIZE" => "5",
            "MULTIPLE" => "Y",
            "DEFAULT" => $arrPropDefault,
            "VALUES" => $arrProp,
        ),
        "PROPERTY_REQUIRED2" => Array(
            "PARENT" => "PROPERTY2",
            "NAME" => GetMessage("PW_TD_PROPERTY_REQUIRED"),
            "TYPE" => "LIST",
            "SIZE" => "5",
            "MULTIPLE" => "Y",
            "DEFAULT" => $arrPropRequired,
            "VALUES" => $arrProp,
        ),
    ),
);
?>
