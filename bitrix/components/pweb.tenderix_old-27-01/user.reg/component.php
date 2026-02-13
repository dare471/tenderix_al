<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

if (!CModule::IncludeModule("pweb.tenderix")) {
    ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
    return;
}

$arResult["ID"]=intval($USER->GetID());

if($arResult["ID"]<=0)
{
	$APPLICATION->ShowAuthForm(GetMessage("ACCESS_DENIED"));
	return;
}

$strError = "";

if($_SERVER["REQUEST_METHOD"]=="POST" && (strlen($_REQUEST["save"])>0 || strlen($_REQUEST["apply"])>0) && check_bitrix_sessid())
{
	$obUser = new CUser;

	$rsUser = CUser::GetByID($arResult["ID"]);
	$arUser = $rsUser->Fetch();

	$arFields = Array(
		"NAME"					=> $_REQUEST["NAME"],
		"LAST_NAME"				=> $_REQUEST["LAST_NAME"],
		"SECOND_NAME"			=> $_REQUEST["SECOND_NAME"],
		"PERSONAL_PHONE"			=> $_REQUEST["PERSONAL_PHONE"],
		"EMAIL"					=> $_REQUEST["EMAIL"],
		//"LOGIN"					=> $_REQUEST["LOGIN"],
	);

	if($arUser)
	{
		if(strlen($arUser['EXTERNAL_AUTH_ID']) > 0)
		{
			$arFields['EXTERNAL_AUTH_ID'] = $arUser['EXTERNAL_AUTH_ID'];
		}
	}


	if(strlen($_REQUEST["NEW_PASSWORD"])>0)
	{
		$arFields["PASSWORD"]=$_REQUEST["NEW_PASSWORD"];
		$arFields["CONFIRM_PASSWORD"]=$_REQUEST["NEW_PASSWORD_CONFIRM"];
	}
	$GLOBALS["USER_FIELD_MANAGER"]->EditFormAddFields("USER", $arFields);
	if($arResult["ID"] > 0) $res = $obUser->Update($arResult["ID"], $arFields, true);

	$strError .= $obUser->LAST_ERROR;
}

$rsUser = CUser::GetByID($arResult["ID"]);
if(!$arResult["arUser"] = $rsUser->GetNext(false))
{
	$arResult["ID"]=0;
}

$arResult["ERRORS"] = $strError;
$arResult["BX_SESSION_CHECK"] = bitrix_sessid_post();


if ($arParams["SET_TITLE"] == "Y") $APPLICATION->SetTitle(GetMessage("PW_TD_PROFILE_TITLE"));

$this->IncludeComponentTemplate();
?>
