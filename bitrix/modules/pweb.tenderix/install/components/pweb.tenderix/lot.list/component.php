<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

global $CACHE_MANAGER;

if (!CModule::IncludeModule("pweb.tenderix")) {
    $this->AbortResultCache();
    ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
    return;
}

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
$S_RIGHT = CTenderixUserSupplierStatus::GetStatusRight();

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if (!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 3600000;

$arParams["LOTS_COUNT"] = intval($arParams["LOTS_COUNT"]);
if ($arParams["LOTS_COUNT"] <= 0)
    $arParams["LOTS_COUNT"] = 20;

$arParams["DETAIL_URL"] = trim($arParams["DETAIL_URL"]);
if (strlen($arParams["DETAIL_URL"]) <= 0)
    $arParams["DETAIL_URL"] = "/tenders/tenders_detail.php?LOT_ID=#LOT_ID#";

$arParams["PROPOSAL_URL"] = trim($arParams["PROPOSAL_URL"]);
if (strlen($arParams["PROPOSAL_URL"]) <= 0)
    $arParams["PROPOSAL_URL"] = "/tenders/add_proposal.php?LOT_ID=#LOT_ID#";


$arParams["SORT_BY"] = (isset($arParams["SORT_BY"]) ? trim($arParams["SORT_BY"]) : "ID");
$arParams["SORT_ORDER"] = (isset($arParams["SORT_ORDER"]) ? trim($arParams["SORT_ORDER"]) : "ASC");

//Set Title
$arParams["SET_TITLE"] = ($arParams["SET_TITLE"] == "N" ? "N" : "Y" );
if ($arParams["SET_TITLE"] == "Y")
    $APPLICATION->SetTitle(GetMessage("PW_TD_LOT_LIST"));

$arParams["ACTIVE_DATE"] = ($arParams["ACTIVE_DATE"] == "N" ? "N" : "Y" );

$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"] == "Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"] != "N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"] != "N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"] == "Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"] !== "N";

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

$arFilter["ACTIVE"] = "Y";
$arFilter["ACTIVE_DATE"] = isset($arFilter["ARCHIVE_LOT"]) ? "" : $arParams["ACTIVE_DATE"];

$by = isset($_REQUEST["SORT_BY"]) ? $_REQUEST["SORT_BY"] : $arParams["SORT_BY"];
$order = isset($_REQUEST["SORT_ORDER"]) ? $_REQUEST["SORT_ORDER"] : $arParams["SORT_ORDER"];

$CACHE_ID = array($USER->GetGroups(), $arFilter, $by, $order, $USER->GetID(), $arNavigation);

$arResult = Array(
    "LOTS" => Array(),
    "NAV_SRTING" => "",
    "NAV_RESULT" => null,
);
$CURR_URL = $APPLICATION->GetCurPageParam("", array("SORT_BY", "SORT_ORDER"));
$arResult["CURR_URL"] = strstr($CURR_URL, "?") ? $CURR_URL . "&" : $CURR_URL . "?";

if ($this->StartResultCache(false, $CACHE_ID)) {
    if (!CModule::IncludeModule("pweb.tenderix")) {
        $this->AbortResultCache();
        ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
        return;
    }

    $arResult["T_RIGHT"] = $T_RIGHT;
    $arResult["S_RIGHT"] = $S_RIGHT;

    $res = CTenderixLot::GetList($by, $order, $arFilter);
    $res->NavStart($arNavParams);
    $arResult["NAV_STRING"] = $res->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
    $arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
    $arResult["NAV_RESULT"] = $res;

    if ($T_RIGHT == "P") {
        $URL = $arParams["PROPOSAL_URL"];
    } else {
        $URL = $arParams["DETAIL_URL"];
    }

    while ($arLots = $res->GetNext()) {
        $arLots["DETAIL_URL"] = CComponentEngine::MakePathFromTemplate(
                        $URL, Array("LOT_ID" => $arLots["ID"])
        );
        $timeZone = time() + CTimeZone::GetOffset();
        $time_end = strtotime($arLots["DATE_END"]) + intval($arLots["TIME_EXTENSION"]);
        if ($time_end < $timeZone) {
            $arLots["ARCHIVE"] = "Y";
        }
        if ($T_RIGHT == "W" || ($T_RIGHT == "S" && $arLots["BUYER_ID"] == $USER->GetID())) {
            $arLots["PROPOSAL"] = intval($arLots["PROPOSAL"]);
        } else {
            unset($arLots["PROPOSAL"]);
        }

        $arResult["LOTS"][] = $arLots;
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
