<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/classes/general/tenderix_company.php");

class CTenderixCompany extends CAllTenderCompany {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixCompany<br>File: " . __FILE__;
    }

    function GetDropDownList() {
        global $DB;
        $err_mess = (CTenderixCompany::err_mess()) . "<br>Function: GetDropDownList<br>Line: ";
        $strSql = "
			SELECT
				ID as REFERENCE_ID,
				TITLE as REFERENCE
			FROM b_tx_company
			ORDER BY C_SORT
			";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetList(&$by, &$order, $arFilter=Array(), &$is_filtered) {
        $err_mess = (CTenderixCompany::err_mess()) . "<br>Function: GetList<br>Line: ";
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
                    case "TITLE":
                        $arSqlSearch[] = GetFilterQuery("C.TITLE", $val, "Y");
                        break;
                    case "CODE_INN":
                        $arSqlSearch[] = GetFilterQuery("C.CODE_INN", $val, "Y");
                        break;
                    case "CODE_KPP":
                        $arSqlSearch[] = GetFilterQuery("C.CODE_KPP", $val, "Y");
                        break;
                    case "ACTIVE":
                        $arSqlSearch[] = ($val == "Y") ? "C.ACTIVE='Y'" : "C.ACTIVE='N'";
                        break;
                }
            }
        }

        if ($by == "s_id")
            $strSqlOrder = "ORDER BY C.ID";
        elseif ($by == "s_timestamp")
            $strSqlOrder = "ORDER BY C.TIMESTAMP_X";
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


        $strSql = "SELECT C.*, " . $DB->DateToCharFunction("C.TIMESTAMP_X") . " TIMESTAMP_X FROM b_tx_company C WHERE " . $strSqlSearch . " " . $strSqlOrder;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function Add($arFields) {
        global $DB;

        if (!CTenderixCompany::CheckFields("ADD", $arFields))
            return false;

        $arInsert = $DB->PrepareInsert("b_tx_company", $arFields);
        $strSql = "INSERT INTO b_tx_company(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());
        return $ID;
    }

    function Update($ID, $arFields) {
        global $DB;
        $ID = intVal($ID);
        if ($ID <= 0)
            return False;

        if (!CTenderixCompany::CheckFields("UPDATE", $arFields, $ID))
            return false;

        $strUpdate = $DB->PrepareUpdate("b_tx_company", $arFields);
        $strSql = "UPDATE b_tx_company SET " . $strUpdate . " WHERE ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return $ID;
    }

    function Delete($ID) {
        global $DB;
        $ID = IntVal($ID);
        if ($ID != '1')
            $DB->Query("DELETE FROM b_tx_company WHERE ID = " . $ID, True);
        return true;
    }

}

?>
