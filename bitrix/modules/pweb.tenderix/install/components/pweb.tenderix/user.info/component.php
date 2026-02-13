<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

global $CACHE_MANAGER, $USER;

if (!CModule::IncludeModule("pweb.tenderix")) {
    $this->AbortResultCache();
    ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
    return;
}

if (!$USER->IsAuthorized()) {
    $arResult["ERROR_AUTH"] = "Y";
}

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
$S_RIGHT = CTenderixUserSupplierStatus::GetStatusRight();

$arResult["T_RIGHT"] = $T_RIGHT;
$arResult["S_RIGHT"] = $S_RIGHT;

if ($S_RIGHT == "D" && $T_RIGHT == "D" && $USER->IsAuthorized()) {
    $arResult["ERROR_PROFILE_SUPPLIER"] = "Y";
}

if ($T_RIGHT == "P") {
    if ($S_RIGHT < "W")
        $arResult["ERROR_SUPPLIER"] = "Y";
}

if (!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 3600000;

$user_id = $USER->GetID();

$CACHE_ID = array("USER_INFO", $USER->GetGroups(), $user_id, $S_RIGHT, $T_RIGHT);
$CACHE_PATH = str_replace(array(":", "//"), "/", "/" . SITE_ID . "/pweb.tenderix/user.info/" . $user_id);

if ($this->StartResultCache(false, $CACHE_ID, $CACHE_PATH)) {

    if ($T_RIGHT == "P") {
        $arResult["SUPPLIER_STATUS"] = CTenderixUserSupplierStatus::GetStatusUser();
        $arResult["PROPOSAL_CNT"] = CTenderixProposal::GetCountProposal($user_id);
        $arResult["PROPOSAL_WIN_CNT"] = CTenderixLot::GetCountProposalWin($user_id);
    }
    
    if ($T_RIGHT == "S" || $T_RIGHT == "W") {
        $arResult["LOT_CNT"] = CTenderixLot::GetCountLotUser($user_id);
        $rsBUYER_INFO = CTenderixUserBuyer::GetByID($user_id);
        $arResult["BUYER_INFO"] = $rsBUYER_INFO->Fetch();
    }

    $rsUser = CUser::GetByID($user_id);
    $arUser = $rsUser->Fetch();
    $arResult["USER_INFO"] = $arUser;

    $CACHE_MANAGER->StartTagCache($this->GetCachePath());
    $CACHE_MANAGER->RegisterTag('pweb.tenderix_user.info_' . $user_id);
    $CACHE_MANAGER->EndTagCache();

    $this->IncludeComponentTemplate();
}
?>
