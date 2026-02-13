<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $CACHE_MANAGER;

if ($arParams["LOT_ID"] <= 0) {
	$this->AbortResultCache();
	return;
}

if (!CModule::IncludeModule("pweb.tenderix")) {
	$this->AbortResultCache();
	ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
	return;
}

$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/pweb.tenderix/colorbox2/jquery.colorbox-min.js"></script>', true);
$APPLICATION->AddHeadString('<link href="/bitrix/js/pweb.tenderix/colorbox2/colorbox.css" type="text/css" rel="stylesheet">', true);

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
// if ($T_RIGHT == "D" || $T_RIGHT == "P")
// 	return;

//add win
if (isset($_REQUEST["win_add_submit"])) {
	$arFields["WIN"] = $_REQUEST["win"];
	$arFields["LOT_ID"] = $arParams["LOT_ID"];
	$arFields["COMMENT"] = $_REQUEST["comment"];
	$arFields["MIN_PROP"] = array_sum($_REQUEST["best"]);
	CTenderixLot::WinAdd($arFields);
}

if (!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600000;

$timeZone = time() + CTimeZone::GetOffset();

$rsLot = CTenderixLot::GetByIDa($arParams["LOT_ID"]);
if ($arLot = $rsLot->GetNext()) {
	
	
	$time_end = strtotime($arLot["DATE_END"]) + intval($arLot["TIME_EXTENSION"]);
	if ($time_end < $timeZone && ($arLot["BUYER_ID"] == $USER->GetID() || $T_RIGHT == "W")) {
		$CACHE_MANAGER->ClearByTag('pweb.tenderix_proposal.list_' . $arParams["LOT_ID"]);
		
	}
	
	
	$arResult["LOT"] = $arLot;

	if($arResult['LOT']['PRIVATE'] == 'Y'){
			$arlistuser = CTenderixLot::GetUserPrivateLot($arResult['LOT']['ID']);
			/* if ($USER->IsAdmin()) {
					echo '<pre>';
					print_r($arResult['LOT']['ID']);
					echo '</pre>';
				} */
			while($listuser = $arlistuser->Fetch()){
				
				$sUser = CTenderixUserSupplier::GetList($by, $order, $arFilter = Array('ID' => $listuser['USER_ID']), $is_filtered);
				$userinfo = $sUser->Fetch();
				$arResult['PRIVATE_USER'][] = $userinfo;
			}
	}
}

$arLotAnalog = CTenderixLotSpec::GetByLotId($arResult['LOT']['ID']);
$arResult["LOT"]["NOT_ANALOG"] = $arLotAnalog["NOT_ANALOG"];

$userBind = array();
$rsUserBind = CTenderixUserBuyer::GetList($by = "", $order = "", array("ID" => $USER->GetID()), $is_filtered = false);
if ($arUserBind = $rsUserBind->Fetch()) {
	$userBind = unserialize($arUserBind["USER_BIND"]);
}
	if(isset($_REQUEST['second_step'])){
		// __($_REQUEST);
		// die();
		//$arFields["LINKED"] = $
		$arFields["ARCHIVE"] = "Y";
		$arFields["PRIVATE"] = $arResult['LOT']["PRIVATE"] == 'Y' ? "Y" : "N";
		foreach ($arResult['PRIVATE_USER'] as $value) {
			$arFields['PRIVATE_LIST'][] = $value['ID'];
		}
		$ID = $arResult["LOT"]["ID"];
		$res_lot = CTenderixLot::Update($ID, $arFields);
		LocalRedirect("/lot.php?COPY_ID=".$arResult['LOT']['ID']."&LINKED=".$arResult['LOT']['ID']."&BUYER_ID=".$arResult['LOT']['BUYER_ID']);
	}


// $CACHE_ID = array("PROPOSAL_", $USER->GetGroups(), $arParams["LOT_ID"]);
// $CACHE_PATH = str_replace(array(":", "//"), "/", "/" . SITE_ID . "/pweb.tenderix/proposal.list/" . $arParams["LOT_ID"]);

// if ($this->StartResultCache(false, $CACHE_ID, $CACHE_PATH)) {

$arFilter = array(
	"LOT_ID" => $arParams["LOT_ID"]
);

//$rsLot = CTenderixLot::GetByIDa($arParams["LOT_ID"]);
if (count($arLot) > 0) {
	$arResult["TYPE_ID"] = $arLot["TYPE_ID"];
	$arResult['END_ANALIZ'] = $arLot['END_ANALIZ'];
	$arResult['SECURITY'] = $arLot['SECURITY'];
	$arResult["OWNER"] = ($arLot["BUYER_ID"] == $USER->GetID() || $T_RIGHT == "W") ? "Y" : "N";
	$arResult["RIGHT"] = $T_RIGHT;
	$time_end = strtotime($arLot["DATE_END"]) + intval($arLot["TIME_EXTENSION"]);
	$arResult["LOT_END"] = 'N';
	//$arResult["LOT_END"] = $arLot['LOT_END'];
	if ($time_end < $timeZone) {
		$arResult["LOT_END"] = "Y";
		$rsWin = CTenderixLot::GetListWinLot(array(), array("LOT_ID" => $arParams["LOT_ID"]));
		while ($arWin = $rsWin->Fetch()) {
			$arResult["WIN"][] = $arWin["USER_ID"];
			$arResult["WIN_COMMENT"][$arWin["USER_ID"]] = $arWin["COMMENT"];
		}
	}

	if ($arLot["NOTVISIBLE_PROPOSAL"] == "N" || ($arLot["NOTVISIBLE_PROPOSAL"] == "Y" && $arResult["LOT_END"] == "Y") || $T_RIGHT == "W" || $arResult["OWNER"] == "Y") {

		//dop property
		$rsPropList = CTenderixProposalProperty::GetList($by = "SORT", $order = "desc");
		while ($arPropList = $rsPropList->GetNext()) {
			if ($arPropList["ACTIVE"] == "N")
				continue;
			if($arPropList['END_LOT'] == "Y")
				$arResult['PROP_LIST']['END_LOT'][$arPropList['ID']] = $arPropList;
			else
				$arResult['PROP_LIST']['START_LOT'][] = $arPropList;
		}

		$db_dopprop = CTenderixProposal::GetPropertyLot($arResult["LOT"]["ID"]);
			foreach($db_dopprop as $dopprop => $value){
				foreach($value as $data){
					$arResult['LOT']['PROPERTY'][$data['PROPERTY_ID']] = $data;
				}
			}

		if(isset($_REQUEST["SECURITY"])):
			$arPropDop = array("PROPERTY" => array( 2 => $_REQUEST["SECURITY"]));
			$n = 'new';
			$ID = $arResult["LOT"]["ID"];

			if (intval($ID) > 0) {
				CTenderixProposal::SetPropertyLot($ID, $arPropDop, $n);
			}
			$arFields["SECURITY"] = 'Y';
			$arFields["PRIVATE"] = $arResult['LOT']["PRIVATE"] == 'Y' ? "Y" : "N";
			foreach ($arResult['PRIVATE_USER'] as $value) {
				$arFields['PRIVATE_LIST'][] = $value['ID'];
			}
			$ID = $arResult["LOT"]["ID"];
			$res_lot = CTenderixLot::Update($ID, $arFields);
			if($res_lot > 0){
				$COMPANY = CTenderixCompany::GetByIdName($arResult['LOT']["COMPANY_ID"]);
				$rsSupplier = CUser::GetByID($arResult['LOT']['BUYER_ID']);
				$arSupplier = $rsSupplier->Fetch();

				$arEventFields = array(
					"LOT_NUM" => $arResult['LOT']['ID'],
					"LOT_NAME" => $arResult['LOT']["TITLE"],
					"COMPANY" => $COMPANY,
					"RESPONSIBLE_FIO" => $arResult['LOT']["RESPONSIBLE_FIO"],
					"RESPONSIBLE_PHONE" => $arResult['LOT']["RESPONSIBLE_PHONE"],
					"DATE_START" => $arResult['LOT']["DATE_START"],
					"DATE_END" => $arResult['LOT']["DATE_END"],
					"EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
					"EMAIL_TO" => $arSupplier['EMAIL'],
					//"NOTE" => strlen($arLot["NOTE"]) > 0 ? $arLot["NOTE"] : "-",
					"COMMENT_MAIL" => "Лот завершен! �? прошел проверку безопасности.",
					"RESPONSIBLE_EMAIL" => $arSupplier["EMAIL"],
				);
				$arrSITE = CTenderixLot::GetSite();
				CEvent::Send("TENDERIX_LOT_DONE", $arrSITE, $arEventFields, "N");
				CEvent::CheckEvents();
			}
		endif;

		//__($_REQUEST);
		if(isset($_FILES)):
			//SetPropertyLot
			$arPropDop = array("PROPERTY" => array( 7 => 'Y'), "FILES" => $_FILES['PROP']);
			$n = 'new';
			$ID = $arResult["LOT"]["ID"];

			if (intval($ID) > 0) {
				CTenderixProposal::SetPropertyLot($ID, $arPropDop, $n);
			}
		endif;	

		if($arResult['LOT']['PROPERTY'][8]['VALUE'] == 1){
			$arFields["END_ANALIZ"] = 'Y';
			$arFields["PRIVATE"] = $arResult['LOT']["PRIVATE"] == 'Y' ? "Y" : "N";
			foreach ($arResult['PRIVATE_USER'] as $value) {
				$arFields['PRIVATE_LIST'][] = $value['ID'];
			}
			$ID = $arResult["LOT"]["ID"];
			$res_lot = CTenderixLot::Update($ID, $arFields);
			if($res_lot > 0 && $T_RIGHT == 'S'){
				$COMPANY = CTenderixCompany::GetByIdName($arResult['LOT']["COMPANY_ID"]);
				$rsSupplier = CUser::GetByID($arResult['LOT']['BUYER_ID']);
				$arSupplier = $rsSupplier->Fetch();

				$arEventFields = array(
					"LOT_NUM" => $arResult['LOT']['ID'],
					"LOT_NAME" => $arResult['LOT']["TITLE"],
					"COMPANY" => $COMPANY,
					"RESPONSIBLE_FIO" => $arResult['LOT']["RESPONSIBLE_FIO"],
					"RESPONSIBLE_PHONE" => $arResult['LOT']["RESPONSIBLE_PHONE"],
					"DATE_START" => $arResult['LOT']["DATE_START"],
					"DATE_END" => $arResult['LOT']["DATE_END"],
					"EMAIL_FROM" => COption::GetOptionString("main", "email_from", "nobody@nobody.com"),
					"EMAIL_TO" => "NChuchupal@mtt.ru",
					//"EMAIL_TO" => $arSupplier['EMAIL'],
					//"NOTE" => strlen($arLot["NOTE"]) > 0 ? $arLot["NOTE"] : "-",
					"COMMENT_MAIL" => "Лот завершен! �? анализ окончен.",
					"RESPONSIBLE_EMAIL" => $arSupplier["EMAIL"],
				);
				$arrSITE = CTenderixLot::GetSite();
				CEvent::Send("TENDERIX_LOT_DONE", $arrSITE, $arEventFields, "N");
				CEvent::CheckEvents();
			}
		}elseif($arResult['LOT']['PROPERTY'][8]['VALUE'] == 0){
			$arResult['END_ANALIZ'] = 'N';
		}

		//__($_REQUEST);
		if(isset($_REQUEST['lotadd_prop'])){

			if(isset($_REQUEST["PROP"][9][n0])){
				$arPropDop = array("PROPERTY" => array( 9 => $_REQUEST["PROP"][9][n0]));
				$n = 'new';
				$ID = $arResult["LOT"]["ID"];
				if (intval($ID) > 0) {
					CTenderixProposal::SetPropertyLot($ID, $arPropDop, $n);
				}
			}
			if(isset($_REQUEST["PROP"][10][n0])){
				$arPropDop = array("PROPERTY" => array( 10 => $_REQUEST["PROP"][10][n0]));
				$n = 'new';
				$ID = $arResult["LOT"]["ID"];
				if (intval($ID) > 0) {
					CTenderixProposal::SetPropertyLot($ID, $arPropDop, $n);
				}
			}
			if(isset($_REQUEST["PROP"][8][0])){
				$arPropDop = array("PROPERTY" => array( 8 => $_REQUEST["PROP"][8][0]));
				$n = 'new';
				$ID = $arResult["LOT"]["ID"];
				if (intval($ID) > 0) {
					CTenderixProposal::SetPropertyLot($ID, $arPropDop, $n);
				}
			}
			LocalRedirect("/user/tenders_detail.php?LOT_ID=".$arResult['LOT']['ID']);
		}





		if(isset($_REQUEST['lotarch_submit'])){
			$arFields["ARCHIVE"] = isset($_REQUEST["lotarch_submit"]) ? "Y" : "N";
			$arFields["PRIVATE"] = $arResult['LOT']["PRIVATE"] == 'Y' ? "Y" : "N";
			foreach ($arResult['PRIVATE_USER'] as $value) {
				$arFields['PRIVATE_LIST'][] = $value['ID'];
			}
			$ID = $arResult["LOT"]["ID"];
			$arFields["MESSAGE_LOT"] = $_REQUEST["message_lot"];
			$res_lot = CTenderixLot::Update($ID, $arFields);
			LocalRedirect("/user/tenders_detail.php?LOT_ID=".$arResult['LOT']['ID'].'&lot_arch=Y');
		}

		if(isset($_REQUEST['fail_lot'])){
			$arFields["FAIL"] = isset($_REQUEST["fail_lot"]) ? "Y" : "N";
			$arFields["ARCHIVE"] = "Y";
			$arFields["PRIVATE"] = $arResult['LOT']["PRIVATE"] == 'Y' ? "Y" : "N";
			foreach ($arResult['PRIVATE_USER'] as $value) {
				$arFields['PRIVATE_LIST'][] = $value['ID'];
			}
			$ID = $arResult["LOT"]["ID"];
			$arFields["MESSAGE_LOT"] = $_REQUEST["message_lot"];
			$res_lot = CTenderixLot::Update($ID, $arFields);
			LocalRedirect("/user/tenders_detail.php?LOT_ID=".$arResult['LOT']['ID'].'&lot_fail=Y');
		}


		if (is_array($_REQUEST["FILE_ID_PROP"])) {
			foreach ($_REQUEST["FILE_ID_PROP"] as $file)
				CTenderixProposal::DeleteFilePropertyLot($arResult["LOT"]["ID"], $file);
		}

		//__($_REQUEST);
		//dop property
		$rsProposal = CTenderixProposal::GetList($arFilter);
		while ($arProposal = $rsProposal->GetNext()) {
			$arResult["PROPOSAL"][$arProposal["ID"]] = $arProposal;
			$arResult['PROPOSAL_USER'][$arProposal['USER_ID']] = $arProposal['USER_ID'];

			//dop property
			$arResult["PROP_PROPOSAL"][$arProposal["ID"]] = CTenderixProposal::GetProperty($arProposal["ID"]);
			//dop property

			$rsFile = CTenderixProposal::GetFileList($arProposal["ID"]);
			$arrFile = array();
			while ($arFile = $rsFile->Fetch()) {
				$arrFile[] = $arFile;
			}

			$rsUser = CTenderixUserSupplier::GetByID($arProposal["USER_ID"]);
			$arUser = $rsUser->Fetch();
			$arUser["LOGO_SMALL"] = CFile::GetPath($arUser["LOGO_SMALL"]);
			$arUser["LOGO_BIG"] = CFile::GetPath($arUser["LOGO_BIG"]);
			$arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"] = $arUser;

			$rsPropList = CTenderixUserSupplierProperty::GetList($by = "SORT", $order = "asc");
			if ($arProposal["USER_ID"] > 0) {
				$arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP_SUPPLIER"] = CTenderixUserSupplier::GetProperty($arProposal["USER_ID"]);
			}
			while ($arPropList = $rsPropList->GetNext()) {
				if ($arPropList["ACTIVE"] == "N")
					continue;

				$arPropList["IS_REQUIRED"] = in_array("PROP_" . $arPropList["ID"], $arParams["REG_FIELDS_REQUIRED"]) ? "Y" : "N";
				$arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP"][$arPropList["ID"]] = $arPropList;
				$arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP"][$arPropList["ID"]] = $arPropList;
				if ($arPropList["PROPERTY_TYPE"] == "F" && $arProposal["USER_ID"] > 0) {
					$rsFiles = CTenderixUserSupplier::GetFileListProperty($arProposal["USER_ID"], $arPropList["ID"]);
					while ($arFile = $rsFiles->GetNext()) {
						$arResult["PROPOSAL"][$arProposal["ID"]]["USER_INFO"]["PROP"][$arPropList["ID"]]["FILE"][] = $arFile;
					}
				}
			}


			$arResult["PROPOSAL"][$arProposal["ID"]]["FILE"] = $arrFile;
			if ($arLot["TYPE_ID"] != "S" && $arLot["TYPE_ID"] != "R") {
				$rsProposalSpec = CTenderixProposal::GetListSpec(array("PROPOSAL_ID" => $arProposal["ID"]));
				
				while ($arProposalSpec = $rsProposalSpec->GetNext()) {
					
					$arResult["PROPOSAL"][$arProposal["ID"]]["SPEC"][$arProposalSpec["PROPERTY_BUYER_ID"]] = $arProposalSpec;
				}
				//natsort($arResult["PROPOSAL"][$arProposal["ID"]]["SPEC"]);
			}
			if ($arLot["TYPE_ID"] == "S" || $arLot["TYPE_ID"] == "R") {
				$rsProduct = CTenderixProposal::GetListProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
				while ($arProduct = $rsProduct->Fetch()) {
					$arResult["PROPOSAL"][$arProposal["ID"]]["PRODUCT"][$arProduct["PROD_BUYER_ID"]] = $arProduct;
					$rsB = CTenderixProducts::GetListBuyer(array("ID" => $arProduct["PROD_BUYER_ID"]));
					$arB = $rsB->Fetch();
					$arProdId[] = $arB["PRODUCTS_ID"];
					$arProdId2[$arProduct["PROD_BUYER_ID"]] = $arB["PRODUCTS_ID"];
				}
				$arProdId = array_unique($arProdId);
				foreach ($arProdId as $prodId) {
					$rsB = CTenderixProducts::GetList($by = "", $order = "", array("ID" => $prodId));
					$arProd[$prodId] = $rsB->Fetch();
				}
				foreach ($arProdId2 as $prodBuyer => $prodId) {
					$arResult["PROPOSAL"][$arProposal["ID"]]["PRODUCT"][$prodBuyer]["PROD"] = $arProd[$prodId];
				}

				$ar_PRODUCTS_PROPERTY_BUYER_ID = array();
				$rsProductProp = CTenderixProposal::GetListPropertyProducts(array("PROPOSAL_ID" => $arProposal["ID"]));
				while ($arProductProp = $rsProductProp->Fetch()) {
					//$arResult["PROPOSAL"][$arProductProp["PROPOSAL_ID"]]["PROP"][$arProductProp["PRODUCTS_PROPERTY_BUYER_ID"]] = $arProductProp;
					$arPrProp[$arProductProp["PRODUCTS_PROPERTY_BUYER_ID"]] = $arProductProp;
					$ar_PRODUCTS_PROPERTY_BUYER_ID[] = $arProductProp["PRODUCTS_PROPERTY_BUYER_ID"];
				}
if($ar_PRODUCTS_PROPERTY_BUYER_ID) {
				$rsProductPropBuyer = CTenderixProductsProperty::GetListProductsPropertyBuyer2($ar_PRODUCTS_PROPERTY_BUYER_ID);
				while ($arProductPropBuyer = $rsProductPropBuyer->Fetch()) {
					$arResult["PROPOSAL"][$arProposal["ID"]]["PROP"][$arProductPropBuyer["PRODUCTS_ID"]][$arProductPropBuyer["ID"]] = $arPrProp[$arProductPropBuyer["ID"]];
				}
}
			}
		}
		//print_r($arResult["PROPOSAL"]);
		$arParams["NDS_TYPE"] = $arLot["WITH_NDS"];

		$res->arResult = Array();
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
		
		$arResult['arCurr'] = $arCurr;//09.10.2017 
		

		foreach ($arResult["PROPOSAL"] as $idProp => $vProp) {
			
			//09,10,2017 Вот говно! Получается, что предложения все равно по однму выскакивают для каждого юзера, а значит история будет говно.
			$itogo = 0;
			$itogo_n = 0;
			$hasPrices = false;
			if ($arResult["TYPE_ID"] != "S" && $arResult["TYPE_ID"] != "R") {
				foreach ($vProp["SPEC"] as $idPropBuyer => $proposals) {
					if (floatval($proposals["PRICE_NDS"]) > 0 && floatval($proposals["COUNT"]) > 0) {
						$hasPrices = true;
						$proposals["PRICE_NDS"] = $proposals["PRICE_NDS"]; // floatval($arCurr[$arResult["LOT"]["CURRENCY"]]);
						$itogo += $proposals["PRICE_NDS"] * $proposals["COUNT"];
						//__($arParams["NDS_TYPE"]);
						// Тут разобраться в параметре NDS
						if ($arParams["NDS_TYPE"] == "N") {
							$itogo_n += CTenderix::PriceNDSy($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
						} else {
							$itogo_n += CTenderix::PriceNDSn($proposals["PRICE_NDS"], $proposals["NDS"]) * $proposals["COUNT"];
						}
					}
					$history[$idPropBuyer] = $proposals;
				}
				$arResult["PROPOSAL"][$idProp]["HISTORY"] = $history;
			}
			if ($arResult["TYPE_ID"] == "S" || $arResult["TYPE_ID"] == "R") {
				foreach ($vProp["PRODUCT"] as $prodArr) {
					if (floatval($prodArr["PRICE_NDS"]) > 0 && floatval($prodArr["COUNT"]) > 0) {
						$hasPrices = true;
						$prodArr["PRICE_NDS"] = $prodArr["PRICE_NDS"] / floatval($arCurr[$arResult["LOT"]["CURRENCY"]]);
						$itogo += $prodArr["PRICE_NDS"] * $prodArr["COUNT"];
						if ($arParams["NDS_TYPE"] == "N") {
							$itogo_n += CTenderix::PriceNDSy($prodArr["PRICE_NDS"], $prodArr["NDS"]) * $prodArr["COUNT"];
						} else {
							$itogo_n += CTenderix::PriceNDSn($prodArr["PRICE_NDS"], $prodArr["NDS"]) * $prodArr["COUNT"];
						}
					}
				}
			}
			//$arResult["PROPOSAL"][$idProp]["ITOGO"] = $itogo;
			// Устанавливаем итоги только если есть заполненные цены
			if ($hasPrices && $itogo > 0) {
				$itogg[$idProp] = $itogo;
				$itogg_n[$idProp] = $itogo_n;
			} else {
				$itogg[$idProp] = 0;
				$itogg_n[$idProp] = 0;
			}
		}
		$arr_proposal = $arResult["PROPOSAL"];
		unset($arResult["PROPOSAL"]);
		unset($itogo);
		unset($itogo_n);
		if ($arParams["SORT_ITOGO"] == "asc") {
			asort($itogg, SORT_NUMERIC);
			asort($itogg_n, SORT_NUMERIC);
		} elseif ($arParams["SORT_ITOGO"] == "desc") {
			arsort($itogg, SORT_NUMERIC);
			arsort($itogg_n, SORT_NUMERIC);
		}
		foreach ($itogg as $idProp => $itogo) {
			$arResult["PROPOSAL"][$idProp] = $arr_proposal[$idProp];
			$arResult["PROPOSAL"][$idProp]["ITOGO"] = $itogo;
		}
		foreach ($itogg_n as $idProp => $itogo_n) {
			$arResult["PROPOSAL"][$idProp]["ITOGO_N"] = $itogo_n;
		}
	}

	if ($_REQUEST["exp"] == "Y") {
		$ff = file_get_contents("http://127.0.0.1:8880/pe/test.php?LOT_ID=" . $arParams["LOT_ID"]);
		file_put_contents("tt.xls", $ff);
		//print_r($arResult);
		//exit;
	}

	$r = array();
	$t = array();
	$p = array();
	foreach ($arResult["PROPOSAL"] as $id_prop => $prop) {
		foreach ($prop["HISTORY"] as $id_spec => $spec) {
			$r[$id_spec][$spec["PROPOSAL_ID"]] = $spec;
			$t[$id_spec] = array(
				"TITLE" => $spec["TITLE"],
				"START_PRICE" => $spec["START_PRICE"],
				"ADD_INFO" => $spec["ADD_INFO"],
				"COUNT" => $spec["COUNT"],
			);
			$p[$id_spec][] = $spec["PRICE_NDS"];
		}
	}
	$r["TOVAR"] = $t;
	$r["PRICE"] = $p;
	$arResult["SPEC2"] = $r;

	$arResult["ARRCUR"] = $arCurr;

	/*$CACHE_MANAGER->StartTagCache($this->GetCachePath());
	$CACHE_MANAGER->RegisterTag('pweb.tenderix_proposal.list_' . $arParams["LOT_ID"]);
	$CACHE_MANAGER->EndTagCache();*/
}
/* else {
			$this->AbortResultCache();
		}*/

$contracts = array();
foreach ($arResult["PROPOSAL"] as $pid => $usid) {
	$contracts[] = $usid["USER_ID"];
}

//if ((in_array($USER->GetID(), $contracts)) || ($arLot["BUYER_ID"] == $USER->GetID())) {
if ($arLot["BUYER_ID"] == $USER->GetID() || $T_RIGHT == "W" || in_array($arLot["BUYER_ID"], $userBind)) {
	//if ($arLot["BUYER_ID"] == $USER->GetID() || $T_RIGHT == "W" || in_array($arLot["BUYER_ID"], $userBind)) {
	$this->IncludeComponentTemplate();
}
if ($T_RIGHT == "P" || (in_array($USER->GetID(), $contracts))) {
	$this->IncludeComponentTemplate('template_p');
}
//}

?>