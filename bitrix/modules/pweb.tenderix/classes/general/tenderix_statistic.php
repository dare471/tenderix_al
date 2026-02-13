<?php

IncludeModuleLangFile(__FILE__);

class CAllTenderStatistic {

    function UpdateAll() {
        CTenderixStatistic::DeleteAll();
        $rsLots = CTenderixLot::GetAll();
        while ($arLots = $rsLots->Fetch()) {
            CTenderixStatistic::Add($arLots["ID"], $arLots);
            if ($arLots["TYPE_ID"] == "N") {
                CTenderixStatistic::UpdatePriceForN($arLots["ID"]);
            }
            if ($arLots["TYPE_ID"] == "S") {
                CTenderixStatistic::UpdatePriceForS($arLots["ID"]);
            }
        }
    }

    function UpdatePriceForN($LOT_ID) {
        $res = CTenderixProposal::GetListSpecPriceAll($LOT_ID);
        while ($arRes = $res->Fetch()) {
            $arProposalPrice[$arRes["PROPERTY_BUYER_ID"]][] = $arRes["PRICE_NDS"];
        }
        $sumMax = 0;
        $rSumMax = 0;
        $sumMin = 0;
        $rSumMin = 0;
        foreach ($arProposalPrice as $arPrice) {
            sort($arPrice, SORT_NUMERIC);
            $priceMin = $arPrice[0];
            rsort($arPrice, SORT_NUMERIC);
            $cnt = count($arPrice);
            if ($cnt > 2) {
                $priceMax = $arPrice[1];
            } else {
                $priceMax = $arPrice[0];
            }
            $rPriceMax = max($arPrice);
            $rPriceMin = min($arPrice);
            $sumMax += ($priceMax + $priceMin) / 2;
            $sumMin += $priceMin;
            $rSumMax += $rPriceMax;
            $rSumMin += $rPriceMin;
        }
        CTenderixStatistic::Update($LOT_ID, array("PRICE_MAX" => $sumMax, "PRICE_MIN" => $sumMin, "RIGHT_PRICE_MAX" => $rSumMax, "RIGHT_PRICE_MIN" => $rSumMin));
    }

    function UpdatePriceForS($LOT_ID) {
        $res = CTenderixProposal::GetListProductsPriceAll($LOT_ID);
        while ($arRes = $res->Fetch()) {
            $arPrice[] = $arRes["PRICE_NDS"];
        }
        $sumMax = 0;
        $rSumMax = 0;
        $sumMin = 0;
        $rSumMin = 0;

        sort($arPrice, SORT_NUMERIC);
        $priceMin = $arPrice[0];
        rsort($arPrice, SORT_NUMERIC);
        $cnt = count($arPrice);
        if ($cnt > 2) {
            $priceMax = $arPrice[1];
        } else {
            $priceMax = $arPrice[0];
        }
        $rPriceMax = max($arPrice);
        $rPriceMin = min($arPrice);
        $priceMax = ($priceMax + $priceMin) / 2;
        CTenderixStatistic::Update($LOT_ID, array("PRICE_MAX" => $priceMax, "PRICE_MIN" => $priceMin, "RIGHT_PRICE_MAX" => $rPriceMax, "RIGHT_PRICE_MIN" => $rPriceMin));
    }

}

?>
