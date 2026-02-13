<?php

IncludeModuleLangFile(__FILE__);

class CAllTenderUserSupplierProperty {

    function CheckFields($ACTION, &$arFields, $ID = 0) {
        $aMsg = array();

        if ($ACTION == "ADD" || $ACTION == "UPDATE") {
            if (strlen(trim($arFields["TITLE"])) <= 0) {
                $aMsg[] = array(
                    "id" => 'TITLE',
                    "text" => GetMessage("PW_TD_ERROR_TITLE_EMPTY"));
            }
        }

        if (is_set($arFields, "SORT")) {
            $arFields["SORT"] = intVal($arFields["SORT"]);
        }

        if (!empty($aMsg)) {
            $e = new CAdminException(array_reverse($aMsg));
            $GLOBALS["APPLICATION"]->ThrowException($e);
            return false;
        }

        return true;
    }

    function GetElementInput($supplierID, $arFields, $arValue=array()) {
        $result = "";
        $result .= '<div id="prop-' . $arFields["ID"] . '">';
        for ($i = 0; $i < $arFields["MULTI_CNT"]; $i++) {
            if ($i > 0 || $supplierID > 0) {
                $arFields["DEFAULT_VALUE"] = "";
            }
            if ($arFields["ROW_COUNT"] <= 1) {
                $result .= '<input name="PROP[' . $arFields["ID"] . '][n' . $i . ']" type="text" value="' . $arFields["DEFAULT_VALUE"] . '" size="' . $arFields["COL_COUNT"] . '" />';
            } elseif ($arFields["ROW_COUNT"] > 1) {
                $result .= '<textarea name="PROP[' . $arFields["ID"] . '][n' . $i . ']" cols="' . $arFields["COL_COUNT"] . '" rows="' . $arFields["ROW_COUNT"] . '">' . $arFields["DEFAULT_VALUE"] . '</textarea>';
            }
            $result .= '<br />';
        }
        $result .= "</div>";
        if ($arFields["MULTI"] == "Y") {
            $result .= '<input type="button" value="Добавить" onclick="addNewElem();" />';
        }

        return $result;
    }

}

?>
