<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?><?

global $CACHE_MANAGER;
if (!CModule::IncludeModule("pweb.tenderix")) {
    ShowError(GetMessage("PW_TD_MODULE_NOT_INSTALLED"));
    return;
}

// if ($arParams["JQUERY"] == "Y") {
//     $APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/pweb.tenderix/jquery.js"></script>', true);
// }

$arResult["USER_ID"] = intval($USER->GetID());

$T_RIGHT = $GLOBALS["APPLICATION"]->GetGroupRight("pweb.tenderix");
if ($T_RIGHT < "S") {
    ShowError(GetMessage("ACCESS_DENIED"));
    return;
}

$arResult["T_RIGHT"] = $T_RIGHT;

$format = $arParams["FORMAT"];

switch ($arParams["UNIT"]) {
    case "r":
        $unit = 1;
        $arResult["UNIT_NAME"] = GetMessage("PW_TD_R");
        break;
    case "tr":
        $unit = 1000;
        $arResult["UNIT_NAME"] = GetMessage("PW_TD_TR");
        break;
    case "mr":
        $unit = 1000000;
        $arResult["UNIT_NAME"] = GetMessage("PW_TD_MR");
        break;
}
if ($arParams["SET_TITLE"] == "Y")
    $APPLICATION->SetTitle(GetMessage("PW_TD_STATISTIC_TITLE"));
