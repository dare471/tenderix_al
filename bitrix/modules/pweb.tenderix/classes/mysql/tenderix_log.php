<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/classes/general/tenderix_log.php");

class CTenderixLog extends CAllTenderLog {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixLog<br>File: " . __FILE__;
    }
	
	//Добработанная функция. Получает массив вида EMAIL => КОЛИЧЕСТВО_ОТПРАВЛЕННЫХ_ПИСЕМ О ДОБАВЛЕННОМ ЛОТЕ
	function GetListNewLotEvent($ID) {
		$result = array();
		$res = self::GetList(array(), array("OBJECT" => $ID, "EVENT" => 'TENDERIX_NEW_LOT'));
		while ($arList = $res->Fetch()) {
			
			$desc = unserialize($arList['DESCRIPTION_EX']);
			//print_r($desc);
			
			if (isset($desc['FIELDS']['EMAIL_TO'])) 
				$result[$desc['FIELDS']['EMAIL_TO']] += 1;
		}
		return $result;		
	}

    function GetList($arOrder=Array(), $arFilter=Array()) {
        $err_mess = (CTenderixLog::err_mess()) . "<br>Function: GetList<br>Line: ";
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
                    case "EVENT":
                        $arSqlSearch[] = GetFilterQuery("C.EVENT", $val, "N");
                        break;
                    case "OBJECT":
                        $arSqlSearch[] = GetFilterQuery("C.OBJECT", $val, "N");
                        break;
                    case "USER_ID":
                        $arSqlSearch[] = GetFilterQuery("C.USER_ID", $val, "N");
                        break;
                    case "DESCRIPTION":
                        $arSqlSearch[] = GetFilterQuery("C.DESCRIPTION", $val, "Y");
                        break;
                }
            }
        }

        $arSqlOrder = array();
        $strSqlOrder = "";
        if (count($arOrder) > 0) {
            foreach ($arOrder as $by => $order) {
                $by = strtoupper($by);
                $order = strtoupper($order);

                if ($by == "ID")
                    $arSqlOrder[] = "C.ID " . ($order == "ASC" ? "ASC" : "DESC");
                elseif ($by == "TIMESTAMP_X")
                    $arSqlOrder[] = "C.TIMESTAMP_X " . ($order == "ASC" ? "ASC" : "DESC");
                elseif ($by == "EVENT")
                    $arSqlOrder[] = "C.EVENT " . ($order == "ASC" ? "ASC" : "DESC");
                elseif ($by == "USER_ID")
                    $arSqlOrder[] = "C.USER_ID " . ($order == "ASC" ? "ASC" : "DESC");
                elseif ($by == "OBJECT")
                    $arSqlOrder[] = "C.OBJECT " . ($order == "ASC" ? "ASC" : "DESC");
                else {
                    $by = "TIMESTAMP_X";
                    $arSqlOrder[] = "C.TIMESTAMP_X " . ($order == "ASC" ? "ASC" : "DESC");
                }
            }
            $strSqlOrder = "ORDER BY " . implode(", ", $arSqlOrder);
        }
        
        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);


        $strSql = "SELECT 
                        C.*, 
                        " . $DB->DateToCharFunction("C.TIMESTAMP_X") . " TIMESTAMP_X,
                        CONCAT(CC.LAST_NAME,' ',CC.NAME,' ',CC.SECOND_NAME) FIO
                   FROM b_tx_log C 
                   LEFT JOIN b_user CC ON (C.USER_ID = CC.ID)
                   WHERE " . $strSqlSearch . " GROUP BY C.ID " . $strSqlOrder;
				   

        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function Add($arFields) {
        global $DB;

        $arInsert = $DB->PrepareInsert("b_tx_log", $arFields);
        $strSql = "INSERT INTO b_tx_log(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());
        return $ID;
    }

    function Update($ID, $arFields) {
        global $DB;
        $ID = intVal($ID);
        if ($ID <= 0)
            return False;

        $strUpdate = $DB->PrepareUpdate("b_tx_log", $arFields);
        $strSql = "UPDATE b_tx_log SET " . $strUpdate . " WHERE ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return $ID;
    }

    function Delete($ID) {
        global $DB;
        $ID = IntVal($ID);
        $DB->Query("DELETE FROM b_tx_log WHERE ID = " . $ID, True);
        return true;
    }

}

?>
