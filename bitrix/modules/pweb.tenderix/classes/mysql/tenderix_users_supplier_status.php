<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/classes/general/tenderix_users_supplier_status.php");

class CTenderixUserSupplierStatus extends CAllTenderUserSupplierStatus {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixUserSupplierStatus<br>File: " . __FILE__;
    }

    function GetDropDownList() {
        global $DB;
        $err_mess = (CTenderixUserSupplierStatus::err_mess()) . "<br>Function: GetDropDownList<br>Line: ";
        $strSql = "
			SELECT
				ID as REFERENCE_ID,
				TITLE as REFERENCE
			FROM b_tx_supplier_status
			";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetDropDownList_forSupplierList() {
        global $DB;
        $err_mess = (CTenderixUserSupplierStatus::err_mess()) . "<br>Function: GetDropDownList<br>Line: ";
        $strSql = "
			SELECT
				ID as REFERENCE_ID,
				TITLE as REFERENCE
			FROM b_tx_supplier_status
			WHERE ACTIVE = 'Y' AND ID <> 1
			ORDER BY C_SORT
			";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetList(&$by, &$order, $arFilter=Array()) {
        $err_mess = (CTenderixUserSupplierStatus::err_mess()) . "<br>Function: GetList<br>Line: ";
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
                    case "TITLE":
                        $arSqlSearch[] = GetFilterQuery("C.TITLE", $val, "Y");
                        break;
                    case "ACTIVE":
                        $arSqlSearch[] = ($val == "Y") ? "C.ACTIVE='Y'" : "C.ACTIVE='N'";
                        break;
                }
            }
        }

        if ($by == "ID")
            $strSqlOrder = "ORDER BY C.ID";
        elseif ($by == "SORT")
            $strSqlOrder = "ORDER BY C.C_SORT";
        elseif ($by == "ACTIVE")
            $strSqlOrder = "ORDER BY C.ACTIVE";
        elseif ($by == "TITLE")
            $strSqlOrder = "ORDER BY C.TITLE ";
        else {
            $by = "ID";
            $strSqlOrder = "ORDER BY C.ID";
        }


        if ($order != "ASC") {
            $strSqlOrder .= " DESC ";
            //$order = "DESC";
        }


        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        //TODO в запросе идёт DESC, даже если передано asc
        $strSql = "SELECT C.* FROM b_tx_supplier_status as C WHERE " . $strSqlSearch . " " . $strSqlOrder;

        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function Add($arFields) {
        global $DB;

        if (!CTenderixUserSupplierStatus::CheckFields("ADD", $arFields))
            return false;
        CFile::SaveForDB($arFields, "LOGO_BIG", "pweb.tenderix");
        CFile::SaveForDB($arFields, "LOGO_SMALL", "pweb.tenderix");
        $arInsert = $DB->PrepareInsert("b_tx_supplier_status", $arFields);
        $strSql = "INSERT INTO b_tx_supplier_status(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());
        return $ID;
    }

    function Update($ID, $arFields) {
        global $DB;
        $ID = intVal($ID);
        if ($ID <= 0)
            return False;

        if (!CTenderixUserSupplierStatus::CheckFields("UPDATE", $arFields, $ID))
            return false;

        CFile::SaveForDB($arFields, "LOGO_BIG", "pweb.tenderix");
        CFile::SaveForDB($arFields, "LOGO_SMALL", "pweb.tenderix");
        $strUpdate = $DB->PrepareUpdate("b_tx_supplier_status", $arFields);
        $strSql = "UPDATE b_tx_supplier_status SET " . $strUpdate . " WHERE ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return $ID;
    }

    function Delete($ID) { 
        global $DB;
        $ID = IntVal($ID);
        if ($ID != '1')
            $DB->Query("DELETE FROM b_tx_supplier_status WHERE ID = " . $ID, True);
        return true;
    }

}

?>
