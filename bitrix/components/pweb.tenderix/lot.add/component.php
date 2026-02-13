<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

//__($_REQUEST);

function nfGetCurPageParam($strParam = '', $arParamKill = array(), $get_index_page = NULL, $uri = FALSE)
{

	if (NULL === $get_index_page) {

		if (defined('BX_DISABLE_INDEX_PAGE'))
			$get_index_page = !BX_DISABLE_INDEX_PAGE;
		else
			$get_index_page = TRUE;

	}

	$sUrlPath = GetPagePath($uri, $get_index_page);
	$strNavQueryString = nfDeleteParam($arParamKill, $uri);

	if ($strNavQueryString != '' && $strParam != '')
		$strNavQueryString = '&' . $strNavQueryString;

	if ($strNavQueryString == '' && $strParam == '')
		return $sUrlPath;
	else
		return $sUrlPath . '?' . $strParam . $strNavQueryString;

}


function nfDeleteParam($arParam, $uri = FALSE)
{

	$get = array();
	if ($uri && ($qPos = strpos($uri, '?')) !== FALSE) {

		$queryString = substr($uri, $qPos + 1);
		parse_str($queryString, $get);
		unset($queryString);

	}

	if (sizeof($get) < 1)
		$get = $_GET;

	if (sizeof($get) < 1)
		return '';

	if (sizeof($arParam) > 0) {

		foreach ($arParam as $param) {

			$search = & $get;
			$param = (array)$param;
			$lastIndex = sizeof($param) - 1;

			foreach ($param as $c => $key) {

				if (array_key_exists($key, $search)) {

					if ($c == $lastIndex)
						unset($search[$key]);
					else
						$search = & $search[$key];

				}

			}

		}

	}

	return str_replace(
		array('%5B', '%5D'),
		array('[', ']'),
		http_build_query($get)
	);

}


global $CACHE_MANAGER, $DB, $USER;
$module_id = "pweb.tenderix";

if (!CModule::IncludeModule("pweb.tenderix")) {
	$this->AbortResultCache();
	ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
	return;
}

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
$arResult["T_RIGHT"] = $T_RIGHT;
if($T_RIGHT == 'D') {
	$T_RIGHT = 'S';
}
//print_r($T_RIGHT);

if ($T_RIGHT < "S") {
	echo '<div class="container">';
	ShowError(GetMessage("ACCESS_DENIED"));
	header('Location: /');
	echo '</div>';
	return;
}

$ID = intval($_REQUEST["ID"]);
$COPY_ID = intval($_REQUEST["COPY_ID"]);
$ARHIVE = intval($_REQUEST["arhive"]);


if ($COPY_ID > 0)
	$arResult["COPY"] = "Y";
else
	$arResult["COPY"] = "N";

$TYPE_ID = $_REQUEST["TYPE_ID"];
$PRODUCTS_ID = $_REQUEST["PRODUCTS_ID"];

if ($ID <= 0) {
	$arResult["LOT"]["ACTIVE"] = "Y";
	$arResult["LOT"]["TIME_EXTENSION"] = 0;
	$arResult["LOT"]["TIME_UPDATE"] = 600;
	$arResult["SPEC_NEW_PROP"] = 0;
}
$rsPropertyLot = CTenderixProposalProperty::GetList($by, $order, $filter);
while ($arPropertyLot = $rsPropertyLot->GetNext() ) {
	$arResult['DOPPROPLOT'][$arPropertyLot['ID']] = $arPropertyLot ;
}
$rsUser = CTenderixUserBuyer::GetByID($USER->GetID());
$arUser = $rsUser->Fetch();
$arResult["LOT"]["COMPANY_ID"] = $arUser["COMPANY_ID"];
$arResult["LOT"]["RESPONSIBLE_FIO"] = $arUser["LAST_NAME"] . " " . $arUser["NAME"] . " " . $arUser["SECOND_NAME"];
$arResult["LOT"]["RESPONSIBLE_PHONE"] = $arUser["PERSONAL_PHONE"];



