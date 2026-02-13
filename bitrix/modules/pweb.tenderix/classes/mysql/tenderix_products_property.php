<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/classes/general/tenderix_products_property.php");

class CTenderixProductsProperty extends CAllTenderProductsProperty {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixProductsProperty<br>File: " . __FILE__;
    }

    function GetList(&$by, &$order, $arFilter=Array(), &$is_filtered) {
        $err_mess = (CTenderixProductsProperty::err_mess()) . "<br>Function: GetList<br>Line: ";
        global $DB;
        $arSqlSearch = Array();
        $strSqlSearch = "";

        if (is_array($arFilter)) {
            foreach ($arFilter as $key => $val) {
                if (is_array($val)) {
                    if (count($val) <= 0)
                        continue;
                }
                else {
                    if ((strlen($val) <= 0) || ($val === "NOT_REF"))
                        continue;
                }
                $key = strtoupper($key);
                switch ($key) {
                    case "ID":
                        $arSqlSearch[] = GetFilterQuery("C.ID", $val, "N");
                        break;
                    case "PRODUCTS_ID":
                        $arSqlSearch[] = GetFilterQuery("C.PRODUCTS_ID", $val, "N");
                        break;
                    case "TITLE":
                        $arSqlSearch[] = GetFilterQuery("C.TITLE", $val, "Y");
                        break;
                    case "ACTIVE":
                        $arSqlSearch[] = ($val == "Y") ? "C.ACTIVE='Y'" : "C.ACTIVE='N'";
                        break;
                }
            }
        }

        if ($by == "s_id")
            $strSqlOrder = "ORDER BY C.ID";
        elseif ($by == "s_c_sort")
            $strSqlOrder = "ORDER BY C.C_SORT";
        elseif ($by == "s_active")
            $strSqlOrder = "ORDER BY C.ACTIVE";
        elseif ($by == "s_title")
            $strSqlOrder = "ORDER BY C.TITLE ";
        else {
            $by = "s_id";
            $strSqlOrder = "ORDER BY C.ID";
        }
        if ($order != "asc") {
            $strSqlOrder .= " desc ";
            $order = "desc";
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);


        $strSql = "SELECT C.* FROM b_tx_prod_property C 
                   WHERE " . $strSqlSearch . " " . $strSqlOrder;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetListBuyer($arFilter=Array()) {
        $err_mess = (CTenderixProductsProperty::err_mess()) . "<br>Function: GetListBuyer<br>Line: ";
        global $DB;
        $arSqlSearch = Array();
        $strSqlSearch = "";

        if (is_array($arFilter)) {
            foreach ($arFilter as $key => $val) {
                if (is_array($val)) {
                    if (count($val) <= 0)
                        continue;
                }
                else {
                    if ((strlen($val) <= 0) || ($val === "NOT_REF"))
                        continue;
                }
                $key = strtoupper($key);
                switch ($key) {
                    case "ID":
                        $arSqlSearch[] = GetFilterQuery("C.ID", $val, "N");
                        break;
                    case "PRODUCTS_ID":
                        $arSqlSearch[] = GetFilterQuery("C.PRODUCTS_ID", $val, "N");
                        break;
                    case "PRODUCTS_PROPERTY_ID":
                        $arSqlSearch[] = GetFilterQuery("C.PRODUCTS_PROPERTY_ID", $val, "N");
                        break;
                }
            }
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        $strSql = "SELECT C.* FROM b_tx_prod_property_b C 
                   WHERE " . $strSqlSearch;

        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetListProductsPropertyBuyer($ID) {
        $err_mess = (CTenderixProductsProperty::err_mess()) . "<br>Function: GetListProductBuyer<br>Line: ";
        global $DB;
        
        $strSql = "SELECT C.*, CC.TITLE FROM b_tx_prod_property_b C
                   LEFT JOIN b_tx_prod_property CC ON (CC.ID = C.PRODUCTS_PROPERTY_ID)
                   WHERE C.ID in (" . $ID . ")";

        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function Add($arFields) {
        global $DB;

        if (!CTenderixProductsProperty::CheckFields("ADD", $arFields))
            return false;

        $arInsert = $DB->PrepareInsert("b_tx_prod_property", $arFields);
        $strSql = "INSERT INTO b_tx_prod_property(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());
        return $ID;
    }

    function Update($ID, $arFields) {
        global $DB;
        $ID = intVal($ID);
        if ($ID <= 0)
            return False;

        if (!CTenderixProductsProperty::CheckFields("UPDATE", $arFields, $ID))
            return false;

        $strUpdate = $DB->PrepareUpdate("b_tx_prod_property", $arFields);
        $strSql = "UPDATE b_tx_prod_property SET " . $strUpdate . " WHERE ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return $ID;
    }

    function Delete($ID) {
        global $DB;
        $ID = IntVal($ID);
        $DB->Query("DELETE FROM b_tx_prod_property WHERE ID = " . $ID, True);
        return true;
    }

}

?>
