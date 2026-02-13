<?php

IncludeModuleLangFile(__FILE__);

class CAllTenderUserSupplierStatus {

    function GetStatusRight() {

       $err_mess = (CTenderixUserSupplierStatus::err_mess()) . "<br>Function: GetStatusRight<br>Line: ";
        global $DB, $USER;

        $rsStatus = CTenderixUserSupplierStatus::GetList($by = "SORT", $order = "ASC", Array(">ID" => 0));
        while ($arStatus = $rsStatus->Fetch())
            $arStatuses[] = $arStatus;

        $rsUser = CTenderixUserSupplier::GetByID($USER->GetID());
        if ($arUser = $rsUser->Fetch()) {

            $STATUS_ID = $arUser["STATUS_ID"];
            $rsStatus = CTenderixUserSupplierStatus::GetList($by = "SORT", $order = "ASC", Array("ID" => $STATUS_ID));
            $arStatus = $rsStatus->Fetch();

            if ($arStatus["PART"] == "Y")
                return "W";
            elseif ($arStatus["AUTH"] == "Y")
                return "A";
            else
                return "D";
        } else {
            //пользователь как поставщик еще не зарегистрирован (только как пользователь Битрикс)
            $arStatus = $arStatuses[0];

            if ($arStatus["PART"] == "Y")
                return "W";
            elseif ($arStatus["AUTH"] == "Y")
                return "A";
            else
                return "D";

        }
    }

    function GetStatusUser() {
        $err_mess = (CTenderixUserSupplierStatus::err_mess()) . "<br>Function: GetStatusUser<br>Line: ";
        global $DB, $USER;

        $rsUser = CTenderixUserSupplier::GetByID($USER->GetID());
        $arUser = $rsUser->Fetch();

        $STATUS_ID = $arUser["STATUS_ID"];

        $rsStatus = CTenderixUserSupplierStatus::GetList($by = "SORT", $order = "ASC", Array("ID" => $STATUS_ID));
        $arStatus = $rsStatus->Fetch();

        return $arStatus;
    }

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
