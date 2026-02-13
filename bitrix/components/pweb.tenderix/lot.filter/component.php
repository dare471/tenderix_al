<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

global $CACHE_MANAGER;

if (!CModule::IncludeModule("pweb.tenderix")) {
    $this->AbortResultCache();
    ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
    return;
}

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");



$S_RIGHT = CTenderixUserSupplierStatus::GetStatusRight();

if (!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 3600000;

if (strlen($arParams["FILTER_NAME"]) <= 0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
    $arParams["FILTER_NAME"] = "arrFilterLot";
$FILTER_NAME = $arParams["FILTER_NAME"];

global $$FILTER_NAME;
$$FILTER_NAME = array();

$arResult["FORM_ACTION"] = isset($_SERVER['REQUEST_URI']) ? htmlspecialchars($_SERVER['REQUEST_URI']) : "";
$arResult["FILTER_NAME"] = $FILTER_NAME;

$arResult["T_RIGHT"] = $T_RIGHT;



if (isset($_REQUEST[$FILTER_NAME . "_ff"]))
    $arrFilterValue = $_REQUEST[$FILTER_NAME . "_ff"];

if (isset($_REQUEST["filter_lot_reset"])) {
    LocalRedirect($APPLICATION->GetCurPage());
}

//$CACHE_ID = array($USER->GetGroups(), ${$FILTER_NAME});
if ($this->StartResultCache()) {
    $rsSection = CTenderixSection::GetList($by = "s_c_sort", $order = "asc", $arFilter = Array(), $is_filtered);
    while ($arSection = $rsSection->Fetch()) {
        $arResult["arrSection"][$arSection["ID"]] = $arSection["TITLE"];
    }
    $rsCompany = CTenderixCompany::GetList($by = "s_c_sort", $order = "asc", $arFilter = Array(), $is_filtered);
    while ($arCompany = $rsCompany->Fetch()) {
        $arResult["arrCompany"][$arCompany["ID"]] = $arCompany["TITLE"];
    }
    $this->EndResultCache();
}

foreach ($arParams["FILTER_FIELDS"] as $field_code) {
    $field_res = array();
    $type_input = "";
    $arrRef = array();
    $name = $FILTER_NAME . "_ff[" . $field_code . "]";
    $value = $arrFilterValue[$field_code];
    switch ($field_code) {
        case "SECTION_ID":
		
            $arrRef = array("reference" => array_values($arResult["arrSection"]), "reference_id" => array_keys($arResult["arrSection"]));
            $field_res = SelectBoxFromArray($name, $arrRef, $value, " ", "id=FILTER_" . $field_code);
            if (!is_array($value) && $value != "NOT_REF" && strlen($value) > 0)
                ${$FILTER_NAME}[$field_code] = intval($value);
            $type_input = "select";
            break;
        case "COMPANY_ID":
            $arrRef = array("reference" => array_values($arResult["arrCompany"]), "reference_id" => array_keys($arResult["arrCompany"]));
            $field_res = SelectBoxFromArray($name, $arrRef, $value, " ", "id=FILTER_" . $field_code);
            if (!is_array($value) && $value != "NOT_REF" && strlen($value) > 0)
                ${$FILTER_NAME}[$field_code] = intval($value);
            $type_input = "select";
            break;
        case "TYPE":
            $arrRef = array("reference" => array(GetMessage("PW_TD_FILTER_TYPE_P"), GetMessage("PW_TD_FILTER_TYPE_N"), GetMessage("PW_TD_FILTER_TYPE_NR")), "reference_id" => array("P", "N", "T"));
            $field_res = SelectBoxFromArray($name, $arrRef, $value, " ", "id=FILTER_" . $field_code);
            if (strlen($value) > 0)
                ${$FILTER_NAME}[$field_code] = $value;
            $type_input = "select";
            break;
        case "TITLE":
            $field_res = '<input type="text" name="' . $name . '" value="' . htmlspecialchars($value) . '" />';
            if (strlen($value) > 0)
                ${$FILTER_NAME}[$field_code] = $value;
            $type_input = "text";
            break;
        case "ID":
            $field_res = '<input type="text" name="' . $name . '" value="' . intval($value) . '" />';
            if (strlen($value) > 0)
                ${$FILTER_NAME}[$field_code] = intval($value);
            $type_input = "text";
            break;
        case "ARCHIVE_LOT":
        case "USER":
            $field_res = InputType("checkbox", $name, "Y", $value, false, "", "id=FILTER_" . $field_code);
            if (strlen($value) > 0)
                ${$FILTER_NAME}[$field_code] = $value;
            $type_input = "checkbox";
            break;
        case "DATE_START":
            if (strlen($value) > 0)
                ${$FILTER_NAME}[$field_code] = $value;
            $type_input = "date_start";
            break;
        case "DATE_END":
            if (strlen($value) > 0)
                ${$FILTER_NAME}[$field_code] = $value;
            $type_input = "date_end";
            break;
    }

    $arResult["ITEMS"][] = array(
        "FIELD_CODE" => $field_code,
        "INPUT" => $field_res,
        "INPUT_TYPE" => $type_input,
        "INPUT_NAME" => $name,
        "LABEL_NAME" => GetMessage("PW_TD_FILTER_" . $field_code),
        "INPUT_VALUE" => is_array($value) ? array_map("htmlspecialchars", $value) : htmlspecialchars($value),
        "~INPUT_VALUE" => $value,
        "SELECT_VALUE" => $arrRef,
    );
}




$this->IncludeComponentTemplate();
?>
