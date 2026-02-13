<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/classes/general/tenderix_proposal_property.php");

class CTenderixProposalProperty extends CAllTenderProposalProperty {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixProposalProperty<br>File: " . __FILE__;
    }

    function GetDropDownList() {
        global $DB;
        $err_mess = (CTenderixProposalProperty::err_mess()) . "<br>Function: GetDropDownList<br>Line: ";
        $strSql = "
			SELECT
				ID as REFERENCE_ID,
				TITLE as REFERENCE
			FROM b_tx_proposal_property
			ORDER BY SORT
			";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }
    
    function GetCountActive() {
        $err_mess = (CTenderixProposalProperty::err_mess()) . "<br>Function: GetCount<br>Line: ";
        global $DB;
        $strSql = "SELECT count(*) CNT FROM b_tx_proposal_property WHERE ACTIVE = 'Y'";
        $rsRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        $arRes = $rsRes -> Fetch();
        return $arRes["CNT"];
    }

    function GetList(&$by, &$order, $arFilter=Array()) {
        $err_mess = (CTenderixProposalProperty::err_mess()) . "<br>Function: GetList<br>Line: ";
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
                    case "T_RIGHT":
                        $arSqlSearch[] = GetFilterQuery("C.T_RIGHT", $val, "Y");
                        break;
                    case "S_RIGHT":
                        $arSqlSearch[] = GetFilterQuery("C.S_RIGHT", $val, "Y");
                        break;
                }
            }
        }

        if ($by == "ID")
            $strSqlOrder = "ORDER BY C.ID";
        elseif ($by == "SORT")
            $strSqlOrder = "ORDER BY C.SORT";
        elseif ($by == "ACTIVE")
            $strSqlOrder = "ORDER BY C.ACTIVE";
        elseif ($by == "TITLE")
            $strSqlOrder = "ORDER BY C.TITLE ";
        elseif ($by == "DESCRIPTION")
            $strSqlOrder = "ORDER BY C.DESCRIPTION ";
        elseif ($by == "CODE")
            $strSqlOrder = "ORDER BY C.CODE ";
        elseif ($by == "T_RIGHT")
            $strSqlOrder = "ORDER BY C.T_RIGHT ";
        elseif ($by == "S_RIGHT")
            $strSqlOrder = "ORDER BY C.S_RIGHT ";
        elseif ($by == "START_LOT")
            $strSqlOrder = "ORDER BY C.START_LOT ";
        elseif ($by == "END_LOT")
            $strSqlOrder = "ORDER BY C.END_LOT ";
        else {
            $by = "ID";
            $strSqlOrder = "ORDER BY C.ID";
        }
        if ($order != "asc") {
            $strSqlOrder .= " desc ";
            $order = "desc";
        } else {
            $strSqlOrder .= " asc ";
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);


        $strSql = "SELECT C.* FROM b_tx_proposal_property C WHERE " . $strSqlSearch . " " . $strSqlOrder;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function Add($arFields) {
        global $DB;

        if (!CTenderixProposalProperty::CheckFields("ADD", $arFields))
            return false;
        //CFile::SaveForDB($arFields, "LOGO_BIG", "pweb.tenderix");
        $arInsert = $DB->PrepareInsert("b_tx_proposal_property", $arFields);
        $strSql = "INSERT INTO b_tx_proposal_property(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());
        return $ID;
    }

    function Update($ID, $arFields) {
        global $DB;
        $ID = intVal($ID);
        if ($ID <= 0)
            return False;

        if (!CTenderixProposalProperty::CheckFields("UPDATE", $arFields, $ID))
            return false;

        $strUpdate = $DB->PrepareUpdate("b_tx_proposal_property", $arFields);
        $strSql = "UPDATE b_tx_proposal_property SET " . $strUpdate . " WHERE ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return $ID;
    }

    function Delete($ID) {
        global $DB;
        $ID = IntVal($ID);
        $DB->Query("DELETE FROM b_tx_proposal_property WHERE ID = " . $ID, True);
        $DB->Query("DELETE FROM b_tx_proposal_propval WHERE PROPERTY_ID = " . $ID, True);
        return true;
    }

}

?>
