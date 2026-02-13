<?

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/classes/general/tenderix_users_buyer.php");

class CTenderixUserBuyer extends CAllTenderUserBuyer {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixUserBuyer<br>File: " . __FILE__;
    }

    function GetList(&$by, &$order, $arFilter = Array(), &$is_filtered) {
        $err_mess = (CTenderixUserBuyer::err_mess()) . "<br>Function: GetList<br>Line: "; 
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
                        $arSqlSearch[] = GetFilterQuery("CC.ID", $val, "N");
                        break;
                    case "NAME":
                        $arSqlSearch[] = GetFilterQuery("CC.NAME, CC.LAST_NAME, CC.SECOND_NAME", $val, "Y");
                        break;
                    case "LOGIN":
                        $arSqlSearch[] = GetFilterQuery("CC.LOGIN", $val, "Y");
                        break;
                    case "COMPANY":
                        $arSqlSearch[] = GetFilterQuery("CCC.ID", $val, "Y");
                        break;
                    case "ACTIVE":
                        $arSqlSearch[] = ($val == "Y") ? "CC.ACTIVE='Y'" : "CC.ACTIVE='N'";
                        break;
                }
            }
        }

        if ($by == "s_id")
            $strSqlOrder = "ORDER BY CC.ID";
        elseif ($by == "s_active")
            $strSqlOrder = "ORDER BY CC.ACTIVE";
        elseif ($by == "s_name")
            $strSqlOrder = "ORDER BY CC.LAST_NAME ";
        elseif ($by == "s_login")
            $strSqlOrder = "ORDER BY CC.LOGIN ";
        elseif ($by == "s_company")
            $strSqlOrder = "ORDER BY CCC.TITLE ";
        else {
            $by = "s_id";
            $strSqlOrder = "ORDER BY CC.ID";
        }
        if ($order == "") {
            $strSqlOrder .= " asc ";
            $order = "asc";
        }
        if ($order != "asc") {
            $strSqlOrder .= " desc ";
            $order = "desc";
        }


        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);


        $strSql = "SELECT 
                        C.*, 
                        CC.*,
                        CCC.TITLE COMPANY,
                        CONCAT(CC.LAST_NAME,' ',CC.NAME,' ',CC.SECOND_NAME) FIO
                   FROM b_tx_buyer C 
                   LEFT JOIN b_user CC ON (C.USER_ID = CC.ID)
                   LEFT JOIN b_tx_company CCC ON (C.COMPANY_ID = CCC.ID)
                   WHERE " . $strSqlSearch . " GROUP BY C.USER_ID, C.COMPANY_ID " . $strSqlOrder;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function Add($arFields) {
        global $DB;

        $arFieldsAll = $arFields;
        $arFieldsBuyer["COMPANY_ID"] = $arFields["COMPANY_ID"];
        $arFieldsBuyer["SUBSCR_OWN_LOT"] = $arFields["SUBSCR_OWN_LOT"];
        $arFieldsBuyer["USER_BIND"] = $arFields["USER_BIND"];
        unset($arFields["COMPANY_ID"]);
        unset($arFields["USER_BIND"]);
        unset($arFields["SUBSCR_OWN_LOT"]);

        $user = new CUser;
        $user->CheckFields($arFields);
        $aMsg = $user->LAST_ERROR;

        if (!empty($aMsg)) {
            if (!CTenderixUserBuyer::CheckFields("ADD", $arFieldsBuyer, $aMsg))
                return false;
        } else {
            if (!CTenderixUserBuyer::CheckFields("ADD", $arFieldsBuyer, $aMsg))
                return false;
            $ID = $user->Add($arFields);
        }

        $arFieldsBuyer["USER_ID"] = $ID;

        $arInsert = $DB->PrepareInsert("b_tx_buyer", $arFieldsBuyer);
        $strSql = "INSERT INTO b_tx_buyer(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        CTenderixLog::Log("BUYER_ADD", array("ID" => $ID, "FIELDS" => $arFieldsAll));

        return $ID;
    }

    function Add2($USER_ID, $arFields) {
        global $DB;

        $arFieldsAll = $arFields;
        $arFieldsBuyer["USER_ID"] = $USER_ID;
        $arFieldsBuyer["COMPANY_ID"] = $arFields["COMPANY_ID"];
        $arFieldsBuyer["SUBSCR_OWN_LOT"] = $arFields["SUBSCR_OWN_LOT"];
        $arFieldsBuyer["USER_BIND"] = $arFields["USER_BIND"];
        unset($arFields);

        if (!CTenderixUserBuyer::CheckFields("ADD2", $arFieldsBuyer, $aMsg))
            return false;

        $arInsert = $DB->PrepareInsert("b_tx_buyer", $arFieldsBuyer);
        $strSql = "INSERT INTO b_tx_buyer(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        CTenderixLog::Log("BUYER_ADD", array("ID" => $USER_ID, "FIELDS" => $arFieldsAll));

        return $USER_ID;
    }

    function Update($ID, $arFields) {
        global $DB;
        $ID = intVal($ID);
        if ($ID <= 0)
            return False;

        $arFieldsAll = $arFields;
        $arFieldsBuyer["COMPANY_ID"] = $arFields["COMPANY_ID"];
        $arFieldsBuyer["SUBSCR_OWN_LOT"] = $arFields["SUBSCR_OWN_LOT"];
        $arFieldsBuyer["USER_BIND"] = $arFields["USER_BIND"];
        unset($arFields["COMPANY_ID"]);
        unset($arFields["USER_BIND"]);
        unset($arFields["SUBSCR_OWN_LOT"]);

        $user = new CUser;
        $user->CheckFields($arFields, $ID);
        $aMsg = $user->LAST_ERROR;
        if (!empty($aMsg)) {
            if (!CTenderixUserBuyer::CheckFields("UPDATE", $arFieldsBuyer, $aMsg))
                return false;
        } else {
            if (!CTenderixUserBuyer::CheckFields("UPDATE", $arFieldsBuyer, $aMsg))
                return false;
            $user->Update($ID, $arFields);
        }

        $strUpdate = $DB->PrepareUpdate("b_tx_buyer", $arFieldsBuyer);
        $strSql = "UPDATE b_tx_buyer SET " . $strUpdate . " WHERE USER_ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        CTenderixLog::Log("BUYER_UPDATE", array("ID" => $ID, "FIELDS" => $arFieldsAll));

        return $ID;
    }

    function Delete($ID) {
        global $DB;
        $ID = IntVal($ID);
        $results = $DB->Query("SELECT 'x' FROM b_tx_buyer WHERE USER_ID = " . $ID, true);
        if (!$row = $results->Fetch())
            return false;

        if ($DB->Query("DELETE FROM b_tx_buyer WHERE USER_ID = " . $ID, True)) {
            if ($ID != '1')
                $DB->Query("DELETE FROM b_user WHERE ID = " . $ID, True);

            CTenderixLog::Log("BUYER_DEL", array("ID" => $ID));
        }
        return true;
    }

}

?>