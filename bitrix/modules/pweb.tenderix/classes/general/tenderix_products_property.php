<?php

IncludeModuleLangFile(__FILE__);

class CAllTenderProductsProperty {

    function CheckFields($ACTION, &$arFields, $ID = 0) {
        $aMsg = array();

        if ($ACTION == "ADD" || $ACTION == "UPDATE") {
            if (strlen(trim($arFields["TITLE"])) <= 0) {
                $aMsg[] = array(
                    "id" => 'TITLE',
                    "text" => GetMessage("PW_TD_ERROR_TITLE_EMPTY"));
            }
        }

        if (is_set($arFields, "C_SORT")) {
            $arFields["C_SORT"] = intVal($arFields["C_SORT"]);
        }

        if (!empty($aMsg)) {
            $e = new CAdminException(array_reverse($aMsg));
            $GLOBALS["APPLICATION"]->ThrowException($e);
            return false;
        }

        return true;
    }

}

?>
