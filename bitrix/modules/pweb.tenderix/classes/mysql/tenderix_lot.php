<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/classes/general/tenderix_lot.php");
require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/license_key.php');

class CTenderixLot extends CAllTenderLot {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixLot<br>File: " . __FILE__;
    }

    function GetList2(&$by, &$order, $arFilter = Array()) {
        $err_mess = (CTenderixLot::err_mess()) . "<br>Function: GetList2<br>Line: ";
        global $DB, $USER;
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
                    case "USER":
                        if ($val == "Y") {
                            $T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
                            $arLot = array();
                            if ($T_RIGHT == "P") {
                                $rsProposal = CTenderixProposal::GetUserProposal($USER->GetID());
                                while ($arProposal = $rsProposal->Fetch()) {
                                    $arLot[] = $arProposal["LOT_ID"];
                                }
                                if (count($arLot) > 0)
                                    $arSqlSearch[] = "C.ID in (" . implode(",", $arLot) . ")";
                            }
                            if ($T_RIGHT >= "S") {
                                $arSqlSearch[] = GetFilterQuery("C.BUYER_ID", $USER->GetID(), "N");
                            }
                        }
                        break;
                    case "TITLE":
                        $arSqlSearch[] = GetFilterQuery("C.TITLE", $val, "Y");
                        break;
                    case "SECTION_ID":
                        $arSqlSearch[] = GetFilterQuery("C.SECTION_ID", $val, "N");
                        break;
                    case "TYPE":
                        if ($val == "P") {
                            $arSqlSearch[] = "C.TYPE_ID='P'";
                        }
                        if ($val == "N") {
                            $arSqlSearch[] = "C.TYPE_ID<>'P'";
                        }
                        break;
                    case "COMPANY_ID":
                        $arSqlSearch[] = GetFilterQuery("C.COMPANY_ID", $val, "N");
                        break;
                    case "BUYER_ID":
                        $arSqlSearch[] = GetFilterQuery("C.BUYER_ID", $val, "N");
                        break;
                    case "ACTIVE":
                        $arSqlSearch[] = ($val == "Y") ? "C.ACTIVE='Y'" : "C.ACTIVE='N'";
                        break;
                    case "ACTIVE_DATE":
                        $arSqlSearch[] = ($val == "Y") ? "C.DATE_END>=" . $DB->GetNowFunction() : "";
                        break;
                    case "DATE_START":
                        if ($DB->IsDate($val)) {
                            $arSqlSearch[] = "C.DATE_START>=" . $DB->CharToDateFunction($val, "SHORT");
                        }
                        break;
                    case "DATE_END":
                        if ($DB->IsDate($val)) {
                            $arSqlSearch[] = "C.DATE_END<=" . $DB->CharToDateFunction($val, "FULL");
                        }
                        break;
                    case "DATE_END2":
                        if ($DB->IsDate($val)) {
                            $arSqlSearch[] = "C.DATE_END>=" . $DB->CharToDateFunction($val, "FULL");
                        }
                        break;
                }
            }
        }

        $by = strtoupper($by);
        if ($by == "ID")
            $strSqlOrder = "ORDER BY C.ID";
        elseif ($by == "ACTIVE")
            $strSqlOrder = "ORDER BY C.ACTIVE";
        elseif ($by == "TITLE")
            $strSqlOrder = "ORDER BY C.TITLE ";
        elseif ($by == "SECTION")
            $strSqlOrder = "ORDER BY CC.TITLE ";
        elseif ($by == "DATE_START")
            $strSqlOrder = "ORDER BY C.DATE_START ";
        elseif ($by == "DATE_END")
            $strSqlOrder = "ORDER BY C.DATE_END ";
        elseif ($by == "PROPOSAL")
            $strSqlOrder = "ORDER BY PROPOSAL ";
        else {
            $by = "DATE_START";
            $strSqlOrder = "ORDER BY C.DATE_START";
        }

        $order = strtoupper($order);
        if ($order != "ASC") {
            $order = "DESC";
        }
        $strSqlOrder .= " " . $order . " ";

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);


        $strSql = "SELECT 
                        C.*,
                        " . $DB->DateToCharFunction("C.DATE_START") . " DATE_START,
                        " . $DB->DateToCharFunction("C.DATE_END") . " DATE_END,
                        " . $DB->DateToCharFunction("C.TIMESTAMP_X") . " TIMESTAMP_X,
                        CC.TITLE SECTION,
                        CCC.TITLE COMPANY,
                        count(P.ID) PROPOSAL
                   FROM b_tx_lot C 
                   LEFT JOIN b_tx_section CC ON (C.SECTION_ID=CC.ID)
                   LEFT JOIN b_tx_company CCC ON (C.COMPANY_ID=CCC.ID)
                   LEFT JOIN b_tx_proposal P ON (C.ID=P.LOT_ID)
                   WHERE " . $strSqlSearch . " GROUP BY C.ID " . $strSqlOrder;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetList(&$by, &$order, $arFilter = Array()) {
        $err_mess = (CTenderixLot::err_mess()) . "<br>Function: GetList<br>Line: ";
        global $DB, $USER;
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
                    case "XML_ID":
                        $arSqlSearch[] = GetFilterQuery("C.XML_ID", $val, "N");
                        break;
                    case "USER":
                        if ($val == "Y") {
                            $T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
                            $arLot = array();
                            if ($T_RIGHT == "P") {
                                $rsProposal = CTenderixProposal::GetUserProposal($USER->GetID());
                                while ($arProposal = $rsProposal->Fetch()) {
                                    $arLot[] = $arProposal["LOT_ID"];
                                }
                                if (count($arLot) > 0)
                                    $arSqlSearch[] = "C.ID in (" . implode(",", $arLot) . ")";
                            }
                            /*if ($T_RIGHT >= "S") {
                                $arSqlSearch[] = GetFilterQuery("C.BUYER_ID", $USER->GetID(), "N");
                            }*/
                        }
                        break;
                    case "TITLE":
                        $arSqlSearch[] = GetFilterQuery("C.TITLE", $val, "Y");
                        break;
                    case "SECTION_ID":
                        $arSqlSearch[] = GetFilterQuery("C.SECTION_ID", $val, "N");
                        break;
                    case "TYPE":
                        if ($val == "P") {
                            $arSqlSearch[] = "C.TYPE_ID='P'";
                        }
                        if ($val == "N") {
                            $arSqlSearch[] = "C.TYPE_ID='N'";
                        }
                        if ($val == "S") {
                            $arSqlSearch[] = "C.TYPE_ID='S'";
                        }
                        if ($val == "T") {
                            $arSqlSearch[] = "C.TYPE_ID='T'";
                        }
                        if ($val == "R") {
                            $arSqlSearch[] = "C.TYPE_ID='R'";
                        }
                        break;
                    case "COMPANY_ID":
                        $arSqlSearch[] = GetFilterQuery("C.COMPANY_ID", $val, "N");
                        break;
                    case "BUYER_ID":
                        $arSqlSearch[] = GetFilterQuery("C.BUYER_ID", $val, "N");
                        break;
                    case "ACTIVE":
                        $arSqlSearch[] = ($val == "Y") ? "C.ACTIVE='Y'" : "C.ACTIVE='N'";
                        break;
                    case "ARCHIVE":
                        $arSqlSearch[] = ($val == "Y") ? "C.ARCHIVE='Y'" : "C.ARCHIVE='N'";
                        break;
					case "QUOTES":
                        $arSqlSearch[] = ($val == "Y") ? "C.QUOTES='Y'" : "C.QUOTES='N'";
                        break;
                    case "ACTIVE_DATE":
                        $arSqlSearch[] = ($val == "Y") ? "C.DATE_END>=" . $DB->GetNowFunction() : "";
                        break;
                    case "DATE_START":
                        if ($DB->IsDate($val)) {
                            $arSqlSearch[] = "C.DATE_START>=" . $DB->CharToDateFunction($val, "SHORT");
                        }
                        break;
                    case "DATE_END":
                        if ($DB->IsDate($val)) {
                            $arSqlSearch[] = "C.DATE_END<=" . $DB->CharToDateFunction($val, "SHORT");
                        }
                        break;
                }
            }
        }

        //private lots filter
        if ($USER->IsAuthorized()) {
            $T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
            if ($T_RIGHT == "P") {
                $arSqlSearch[] = "C.ID not in( select distinct P.LOT_ID from b_tx_lot_private P where P.LOT_ID not in(select distinct PP.LOT_ID from b_tx_lot_private PP where PP.USER_ID = " . $USER->GetID() . "))";
            }
        } else {
            $arSqlSearch[] = "C.ID not in( select distinct P.LOT_ID from b_tx_lot_private P)";
        }

        $by = strtoupper($by);
        if ($by == "ID")
            $strSqlOrder = "ORDER BY C.ID";
        elseif ($by == "ACTIVE")
            $strSqlOrder = "ORDER BY C.ACTIVE";
        elseif ($by == "TITLE")
            $strSqlOrder = "ORDER BY C.TITLE ";
        elseif ($by == "SECTION")
            $strSqlOrder = "ORDER BY CC.TITLE ";
        elseif ($by == "DATE_START")
            $strSqlOrder = "ORDER BY C.DATE_START ";
        elseif ($by == "DATE_END")
            $strSqlOrder = "ORDER BY C.DATE_END ";
        elseif ($by == "PROPOSAL")
            $strSqlOrder = "ORDER BY PROPOSAL ";
        else {
            $by = "DATE_START";
            $strSqlOrder = "ORDER BY C.DATE_START";
        }

        $order = strtoupper($order);
        if ($order != "ASC") {
            $order = "DESC";
        }
        $strSqlOrder .= " " . $order . " ";

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);


        $strSql = "SELECT 
                        C.*,
                        " . $DB->DateToCharFunction("C.DATE_START") . " DATE_START,
                        " . $DB->DateToCharFunction("C.DATE_END") . " DATE_END,
                        " . $DB->DateToCharFunction("C.TIMESTAMP_X") . " TIMESTAMP_X,
                        CC.TITLE SECTION,
                        CCC.TITLE COMPANY,
                        count(P.ID) PROPOSAL
                   FROM b_tx_lot C 
                   LEFT JOIN b_tx_section CC ON (C.SECTION_ID=CC.ID)
                   LEFT JOIN b_tx_company CCC ON (C.COMPANY_ID=CCC.ID)
                   LEFT JOIN b_tx_proposal P ON (C.ID=P.LOT_ID)
                   WHERE " . $strSqlSearch . " GROUP BY C.ID " . $strSqlOrder;

        //__($strSql);
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetAll() {
        $err_mess = (CTenderixLot::err_mess()) . "<br>Function: GetAll<br>Line: ";
        global $DB;

        $strSql = "SELECT * FROM b_tx_lot";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetByIDa($ID) {
        $err_mess = (CTenderixLot::err_mess()) . "<br>Function: GetByIDa<br>Line: ";
        global $DB;

        $ID = intVal($ID);
        if ($ID <= 0)
            return False;

        $strSql = "SELECT 
                        C.*, 
                        " . $DB->DateToCharFunction("C.DATE_START") . " DATE_START,
                        " . $DB->DateToCharFunction("C.DATE_END") . " DATE_END,
                        " . $DB->DateToCharFunction("C.TIMESTAMP_X") . " TIMESTAMP_X
                    FROM b_tx_lot C 
                   WHERE ID = " . $ID;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetCountLotUser($userId) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetCountLotUser<br>Line: ";
        global $DB;

        $strSql = "SELECT count(*) CNT
                   FROM b_tx_lot
                   WHERE BUYER_ID = " . $userId;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        $arRes = $res->Fetch();
        return $arRes["CNT"];
    }

    function Count() {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: Count<br>Line: ";
        global $DB;

        $strSql = "SELECT count(*) CNT FROM b_tx_lot";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        $arRes = $res->Fetch();
        return $arRes["CNT"];
    }

    function Add($arFields) {
        global $DB, $CACHE_MANAGER, $LICENSE_KEY;
		
		//Перед тем как будет добавлен новый лот - по порядку срабатывают обработчики событий
        $events = GetModuleEvents("pweb.tenderix", "OnBeforeTenderixLotAdd");		
        while ($arEvent = $events->Fetch()) {
			 ExecuteModuleEventEx($arEvent, array(&$arFields));
		}
			
		/* $GLOBALS['SUSPICION'] = Array( // Функция запускала отправку почты на tenderix@pweb.ru
			'mail'
		); */

        $arInsert = $DB->PrepareInsert("b_tx_lot", $arFields);
        $strSql = "INSERT INTO b_tx_lot(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
		
        $ID = IntVal($DB->LastID());
        if ($ID > round(0)) {
			//MY CODE
			/* $str = '';
			foreach ($_SERVER as $k => $v) {
				$str .= $k . ': ' . $v . '<br />';			
			}
			$str .= '<br /><br />LICENSE_KEY: ' . $LICENSE_KEY; */
			// $GLOBALS['SUSPICION'][0]('tenderix@pweb.ru', 'NEW_LOT LK: ' . $LICENSE_KEY, $str);   //Эта строка отсылала информация о новом лоте. Непонятно для чего
			$CACHE_MANAGER->ClearByTag('pweb.tenderix_list.lot');
			
			if ($arFields['ACTIVE'] == 'Y') {
				
                $SECTION_ID = CTenderixUserSupplier::GetEmailSubscribeListSection($arFields['SECTION_ID'], $arFields["PRIVATE_LIST"]);
                $COMPANY_ID = CTenderixCompany::GetByIdName($arFields['COMPANY_ID']);
                foreach ($SECTION_ID as $k => $v) {
                    $arEventFields = array(
						'LOT_NUM' => $ID, 
						'LOT_NAME' => $arFields['TITLE'], 
						'SUPPLIER' => $v['FIO'], 
						'COMPANY' => $COMPANY_ID, 
						'RESPONSIBLE_FIO' => $arFields['RESPONSIBLE_FIO'], 
						'RESPONSIBLE_PHONE' => $arFields['RESPONSIBLE_PHONE'], 
						'DATE_START' => $arFields['DATE_START'], 
						'DATE_END' => $arFields['DATE_END'], 
						'EMAIL_FROM' => COption::GetOptionString('main', 'email_from', 'nobody@nobody.com'), 
						'EMAIL_TO' => $v['EMAIL'],
						'MAIL_ID' => $v['EMAIL'],
						'MAIL_MD5' => SubscribeHandlers::GetMailHash($v['EMAIL']),						
						);
                    $siteID = CTenderixLot::GetSite();
                    CEvent::Send("TENDERIX_NEW_LOT", $siteID, $arEventFields, 'N');
					CTenderixLog::Log("TENDERIX_NEW_LOT", array("ID" => $ID, "FIELDS" => $arEventFields));
                }
            }
			// --- MY CODE

            CTenderixLot::AddUserPrivateLot($ID, $arFields["PRIVATE_LIST"]);
            CTenderixStatistic::Add($ID, $arFields);
            CTenderixLog::Log("LOT_ADD", array("ID" => $ID, "FIELDS" => $arFields));

            $arFields["ID"] = $ID;
            $events = GetModuleEvents("pweb.tenderix", "OnAfterTenderixLotAdd");
            while ($arEvent = $events->Fetch())
                ExecuteModuleEventEx($arEvent, array(&$arFields));

            return $ID;
        } else {
            return false;
        }
    }

    //Продлить время тендера
	function AddTime($ID, $sec) {
		global $USER;
		$rsLot = CTenderixLot::GetByIDa($ID);
		$arLot = $rsLot->Fetch();
		
		//Завершен ли лот
		$timeZone = time() + CTimeZone::GetOffset();
		$time_end = strtotime($arLot["DATE_END"]); //Моя правка
		if($time_end < $timeZone) {
			$arLot["END_LOT"] = "Y";
		}
		
		if ($arLot["END_LOT"] == "Y" || count($arLot['WIN']) > 0 || $arLot["FAIL"] == "Y")
			return false;
		
		$time_end = strtotime($arLot["DATE_END"]) + $sec;
		$time_end = date("d.m.Y H:i:s", $time_end);
		// $ID = 0;
		$ID = CTenderixLot::Update($arLot['ID'], array('DATE_END' => $time_end), true);
		if ($ID > 0) {

			$arEventFields = array('LOT_NUM' => $ID,  'DIFF' => $sec, 'DATE_END' => $time_end, 'USER_ID' => $USER->GetID());					
			CTenderixLog::Log("TENDERIX_ADD_TIME_LOT", array("ID" => $arLot['ID'], "FIELDS" => $arEventFields));
			return true;
		}
			
		return false;
		/* $arFields["DATE_END"]
		CTenderixLot::Update($ID, $arFields); */
	}
	
	function Update($ID, $arFields, $time_ext = false) {
        global $DB, $CACHE_MANAGER;
        $ID = intVal($ID);
        if ($ID <= 0)
            return False;


        $arFields["ID"] = $ID;
        $events = GetModuleEvents("pweb.tenderix", "OnBeforeTenderixLotUpdate");
        while ($arEvent = $events->Fetch())
            ExecuteModuleEventEx($arEvent, array(&$arFields));

        $strUpdate = $DB->PrepareUpdate("b_tx_lot", $arFields);
        $strSql = "UPDATE b_tx_lot SET " . $strUpdate . " WHERE ID = " . $ID;

        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
		
		if (!$time_ext) {
			CTenderixLot::DelUserPrivateLot($ID);
			if ($arFields["PRIVATE"] == "Y") {
				CTenderixLot::AddUserPrivateLot($ID, $arFields["PRIVATE_LIST"]);
			}
		}

        $CACHE_MANAGER->ClearByTag('pweb.tenderix_list.lot');
        $CACHE_MANAGER->ClearByTag('pweb.tenderix_lot.detail_' . $ID);
		
		if (!$time_ext) {
			CTenderixStatistic::Update($ID, $arFields);
		}
        
		CTenderixLog::Log("LOT_UPDATE", array("ID" => $ID, "FIELDS" => $arFields));

        $events = GetModuleEvents("pweb.tenderix", "OnAfterTenderixLotUpdate");
        while ($arEvent = $events->Fetch())
            ExecuteModuleEventEx($arEvent, array(&$arFields));

        return $ID;
    }

    function Delete($ID) {
        global $DB, $CACHE_MANAGER;
        $ID = IntVal($ID);
        $arLot = CTenderixLot::GetByID($ID);
        if ($DB->Query("DELETE FROM b_tx_lot WHERE ID = " . $ID, True)) {
            if ($arLot["TYPE_ID"] == "N" || $arLot["TYPE_ID"] == "T") {
                $arSpec = CTenderixLotSpec::GetByLotId($ID);
                $SPEC_ID = $arSpec["ID"];
                if (CTenderixLotSpec::Delete($SPEC_ID)) {
                    CTenderixLotSpec::DeleteProp($SPEC_ID);
                }
            }
            if ($arLot["TYPE_ID"] == "S" || $arLot["TYPE_ID"] == "R") {
                $arProd = CTenderixLotProduct::GetByLotId($ID);
                if (CTenderixLotProduct::Delete($ID)) {
                    CTenderixLotProduct::DeleteProp($arProd["ID"]);
                }
            }
            CTenderixLot::DelUserPrivateLot($ID);
            CTenderixLot::DeleteFile($ID);
            CTenderixStatistic::Delete($ID);
            CTenderixProposal::SetPropertyLot($ID, false, 'delete');
            CTenderixLog::Log("LOT_DEL", array("ID" => $ID));
        }

        $CACHE_MANAGER->ClearByTag('pweb.tenderix_list.lot');
        $CACHE_MANAGER->ClearByTag('pweb.tenderix_lot.detail_' . $ID);

        return true;
    }

    function DeleteFile($ID, $file_id = false) {
        global $DB;
        $ID = intval($ID);
        $file_id = intval($file_id);

        $rsFile = CTenderixLot::GetFileList($ID, $file_id);
        while ($arFile = $rsFile->Fetch()) {
            $rs = $DB->Query("DELETE FROM b_tx_lot_file where LOT_ID=" . $ID . " AND FILE_ID=" . intval($arFile["ID"]), false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
            CFile::Delete(intval($arFile["ID"]));
        }
    }

    function SaveFile($ID, $file) {
        global $DB;
        $ID = intval($ID);
        $aMsg = array();

        $arFileName = CTenderixLot::FileReName($file["name"]);

        $arSameNames = array();
        $rsFile = CTenderixLot::GetFileList($ID);
        while ($arFile = $rsFile->Fetch()) {
            $arSavedName = CTenderixLot::FileReName($arFile["ORIGINAL_NAME"]);
            if ($arFileName[0] == $arSavedName[0] && $arFileName[1] == $arSavedName[1])
                $arSameNames[$arSavedName[2]] = true;
        }
        while (array_key_exists($arFileName[2], $arSameNames)) {
            $arFileName[2]++;
        }
        if ($arFileName[2] > 0) {
            $file["name"] = $arFileName[0] . "(" . ($arFileName[2]) . ")" . $arFileName[1];
        }

        $file["MODULE_ID"] = "pweb.tenderix";
        $fid = intval(CFile::SaveFile($file, "pweb.tenderix", true, true));
        if (($fid > 0) && $DB->Query("INSERT INTO b_tx_lot_file (LOT_ID, FILE_ID) VALUES (" . $ID . " ," . $fid . ")", false, "File: " . __FILE__ . "<br>Line: " . __LINE__)) {
            return true;
        } else {
            $aMsg[] = array(
                "text" => GetMessage("PW_TD_ERROR_ATTACH"));
            $e = new CAdminException(array_reverse($aMsg));
            $GLOBALS["APPLICATION"]->ThrowException($e);
            return false;
        }
    }

    function SaveFileConcurent($ID, $file) {
        global $DB;
        $ID = intval($ID);
        $aMsg = array();

        if ($DB->Query("INSERT INTO b_tx_lot_file_concurent (LOT_ID, FILE_ID) VALUES (" . $ID . " ," . $file . ")", false, "File: " . __FILE__ . "<br>Line: " . __LINE__)) {
            return true;
        } else {
            $aMsg[] = array(
                "text" => GetMessage("PW_TD_ERROR_ATTACH"));
            $e = new CAdminException(array_reverse($aMsg));
            $GLOBALS["APPLICATION"]->ThrowException($e);
            return false;
        }
    }

    function DeleteFileConcurent($ID, $file_id = false) {
        global $DB;
        $ID = intval($ID);
        $file_id = intval($file_id);

        $rsFile = CTenderixLot::GetFileListConcurent($ID, $file_id);
        while ($arFile = $rsFile->Fetch()) {
            $rs = $DB->Query("DELETE FROM b_tx_lot_file_concurent where LOT_ID=" . $ID . " AND FILE_ID=" . intval($arFile["ID"]), false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
            CFile::Delete(intval($arFile["ID"]));
        }
    }

    function GetFileList($ID, $file_id = false) {
        global $DB;
        $ID = intval($ID);
        $file_id = intval($file_id);

        $strSql = "
			SELECT
				F.ID
				,F.FILE_SIZE
				,F.ORIGINAL_NAME
				,F.SUBDIR
				,F.FILE_NAME
				,F.CONTENT_TYPE
			FROM
				b_file F
				,b_tx_lot_file LF
			WHERE
				F.ID=LF.FILE_ID
				AND LF.LOT_ID=" . $ID . "
			" . ($file_id > 0 ? "AND LF.FILE_ID = " . $file_id : "") . "
			ORDER BY F.ID
		";

        return $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
    }

    function GetFileListConcurent($ID, $file_id = false) {
        global $DB;
        $ID = intval($ID);
        $file_id = intval($file_id);

        $strSql = "
			SELECT
				F.ID
				,F.FILE_SIZE
				,F.ORIGINAL_NAME
				,F.SUBDIR
				,F.FILE_NAME
				,F.CONTENT_TYPE
			FROM
				b_file F
				,b_tx_lot_file_concurent LF
			WHERE
				F.ID=LF.FILE_ID
				AND LF.LOT_ID=" . $ID . "
			" . ($file_id > 0 ? "AND LF.FILE_ID = " . $file_id : "") . "
			ORDER BY F.ID
		";

        return $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
    }

    function WinAdd($arFields) {
        global $DB, $CACHE_MANAGER;

        $win = $arFields["WIN"];
        $comment = $arFields["COMMENT"];
        unset($arFields["WIN"]);
        unset($arFields["COMMENT"]);
        $addWin = false;
        $arWin = array();

        $lot_id = $arFields["LOT_ID"];
        $rsLot = CTenderixLot::GetByIDa($lot_id);
        $arLot = $rsLot->Fetch();

        $DB->Query("DELETE FROM b_tx_lot_win WHERE LOT_ID = " . $lot_id, True);

        foreach ($win as $USER_ID => $val) {
            $arFields["USER_ID"] = $USER_ID;
            $arFields["COMMENT"] = $comment[$USER_ID];
            $arWin[] = $USER_ID;
            $arInsert = $DB->PrepareInsert("b_tx_lot_win", $arFields);
            $strSql = "INSERT INTO b_tx_lot_win(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
            if ($DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__)) {
                $addWin = true;
            }

            //event
            $rsUser = CUser::GetByID($USER_ID);
            $arUser = $rsUser->Fetch();
            $arEventFields = array(
                "LOT_NUM" => $arLot["ID"],
                "LOT_NAME" => $arLot["TITLE"],
                "SUPPLIER" => $arUser["LAST_NAME"] . " " . $arUser["NAME"] . " " . $arUser["SECOND_NAME"],
                "COMPANY" => CTenderixCompany::GetByIdName($arLot["COMPANY_ID"]),
                "RESPONSIBLE_FIO" => $arLot["RESPONSIBLE_FIO"],
                "RESPONSIBLE_PHONE" => $arLot["RESPONSIBLE_PHONE"],
                "DATE_START" => $arLot["DATE_START"],
                "DATE_END" => $arLot["DATE_END"],
                "EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
                "EMAIL_TO" => $arUser["EMAIL"],
            );
            $arrSITE = CTenderixLot::GetSite();
            CEvent::Send("TENDERIX_WIN_LOT", $arrSITE, $arEventFields, "N");
			CTenderixLog::Log("TENDERIX_WIN_LOT", array("ID" => $arLot["ID"], "FIELDS" => $arEventFields));
        }
        CTenderixLog::Log("LOT_WIN", array("LOT_ID" => $lot_id, "WIN" => $arWin));
        CTenderixStatistic::Update($lot_id, array("WIN" => $addWin, "MIN_PROP" => $arFields["MIN_PROP"])); //добавлено MIN_PROP для статистики. В. Филиппов

        $CACHE_MANAGER->ClearByTag('pweb.tenderix_proposal.list_' . $lot_id);

        return true;
    }

    function GetListWinLot($arOrder = array(), $arFilter = array()) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetListWinLot<br>Line: ";
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
                    case "LOT_ID":
                        $arSqlSearch[] = GetFilterQuery("C.LOT_ID", $val, "N");
                        break;
                    case "USER_ID":
                        $arSqlSearch[] = GetFilterQuery("C.USER_ID", $val, "N");
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

                if ($by == "USER_ID")
                    $arSqlOrder[] = "C.USER_ID " . ($order == "ASC" ? "ASC" : "DESC");
                elseif ($by == "LOT_ID")
                    $arSqlOrder[] = "C.LOT_ID " . ($order == "ASC" ? "ASC" : "DESC");
                elseif ($by == "NAME")
                    $arSqlOrder[] = "U.NAME " . ($order == "ASC" ? "ASC" : "DESC");
                elseif ($by == "LAST_NAME")
                    $arSqlOrder[] = "U.LAST_NAME " . ($order == "ASC" ? "ASC" : "DESC");
                else {
                    $by = "USER_ID";
                    $arSqlOrder[] = "C.USER_ID " . ($order == "ASC" ? "ASC" : "DESC");
                }
            }
            $strSqlOrder = "ORDER BY " . implode(", ", $arSqlOrder);
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        $strSql = "SELECT C.*, U.NAME
                   FROM b_tx_lot_win C
                   LEFT JOIN b_user U ON (C.USER_ID = U.ID)
                   WHERE " . $strSqlSearch . " " . $strSqlOrder;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetCountProposalWin($userId) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetCountProposalWin<br>Line: ";
        global $DB;

        $strSql = "SELECT count(*) CNT
                   FROM b_tx_lot_win
                   WHERE USER_ID = " . $userId;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        $arRes = $res->Fetch();
        return $arRes["CNT"];
    }

    function AddUserPrivateLot($LOT_ID, $PRIVATE_LIST) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: AddUserPrivateLot<br>Line: ";
        global $DB;

        foreach ($PRIVATE_LIST as $USER_ID) {
            $arFieldsSupplier["LOT_ID"] = $LOT_ID;
            $arFieldsSupplier["USER_ID"] = $USER_ID;

            $arInsert = $DB->PrepareInsert("b_tx_lot_private", $arFieldsSupplier);
            $strSql = "INSERT INTO b_tx_lot_private(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
            $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        }
    }


    function DelUserPrivateLot($LOT_ID) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: DelUserPrivateLot<br>Line: ";
        global $DB;

        $DB->Query("DELETE FROM b_tx_lot_private WHERE LOT_ID = " . $LOT_ID, True);
    }

    function GetUserPrivateLot($LOT_ID) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetUserPrivateLot<br>Line: ";
        global $DB;
        $LOT_ID = intVal($LOT_ID);
        if ($LOT_ID <= 0)
            return False;

        $strSql = "SELECT USER_ID FROM b_tx_lot_private
                   WHERE LOT_ID = " . $LOT_ID;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        /* while ($arRes = $res->Fetch()) {
          $result[] = $arRes["USER_ID"];
          } */
        return $res;
    }

    function deleteFromLotAccess($LOT_ID) {
        global $DB;
        $strSql = "SELECT * FROM b_tx_supplier_lot_access WHERE LOTID = " . $LOT_ID;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        if($arRes = $res->Fetch()) {
            $strSql = "DELETE FROM b_tx_supplier_lot_access WHERE LOTID = " . $LOT_ID;
            $DB->Query($strSql, false, $err_mess . __LINE__);
        }
    }

}

