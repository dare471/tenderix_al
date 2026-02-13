<?php

IncludeModuleLangFile(__FILE__);

class CAllTenderUserBuyer {

    function CheckFields($ACTION, &$arFields, $aMsgUser="") {
        $aMsg = "";
        if (strlen($aMsgUser) > 0) {
            $aMsg .= $aMsgUser;
        }
        if ($ACTION == "ADD" || $ACTION == "UPDATE") {
            if (strlen(trim($arFields["COMPANY_ID"])) <= 0) {
                $aMsg .= GetMessage("PW_TD_ERROR_COMPANY_EMPTY");
            }
        }

        if ($ACTION == "ADD2") {
            $res = CTenderixUserBuyer::GetByID($arFields["USER_ID"]);
            if ($arRes = $res->Fetch()) {
                $aMsg .= GetMessage("PW_TD_ERROR_USER");
            } else {
                if (strlen(trim($arFields["COMPANY_ID"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_COMPANY_EMPTY");
                }
            }
        }

        if (!empty($aMsg)) {
            $GLOBALS["APPLICATION"]->ThrowException($aMsg);
            return false;
        }

        return true;
    }

    function GetByID($ID) {
        $err_mess = (CTenderixUserBuyer::err_mess()) . "<br>Function: GetByID<br>Line: ";
        $ID = intval($ID);
        $res = CTenderixUserBuyer::GetList($by, $order, array("ID" => $ID), $is_filtered);
        return $res;
    }

}

?>
