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
$CACHE_PATH = str_replace(array(":", "//"), "/", "/" . SITE_ID . "/pweb.tenderix/statistic.lots");

if ($this->StartResultCache(false, $CACHE_ID, $CACHE_PATH)) {

    $countLotStatistic = CTenderixStatistic::Count();
    $countLot = CTenderixLot::Count();
    if ($countLotStatistic != $countLot) {
        CTenderixStatistic::UpdateAll();
    }


    $arFilter = array();
    if ($companyUser > 0) {
        $arFilter = array("COMPANY_ID" => $companyUser);
    }
    $rsCompany = CTenderixCompany::GetList($by = "", $order = "", $arFilter = Array(), $is_filtered);
    while ($arCompany = $rsCompany->Fetch()) {
        $COMPANY_NAME[$arCompany["ID"]] = $arCompany["TITLE"];
    }
    $rsSection = CTenderixSection::GetList($by = "", $order = "", $arFilter = Array(), $is_filtered);
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

    $arFilter = array();
    if ($companyUser > 0) {
        $arFilter = array("COMPANY_ID" => $companyUser);
    }
    $arStatistic = CTenderixStatistic::GetList(array(), $arFilter); 
	$timeZone = time() + CTimeZone::GetOffset();
    foreach ($arStatistic as $arItem) {
        $lot_not_win = $arItem["DATE_TIME"] < $timeZone && $arItem["WIN"] == "N" && $arItem["ACTIVE"] == "Y" ? 1 : 0;
        $lot_active = $arItem["DATE_TIME"] > $timeZone && $arItem["ACTIVE"] == "Y" ? 1 : 0;
        $lot_lifted = $arItem["ACTIVE"] == "N" ? 1 : 0;

        $arResL1[$arItem[$arParams["TYPE_L1"]]]["MAX"] += floatval($arItem["PRICE_MAX"]);
        $arResL1[$arItem[$arParams["TYPE_L1"]]]["MIN"] += floatval($arItem["PRICE_MIN"]);
        $arResL1[$arItem[$arParams["TYPE_L1"]]]["DEP"] += floatval($arItem["PRICE_MAX"]) - floatval($arItem["PRICE_MIN"]);
        $arResL1[$arItem[$arParams["TYPE_L1"]]]["LOTS_ALL"] += 1;
        $arResL1[$arItem[$arParams["TYPE_L1"]]]["LOTS_NOT_WIN"] += $lot_not_win;
        $arResL1[$arItem[$arParams["TYPE_L1"]]]["LOTS_LIFTED"] += $lot_lifted;
        $arResL1[$arItem[$arParams["TYPE_L1"]]]["LOTS_ACTIVE"] += $lot_active;

        $arResL2[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]]["MAX"] += floatval($arItem["PRICE_MAX"]);
        $arResL2[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]]["MIN"] += floatval($arItem["PRICE_MIN"]);
        $arResL2[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]]["DEP"] += floatval($arItem["PRICE_MAX"]) - floatval($arItem["PRICE_MIN"]);
        $arResL2[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]]["LOTS_ALL"] += 1;
        $arResL2[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]]["LOTS_NOT_WIN"] += $lot_not_win;
        $arResL2[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]]["LOTS_LIFTED"] += $lot_lifted;
        $arResL2[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]]["LOTS_ACTIVE"] += $lot_active;

        $arResL3[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]]["MAX"] += floatval($arItem["PRICE_MAX"]);
        $arResL3[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]]["MIN"] += floatval($arItem["PRICE_MIN"]);
        $arResL3[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]]["DEP"] += floatval($arItem["PRICE_MAX"]) - floatval($arItem["PRICE_MIN"]);
        $arResL3[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]]["LOTS_ALL"] += 1;
        $arResL3[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]]["LOTS_NOT_WIN"] += $lot_not_win;
        $arResL3[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]]["LOTS_LIFTED"] += $lot_lifted;
        $arResL3[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]]["LOTS_ACTIVE"] += $lot_active;

        $arResL4[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]][$arItem[$arParams["TYPE_L4"]]]["MAX"] += floatval($arItem["PRICE_MAX"]);
        $arResL4[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]][$arItem[$arParams["TYPE_L4"]]]["MIN"] += floatval($arItem["PRICE_MIN"]);
        $arResL4[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]][$arItem[$arParams["TYPE_L4"]]]["DEP"] += floatval($arItem["PRICE_MAX"]) - floatval($arItem["PRICE_MIN"]);
        $arResL4[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]][$arItem[$arParams["TYPE_L4"]]]["LOTS_ALL"] += 1;
        $arResL4[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]][$arItem[$arParams["TYPE_L4"]]]["LOTS_NOT_WIN"] += $lot_not_win;
        $arResL4[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]][$arItem[$arParams["TYPE_L4"]]]["LOTS_LIFTED"] += $lot_lifted;
        $arResL4[$arItem[$arParams["TYPE_L1"]]][$arItem[$arParams["TYPE_L2"]]][$arItem[$arParams["TYPE_L3"]]][$arItem[$arParams["TYPE_L4"]]]["LOTS_ACTIVE"] += $lot_active;

        $arResItog["MAX"] += floatval($arItem["PRICE_MAX"]);
        $arResItog["MIN"] += floatval($arItem["PRICE_MIN"]);
        $arResItog["DEP"] += floatval($arItem["PRICE_MAX"]) - floatval($arItem["PRICE_MIN"]);
        $arResItog["LOTS_ALL"] += 1;
        $arResItog["LOTS_NOT_WIN"] += $lot_not_win;
        $arResItog["LOTS_LIFTED"] += $lot_lifted;
        $arResItog["LOTS_ACTIVE"] += $lot_active;
    }

    unset($arStatistic);
    $arStatistic = array("L4" => $arResL4, "L3" => $arResL3, "L2" => $arResL2, "L1" => $arResL1, "ITOG" => $arResItog);
    if (intval($arParams["LEVEL_COL"]) == 2) {
        unset($arStatistic["L3"]);
        unset($arStatistic["L4"]);
    }
    if (intval($arParams["LEVEL_COL"]) == 3) {
        unset($arStatistic["L4"]);
    }

    foreach ($arStatistic["L1"] as $arResL1ID => $arResL1) {
        $arStatistic["L1"][$arResL1ID]["MAX"] = CTenderix::formatPrice(floatval($arResL1["MAX"]) / $unit, $format);
        $arStatistic["L1"][$arResL1ID]["MIN"] = CTenderix::formatPrice(floatval($arResL1["MIN"]) / $unit, $format);
        $arStatistic["L1"][$arResL1ID]["DEP"] = CTenderix::formatPrice(floatval($arResL1["DEP"]) / $unit, $format);
        $arStatistic["L1"][$arResL1ID]["MIN_E"] = round(($arResL1["MIN"] * 100) / $arResL1["MAX"]);
        $arStatistic["L1"][$arResL1ID]["DEP_E"] = round(($arResL1["DEP"] * 100) / $arResL1["MAX"]);
        $arStatistic["L1"][$arResL1ID]["LOTS_LIFTED_E"] = round(($arResL1["LOTS_LIFTED"] * 100) / $arResL1["LOTS_ALL"]);
        $arStatistic["L1"][$arResL1ID]["LOTS_NOT_WIN_E"] = round(($arResL1["LOTS_NOT_WIN"] * 100) / $arResL1["LOTS_ALL"]);

        if ($arParams["TYPE_L1"] == "COMPANY_ID") {
            $arResult["NAME"]["L1"][$arResL1ID] = $COMPANY_NAME[$arResL1ID];
        } elseif ($arParams["TYPE_L1"] == "SECTION_ID") {
            $arResult["NAME"]["L1"][$arResL1ID] = $SECTION_NAME[$arResL1ID];
        } elseif ($arParams["TYPE_L1"] == "DATE_MONTH") {
            $arResult["NAME"]["L1"][$arResL1ID] = $MONTH_NAME[$arResL1ID];
        } else {
            $arResult["NAME"]["L1"][$arResL1ID] = $arResL1ID;
        }

        foreach ($arStatistic["L2"][$arResL1ID] as $arResL2ID => $arResL2) {
            $arStatistic["L2"][$arResL1ID][$arResL2ID]["MAX"] = CTenderix::formatPrice(floatval($arResL2["MAX"]) / $unit, $format);
            $arStatistic["L2"][$arResL1ID][$arResL2ID]["MIN"] = CTenderix::formatPrice(floatval($arResL2["MIN"]) / $unit, $format);
            $arStatistic["L2"][$arResL1ID][$arResL2ID]["DEP"] = CTenderix::formatPrice(floatval($arResL2["DEP"]) / $unit, $format);
            $arStatistic["L2"][$arResL1ID][$arResL2ID]["MIN_E"] = round(($arResL2["MIN"] * 100) / $arResL2["MAX"]);
            $arStatistic["L2"][$arResL1ID][$arResL2ID]["DEP_E"] = round(($arResL2["DEP"] * 100) / $arResL2["MAX"]);
            $arStatistic["L2"][$arResL1ID][$arResL2ID]["LOTS_LIFTED_E"] = round(($arResL2["LOTS_LIFTED"] * 100) / $arResL2["LOTS_ALL"]);
            $arStatistic["L2"][$arResL1ID][$arResL2ID]["LOTS_NOT_WIN_E"] = round(($arResL2["LOTS_NOT_WIN"] * 100) / $arResL2["LOTS_ALL"]);

            if ($arParams["TYPE_L2"] == "COMPANY_ID") {
                $arResult["NAME"]["L2"][$arResL2ID] = $COMPANY_NAME[$arResL2ID];
            } elseif ($arParams["TYPE_L2"] == "SECTION_ID") {
                $arResult["NAME"]["L2"][$arResL2ID] = $SECTION_NAME[$arResL2ID];
            } elseif ($arParams["TYPE_L2"] == "DATE_MONTH") {
                $arResult["NAME"]["L2"][$arResL2ID] = $MONTH_NAME[$arResL2ID];
            } else {
                $arResult["NAME"]["L2"][$arResL2ID] = $arResL2ID;
            }

            foreach ($arStatistic["L3"][$arResL1ID][$arResL2ID] as $arResL3ID => $arResL3) {
                $arStatistic["L3"][$arResL1ID][$arResL2ID][$arResL3ID]["MAX"] = CTenderix::formatPrice(floatval($arResL3["MAX"]) / $unit, $format);
                $arStatistic["L3"][$arResL1ID][$arResL2ID][$arResL3ID]["MIN"] = CTenderix::formatPrice(floatval($arResL3["MIN"]) / $unit, $format);
                $arStatistic["L3"][$arResL1ID][$arResL2ID][$arResL3ID]["DEP"] = CTenderix::formatPrice(floatval($arResL3["DEP"]) / $unit, $format);
                $arStatistic["L3"][$arResL1ID][$arResL2ID][$arResL3ID]["MIN_E"] = round(($arResL3["MIN"] * 100) / $arResL3["MAX"]);
                $arStatistic["L3"][$arResL1ID][$arResL2ID][$arResL3ID]["DEP_E"] = round(($arResL3["DEP"] * 100) / $arResL3["MAX"]);
                $arStatistic["L3"][$arResL1ID][$arResL2ID][$arResL3ID]["LOTS_LIFTED_E"] = round(($arResL3["LOTS_LIFTED"] * 100) / $arResL3["LOTS_ALL"]);
                $arStatistic["L3"][$arResL1ID][$arResL2ID][$arResL3ID]["LOTS_NOT_WIN_E"] = round(($arResL3["LOTS_NOT_WIN"] * 100) / $arResL3["LOTS_ALL"]);

                if ($arParams["TYPE_L3"] == "COMPANY_ID") {
                    $arResult["NAME"]["L3"][$arResL3ID] = $COMPANY_NAME[$arResL3ID];
                } elseif ($arParams["TYPE_L3"] == "SECTION_ID") {
                    $arResult["NAME"]["L3"][$arResL3ID] = $SECTION_NAME[$arResL3ID];
                } elseif ($arParams["TYPE_L3"] == "DATE_MONTH") {
                    $arResult["NAME"]["L3"][$arResL3ID] = $MONTH_NAME[$arResL3ID];
                } else {
                    $arResult["NAME"]["L3"][$arResL3ID] = $arResL3ID;
                }

                foreach ($arStatistic["L4"][$arResL1ID][$arResL2ID][$arResL3ID] as $arResL4ID => $arResL4) {
                    $arStatistic["L4"][$arResL1ID][$arResL2ID][$arResL3ID][$arResL4ID]["MAX"] = CTenderix::formatPrice(floatval($arResL4["MAX"]) / $unit, $format);
                    $arStatistic["L4"][$arResL1ID][$arResL2ID][$arResL3ID][$arResL4ID]["MIN"] = CTenderix::formatPrice(floatval($arResL4["MIN"]) / $unit, $format);
                    $arStatistic["L4"][$arResL1ID][$arResL2ID][$arResL3ID][$arResL4ID]["DEP"] = CTenderix::formatPrice(floatval($arResL4["DEP"]) / $unit, $format);
                    $arStatistic["L4"][$arResL1ID][$arResL2ID][$arResL3ID][$arResL4ID]["MIN_E"] = round(($arResL4["MIN"] * 100) / $arResL4["MAX"]);
                    $arStatistic["L4"][$arResL1ID][$arResL2ID][$arResL3ID][$arResL4ID]["DEP_E"] = round(($arResL4["DEP"] * 100) / $arResL4["MAX"]);
                    $arStatistic["L4"][$arResL1ID][$arResL2ID][$arResL3ID][$arResL4ID]["LOTS_LIFTED_E"] = round(($arResL4["LOTS_LIFTED"] * 100) / $arResL4["LOTS_ALL"]);
                    $arStatistic["L4"][$arResL1ID][$arResL2ID][$arResL3ID][$arResL4ID]["LOTS_NOT_WIN_E"] = round(($arResL4["LOTS_NOT_WIN"] * 100) / $arResL4["LOTS_ALL"]);

                    if ($arParams["TYPE_L4"] == "COMPANY_ID") {
                        $arResult["NAME"]["L4"][$arResL4ID] = $COMPANY_NAME[$arResL4ID];
                    } elseif ($arParams["TYPE_L4"] == "SECTION_ID") {
                        $arResult["NAME"]["L4"][$arResL4ID] = $SECTION_NAME[$arResL4ID];
                    } elseif ($arParams["TYPE_L4"] == "DATE_MONTH") {
                        $arResult["NAME"]["L4"][$arResL4ID] = $MONTH_NAME[$arResL4ID];
                    } else {
                        $arResult["NAME"]["L4"][$arResL4ID] = $arResL4ID;
                    }
                }
            }
        }
    }
    $arStatistic["ITOG"]["MIN_E"] = round(($arStatistic["ITOG"]["MIN"] * 100) / $arStatistic["ITOG"]["MAX"]);
    $arStatistic["ITOG"]["DEP_E"] = round(($arStatistic["ITOG"]["DEP"] * 100) / $arStatistic["ITOG"]["MAX"]);
    $arStatistic["ITOG"]["MAX"] = CTenderix::formatPrice(floatval($arStatistic["ITOG"]["MAX"]) / $unit, $format);
    $arStatistic["ITOG"]["MIN"] = CTenderix::formatPrice(floatval($arStatistic["ITOG"]["MIN"]) / $unit, $format);
    $arStatistic["ITOG"]["DEP"] = CTenderix::formatPrice(floatval($arStatistic["ITOG"]["DEP"]) / $unit, $format);
    $arStatistic["ITOG"]["LOTS_LIFTED_E"] = round(($arStatistic["ITOG"]["LOTS_LIFTED"] * 100) / $arStatistic["ITOG"]["LOTS_ALL"]);
    $arStatistic["ITOG"]["LOTS_NOT_WIN_E"] = round(($arStatistic["ITOG"]["LOTS_NOT_WIN"] * 100) / $arStatistic["ITOG"]["LOTS_ALL"]);

    $arResult["STATISTIC"] = $arStatistic;

    $CACHE_MANAGER->StartTagCache($this->GetCachePath());
    $CACHE_MANAGER->RegisterTag('pweb.tenderix_statistic.lots');
    $CACHE_MANAGER->EndTagCache();

    $this->IncludeComponentTemplate();
}
?>
