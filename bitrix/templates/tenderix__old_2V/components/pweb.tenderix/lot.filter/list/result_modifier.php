<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>
<?
 foreach ($arResult["ITEMS"] as $k => $item) {
     $arResult["FORM"][$item["FIELD_CODE"]] = $item;
    }
    //echo "<pre>";print_r($arResult);echo "</pre>";
?>