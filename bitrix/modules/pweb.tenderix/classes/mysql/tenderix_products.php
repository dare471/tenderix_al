<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/classes/general/tenderix_products.php");

class CTenderixProducts extends CAllTenderProducts {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixProducts<br>File: " . __FILE__;
    }

    function GetDropDownList() {
        global $DB;
        $err_mess = (CTenderixProducts::err_mess()) . "<br>Function: GetDropDownList<br>Line: ";
        $strSql = "
			SELECT
				ID as REFERENCE_ID,
				TITLE as REFERENCE
			FROM b_tx_prod
			ORDER BY C_SORT
			";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetList(&$by, &$order, $arFilter=Array(), &$is_filtered) {
        $err_mess = (CTenderixProducts::err_mess()) . "<br>Function: GetList<br>Line: ";
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
					case "XML_ID":
                        $arSqlSearch[] = GetFilterQuery("C.XML_ID", $val, "N");
                        break;
                    case "ID":
                        $arSqlSearch[] = GetFilterQuery("C.ID", $val, "N");
                        break;
                    case "SECTION_ID":
                        $arSqlSearch[] = GetFilterQuery("C.SECTION_ID", $val, "N");
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
        elseif ($by == "s_property")
            $strSqlOrder = "ORDER BY PROPERTY ";
        elseif ($by == "s_section")
            $strSqlOrder = "ORDER BY SECTION ";
        else {
            $by = "s_id";
            $strSqlOrder = "ORDER BY C.ID";
        }
        if ($order != "asc") {
            $strSqlOrder .= " desc ";
            $order = "desc";
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);


        $strSql = "SELECT 
                        C.*, 
                        count(CC.ID) PROPERTY,
                        CCC.TITLE SECTION,
                        U.TITLE UNIT_NAME
                   FROM b_tx_prod C 
                   LEFT JOIN b_tx_prod_property CC ON (C.ID = CC.PRODUCTS_ID)
                   LEFT JOIN b_tx_section CCC ON (C.SECTION_ID = CCC.ID)
                   LEFT JOIN b_tx_spr_details U ON (C.UNIT_ID = U.ID)
                   WHERE " . $strSqlSearch . " GROUP BY C.ID " . $strSqlOrder;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }
    
    function GetListProducts($arOrder=Array(), $arFilter=Array()) {
        $err_mess = (CTenderixProducts::err_mess()) . "<br>Function: GetListProducts<br>Line: ";
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
					case "XML_ID":
                        $arSqlSearch[] = GetFilterQuery("C.XML_ID", $val, "N");
                        break;
                    case "ID":
                        $arSqlSearch[] = GetFilterQuery("C.ID", $val, "N");
                        break;
                    case "SECTION_ID":
                        $arSqlSearch[] = GetFilterQuery("C.SECTION_ID", $val, "N");
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

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);


        $strSql = "SELECT C.*
                   FROM b_tx_prod C
                   WHERE " . $strSqlSearch;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetListBuyer($arFilter) {
        $err_mess = (CTenderixProducts::err_mess()) . "<br>Function: GetList<br>Line: ";
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
                    case "LOT_ID":
                        $arSqlSearch[] = GetFilterQuery("C.LOT_ID", $val, "N");
                        break;
                }
            }
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);
        $strSql = "SELECT C.* FROM b_tx_prod_buyer C 
                   WHERE " . $strSqlSearch;

        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function Add($arFields) {
        global $DB;

        if (!CTenderixProducts::CheckFields("ADD", $arFields))
            return false;

        $arInsert = $DB->PrepareInsert("b_tx_prod", $arFields);
        $strSql = "INSERT INTO b_tx_prod(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());
        return $ID;
    }

    function Update($ID, $arFields) {
        global $DB;
        $ID = intVal($ID);
        if ($ID <= 0)
            return False;

        if (!CTenderixProducts::CheckFields("UPDATE", $arFields, $ID))
            return false;

        $strUpdate = $DB->PrepareUpdate("b_tx_prod", $arFields);
        $strSql = "UPDATE b_tx_prod SET " . $strUpdate . " WHERE ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return $ID;
    }

    function Delete($ID) {
        global $DB;
        $ID = IntVal($ID);
        $DB->Query("DELETE FROM b_tx_prod WHERE ID = " . $ID, True);
        $DB->Query("DELETE FROM b_tx_prod_property WHERE PRODUCTS_ID = " . $ID, True);
        return true;
    }

}

?>
