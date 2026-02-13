<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/classes/general/tenderix_statistic.php");

class CTenderixStatistic extends CAllTenderStatistic
{

    function err_mess()
    {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixStatistic<br>File: " . __FILE__;
    }

    function GetList($arOrder = Array(), $arFilter = Array())
    { //TODO: $arOrder
        $err_mess = (CTenderixStatistic::err_mess()) . "<br>Function: GetList<br>Line: ";
        global $DB;

        $arSqlSearch = Array();
        $strSqlSearch = "";

        if (is_array($arFilter)) {
            foreach ($arFilter as $key => $val) {
                if (is_array($val)) {
                    if (count($val) <= 0)
                        continue;
                } else {
                    if ((strlen($val) <= 0) || ($val === "NOT_REF"))
                        continue;
                }
                $key = strtoupper($key);
                switch ($key) {
                    case "COMPANY_ID":
                        $arSqlSearch[] = GetFilterQuery("C.COMPANY_ID", $val, "N");
                        break;
                }
            }
        }

        $strSqlOrder = "";
        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        $strSql = "SELECT C.* FROM b_tx_statistic C WHERE " . $strSqlSearch . " " . $strSqlOrder;
        $arRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        while ($rsRes = $arRes->Fetch()) {
            $arStatistic[$rsRes["LOT_ID"]] = $rsRes;
        }

        return $arStatistic;
    }

    function Count()
    {
        $err_mess = (CTenderixStatistic::err_mess()) . "<br>Function: GetList<br>Line: ";
        global $DB;

        $strSql = "SELECT count(LOT_ID) CNT FROM b_tx_statistic";
        $arRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        $rsRes = $arRes->Fetch();

        return $rsRes["CNT"];
    }

    function Add($ID, $arFields)
    {
        global $DB;
        $ID = intVal($ID);
        if ($ID <= 0)
            return false;

//        __($arFields);
//        die();

        $arFieldsStatistic["LOT_ID"] = $ID;
        if (isset($arFields["DATE_END"])) {
            $arFieldsStatistic["DATE_TIME"] = strtotime($arFields["DATE_END"]);
            $arFieldsStatistic["DATE_MONTH"] = intval(date("m", strtotime($arFields["DATE_END"])));
            $arFieldsStatistic["DATE_YEAR"] = intval(date("Y", strtotime($arFields["DATE_END"])));
        }
        if (isset($arFields["ACTIVE"])) {
            $arFieldsStatistic["ACTIVE"] = $arFields["ACTIVE"];
        }
        if (isset($arFields["SECTION_ID"])) {
            $arFieldsStatistic["SECTION_ID"] = $arFields["SECTION_ID"];
        }
        if (isset($arFields["COMPANY_ID"])) {
            $arFieldsStatistic["COMPANY_ID"] = $arFields["COMPANY_ID"];
        }
        if (isset($arFields["LOT_PRICE"])) {
            $arFieldsStatistic["LOT_PRICE"] = $arFields["LOT_PRICE"];
        }

        if (isset($arFields["FAIL"])) {
            $arFieldsStatistic["FAIL"] = $arFields["FAIL"];
        }

        if (isset($arFields["TYPE_ID"])) {
            $arFieldsStatistic["TYPE_ID"] = $arFields["TYPE_ID"];
        }

        if (isset($arFields["DATE_START"])) {
            $arFieldsStatistic["DATE_START"] = strtotime($arFields["DATE_START"]);
            $date_reg = explode(" ", $arFields["DATE_START"], 2);
            $date_reg = explode(".", $date_reg[0]);
            $arFieldsStatistic["DATE_START_YEAR"] = $date_reg[2];
            $arFieldsStatistic["DATE_START_MONTH"] = $date_reg[1];
        }

        $arInsert = $DB->PrepareInsert("b_tx_statistic", $arFieldsStatistic);
        $strSql = "INSERT INTO b_tx_statistic(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());
        return $ID;
    }