if ((isset($_REQUEST["lotadd_submit"]) || isset($_REQUEST["lotapply_submit"]) || isset($_REQUEST["lotarch_submit"]) || isset($_REQUEST["lotopen_submit"])) && $T_RIGHT >= "S" || $arhive_submit == 'Y') {

	//__($_REQUEST);

	$arFields = array(
		"SECTION_ID" => intval($_REQUEST["SECTION_ID"]),
		"TYPE_ID" => $_REQUEST["TYPE_ID"],
		"TIMESTAMP_X" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), time() + CTimeZone::GetOffset()),
		"DATE_START" => $_REQUEST["DATE_START"],
		"DATE_END" => $_REQUEST["DATE_END"],
		"NOTE" => $_REQUEST["NOTE"],
		"OPEN_PRICE" => ($_REQUEST["OPEN_PRICE"] == "Y" ? "Y" : "N"),
		"TIME_UPDATE" => (intval($_REQUEST["TIME_UPDATE"]) > 0 ? $_REQUEST["TIME_UPDATE"] : "600"),
		"DATE_DELIVERY" => $_REQUEST["DATE_DELIVERY"],
		"RESPONSIBLE_FIO" => $_REQUEST["RESPONSIBLE_FIO"],
		"TIME_EXTENSION" => $_REQUEST["TIME_EXTENSION"],
		"RESPONSIBLE_PHONE" => $_REQUEST["RESPONSIBLE_PHONE"],
		"TERM_PAYMENT_ID" => $_REQUEST["TERM_PAYMENT_ID"],
		"TERM_PAYMENT_VAL" => ($_REQUEST["TERM_PAYMENT_ID"] == 0 ? "" : $_REQUEST["TERM_PAYMENT_VAL"]),
		"TERM_PAYMENT_REQUIRED" => ($_REQUEST["TERM_PAYMENT_ID"] == 0 ? "N" : ($_REQUEST["TERM_PAYMENT_REQUIRED"] == "Y" ? "Y" : "N")),
		"TERM_PAYMENT_EDIT" => ($_REQUEST["TERM_PAYMENT_ID"] == 0 ? "N" : ($_REQUEST["TERM_PAYMENT_EDIT"] == "Y" ? "Y" : "N")),
		"TERM_DELIVERY_ID" => $_REQUEST["TERM_DELIVERY_ID"],
		"TERM_DELIVERY_VAL" => ($_REQUEST["TERM_DELIVERY_ID"] == 0 ? "" : $_REQUEST["TERM_DELIVERY_VAL"]),
		"TERM_DELIVERY_REQUIRED" => ($_REQUEST["TERM_DELIVERY_ID"] == 0 ? "N" : ($_REQUEST["TERM_DELIVERY_REQUIRED"] == "Y" ? "Y" : "N")),
		"TERM_DELIVERY_EDIT" => ($_REQUEST["TERM_DELIVERY_ID"] == 0 ? "N" : ($_REQUEST["TERM_DELIVERY_EDIT"] == "Y" ? "Y" : "N")),
		"PRIVATE" => $_REQUEST["PRIVATE"] == "Y" ? "Y" : "N",
		"NOTVISIBLE_PROPOSAL" => $_REQUEST["NOTVISIBLE_PROPOSAL"] == "Y" ? "Y" : "N",
		"PRIVATE_LIST" => $_REQUEST["PRIVATE_LIST"],
		"WITH_NDS" => $_REQUEST["WITH_NDS"] == "Y" ? "Y" : "N",
		"CURRENCY" => $_REQUEST["CURRENCY"],
		"QUOTES" => $_REQUEST["QUOTES"] == "Y" ? "Y" : "N",
		"NOSAME" => $_REQUEST["NOSAME"] == "Y" ? "Y" : "N",
		"NOBAD" => $_REQUEST["NOBAD"] == "Y" ? "Y" : "N",
		"SEND_SPEC" => $_REQUEST["SEND_SPEC"] == "Y" ? "Y" : "N",
		"SUBSCR_NOW" => '$_REQUEST["SUBSCR_NOW"] == "Y" ? "Y" : "N"',
		"SUBSCR_START" => intval($_REQUEST["SUBSCR_START"]),
		"SUBSCR_END" => intval($_REQUEST["SUBSCR_END"]),
		"VIZ_HIST" => $_REQUEST["VIZ_HIST"] == "Y" ? "Y" : "N",
		"ONLY_BEST" => $_REQUEST["ONLY_BEST"] == "Y" ? "Y" : "N",
		"NOEDIT" => $_REQUEST["NOEDIT"] == "Y" ? "Y" : "N",
		"PRE_PROPOSAL" => $_REQUEST["PRE_PROPOSAL"] == "Y" ? "Y" : "N",
	);



	if(is_array($_REQUEST["DOPPROP"])) {
		foreach ($_REQUEST["DOPPROP"] as $dopprop) {
			//__($dopprop);
			if ($dopprop['VALUE'] >= 0):
			$arFields += array(
				"DOP_".$dopprop['ID']."_PROP" => $dopprop['VALUE'],
				);
			$arResult['DOPPROP'][$dopprop['ID']] = $dopprop['VALUE'];
			endif;
		}
	}

	if (isset($_REQUEST["ACTIVE"])) {
		$arFields["ACTIVE"] = $_REQUEST["ACTIVE"];
	} else {
		$arFields["ACTIVE"] = isset($_REQUEST["lotopen_submit"]) ? "Y" : "N";
	}

	if (isset($_REQUEST["ARCHIVE"])) {
		$arFields["ARCHIVE"] = $_REQUEST["ARCHIVE"];
	} else {
		$arFields["ARCHIVE"] = isset($_REQUEST["lotarch_submit"]) ? "Y" : "N";
	}
	if (isset($_REQUEST["FAIL"])) {
		$arFields["FAIL"] = $_REQUEST["FAIL"];
	} else {
		$arFields["FAIL"] = isset($_REQUEST["lot_fail_submit"]) ? "Y" : "N";
	}

	if ($arParams["COMPANY_ONLY"] == "Y" && $ID <= 0) {
		$arFields["COMPANY_ID"] = $arUser["COMPANY_ID"];
	} elseif ($arParams["COMPANY_ONLY"] == "Y" && $ID > 0) {
		$res = CTenderixLot::GetByIDa($ID);
		$arLot = $res->Fetch();
		$arFields["COMPANY_ID"] = $arLot["COMPANY_ID"];
	} elseif (isset($_REQUEST["COMPANY_ID"])) {
		$arFields["COMPANY_ID"] = intval($_REQUEST["COMPANY_ID"]);
	}

	$arFields["TITLE"] = $_REQUEST["TITLE"];
	/*if ($TYPE_ID != 'S') {
		$arFields["TITLE"] = $_REQUEST["TITLE"];
	} else {
		if ($PRODUCTS_ID > 0) {
			$prod_arr = $_REQUEST["PRODUCTS_ID"];
			$prod_arr2 = array();
			ksort($prod_arr);
			foreach ($prod_arr as $prod_val) {
				if ($prod_val > 0)
					$prod_arr2[] = $prod_val;
			}
			$arResult["PRODUCTS_ID"] = $prod_arr2;

			$arFields["TITLE"] = "0";
			$arFields["SECTION_ID"] = 0;
		} else {
			$arFields["TITLE"] = "0";
		}
	}*/
	
	

	if ($TYPE_ID != "S" && $TYPE_ID != "R") {
		if ($ID > 0) {
			$rsSpecProp = CTenderixLotSpec::GetListProp($ID);

			//__(count($rsSpecProp->GetNext()));
			while ($arSpecProp = $rsSpecProp->GetNext()) {
				if ($_REQUEST["PROP_" . $arSpecProp["ID"] . "_DEL"] == "Y") {
					if (!CTenderixLotSpec::DeletePropID($arSpecProp["ID"])) {
						$message = Array("MESSAGE" => GetMessage("PROP_DELETE_ERROR"));
						$bInitVars = true;
					}
					continue;
				}
				$arFieldsDop[] = array(
					"TITLE" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_TITLE"],
					"ADD_INFO" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_ADD_INFO"],
					"COUNT" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_COUNT"],
					"UNIT_ID" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_UNIT_ID"],
					"START_PRICE" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_START_PRICE"],
					"STEP_PRICE" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_STEP_PRICE"],
				);
				$arFieldsDopUpdate[$arSpecProp["ID"]] = array(
					"TITLE" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_TITLE"],
					"ADD_INFO" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_ADD_INFO"],
					"COUNT" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_COUNT"],
					"UNIT_ID" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_UNIT_ID"],
					"START_PRICE" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_START_PRICE"],
					"STEP_PRICE" => $_REQUEST["PROP_" . $arSpecProp["ID"] . "_STEP_PRICE"],
				);

				$arFields["LOT_PRICE"] += $_REQUEST["PROP_" . $arSpecProp["ID"] . "_START_PRICE"];
			}

			//__($arFieldsDop);
		}

		for ($i = 0; $i < $_REQUEST["newProp"] + 1; $i++) {
			if (strlen($_REQUEST["PROP_n" . $i . "_TITLE"]) <= 0 //||
				//strlen($_REQUEST["PROP_n" . $i . "_COUNT"]) <= 0 ||
				//strlen($_REQUEST["PROP_n" . $i . "_UNIT_ID"]) <= 0
			)
				continue;
			//echo "<pre>";print_r($_REQUEST);echo "</pre>";
			$arFieldsDop[] = array(
				"TITLE" => $_REQUEST["PROP_n" . $i . "_TITLE"],
				"ADD_INFO" => $_REQUEST["PROP_n" . $i . "_ADD_INFO"],
				"COUNT" => $_REQUEST["PROP_n" . $i . "_COUNT"],
				"UNIT_ID" => $_REQUEST["PROP_n" . $i . "_UNIT_ID"],
				"START_PRICE" => $_REQUEST["PROP_n" . $i . "_START_PRICE"],
				"STEP_PRICE" => $_REQUEST["PROP_n" . $i . "_STEP_PRICE"],
			);
			$arFieldsDopNew[] = array(
				"TITLE" => $_REQUEST["PROP_n" . $i . "_TITLE"],
				"ADD_INFO" => $_REQUEST["PROP_n" . $i . "_ADD_INFO"],
				"COUNT" => $_REQUEST["PROP_n" . $i . "_COUNT"],
				"UNIT_ID" => $_REQUEST["PROP_n" . $i . "_UNIT_ID"],
				"START_PRICE" => $_REQUEST["PROP_n" . $i . "_START_PRICE"],
				"STEP_PRICE" => $_REQUEST["PROP_n" . $i . "_STEP_PRICE"],
			);

			/**
			 * Здесь считается цена лота (сумма по всем пунктам спецификации) - В. Филиппов, 30.03.16
			 */
			$arFields["LOT_PRICE"] += $_REQUEST["PROP_n" . $i . "_START_PRICE"];
		}

	}
	
	
	

	if ($TYPE_ID == "S" || $TYPE_ID == "R") {
		if ($ID > 0) {
			$k = 0;
			$rsProdBuyer = CTenderixProducts::GetListBuyer($arFilter = Array("LOT_ID" => $ID));
			while ($arProdBuyer = $rsProdBuyer->Fetch()) {
				$id_prod = $arProdBuyer["PRODUCTS_ID"];
				$arResult["PRODUCTS_ID"][] = $id_prod;
				$arFieldsDop["PRODUCTS"][$k]["PRODUCTS_ID"] = $arProdBuyer["ID"];
				$arFieldsDop["PRODUCTS"][$k]["START_PRICE"] = $_REQUEST["START_PRICE"][$k][$arProdBuyer["ID"]];
				$arFieldsDop["PRODUCTS"][$k]["STEP_PRICE"] = $_REQUEST["STEP_PRICE"][$k][$arProdBuyer["ID"]];
				$arFieldsDop["PRODUCTS"][$k]["COUNT"] = $_REQUEST["COUNT"][$k][$arProdBuyer["ID"]];
				$arFieldsDop["PRODUCTS"][$k]["COUNT_EDIT"] = ($_REQUEST["COUNT_EDIT"][$k][$arProdBuyer["ID"]] == "Y" ? "Y" : "N");

				$rsProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $id_prod), $is_filtered);
				while ($arProps = $rsProps->Fetch()) {
					$rsPropsBuyer = CTenderixProductsProperty::GetListBuyer(array("PRODUCTS_ID" => $arProdBuyer["ID"], "PRODUCTS_PROPERTY_ID" => $arProps["ID"]));
					$arPropsBuyer = $rsPropsBuyer->Fetch();
					$arFieldsDop["PRODUCTS_PROPERTY"][$k][$arPropsBuyer["ID"]]["PRODUCTS_PROPERTY_BUYER"] = $arPropsBuyer["ID"];
					$arFieldsDop["PRODUCTS_PROPERTY"][$k][$arPropsBuyer["ID"]]["VALUE"] = $_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_VALUE"][$k][$arPropsBuyer["PRODUCTS_ID"]];
					$arFieldsDop["PRODUCTS_PROPERTY"][$k][$arPropsBuyer["ID"]]["REQUIRED"] = ($_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_REQUIRED"][$k][$arPropsBuyer["PRODUCTS_ID"]] == "Y" ? "Y" : "N");
					$arFieldsDop["PRODUCTS_PROPERTY"][$k][$arPropsBuyer["ID"]]["EDIT"] = ($_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_EDIT"][$k][$arPropsBuyer["PRODUCTS_ID"]] == "Y" ? "Y" : "N");
					$arFieldsDop["PRODUCTS_PROPERTY"][$k][$arPropsBuyer["ID"]]["VISIBLE"] = ($_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_VISIBLE"][$k][$arPropsBuyer["PRODUCTS_ID"]] == "Y" ? "Y" : "N");
				}
				$k++;
			}
		} else {
			$arp = $_REQUEST["PRODUCTS_ID"];
			ksort($arp);
			foreach($arp as $vapr) {
				echo "<pre>";print_r($vapr); echo "-"; echo "</pre>";   
				if(intval($vapr) >0)
					$arResult["PRODUCTS_ID"][] = $vapr;
			}
			foreach ($arResult["PRODUCTS_ID"] as $k => $id_prod) {
				$arFieldsDop["PRODUCTS"][$k]["PRODUCTS_ID"] = $id_prod;
				$arFieldsDop["PRODUCTS"][$k]["START_PRICE"] = $_REQUEST["START_PRICE"][$k][$id_prod];
				$arFieldsDop["PRODUCTS"][$k]["STEP_PRICE"] = $_REQUEST["STEP_PRICE"][$k][$id_prod];
				$arFieldsDop["PRODUCTS"][$k]["COUNT"] = $_REQUEST["COUNT"][$k][$id_prod];
				$arFieldsDop["PRODUCTS"][$k]["COUNT_EDIT"] = ($_REQUEST["COUNT_EDIT"][$k][$id_prod] == "Y" ? "Y" : "N");
				$rsProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $id_prod), $is_filtered);
				while ($arProps = $rsProps->Fetch()) {
					$arFieldsDop["PRODUCTS_PROPERTY"][$k][$arProps["ID"]]["PRODUCTS_PROPERTY_ID"] = $arProps["ID"];
					$arFieldsDop["PRODUCTS_PROPERTY"][$k][$arProps["ID"]]["VALUE"] = $_REQUEST["PROP_PROD_" . $arProps["ID"] . "_VALUE"][$k][$id_prod];
					$arFieldsDop["PRODUCTS_PROPERTY"][$k][$arProps["ID"]]["REQUIRED"] = ($_REQUEST["PROP_PROD_" . $arProps["ID"] . "_REQUIRED"][$k][$id_prod] == "Y" ? "Y" : "N");
					$arFieldsDop["PRODUCTS_PROPERTY"][$k][$arProps["ID"]]["EDIT"] = ($_REQUEST["PROP_PROD_" . $arProps["ID"] . "_EDIT"][$k][$id_prod] == "Y" ? "Y" : "N");
					$arFieldsDop["PRODUCTS_PROPERTY"][$k][$arProps["ID"]]["VISIBLE"] = ($_REQUEST["PROP_PROD_" . $arProps["ID"] . "_VISIBLE"][$k][$id_prod] == "Y" ? "Y" : "N");
				}
			}
		}
	}

	$res_lot = 0;
	$lotSendSubscr = false;
	$lotNew = false;
	$lotUpdate = false;


	$arPropDop = array("PROPERTY" => $arResult['DOPPROP'], "FILES" => $_FILES["DOPPROP"]);
	
	

	if ($ID > 0) {
		
		
		if (CTenderixLot::CheckFields("UPDATE", $arFields, $arFieldsDop)) {

			$rsCurrLot = CTenderixLot::GetByIDa($ID);
			$arCurrLot = $rsCurrLot->GetNext();
			
			$n = 'update';

			$db_dopprop = CTenderixProposal::GetPropertyLot($ID);
			foreach($db_dopprop as $dopprop => $value){
				foreach($value as $data){
					$arPropDop['PROPERTY'][$data['PROPERTY_ID']] = $data;
					$arPropDop['PROPERTY'][$data['PROPERTY_ID']]['VALUE_N'] = $arResult['DOPPROP'][$data['PROPERTY_ID']];
				}
			}
			$updateprop = CTenderixProposal::SetPropertyLot($ID, $arPropDop, $n);


			$res_lot = CTenderixLot::Update($ID, $arFields);

			$lotUpdate = true;

			if (intval($res_lot) > 0) {
				CTenderixProposal::SetPropertyLot($ID, $arPropDop, $n);
			}

			if ($TYPE_ID != 'S' && $TYPE_ID != 'R' && intval($res_lot) > 0) {
				//Update NS lot
				$arFieldsSpec = array(
					"FULL_SPEC" => ($_REQUEST["FULL_SPEC"] == "Y" ? "Y" : "N"),
					"NOT_ANALOG" => ($_REQUEST["NOT_ANALOG"] == "Y" ? "Y" : "N")
				);

				$db_lot_spec = CTenderixLotSpec::GetListSpec($by = "", $order = "", array("LOT_ID" => $ID), $is_filtered);
				if ($arLotSpec = $db_lot_spec->Fetch()) {
					$res = CTenderixLotSpec::Update($ID, $arFieldsSpec);
				} else {
					$arFieldsSpec = array(
						"LOT_ID" => intval($ID)
					);
					$res = CTenderixLotSpec::Add($arFieldsSpec);
				}

				$SPEC_ID = intval($res);
				if ($SPEC_ID > 0) {
					foreach ($arFieldsDopUpdate as $arSpecPropId => $arFieldsProp) {
						$res = CTenderixLotSpec::UpdateProp($arSpecPropId, $arFieldsProp);
					}

					foreach ($arFieldsDopNew as $fieldPropNew) {
						$fieldPropNew["SPEC_ID"] = $SPEC_ID;
						$res = CTenderixLotSpec::AddProp($fieldPropNew);
					}
				}
			} elseif (($TYPE_ID == 'S' || $TYPE_ID == 'R') && intval($ID) > 0) {
				//Update S lot
				$arFieldsProduct = $arFieldsDop["PRODUCTS"];
				foreach ($arResult["PRODUCTS_ID"] as $k => $id_prod) {
					$arFieldsProduct[$k]["LOT_ID"] = intval($ID);
					$res = CTenderixLotProduct::Update($ID, $arFieldsProduct[$k]); //:TODO поправить CheckFields
					$PRODUCT_ID_BUYER = intval($res);
					if ($PRODUCT_ID_BUYER > 0) {
						foreach ($arFieldsDop["PRODUCTS_PROPERTY"][$k] as $arFieldsProductProps) {
							$arFieldsProductProps["PRODUCTS_ID"] = $PRODUCT_ID_BUYER;
							$res = CTenderixLotProduct::UpdateProp($arFieldsProductProps["PRODUCTS_PROPERTY_BUYER"], $arFieldsProductProps);
						}
					}
				}
			}
		}
	} else {
		if (isset($_GET["BUYER_ID"]) && isset($_GET["LINKED"])) {
			$arFields["BUYER_ID"] = intval($_REQUEST["BUYER_ID"]);
			$arFields["LINKED"] = $COPY_ID;
		}else {			
			$arFields["BUYER_ID"] = $USER->GetID();
		}

		//__($arFields);

		//$arPropDop["PROPERTY_S"] = $arrPropS;
		if (CTenderixLot::CheckFields("ADD", $arFields, $arFieldsDop)) {
		//if (CTenderixLot::CheckFields("ADD", $arFields)) {
			// //__($arFields);
			// $ID = 21;
			// $create_dop = CTenderixProposal::SetPropertyLot($ID, $arPropDop);

//			__($arFields);
//			die();
			/* echo '<pre>';
			print_r($arFields); */
			
			$res_lot = CTenderixLot::Add($arFields);
			$n = 'new';
			$ID = intval($res_lot);
			
			
			
			// if (intval($ID) > 0) {
			// 	$arResult["LOT_ID"] = $ID;
			// 	CTenderixProposal::SetPropertyLot($ID, $arPropDop);
			// }
			if (intval($ID) > 0) {
				CTenderixProposal::SetPropertyLot($ID, $arPropDop, $n);
			}

			$lotNew = true;

			$CACHE_MANAGER->ClearByTag('pweb.tenderix_user.info_' . $USER->GetID());
			if ($TYPE_ID != 'S' && $TYPE_ID != 'R' && intval($res_lot) > 0) {
				$arFieldsSpec = array(
					"FULL_SPEC" => ($_REQUEST["FULL_SPEC"] == "Y" ? "Y" : "N"),
					"NOT_ANALOG" => ($_REQUEST["NOT_ANALOG"] == "Y" ? "Y" : "N"),
					"LOT_ID" => intval($res_lot)
				);
				$res = CTenderixLotSpec::Add($arFieldsSpec);

				$SPEC_ID = intval($res);
				if ($SPEC_ID > 0) {
					foreach ($arFieldsDop as $fieldPropNew) {
						$fieldPropNew["SPEC_ID"] = $SPEC_ID;
						$res = CTenderixLotSpec::AddProp($fieldPropNew);
					}
				}
			} elseif (($TYPE_ID == 'S' || $TYPE_ID == 'R') && intval($res_lot) > 0) {
				$arFieldsProduct = $arFieldsDop["PRODUCTS"];
				foreach ($arResult["PRODUCTS_ID"] as $k => $id_prod) {
					$arFieldsProduct[$k]["LOT_ID"] = intval($res_lot);
					$res = CTenderixLotProduct::Add($arFieldsProduct[$k]); //:TODO поправить CheckFields
					$PRODUCT_ID_BUYER = intval($res);
					if ($PRODUCT_ID_BUYER > 0) {
						foreach ($arFieldsDop["PRODUCTS_PROPERTY"][$k] as $arFieldsProductProps) {
							//print_r($arFieldsProductProps);
							
							$arFieldsProductProps["PRODUCTS_ID"] = $PRODUCT_ID_BUYER;
							$res = CTenderixLotProduct::AddProp($arFieldsProductProps);
						}
					}
				}
			}
		}
	}
	
	
	//Рассылка
	
	
	/*
	if ($res_lot > 0 && isset($_REQUEST["lotopen_submit"]) && $_REQUEST["SUBSCR_NOW"] == "Y") {
		if($arFields['PRIVATE'] == 'Y'){
			$emailSubscrSupplier = CTenderixUserSupplier::GetEmailSubscribeListLot($arFields["PRIVATE_LIST"]);
		}else{
			$emailSubscrSupplier = CTenderixUserSupplier::GetEmailSubscribeListSection($arFields["SECTION_ID"], $arFields["PRIVATE_LIST"]);
		}
		$COMPANY = CTenderixCompany::GetByIdName($arFields["COMPANY_ID"]);
		$rsSupplier = CUser::GetByID($arFields["BUYER_ID"]);
		$arSupplier = $rsSupplier->Fetch();
		foreach ($emailSubscrSupplier as $idSupplier => $infoSupplier) {
			$arEventFields = array(
				"LOT_NUM" => $ID,
				"LOT_NAME" => $arFields["TITLE"],
				"SUPPLIER" => $infoSupplier["FIO"],
				"COMPANY" => $COMPANY,
				"RESPONSIBLE_FIO" => $arFields["RESPONSIBLE_FIO"],
				"RESPONSIBLE_PHONE" => $arFields["RESPONSIBLE_PHONE"],
				"DATE_START" => $arFields["DATE_START"],
				"DATE_END" => $arFields["DATE_END"],
				"EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
				"EMAIL_TO" => $infoSupplier["EMAIL"],
				"NOTE" => strlen($arFields["NOTE"]) > 0 ? $arFields["NOTE"] : "-",
				"RESPONSIBLE_EMAIL" => $arSupplier["EMAIL"],
			);
			$arrSITE = CTenderixLot::GetSite();

			if ($lotNew) {
				CEvent::Send("TENDERIX_NEW_LOT", $arrSITE, $arEventFields, "N");
				CTenderixLog::Log("TENDERIX_NEW_LOT", array("ID" => $ID, "FIELDS" => $arEventFields));
			}			
			if ($lotUpdate && $arCurrLot["ACTIVE"] == "Y" && $arCurrLot["SUBSCR_NOW"] == "Y")
				CEvent::Send("TENDERIX_UPDATE_LOT", $arrSITE, $arEventFields, "N");
			elseif ($lotUpdate && $arCurrLot["ACTIVE"] == "Y" && $arCurrLot["SUBSCR_NOW"] == "N") {
				CEvent::Send("TENDERIX_NEW_LOT", $arrSITE, $arEventFields, "N");
				CTenderixLog::Log("TENDERIX_NEW_LOT", array("ID" => $ID, "FIELDS" => $arEventFields));
			}
			else {
				CEvent::Send("TENDERIX_NEW_LOT", $arrSITE, $arEventFields, "N");
				CTenderixLog::Log("TENDERIX_NEW_LOT", array("ID" => $ID, "FIELDS" => $arEventFields));
			}
		}
	}
	*/

	//Add Files
	if (intval($res_lot) > 0) {
		//Delete
		if (is_array($_REQUEST["FILE_ID"]))
			foreach ($_REQUEST["FILE_ID"] as $file)
				CTenderixLot::DeleteFile($ID, $file);

		//New files
		$arFiles = array();

		//Brandnew
		if (is_array($_FILES["NEW_FILE"]))
			foreach ($_FILES["NEW_FILE"] as $attribute => $files)
				if (is_array($files))
					foreach ($files as $index => $value)
						$arFiles[$index][$attribute] = $value;


		foreach ($arFiles as $file) {

			if (strlen($file["name"]) > 0 && intval($file["size"]) > 0) {
				$res = CTenderixLot::SaveFile($ID, $file);
				if (!$res)
					break;
			}
		}

		//add subscr
		if (isset($_REQUEST["lotopen_submit"]) || $arFields["ACTIVE"] == "Y") {
			$tekTime = date("d.m.Y H:i:s");
			if($arFields["DATE_END"]){
				$min_add = 30;
				$dEnd = date("d.m.Y H:i:s", strtotime($arFields["DATE_END"] . " + " . $min_add . " minutes"));
				if (strtotime($dEnd) >= strtotime($tekTime)) {
					//CAgent::RemoveAgent("TenderixSequrityLot(1," . $ID . ");", "");
					//CAgent::AddAgent("TenderixSequrityLot(1," . $ID . ");", "", "N", 0, $dEnd, "Y", $dEnd);
				}
			}
			if ($arFields["SUBSCR_START"] > 0) {
				$sendTime = date("d.m.Y H:i:s", strtotime($arFields["DATE_START"] . " - " . $arFields["SUBSCR_START"] . " minutes"));
				if (strtotime($sendTime) >= strtotime($tekTime)) {
					CAgent::RemoveAgent("TenderixDistrLot(1," . $ID . ");", "");
					CAgent::AddAgent("TenderixDistrLot(1," . $ID . ");", "", "N", 0, $sendTime, "Y", $sendTime);
				}
			} else {
				CAgent::RemoveAgent("TenderixDistrLot(1," . $ID . ");", "");
			}
			if ($arFields["SUBSCR_END"] > 0) {
				$sendTime = date("d.m.Y H:i:s", strtotime($arFields["DATE_END"] . " - " . $arFields["SUBSCR_END"] . " minutes"));
				if (strtotime($sendTime) >= strtotime($tekTime)) {
					CAgent::RemoveAgent("TenderixDistrLot(2," . $ID . ");", "");
					CAgent::AddAgent("TenderixDistrLot(2," . $ID . ");", "", "N", 0, $sendTime, "Y", $sendTime);
				}
			} else {
				CAgent::RemoveAgent("TenderixDistrLot(2," . $ID . ");", "");
			}
		} else {
			CAgent::RemoveAgent("TenderixDistrLot(1," . $ID . ");", "");
			CAgent::RemoveAgent("TenderixDistrLot(2," . $ID . ");", "");
		}


	}

