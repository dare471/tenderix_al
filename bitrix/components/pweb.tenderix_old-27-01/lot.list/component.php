	<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $CACHE_MANAGER;

if (!CModule::IncludeModule("pweb.tenderix")) {
	$this->AbortResultCache();
	ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
	return;
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/pweb.tenderix/list.suppliers/class.php");

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
$S_RIGHT = CTenderixUserSupplierStatus::GetStatusRight();

if ($S_RIGHT == "D" && $T_RIGHT == "P" /*|| $S_RIGHT == "A"*/) {
	ShowError(GetMessage("ACCESS_DENIED"));
	return;
}

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if (!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600000;

$arParams["LOTS_COUNT"] = intval($arParams["LOTS_COUNT"]);
if ($arParams["LOTS_COUNT"] <= 0)
	$arParams["LOTS_COUNT"] = 20;

$arParams["DETAIL_URL"] = trim($arParams["DETAIL_URL"]);
if (strlen($arParams["DETAIL_URL"]) <= 0)
	$arParams["DETAIL_URL"] = "/tenders_detail.php?LOT_ID=#LOT_ID#";

$arParams["PROPOSAL_URL"] = trim($arParams["PROPOSAL_URL"]);
if (strlen($arParams["PROPOSAL_URL"]) <= 0)
	$arParams["PROPOSAL_URL"] = "/add_proposal.php?LOT_ID=#LOT_ID#";


$arParams["SORT_BY"] = (isset($arParams["SORT_BY"]) ? trim($arParams["SORT_BY"]) : "ID");
$arParams["SORT_ORDER"] = (isset($arParams["SORT_ORDER"]) ? trim($arParams["SORT_ORDER"]) : "ASC");

//Set Title
$arParams["SET_TITLE"] = ($arParams["SET_TITLE"] == "N" ? "N" : "Y" );
if ($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle(GetMessage("PW_TD_LOT_LIST"));

$arParams["ACTIVE_DATE"] = ($arParams["ACTIVE_DATE"] == "N" ? "N" : "Y" );
if ($T_RIGHT >= "S") {
	$arParams["ACTIVE_DATE"] = "N";
}

$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"] != "N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"] != "N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"] == "Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"] !== "N";$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"] == "Y";


//$arParams["LOTS_COUNT"] = 200;

if ($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"]) {
	$arNavParams = array(
		"nPageSize" => $arParams["LOTS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
		"bShowAll" => $arParams["PAGER_SHOW_ALL"],
	);
	$arNavigation = CDBResult::GetNavParams($arNavParams);

	if ($arNavigation["PAGEN"] == 0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] > 0)
		$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
}
else {
	$arNavParams = array(
		"nTopCount" => $arParams["LOTS_COUNT"],
		"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	);
	$arNavigation = false;
}

if (strlen($arParams["FILTER_NAME"]) <= 0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
	$arParams["FILTER_NAME"] = "arrFilterLot";
$FILTER_NAME = $arParams["FILTER_NAME"];

global $$FILTER_NAME;
$arFilter = ${$FILTER_NAME};

// if ($T_RIGHT == "S") {
//     $arFilter["USER"] = "Y";
//  }



//$arFilter["ACTIVE_DATE"] = isset($arFilter["ARCHIVE_LOT"]) ? "" : $arParams["ACTIVE_DATE"];

//if($T_RIGHT != 'P' && !isset($arFilter["USER"])){
//   $arFilter["ACTIVE"] = "Y"; // Убрал это чтобы показывал все лоты, в том числе и неактивные.
//  $arFilter[""] = "";
//} else {
//  $arFilter["ARCHIVE_LOT"] = "Y";
//}

  
$yesterday  = date("d.m.Y H:i:s", mktime(date("H"), date("i"), date("s"), date("m")  , date("d")-1, date("Y")));
if (count($arFilter) <= 2) {$arFilter["DATE_END2"] = $yesterday;}

//echo "<br />" . $yesterday;

// получение разделов, доступных пользователю - начало vvvvvvvvvv
$sec_filtr = array();
$rsSection = CTenderixSection::GetList($by = "s_c_sort", $order = "asc", array("ACTIVE" => "Y"), $is_filtered = false);
while ($arSection = $rsSection->Fetch()) {
	if ($arSection["GROUP_ARR"] != "") {$sec_filtr["SECTION_GROUP"][$arSection["ID"]] = explode(",", $arSection["GROUP_ARR"]);}
	else {$sec_filtr["SECTION_GROUP"][$arSection["ID"]] = array();}
}
$user_get_id = $USER->GetID();
$group_users = CUser::GetUserGroup($user_get_id);
$mas_sections = array();
foreach ($group_users as $usgr_key => $usgr_val) {
	foreach ($sec_filtr["SECTION_GROUP"] as $sec_key => $sec_val) {
		if (!empty($sec_val)) {
			if (in_array($usgr_val, $sec_val)) $mas_sections[] = $sec_key;
		} else {
			$mas_sections[] = $sec_key;
		}
	}
}
$mas_sections = array_unique($mas_sections);
$arFilter["SECTION_ARR"] = $mas_sections;
//echo "<pre>"; print_r($arFilter);echo "</pre>";
// получение разделов, доступных пользователю - конец ^^^^^^^^^^^^

$by = isset($_REQUEST["SORT_BY"]) ? $_REQUEST["SORT_BY"] : $arParams["SORT_BY"];
$order = isset($_REQUEST["SORT_ORDER"]) ? $_REQUEST["SORT_ORDER"] : $arParams["SORT_ORDER"];

$CACHE_ID = array($USER->GetGroups(), $arFilter, $by, $order, $USER->GetID(), $arNavigation);

$arResult = Array(
	"LOTS" => Array(),
	"NAV_STRING" => "",
	"NAV_RESULT" => null,
	"SUPPLIER_STATUS" => CAllTenderUserSupplierStatus::GetStatusUser()
);
/////////////////
    //echo "1";die();

$rsUsers = CUser::GetList(($by="ID"), ($order="desc"), array('GROUPS_ID' => array(1,6,8)));
while ($arUser = $rsUsers->Fetch()) 
	$arResult['BUYERS'][$arUser['ID']] = $arUser;
////////////////

$CURR_URL = $APPLICATION->GetCurPageParam("", array("SORT_BY", "SORT_ORDER"));
$arResult["CURR_URL"] = strstr($CURR_URL, "?") ? $CURR_URL . "&" : $CURR_URL . "?";

if ($this->StartResultCache(false, $CACHE_ID)) {
	if(!CModule::IncludeModule("pweb.tenderix")) {
		$this->AbortResultCache();
		ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
		return;
	}
	$arFilter["NOEDIT"] = "";
	$arFilter["TYPE_ID"] = $arFilter["TYPE"];
	$arResult["T_RIGHT"] = $T_RIGHT;
	$arResult["S_RIGHT"] = $S_RIGHT;

	if(!$USER->IsAuthorized()) {
		$arFilter['ACTIVE'] = 'Y';
	}
	/*elseif($USER->IsAuthorized() && isset($arFilter['USER']) && $arFilter['USER'] == 'Y'){
		$arFilter['BUYER_ID'] = $USER->GetID();
		$arFilter['ACTIVE'] = 'N';
	}*/
	elseif($USER->IsAuthorized()) {
		if($T_RIGHT != 'P') {
			if(isset($arFilter['USER']) && $arFilter['USER'] == 'Y') {
				$arFilter['ACTIVE'] = 'N';
				//$arFilter['BUYER_ID'] = '';
			} else {
				$arFilter['ACTIVE'] = '';
			}
			if(isset($arFilter['ARCHIVE_LOT']) && $arFilter['ARCHIVE_LOT'] == 'Y') {
				$arFilter['ARCHIVE'] = 'Y';
			} else {
				$arFilter['ARCHIVE'] = '';
			}
		} else {
			if($T_RIGHT == 'P') {
				$arFilter['ACTIVE'] = 'Y';
				$arFilter['ARCHIVE'] = '';
			} else {
				if (isset($arFilter['ARCHIVE_LOT']) && $arFilter['ARCHIVE_LOT'] == 'Y') {
					$arFilter['ARCHIVE'] = 'Y';
				} else {
					$arFilter['ARCHIVE'] = 'N';
				}
			}
		}
	}

	/*if($T_RIGHT == "S" && $arLots['BUYER_ID'] != $USER->GetID())
		$arFilter['BUYER_ID'] = $USER->GetID();
*/
	//if(isset($arFilter['ARCHIVE_LOT']) && $arFilter['ARCHIVE_LOT'] == 'Y')
		//$arFilter['ARCHIVE'] = 'Y';
		
	/*if ($T_RIGHT == 'W' && isset($arFilter['USER']) && $arFilter['USER'] == 'Y' ){
		//unset($arFilter);
		$arFilter['ACTIVE'] = 'N';	
	}*/
		// $arFilter['ARCHIVE'] = 'N';
		// $arFilter['ARCHIVE_LOT'] = "N";
	/*if($arFilter['ARCHIVE_LOT'] == "Y"){
		$arFilter['ARCHIVE'] = 'Y';
	}else {
		$arFilter['ARCHIVE'] = 'N';
	}*/
	
	// if($arResult["T_RIGHT"] == 'P') {
	// 	$arFilter['PRIVATE'] = 'N';
	// }

	$res = CTenderixLot::GetList($by, $order, $arFilter);
	$res->NavStart($arNavParams);
	$arResult["NAV_STRING"] = $res->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
	$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
	$arResult["NAV_RESULT"] = $res;

	if($T_RIGHT == "P") {
		$URL = $arParams["PROPOSAL_URL"];
	}else{
		$URL = $arParams["DETAIL_URL"];
	}

	$is_archive = $arFilter['ARCHIVE_LOT'];

	while ($arLots = $res->GetNext()) {
		
		$arLots["DETAIL_URL"] = CComponentEngine::MakePathFromTemplate(
			$URL, Array("LOT_ID" => $arLots["ID"])
		);
		$timeZone = time() + CTimeZone::GetOffset();
		//Ярослав правка 04.10.2017
		// $time_end = strtotime($arLots["DATE_END"]) + intval($arLots["TIME_EXTENSION"]); //Оригинал
		$time_end = strtotime($arLots["DATE_END"]); //Моя правка
		//__($arLots);
		$pUser = CTenderixLot::GetUserPrivateLot($arLots['ID']);
		while($apUser = $pUser->GetNext()){
			$arLots['private_user'][] = $apUser;
		}

		//Запрос доступа к лоту
		/*if($USER->IsAuthorized()) {
			$lotAccess = CListSupplierClass::selectRequestProposal($arLots['ID'], $USER->GetID(), 'requestAddProposal');

			$arLots["ACCESS"] = $lotAccess[0]["ACCESS"];
		}*/

		// Если время истекло то становиться архивным. Убрал.
		// if($time_end < $timeZone)
		// 	$arLots["ARCHIVE"] = "Y";
		// Усли время вышло. то он завершенный
		if($time_end < $timeZone) {
			$arLots["END_LOT"] = "Y";
			//CTenderixLot::deleteFromLotAccess($arLots['ID']);
		}
		
		/* Сумма */
		
		$rsProp = CTenderixLotSpec::GetListProp($arLots["ID"]);
		while ($arProp = $rsProp->Fetch()) {
			$arLots["PROPERTY_SPEC"][] = $arProp;
		}
		
		$arLots["TOTAL_SUM"] = 0;
		
		for ($i = 0; $i < count($arLots["PROPERTY_SPEC"]); $i++) {
			$arLots["TOTAL_SUM"] += $arLots["PROPERTY_SPEC"][$i]['START_PRICE']*$arLots["PROPERTY_SPEC"][$i]['COUNT'];
		}
		//
		
		if ($T_RIGHT == "W" || ($T_RIGHT == "S" && $arLots["BUYER_ID"] == $USER->GetID()))
			$arLots["PROPOSAL"] = intval($arLots["PROPOSAL"]);
		else
			unset($arLots["PROPOSAL"]);

		///////
		////////
		//echo $arLots["ID"] . "<br />";
		$arFilter3 = array(
			"LOT_ID" => $arLots["ID"]
		);
		if (count($arLots) > 0) {
			//$arResult["TYPE_ID"] = $arLots["TYPE_ID"];
			
			//$arResult["OWNER"] = ($arLot["BUYER_ID"] == $USER->GetID() || $T_RIGHT == "W") ? "Y" : "N";
			//$arResult["RIGHT"] = $T_RIGHT;
			//$time_end = strtotime($arLot["DATE_END"]) + intval($arLot["TIME_EXTENSION"]);
			//$arResult["LOT_END"] = "N";

			$arResult3 = array();
			//if ($arLots["NOTVISIBLE_PROPOSAL"] == "N") {
				$rsProposal = CTenderixProposal::GetList($arFilter3);
				while ($arProposal = $rsProposal->GetNext()) {
					$arResult3["PROPOSAL"][$arProposal["ID"]] = $arProposal;

					//dop property 
					$arResult3["PROP_PROPOSAL"][$arProposal["ID"]] = CTenderixProposal::GetProperty($arProposal["ID"]);
					//dop property

					$rsFile = CTenderixProposal::GetFileList($arProposal["ID"]);

					$rsUser = CTenderixUserSupplier::GetByID($arProposal["USER_ID"]);
					$arUser = $rsUser->Fetch();
					$arUser["LOGO_SMALL"] = CFile::GetPath($arUser["LOGO_SMALL"]);
					$arUser["LOGO_BIG"] = CFile::GetPath($arUser["LOGO_BIG"]);
					$arResult3["PROPOSAL"][$arProposal["ID"]]["USER_INFO"] = $arUser;

					$rsPropList = CTenderixUserSupplierProperty::GetList($by = "SORT", $order = "asc");
					if ($arProposal["USER_ID"] > 0) {
						$arResult3["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP_SUPPLIER"] = CTenderixUserSupplier::GetProperty($arProposal["USER_ID"]);
					}
					while ($arPropList = $rsPropList->GetNext()) {
						if ($arPropList["ACTIVE"] == "N")
							continue;

						//$arPropList["IS_REQUIRED"] = in_array("PROP_" . $arPropList["ID"], $arParams["REG_FIELDS_REQUIRED"]) ? "Y" : "N";
						$arResult3["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP"][$arPropList["ID"]] = $arPropList;
						//$arResult3["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP"][$arPropList["ID"]] = $arPropList;
					}

					if ($arLots["TYPE_ID"] != "S" && $arLots["TYPE_ID"] != "R") {
						$rsProposalSpec = CTenderixProposal::GetListSpec(array("PROPOSAL_ID" => $arProposal["ID"]));
						while ($arProposalSpec = $rsProposalSpec->GetNext()) {
							$arResult3["PROPOSAL"][$arProposal["ID"]]["SPEC"][$arProposalSpec["PROPERTY_BUYER_ID"]] = $arProposalSpec;
						}
						//natsort($arResult["PROPOSAL"][$arProposal["ID"]]["SPEC"]);
					}
					if ($arLots["TYPE_ID"] == "S" || $arLots["TYPE_ID"] == "R") {
						$rsProduct = CTenderixProposal::GetListProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
						$arProduct = $rsProduct->Fetch();
						$arResult3["PROPOSAL"][$arProposal["ID"]]["PRODUCT"] = $arProduct;

						$rsProductProp = CTenderixProposal::GetListPropertyProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
						while ($arProductProp = $rsProductProp->Fetch()) {
							$arResult3["PROPOSAL"][$arProductProp["PROPOSAL_ID"]]["PROP"][$arProductProp["PRODUCTS_PROPERTY_BUYER_ID"]] = $arProductProp;
						}
					}
				}
				//print_r($arProposal);
				$arParams["NDS_TYPE"] = $arLots["WITH_NDS"];

				//$res->arResult3 = Array();
				//unset($arLot);

				$arCurr = array();
				if (CModule::IncludeModule("currency")) {
					$lcur = CCurrency::GetList(($b = "sort"), ($order1 = "asc"), LANGUAGE_ID);
					while ($lcur_res = $lcur->Fetch()) {
						$rsCur = CCurrencyRates::GetList($by = "DATE_RATE", $order = "desc", $arFilter = Array("CURRENCY" => $lcur_res["CURRENCY"]));
						$arCur = $rsCur->Fetch();
						$arCurr[$lcur_res["CURRENCY"]] = $arCur["RATE"] > 0 ? $arCur["RATE"] : 1;
					}
				}
				$itogg = array();
				$itogg_n = array();
				foreach ($arResult3["PROPOSAL"] as $idProp => $vProp) {
					$itogo = 0;
					$itogo_n = 0;
					$history = array();
					if ($arLots["TYPE_ID"] != "S" && $arLots["TYPE_ID"] != "R") {
						foreach ($vProp["SPEC"] as $idPropBuyer => $proposals) {

							//$proposals["PRICE_NDS"]= $proposals["PRICE_NDS"] / floatval($arCurr[$arResult3["LOT"]["CURRENCY"]]);
							$proposals["PRICE_NDS"]= $proposals["PRICE_NDS"] / floatval($arCurr[$arLots["CURRENCY"]]);
							
							$itogo += $proposals["PRICE_NDS"] * $proposals["COUNT"];
							if ($arParams["NDS_TYPE"] == "N") {
								$itogo_n += CTenderix::PriceNDSy($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
							} else {
								$itogo_n += CTenderix::PriceNDSn($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
							}
							$history[$idPropBuyer] = $proposals;
							//print_r($USER->GetID());
							//die();
						}
						$arResult["PROPOSAL"][$idProp]["HISTORY"] = $history;
						$arResult["MYPROPOSAL"][$idProp]["HISTORY"] = $history;
					}
					if ($arLots["TYPE_ID"] == "S" || $arLots["TYPE_ID"] == "R") {
						//$vProp["PRODUCT"]["PRICE_NDS"] = $vProp["PRODUCT"]["PRICE_NDS"] / floatval($arCurr[$arResult3["LOT"]["CURRENCY"]]);
						$vProp["PRODUCT"]["PRICE_NDS"] = $vProp["PRODUCT"]["PRICE_NDS"] / floatval($arCurr[$arLots["CURRENCY"]]);
						$itogo = $vProp["PRODUCT"]["PRICE_NDS"] * $vProp["PRODUCT"]["COUNT"];
						if ($arParams["NDS_TYPE"] == "N") {
							$itogo_n = CTenderix::PriceNDSy($vProp["PRODUCT"]["PRICE_NDS"], $vProp["PRODUCT"]["NDS"]) * $vProp["PRODUCT"]["COUNT"];
						} else {
							$itogo_n = CTenderix::PriceNDSn($vProp["PRODUCT"]["PRICE_NDS"], $vProp["PRODUCT"]["NDS"]) * $vProp["PRODUCT"]["COUNT"];
						}
					}
					//$arResult["PROPOSAL"][$idProp]["ITOGO"] = $itogo;
					$itogg[$idProp] = $itogo;
					$itogg_n[$idProp] = $itogo_n;
				}
				$arr_proposal = $arResult3["PROPOSAL"];
				unset($arResult3["PROPOSAL"]);
				unset($itogo);
				unset($itogo_n);
				/*if ($arParams["SORT_ITOGO"] == "asc") {
					asort($itogg, SORT_NUMERIC);
					asort($itogg_n, SORT_NUMERIC);
				} elseif ($arParams["SORT_ITOGO"] == "desc") {*/
					arsort($itogg, SORT_NUMERIC);
					arsort($itogg_n, SORT_NUMERIC);
			   // }
			   $tseni = array();
				/*echo "<pre>";
				print_r($arr_proposal);
				echo "</pre>";*/
				foreach ($itogg as $idProp => $itogo) {
					$arResult3["PROPOSAL"][$idProp] = $arr_proposal[$idProp];
					//$arResult3["PROPOSAL"][$idProp]["ITOGO"] = $itogo;
					$arResult3["PROPOSAL"][$arr_proposal[$idProp]["USER_INFO"]["USER_ID"]]["ITOGO"] = $itogo;
					
						$tseni[$arLots["ID"]][$arr_proposal[$idProp]["USER_INFO"]["USER_ID"]] = $itogo;
					
				}
				foreach ($itogg_n as $idProp => $itogo_n) {
					$arResult3["PROPOSAL"][$idProp]["ITOGO_N"] = $itogo_n;
					}
				//}
		}

		/*echo "<pre>";
		print_r($tseni);
		echo "</pre>";*/
		$bestpr = 0;
		foreach ($tseni as $lot_id => $predl) {
			$predl2 = $predl;
			if ($arLots["TYPE_ID"] == "P") {
				rsort($predl);
			} else {
				sort($predl);
			}
			$bestpr = $predl[0]; //лучшая цена по лотам
			$bestpr_usid = "";
			
			foreach ($predl2 as $key => $val) {
				if ($val == $bestpr) {
					$bestpr_usid = $key; // id подавшего лучшее предложение по лоту
				}
			}
		}
		$arLots["BEST_PR"] = $bestpr;
		$arLots["BEST_ID"] = $bestpr_usid;

		$rsWin = CTenderixLot::GetListWinLot(array(), array("LOT_ID" => $arLots['ID']));
		while ($arWin = $rsWin->Fetch()) {
			$arLots["WIN"][] = $arWin["USER_ID"];
			$arLots["WIN_COMMENT"][$arWin["USER_ID"]] = $arWin["COMMENT"];
		}

		// Доступ только к закрытым лотам поставщика
		if (($arResult['S_RIGHT'] == 'W' || $arResult['S_RIGHT'] == 'A') && $arResult['T_RIGHT'] == 'P') {
			if($arLots['PRIVATE'] == 'Y'){
				foreach($arLots['private_user'] as $pUser){
					if($user_get_id = $pUser['USER_ID']){
						$arLots['P_ACCESS'] = 'Y';
					}
				}
				if($arLots['P_ACCESS'] == 'Y'){
					$arResult["LOTS"][] = $arLots;
				}
			}else {
				$arResult["LOTS"][] = $arLots;
			}
		}else {
			$arResult["LOTS"][] = $arLots;
		}

		// if ($arResult['T_RIGHT'] == "P" && $arLots['PRIVATE'] == "N") {
		// 	$arResult["LOTS"][] = $arLots;
		// }else {
			
		// }

	}

	if (count($arResult["LOTS"]) <= 0)
		$this->AbortResultCache();

	$res->arResult = Array();
	unset($arLots);

	$CACHE_MANAGER->StartTagCache($this->GetCachePath());
	$CACHE_MANAGER->RegisterTag('pweb.tenderix_list.lot');
	$CACHE_MANAGER->EndTagCache();

	$this->IncludeComponentTemplate();
}

if ($APPLICATION->GetUserRight("pweb.tenderix") == "W" || $USER->IsAdmin()) {
	$arAreaButtons = array(
		array(
			"TEXT" => GetMessage("PW_TD_LOT_ADD"),
			"TITLE" => GetMessage("PW_TD_LOT_ADD"),
			"URL" => "/bitrix/admin/tenderix_lot_edit.php?lang=" . LANGUAGE_ID,
			"ICON" => "bx-context-toolbar-create-icon",
			"ID" => "bx-context-toolbar-create-lot",
		)
	);

	$this->AddIncludeAreaIcons($arAreaButtons);
}
?>