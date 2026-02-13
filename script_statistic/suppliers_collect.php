<?php
/**
 * Created by PhpStorm.
 * User: vfilippov
 * Date: 31.03.16
 * Time: 13:31
 */

require($_SERVER['DOCUMENT_ROOT']."/bitrix/header.php");

$strSql = "SELECT ID, ACTIVE, DATE_REGISTER AS DATE_REGISTER
            FROM b_user b
            RIGHT JOIN b_tx_supplier s ON b.ID = s.USER_ID
            ";

$arRes = $DB->Query($strSql, false, $err_mess . __LINE__);
while ($rsRes = $arRes->Fetch()) {
    $arSup[] = $rsRes;
}

//__($arSup);

foreach($arSup as $arkey) {
    $supplier["USER_ID"] = $arkey["ID"];
    $supplier["ACTIVE"] = $arkey["ACTIVE"];
    $supplier["DATE_REGISTER"] = strtotime($arkey["DATE_REGISTER"]);
    $date_reg = explode(" ", $arkey["DATE_REGISTER"], 2);
    $date_reg = explode("-", $date_reg[0]);
    $supplier["YEAR_REGISTER"] = $date_reg[0];
    $supplier["MONTH_REGISTER"] = $date_reg[1];

    $arInsert = $DB->PrepareInsert("b_tx_supplier_statistic", $supplier);
    $strSql = "INSERT INTO b_tx_supplier_statistic(" . $arInsert[0] . ") VALUES(" . $arInsert[1] . ")";
    $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
}

require($_SERVER['DOCUMENT_ROOT']."/bitrix/footer.php");