// Тут надо сделать чтобы перенаправление было на список более гибко
	
	$sRedirectUrl = $APPLICATION->GetCurPage() . "?ID=" . $ID;
	if (!$ex = $APPLICATION->GetException()) {
		if (isset($_REQUEST["lotadd_submit"]) || isset($_REQUEST["lotopen_submit"]) || isset($_REQUEST["lotapply_submit"]))
			if(isset($_REQUEST["lotadd_submit"]))
				LocalRedirect("/?af_ff%5BUSER%5D=Y");
			elseif(isset($_REQUEST["lotapply_submit"]))
				LocalRedirect($sRedirectUrl);
			elseif($arhive_submit == 'Y')
				LocalRedirect("/");
			else
				LocalRedirect("/");
		else
			LocalRedirect($sRedirectUrl);
		exit();
	}
}

if ($ex = $APPLICATION->GetException()) {
	$e = new CAdminException($messages ="", $id = false);
	$arResult["ERRORS_ARRAY"] = $ex->GetMessages();
	$arResult["ERRORS"] = $ex->GetString();
}

if ($ID > 0) {
	$arFilterLot["ID"] = $ID;
	/* if ($T_RIGHT != "W") {
	  $arFilterLot["BUYER_ID"] = $USER->GetID();
	  } */

	$db_lot = CTenderixLot::GetList($by = "", $order = "", $arFilterLot);
	if ($arLot = $db_lot->Fetch()) {
		$arResult["LOT"] = $arLot;
	}
	if ($arResult["LOT"]["BUYER_ID"] != $USER->GetID() && $T_RIGHT != "W" && !in_array($arResult["LOT"]["BUYER_ID"], unserialize($arUser["USER_BIND"]))) {
		ShowError(GetMessage("ACCESS_DENIED"));
		return;
	}
	$db_dopprop = CTenderixProposal::GetPropertyLot($arResult["LOT"]['ID']);
	foreach($db_dopprop as $dopprop => $value){
		foreach($value as $data){
			$arResult['DOPPROP'][$data['PROPERTY_ID']] = $data['VALUE'];
			$arResult['DOPPROP_DB'][$data['PROPERTY_ID']] = $data;
		}
	}

	//private lot -->
	if ($arLot["PRIVATE"] == "Y") {
		$rsRes = CTenderixLot::GetUserPrivateLot($ID);
		while ($arRes = $rsRes->Fetch()) {
			$arSupplierSelect[] = $arRes["USER_ID"];
		}
		$rsSupplier = CTenderixUserSupplier::GetListUser(array("NAME_COMPANY" => "ASC"), array());
		while ($arSupplier = $rsSupplier->Fetch()) {
			$rsUser = CUser::GetByID($arSupplier['USER_ID']);
			$arSupplier['USER_INFO'] = $rsUser->Fetch();
			$arResult["LOT"]["PRIVATE_USER"][] = array(
				"company" => $arSupplier["NAME_COMPANY"],
				"id" => $arSupplier["USER_ID"],
				"email" => $arSupplier["USER_INFO"]["EMAIL"],
				"login" => $arSupplier["USER_INFO"]["LOGIN"],
			);
			if (in_array($arSupplier["USER_ID"], $arSupplierSelect)) {
				$arResult["LOT"]["PRIVATE_LIST"][] = array(
					"company" => $arSupplier["NAME_COMPANY"],
					"id" => $arSupplier["USER_ID"],
					"email" => $arSupplier["USER_INFO"]["EMAIL"],
					"login" => $arSupplier["USER_INFO"]["LOGIN"],
				);
			}
		}
	}
	//private lot <--

	$TYPE_ID = $arResult["LOT"]["TYPE_ID"];
	if ($TYPE_ID != 'S' && $TYPE_ID != 'R') {
		$db_lot_spec = CTenderixLotSpec::GetListSpec($by = "", $order = "", array("LOT_ID" => $ID), $is_filtered);
		if ($arLotSpec = $db_lot_spec->Fetch()) {
			$arResult["LOT"] = array_merge($arLotSpec, $arResult["LOT"]);
		}
		$db_lot_spec_prop = CTenderixLotSpec::GetListProp($ID);
		while ($arLotSpecProp = $db_lot_spec_prop->Fetch()) {
			$arResult["LOT"]["SPEC"][] = $arLotSpecProp;
		}
	}

	if ($TYPE_ID == 'S' || $TYPE_ID == 'R') {
		$db_lot_prod = CTenderixProducts::GetListBuyer(array("LOT_ID" => $ID));
		$k = 0;
		while ($arLotProd = $db_lot_prod->Fetch()) {
			$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PRODUCTS_ID"][$k] = $arLotProd["PRODUCTS_ID"];
			$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["START_PRICE"][$k] = $arLotProd["START_PRICE"];
			$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["STEP_PRICE"][$k] = $arLotProd["STEP_PRICE"];
			$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["COUNT"][$k] = $arLotProd["COUNT"];
			$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["COUNT_EDIT"][$k] = $arLotProd["COUNT_EDIT"];

			$arResult["PRODUCTS_ID"][$k] = $arLotProd["ID"];
			$arResult["PRODUCTS_ID2"][$k] = $arLotProd["PRODUCTS_ID"];

			$db_lot_prod_prop = CTenderixProductsProperty::GetListBuyer(array("ACTIVE" => "Y", "PRODUCTS_ID" => $arLotProd["ID"]));
			while ($arLotProdProps = $db_lot_prod_prop->Fetch()) {
				$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]]["VALUE"][$k] = $arLotProdProps["VALUE"];
				$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]]["PRODUCTS_PROPERTY_ID"][$k] = $arLotProdProps["PRODUCTS_PROPERTY_ID"];
				$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]]["REQUIRED"][$k] = $arLotProdProps["REQUIRED"];
				$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]]["VISIBLE"][$k] = $arLotProdProps["VISIBLE"];
				$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]]["EDIT"][$k] = $arLotProdProps["EDIT"];
				$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]]["ID"][$k] = $arLotProdProps["ID"];
			}
			$k++;
		}
	}

	$rsFiles = CTenderixLot::GetFileList($ID);
	while ($arFile = $rsFiles->GetNext()) {
		$arResult["LOT"]["FILE"][] = $arFile;
	}
}

