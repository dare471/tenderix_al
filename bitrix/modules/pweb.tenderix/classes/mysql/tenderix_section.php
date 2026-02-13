<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/classes/general/tenderix_section.php");

class CTenderixSection extends CAllTenderSection {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixSection<br>File: " . __FILE__;
    }

    function GetDropDownList() {
        global $DB;
        $err_mess = (CTenderixSection::err_mess()) . "<br>Function: GetDropDownList<br>Line: ";
        $strSql = "
			SELECT
				ID as REFERENCE_ID,
				TITLE as REFERENCE
			FROM b_tx_section
			ORDER BY C_SORT
			";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetMixedList(&$by, &$order, $arFilter = Array()) {
        global $DB;
        $arResult = array();

        $rsCatalog = CTenderixSection::GetCatalogList($by, $order, $arFilter);
        while ($arCatalog = $rsCatalog->Fetch()) {
            $arCatalog["TYPE"] = "C";
            $arResult[] = $arCatalog;
        }

        $rsSection = CTenderixSection::GetList($by, $order, $arFilter, $is_filtered = false);
        while ($arSection = $rsSection->Fetch()) {
            $arSection["TYPE"] = "S";
            $arResult[] = $arSection;
        }

        $rsResult = new CDBResult;
        $rsResult->InitFromArray($arResult);

        return $rsResult;
    }

    function GetCatalogList(&$by, &$order, $arFilter = Array()) {
        $err_mess = (CTenderixSection::err_mess()) . "<br>Function: GetCatalogList<br>Line: ";
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
                        $arSqlSearch[] = GetFilterQuery("C.TITLE", $val, "N");
                        break;
                    case "ACTIVE":
                        $arSqlSearch[] = ($val == "Y") ? "C.ACTIVE='Y'" : "C.ACTIVE='N'";
                        break;
                    case "CATALOG_ID":
                        $arSqlSearch[] = GetFilterQuery("C.CATALOG_ID", $val, "N");
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


        $strSql = "SELECT C.* FROM b_tx_catalog C WHERE " . $strSqlSearch . " " . $strSqlOrder;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetList(&$by, &$order, $arFilter = Array(), &$is_filtered) {
        $err_mess = (CTenderixSection::err_mess()) . "<br>Function: GetList<br>Line: ";
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
                        $arSqlSearch[] = GetFilterQuery("C.TITLE", $val, "N");
                        break;
                    case "ACTIVE":
                        $arSqlSearch[] = ($val == "Y") ? "C.ACTIVE='Y'" : "C.ACTIVE='N'";
                        break;
                    case "CATALOG_ID":
                        $arSqlSearch[] = GetFilterQuery("C.CATALOG_ID", $val, "N");
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


        $strSql = "SELECT C.* FROM b_tx_section C WHERE " . $strSqlSearch . " " . $strSqlOrder;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function Add($arFields) {
        global $DB;

        if (!CTenderixSection::CheckFields("ADD", $arFields))
            return false;

        $arInsert = $DB->PrepareInsert("b_tx_section", $arFields);
        $strSql = "INSERT INTO b_tx_section(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());
        return $ID;
    }

    function Update($ID, $arFields) {
        global $DB;
        $ID = intVal($ID);
        if ($ID <= 0)
            return False;

        if (!CTenderixSection::CheckFields("UPDATE", $arFields, $ID))
            return false;

        $strUpdate = $DB->PrepareUpdate("b_tx_section", $arFields);
        $strSql = "UPDATE b_tx_section SET " . $strUpdate . " WHERE ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return $ID;
    }

    function Delete($ID) {
        global $DB;
        $ID = IntVal($ID);
        $DB->Query("DELETE FROM b_tx_section WHERE ID = " . $ID, True);
        return true;
    }

    function CatalogAdd($arFields) {
        global $DB;

        if (!CTenderixSection::CheckFields("ADD", $arFields))
            return false;

        $arInsert = $DB->PrepareInsert("b_tx_catalog", $arFields);
        $strSql = "INSERT INTO b_tx_catalog(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());
        return $ID;
    }

    function CatalogUpdate($ID, $arFields) {
        global $DB;
        $ID = intVal($ID);
        if ($ID <= 0)
            return False;

        if (!CTenderixSection::CheckFields("UPDATE", $arFields, $ID))
            return false;

        $strUpdate = $DB->PrepareUpdate("b_tx_catalog", $arFields);
        $strSql = "UPDATE b_tx_catalog SET " . $strUpdate . " WHERE ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return $ID;
    }

    function CatalogDelete($ID) {
        global $DB;
        $ID = IntVal($ID);

        //delete section
        $rsSection = CTenderixSection::GetList($by, $order, array("CATALOG_ID" => $ID), $is_filtered);
        while ($arSection = $rsSection->GetNext()) {
            CTenderixSection::Delete($arSection["ID"]);
        }
        //delete catalog
        $DB->Query("DELETE FROM b_tx_catalog WHERE ID = " . $ID, True);
        return true;
    }

}

?>
