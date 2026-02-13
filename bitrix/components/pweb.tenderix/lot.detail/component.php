<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

global $CACHE_MANAGER;

if (!CModule::IncludeModule("pweb.tenderix")) {
	$this->AbortResultCache();
	ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
	return;
}

$LOT_ID = intval($_REQUEST["LOT_ID"]);

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
$S_RIGHT = CTenderixUserSupplierStatus::GetStatusRight();
//echo $T_RIGHT.$S_RIGHT;
if ($T_RIGHT == "D") {
	$T_RIGHT = 'S';
}
if ($T_RIGHT == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
if ($S_RIGHT == "D" && $T_RIGHT == "P") {
	ShowError(GetMessage("ACCESS_DENIED"));
	header('Location: /');
	return;
}
if ($T_RIGHT == "W" || ($S_RIGHT == "W" && $T_RIGHT == "P"))
	$arResult["PROPOSAL_ADD_LINK"] = true;

if (!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600000;

$arParams["LOT_ID"] = intval($arParams["~LOT_ID"]);


if(isset($_REQUEST['lot_file_concurent_submit'])) {
	$ID = $LOT_ID;

//	__($_REQUEST);
//	die();

	if($ID > 0){
		$arFields["COMMENT"] = $_REQUEST["COMMENT"];

		$arFields["PRIVATE"] = $_REQUEST["PRIVATE"];

		foreach ($_REQUEST['PRIVATE_USER'] as $key => $value) {
			$arFields['PRIVATE_LIST'][] = $value;
		}

		if (isset($_REQUEST["COMMENT"])) {
			$res_lot = CTenderixLot::Update($ID, $arFields);
		}


		if (is_array($_REQUEST["FILE_ID"])) {
			foreach ($_REQUEST["FILE_ID"] as $file)
				$del_file = CTenderixLot::DeleteFileConcurent($ID, $file);
			LocalRedirect('/tenders_detail.php?LOT_ID='.$ID.$backurl);
		}

		if (isset($_REQUEST['LOT_FILE_CONCURENT'])){
			foreach($_REQUEST['LOT_FILE_CONCURENT'] as $file) {
				$res_file = CTenderixLot::SaveFileConcurent($ID, $file);
				if (!$res_file) {
					break;
				}
			}
			LocalRedirect('/tenders_detail.php?LOT_ID='.$ID);
		}

	}
}

$arResult["T_RIGHT"] = $T_RIGHT;

$arParams["PROPOSAL_URL"] = (strlen($arParams["PROPOSAL_URL"]) > 0 ? $arParams["PROPOSAL_URL"] : "proposal.php?LOT_ID=#LOT_ID#");
$arResult["PROPOSAL_URL"] = CComponentEngine::MakePathFromTemplate($arParams["PROPOSAL_URL"], Array("LOT_ID" => $arParams["LOT_ID"]));
$arParams["LOT_URL"] = (strlen($arParams["LOT_URL"]) > 0 ? $arParams["LOT_URL"] : "lot.php?ID=#ID#");
$arResult["LOT_URL"] = CComponentEngine::MakePathFromTemplate($arParams["LOT_URL"], Array("ID" => $arParams["LOT_ID"]));

if ($T_RIGHT == "P") {
	LocalRedirect($arResult["PROPOSAL_URL"]);
}
$LOT_ID = intval($_REQUEST["LOT_ID"]);
if ($LOT_ID <= 0) {
	ShowError(GetMessage("PW_TD_LOT_NOTFOUND"));
	return;
}

$arLots = array();
$arResult["OWNER"] = "";
$arFilter = array(
	"ID" => $arParams["LOT_ID"]
);
$res = CTenderixLot::GetList($by = "", $order = "", $arFilter);
if ($arLots = $res->GetNext()) {
	$arResult["OWNER"] = "N";
	if ($arLots["BUYER_ID"] == $USER->GetID() || $T_RIGHT == "W") {
		$arResult["OWNER"] = "Y";
	} else {
		$rsUserBind = CTenderixUserBuyer::GetList($by = "", $order = "", array("ID" => $USER->GetID()), $is_filtered = false);
		if ($arUserBind = $rsUserBind->Fetch()) {
			$userBind = unserialize($arUserBind["USER_BIND"]);
			if (in_array($arLots["BUYER_ID"], $userBind)) {
				$arResult["OWNER"] = "Y";
			}
		}
	}
	$arParams["NDS_TYPE"] = $arLots["WITH_NDS"];
}

$CACHE_ID = array("LOT_DETAIL", $USER->GetGroups(), $arParams["LOT_ID"], $arResult["OWNER"]);
$CACHE_PATH = str_replace(array(":", "//"), "/", "/" . SITE_ID . "/pweb.tenderix/lot.detail/" . $arParams["LOT_ID"] . $T_RIGHT . $S_RIGHT);

if ($this->StartResultCache(false, $CACHE_ID, $CACHE_PATH)) {

	if ($arLots) {
		$timeZone = time() + CTimeZone::GetOffset();
		$time_end = strtotime($arLots["DATE_END"]) + intval($arLots["TIME_EXTENSION"]);
		if ($time_end < $timeZone) {
			//$arLots["ARCHIVE"] = "Y";
			$arLots["END_LOT"] = "Y";
			//WIN
			/* $rsWin = CTenderixLot::GetListWinLot(array(), array("LOT_ID" => $arParams["LOT_ID"]));
			while ($arWin = $rsWin->Fetch()) {
				$arResult["WIN"][] = $arWin["USER_ID"];
				$arResult["WIN_COMMENT"][$arWin["USER_ID"]] = $arWin["COMMENT"];
			} */
		}
		else {
			$arLots["END_LOT"] = "N";
		}
		$arResult["LOT"] = $arLots;

		$db_dopprop = CTenderixProposal::GetPropertyLot($arLots["ID"]);
		foreach($db_dopprop as $dopprop => $value){
			foreach($value as $data){
				$arResult["LOT"]['DOPPROP'][$data['PROPERTY_ID']] = $data;
			}
		}

		$rsFile = CTenderixLot::GetFileList($arLots["ID"]);
		$arrFile = array();
		while ($arFile = $rsFile->Fetch()) {
			$arrFile[] = $arFile;
		}
		$arResult["LOT"]["FILE"] = $arrFile;

		$rsFileConcurent = CTenderixLot::GetFileListConcurent($arLots["ID"]);
		$arrFileConcurent = array();
		while ($arFileConcurent = $rsFileConcurent->GetNext()) {
			$arrFileConcurent[] = $arFileConcurent;
		}
		$arResult["LOT"]["FILE_CONCURENT"] = $arrFileConcurent;

		if($arLots['PRIVATE'] == 'Y'){
			$arlistuser = CTenderixLot::GetUserPrivateLot($arLots["ID"]);
			while($listuser = $arlistuser->Fetch()){
				$sUser = CTenderixUserSupplier::GetList($by, $order, $arFilter = Array('ID' => $listuser['USER_ID']), $is_filtered);
				$userinfo = $sUser->Fetch();
				$arResult["LOT"]['PRIVATE_USER'][] = $userinfo;
			}
		}

		$rsPayment = CTenderixSprDetails::GetList($by = "", $order = "", $arFilter = Array("ID" => $arLots["TERM_PAYMENT_ID"]), $is_filtered);
		$arPayment = $rsPayment->Fetch();
		$arResult["PAYMENT"] = $arPayment["TITLE"];

		$rsDelivery = CTenderixSprDetails::GetList($by = "", $order = "", $arFilter = Array("ID" => $arLots["TERM_DELIVERY_ID"]), $is_filtered);
		$arDelivery = $rsDelivery->Fetch();
		$arResult["DELIVERY"] = $arDelivery["TITLE"];

		if ($arLots["TYPE_ID"] == "S" || $arLots["TYPE_ID"] == "R") {
			$rsProdBuyer = CTenderixProducts::GetListBuyer(array("LOT_ID" => $arLots["ID"]));
			while ($arProdBuyer = $rsProdBuyer->Fetch()) {
				$rsProd = CTenderixProducts::GetList($by, $order, array("ID" => $arProdBuyer["PRODUCTS_ID"]), $is_filtered);
				$arProd = $rsProd->Fetch();

				$arrPropProduct = array();
				$arrPropProductBuyer = array();
				$rsProdProps = CTenderixProductsProperty::GetList($by = "s_c_sort", $order = "asc", Array("PRODUCTS_ID" => $arProdBuyer["PRODUCTS_ID"]), $is_filtered);
				while ($arProdProps = $rsProdProps->GetNext()) {
					$rsProps2 = CTenderixProductsProperty::GetListBuyer(Array("PRODUCTS_ID" => $arProdBuyer["ID"], "PRODUCTS_PROPERTY_ID" => $arProdProps["ID"]));
					$arProps2 = $rsProps2->Fetch();
					$arrPropProduct[$arProdProps["ID"]] = $arProdProps;
					$arrPropProductBuyer[$arProps2["PRODUCTS_PROPERTY_ID"]] = $arProps2;
				}
				$arResult["PROPERTY_PRODUCT"][$arProdBuyer["ID"]] = $arrPropProduct;
				$arResult["PROPERTY_PRODUCT_BUYER"][$arProdBuyer["ID"]] = $arrPropProductBuyer;
				$arResult["PRODUCT_BUYER"][$arProdBuyer["ID"]] = $arProdBuyer;
				$arResult["PRODUCT"][$arProdBuyer["ID"]] = $arProd;
			}
		}
		if ($arLots["TYPE_ID"] != "S") {
			$arResult["SPEC"] = CTenderixLotSpec::GetByLotId($arLots["ID"]);

			$rsProp = CTenderixLotSpec::GetListProp($arLots["ID"]);
			while ($arProp = $rsProp->Fetch()) {
				$arResult["PROPERTY_SPEC"][] = $arProp;
			}
		}
	} else {
		$this->AbortResultCache();
	}

	$res->arResult = Array();
	unset($arLots);

	$CACHE_MANAGER->StartTagCache($this->GetCachePath());
	$CACHE_MANAGER->RegisterTag('pweb.tenderix_lot.detail_' . $arParams["LOT_ID"]);
	$CACHE_MANAGER->EndTagCache();

	$this->IncludeComponentTemplate();
}
?>