    function Update($ID, $arFields)
    {
        global $DB;
        $ID = intVal($ID);
        if ($ID <= 0)
            return false;

//        die();

        if (isset($arFields["DATE_END"])) {
            $arFieldsStatistic["DATE_TIME"] = strtotime($arFields["DATE_END"]);
            $arFieldsStatistic["DATE_MONTH"] = intval(date("m", strtotime($arFields["DATE_END"])));
            $arFieldsStatistic["DATE_YEAR"] = intval(date("Y", strtotime($arFields["DATE_END"])));
        }
        if (isset($arFields["ACTIVE"])) {
            $arFieldsStatistic["ACTIVE"] = $arFields["ACTIVE"];
        }
        if (isset($arFields["SECTION_ID"])) {
            $arFieldsStatistic["SECTION_ID"] = $arFields["SECTION_ID"];
        }
        if (isset($arFields["COMPANY_ID"])) {
            $arFieldsStatistic["COMPANY_ID"] = $arFields["COMPANY_ID"];
        }
        if (isset($arFields["LOT_PRICE"])) {
            $arFieldsStatistic["LOT_PRICE"] = $arFields["LOT_PRICE"];
        }

        if (isset($arFields["FAIL"])) {
            $arFieldsStatistic["FAIL"] = $arFields["FAIL"];
        }

        if (isset($arFields["TYPE_ID"])) {
            $arFieldsStatistic["TYPE_ID"] = $arFields["TYPE_ID"];
        }
        if (isset($arFields["WIN"]) && $arFields["WIN"]) {
            $arFieldsStatistic["WIN"] = "Y";
        } 
		/* else {
            $arFieldsStatistic["WIN"] = "N";
        } */
        if (isset($arFields["PRICE_MAX"])) {
            $arFieldsStatistic["PRICE_MAX"] = $arFields["PRICE_MAX"];
        }
        if (isset($arFields["RIGHT_PRICE_MAX"])) {
            $arFieldsStatistic["RIGHT_PRICE_MAX"] = $arFields["RIGHT_PRICE_MAX"];
        }
        if (isset($arFields["PRICE_MIN"])) {
            $arFieldsStatistic["PRICE_MIN"] = $arFields["PRICE_MIN"];
        }
        if (isset($arFields["RIGHT_PRICE_MIN"])) {
            $arFieldsStatistic["RIGHT_PRICE_MIN"] = $arFields["RIGHT_PRICE_MIN"];
        }

        if (isset($arFields["DATE_START"])) {
            $arFieldsStatistic["DATE_START"] = strtotime($arFields["DATE_START"]);
            $date_reg = explode(" ", $arFields["DATE_START"], 2);
            $date_reg = explode(".", $date_reg[0]);
            $arFieldsStatistic["DATE_START_YEAR"] = $date_reg[2];
            $arFieldsStatistic["DATE_START_MONTH"] = $date_reg[1];
        }

        if (isset($arFields["MIN_PROP"])) {
            $arFieldsStatistic["MIN_PROP"] = $arFields["MIN_PROP"];
        }
			
        $strUpdate = $DB->PrepareUpdate("b_tx_statistic", $arFieldsStatistic);
		if ($strUpdate == "")
			return 0;
        $strSql = "UPDATE b_tx_statistic SET " . $strUpdate . " WHERE LOT_ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return $ID;
    }

    function Delete($ID)
    {
        global $DB;
        $ID = IntVal($ID);
        $DB->Query("DELETE FROM b_tx_statistic WHERE ID = " . $ID, True);
        return true;
    }

    function DeleteAll()
    {
        global $DB;
        $DB->Query("DELETE FROM b_tx_statistic", True);
        return true;
    }

    /**
     * Собираем статистику по пользователям и лотам. В. Филиппов
     */

    //Количество пользователей
    function UsersCount($year, $filter, $month = 12)
    {
        $err_mess = (CTenderixStatistic::err_mess()) . "<br>Function: UsersCount<br>Line: ";
        global $DB;

        $lastday = mktime(23, 59, 59, $month, 0, $year);

        switch ($filter) {
            case "all": //всего
                $where = "DATE_REGISTER <= " . $lastday;
                break;

            case "new": //новых за месяц
                $where = "MONTH_REGISTER =" . $month . " AND YEAR_REGISTER =" . $year;
                break;

            case "year_new": //новых пользователей за год
                $where = "YEAR_REGISTER =" . $year;
                break;

            case "year_all": //всего пользователей на конец года
                $where = "YEAR_REGISTER <=" . $year;
                break;
        }

        $strSql = "SELECT count(USER_ID) CNT FROM b_tx_supplier_statistic WHERE ".$where;
        $arRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        $rsRes = $arRes->Fetch();

        return $rsRes["CNT"];
    }