//print_r($arResult["LOT"]["TOVAR"]);

if ($COPY_ID > 0) {
	$arFilterLot["ID"] = $COPY_ID;
	/* if ($T_RIGHT != "W") {
	  $arFilterLot["BUYER_ID"] = $USER->GetID();
	  } */

	$db_lot = CTenderixLot::GetList($by = "", $order = "", $arFilterLot);
	if ($arLot = $db_lot->Fetch()) {
		$arResult["LOT"] = $arLot;
	}

	// Разрешаю покупателям копировать лоты
	// if ($arResult["LOT"]["BUYER_ID"] != $USER->GetID() && $T_RIGHT != "W" && !in_array($arResult["LOT"]["BUYER_ID"], unserialize($arUser["USER_BIND"]))) {
	//     ShowError(GetMessage("ACCESS_DENIED"));
	//     return;
	// }

	//private lot -->
	if ($arLot["PRIVATE"] == "Y") {
		$rsRes = CTenderixLot::GetUserPrivateLot($COPY_ID);
		while ($arRes = $rsRes->Fetch()) {
			$arSupplierSelect[] = $arRes["USER_ID"];
		}
		$rsSupplier = CTenderixUserSupplier::GetListUser(array("NAME_COMPANY" => "ASC"), array());
		while ($arSupplier = $rsSupplier->Fetch()) {
			$rsUser = CUser::GetByID($arSupplier['USER_ID']);
			$arSupplier['USER_INFO'] = $rsUser->Fetch();
			$arResult["LOT"]["PRIVATE_USER"][] = array(
				"company" => $arSupplier["NAME_COMPANY"],
				"id" => $arSupplier["USER_ID"],
				"email" => $arSupplier["USER_INFO"]["EMAIL"],
				"login" => $arSupplier["USER_INFO"]["LOGIN"],
			);
			if (in_array($arSupplier["USER_ID"], $arSupplierSelect)) {
				$arResult["LOT"]["PRIVATE_LIST"][] = array(
					"company" => $arSupplier["NAME_COMPANY"],
					"id" => $arSupplier["USER_ID"],
					"email" => $arSupplier["USER_INFO"]["EMAIL"],
					"login" => $arSupplier["USER_INFO"]["LOGIN"],
				);
			}
		}
	}
	//private lot <--

	$TYPE_ID = $arResult["LOT"]["TYPE_ID"];
	if ($TYPE_ID != 'S' && $TYPE_ID != 'R') {
		$db_lot_spec = CTenderixLotSpec::GetListSpec($by = "", $order = "", array("LOT_ID" => $COPY_ID), $is_filtered);
		if ($arLotSpec = $db_lot_spec->Fetch()) {
			$arResult["LOT"] = array_merge($arLotSpec, $arResult["LOT"]);
		}
		/*$db_lot_spec_prop = CTenderixLotSpec::GetListProp($COPY_ID);
		while ($arLotSpecProp = $db_lot_spec_prop->Fetch()) {
			$arResult["LOT"]["SPEC"][] = $arLotSpecProp;
		}*/
		$idProp = 1;
		$db_lot_spec_prop = CTenderixLotSpec::GetListProp($COPY_ID);
		while ($arLotSpecProp = $db_lot_spec_prop->Fetch()) {
			$arLotSpecProp["PROP_ID"] = "n" . $idProp;
			$arResult["LOT"]["SPEC"][] = $arLotSpecProp;
			$idProp++;
		}
		$arResult["SPEC_NEW_PROP"] = $idProp;
	}
	if ($TYPE_ID == 'S' || $TYPE_ID == 'R') {
		$db_lot_prod = CTenderixProducts::GetListBuyer(array("LOT_ID" => $COPY_ID));
		$k = 0;
		while ($arLotProd = $db_lot_prod->Fetch()) {
			$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PRODUCTS_ID"][$k] = $arLotProd["PRODUCTS_ID"];
			$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["START_PRICE"][$k] = $arLotProd["START_PRICE"];
			$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["STEP_PRICE"][$k] = $arLotProd["STEP_PRICE"];
			$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["COUNT"][$k] = $arLotProd["COUNT"];
			$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["COUNT_EDIT"][$k] = $arLotProd["COUNT_EDIT"];

			$arResult["PRODUCTS_ID"][$k] = $arLotProd["ID"];
			$arResult["PRODUCTS_ID2"][$k] = $arLotProd["PRODUCTS_ID"];

			$db_lot_prod_prop = CTenderixProductsProperty::GetListBuyer(array("ACTIVE" => "Y", "PRODUCTS_ID" => $arLotProd["ID"]));
			while ($arLotProdProps = $db_lot_prod_prop->Fetch()) {
				$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]]["VALUE"][$k] = $arLotProdProps["VALUE"];
				$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]]["PRODUCTS_PROPERTY_ID"][$k] = $arLotProdProps["PRODUCTS_PROPERTY_ID"];
				$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]]["REQUIRED"][$k] = $arLotProdProps["REQUIRED"];
				$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]]["VISIBLE"][$k] = $arLotProdProps["VISIBLE"];
				$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]]["EDIT"][$k] = $arLotProdProps["EDIT"];
				$arResult["LOT"]["TOVAR_BUYER"][$arLotProd["ID"]]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]]["ID"][$k] = $arLotProdProps["ID"];
			}
			$k++;
		}
	}
	/*if ($TYPE_ID == 'S') { //:TODO поправить тут тоже
		$db_lot_prod = CTenderixProducts::GetListBuyer(array("LOT_ID" => $COPY_ID));
		if ($arLotProd = $db_lot_prod->Fetch()) {
			$arResult["LOT"]["TOVAR_BUYER"] = $arLotProd;
			$arResult["PRODUCTS_ID"] = $arResult["LOT"]["TOVAR_BUYER"]["PRODUCTS_ID"];
		}
		$db_lot_prod_prop = CTenderixProductsProperty::GetListBuyer(array("ACTIVE" => "Y", "PRODUCTS_ID" => $arResult["LOT"]["TOVAR_BUYER"]["ID"]));
		while ($arLotProdProps = $db_lot_prod_prop->Fetch()) {
			$arResult["LOT"]["TOVAR_BUYER"]["PROP"][$arLotProdProps["PRODUCTS_PROPERTY_ID"]] = $arLotProdProps;
		}
	}*/

	/*$rsFiles = CTenderixLot::GetFileList($COPY_ID);
	while ($arFile = $rsFiles->GetNext()) {
		$arResult["LOT"]["FILE"][] = $arFile;
	}*/
}