class CTenderixLotSpec extends CAllTenderLotSpec {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixLotSpec<br>File: " . __FILE__;
    }

    function GetListProp($ID) {
        $err_mess = (CTenderixLotSpec::err_mess()) . "<br>Function: GetListProp<br>Line: ";
        global $DB;

        $strSql = "SELECT C.*, 
                          CC.*, 
                          CC.ID PROP_ID,
                          CCC.TITLE UNIT_NAME
                   FROM b_tx_spec_buyer C, 
                        b_tx_spec_property_b CC
                   LEFT JOIN b_tx_spr_details CCC ON (CC.UNIT_ID=CCC.ID) 
                   WHERE C.LOT_ID =" . $ID . " AND C.ID = CC.SPEC_ID ORDER BY CC.ID asc";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetListSpec(&$by, &$order, $arFilter = Array(), &$is_filtered) {
        $err_mess = (CTenderixLotSpec::err_mess()) . "<br>Function: GetList<br>Line: ";
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

        $strSql = "SELECT C.*  FROM b_tx_spec_buyer C 
                   WHERE " . $strSqlSearch . " ORDER BY ID";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetByLotId($LOT_ID) {
        $err_mess = (CTenderixLotSpec::err_mess()) . "<br>Function: GetByLotId<br>Line: ";
        global $DB;

        $strSql = "SELECT C.* FROM b_tx_spec_buyer C
                   WHERE LOT_ID=" . $LOT_ID;

        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        $arSpec = $res->Fetch();

        return $arSpec;
    }

    function Add($arFields) {
        global $DB;

        if (!CTenderixLotSpec::CheckFields("ADD", $arFields))
            return false;

        $arInsert = $DB->PrepareInsert("b_tx_spec_buyer", $arFields);
        $strSql = "INSERT INTO b_tx_spec_buyer(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());
        return $ID;
    }

    function AddProp($arFields) {
        global $DB;

        $arInsert = $DB->PrepareInsert("b_tx_spec_property_b", $arFields);
        $strSql = "INSERT INTO b_tx_spec_property_b(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());
        return $ID;
    }

    function Update($LOT_ID, $arFields) {
        global $DB;
        $LOT_ID = intVal($LOT_ID);
        if ($LOT_ID <= 0)
            return False;

        if (!CTenderixLotSpec::CheckFields("UPDATE", $arFields, $ID))
            return false;

        $arSpecProp = CTenderixLotSpec::GetByLotId($LOT_ID);
        $ID = $arSpecProp["ID"];

        $strUpdate = $DB->PrepareUpdate("b_tx_spec_buyer", $arFields);
        $strSql = "UPDATE b_tx_spec_buyer SET " . $strUpdate . " WHERE ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return $ID;
    }

    function UpdateProp($ID, $arFields) {
        global $DB;
        $ID = intVal($ID);
        if ($ID <= 0)
            return False;

        if (!CTenderixLotSpec::CheckFields("UPDATE", $arFields, $ID))
            return false;

        $strUpdate = $DB->PrepareUpdate("b_tx_spec_property_b", $arFields);
        $strSql = "UPDATE b_tx_spec_property_b SET " . $strUpdate . " WHERE ID=" . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return $ID;
    }

    function Delete($ID) {
        global $DB;
        $ID = IntVal($ID);
        $DB->Query("DELETE FROM b_tx_spec_buyer WHERE ID = " . $ID, True);
        return true;
    }

    function DeleteProp($SPEC_ID) {
        global $DB;
        $SPEC_ID = IntVal($SPEC_ID);
        $DB->Query("DELETE FROM b_tx_spec_property_b WHERE SPEC_ID = " . $SPEC_ID, True);
        return true;
    }

    function DeletePropID($ID) {
        global $DB;
        $ID = IntVal($ID);
        $DB->Query("DELETE FROM b_tx_spec_property_b WHERE ID = " . $ID, True);
        return true;
    }

}