    function LotsCount($year, $filter, $month = 12, $sections)
    {
        $err_mess = (CTenderixStatistic::err_mess()) . "<br>Function: LotsCount<br>Line: ";
        global $DB;

        $lastday = mktime(23, 59, 59, $month+1, 0, $year);
        $firstday = mktime(0, 0, 0, $month, 1, $year);
		
		

        switch ($filter) {
            case "all": //всего
                $where = "DATE_START >=" . $firstday . " AND DATE_START <=" . $lastday;
                break;

            case "all_for_graph": //всего лотов для графика за месяц
                $where = "DATE_START_MONTH =" . $month . " AND DATE_START_YEAR =" . $year;				
                break;

            case "all_win_for_graph": //лотов, где победитель определён, для диаграммы за месяц
                $where = "DATE_START_MONTH =" . $month . " AND WIN='Y' AND DATE_START_YEAR =" . $year;
                break;

            case "all_fail_for_graph": //несостоявшихся лотов для диаграммы за месяц
                $where = "DATE_START_MONTH =" . $month . " AND FAIL='Y' AND DATE_START_YEAR =" . $year;
                break;

            case "all_active": //всего активных лотов
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND ACTIVE='Y'";
                break;

            case "all_fail": //всего несостоявшихся лотов
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND FAIL='Y'";
                break;

            case "all_win": //всего лотов, где выбран победитель
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND WIN='Y'";
                break;

            case "all_n": //всего лотов (покупка)
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND (TYPE_ID = 'N' OR TYPE_ID = 'T')";
                break;

            case "all_p": //всего лотов (продажа)
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND TYPE_ID = 'P'";
                break;

            case "all_n_active": //всего активных лотов (покупка)
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND (TYPE_ID = 'N' OR TYPE_ID = 'T') AND ACTIVE='Y' AND WIN='N'";
                break;

            case "all_p_active": //всего активных лотов (продажа)
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND TYPE_ID = 'P' AND ACTIVE='Y' AND WIN='N'";
                break;

            case "all_n_fail": //всего несостоявшихся лотов (покупка)
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND (TYPE_ID = 'N' OR TYPE_ID = 'T') AND FAIL='Y'";
                break;

            case "all_p_fail": //всего несостоявшихся лотов (продажа)
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND TYPE_ID = 'P' AND FAIL='Y'";
                break;

            case "all_n_win": //всего лотов, где выбран победитель (покупка)
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND (TYPE_ID = 'N' OR TYPE_ID = 'T') AND WIN='Y'";
                break;

            case "all_p_win": //всего лотов, где выбран победитель (продажа)
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND TYPE_ID = 'P' AND WIN='Y'";
                break;

            case "new": //новых за месяц
                $where = "DATE_START_MONTH =" . $month . " AND DATE_START_YEAR =" . $year;
                break;

            case "year_new": //новых лотов за год
                $where = "DATE_START_YEAR =" . $year;
                break;

            case "year_all": //всего лотов на конец года
                $where = "DATE_START_YEAR <=" . $year;
                break;

            case "year_all_active": //активных лотов на конец года
                $where = "DATE_START_YEAR <=" . $year." AND ACTIVE='Y' AND WIN = 'N'";
                break;

            case "year_all_fail": //несостоявшихся лотов на конец года
                $where = "DATE_START_YEAR <=" . $year." AND FAIL='Y'";
                break;

            case "year_all_win": //всего лотов, где выбран победитель, на конец года
                $where = "DATE_START_YEAR <=" . $year." AND WIN='Y'";
                break;

            case "year_all_n": //всего лотов на конец года (покупка)
                $where = "DATE_START_YEAR <=" . $year. " AND (TYPE_ID = 'N' OR TYPE_ID = 'T')";
                break;

            case "year_all_p": //всего лотов на конец года (продажа)
                $where = "DATE_START_YEAR <=" . $year. " AND TYPE_ID = 'P'";
                break;

            case "year_all_n_active": //всего активных лотов на конец года (покупка)
                $where = "DATE_START_YEAR <=" . $year. " AND TYPE_ID = 'N' AND ACTIVE='Y' AND WIN='N'";
                break;

            case "year_all_p_active": //всего активных лотов на конец года (продажа)
                $where = "DATE_START_YEAR <=" . $year. " AND TYPE_ID = 'P' AND ACTIVE='Y' AND WIN='N'";
                break;

            case "year_all_n_fail": //всего несостоявшихся лотов на конец года (покупка)
                $where = "DATE_START_YEAR <=" . $year. " AND (TYPE_ID = 'N' OR TYPE_ID = 'T') AND FAIL='Y'";
                break;

            case "year_all_p_fail": //всего несостоявшихся лотов на конец года (продажа)
                $where = "DATE_START_YEAR <=" . $year. " AND TYPE_ID = 'P' AND FAIL='Y'";
                break;

            case "year_all_n_win": //всего лотов, где выбран победитель, на конец года (покупка)
                $where = "DATE_START_YEAR <=" . $year. " AND (TYPE_ID = 'N' OR TYPE_ID = 'T') AND WIN='Y'";
                break;

            case "year_all_p_win": //всего лотов, где выбран победитель, на конец года (продажа)
                $where = "DATE_START_YEAR <=" . $year. " AND TYPE_ID = 'P' AND WIN='Y'";
                break;

            case "year_all_for_graph": //для графика за год
                $where = "DATE_START_YEAR =" . $year;
                break;

            case "year_all_win_for_graph": //лотов, где победитель определён, для диаграммы за год
                $where = "DATE_START_YEAR =" . $year . " AND WIN='Y'";
                break;

            case "year_all_fail_for_graph": //несостоявшихся лотов для диаграммы за год
                $where = "DATE_START_YEAR =" . $year . " AND FAIL='Y'";
                break;
        }

        if(!empty($sections)) {
            if (count($sections) == 1) {
                if ($sections[0] != 0) {
                    $and_sect = " AND SECTION_ID = " . $sections[0];
                } else {
                    $and_sect = "";
                }
            } else {
                $and_sect = " AND (";
                for ($i = 0; $i < count($sections); $i++) {
                    $and_sect .= "SECTION_ID=" . $sections[$i];
                    if ($i != count($sections) - 1) {
                        $and_sect .= " OR ";
                    } else {
                        $and_sect .= ")";
                    }
                }
            }
        } else {
            $and_sect = "";
        }

        $strSql = "SELECT count(LOT_ID) CNT FROM b_tx_statistic WHERE ".$where.$and_sect;
		/* echo '<pre>';
		print_r($strSql);
		echo '</pre>'; */
        //__($strSql);
        $arRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        $rsRes = $arRes->Fetch();

        return $rsRes["CNT"];
    }