if (!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 86400;


$companyUser = 0;
if ($arParams["COMPANY_ONLY"] == "Y") {
    $rsUser = CTenderixUserBuyer::GetByID($USER->GetID());
    $arUser = $rsUser->Fetch();
    $companyUser = $arUser["COMPANY_ID"];
}

$CACHE_ID = array("STATISTIC_LOTS", $companyUser);
$CACHE_PATH = str_replace(array(":", "//"), "/", "/" . SITE_ID . "/pweb.tenderix/statistic");

//if ($this->StartResultCache(false, $CACHE_ID, $CACHE_PATH)) {
	
    $countLotStatistic = CTenderixStatistic::Count();
    $countLot = CTenderixLot::Count();
    if ($countLotStatistic != $countLot) {
        CTenderixStatistic::UpdateAll();
    }


    $arFilter = array();
    if ($companyUser > 0) {
        $arFilter = array("COMPANY_ID" => $companyUser);
    }
    $rsCompany = CTenderixCompany::GetList($by = "", $order = "", $arFilter = Array(), $is_filtered = false);
    while ($arCompany = $rsCompany->Fetch()) {
        $COMPANY_NAME[$arCompany["ID"]] = $arCompany["TITLE"];
    }
    $rsSection = CTenderixSection::GetList($by = "", $order = "", $arFilter = Array(), $is_filtered = false);
    while ($arSection = $rsSection->Fetch()) {
        $SECTION_NAME[$arSection["ID"]] = $arSection["TITLE"];
    }
    $MONTH_NAME = array(
        "1" => GetMessage("PW_TD_MONTH1"),
        "2" => GetMessage("PW_TD_MONTH2"),
        "3" => GetMessage("PW_TD_MONTH3"),
        "4" => GetMessage("PW_TD_MONTH4"),
        "5" => GetMessage("PW_TD_MONTH5"),
        "6" => GetMessage("PW_TD_MONTH6"),
        "7" => GetMessage("PW_TD_MONTH7"),
        "8" => GetMessage("PW_TD_MONTH8"),
        "9" => GetMessage("PW_TD_MONTH9"),
        "10" => GetMessage("PW_TD_MONTH10"),
        "11" => GetMessage("PW_TD_MONTH11"),
        "12" => GetMessage("PW_TD_MONTH12"),
    );


    $years_reg = CTenderixStatistic::YearsRegisterUser();

    foreach($years_reg as $k => $vy) {
        $countUsersNew[$vy] = CTenderixStatistic::UsersCount($vy, "year_new");
        $countUsersAll[$vy] = CTenderixStatistic::UsersCount($vy, "year_all");

        $countLotsAll[$vy] = CTenderixStatistic::LotsCount($vy, "year_all", 12, $_REQUEST["SECTION_ID"]);
        $countLotsAllGraph[$vy] = CTenderixStatistic::LotsCount($vy,"year_all_for_graph", 12, $_REQUEST["SECTION_ID"]);

        $countLotsAllWinGraph[$vy] = CTenderixStatistic::LotsCount($vy,"year_all_win_for_graph", 12, $_REQUEST["SECTION_ID"]);
        $countLotsAllFailGraph[$vy] = CTenderixStatistic::LotsCount($vy,"year_all_fail_for_graph", 12, $_REQUEST["SECTION_ID"]);

        $countLotsAllN[$vy] = CTenderixStatistic::LotsCount($vy, "year_all_n", 12, $_REQUEST["SECTION_ID"]);
        $countLotsAllP[$vy] = CTenderixStatistic::LotsCount($vy, "year_all_p", 12, $_REQUEST["SECTION_ID"]);

        $PriceLotsAllP[$vy] = floatval(CTenderixStatistic::LotsPrice($vy, "year_all_p", 12, $_REQUEST["SECTION_ID"]));
        $PriceLotsAllN[$vy] = floatval(CTenderixStatistic::LotsPrice($vy, "year_all_n", 12, $_REQUEST["SECTION_ID"]));

        $PriceLotsAll[$vy] = floatval(CTenderixStatistic::LotsPrice($vy,"year_all", 12, $_REQUEST["SECTION_ID"]));

        $MinPriceLotsAllN[$vy] = CTenderix::formatPrice(floatval(CTenderixStatistic::LotsMinPrice($vy, "year_all_n", 12, $_REQUEST["SECTION_ID"])), $format);
        $MinPriceLotsAllP[$vy] = CTenderix::formatPrice(floatval(CTenderixStatistic::LotsMinPrice($vy, "year_all_p", 12, $_REQUEST["SECTION_ID"])), $format);

        $effect_N = floatval(CTenderixStatistic::LotsPrice($vy, "year_all_n", 12, $_REQUEST["SECTION_ID"])) - floatval(CTenderixStatistic::LotsMinPrice($vy, "year_all_n", 12, $_REQUEST["SECTION_ID"]));
        $effect_P = floatval(CTenderixStatistic::LotsPrice($vy, "year_all_p", 12, $_REQUEST["SECTION_ID"])) - floatval(CTenderixStatistic::LotsMinPrice($vy, "year_all_p", 12, $_REQUEST["SECTION_ID"]));
		
		$lot_price = floatval(CTenderixStatistic::LotsPrice($vy, "year_all_n", 12, $_REQUEST["SECTION_ID"]));
		if ($lot_price != 0)			
			$effect_N_percent = (floatval(CTenderixStatistic::LotsMinPrice($vy, "year_all_n", 12, $_REQUEST["SECTION_ID"])) / $lot_price) * 100;
		else
			$effect_N_percent = 0;
			
		$lot_price = floatval(CTenderixStatistic::LotsPrice($vy, "year_all_p", 12, $_REQUEST["SECTION_ID"]));
		if ($lot_price != 0)
			$effect_P_percent = (floatval(CTenderixStatistic::LotsMinPrice($vy, "year_all_p", 12, $_REQUEST["SECTION_ID"])) / $lot_price) * 100;
		else
			$effect_P_percent = 0;
			
        $EffectPriceLotsAllN[$vy] = CTenderix::formatPrice($effect_N, $format);
        $EffectPriceLotsAllP[$vy] = CTenderix::formatPrice($effect_P, $format);

        $EffectPPriceLotsAllN[$vy] = $effect_N_percent;
        $EffectPPriceLotsAllP[$vy] = $effect_P_percent;

        $countLotsAllActive[$vy] = CTenderixStatistic::LotsCount($vy, "year_all_active", 12, $_REQUEST["SECTION_ID"]);
        $countLotsAllNActive[$vy] = CTenderixStatistic::LotsCount($vy, "year_all_n_active", 12, $_REQUEST["SECTION_ID"]);
        $countLotsAllPActive[$vy] = CTenderixStatistic::LotsCount($vy, "year_all_p_active", 12, $_REQUEST["SECTION_ID"]);

        $countLotsAllFail[$vy] = CTenderixStatistic::LotsCount($vy, "year_all_fail", 12, $_REQUEST["SECTION_ID"]);
        $countLotsAllNFail[$vy] = CTenderixStatistic::LotsCount($vy, "year_all_n_fail", 12, $_REQUEST["SECTION_ID"]);
        $countLotsAllPFail[$vy] = CTenderixStatistic::LotsCount($vy, "year_all_p_fail", 12, $_REQUEST["SECTION_ID"]);

        $countLotsAllWin[$vy] = CTenderixStatistic::LotsCount($vy, "year_all_win", 12, $_REQUEST["SECTION_ID"]);
        $countLotsAllNWin[$vy] = CTenderixStatistic::LotsCount($vy, "year_all_n_win", 12, $_REQUEST["SECTION_ID"]);
        $countLotsAllPWin[$vy] = CTenderixStatistic::LotsCount($vy, "year_all_p_win", 12, 12, $_REQUEST["SECTION_ID"]);
    }

    foreach($MONTH_NAME as $km => $nm) {
        $year = (isset($_REQUEST["year"]) ? $_REQUEST["year"] : date('Y'));


        $countUsersAll[$km] = CTenderixStatistic::UsersCount($year,"all", $km);
        $countUsersNew[$km] = CTenderixStatistic::UsersCount($year, "new", $km);
		
        $countLotsAll[$km] = CTenderixStatistic::LotsCount($year,"all", $km, $_REQUEST["SECTION_ID"]);
        $countLotsAllGraph[$km] = CTenderixStatistic::LotsCount($year,"all_for_graph", $km, $_REQUEST["SECTION_ID"]);
		

        $countLotsAllWinGraph[$km] = CTenderixStatistic::LotsCount($year,"all_win_for_graph", $km, $_REQUEST["SECTION_ID"]);
        $countLotsAllFailGraph[$km] = CTenderixStatistic::LotsCount($year,"all_fail_for_graph", $km, $_REQUEST["SECTION_ID"]);

        $countLotsAllN[$km] = CTenderixStatistic::LotsCount($year,"all_n", $km, $_REQUEST["SECTION_ID"]);
        $countLotsAllP[$km] = CTenderixStatistic::LotsCount($year,"all_p", $km, $_REQUEST["SECTION_ID"]);

        $PriceLotsAllN[$km] = floatval(CTenderixStatistic::LotsPrice($year,"all_n", $km, $_REQUEST["SECTION_ID"]));
        $PriceLotsAllP[$km] = floatval(CTenderixStatistic::LotsPrice($year,"all_p", $km, $_REQUEST["SECTION_ID"]));

        $PriceLotsAll[$km] = floatval(CTenderixStatistic::LotsPrice($year,"all", $km, $_REQUEST["SECTION_ID"]));

        $MinPriceLotsAllN[$km] = CTenderix::formatPrice(floatval(CTenderixStatistic::LotsMinPrice($year,"all_n", $km, $_REQUEST["SECTION_ID"])), $format);
        $MinPriceLotsAllP[$km] = CTenderix::formatPrice(floatval(CTenderixStatistic::LotsMinPrice($year,"all_p", $km, $_REQUEST["SECTION_ID"])), $format);

        $effect_N = floatval(CTenderixStatistic::LotsPrice($year,"all_n", $km, $_REQUEST["SECTION_ID"])) - floatval(CTenderixStatistic::LotsMinPrice($year,"all_n", $km, $_REQUEST["SECTION_ID"]));
        $effect_P = floatval(CTenderixStatistic::LotsPrice($year,"all_p", $km, $_REQUEST["SECTION_ID"])) - floatval(CTenderixStatistic::LotsMinPrice($year,"all_p", $km, $_REQUEST["SECTION_ID"]));
		
		$lot_price = floatval(CTenderixStatistic::LotsPrice($year,"all_n", $km, $_REQUEST["SECTION_ID"]));
		if ($lot_price != 0)
			$effect_N_percent = (floatval(CTenderixStatistic::LotsMinPrice($year,"all_n", $km, $_REQUEST["SECTION_ID"])) / $lot_price)*100;
        else
			$effect_N_percent = 0;
			
		$lot_price = floatval(CTenderixStatistic::LotsPrice($year,"all_p", $km, $_REQUEST["SECTION_ID"]));
		if ($lot_price != 0)
			$effect_P_percent = (floatval(CTenderixStatistic::LotsMinPrice($year,"all_p", $km, $_REQUEST["SECTION_ID"])) / $lot_price)*100;
		else
			$effect_P_percent = 0;

        $EffectPriceLotsAllN[$km] = CTenderix::formatPrice($effect_N, $format);
        $EffectPriceLotsAllP[$km] = CTenderix::formatPrice($effect_P, $format);

        $EffectPPriceLotsAllN[$km] = $effect_N_percent;
        $EffectPPriceLotsAllP[$km] = $effect_P_percent;

        $countLotsAllActive[$km] = CTenderixStatistic::LotsCount($year, "all_active" , $km, $_REQUEST["SECTION_ID"]);
        $countLotsAllNActive[$km] = CTenderixStatistic::LotsCount($year,"all_n_active", $km, $_REQUEST["SECTION_ID"]);
        $countLotsAllPActive[$km] = CTenderixStatistic::LotsCount($year,"all_p_active", $km, $_REQUEST["SECTION_ID"]);

        $countLotsAllFail[$km] = CTenderixStatistic::LotsCount($year, "all_fail" , $km, $_REQUEST["SECTION_ID"]);
        $countLotsAllNFail[$km] = CTenderixStatistic::LotsCount($year,"all_n_fail", $km, $_REQUEST["SECTION_ID"]);
        $countLotsAllPFail[$km] = CTenderixStatistic::LotsCount($year,"all_p_fail", $km, $_REQUEST["SECTION_ID"]);

        $countLotsAllWin[$km] = CTenderixStatistic::LotsCount($year, "all_win" , $km, $_REQUEST["SECTION_ID"]);
        $countLotsAllNWin[$km] = CTenderixStatistic::LotsCount($year,"all_n_win", $km, $_REQUEST["SECTION_ID"]);
        $countLotsAllPWin[$km] = CTenderixStatistic::LotsCount($year,"all_p_win", $km, $_REQUEST["SECTION_ID"]);

        if($km >= date('n') && $year == date('Y')) {
            break;
        }
    }
		

    $arResult["USERS_ITOGO"] = $countUsersAll;
    $arResult["USERS_NEW"] = $countUsersNew;
    $arResult["LOTS_ITOGO"] = $countLotsAll;
    $arResult["LOTS_ITOGO_GRAPH"] = $countLotsAllGraph;
    $arResult["LOTS_ITOGO_WIN_GRAPH"] = $countLotsAllWinGraph;
    $arResult["LOTS_ITOGO_FAIL_GRAPH"] = $countLotsAllFailGraph;
    $arResult["LOTS_ITOGO_N"] = $countLotsAllN;
    $arResult["LOTS_ITOGO_P"] = $countLotsAllP;
    $arResult["LOTS_PRICE_P"] = $PriceLotsAllP;
    $arResult["LOTS_PRICE_N"] = $PriceLotsAllN;
    $arResult["LOTS_PRICE"] = $PriceLotsAll;
    $arResult["LOTS_MIN_PRICE_P"] = $MinPriceLotsAllP;
    $arResult["LOTS_MIN_PRICE_N"] = $MinPriceLotsAllN;
    $arResult["LOTS_EFFECT_PRICE_P"] = $EffectPriceLotsAllP;
    $arResult["LOTS_EFFECT_PRICE_N"] = $EffectPriceLotsAllN;
    $arResult["LOTS_EFFECT_P_PRICE_P"] = $EffectPPriceLotsAllP;
    $arResult["LOTS_EFFECT_P_PRICE_N"] = $EffectPPriceLotsAllN;
    $arResult["LOTS_ITOGO_ACTIVE"] = $countLotsAllActive;
    $arResult["LOTS_ITOGO_N_ACTIVE"] = $countLotsAllNActive;
    $arResult["LOTS_ITOGO_P_ACTIVE"] = $countLotsAllPActive;
    $arResult["LOTS_ITOGO_FAIL"] = $countLotsAllFail;
    $arResult["LOTS_ITOGO_N_FAIL"] = $countLotsAllNFail;
    $arResult["LOTS_ITOGO_P_FAIL"] = $countLotsAllPFail;
    $arResult["LOTS_ITOGO_WIN"] = $countLotsAllWin;
    $arResult["LOTS_ITOGO_N_WIN"] = $countLotsAllNWin;
    $arResult["LOTS_ITOGO_P_WIN"] = $countLotsAllPWin;

    $arResult["YEARS_REGISTER"] = $years_reg;

    $arResult["MONTHS"] = $MONTH_NAME;
	
	

    $rsSection = CTenderixSection::GetList($by = "s_c_sort", $order = "asc", array("ACTIVE" => "Y"), $is_filtered = false);
    while ($arSection = $rsSection->Fetch()) {
        $arResult["SECTION_ARR"][$arSection["CATALOG_ID"]][] = $arSection;
    }


    $CACHE_MANAGER->StartTagCache($this->GetCachePath());
    $CACHE_MANAGER->RegisterTag('pweb.tenderix_statistic.lots');
    $CACHE_MANAGER->EndTagCache();
	

    $this->IncludeComponentTemplate();
//}
?>
