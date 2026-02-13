<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/pweb.tenderix/classes/general/tenderix_proposal.php");

class CTenderixProposal extends CAllTenderProposal {

    function err_mess() {
        $module_id = "pweb.tenderix";
        return "<br>Module: " . $module_id . "<br>Class: CTenderixProposal<br>File: " . __FILE__;
    }

    function GetList($arFilter) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetList<br>Line: ";
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
                    case "USER_ID":
                        $arSqlSearch[] = GetFilterQuery("C.USER_ID", $val, "N");
                        break;
                }
            }
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        $strSql = "SELECT 
                        C.*,
                        " . $DB->DateToCharFunction("C.DATE_START") . " DATE_START,
                        U.NAME,
                        U.LAST_NAME,
                        U.SECOND_NAME,
                        U.EMAIL
                   FROM b_tx_proposal C
                   LEFT JOIN b_user U ON (C.USER_ID = U.ID)
                   WHERE " . $strSqlSearch;

        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }
	
	/* Минимальная  и стартовая цена по лоту */
	function GetPrice($arParams) {
		// Список предложений
		$rsProposal = self::GetList(array("LOT_ID" => $arParams["LOT_ID"]));
		// Временная сумма
		$arSum = array();	
		$arStartSum = array();
		$arProductSum = array();
		// echo '<pre>';
		while($arProposal = $rsProposal->Fetch()) {		
			$nTmpSum = 0;
			$nStartSum = 0;
			//print_r($arProposal);      
            if ($arLots["TYPE_ID"] == "S" || $arLots["TYPE_ID"] == "R") {
                $arFieldsPropertyProducts = array();
                $rsPropertyProducts = self::GetListPropertyProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
				//
                while ($arPropertyProducts = $rsPropertyProducts->Fetch()) {
                    $arFieldsPropertyProducts[$arPropertyProducts["PRODUCTS_PROPERTY_BUYER_ID"]] = $arPropertyProducts["VALUE"];					
                }
                //$arResult["PROPOSAL_PROPERTY_PRODUCTS"] = $arFieldsPropertyProducts;				
				//print_r($arFieldsPropertyProducts);				
                $arFieldsProducts = array();
                $rsProducts = self::GetListProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
                while($arProducts = $rsProducts->Fetch()) {
					//print_r($arProducts);
					//$arProductSum = 
                    $arProducts["PRICE_NDS"] = floatval($arProducts["PRICE_NDS"]) / (floatval($curr_list[$arProposal["CURRENCY"]]) <= 0 ? 1 : floatval($curr_list[$arProposal["CURRENCY"]]));
					// $arResult["PROPOSAL_PRODUCTS"][$arProducts["PROD_BUYER_ID"]] = $arProducts;
					$nTmpSum += $arProducts["PRICE_NDS"]*$arProducts['COUNT'];
					$nStartSum += $arProducts["START_PRICE"]*$arProducts['COUNT'];
					//echo "type S";
					if ( ! isset($arProductSum[$arProducts['PROPERTY_BUYER_ID']]) ) {
						$arProductSum[$arProducts['PROPERTY_BUYER_ID']]['PRICE_NDS'] = $arProducts["PRICE_NDS"]; //Цена НДС (минимальная);
						
					} else {
						if ($arProducts["PRICE_NDS"] < $arProductSum[$arProducts['PROPERTY_BUYER_ID']]['PRICE_NDS']) {
							$arProductSum[$arProducts['PROPERTY_BUYER_ID']]['PRICE_NDS'] = $arProducts["PRICE_NDS"];
						}
					}
                } 
				$arSum[] = $nTmpSum;
				if (empty($arStartSum)) {
					$arStartSum[] = $nStartSum;
				}
				//echo $arProposal["ID"];
				//print_r($arResult["PROPOSAL_PRODUCTS"]);
            }
            if ($arLots["TYPE_ID"] != "S" && $arLots["TYPE_ID"] != "R") {
                $arFieldsSpec = array();
                $rsProposalSpec = self::GetListSpec(array("PROPOSAL_ID" => $arProposal["ID"]));
				
                while ($arProposalSpec = $rsProposalSpec->Fetch()) {
					//print_r($arProposalSpec);
                    $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["PROPERTY_BUYER_ID"] = $arProposalSpec["PROPERTY_BUYER_ID"];
                    $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["NDS"] = $arProposalSpec["NDS"];
                    $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["PRICE_NDS"] = floatval($arProposalSpec["PRICE_NDS"]) / (floatval($curr_list[$arProposal["CURRENCY"]]) <= 0 ? 1 : floatval($curr_list[$arProposal["CURRENCY"]]));
                    $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["ANALOG"] = $arProposalSpec["ANALOG"];
					$nTmpSum += $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["PRICE_NDS"]*$arProposalSpec['COUNT'];
					$nStartSum += $arProposalSpec["START_PRICE"]*$arProposalSpec['COUNT'];
					
					if ( ! isset($arProductSum[$arProposalSpec['PROPERTY_BUYER_ID']]) ) {
						$arProductSum[$arProposalSpec['PROPERTY_BUYER_ID']]['PRICE_NDS'] = $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["PRICE_NDS"]; //Цена НДС (минимальная);
					} else {
						if ($arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["PRICE_NDS"] < $arProductSum[$arProposalSpec['PROPERTY_BUYER_ID']]['PRICE_NDS']) {
							$arProductSum[$arProposalSpec['PROPERTY_BUYER_ID']]['PRICE_NDS'] = $arFieldsSpec[$arProposalSpec["PROPERTY_BUYER_ID"]]["PRICE_NDS"];
						}
					}	
                }
				$arSum[] = $nTmpSum;
				if (empty($arStartSum)) {
					$arStartSum[] = $nStartSum;
				}		
				//print_r($arFieldsSpec);
                //$arResult["PROPOSAL_SPEC"] = $arFieldsSpec;
            } 
        }
		return array(
			'start' => empty($arStartSum) ? 0 : $arStartSum[0],
			'min' => min($arSum),
			'product' => $arProductSum,
		);
	}

    function GetListSpec2($arFilter) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetListSpec2<br>Line: ";
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
                    case "USER_ID":
                        $arSqlSearch[] = GetFilterQuery("C.USER_ID", $val, "N");
                        break;
                }
            }
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        $strSql = "SELECT C.*, CC.NDS, CC.PRICE_NDS, CC.PROPERTY_BUYER_ID, CC.DATE_START, CC.PROPOSAL_ID, U.NAME
                   FROM b_tx_proposal C
                   LEFT JOIN b_user U ON (C.USER_ID = U.ID)
                   LEFT JOIN b_tx_proposal_spec CC ON (C.ID = CC.PROPOSAL_ID)
                   WHERE " . $strSqlSearch;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function Add($arFields) {
        global $DB, $CACHE_MANAGER;

        $events = GetModuleEvents("pweb.tenderix", "OnBeforeTenderixProposalAdd");
        while ($arEvent = $events->Fetch())
            ExecuteModuleEventEx($arEvent, array(&$arFields));

        $arInsert = $DB->PrepareInsert("b_tx_proposal", $arFields);
        $strSql = "INSERT INTO b_tx_proposal(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());

        $CACHE_MANAGER->ClearByTag('pweb.tenderix_user.info_' . $arFields["USER_ID"]);
        $CACHE_MANAGER->ClearByTag('pweb.tenderix_list.lot');

        if ($ID > 0) {
            $arFields["ID"] = $ID;
            $events = GetModuleEvents("pweb.tenderix", "OnAfterTenderixProposalAdd");
            while ($arEvent = $events->Fetch())
                ExecuteModuleEventEx($arEvent, array(&$arFields));
        }
        return $ID;
    }

    function Update($ID, $arFields) {
        global $DB;

        $arFields["ID"] = $ID;
        $events = GetModuleEvents("pweb.tenderix", "OnBeforeTenderixProposalUpdate");
        while ($arEvent = $events->Fetch())
            ExecuteModuleEventEx($arEvent, array(&$arFields));

        $strUpdate = $DB->PrepareUpdate("b_tx_proposal", $arFields);
        $strSql = "UPDATE b_tx_proposal SET " . $strUpdate . " WHERE ID = " . $ID;
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        $events = GetModuleEvents("pweb.tenderix", "OnAfterTenderixProposalUpdate");
        while ($arEvent = $events->Fetch())
            ExecuteModuleEventEx($arEvent, array(&$arFields));

        return $ID;
    }

    function GetListSpec($arFilter) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetListSpec<br>Line: ";
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
                    case "PROPOSAL_ID":
                        $arSqlSearch[] = GetFilterQuery("C.PROPOSAL_ID", $val, "N");
                        break;
                    case "PROPERTY_BUYER_ID":
                        $arSqlSearch[] = GetFilterQuery("C.PROPERTY_BUYER_ID", $val, "N");
                        break;
                }
            }
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        $strSql = "SELECT 
                        C.*, 
                        " . $DB->DateToCharFunction("C.DATE_START") . " DATE_START,
                        CC.TITLE TITLE,
                        CC.ADD_INFO ADD_INFO,
                        CC.COUNT COUNT,
                        CC.UNIT_ID UNIT_ID,
                        CC.START_PRICE START_PRICE,
                        CC.STEP_PRICE STEP_PRICE
                   FROM b_tx_proposal_spec C
                   LEFT JOIN b_tx_spec_property_b CC ON (C.PROPERTY_BUYER_ID = CC.ID)
                   WHERE " . $strSqlSearch . " " . " ORDER BY C.ID asc";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function GetListSpecPrice($ID) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetListSpecPrice<br>Line: ";
        global $DB;

        $strSql = "SELECT PROPERTY_BUYER_ID, PROPOSAL_ID, P.USER_ID AS USER_ID, MAX(C.PRICE_NDS) MAX, MIN(C.PRICE_NDS) MIN
                   FROM b_tx_proposal_spec C
                   INNER JOIN b_tx_proposal P ON C.PROPOSAL_ID = P.ID
                   WHERE C.PRICE_NDS > 0 AND C.PROPOSAL_ID IN (SELECT CC.ID FROM b_tx_proposal CC WHERE CC.LOT_ID = " . $ID . ") 
                   GROUP BY C.PROPERTY_BUYER_ID";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        while ($arRes = $res->Fetch()) {
            $result[$arRes["PROPERTY_BUYER_ID"]] = $arRes;
        }
        return $result;
    }

    function GetListPriceCurUser($ID) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetListPriceCurUser<br>Line: ";
        global $DB;

