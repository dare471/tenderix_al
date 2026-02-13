<?

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/classes/general/tenderix_users_supplier.php");

class CTenderixUserSupplier extends CAllTenderUserSupplier {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixUserSupplier<br>File: " . __FILE__;
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
                //__($key);
                switch ($key) {
					case "XML_ID":
                        $arSqlSearch[] = GetFilterQuery("C.XML_ID", $val, "N");
                        break;
                    case "ID":
                        $arSqlSearch[] = GetFilterQuery("CC.ID", $val, "N");
                        break;
                    case "USER_ID":
                        $arSqlSearch[] = GetFilterQuery("C.USER_ID", $val, "N");
                        break;
                    case "NAME":
                        $arSqlSearch[] = GetFilterQuery("CC.NAME, CC.LAST_NAME, CC.SECOND_NAME", $val, "Y");
                        break;
                    case "LOGIN":
                        $arSqlSearch[] = GetFilterQuery("CC.LOGIN", $val, "Y");
                        break;
                    case "NAME_COMPANY":
                        $arSqlSearch[] = GetFilterQuery("C.NAME_COMPANY", $val, "Y");
                        break;
                    case "STATUS":
                        $arSqlSearch[] = GetFilterQuery("CCC.ID", $val, "N");
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
            $strSqlOrder = "ORDER BY C.NAME_COMPANY ";
        elseif ($by == "s_datereg")
            $strSqlOrder = "ORDER BY CC.DATE_REGISTER ";
        elseif ($by == "s_status")
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
        //__($arFilter);

        $strSql = "SELECT 
                        C.*, 
                        CC.*,
                        CCC.TITLE STATUS_NAME,
                        CCC.ID STATUS_ID,
                        CCC.ID STATUS_ID,
                        CCC.LOGO_BIG LOGO_BIG,
                        CCC.LOGO_SMALL LOGO_SMALL,
                        CCC.COLOR COLOR,
                        CCC.AUTH AUTH,
                        CCC.PART PART, 
                        CONCAT(CC.LAST_NAME,' ',CC.NAME,' ',CC.SECOND_NAME) FIO,
                        " . $DB->DateToCharFunction("CC.DATE_REGISTER") . " DATE_REGISTER
                   FROM b_tx_supplier C 
                   LEFT JOIN b_user CC ON (C.USER_ID = CC.ID)
                   LEFT JOIN b_tx_supplier_status CCC ON (C.STATUS = CCC.ID)
                   WHERE " . $strSqlSearch . " " . $strSqlOrder;

        //__($strSql);
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetListUser($arOrder = array(), $arFilter = array()) {
        $err_mess = (CTenderixUserSupplier::err_mess()) . "<br>Function: GetListUser<br>Line: ";
        global $DB;
        $arSqlSearch = Array();
        $strSqlSearch = "";

//        __($arFilter);

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
                    case "USER_ID":
                        $arSqlSearch[] = GetFilterQuery("C.USER_ID", $val, "N");
                        break;
                    case "NAME_COMPANY":
                        $arSqlSearch[] = GetFilterQuery("C.NAME_COMPANY", $val);
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
                elseif ($by == "NAME_COMPANY")
                    $arSqlOrder[] = "C.NAME_COMPANY " . ($order == "ASC" ? "ASC" : "DESC");
                else {
                    $by = "USER_ID";
                    $arSqlOrder[] = "C.USER_ID " . ($order == "ASC" ? "ASC" : "DESC");
                }
            }
            $strSqlOrder = "ORDER BY " . implode(", ", $arSqlOrder);
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);


