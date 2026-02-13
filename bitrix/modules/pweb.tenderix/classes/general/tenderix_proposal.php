<?php

IncludeModuleLangFile(__FILE__);

class CAllTenderProposal {

    function CheckFields($arFields, $arFieldsProp = array(), $arFieldsBuyer = array()) {
        $aMsg = array();

        $rsLot = CTenderixLot::GetByIDa($arFields["LOT_ID"]);
        $arLot = $rsLot->GetNext();

        if ($arLot["TERM_PAYMENT_REQUIRED"] == "Y" && $arLot["TERM_PAYMENT_EDIT"] == "Y" && strlen(trim($arFields["TERM_PAYMENT_VAL"])) <= 0) {
            $aMsg[] = array(
                "id" => 'TERM_PAYMENT_REQUIRED',
                "text" => GetMessage("PW_TD_ERROR_TERM_PAYMENT_REQUIRED"));
        }
        if ($arLot["TERM_DELIVERY_REQUIRED"] == "Y" && $arLot["TERM_DELIVERY_EDIT"] == "Y" && strlen(trim($arFields["TERM_DELIVERY_VAL"])) <= 0) {
            $aMsg[] = array(
                "id" => 'TERM_DELIVERY_REQUIRED',
                "text" => GetMessage("PW_TD_ERROR_TERM_DELIVERY_REQUIRED"));
        }

        if ($arLot["TYPE_ID"] == "S" || $arLot["TYPE_ID"] == "R") {
            foreach ($arFieldsProp as $key => $val) {
                $arBuyerID[] = $val["PRODUCTS_PROPERTY_BUYER_ID"];
                $arSupplierValue[$val["PRODUCTS_PROPERTY_BUYER_ID"]] = $val["VALUE"];
            }
            $arBuyerID = implode(",", $arBuyerID);
            if (strlen($arBuyerID) > 0) {
                $rsProp = CTenderixProductsProperty::GetListProductsPropertyBuyer($arBuyerID);
                while ($arProp = $rsProp->GetNext()) {
                    if (strlen(trim($arSupplierValue[$arProp["ID"]])) == "" && $arProp["REQUIRED"] == "Y" && $arProp["EDIT"] == "Y") {
                        $aMsg[] = array(
                            "id" => 'PRODUCTS_PROPERTY_BUYER_ID_' . $arProp["ID"],
                            "text" => GetMessage("PW_TD_ERROR_PROPERTY_BUYER_REQUIRED") . ": " . $arProp["TITLE"]);
                    }
                }
            }
        }
        //__("arFieldsProp");
        //__($arFieldsProp);
        if ($arLot["TYPE_ID"] != "S" && $arLot["TYPE_ID"] != "R") {
            foreach ($arFieldsProp as $key => $val) {
                if (floatval($val["PRICE_NDS"]) <= 0 && $arFieldsBuyer["FULL_SPEC"] == "Y") {
                    $aMsg[] = array(
                        "id" => 'SPEC_BUYER_ID',
                        "text" => GetMessage("PW_TD_ERROR_FULL_SPEC"));
                    break;
                }
            }
            foreach ($arFieldsProp as $key => $val) {
                //__(floatval($val["PRICE_NDS"]));
                if (floatval($val["PRICE_NDS"]) < 0 || !preg_match('/^\d*[\.]?\d+$/', $val["PRICE_NDS"])) { //04.10.2017 Ярослав
                    $aMsg[] = array(
                        "id" => 'SPEC_BUYER_PRICE_ERROR',
                        "text" => GetMessage("PW_TD_ERROR_PRICE"));
                    break;
                }
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
            if ($arProp["PROPERTY_S"][$k]["ACTIVE"] == "Y" && $arProp["PROPERTY_S"][$k]["IS_REQUIRED"] == "Y" && $empty && count($arProp["PROPERTY"][$k]) <= 0) {
                $aMsg[] = array(
                    "id" => 'PROPERTY_S_' . $k,
                    "text" => GetMessage("PW_TD_ERROR_EMPTY") . ": " . $arProp["PROPERTY_S"][$k]["TITLE"]
                );
            }
        }
        foreach ($arProp["PROPERTY"] as $k => $arPropValue) {
            $empty = true;
            foreach ($arPropValue as $val) {
                if (trim(strlen($val)) > 0) {
                    $empty = false;
                }
            }
            if ($arProp["PROPERTY_S"][$k]["ACTIVE"] == "Y" && $arProp["PROPERTY_S"][$k]["IS_REQUIRED"] == "Y" && $empty && $arProp["PROPERTY_S"][$k]["PROPERTY_TYPE"] != "F") {
                $aMsg[] = array(
                    "id" => 'PROPERTY_S_' . $k,
                    "text" => GetMessage("PW_TD_ERROR_EMPTY") . ": " . $arProp["PROPERTY_S"][$k]["TITLE"]
                );
            }
        }

        if (!empty($aMsg)) {
            $e = new CAdminException(array_reverse($aMsg));
            $GLOBALS["APPLICATION"]->ThrowException($e);
            return false;
        }

        return true;
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