    function LotsPrice($year, $filter, $month = 12, $sections) {
        $err_mess = (CTenderixStatistic::err_mess()) . "<br>Function: LotsPrice<br>Line: ";
        global $DB;

        $lastday = mktime(23, 59, 59, $month+1, 0, $year);
		$firstday = mktime(0, 0, 0, $month, 1, $year);

        switch ($filter) {
            case "all_n": //общая стоимость лотов (покупка)
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND TYPE_ID = 'N' AND WIN ='Y'";
                break;

            case "all_p": //общая стоимость лотов (продажа)
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND TYPE_ID = 'P' AND WIN ='Y'";
                break;

            case "year_all_n": //общая стоимость лотов на конец года (покупка)
                $where = "DATE_START_YEAR <=" . $year. " AND TYPE_ID = 'N' AND WIN ='Y'";
                break;

            case "year_all_p": //общая стоимость лотов на конец года (продажа)
                $where = "DATE_START_YEAR <=" . $year. " AND TYPE_ID = 'P' AND WIN ='Y'";
                break;

            case "all": //общая стоимость лотов
                $where = "DATE_START_MONTH =" . $month . " AND DATE_START_YEAR =" . $year .  " AND WIN ='Y'";
                break;

            case "year_all": //общая стоимость лотов на конец года (продажа)
                $where = "DATE_START_YEAR =" . $year . "  AND WIN ='Y'";
                break;
        }

        if(!empty($sections)) {
            if (count($sections) == 1) {
                if ($sections[0] != 0) {
                    $and_sect = " AND SECTION_ID = " . $sections[0];
                } else {
                    $and_sect = "";
                }
            } else {
                $and_sect = " AND (";
                for ($i = 0; $i < count($sections); $i++) {
                    $and_sect .= "SECTION_ID=" . $sections[$i];
                    if ($i != count($sections) - 1) {
                        $and_sect .= " OR ";
                    } else {
                        $and_sect .= ")";
                    }
                }
            }
        } else {
            $and_sect = "";
        }

        $strSql = "SELECT SUM(LOT_PRICE) PRICE FROM b_tx_statistic WHERE ".$where.$and_sect;
        //__($strSql);
        $arRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        $rsRes = $arRes->Fetch();

        return $rsRes["PRICE"];
    }