        $strSql = "SELECT C.*
                   FROM b_tx_supplier C 
                   WHERE " . $strSqlSearch . " " . $strSqlOrder;

        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }
	
	// Добавление поставщика в систему
	function Add($arFields, $ID = "", $arRequired = array()) {
        global $DB, $USER;
		
		//Выполняются обработчики события "Добавление поставщика" один за другим по порядку
		//Массив $arFields передается по ссылке и может быть изменен в одном из обработчиков
        $events = GetModuleEvents("pweb.tenderix", "OnBeforeTenderixSupplierAdd");
        while ($arEvent = $events->Fetch())
            ExecuteModuleEventEx($arEvent, array(&$arFields));
		
		//Полный массив
        $arFieldsAll = $arFields;
		
		//Собираем массив $arFieldsSupplier
        $arFieldsSupplier["NAME_COMPANY"] = $arFields["NAME_COMPANY"];
        unset($arFields["NAME_COMPANY"]);
        $arFieldsSupplier["NAME_DIRECTOR"] = $arFields["NAME_DIRECTOR"];
        unset($arFields["NAME_DIRECTOR"]);
        $arFieldsSupplier["NAME_ACCOUNTANT"] = $arFields["NAME_ACCOUNTANT"];
        unset($arFields["NAME_ACCOUNTANT"]);
        $arFieldsSupplier["CODE_INN"] = $arFields["CODE_INN"];
        unset($arFields["CODE_INN"]);
        $arFieldsSupplier["CODE_KPP"] = $arFields["CODE_KPP"];
        unset($arFields["CODE_KPP"]);
        $arFieldsSupplier["CODE_OKVED"] = $arFields["CODE_OKVED"];
        unset($arFields["CODE_OKVED"]);
        $arFieldsSupplier["CODE_OKPO"] = $arFields["CODE_OKPO"];
        unset($arFields["CODE_OKPO"]);
        $arFieldsSupplier["LEGALADDRESS_REGION"] = $arFields["LEGALADDRESS_REGION"];
        unset($arFields["LEGALADDRESS_REGION"]);
        $arFieldsSupplier["LEGALADDRESS_CITY"] = $arFields["LEGALADDRESS_CITY"];
        unset($arFields["LEGALADDRESS_CITY"]);
        $arFieldsSupplier["LEGALADDRESS_INDEX"] = $arFields["LEGALADDRESS_INDEX"];
        unset($arFields["LEGALADDRESS_INDEX"]);
        $arFieldsSupplier["LEGALADDRESS_STREET"] = $arFields["LEGALADDRESS_STREET"];
        unset($arFields["LEGALADDRESS_STREET"]);
        $arFieldsSupplier["LEGALADDRESS_POST"] = $arFields["LEGALADDRESS_POST"];
        unset($arFields["LEGALADDRESS_POST"]);
        $arFieldsSupplier["POSTALADDRESS_REGION"] = $arFields["POSTALADDRESS_REGION"];
        unset($arFields["POSTALADDRESS_REGION"]);
        $arFieldsSupplier["POSTALADDRESS_CITY"] = $arFields["POSTALADDRESS_CITY"];
        unset($arFields["POSTALADDRESS_CITY"]);
        $arFieldsSupplier["POSTALADDRESS_INDEX"] = $arFields["POSTALADDRESS_INDEX"];
        unset($arFields["POSTALADDRESS_INDEX"]);
        $arFieldsSupplier["POSTALADDRESS_STREET"] = $arFields["POSTALADDRESS_STREET"];
        unset($arFields["POSTALADDRESS_STREET"]);
        $arFieldsSupplier["POSTALADDRESS_POST"] = $arFields["POSTALADDRESS_POST"];
        unset($arFields["POSTALADDRESS_POST"]);
        $arFieldsSupplier["PHONE"] = $arFields["PHONE"];
        unset($arFields["PHONE"]);
        $arFieldsSupplier["FAX"] = $arFields["FAX"];
        unset($arFields["FAX"]);
        $arFieldsSupplier["STATEREG_PLACE"] = $arFields["STATEREG_PLACE"];
        unset($arFields["STATEREG_PLACE"]);
        $arFieldsSupplier["STATEREG_DATE"] = $arFields["STATEREG_DATE"];
        unset($arFields["STATEREG_DATE"]);
		$arFieldsSupplier["STATEREG_NDS"] = $arFields["STATEREG_NDS"];
        unset($arFields["STATEREG_NDS"]);
        $arFieldsSupplier["STATEREG_OGRN"] = $arFields["STATEREG_OGRN"];
        unset($arFields["STATEREG_OGRN"]);
        $arFieldsSupplier["BANKING_NAME"] = $arFields["BANKING_NAME"];
        unset($arFields["BANKING_NAME"]);
        $arFieldsSupplier["BANKING_ACCOUNT"] = $arFields["BANKING_ACCOUNT"];
        unset($arFields["BANKING_ACCOUNT"]);
        $arFieldsSupplier["BANKING_ACCOUNTCORR"] = $arFields["BANKING_ACCOUNTCORR"];
        unset($arFields["BANKING_ACCOUNTCORR"]);
        $arFieldsSupplier["BANKING_BIK"] = $arFields["BANKING_BIK"];
        unset($arFields["BANKING_BIK"]);
        $arFieldsSupplier["STATUS"] = $arFields["STATUS"];
        unset($arFields["STATUS"]);
        $arProp = $arFields["PROPERTY"];
        unset($arFields["PROPERTY"]);
		
		//Список свойств
        $rsPropS = CTenderixUserSupplierProperty::GetList($by = "", $order = "", $arFilter=Array());
        while ($arPropS = $rsPropS->Fetch()) {
            $arrPropS[$arPropS["ID"]] = $arPropS;
        }
        $arProp["PROPERTY_S"] = $arrPropS;
        $arFields["PROPERTY"] = $arProp;

        $arFieldsSupplier["AGREE"] = $arFields["AGREE"];
        unset($arFields["AGREE"]);

        $user = new CUser;
		
		//Проверка значений для CUser
        if (!$ID) {
            $user->CheckFields($arFields);
        } else {
            $user->CheckFields($arFields, $ID);
        }
        $aMsg = $user->LAST_ERROR;
		//echo $aMsg;
		
		//Проверка значений для CTenderixUserSupplier
        if (!empty($aMsg)) {
            //print_r($aMsg);
            if (!CTenderixUserSupplier::CheckFields("ADD", $arFields, $arFieldsSupplier, $aMsg, $arRequired))
                return false;
        } else {
            if (!CTenderixUserSupplier::CheckFields("ADD", $arFields, $arFieldsSupplier, $aMsg, $arRequired))
                return false;
            if (!$ID) {
                $ID = $user->Add($arFields);
            } else {
                $user->Update($ID, $arFields);
            }
        }
		
		//INSERT b_tx_supplier
        $arFieldsSupplier["USER_ID"] = $ID;

        $arInsert = $DB->PrepareInsert("b_tx_supplier", $arFieldsSupplier);
        $strSql = "INSERT INTO b_tx_supplier(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        if ($DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__)) {
            $arSUPPLIER_GROUPS[] = COption::GetOptionString("pweb.tenderix", "PW_TD_SUPPLIER_GROUPS_DEFAULT");
            if ($ID > 0) {
                $arGroups = $USER->GetUserGroup($ID);
            } else {
                $arGroups = array();
            }
            $arSupplier = array_merge($arGroups, $arSUPPLIER_GROUPS);
            $user->Update($ID, array("GROUP_ID" => $arSupplier));
            CTenderixUserSupplier::SetProperty($ID, $arProp);
            CTenderixLog::Log("SUPPLIER_ADD", array("ID" => $ID, "FIELDS" => $arFieldsAll));
            $arEventFields = array(
                "ID" => $ID,
                "LAST_NAME" => $arFieldsAll["LAST_NAME"],
                "NAME" => $arFieldsAll["NAME"],
                "SECOND_NAME" => $arFieldsAll["SECOND_NAME"],
                "LOGIN" => $arFieldsAll["LOGIN"],
                "EMAIL" => $arFieldsAll["EMAIL"],
                "NAME_COMPANY" => $arFieldsAll["NAME_COMPANY"],
                "NAME_DIRECTOR" => $arFieldsAll["NAME_DIRECTOR"],
                "NAME_ACCOUNTANT" => $arFieldsAll["NAME_ACCOUNTANT"],
                "CODE_INN" => $arFieldsAll["CODE_INN"],
                "CODE_KPP" => $arFieldsAll["CODE_KPP"],
                "CODE_OKVED" => $arFieldsAll["CODE_OKVED"],
                "CODE_OKPO" => $arFieldsAll["CODE_OKPO"],
                "LEGALADDRESS_REGION" => $arFieldsAll["LEGALADDRESS_REGION"],
                "LEGALADDRESS_CITY" => $arFieldsAll["LEGALADDRESS_CITY"],
                "LEGALADDRESS_INDEX" => $arFieldsAll["LEGALADDRESS_INDEX"],
                "LEGALADDRESS_STREET" => $arFieldsAll["LEGALADDRESS_STREET"],
                "LEGALADDRESS_POST" => $arFieldsAll["LEGALADDRESS_POST"],
                "POSTALADDRESS_REGION" => $arFieldsAll["POSTALADDRESS_REGION"],
                "POSTALADDRESS_CITY" => $arFieldsAll["POSTALADDRESS_CITY"],
                "POSTALADDRESS_INDEX" => $arFieldsAll["POSTALADDRESS_INDEX"],
                "POSTALADDRESS_STREET" => $arFieldsAll["POSTALADDRESS_STREET"],
                "POSTALADDRESS_POST" => $arFieldsAll["POSTALADDRESS_POST"],
                "PHONE" => $arFieldsAll["PHONE"],
                "FAX" => $arFieldsAll["FAX"],
                "STATEREG_PLACE" => $arFieldsAll["STATEREG_PLACE"],
                "STATEREG_DATE" => $arFieldsAll["STATEREG_DATE"],
				"STATEREG_NDS" => $arFieldsAll["STATEREG_NDS"],
                "STATEREG_OGRN" => $arFieldsAll["STATEREG_OGRN"],
                "BANKING_NAME" => $arFieldsAll["BANKING_NAME"],
                "BANKING_ACCOUNT" => $arFieldsAll["BANKING_ACCOUNT"],
                "BANKING_ACCOUNTCORR" => $arFieldsAll["BANKING_ACCOUNTCORR"],
                "BANKING_BIK" => $arFieldsAll["BANKING_BIK"],
            );
            $arrSITE = CTenderixLot::GetSite();
			//Send e-mail
            CEvent::Send("TENDERIX_NEW_PARTY", $arrSITE, $arEventFields, "N");
        }
		
        $arFieldsAll["ID"] = $ID;
		
		//Выполняются обработчики события "Добавление поставщика" один за другим по порядку
		//Массив $arFields передается по ссылке и может быть изменен в одном из обработчиков
        $events = GetModuleEvents("pweb.tenderix", "OnAfterTenderixSupplierAdd");
        while ($arEvent = $events->Fetch())
            ExecuteModuleEventEx($arEvent, array(&$arFieldsAll));

        return $ID;
    }

    function Update($ID, $arFields, $arRequired = array()) {
        global $DB, $CACHE_MANAGER;

        if ($ID <= 0)
            return False;

        $ID = intVal($ID);
        $arFields["ID"] = $ID;
        $events = GetModuleEvents("pweb.tenderix", "OnBeforeTenderixSupplierUpdate");
        while ($arEvent = $events->Fetch())
            ExecuteModuleEventEx($arEvent, array(&$arFields));

        $arFieldsAll = $arFields;
        $arFieldsSupplier["NAME_COMPANY"] = $arFields["NAME_COMPANY"];
        unset($arFields["NAME_COMPANY"]);
        $arFieldsSupplier["NAME_DIRECTOR"] = $arFields["NAME_DIRECTOR"];
        unset($arFields["NAME_DIRECTOR"]);
        $arFieldsSupplier["NAME_ACCOUNTANT"] = $arFields["NAME_ACCOUNTANT"];
        unset($arFields["NAME_ACCOUNTANT"]);
        $arFieldsSupplier["CODE_INN"] = $arFields["CODE_INN"];
        unset($arFields["CODE_INN"]);
        $arFieldsSupplier["CODE_KPP"] = $arFields["CODE_KPP"];
        unset($arFields["CODE_KPP"]);
        $arFieldsSupplier["CODE_OKVED"] = $arFields["CODE_OKVED"];
        unset($arFields["CODE_OKVED"]);
        $arFieldsSupplier["CODE_OKPO"] = $arFields["CODE_OKPO"];
        unset($arFields["CODE_OKPO"]);
        $arFieldsSupplier["LEGALADDRESS_REGION"] = $arFields["LEGALADDRESS_REGION"];
        unset($arFields["LEGALADDRESS_REGION"]);
        $arFieldsSupplier["LEGALADDRESS_CITY"] = $arFields["LEGALADDRESS_CITY"];
        unset($arFields["LEGALADDRESS_CITY"]);
        $arFieldsSupplier["LEGALADDRESS_INDEX"] = $arFields["LEGALADDRESS_INDEX"];
        unset($arFields["LEGALADDRESS_INDEX"]);
        $arFieldsSupplier["LEGALADDRESS_STREET"] = $arFields["LEGALADDRESS_STREET"];
        unset($arFields["LEGALADDRESS_STREET"]);
        $arFieldsSupplier["LEGALADDRESS_POST"] = $arFields["LEGALADDRESS_POST"];
        unset($arFields["LEGALADDRESS_POST"]);
        $arFieldsSupplier["POSTALADDRESS_REGION"] = $arFields["POSTALADDRESS_REGION"];
        unset($arFields["POSTALADDRESS_REGION"]);
        $arFieldsSupplier["POSTALADDRESS_CITY"] = $arFields["POSTALADDRESS_CITY"];
        unset($arFields["POSTALADDRESS_CITY"]);
        $arFieldsSupplier["POSTALADDRESS_INDEX"] = $arFields["POSTALADDRESS_INDEX"];
        unset($arFields["POSTALADDRESS_INDEX"]);
        $arFieldsSupplier["POSTALADDRESS_STREET"] = $arFields["POSTALADDRESS_STREET"];
        unset($arFields["POSTALADDRESS_STREET"]);
        $arFieldsSupplier["POSTALADDRESS_POST"] = $arFields["POSTALADDRESS_POST"];
        unset($arFields["POSTALADDRESS_POST"]);
        $arFieldsSupplier["PHONE"] = $arFields["PHONE"];
        unset($arFields["PHONE"]);
        $arFieldsSupplier["FAX"] = $arFields["FAX"];
        unset($arFields["FAX"]);
        $arFieldsSupplier["STATEREG_PLACE"] = $arFields["STATEREG_PLACE"];
        unset($arFields["STATEREG_PLACE"]);
        $arFieldsSupplier["STATEREG_DATE"] = $arFields["STATEREG_DATE"];
        unset($arFields["STATEREG_DATE"]);
	    $arFieldsSupplier["STATEREG_NDS"] = $arFields["STATEREG_NDS"];
        unset($arFields["STATEREG_NDS"]);
        $arFieldsSupplier["STATEREG_OGRN"] = $arFields["STATEREG_OGRN"];
        unset($arFields["STATEREG_OGRN"]);
        $arFieldsSupplier["BANKING_NAME"] = $arFields["BANKING_NAME"];
        unset($arFields["BANKING_NAME"]);
        $arFieldsSupplier["BANKING_ACCOUNT"] = $arFields["BANKING_ACCOUNT"];
        unset($arFields["BANKING_ACCOUNT"]);
        $arFieldsSupplier["BANKING_ACCOUNTCORR"] = $arFields["BANKING_ACCOUNTCORR"];
        unset($arFields["BANKING_ACCOUNTCORR"]);
        $arFieldsSupplier["BANKING_BIK"] = $arFields["BANKING_BIK"];
        unset($arFields["BANKING_BIK"]);
        $arFieldsSupplier["STATUS"] = $arFields["STATUS"];
        unset($arFields["STATUS"]);
        $arProp = $arFields["PROPERTY"];
        unset($arFields["PROPERTY"]);
        $rsPropS = CTenderixUserSupplierProperty::GetList($by ="", $order ="", $arFilter=Array());
        while ($arPropS = $rsPropS->Fetch()) {
            $arrPropS[$arPropS["ID"]] = $arPropS;
        }
        $arProp["PROPERTY_S"] = $arrPropS;
        $arFields["PROPERTY"] = $arProp;

        $arFieldsSupplier["AGREE"] = $arFields["AGREE"];
        unset($arFields["AGREE"]);

        $rsUser = CTenderixUserSupplier::GetList($by, $order, array("ID" => $ID), $is_filtered = false);
        $arUser = $rsUser->Fetch();
        if (isset($arFieldsSupplier["STATUS"]) && $arFieldsSupplier["STATUS"] != $arUser["STATUS"]) {
            $rsStatus = CTenderixUserSupplierStatus::GetList($by = "", $order = "", array("ID" => $arFieldsSupplier["STATUS"]));
            $arStatus = $rsStatus->Fetch();
            $arEventFields = array(
                "SUPPLIER" => $arUser["LAST_NAME"] . " " . $arUser["NAME"] . " " . $arUser["SECOND_NAME"],
                "EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
                "EMAIL_TO" => $arUser["EMAIL"],
                "STATUS" => $arStatus["TITLE"]
            );

            //__($arEventFields);

            $arrSITE = CTenderixLot::GetSite();
            CEvent::Send("TENDERIX_STATUS_UPDATE", $arrSITE, $arEventFields, "N");
        }

        $user = new CUser;
        $user->CheckFields($arFields, $ID);
        $aMsg = $user->LAST_ERROR;
        if (!empty($aMsg)) {
            if (!CTenderixUserSupplier::CheckFields("UPDATE", $arFields, $arFieldsSupplier, $aMsg, $arRequired))
                return false;
        } else {
            if (!CTenderixUserSupplier::CheckFields("UPDATE", $arFields, $arFieldsSupplier, $aMsg, $arRequired))
                return false;
            $user->Update($ID, $arFields);
        }

        $strUpdate = $DB->PrepareUpdate("b_tx_supplier", $arFieldsSupplier);
        $strSql = "UPDATE b_tx_supplier SET " . $strUpdate . " WHERE USER_ID = " . $ID;
        if ($DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__)) {
            CTenderixUserSupplier::SetProperty($ID, $arProp);
            $CACHE_MANAGER->ClearByTag('pweb.tenderix_user.info_' . $ID);
            CTenderixLog::Log("SUPPLIER_UPDATE", array("ID" => $ID, "FIELDS" => $arFieldsAll));

            /**
             * Добавлено для статистики. В. Филиппов
             */
            $userid = CTenderixStatistic::SelectUserFromStatistic($ID);
            if ($arFieldsSupplier["STATUS"] == 3) {
                if($userid == 0) {
                    CTenderixStatistic::AddUserToStatistic($ID);
                }
            }
        }

        $events = GetModuleEvents("pweb.tenderix", "OnAfterTenderixSupplierUpdate");
        while ($arEvent = $events->Fetch())
            ExecuteModuleEventEx($arEvent, array(&$arFieldsAll));

        return $ID;
    }
	
	 function Update2($ID, $arFields, $arRequired = array(), $arExcept = array()) {
        global $DB, $CACHE_MANAGER;

        if ($ID <= 0)
            return False;

        $ID = intVal($ID);
        $arFields["ID"] = $ID;
        $events = GetModuleEvents("pweb.tenderix", "OnBeforeTenderixSupplierUpdate");
        while ($arEvent = $events->Fetch())
            ExecuteModuleEventEx($arEvent, array(&$arFields));

        $arFieldsAll = $arFields;
        $arFieldsSupplier["NAME_COMPANY"] = $arFields["NAME_COMPANY"];
        unset($arFields["NAME_COMPANY"]);
        $arFieldsSupplier["NAME_DIRECTOR"] = $arFields["NAME_DIRECTOR"];
        unset($arFields["NAME_DIRECTOR"]);
        $arFieldsSupplier["NAME_ACCOUNTANT"] = $arFields["NAME_ACCOUNTANT"];
        unset($arFields["NAME_ACCOUNTANT"]);
        $arFieldsSupplier["CODE_INN"] = $arFields["CODE_INN"];
        unset($arFields["CODE_INN"]);
        $arFieldsSupplier["CODE_KPP"] = $arFields["CODE_KPP"];
        unset($arFields["CODE_KPP"]);
        $arFieldsSupplier["CODE_OKVED"] = $arFields["CODE_OKVED"];
        unset($arFields["CODE_OKVED"]);
        $arFieldsSupplier["CODE_OKPO"] = $arFields["CODE_OKPO"];
        unset($arFields["CODE_OKPO"]);
        $arFieldsSupplier["LEGALADDRESS_REGION"] = $arFields["LEGALADDRESS_REGION"];
        unset($arFields["LEGALADDRESS_REGION"]);
        $arFieldsSupplier["LEGALADDRESS_CITY"] = $arFields["LEGALADDRESS_CITY"];
        unset($arFields["LEGALADDRESS_CITY"]);
        $arFieldsSupplier["LEGALADDRESS_INDEX"] = $arFields["LEGALADDRESS_INDEX"];
        unset($arFields["LEGALADDRESS_INDEX"]);
        $arFieldsSupplier["LEGALADDRESS_STREET"] = $arFields["LEGALADDRESS_STREET"];
        unset($arFields["LEGALADDRESS_STREET"]);
        $arFieldsSupplier["LEGALADDRESS_POST"] = $arFields["LEGALADDRESS_POST"];
        unset($arFields["LEGALADDRESS_POST"]);
        $arFieldsSupplier["POSTALADDRESS_REGION"] = $arFields["POSTALADDRESS_REGION"];
        unset($arFields["POSTALADDRESS_REGION"]);
        $arFieldsSupplier["POSTALADDRESS_CITY"] = $arFields["POSTALADDRESS_CITY"];
        unset($arFields["POSTALADDRESS_CITY"]);
        $arFieldsSupplier["POSTALADDRESS_INDEX"] = $arFields["POSTALADDRESS_INDEX"];
        unset($arFields["POSTALADDRESS_INDEX"]);
        $arFieldsSupplier["POSTALADDRESS_STREET"] = $arFields["POSTALADDRESS_STREET"];
        unset($arFields["POSTALADDRESS_STREET"]);
        $arFieldsSupplier["POSTALADDRESS_POST"] = $arFields["POSTALADDRESS_POST"];
        unset($arFields["POSTALADDRESS_POST"]);
        $arFieldsSupplier["PHONE"] = $arFields["PHONE"];
        unset($arFields["PHONE"]);
        $arFieldsSupplier["FAX"] = $arFields["FAX"];
        unset($arFields["FAX"]);
        $arFieldsSupplier["STATEREG_PLACE"] = $arFields["STATEREG_PLACE"];
        unset($arFields["STATEREG_PLACE"]);
        $arFieldsSupplier["STATEREG_DATE"] = $arFields["STATEREG_DATE"];
        unset($arFields["STATEREG_DATE"]);
	    $arFieldsSupplier["STATEREG_NDS"] = $arFields["STATEREG_NDS"];
        unset($arFields["STATEREG_NDS"]);
        $arFieldsSupplier["STATEREG_OGRN"] = $arFields["STATEREG_OGRN"];
        unset($arFields["STATEREG_OGRN"]);
        $arFieldsSupplier["BANKING_NAME"] = $arFields["BANKING_NAME"];
        unset($arFields["BANKING_NAME"]);
        $arFieldsSupplier["BANKING_ACCOUNT"] = $arFields["BANKING_ACCOUNT"];
        unset($arFields["BANKING_ACCOUNT"]);
        $arFieldsSupplier["BANKING_ACCOUNTCORR"] = $arFields["BANKING_ACCOUNTCORR"];
        unset($arFields["BANKING_ACCOUNTCORR"]);
        $arFieldsSupplier["BANKING_BIK"] = $arFields["BANKING_BIK"];
        unset($arFields["BANKING_BIK"]);
        $arFieldsSupplier["STATUS"] = $arFields["STATUS"];
        unset($arFields["STATUS"]);
        $arProp = $arFields["PROPERTY"];
        unset($arFields["PROPERTY"]);
        $rsPropS = CTenderixUserSupplierProperty::GetList($by = "", $order = "", $arFilter=Array());
        while ($arPropS = $rsPropS->Fetch()) {
            $arrPropS[$arPropS["ID"]] = $arPropS;
        }
        $arProp["PROPERTY_S"] = $arrPropS;
        $arFields["PROPERTY"] = $arProp;

        $arFieldsSupplier["AGREE"] = $arFields["AGREE"];
        unset($arFields["AGREE"]);
		//!!!
		

		$arFieldsSupplier = array_diff_key($arFieldsSupplier, $arExcept);
		
		

        $rsUser = CTenderixUserSupplier::GetList($by, $order, array("ID" => $ID), $is_filtered = false);
        $arUser = $rsUser->Fetch();
        if (isset($arFieldsSupplier["STATUS"]) && $arFieldsSupplier["STATUS"] != $arUser["STATUS"]) {
            $rsStatus = CTenderixUserSupplierStatus::GetList($by = "", $order = "", array("ID" => $arFieldsSupplier["STATUS"]));
            $arStatus = $rsStatus->Fetch();
            $arEventFields = array(
                "SUPPLIER" => $arUser["LAST_NAME"] . " " . $arUser["NAME"] . " " . $arUser["SECOND_NAME"],
                "EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
                "EMAIL_TO" => $arUser["EMAIL"],
                "STATUS" => $arStatus["TITLE"]
            );

            //__($arEventFields);

            $arrSITE = CTenderixLot::GetSite();
            CEvent::Send("TENDERIX_STATUS_UPDATE", $arrSITE, $arEventFields, "N");
        }

        $user = new CUser;
        $user->CheckFields($arFields, $ID);
        $aMsg = $user->LAST_ERROR;
        if (!empty($aMsg)) {
            if (!CTenderixUserSupplier::CheckFields("UPDATE", $arFields, $arFieldsSupplier, $aMsg, $arRequired))
                return false;
        } else {
            if (!CTenderixUserSupplier::CheckFields("UPDATE", $arFields, $arFieldsSupplier, $aMsg, $arRequired))
                return false;
            $user->Update($ID, $arFields);
        }

        $strUpdate = $DB->PrepareUpdate("b_tx_supplier", $arFieldsSupplier);
        $strSql = "UPDATE b_tx_supplier SET " . $strUpdate . " WHERE USER_ID = " . $ID;
        if ($DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__)) {
            CTenderixUserSupplier::SetProperty($ID, $arProp);
            $CACHE_MANAGER->ClearByTag('pweb.tenderix_user.info_' . $ID);
            CTenderixLog::Log("SUPPLIER_UPDATE", array("ID" => $ID, "FIELDS" => $arFieldsAll));

            /**
             * Добавлено для статистики. В. Филиппов
             */
            $userid = CTenderixStatistic::SelectUserFromStatistic($ID);
            if ($arFieldsSupplier["STATUS"] == 3) {
                if($userid == 0) {
                    CTenderixStatistic::AddUserToStatistic($ID);
                }
            }
        }

        $events = GetModuleEvents("pweb.tenderix", "OnAfterTenderixSupplierUpdate");
        while ($arEvent = $events->Fetch())
            ExecuteModuleEventEx($arEvent, array(&$arFieldsAll));

        return $ID;
    }

    function Delete($ID) {
        global $DB;
        $ID = IntVal($ID);
        $results = $DB->Query("SELECT 'x' FROM b_tx_supplier WHERE USER_ID = " . $ID, true);
        if (!$row = $results->Fetch())
            return false;


        if ($DB->Query("DELETE FROM b_tx_supplier WHERE USER_ID = " . $ID, True)) {
            CTenderixUserSupplier::SubscribeDelete($ID);
            CTenderixUserSupplier::DirectionDelete($ID);
            CTenderixUserSupplier::DeleteFile($ID);

            if ($ID != '1')
                $DB->Query("DELETE FROM b_user WHERE ID = " . $ID, True);

            CTenderixLog::Log("SUPPLIER_DEL", array("ID" => $ID));
        }
        return true;
    }

    function SubscribeList($USER_ID) {
        $err_mess = (CTenderixUserSupplier::err_mess()) . "<br>Function: SubscribeList<br>Line: ";
        global $DB;

        $strSql = "SELECT C.*
                   FROM b_tx_supplier_subscr C 
                   WHERE USER_ID =" . $USER_ID;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetEmailSubscribeListSection($SECTION_ID, $PRIVATE_LIST = array()) {
        $err_mess = (CTenderixUserSupplier::err_mess()) . "<br>Function: SubscribeListSection<br>Line: ";
        global $DB;

        // __($SECTION_ID);
        // __($PRIVATE_LIST);
        // die('hi');

        $strPrivateSql = "";
        if (count($PRIVATE_LIST) > 0) {
            //private lot
            $strPrivateSql = " AND C.USER_ID in (" . implode(",", $PRIVATE_LIST) . ")";
        }

        $strSql = "SELECT CC.*
                   FROM b_tx_supplier_subscr C
                   LEFT JOIN b_user CC ON (C.USER_ID = CC.ID)
                   WHERE CC.ACTIVE = 'Y' AND C.SECTION_ID = " . $SECTION_ID . $strPrivateSql;
				   
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        while ($arRes = $res->Fetch()) {
            $email[$arRes["ID"]] = array(
                "EMAIL" => $arRes["EMAIL"],
                "FIO" => $arRes["LAST_NAME"] . " " . $arRes["NAME"] . " " . $arRes["SECOND_NAME"],
            );
        }
        return $email;
    }
	
	// Доработанная функция
	function GetEmailSubscribeListSectionEx($SECTION_ID, $PRIVATE_LIST = array()) {
        $err_mess = (CTenderixUserSupplier::err_mess()) . "<br>Function: SubscribeListSection<br>Line: ";
        global $DB;

        // __($SECTION_ID);
        // __($PRIVATE_LIST);
        // die('hi');

        $strPrivateSql = "";
        if (count($PRIVATE_LIST) > 0) {
            //private lot
            $strPrivateSql = " AND C.USER_ID in (" . implode(",", $PRIVATE_LIST) . ")";
        }

        $strSql = "SELECT CC.*, SP.NAME_COMPANY 
                   FROM b_tx_supplier_subscr C
                   LEFT JOIN b_user CC ON (C.USER_ID = CC.ID)
				   LEFT JOIN b_tx_supplier SP ON (SP.USER_ID = CC.ID)
                   WHERE CC.ACTIVE = 'Y' AND C.SECTION_ID = " . $SECTION_ID . $strPrivateSql;

        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        while ($arRes = $res->Fetch()) {
            $email[$arRes["ID"]] = array(
                "EMAIL" => $arRes["EMAIL"],
                "FIO" => $arRes["LAST_NAME"] . " " . $arRes["NAME"] . " " . $arRes["SECOND_NAME"],
				"NAME_COMPANY" => $arRes["NAME_COMPANY"],
            );
        }
        return $email;
    }

    function GetEmailSubscribeListLot($PRIVATE_LIST = array()) {
        $err_mess = (CTenderixUserSupplier::err_mess()) . "<br>Function: SubscribeListSection<br>Line: ";
        global $DB;

        $strPrivateSql = "";
        if (count($PRIVATE_LIST) > 0) {
            //private lot
            $strPrivateSql = " AND C.ID in (" . implode(",", $PRIVATE_LIST) . ")";
        }

        $strSql = "SELECT C.*
                   FROM b_user C
                   WHERE C.ACTIVE = 'Y' " . $strPrivateSql;

        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        while ($arRes = $res->Fetch()) {
            $email[$arRes["ID"]] = array(
                "EMAIL" => $arRes["EMAIL"],
                "FIO" => $arRes["LAST_NAME"] . " " . $arRes["NAME"] . " " . $arRes["SECOND_NAME"],
            );
        }
        return $email;
    }

    function SubscribeAdd($USER_ID, $SUBSCRIBE) {
        global $DB;

        if ($USER_ID > 0) {
            CTenderixUserSupplier::SubscribeDelete($USER_ID);

            foreach ($SUBSCRIBE as $SECTION_ID) {
                $arFields["USER_ID"] = $USER_ID;
                $arFields["SECTION_ID"] = $SECTION_ID;
                $arInsert = $DB->PrepareInsert("b_tx_supplier_subscr", $arFields);
                $strSql = "INSERT INTO b_tx_supplier_subscr(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
                if (!$DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__))
                    return false;
            }
        }
        return true;
    }

    function SubscribeDelete($USER_ID) {
        global $DB;

        if ($USER_ID > 0 && $DB->Query("DELETE FROM b_tx_supplier_subscr WHERE USER_ID = " . $USER_ID, True))
            return true;

        return false;
    }

    function DirectionList($USER_ID) {
        $err_mess = (CTenderixUserBuyer::err_mess()) . "<br>Function: DirectionList<br>Line: ";
        global $DB;


        $strSql = "SELECT C.*
                   FROM b_tx_supplier_dir C 
                   WHERE USER_ID =" . $USER_ID;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function DirectionAdd($USER_ID, $SUBSCRIBE) {
        global $DB;

        if ($USER_ID > 0) {
            CTenderixUserSupplier::DirectionDelete($USER_ID);

            foreach ($SUBSCRIBE as $SECTION_ID) {
                $arFields["USER_ID"] = $USER_ID;
                $arFields["SECTION_ID"] = $SECTION_ID;
                $arInsert = $DB->PrepareInsert("b_tx_supplier_dir", $arFields);
                $strSql = "INSERT INTO b_tx_supplier_dir(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
                if (!$DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__))
                    return false;
            }
        }
        return true;
    }

    function DirectionDelete($USER_ID) {
        global $DB;

        if ($USER_ID > 0 && $DB->Query("DELETE FROM b_tx_supplier_dir WHERE USER_ID = " . $USER_ID, True))
            return true;

        return false;
    }

    function DeleteFile($ID, $file_id = false) {
        global $DB;
        $ID = intval($ID);
        $file_id = intval($file_id);

        $rsFile = CTenderixUserSupplier::GetFileList($ID, $file_id);
        while ($arFile = $rsFile->Fetch()) {
            $rs = $DB->Query("DELETE FROM b_tx_supplier_file where USER_ID=" . $ID . " AND FILE_ID=" . intval($arFile["ID"]), false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
            CFile::Delete(intval($arFile["ID"]));
        }
    }

    function DeleteFileProperty($ID, $file_id) {
        global $DB;
        $ID = intval($ID);
        $file_id = intval($file_id);

        $rs = $DB->Query("DELETE FROM b_tx_supplier_propval where USER_ID=" . $ID . " AND VALUE=" . intval($file_id), false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        CFile::Delete($file_id);
    }

    function SaveFile($ID, $file) {
        global $DB;
        $ID = intval($ID);
        $aMsg = array();

        $arFileName = CTenderixUserSupplier::FileReName($file["name"]);

        $arSameNames = array();
        $rsFile = CTenderixUserSupplier::GetFileList($ID);
        while ($arFile = $rsFile->Fetch()) {
            $arSavedName = CTenderixUserSupplier::FileReName($arFile["ORIGINAL_NAME"]);
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
        if (($fid > 0) && $DB->Query("INSERT INTO b_tx_supplier_file (USER_ID, FILE_ID) VALUES (" . $ID . " ," . $fid . ")", false, "File: " . __FILE__ . "<br>Line: " . __LINE__)) {
            return true;
        } else {
            $aMsg[] = array(
                "text" => GetMessage("PW_TD_ERROR_ATTACH"));
            $e = new CAdminException(array_reverse($aMsg));
            $GLOBALS["APPLICATION"]->ThrowException($e);
            return false;
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
				,b_tx_supplier_file SF
			WHERE
				F.ID=SF.FILE_ID
				AND SF.USER_ID=" . $ID . "
			" . ($file_id > 0 ? "AND SF.FILE_ID = " . $file_id : "") . "
			ORDER BY F.ID
		";

        return $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
    }

    function GetFileListProperty($USER_ID, $PROPERTY_ID, $file_id) {
        global $DB;
        $USER_ID = intval($USER_ID);
        $PROPERTY_ID = intval($PROPERTY_ID);
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
				,b_tx_supplier_propval SF
			WHERE
				F.ID=SF.VALUE
				AND SF.USER_ID=" . $USER_ID . "
				AND SF.PROPERTY_ID=" . $PROPERTY_ID . "
                                " . ($file_id > 0 ? "AND SF.VALUE = " . $file_id : "") . "
			ORDER BY F.ID
		";

        return $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
    }

    function GetProperty($USER_ID) {
        global $DB;
        $strSql = "SELECT C.* FROM b_tx_supplier_propval C WHERE USER_ID =" . $USER_ID . " ORDER BY ID asc";
        $rsRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        while ($arRes = $rsRes->Fetch()) {
            $res[$arRes["PROPERTY_ID"]][] = $arRes;
        }
        return $res;
    }

    function SetProperty($USER_ID, $PROPERTY = array()) {
        global $DB;
        $FILES = $PROPERTY["FILES"];
        $PROPERTY = $PROPERTY["PROPERTY"];

        foreach ($FILES["name"] as $idProp => $arrFile) {
            $file = array();
            $file["MODULE_ID"] = "pweb.tenderix";
            foreach ($arrFile as $k => $nameFile) {
                if (strlen($nameFile) > 0) {
                    $file["name"] = $nameFile;
                    $file["type"] = $FILES["type"][$idProp][$k];
                    $file["tmp_name"] = $FILES["tmp_name"][$idProp][$k];
                    $file["size"] = $FILES["size"][$idProp][$k];
                    $file["error"] = $FILES["error"][$idProp][$k];
                    $fid = intval(CFile::SaveFile($file, "pweb.tenderix", true, true));
                    if ($fid > 0) {
                        $arFields = array(
                            "USER_ID" => $USER_ID,
                            "PROPERTY_ID" => $idProp,
                            "VALUE" => $fid,
                        );
                        $arInsert = $DB->PrepareInsert("b_tx_supplier_propval", $arFields);
                        $strSql = "INSERT INTO b_tx_supplier_propval(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
                        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
                    }
                }
            }
        }

        foreach ($PROPERTY as $idProp => $arrProperty) {
            $rsProp = CTenderixUserSupplierProperty::GetList($by = "", $order = "", array("ID" => $idProp));
            $arProp = $rsProp->Fetch();

            switch ($arProp["PROPERTY_TYPE"]) {
                case "S":
                case "N":
                case "T":
                case "D":
                    foreach ($arrProperty as $k => $v) {
                        $arFields = array(
                            "USER_ID" => $USER_ID,
                            "PROPERTY_ID" => $idProp
                        );
                        if ($arProp["PROPERTY_TYPE"] == "D") {
                            $arFields["VALUE"] = ConvertDateTime($v, "YYYY-MM-DD HH:MI:SS");
                        } else {
                            $arFields["VALUE"] = $v;
                        }

                        if (strstr($k, "n")) {
                            if (strlen(trim($PROPERTY[$idProp][$k])) > 0) {
                                $arInsert = $DB->PrepareInsert("b_tx_supplier_propval", $arFields);
                                $strSql = "INSERT INTO b_tx_supplier_propval(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
                                $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
                            }
                        } else {
                            if (strlen(trim($PROPERTY[$idProp][$k])) > 0) {
                                $strUpdate = $DB->PrepareUpdate("b_tx_supplier_propval", $arFields);
                                $strSql = "UPDATE b_tx_supplier_propval SET " . $strUpdate . " WHERE ID = " . $k;
                                $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
                            } else {
                                $DB->Query("DELETE FROM b_tx_supplier_propval WHERE ID = " . $k, True);
                            }
                        }
                    }
                    break;
                case "L":
                    $DB->Query("DELETE FROM b_tx_supplier_propval WHERE USER_ID = " . $USER_ID . " AND PROPERTY_ID=" . $idProp, True);
                    foreach ($arrProperty as $k => $v) {
                        $arFields = array(
                            "USER_ID" => $USER_ID,
                            "PROPERTY_ID" => $idProp
                        );
                        $arFields["VALUE"] = $v;
                        if (strlen(trim($PROPERTY[$idProp][$k])) > 0) {
                            $arInsert = $DB->PrepareInsert("b_tx_supplier_propval", $arFields);
                            $strSql = "INSERT INTO b_tx_supplier_propval(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
                            $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
                        }
                    }
                    break;
            }
        }
    }

}

?>