<?php
/**
 * Created by PhpStorm.
 * User: vfilippov
 * Date: 19.08.15
 * Time: 12:26
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


if (!CModule::IncludeModule("pweb.tenderix"))
    return;

//ob_start();
?>
<div>

<? $APPLICATION->IncludeComponent(
    "pweb.tenderix:list.suppliers",
    "",
    Array(
        "IBLOCK_TYPE" => "-",
        "IBLOCK_ID" => "0",
        "SHOW_NAV" => "N",
        "COUNT" => "0",
        "FILTER" => ARRAY(),
        "SORT_FIELD1" => "ID",
        "SORT_DIRECTION1" => "ASC",
        "SORT_FIELD2" => "ID",
        "SORT_DIRECTION2" => "ASC",
        "CACHE_TYPE" => "N",
        "CACHE_TIME" => "3600"
    ),
    false
); ?>

</div>

<? //$out = ob_get_contents();
//ob_end_clean();
//return $out;
    ?>
