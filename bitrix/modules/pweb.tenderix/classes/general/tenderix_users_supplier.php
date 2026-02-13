<?php

IncludeModuleLangFile(__FILE__);

class CAllTenderUserSupplier {

    function CheckFields($ACTION, &$arFields, &$arFieldsSupplier, $aMsgUser = "", $arRequired) {
        $aMsg = "";
        if (strlen($aMsgUser) > 0) {
            $aMsg .= $aMsgUser;
        }

        if ($ACTION == "ADD" || $ACTION == "UPDATE") {
            if (in_array("NAME", $arRequired)  && isset($arFields["NAME"])) {
                if (strlen(trim($arFields["NAME"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_NAME") . "<br />";
                }
            }
            if (in_array("LAST_NAME", $arRequired) && isset($arFields["LAST_NAME"])) {
                if (strlen(trim($arFields["LAST_NAME"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_LAST_NAME") . "<br />";
                }
            }
            if (in_array("SECOND_NAME", $arRequired) && isset($arFields["SECOND_NAME"])) {
                if (strlen(trim($arFields["SECOND_NAME"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_SECOND_NAME") . "<br />";
                }
            }
            if (in_array("NAME_COMPANY", $arRequired) && isset($arFieldsSupplier["NAME_COMPANY"])) {
                if (strlen(trim($arFieldsSupplier["NAME_COMPANY"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_NAME_COMPANY") . "<br />";
                }
            }
            if (in_array("NAME_DIRECTOR", $arRequired) && isset($arFieldsSupplier["NAME_DIRECTOR"])) {
                if (strlen(trim($arFieldsSupplier["NAME_DIRECTOR"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_NAME_DIRECTOR") . "<br />";
                }
            }
            if (in_array("NAME_ACCOUNTANT", $arRequired) && isset($arFieldsSupplier["NAME_ACCOUNTANT"])) {
                if (strlen(trim($arFieldsSupplier["NAME_ACCOUNTANT"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_NAME_ACCOUNTANT") . "<br />";
                }
            }
            if (in_array("CODE_INN", $arRequired) && isset($arFieldsSupplier["CODE_INN"])) {
                if (strlen(trim($arFieldsSupplier["CODE_INN"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_CODE_INN") . "<br />";
                }
            }
            if (in_array("CODE_KPP", $arRequired) && isset($arFieldsSupplier["CODE_KPP"])) {
                if (strlen(trim($arFieldsSupplier["CODE_KPP"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_CODE_KPP") . "<br />";
                }
            }
            if (in_array("CODE_OKVED", $arRequired) && isset($arFieldsSupplier["CODE_OKVED"])) {
                if (strlen(trim($arFieldsSupplier["CODE_OKVED"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_CODE_OKVED") . "<br />";
                }
            }
            if (in_array("CODE_OKPO", $arRequired) && isset($arFieldsSupplier["CODE_OKPO"])) {
                if (strlen(trim($arFieldsSupplier["CODE_OKPO"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_CODE_OKPO") . "<br />";
                }
            }
            if (in_array("LEGALADDRESS_REGION", $arRequired) && isset($arFieldsSupplier["LEGALADDRESS_REGION"])) {
                if (strlen(trim($arFieldsSupplier["LEGALADDRESS_REGION"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_LEGALADDRESS_REGION") . "<br />";
                }
            }
            if (in_array("LEGALADDRESS_CITY", $arRequired) && isset($arFieldsSupplier["LEGALADDRESS_CITY"])) {
                if (strlen(trim($arFieldsSupplier["LEGALADDRESS_CITY"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_LEGALADDRESS_CITY") . "<br />";
                }
            }
            if (in_array("LEGALADDRESS_INDEX", $arRequired) && isset($arFieldsSupplier["LEGALADDRESS_INDEX"])) {
                if (strlen(trim($arFieldsSupplier["LEGALADDRESS_INDEX"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_LEGALADDRESS_INDEX") . "<br />";
                }
            }
            if (in_array("LEGALADDRESS_STREET", $arRequired) && isset($arFieldsSupplier["LEGALADDRESS_STREET"])) {
                if (strlen(trim($arFieldsSupplier["LEGALADDRESS_STREET"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_LEGALADDRESS_STREET") . "<br />";
                }
            }
            if (in_array("LEGALADDRESS_POST", $arRequired) && isset($arFieldsSupplier["LEGALADDRESS_POST"])) {
                if (strlen(trim($arFieldsSupplier["LEGALADDRESS_POST"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_LEGALADDRESS_POST") . "<br />";
                }
            }
            if (in_array("POSTALADDRESS_REGION", $arRequired) && isset($arFieldsSupplier["POSTALADDRESS_REGION"])) {
                if (strlen(trim($arFieldsSupplier["POSTALADDRESS_REGION"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_POSTALADDRESS_REGION") . "<br />";
                }
            }
            if (in_array("POSTALADDRESS_CITY", $arRequired) && isset($arFieldsSupplier["POSTALADDRESS_CITY"])) {
                if (strlen(trim($arFieldsSupplier["POSTALADDRESS_CITY"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_POSTALADDRESS_CITY") . "<br />";
                }
            }
            if (in_array("POSTALADDRESS_INDEX", $arRequired) && isset($arFieldsSupplier["POSTALADDRESS_INDEX"])) {
                if (strlen(trim($arFieldsSupplier["POSTALADDRESS_INDEX"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_POSTALADDRESS_INDEX") . "<br />";
                }
            }
            if (in_array("POSTALADDRESS_STREET", $arRequired) && isset($arFieldsSupplier["POSTALADDRESS_STREET"])) {
                if (strlen(trim($arFieldsSupplier["POSTALADDRESS_STREET"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_POSTALADDRESS_STREET") . "<br />";
                }
            }
            if (in_array("POSTALADDRESS_POST", $arRequired) && isset($arFieldsSupplier["POSTALADDRESS_POST"])) {
                if (strlen(trim($arFieldsSupplier["POSTALADDRESS_POST"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_POSTALADDRESS_POST") . "<br />";
                }
            }
            if (in_array("FAX", $arRequired) && isset($arFieldsSupplier["FAX"])) {
                if (strlen(trim($arFieldsSupplier["FAX"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_FAX") . "<br />";
                }
            }
            if (in_array("PHONE", $arRequired) && isset($arFieldsSupplier["PHONE"])) {
                if (strlen(trim($arFieldsSupplier["PHONE"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_PHONE") . "<br />";
                }
            }
            if (in_array("STATEREG_PLACE", $arRequired) && isset($arFieldsSupplier["STATEREG_PLACE"])) {
                if (strlen(trim($arFieldsSupplier["STATEREG_PLACE"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_STATEREG_PLACE") . "<br />";
                }
            }
            if (in_array("STATEREG_DATE", $arRequired) && isset($arFieldsSupplier["STATEREG_DATE"])) {
                if (strlen(trim($arFieldsSupplier["STATEREG_DATE"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_STATEREG_DATE") . "<br />";
                }
            }
			if (in_array("STATEREG_NDS", $arRequired) && isset($arFieldsSupplier["STATEREG_NDS"])) {
                if (!in_array($arFieldsSupplier["STATEREG_NDS"], array("Y", "N"))) {
                    $aMsg .= GetMessage("PW_TD_ERROR_STATEREG_NDS") . "<br />";
                }
            }
            if (in_array("STATEREG_OGRN", $arRequired) && isset($arFieldsSupplier["STATEREG_OGRN"])) {
                if (strlen(trim($arFieldsSupplier["STATEREG_OGRN"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_STATEREG_OGRN") . "<br />";
                }
            }
            if (in_array("BANKING_NAME", $arRequired) && isset($arFieldsSupplier["BANKING_NAME"])) {
                if (strlen(trim($arFieldsSupplier["BANKING_NAME"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_BANKING_NAME") . "<br />";
                }
            }
            if (in_array("BANKING_ACCOUNT", $arRequired) && isset($arFieldsSupplier["BANKING_ACCOUNT"])) {
                if (strlen(trim($arFieldsSupplier["BANKING_ACCOUNT"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_BANKING_ACCOUNT") . "<br />";
                }
            }
            if (in_array("BANKING_ACCOUNTCORR", $arRequired) && isset($arFieldsSupplier["BANKING_ACCOUNTCORR"])) {
                if (strlen(trim($arFieldsSupplier["BANKING_ACCOUNTCORR"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_BANKING_ACCOUNTCORR") . "<br />";
                }
            }
            if (in_array("BANKING_BIK", $arRequired) && isset($arFieldsSupplier["BANKING_BIK"])) {
                if (strlen(trim($arFieldsSupplier["BANKING_BIK"])) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_BANKING_BIK") . "<br />";
                }
            }

            //property 
            $arProp = $arFields["PROPERTY"];
            foreach ($arProp["FILES"]["name"] as $k => $arFile) {
                $empty = true;
                foreach ($arFile as $aFile) {
                    if (strlen($aFile) > 0) {
                        $empty = false;
                    }
                }
                //if ($arProp["PROPERTY_S"][$k]["ACTIVE"] == "Y" && $arProp["PROPERTY_S"][$k]["IS_REQUIRED"] == "Y" && $empty && count($arProp["PROPERTY"][$k]) <= 0) {
                if ($arProp["PROPERTY_S"][$k]["ACTIVE"] == "Y" && in_array("PROP_" . $k, $arRequired) && $empty && count($arProp["PROPERTY"][$k]) <= 0) {
                    $aMsg .= GetMessage("PW_TD_ERROR_EMPTY") . " \"" . $arProp["PROPERTY_S"][$k]["TITLE"] . "\"<br />";
                }
            }
            foreach ($arProp["PROPERTY"] as $k => $arPropValue) {
                $empty = true;
                foreach ($arPropValue as $val) {
                    if (trim(strlen($val)) > 0) {
                        $empty = false;
                    }
                }
                //if ($arProp["PROPERTY_S"][$k]["ACTIVE"] == "Y" && $arProp["PROPERTY_S"][$k]["IS_REQUIRED"] == "Y" && $empty && $arProp["PROPERTY_S"][$k]["PROPERTY_TYPE"] != "F") {
                if ($arProp["PROPERTY_S"][$k]["ACTIVE"] == "Y" && in_array("PROP_" . $k, $arRequired) && $empty && $arProp["PROPERTY_S"][$k]["PROPERTY_TYPE"] != "F") {
                    $aMsg .= GetMessage("PW_TD_ERROR_EMPTY") . " \"" . $arProp["PROPERTY_S"][$k]["TITLE"] . "\"<br />";
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
        $err_mess = (CTenderixUserSupplier::err_mess()) . "<br>Function: GetByID<br>Line: ";
        $ID = intval($ID);
        $res = CTenderixUserSupplier::GetList($by, $order, array("ID" => $ID), $is_filtered);
        return $res;
    }

    function SubscribeListArr($USER_ID) {
        $arrSubcribe = array();

        $rsSubcribe = CTenderixUserSupplier::SubscribeList($USER_ID);
        while ($arSubcribe = $rsSubcribe->GetNext()) {
            $arrSubcribe[] = $arSubcribe["SECTION_ID"];
        }
        return $arrSubcribe;
    }

    function DirectionListArr($USER_ID) {
        $arrSubcribe = array();

        $rsSubcribe = CTenderixUserSupplier::DirectionList($USER_ID);
        while ($arSubcribe = $rsSubcribe->GetNext()) {
            $arrSubcribe[] = $arSubcribe["SECTION_ID"];
        }
        return $arrSubcribe;
    }

    function FileReName($file_name) {
        $found = array();
        // exapmle(2).txt
        if (preg_match("/^(.*)\((\d+?)\)(\..+?)$/", $file_name, $found)) {
            $fname = $found[1];
            $fext = $found[3];
            $index = $found[2];
        }
        // example(2)
        elseif (preg_match("/^(.*)\((\d+?)\)$/", $file_name, $found)) {
            $fname = $found[1];
            $fext = "";
            $index = $found[2];
        }
        // example.txt
        elseif (preg_match("/^(.*)(\..+?)$/", $file_name, $found)) {
            $fname = $found[1];
            $fext = $found[2];
            $index = 0;
        }
        // example
        else {
            $fname = $file_name;
            $fext = "";
            $index = 0;
        }
        return array($fname, $fext, $index);
    }

}

?>