//        $strSql = "SELECT PROPERTY_BUYER_ID, PROPOSAL_ID, P.USER_ID AS USER_ID, PRICE_NDS
//                   FROM b_tx_proposal_spec C
//                   INNER JOIN b_tx_proposal P ON P.ID = C.PROPOSAL_ID
//                   WHERE C.PRICE_NDS > 0 AND P.LOT_ID = " . $ID;

        $strSql = "SELECT S.PROPERTY_BUYER_ID, S.PROPOSAL_ID, P.USER_ID AS USER_ID, S.PRICE_NDS
                   FROM b_tx_proposal P
                   LEFT JOIN b_tx_proposal_spec S ON S.PROPOSAL_ID = P.ID
                   WHERE S.PRICE_NDS > 0 AND P.LOT_ID = ".$ID;

        //__($strSql);
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        while ($arRes = $res->Fetch()) {
            $result[$arRes["USER_ID"]][$arRes["PROPERTY_BUYER_ID"]] = $arRes;
        }
        return $result;
    }

    function GetListSpecPriceAll($ID) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetListSpecPriceAll<br>Line: ";
        global $DB;

        $strSql = "SELECT C.*,
                    " . $DB->DateToCharFunction("C.DATE_START") . " DATE_START
                   FROM b_tx_proposal_spec C 
                   WHERE C.PRICE_NDS > 0 AND C.PROPOSAL_ID IN (SELECT CC.ID FROM b_tx_proposal CC WHERE CC.LOT_ID = " . $ID . ") 
                   ";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function AddSpec($arFields) {
        global $DB;

        foreach ($arFields as $arFieldsName => $arFieldsValue) {
            $arInsert = $DB->PrepareInsert("b_tx_proposal_spec", $arFieldsValue);
            $strSql = "INSERT INTO b_tx_proposal_spec(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
            $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
            $ID = IntVal($DB->LastID());
        }
        return true;
    }

    function UpdateSpec($arFields) {
        global $DB;

        foreach ($arFields as $arFieldsName => $arFieldsValue) {
            $strUpdate = $DB->PrepareUpdate("b_tx_proposal_spec", $arFieldsValue);
            $strSql = "UPDATE b_tx_proposal_spec SET " . $strUpdate . " WHERE PROPOSAL_ID = " . $arFieldsValue["PROPOSAL_ID"] . " AND PROPERTY_BUYER_ID = " . $arFieldsValue["PROPERTY_BUYER_ID"];
            $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        }
        return true;
    }
	
	//09.10.2017
	//Get spec history
	function GetSpecHistory($arFilter) {
		$err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetSpecHistory<br>Line: ";
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
                    case "PROPOSAL_ID":
                        $arSqlSearch[] = GetFilterQuery("H.PROPOSAL_ID", $val, "N");
                        break;
                }
            }
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        $strSql = "SELECT 
                        H.*, 
						CC.COUNT,
                        " . $DB->DateToCharFunction("H.DATE_START") . " DATE_START 
                   FROM b_tx_proposal_spec_h H
				   LEFT JOIN b_tx_spec_property_b CC ON (H.PROPERTY_BUYER_ID = CC.ID)
                   LEFT JOIN b_tx_proposal P ON (P.ID = H.PROPOSAL_ID)
                   WHERE " . $strSqlSearch . " " . " 
				   GROUP BY DATE_START, PROPOSAL_ID, PROPERTY_BUYER_ID
				   ORDER BY H.DATE_START DESC";
				   
				 /*   echo '<pre>'; print_r($strSql); */
				   
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
	} 

    function AddSpecHistory($arFields, $arParams) {
        global $DB, $CACHE_MANAGER;

        $LOT_ID = $arParams["LOT_ID"];

        $arProposalSpec = CTenderixProposal::GetListSpecPrice($LOT_ID);
        foreach ($arProposalSpec as $proposalBuyerId => $proposalValue) {
            $arProposalSpecMin[$proposalBuyerId] = $proposalValue["MIN"];
        }

        $update_time = false;
        foreach ($arFields as $arFieldsName => $arFieldsValue) {
            if ($arProposalSpecMin[$arFieldsValue["PROPERTY_BUYER_ID"]] > $arFieldsValue["PRICE_NDS"])
                $update_time = true;
            $ID = $DB->Add("b_tx_proposal_spec_h", $arFieldsValue, array());
        }
		//Надо протестировать !!! По продлению времени
        if ($update_time) {
            $rsLot = CTenderixLot::GetByIDa($LOT_ID);
            $arLot = $rsLot->Fetch();
            $time_update_value = $arLot["TIME_UPDATE"];
            $time_extension_value = $arLot["TIME_EXTENSION"];
            $timeZone = time() + CTimeZone::GetOffset();
            if ((strtotime($arLot["DATE_END"]) - $timeZone) < $time_update_value) {
                $time_extension = $time_update_value - (strtotime($arLot["DATE_END"]) - $timeZone);
                $date_end = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), strtotime($arLot["DATE_END"]) + $time_extension);
                CTenderixLot::Update($LOT_ID, array("TIME_EXTENSION" => ($time_extension + $time_extension_value), "DATE_END" => $date_end));
            }
        }

        $CACHE_MANAGER->ClearByTag('pweb.tenderix_proposal.list_' . $LOT_ID);

        return true;
    }

    function GetListPropertyProducts($arFilter) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetListPropertyProducts<br>Line: ";
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
                    case "PROPOSAL_ID":
                        $arSqlSearch[] = GetFilterQuery("C.PROPOSAL_ID", $val, "N");
                        break;
                    case "PRODUCTS_PROPERTY_BUYER_ID":
                        $arSqlSearch[] = GetFilterQuery("C.PRODUCTS_PROPERTY_BUYER_ID", $val, "N");
                        break;
                }
            }
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        $strSql = "SELECT C.*, CCC.TITLE
                   FROM b_tx_prod_property_s C
                   LEFT JOIN b_tx_prod_property_b CC ON (C.PRODUCTS_PROPERTY_BUYER_ID = CC.ID)
                   LEFT JOIN b_tx_prod_property CCC ON (CC.PRODUCTS_PROPERTY_ID = CCC.ID)
                   WHERE " . $strSqlSearch;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function AddPropertyProducts($arFields) {
        global $DB;

        foreach ($arFields as $arPropId => $arPropValue) {
            $arInsert = $DB->PrepareInsert("b_tx_prod_property_s", $arPropValue);
            $strSql = "INSERT INTO b_tx_prod_property_s(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
            $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
            $ID = IntVal($DB->LastID());
        }
        return true;
    }

    function UpdatePropertyProducts($arFields) {
        global $DB;

        foreach ($arFields as $arPropId => $arPropValue) {
            $strUpdate = $DB->PrepareUpdate("b_tx_prod_property_s", $arPropValue);
            $strSql = "UPDATE b_tx_prod_property_s SET " . $strUpdate . " WHERE PROPOSAL_ID = " . $arPropValue["PROPOSAL_ID"] . " AND PRODUCTS_PROPERTY_BUYER_ID = " . $arPropValue["PRODUCTS_PROPERTY_BUYER_ID"];
            $res = $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        }
        return $res;
    }

    function GetListProducts($arFilter) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetListProducts<br>Line: ";
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
                    case "PROPOSAL_ID":
                        $arSqlSearch[] = GetFilterQuery("C.PROPOSAL_ID", $val, "N");
                        break;
                }
            }
        }

        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        $strSql = "SELECT C.*,
                    " . $DB->DateToCharFunction("C.DATE_START") . " DATE_START
                   FROM b_tx_proposal_prod C
                   WHERE " . $strSqlSearch;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function AddProducts($arFields) {
        global $DB;

        $arInsert = $DB->PrepareInsert("b_tx_proposal_prod", $arFields);
        $strSql = "INSERT INTO b_tx_proposal_prod(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        $ID = IntVal($DB->LastID());

        return true;
    }

    function UpdateProducts($arFields) {
        global $DB;

        $strUpdate = $DB->PrepareUpdate("b_tx_proposal_prod", $arFields);
        $strSql = "UPDATE b_tx_proposal_prod SET " . $strUpdate . " WHERE PROPOSAL_ID = " . $arFields["PROPOSAL_ID"];
        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

        return true;
    }

    function AddProductsHistory($arFields, $LOT_ID) {
        global $DB, $CACHE_MANAGER;

        $arProposalProd = CTenderixProposal::GetListProductsPrice($LOT_ID);
        $arProposalProdMin = $arProposalProd["MIN"];

        $update_time = false;
        if ($arProposalProdMin > $arFields["PRICE_NDS"]) {
            $update_time = true;
        }
        $ID = $DB->Add("b_tx_proposal_prod_h", $arFields, array());

        if ($update_time) {
            $rsLot = CTenderixLot::GetByIDa($LOT_ID);
            $arLot = $rsLot->Fetch();
            $time_update_value = $arLot["TIME_UPDATE"];
            $time_extension_value = $arLot["TIME_EXTENSION"];
            $timeZone = time() + CTimeZone::GetOffset();
            if ((strtotime($arLot["DATE_END"]) - $timeZone) < $time_update_value) {
                $time_extension = $time_update_value - (strtotime($arLot["DATE_END"]) - $timeZone);
                $date_end = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), strtotime($arLot["DATE_END"]) + $time_extension);
                CTenderixLot::Update($LOT_ID, array("TIME_EXTENSION" => ($time_extension + $time_extension_value), "DATE_END" => $date_end));
            }
        }

        $CACHE_MANAGER->ClearByTag('pweb.tenderix_proposal.list_' . $LOT_ID);

        return true;
    }

    function GetListProductsPrice($ID) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetListProductsPrice<br>Line: ";
        global $DB;

        $strSql = "SELECT MAX(C.PRICE_NDS) MAX, MIN(C.PRICE_NDS) MIN
                   FROM b_tx_proposal_prod C 
                   WHERE C.PRICE_NDS > 0 AND C.PROPOSAL_ID IN (SELECT CC.ID FROM b_tx_proposal CC WHERE CC.LOT_ID = " . $ID . ")";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        if ($arRes = $res->Fetch()) {
            $result = $arRes;
        }
        return $result;
    }

    function GetListProductsPriceAll($ID) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetListProductsPriceAll<br>Line: ";
        global $DB;

        $strSql = "SELECT C.*,
                    " . $DB->DateToCharFunction("C.DATE_START") . " DATE_START
                   FROM b_tx_proposal_prod C 
                   WHERE C.PRICE_NDS > 0 AND C.PROPOSAL_ID IN (SELECT CC.ID FROM b_tx_proposal CC WHERE CC.LOT_ID = " . $ID . ")";
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    function DeleteFile($ID, $file_id = false) {
        global $DB;
        $ID = intval($ID);
        $file_id = intval($file_id);

        $rsFile = CTenderixProposal::GetFileList($ID, $file_id);
        while ($arFile = $rsFile->Fetch()) {
            $rs = $DB->Query("DELETE FROM b_tx_proposal_file where PROPOSAL_ID=" . $ID . " AND FILE_ID=" . intval($arFile["ID"]), false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
            CFile::Delete(intval($arFile["ID"]));
        }
    }

    function SaveFile($ID, $file) {
        global $DB;
        $ID = intval($ID);
        $aMsg = array();

        $arFileName = CTenderixProposal::FileReName($file["name"]);

        $arSameNames = array();
        $rsFile = CTenderixProposal::GetFileList($ID);
        while ($arFile = $rsFile->Fetch()) {
            $arSavedName = CTenderixProposal::FileReName($arFile["ORIGINAL_NAME"]);
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
        if (($fid > 0) && $DB->Query("INSERT INTO b_tx_proposal_file (PROPOSAL_ID, FILE_ID) VALUES (" . $ID . " ," . $fid . ")", false, "File: " . __FILE__ . "<br>Line: " . __LINE__)) {
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
				,b_tx_proposal_file SF
			WHERE
				F.ID=SF.FILE_ID
				AND SF.PROPOSAL_ID=" . $ID . "
			" . ($file_id > 0 ? "AND SF.FILE_ID = " . $file_id : "") . "
			ORDER BY F.ID
		";

        return $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
    }

    function GetCountProposal($userId) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetCountProposal<br>Line: ";
        global $DB;

        $strSql = "SELECT count(*) CNT
                   FROM b_tx_proposal
                   WHERE USER_ID = " . $userId;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        $arRes = $res->Fetch();
        return $arRes["CNT"];
    }

    function GetUserProposal($userId) {
        $err_mess = (CTenderixProposal::err_mess()) . "<br>Function: GetUserProposal<br>Line: ";
        global $DB;

        $strSql = "SELECT *
                   FROM b_tx_proposal
                   WHERE USER_ID = " . $userId;
        $res = $DB->Query($strSql, false, $err_mess . __LINE__);
        return $res;
    }

    //property

    function GetFileListProperty($PROPOSAL_ID, $PROPERTY_ID, $file_id) {
        global $DB;
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
				,b_tx_proposal_propval SF
			WHERE
				F.ID=SF.VALUE
				AND SF.PROPOSAL_ID=" . $PROPOSAL_ID . "
				AND SF.PROPERTY_ID=" . $PROPERTY_ID . "
                                " . ($file_id > 0 ? "AND SF.VALUE = " . $file_id : "") . "
			ORDER BY F.ID
		";

        return $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
    }
    function GetFileListPropertyLot($LOT_ID, $PROPERTY_ID, $file_id) {
        global $DB;
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
                ,b_tx_proposal_propval SF
            WHERE
                F.ID=SF.VALUE
                AND SF.LOT_ID=" . $LOT_ID . "
                AND SF.PROPERTY_ID=" . $PROPERTY_ID . "
                                " . ($file_id > 0 ? "AND SF.VALUE = " . $file_id : "") . "
            ORDER BY F.ID
        ";

        return $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
    }

    function GetProperty($PROPOSAL_ID) {
        global $DB;
        $strSql = "SELECT * FROM b_tx_proposal_propval WHERE PROPOSAL_ID =" . $PROPOSAL_ID . " ORDER BY ID asc";
        $rsRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        while ($arRes = $rsRes->Fetch()) {
            $res[$arRes["PROPERTY_ID"]][] = $arRes;
        }
        return $res;
    }
    function GetPropertyLot($LOT_ID) {
        global $DB;
        $strSql = "SELECT * FROM b_tx_proposal_propval WHERE LOT_ID =" . $LOT_ID . " ORDER BY ID asc";
        $rsRes = $DB->Query($strSql, false, $err_mess . __LINE__);
        while ($arRes = $rsRes->Fetch()) {
            $res[$arRes["LOT_ID"]][] = $arRes;
        }
        return $res;
    }

    function SetProperty($PROPOSAL_ID, $PROPERTY = array()) {
        global $DB;
        global $USER;
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
                            "PROPOSAL_ID" => $PROPOSAL_ID,
                            "PROPERTY_ID" => $idProp,
                            "VALUE" => $fid,
                        );
                        $arInsert = $DB->PrepareInsert("b_tx_proposal_propval", $arFields);
                        $strSql = "INSERT INTO b_tx_proposal_propval(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
                        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
                    }
                }
            }
        }

        foreach ($PROPERTY as $idProp => $arrProperty) {
            $rsProp = CTenderixProposalProperty::GetList($by = "", $order = "", array("ID" => $idProp));
            $arProp = $rsProp->Fetch();

            switch ($arProp["PROPERTY_TYPE"]) {
                case "S":
                case "N":
                case "T":
                case "D":
                    foreach ($arrProperty as $k => $v) {
                        $arFields = array(
                            "PROPOSAL_ID" => $PROPOSAL_ID,
                            "PROPERTY_ID" => $idProp
                        );
                        if ($arProp["PROPERTY_TYPE"] == "D") {
                            $arFields["VALUE"] = ConvertDateTime($v, "YYYY-MM-DD HH:MI:SS");
                        } else {
                            $arFields["VALUE"] = $v;
                        }

                        if (strstr($k, "n")) {
                            if (strlen(trim($PROPERTY[$idProp][$k])) > 0) {
                                $arInsert = $DB->PrepareInsert("b_tx_proposal_propval", $arFields);
                                $strSql = "INSERT INTO b_tx_proposal_propval(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
                                $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
                            }
                        } else {
                            if (strlen(trim($PROPERTY[$idProp][$k])) > 0) {
                                $strSql_prop = "SELECT * FROM b_tx_proposal_propval WHERE PROPOSAL_ID =" . $PROPOSAL_ID . " AND PROPERTY_ID=".$idProp;
                                $rsRes_prop = $DB->Query($strSql_prop, false, $err_mess . __LINE__);
                                if(!$rsRes_prop->Fetch()) {
                                    $arInsert = $DB->PrepareInsert("b_tx_proposal_propval", $arFields);
                                    $strSql = "INSERT INTO b_tx_proposal_propval(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
                                    $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
                                } else {
                                    $strUpdate = $DB->PrepareUpdate("b_tx_proposal_propval", $arFields);
                                    $strSql = "UPDATE b_tx_proposal_propval SET " . $strUpdate . " WHERE ID = " . $k;
                                    $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
                                }
                            } else {
                                $DB->Query("DELETE FROM b_tx_proposal_propval WHERE ID = " . $k, True);
                            }
                        }
                    }
                    break;
                case "L":
                    $DB->Query("DELETE FROM b_tx_proposal_propval WHERE PROPOSAL_ID = " . $PROPOSAL_ID . " AND PROPERTY_ID = " . $idProp, True);
                    foreach ($arrProperty as $k => $v) {
                        $arFields = array(
                            "PROPOSAL_ID" => $PROPOSAL_ID,
                            "PROPERTY_ID" => $idProp
                        );
                        $arFields["VALUE"] = $v;
                        if (strlen(trim($PROPERTY[$idProp][$k])) > 0) {
                            $arInsert = $DB->PrepareInsert("b_tx_proposal_propval", $arFields);
                            $strSql = "INSERT INTO b_tx_proposal_propval(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
                            $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
                        }
                    }
                    break;
            }
        }
    }
    function SetPropertyLot($LOT_ID, $PROPERTY = array(), $n) {
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
                            "LOT_ID" => $LOT_ID,
                            "PROPERTY_ID" => $idProp,
                            "VALUE" => $fid,
                        );
                        $arInsert = $DB->PrepareInsert("b_tx_proposal_propval", $arFields);
                        $strSql = "INSERT INTO b_tx_proposal_propval(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
                        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
                    }
                }
            }
        }
        if($n == 'delete') {
            $DB->Query("DELETE FROM b_tx_proposal_propval WHERE LOT_ID = " . $LOT_ID, True);
        }
        foreach ($PROPERTY as $idProp => $arrProperty) {
            $rsProp = CTenderixProposalProperty::GetList($by = "", $order = "", array("ID" => $idProp));
            $arProp = $rsProp->Fetch();
            switch ($arProp['PROPERTY_TYPE']) {
                case 'N':
                case 'T':
                    if($n == 'new') {
                        $arFields = array("LOT_ID" => $LOT_ID,"PROPERTY_ID" => $idProp,"VALUE" => $arrProperty,);
                        
                        $arInsert = $DB->PrepareInsert("b_tx_proposal_propval", $arFields);
                        $strSql = "INSERT INTO b_tx_proposal_propval(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
                        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

                    }elseif($n == 'update'){
                        $arFields = array("LOT_ID" => $LOT_ID,"PROPERTY_ID" => $idProp,"VALUE" => $PROPERTY[$idProp]['VALUE_N'],);
                        
                        $strUpdate = $DB->PrepareUpdate("b_tx_proposal_propval", $arFields);
                        $strSql = "UPDATE b_tx_proposal_propval SET " . $strUpdate . " WHERE ID = " . $PROPERTY[$idProp]['ID'];
                        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

                    }
                    
                    break;
                case "L":
                    $DB->Query("DELETE FROM b_tx_proposal_propval WHERE LOT_ID = " . $LOT_ID . " AND PROPERTY_ID = " . $idProp, True);
                    $arFields = array(
                        "LOT_ID" => $LOT_ID,
                        "PROPERTY_ID" => $idProp
                    );
                    $arFields["VALUE"] = $arrProperty;
                        $arInsert = $DB->PrepareInsert("b_tx_proposal_propval", $arFields);
                        $strSql = "INSERT INTO b_tx_proposal_propval(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
                        $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
                    break;
                
            }


            // switch ($arProp["PROPERTY_TYPE"]) {
            //     case "S":
            //     case "N":
            //     case "T":
            //     case "D":
            //         foreach ($arrProperty as $k => $v) {
            //             $arFields = array(
            //                 "LOT_ID" => $LOT_ID,
            //                 "PROPERTY_ID" => $idProp
            //             );
            //             if ($arProp["PROPERTY_TYPE"] == "D") {
            //                 $arFields["VALUE"] = ConvertDateTime($v, "YYYY-MM-DD HH:MI:SS");
            //             } else {
            //                 $arFields["VALUE"] = $v;
            //             }

            //             if (strstr($k, "n")) {
            //                 if (strlen(trim($PROPERTY[$idProp][$k])) > 0) {
            //                     $arInsert = $DB->PrepareInsert("b_tx_proposal_propval", $arFields);
            //                     $strSql = "INSERT INTO b_tx_proposal_propval(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
            //                     $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
            //                 }
            //             } else {
            //                 if (strlen(trim($PROPERTY[$idProp][$k])) > 0) {
            //                     $strUpdate = $DB->PrepareUpdate("b_tx_proposal_propval", $arFields);
            //                     $strSql = "UPDATE b_tx_proposal_propval SET " . $strUpdate . " WHERE ID = " . $k;
            //                     $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
            //                 } else {
            //                     $DB->Query("DELETE FROM b_tx_proposal_propval WHERE ID = " . $k, True);
            //                 }
            //             }
            //         }
            //         break;
            //     case "L":
            //         $DB->Query("DELETE FROM b_tx_proposal_propval WHERE LOT_ID = " . $LOT_ID . " AND LOT_ID = " . $idProp, True);
            //         foreach ($arrProperty as $k => $v) {
            //             $arFields = array(
            //                 "LOT_ID" => $LOT_ID,
            //                 "PROPERTY_ID" => $idProp
            //             );
            //             $arFields["VALUE"] = $v;
            //             if (strlen(trim($PROPERTY[$idProp][$k])) > 0) {
            //                 $arInsert = $DB->PrepareInsert("b_tx_proposal_propval", $arFields);
            //                 $strSql = "INSERT INTO b_tx_proposal_propval(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
            //                 $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
            //             }
            //         }
            //         break;
            // }
        }
    }


    function DeleteFileProperty($PROPOSAL_ID, $file_id) {
        global $DB;
        $PROPOSAL_ID = intval($PROPOSAL_ID);
        $file_id = intval($file_id);

        $rs = $DB->Query("DELETE FROM b_tx_proposal_propval where PROPOSAL_ID=" . $PROPOSAL_ID . " AND VALUE=" . intval($file_id), false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        CFile::Delete($file_id);
    }

    function DeleteFilePropertyLot($LOT_ID, $file_id) {
        global $DB;
        $LOT_ID = intval($LOT_ID);
        $file_id = intval($file_id);

        $rs = $DB->Query("DELETE FROM b_tx_proposal_propval where LOT_ID=" . $LOT_ID . " AND VALUE=" . intval($file_id), false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        CFile::Delete($file_id);
    }

}

?>
