<?
if (! defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("pweb.tenderix"))
{
	ShowError(GetMessage("TENDERIX_MODULE_NOT_INSTALLED"));
	return;
}

$arParams["CONFIRM_UNSUBSCIBE"] = $_GET["confirm"]=="Y" ? "Y" : "N";
$arParams["CONFIRMED_URL"] = $APPLICATION->GetCurPageParam("confirm=Y", array("confirm"), false);

$arResult["ERROR"] = "";
$arResult["SUCCESS"] = "";

$arSubscribeList = Array();
$arUserList = Array();

if ($arParams["ASD_MAIL_ID"] <> "") {
	$rsUserList = CUser::GetList($by, $order, Array('email' => $arParams["ASD_MAIL_ID"]), Array());
	while ($arUser = $rsUserList->Fetch()) {
				
		$rsSubscribeList = CTenderixUserSupplier::SubscribeList($arUser["ID"]);
		
		if ( intval($rsSubscribeList->SelectedRowsCount()) > 0 ) {
			$arUserList[] = $arUser["ID"];
			
			while ($arSubscribe = $rsSubscribeList->Fetch()) {
				$arSubscribeList[] = $arSubscribe;
			}
		}
	}
}

if (count($arSubscribeList) > 0) {
	if (SubscribeHandlers::GetMailHash($arParams["ASD_MAIL_ID"]) != $arParams["ASD_MAIL_MD5"])
	{
		$arResult["ERROR"] = GetMessage("ASD_INCORRECT_HASH_L");
	}
	else
	{
		$arResult["EMAIL"] = $arParams["ASD_MAIL_ID"];
	}
}
else
{
	$arResult["ERROR"] = GetMessage("ASD_SUBSCRIBE_NOT_FOUND_L"); 
}

if ($arResult["ERROR"]=="" && $arParams["CONFIRM_UNSUBSCIBE"]=="Y")
{
	$bUnsubscribeResult = true;
	foreach ($arUserList as $id) {
		$bUnsubscribeResult &= CTenderixUserSupplier::SubscribeDelete($id);
	}
	if (!$bUnsubscribeResult) {
		$arResult["ERROR"]==GetMessage("ASD_UNSUBSRIBE_ERROR");
	}
}

$this->IncludeComponentTemplate();
?>