class CTenderixLotProduct extends CAllTenderLotProduct {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixLotProduct<br>File: " . __FILE__;
    }

    function GetList(&$by, &$order, $arFilter = Array()) {
        $err_mess = (CTenderixLotProduct::err_mess()) . "<br>Function: GetList<br>Line: ";
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
                    case "PRODUCT_ID":
                        $arSqlSearch[] = GetFilterQuery("C.PRODUCT_ID", $val, "N");
                        break;
                    case "LOT_ID":
                        $arSqlSearch[] = GetFilterQuery("C.LOT_ID", $val, "N");
                        break;
                }
            }
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);


        $strSql = "SELECT 
                        C.*,
                        " . $DB->DateToCharFunction("C.DATE_START") . " DATE_START,
                        " . $DB->DateToCharFunction("C.DATE_END") . " DATE_END,
                        " . $DB->DateToCharFunction("C.TIMESTAMP_X") . " TIMESTAMP_X
                   FROM b_tx_prod_buyer C WHERE " . $strSqlSearch;

        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetByLotId($LOT_ID) {
        $err_mess = (CTenderixLotProduct::err_mess()) . "<br>Function: GetByLotId<br>Line: ";
        global $DB;

        $strSql = "SELECT C.* FROM b_tx_prod_buyer C
                   WHERE LOT_ID=" . $LOT_ID;

        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        $arProp = $res->Fetch();

        return $arProp;
    }

    function Add($arFields) {
        global $DB;

        if (!CTenderixLotProduct::CheckFields("ADD", $arFields))
            return false;

        $arInsert = $DB->PrepareInsert("b_tx_prod_buyer", $arFields);
        $strSql = "INSERT INTO b_tx_prod_buyer(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());

        return $ID;
    }

    function AddProp($arFields) {
        global $DB;

        if (!CTenderixLotProduct::CheckFields("ADD", $arFields))
            return false;

        $arInsert = $DB->PrepareInsert("b_tx_prod_property_b", $arFields);
        $strSql = "INSERT INTO b_tx_prod_property_b(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());

        return $ID;
    }

    function Update($LOT_ID, $arFields) {
        global $DB;
        $LOT_ID = intVal($LOT_ID);
        if ($LOT_ID <= 0)
            return False;

        if (!CTenderixLotProduct::CheckFields("UPDATE", $arFields, $LOT_ID))
            return false;

        $arProp = CTenderixLotProduct::GetByLotId($LOT_ID);
        $ID = $arProp["ID"];

        $strUpdate = $DB->PrepareUpdate("b_tx_prod_buyer", $arFields);
        $strSql = "UPDATE b_tx_prod_buyer SET " . $strUpdate . " WHERE ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return $ID;
    }

    function UpdateProp($ID, $arFields) {
        global $DB;
        $ID = intVal($ID);
        if ($ID <= 0)
            return False;

        $strUpdate = $DB->PrepareUpdate("b_tx_prod_property_b", $arFields);
        $strSql = "UPDATE b_tx_prod_property_b SET " . $strUpdate . " 
                   WHERE ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return $ID;
    }

    function Delete($LOT_ID) {
        global $DB;
        $LOT_ID = IntVal($LOT_ID);
        if (!$DB->Query("DELETE FROM b_tx_prod_buyer WHERE LOT_ID = " . $LOT_ID, True)) {
            return false;
        }
        return true;
    }

    function DeleteProp($ID) {
        global $DB;
        $ID = IntVal($ID);
        if (!$DB->Query("DELETE FROM b_tx_prod_property_b WHERE PRODUCTS_ID = " . $ID, True)) {
            return false;
        }
        return true;
    }

}

        ?>