$arResult["LOT"]["CURRENCY_ARRAY"] = array();
if (CModule::IncludeModule("currency")) {
	$lcur = CCurrency::GetList(($b = "sort"), ($order1 = "asc"), LANGUAGE_ID);
	while ($lcur_res = $lcur->Fetch()) {
		$rsCur = CCurrencyRates::GetList($by = "DATE_RATE", $order = "desc", $arFilter = Array("CURRENCY" => $lcur_res["CURRENCY"]));
		$arCur = $rsCur->Fetch();
		$arResult["LOT"]["CURRENCY_ARRAY"][$lcur_res["CURRENCY"]] = $arCur["RATE"] > 0 ? CurrencyFormat($arCur["RATE"], "KZT") : "";
	}
}
//!!!

/* $arCurrencyKZT = $arResult["LOT"]["CURRENCY_ARRAY"]["KZT"];
$arResult["LOT"]["CURRENCY_ARRAY"] = array (
	'KZT' => $arCurrencyKZT,
); */

$arResult["TYPE_URL"]["N"] = $APPLICATION->GetCurPageParam("TYPE_ID=N", array("TYPE_ID"));
$arResult["TYPE_URL"]["S"] = $APPLICATION->GetCurPageParam("TYPE_ID=S", array("TYPE_ID"));
$arResult["TYPE_URL"]["P"] = $APPLICATION->GetCurPageParam("TYPE_ID=P", array("TYPE_ID"));
$arResult["TYPE_URL"]["T"] = $APPLICATION->GetCurPageParam("TYPE_ID=T", array("TYPE_ID"));
$arResult["TYPE_URL"]["R"] = $APPLICATION->GetCurPageParam("TYPE_ID=R", array("TYPE_ID"));
if (isset($_REQUEST["TYPE_ID"])) {
	$arResult["TYPE_ID"] = $_REQUEST["TYPE_ID"];
} else {
	$arResult["TYPE_ID"] = isset($arResult["LOT"]["TYPE_ID"]) ? $arResult["LOT"]["TYPE_ID"] : "N";
}

