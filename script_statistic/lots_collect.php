<?php
/**
 * Created by PhpStorm.
 * User: vfilippov
 * Date: 01.04.16
 * Time: 17:38
 */

require($_SERVER['DOCUMENT_ROOT']."/bitrix/header.php");

$strSql = "SELECT l.ID AS LOT_ID, l.DATE_START, SUM(p.START_PRICE) AS START_PRICE
            FROM b_tx_lot l
            LEFT JOIN b_tx_spec_buyer b ON l.ID = b.LOT_ID
            LEFT JOIN b_tx_spec_property_b p ON p.SPEC_ID = b.ID
            GROUP BY LOT_ID
            ";

$arRes = $DB->Query($strSql, false, $err_mess . __LINE__);
while ($rsRes = $arRes->Fetch()) {
    $arLot[$rsRes["LOT_ID"]] = $rsRes;
}

//__($arLot);

$strSql1 = "SELECT LOT_ID FROM b_tx_lot_win GROUP BY LOT_ID";
$arRes1 = $DB->Query($strSql1, false, $err_mess . __LINE__);
while ($rsRes1 = $arRes1->Fetch()) {
    $arLotID[] = $rsRes1["LOT_ID"];
}

//__($arLotID);

$strSql2 = "SELECT LOT_ID, USER_ID FROM b_tx_lot_win";
$arRes2 = $DB->Query($strSql2, false, $err_mess . __LINE__);
while ($rsRes2 = $arRes2->Fetch()) {
    $arLotWinUser[$rsRes2["LOT_ID"]][] = $rsRes2["USER_ID"];
}

//__($arLotWinUser);


$i=0;
$itogo = array();
foreach($arLotWinUser as $lotid) {
    foreach($lotid as $k => $v) {
        $strSqlI = "SELECT
                        b.COUNT*s.PRICE_NDS AS ITOGO
                    FROM
                        b_tx_proposal p
                    LEFT JOIN
                        b_tx_proposal_spec s
                    ON p.ID = s.PROPOSAL_ID
                    LEFT JOIN
                        b_tx_spec_property_b b
                    ON b.ID = s.PROPERTY_BUYER_ID
                    WHERE
                        p.LOT_ID = ".$arLotID[$i]." AND p.USER_ID =".$v;

        //__($strSqlI);

        $arResI = $DB->Query($strSqlI, false, $err_mess . __LINE__);
        $rsResI = $arResI->Fetch();
        $itogo[$arLotID[$i]][] = $rsResI["ITOGO"];
    }
    $i++;
}

//__($itogo);
$i=0;
foreach($itogo as $lotid) {
    $strSqlP = "UPDATE b_tx_statistic SET MIN_PROP = ".array_sum($lotid)." WHERE LOT_ID = " . $arLotID[$i];
    $i++;

    //__($strSqlP);
    $DB->Query($strSqlP, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);

}

//__($arLot);

foreach($arLot as $arkey) {
    $lot["DATE_START"] = strtotime($arkey["DATE_START"]);
    $date_reg = explode(" ", $arkey["DATE_START"], 2);
    $date_reg = explode("-", $date_reg[0]);
    $lot["DATE_START_YEAR"] = $date_reg[0];
    $lot["DATE_START_MONTH"] = $date_reg[1];
    $lot["LOT_PRICE"] = $arkey["START_PRICE"];

    $strUpdate = $DB->PrepareUpdate("b_tx_statistic", $lot);
    $strSql = "UPDATE b_tx_statistic SET " . $strUpdate . " WHERE LOT_ID = " . $arkey["LOT_ID"];
    //__($strSql);
    $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
}

foreach($arLotID as $k => $val) {
    $strSqlU = "UPDATE b_tx_statistic SET WIN = 'Y' WHERE LOT_ID = " . $val;
    $DB->Query($strSqlU, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
}

require($_SERVER['DOCUMENT_ROOT']."/bitrix/footer.php");