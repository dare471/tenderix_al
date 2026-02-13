<?php

IncludeModuleLangFile(__FILE__);

class CAllTenderLot {

    function CheckFields($ACTION, &$arFields, &$arFieldsDop, $ID = 0) {
        $aMsg = array();
        $date_start = 0;
        $date_end = 0;

        if ($ACTION == "ADD" || $ACTION == "UPDATE") {

            if (strlen(trim($arFields["TITLE"])) <= 0 /*&& $arFields["TYPE_ID"] == 'N'*/) {
                $aMsg[] = array(
                    "id" => 'TITLE',
                    "text" => GetMessage("PW_TD_ERROR_TITLE_EMPTY"));
            }
            if ($arFields["SECTION_ID"] <= 0 && $arFields["TYPE_ID"] == "N") {
                $fff = print_r($arFields, true);
                $aMsg[] = array(
                    "id" => 'SECTION_ID',
                    "text" => GetMessage("PW_TD_ERROR_SECTION"));
            }
            if ($arFields["COMPANY_ID"] <= 0) {
                $aMsg[] = array(
                    "id" => 'COMPANY_ID',
                    "text" => GetMessage("PW_TD_ERROR_COMPANY"));
            }
            if (strlen($arFields["RESPONSIBLE_FIO"]) <= 0) {
                $aMsg[] = array(
                    "id" => 'RESPONSIBLE_FIO',
                    "text" => GetMessage("PW_TD_ERROR_RESPONSIBLE_FIO"));
            }
            if (strlen($arFields["RESPONSIBLE_PHONE"]) <= 0) {
                $aMsg[] = array(
                    "id" => 'RESPONSIBLE_PHONE',
                    "text" => GetMessage("PW_TD_ERROR_RESPONSIBLE_PHONE"));
            }
            // if ($arFields["PRIVATE"] != 'Y') {
            //     $aMsg[] = array(
            //         "id" => 'PRIVATE',
            //         "text" => 'Вы должны выбрать поставщиков');
            // }
            // if ($arFields["PRIVATE_LIST"] == NULL) {
            //     $aMsg[] = array(
            //         "id" => 'PRIVATE_LIST',
            //         "text" => 'Вы не выбрали ни одного поставщика.');
            // }

            $date_start = false;
            if (isset($arFields["DATE_START"])) {
                $arFields["DATE_START"] = trim($arFields["DATE_START"]);
                $date_start = MakeTimeStamp($arFields["DATE_START"]);
                if (!$date_start):
                    $aMsg[] = array(
                        "id" => "DATE_START",
                        "text" => GetMessage("PW_TD_ERROR_DATE_START"));
                endif;
            }

            if (isset($arFields["DATE_END"])) {
                $arFields["DATE_END"] = trim($arFields["DATE_END"]);
                $date_end = false;
                $date_end = MakeTimeStamp($arFields["DATE_END"]);

                if (!$date_end):
                    $aMsg[] = array(
                        "id" => "DATE_END",
                        "text" => GetMessage("PW_TD_ERROR_DATE_END"));
                elseif ($date_start >= $date_end && !empty($arFields["DATE_START"])):
                    $aMsg[] = array(
                        "id" => "DATE_END",
                        "text" => GetMessage("PW_TD_ERROR_DATE_TILL"));
                endif;
            }

            if ($arFields["TERM_PAYMENT_ID"] > 0 && strlen(trim($arFields["TERM_PAYMENT_VAL"])) <= 0 && $arFields["TERM_PAYMENT_REQUIRED"] == "Y" && $arFields["TERM_PAYMENT_EDIT"] == "N") {
                $aMsg[] = array(
                    "text" => GetMessage("PW_TD_ERROR_PAYMENT_EMPTY"));
            }
            if ($arFields["TERM_DELIVERY_ID"] > 0 && strlen(trim($arFields["TERM_DELIVERY_VAL"])) <= 0 && $arFields["TERM_DELIVERY_REQUIRED"] == "Y" && $arFields["TERM_DELIVERY_EDIT"] == "N") {
                $aMsg[] = array(
                    "text" => GetMessage("PW_TD_ERROR_DELIVERY_EMPTY"));
            }

            //__($arFields["TYPE_ID"]);
            //__($arFieldsDop);
            //die();

            if ($arFields["TYPE_ID"] == 'N' || $arFields["TYPE_ID"] == 'T') {
                $title = true;
                $count = true;
                $start_price = true;
                $step_price = true;

                if (count($arFieldsDop) <= 0) {
                    $aMsg[] = array(
                        "text" => GetMessage("PW_TD_ERROR_SPEC_EMPTY"));
                    $aMsg[] = array(
                        "text" => print_r($arFieldsDop, true));
                }
                foreach ($arFieldsDop as $fieldPropNew) {
                    if (strlen(trim($fieldPropNew["TITLE"])) <= 0)
                        $title = false;
                    if (!is_numeric($fieldPropNew["COUNT"]))
                        $count = false;
                    if (!is_numeric($fieldPropNew["START_PRICE"]) && strlen(trim($fieldPropNew["START_PRICE"])) > 0)
                        $start_price = false;
                    if (!is_numeric($fieldPropNew["STEP_PRICE"]) && strlen(trim($fieldPropNew["STEP_PRICE"])) > 0)
                        $step_price = false;
                }

                if (!$title) {
                    $aMsg[] = array(
                        "text" => GetMessage("PW_TD_ERROR_TITLE_SPEC_EMPTY"));
                }
                if (!$count) {
                    $aMsg[] = array(
                        "text" => GetMessage("PW_TD_ERROR_COUNT_SPEC_EMPTY"));
                }
                if (!$start_price) {
                    $aMsg[] = array(
                        "text" => GetMessage("PW_TD_ERROR_START_PRICE_SPEC_EMPTY"));
                }
                if (!$step_price) {
                    $aMsg[] = array(
                        "text" => GetMessage("PW_TD_ERROR_STEP_PRICE_SPEC_EMPTY"));
                }
            }

            if ($arFields["TYPE_ID"] == 'S' || $arFields["TYPE_ID"] == 'R') {
                // TODO: РАЗОБРАТЬСЯ С ЭТИМ БЛОКОМ !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                //print_r($arFieldsDop); die;
                if (intval($arFieldsDop["PRODUCTS"]["PRODUCTS_ID"]) > 0) {
                    if (intval($arFieldsDop["PRODUCTS"]["COUNT"]) <= 0) {
                        $aMsg[] = array(
                            "text" => GetMessage("PW_TD_ERROR_COUNT_PRODUCT_EMPTY"));
                    }
                    if (!is_numeric($arFieldsDop["PRODUCTS"]["START_PRICE"]) && strlen(trim($arFieldsDop["PRODUCTS"]["START_PRICE"])) > 0) {
                        $aMsg[] = array(
                            "text" => GetMessage("PW_TD_ERROR_START_PRICE_PRODUCT_EMPTY"));
                    }
                    if (!is_numeric($arFieldsDop["PRODUCTS"]["STEP_PRICE"]) && strlen(trim($arFieldsDop["PRODUCTS"]["STEP_PRICE"])) > 0) {
                        $aMsg[] = array(
                            "text" => GetMessage("PW_TD_ERROR_STEP_PRICE_PRODUCT_EMPTY"));
                    }
                    foreach ($arFieldsDop["PRODUCTS_PROPERTY"] as $property) {
                        if ($property["REQUIRED"] == "Y" && $property["EDIT"] == "N" && $property["VISIBLE"] == "Y" && trim($property["VALUE"]) == "") {
                            $aMsg[] = array(
                                "text" => GetMessage("PW_TD_ERROR_VALUE_PRODUCT_EMPTY"));
                        }
                    }
                } else {
                    //$aMsg[] = array(
                        //"text" => GetMessage("PW_TD_ERROR_NO_PRODUCT"));
                }
            }
            //__($arFields);
        }

        if (!empty($aMsg)) {
            $e = new CAdminException(array_reverse($aMsg));
            $GLOBALS["APPLICATION"]->ThrowException($e);
            return false;
        }

        return true;
    }

    function GetByID($ID) {
        $ID = intVal($ID);
        if ($ID <= 0)
            return False;

        $rsLot = CTenderixLot::GetList($by = "", $order = "", $arFilter = Array("ID" => $ID));
        $arLot = $rsLot->Fetch();

        return $arLot;
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

    function GetSite() {
        $arSITE = COption::GetOptionString("pweb.tenderix", "PW_TD_SITE");
        return $arSITE;
    }

}

class CAllTenderLotSpec {

    function CheckFields($ACTION, &$arFields, $ID = 0) {
        $aMsg = array();

        if ($ACTION == "ADD" || $ACTION == "UPDATE") {
            
        }

        if (!empty($aMsg)) {
            $e = new CAdminException(array_reverse($aMsg));
            $GLOBALS["APPLICATION"]->ThrowException($e);
            return false;
        }

        return true;
    }

}

class CAllTenderLotProduct {

    function CheckFields($ACTION, &$arFields, $ID = 0) {
        $aMsg = array();

        if ($ACTION == "ADD" || $ACTION == "UPDATE") {
            
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