$rsCatalog = CTenderixSection::GetCatalogList($by = "id", $order = "asc", array("ACTIVE" => "Y"));
while ($arCatalog = $rsCatalog->GetNext()) {
	$arCat[$arCatalog["CATALOG_ID"]][] = $arCatalog;
}
$arResult["CATALOG"] = CTenderixSection::BuildTree($arCat, 0, 0);

$rsSection = CTenderixSection::GetList($by = "s_c_sort", $order = "asc", array("ACTIVE" => "Y"), $is_filtered = false);
while ($arSection = $rsSection->Fetch()) {
	$arResult["SECTION"][$arSection["ID"]] = $arSection["TITLE"];
	$arResult["SECTION_ARR"][$arSection["CATALOG_ID"]][] = $arSection;
}

$rsCompany = CTenderixCompany::GetList($by = "s_title", $order = "desc", array(), $is_filtered);
while ($arCompany = $rsCompany->Fetch()) {
	$arResult["COMPANY"][$arCompany["ID"]] = $arCompany["TITLE"];
}
//N tovar
if ($arResult["TYPE_ID"] != "S" && $arResult["TYPE_ID"] != "R") {
	$rsUnit = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_UNIT")), $is_filtered);
	while ($arUnit = $rsUnit->GetNext()) {
		$arResult["UNIT"][$arUnit["ID"]] = $arUnit["TITLE"];
	}
}
//S tovar Сделал чтобы менялись при выборе категории
if ($arResult["TYPE_ID"] == "S" || $arResult["TYPE_ID"] == "R"){
	
	$rsProducts = CTenderixProducts::GetList($by = "ID" , $order= "asc", $arFilter = array("SECTION_ID" => $_REQUEST["SECTION_ID"]));
	$arResult["PRODUCTS"][0] = "--";
	$arResult["PRODUCTS_URL"][0] = $APPLICATION->GetCurPageParam("PRODUCTS_ID[]=0", array("PRODUCTS_ID"));
	while ($arProducts = $rsProducts->Fetch()) {
		$arResult["PRODUCTS"][$arProducts["ID"]] = $arProducts["TITLE"];
		$arResult["PRODUCTS_URL"][$arProducts["ID"]] = $APPLICATION->GetCurPageParam("PRODUCTS_ID[]=" . $arProducts["ID"], array("PRODUCTS_ID"));
	}
	if (isset($_REQUEST["PRODUCTS_ID"]) && !isset($arResult["PRODUCTS_ID"])) {
		$prod_arr = $_REQUEST["PRODUCTS_ID"];
		$prod_arr2 = array();
		ksort($prod_arr);
		foreach ($prod_arr as $prod_val) {
			if ($prod_val > 0)
				$prod_arr2[] = $prod_val;
		}
		//$_REQUEST["PRODUCTS_ID"] = $prod_arr2;
		$arResult["PRODUCTS_ID"] = $prod_arr2;
	}


	/*if (isset($arResult["PRODUCTS_ID2"])) {
		foreach ($arResult["PRODUCTS_ID2"] as $k2 => $id_prod2) {
			$rsProd2 = CTenderixProducts::GetList($by = "", $order = "", array("ID" => $id_prod2));
			while ($arProd2 = $rsProd2->Fetch()) {
				$arResult["LOT"]["TOVAR"][$id_prod2]["TITLE"] = $arProd2["TITLE"];
				$arResult["LOT"]["TOVAR"][$id_prod2]["UNIT_NAME"] = $arProd2["UNIT_NAME"];
				$arrr[] = $arProd2;
			}
		}
	} print_r($arResult["PRODUCTS_ID"]);*/

	if (isset($arResult["PRODUCTS_ID2"])) {
		$ppprr = $arResult["PRODUCTS_ID"];
		$arResult["PRODUCTS_ID"] = $arResult["PRODUCTS_ID2"];
	}

	foreach ($arResult["PRODUCTS_ID"] as $k => $id_prod) {
		/*if (isset($arResult["PRODUCTS_ID2"]))
			$rsProd = CTenderixProducts::GetList($by = "", $order = "", array("ID" => $arResult["PRODUCTS_ID2"][$k]));
		else*/
		$rsProd = CTenderixProducts::GetList($by = "", $order = "", array("ID" => $id_prod));

		while ($arProd = $rsProd->Fetch()) {
			if (isset($arResult["PRODUCTS_ID2"])) {
				$arProd["ID"] = $ppprr[$k];
			}
			$arResult["LOT"]["TOVAR"][$arProd["ID"]]["ID"] = $arProd["ID"];
			$arResult["LOT"]["TOVAR"][$arProd["ID"]]["SECTION_ID"] = $arProd["SECTION_ID"];
			$arResult["LOT"]["TOVAR"][$arProd["ID"]]["TITLE"] = $arProd["TITLE"];
			$arResult["LOT"]["TOVAR"][$arProd["ID"]]["UNIT_NAME"] = $arProd["UNIT_NAME"];
			$arResult["LOT"]["TOVAR"][$arProd["ID"]]["ACTIVE"] = $arProd["ACTIVE"];
			$arResult["LOT"]["TOVAR"][$arProd["ID"]]["UNIT_ID"] = $arProd["UNIT_ID"];
			$arResult["LOT"]["TOVAR"][$arProd["ID"]]["PROPERTY"] = $arProd["PROPERTY"];
			$arResult["LOT"]["TOVAR"][$arProd["ID"]]["SECTION"] = $arProd["SECTION"];

			if (isset($arResult["PRODUCTS_ID2"])) {
				$arProd["ID"] = $arResult["PRODUCTS_ID2"][$k];
			}

			$rsProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("ACTIVE" => "Y", "PRODUCTS_ID" => $arProd["ID"]), $is_filtered);
			while ($arProps = $rsProps->Fetch()) {
				/*if (isset($arResult["LOT"]["TOVAR_BUYER"]) && !isset($arResult["LOT"]["TOVAR_BUYER"][$arProd["ID"]]["PROP"][$arProps["ID"]])) {
					continue;
				}*/
				if (isset($arResult["PRODUCTS_ID2"])) {
					$arProd["ID"] = $ppprr[$k];
				}
				$arResult["LOT"]["TOVAR"][$arProd["ID"]]["PROP"][$arProps["ID"]]["ID"][$k] = $arProps["ID"];
				$arResult["LOT"]["TOVAR"][$arProd["ID"]]["PROP"][$arProps["ID"]]["TITLE"][$k] = $arProps["TITLE"];
				$arResult["LOT"]["TOVAR"][$arProd["ID"]]["PROP"][$arProps["ID"]]["REQUIRED"][$k] = $arProps["REQUIRED"];
				$arResult["LOT"]["TOVAR"][$arProd["ID"]]["PROP"][$arProps["ID"]]["EDIT"][$k] = $arProps["EDIT"];
				$arResult["LOT"]["TOVAR"][$arProd["ID"]]["PROP"][$arProps["ID"]]["ACTIVE"][$k] = $arProps["ACTIVE"];
				//$arResult["LOT"]["TOVAR"][$arProd["ID"]]["PROP"][$arProps["ID"]]["VISIBLE"][$k] = "Y";
				if (intval($arProps["SPR_ID"]) > 0) {
					$rsSpr = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => $arProps["SPR_ID"]), $is_filtered);
					while ($arSpr = $rsSpr->GetNext()) {
						$arrSpr[$arSpr["ID"]] = $arSpr["TITLE"];
					}
					$arResult["LOT"]["TOVAR"][$arProd["ID"]]["PROP"][$arProps["ID"]]["SPR_ID"][$k] = $arrSpr;
				}
				$arResult["LOT"]["TOVAR"][$arProd["ID"]]["PROP"][$arProps["ID"]]["VISIBLE"][$k] = "Y";
			}
		}
	}

	if (isset($arResult["PRODUCTS_ID2"])) {
		$arResult["PRODUCTS_ID"] = $ppprr;
	}

	//data merge tovar base and tovar buyer -->
	if (isset($arResult["LOT"]["TOVAR_BUYER"])) {
		/*foreach ($arResult["LOT"]["TOVAR_BUYER"] as $k_TB => $v_TB) {
			if ($k_TB != "PROP") {
				$arResult["LOT"]["TOVAR"][$k_TB] = $v_TB;
			} else {
				foreach ($v_TB as $k_TB_PROP => $v_TB_PROP) {
					foreach ($v_TB_PROP as $k_TB_PROP_name => $v_TB_PROP_val) {
						$arResult["LOT"]["TOVAR"]["PROP"][$k_TB_PROP][$k_TB_PROP_name] = $v_TB_PROP_val;
					}
				}
			}
		}*/
		foreach ($arResult["LOT"]["TOVAR_BUYER"] as $idTov => $vTov) {
			foreach ($vTov as $k_TB => $v_TB) {
				if ($k_TB != "PROP") {
					$arResult["LOT"]["TOVAR"][$idTov][$k_TB] = $v_TB;
				} else {
					foreach ($v_TB as $k_TB_PROP => $v_TB_PROP) {
						foreach ($v_TB_PROP as $k_TB_PROP_name => $v_TB_PROP_val) {
							$arResult["LOT"]["TOVAR"][$idTov]["PROP"][$k_TB_PROP][$k_TB_PROP_name] = $v_TB_PROP_val;
						}
					}
				}
			}
		}
	}
	//data merge tovar base and tovar buyer <--
}