    function LotsMinPrice($year, $filter, $month = 12, $sections) {
        $err_mess = (CTenderixStatistic::err_mess()) . "<br>Function: LotsPrice<br>Line: ";
        global $DB;
		
		$firstday = mktime(0, 0, 0, $month, 1, $year);
        $lastday = mktime(23, 59, 59, $month+1, 0, $year);

        switch ($filter) {
            case "all_n": //всего
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND TYPE_ID = 'N'";
                break;

            case "all_p": //всего
                $where = "(DATE_START >=" . $firstday . " AND DATE_START <="  . $lastday . ") AND TYPE_ID = 'P'";
                break;

            case "year_all_n": //всего пользователей на конец года (покупка)
                $where = "DATE_START_YEAR =" . $year. " AND TYPE_ID = 'N'";
                break;

            case "year_all_p": //всего пользователей на конец года (продажа)
                $where = "DATE_START_YEAR =" . $year. " AND TYPE_ID = 'P'";
                break;
        }

        if(!empty($sections)) {
            if (count($sections) == 1) {
                if ($sections[0] != 0) {
                    $and_sect = " AND SECTION_ID = " . $sections[0];
                } else {
                    $and_sect = "";
                }
            } else {
                $and_sect = " AND (";
                for ($i = 0; $i < count($sections); $i++) {
                    $and_sect .= "SECTION_ID=" . $sections[$i];
                    if ($i != count($sections) - 1) {
                        $and_sect .= " OR ";
                    } else {
                        $and_sect .= ")";
                    }
                }
            }
        } else {
            $and_sect = "";
        }

        $strSql = "SELECT SUM(MIN_PROP) PRICE FROM b_tx_statistic WHERE ".$where.$and_sect;
        //__($strSql);
        $arRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        $rsRes = $arRes->Fetch();

        return $rsRes["PRICE"];
    }

    function YearsRegisterUser() {
        $err_mess = (CTenderixStatistic::err_mess()) . "<br>Function: MinYearRegisterUser<br>Line: ";
        global $DB;

        $strSql = "SELECT DISTINCT DATE_START_YEAR FROM b_tx_statistic";
        $arRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        while($rsRes = $arRes->Fetch()) {
            $years[] =$rsRes["DATE_START_YEAR"];
        }

        $strSql = "SELECT DISTINCT YEAR_REGISTER FROM b_tx_supplier_statistic";
        $arRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        while($rsRes = $arRes->Fetch()) {
            $years[] =$rsRes["YEAR_REGISTER"];
        }

        $years = array_diff($years, array(0, null));
        $years = array_unique($years);

        asort($years);

        return $years;
    }

    function AddUserToStatistic($ID) {
        $err_mess = (CTenderixStatistic::err_mess()) . "<br>Function: AddUserToStatistic<br>Line: ";
        global $DB;

        $strSql = "SELECT ID, ACTIVE, DATE_REGISTER AS DATE_REGISTER
            FROM b_user b
            RIGHT JOIN b_tx_supplier s ON b.ID = s.USER_ID
            WHERE b.ID = ". $ID;

        $arRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        while ($rsRes = $arRes->Fetch()) {
            $arSup[] = $rsRes;
        }

        //__($arSup);

        foreach($arSup as $arkey) {
            $supplier["USER_ID"] = $arkey["ID"];
            $supplier["ACTIVE"] = $arkey["ACTIVE"];
            $supplier["DATE_REGISTER"] = strtotime($arkey["DATE_REGISTER"]);
            $date_reg = explode(" ", $arkey["DATE_REGISTER"], 2);
            $date_reg = explode("-", $date_reg[0]);
            $supplier["YEAR_REGISTER"] = $date_reg[0];
            $supplier["MONTH_REGISTER"] = $date_reg[1];

            $arInsert = $DB->PrepareInsert("b_tx_supplier_statistic", $supplier);
            $strSql = "INSERT INTO b_tx_supplier_statistic(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
            $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        }
    }

    function SelectUserFromStatistic($ID) {
        $err_mess = (CTenderixStatistic::err_mess()) . "<br>Function: AddUserToStatistic<br>Line: ";
        global $DB;

        $strSql = "SELECT USER_ID FROM b_tx_supplier_statistic WHERE USER_ID =". $ID;

        $arRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        $rsRes = $arRes->Fetch();

        return $rsRes["USER_ID"];
    }
}

?>