$rsUnit = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_TERM_DELIVERY")), $is_filtered);
while ($arUnit = $rsUnit->GetNext()) {
	$arResult["DELIVERY"][$arUnit["ID"]] = $arUnit["TITLE"];
}

$rsUnit = CTenderixSprDetails::GetList($by, $order, $arFilter = Array("SPR_ID" => COption::GetOptionString($module_id, "PW_TD_OPTIONS_SPR_TERM_PAYMENT")), $is_filtered);
while ($arUnit = $rsUnit->GetNext()) {
	$arResult["PAYMENT"][$arUnit["ID"]] = $arUnit["TITLE"];
}

//save data if error
if ($ex = $APPLICATION->GetException()) {
	$arResult["TYPE_ID"] = $_REQUEST["TYPE_ID"];
	$arResult["LOT"]["TITLE"] = $_REQUEST["TITLE"];
	$arResult["LOT"]["ACTIVE"] = isset($_REQUEST["ACTIVE"]) ? "Y" : "N";
	$arResult["LOT"]["SECTION_ID"] = $_REQUEST["SECTION_ID"];
	$arResult["LOT"]["FULL_SPEC"] = isset($_REQUEST["FULL_SPEC"]) ? "Y" : "N";
	$arResult["LOT"]["NOT_ANALOG"] = isset($_REQUEST["NOT_ANALOG"]) ? "Y" : "N";
	$arResult["LOT"]["OPEN_PRICE"] = isset($_REQUEST["OPEN_PRICE"]) ? "Y" : "N";
	$arResult["LOT"]["COMPANY_ID"] = $_REQUEST["COMPANY_ID"];
	$arResult["LOT"]["RESPONSIBLE_FIO"] = $_REQUEST["RESPONSIBLE_FIO"];
	$arResult["LOT"]["RESPONSIBLE_PHONE"] = $_REQUEST["RESPONSIBLE_PHONE"];
	$arResult["LOT"]["DATE_START"] = $_REQUEST["DATE_START"];
	$arResult["LOT"]["DATE_END"] = $_REQUEST["DATE_END"];
	$arResult["LOT"]["TIME_EXTENSION"] = $_REQUEST["TIME_EXTENSION"];
	$arResult["LOT"]["TIME_UPDATE"] = $_REQUEST["TIME_UPDATE"];
	$arResult["LOT"]["DATE_DELIVERY"] = $_REQUEST["DATE_DELIVERY"];
	$arResult["LOT"]["NOTE"] = $_REQUEST["NOTE"];
	$arResult["LOT"]["TERM_DELIVERY_ID"] = $_REQUEST["TERM_DELIVERY_ID"];
	$arResult["LOT"]["TERM_DELIVERY_VAL"] = $_REQUEST["TERM_DELIVERY_VAL"];
	$arResult["LOT"]["TERM_DELIVERY_REQUIRED"] = isset($_REQUEST["TERM_DELIVERY_REQUIRED"]) ? "Y" : "N";
	$arResult["LOT"]["TERM_DELIVERY_EDIT"] = isset($_REQUEST["TERM_DELIVERY_EDIT"]) ? "Y" : "N";
	$arResult["LOT"]["TERM_PAYMENT_ID"] = $_REQUEST["TERM_PAYMENT_ID"];
	$arResult["LOT"]["TERM_PAYMENT_VAL"] = $_REQUEST["TERM_PAYMENT_VAL"];
	$arResult["LOT"]["TERM_PAYMENT_REQUIRED"] = isset($_REQUEST["TERM_PAYMENT_REQUIRED"]) ? "Y" : "N";
	$arResult["LOT"]["TERM_PAYMENT_EDIT"] = isset($_REQUEST["TERM_PAYMENT_EDIT"]) ? "Y" : "N";
	$arResult["LOT"]["PRIVATE"] = isset($_REQUEST["PRIVATE"]) ? "Y" : "N";
	$arResult["LOT"]["WITH_NDS"] = $_REQUEST["WITH_NDS"] == "Y" ? "Y" : "N";
	$arResult["LOT"]["CURRENCY"] = $_REQUEST["CURRENCY"];
	$arResult["LOT"]["QUOTES"] = isset($_REQUEST["QUOTES"]) == "Y" ? "Y" : "N";
	$arResult["LOT"]["NOSAME"] = isset($_REQUEST["NOSAME"]) ? "Y" : "N";
	$arResult["LOT"]["NOBAD"] = isset($_REQUEST["NOBAD"]) == "Y" ? "Y" : "N";
	$arResult["LOT"]["NOTVISIBLE_PROPOSAL"] = isset($_REQUEST["NOTVISIBLE_PROPOSAL"]) ? "Y" : "N";
	$arResult["LOT"]["SEND_SPEC"] = isset($_REQUEST["SEND_SPEC"]) == "Y" ? "Y" : "N";
	$arResult["LOT"]["SUBSCR_NOW"] = isset($_REQUEST["SUBSCR_NOW"]) == "Y" ? "Y" : "N";
	$arResult["LOT"]["SUBSCR_START"] = intval($_REQUEST["SUBSCR_START"]);
	$arResult["LOT"]["SUBSCR_END"] = intval($_REQUEST["SUBSCR_END"]);
	$arResult["LOT"]["VIZ_HIST"] = isset($_REQUEST["VIZ_HIST"]) == "Y" ? "Y" : "N";
	$arResult["LOT"]["ONLY_BEST"] = isset($_REQUEST["ONLY_BEST"]) == "Y" ? "Y" : "N";
	$arResult["LOT"]["NOEDIT"] = isset($_REQUEST["NOEDIT"]) == "Y" ? "Y" : "N";
	$arResult["LOT"]["PRE_PROPOSAL"] = isset($_REQUEST["PRE_PROPOSAL"]) == "Y" ? "Y" : "N";

	//private lot -->
	if ($arResult["LOT"]["PRIVATE"] == "Y") {
		unset($arResult["LOT"]["PRIVATE_USER"]);
		unset($arResult["LOT"]["PRIVATE_LIST"]);
		$rsSupplier = CTenderixUserSupplier::GetListUser(array("NAME_COMPANY" => "ASC"), array());
		while ($arSupplier = $rsSupplier->Fetch()) {
			$arResult["LOT"]["PRIVATE_USER"][] = array(
				"company" => $arSupplier["NAME_COMPANY"],
				"id" => $arSupplier["USER_ID"]
			);
			if (in_array($arSupplier["USER_ID"], $_REQUEST["PRIVATE_LIST"])) {
				$arResult["LOT"]["PRIVATE_LIST"][] = array(
					"company" => $arSupplier["NAME_COMPANY"],
					"id" => $arSupplier["USER_ID"]
				);
			}
		}
	}
	//private lot <--

	if ($arResult["TYPE_ID"] == "S" || $arResult["TYPE_ID"] == "R") {
		$prod_arr = $_REQUEST["PRODUCTS_ID"];
		$prod_arr2 = array();
		ksort($prod_arr);
		foreach ($prod_arr as $prod_val) {
			if ($prod_val > 0)
				$prod_arr2[] = $prod_val;
		}
		$arResult["PRODUCTS_ID"] = $prod_arr2;

		foreach ($arResult["PRODUCTS_ID"] as $k => $id_prod) {
			$arResult["LOT"]["TOVAR"][$id_prod]["START_PRICE"][$k] = $_REQUEST["START_PRICE"][$k][$id_prod];
			$arResult["LOT"]["TOVAR"][$id_prod]["STEP_PRICE"][$k] = $_REQUEST["STEP_PRICE"][$k][$id_prod];
			$arResult["LOT"]["TOVAR"][$id_prod]["COUNT"][$k] = $_REQUEST["COUNT"][$k][$id_prod];
			$arResult["LOT"]["TOVAR"][$id_prod]["COUNT_EDIT"][$k] = ($_REQUEST["COUNT_EDIT"][$k][$id_prod] == "Y" ? "Y" : "N");
		}

		/*$arResult["LOT"]["TOVAR"]["START_PRICE"] = $_REQUEST["START_PRICE"];
		$arResult["LOT"]["TOVAR"]["STEP_PRICE"] = $_REQUEST["STEP_PRICE"];
		$arResult["LOT"]["TOVAR"]["COUNT"] = $_REQUEST["COUNT"];
		$arResult["LOT"]["TOVAR"]["COUNT_EDIT"] = ($_REQUEST["COUNT_EDIT"] == "Y" ? "Y" : "N");*/

		if ($ID > 0) {
			$rsProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $PRODUCTS_ID), $is_filtered);
			$rsProdBuyer = CTenderixProducts::GetListBuyer($arFilter = Array("LOT_ID" => $ID));
			$arProdBuyer = $rsProdBuyer->Fetch();
			while ($arProps = $rsProps->Fetch()) {
				$rsPropsBuyer = CTenderixProductsProperty::GetListBuyer(array("PRODUCTS_ID" => $arProdBuyer["ID"], "PRODUCTS_PROPERTY_ID" => $arProps["ID"]));
				$arPropsBuyer = $rsPropsBuyer->Fetch();

				$arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["ID"] = $arPropsBuyer["ID"];
				$arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["VALUE"] = $_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_VALUE"];
				$arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["REQUIRED"] = ($_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_REQUIRED"] == "Y" ? "Y" : "N");
				$arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["EDIT"] = ($_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_EDIT"] == "Y" ? "Y" : "N");
				$arResult["LOT"]["TOVAR"]["PROP"][$arProps["ID"]]["VISIBLE"] = ($_REQUEST["PROP_PROD_" . $arPropsBuyer["ID"] . "_VISIBLE"] == "Y" ? "Y" : "N");
			}
		} else {
			foreach ($arResult["PRODUCTS_ID"] as $k => $id_prod) {
				$rsProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $id_prod), $is_filtered);
				while ($arProps = $rsProps->Fetch()) {
					$arResult["LOT"]["TOVAR"][$id_prod]["PROP"][$arProps["ID"]]["ID"][$k] = $arProps["ID"];
					$arResult["LOT"]["TOVAR"][$id_prod]["PROP"][$arProps["ID"]]["VALUE"][$k] = $_REQUEST["PROP_PROD_" . $arProps["ID"] . "_VALUE"][$k][$id_prod];
					$arResult["LOT"]["TOVAR"][$id_prod]["PROP"][$arProps["ID"]]["REQUIRED"][$k] = ($_REQUEST["PROP_PROD_" . $arProps["ID"] . "_REQUIRED"][$k][$id_prod] == "Y" ? "Y" : "N");
					$arResult["LOT"]["TOVAR"][$id_prod]["PROP"][$arProps["ID"]]["EDIT"][$k] = ($_REQUEST["PROP_PROD_" . $arProps["ID"] . "_EDIT"][$k][$id_prod] == "Y" ? "Y" : "N");
					$arResult["LOT"]["TOVAR"][$id_prod]["PROP"][$arProps["ID"]]["VISIBLE"][$k] = ($_REQUEST["PROP_PROD_" . $arProps["ID"] . "_VISIBLE"][$k][$id_prod] == "Y" ? "Y" : "N");
				}
			}
		}
	}
	if ($arResult["TYPE_ID"] != "S" && $arResult["TYPE_ID"] != "R") {
		for ($i = 0; $i < $_REQUEST["newProp"] + 1; $i++) {
			if (strlen($_REQUEST["PROP_n" . $i . "_TITLE"]) <= 0 //||
				// strlen($_REQUEST["PROP_n" . $i . "_COUNT"]) <= 0 ||
				// strlen($_REQUEST["PROP_n" . $i . "_UNIT_ID"]) <= 0
			)
				continue;
			$arResult["LOT"]["SPEC"]["n" . $i]["TITLE"] = $_REQUEST["PROP_n" . $i . "_TITLE"];
			$arResult["LOT"]["SPEC"]["n" . $i]["ADD_INFO"] = $_REQUEST["PROP_n" . $i . "_ADD_INFO"];
			$arResult["LOT"]["SPEC"]["n" . $i]["COUNT"] = (strlen($_REQUEST["PROP_n" . $i . "_COUNT"]) <= 0 ? "0" : $_REQUEST["PROP_n" . $i . "_COUNT"]);
			$arResult["LOT"]["SPEC"]["n" . $i]["UNIT_ID"] = $_REQUEST["PROP_n" . $i . "_UNIT_ID"];
			$arResult["LOT"]["SPEC"]["n" . $i]["START_PRICE"] = $_REQUEST["PROP_n" . $i . "_START_PRICE"];
			$arResult["LOT"]["SPEC"]["n" . $i]["STEP_PRICE"] = $_REQUEST["PROP_n" . $i . "_STEP_PRICE"];
			$arResult["LOT"]["SPEC"]["n" . $i]["PROP_ID"] = "n" . $i;
		}
		$arResult["SPEC_NEW_PROP"] = $_REQUEST["newProp"];
	}
}
//print_r($arResult);

$this->IncludeComponentTemplate();
?>