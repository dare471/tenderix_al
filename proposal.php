<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подача предложения");

LocalRedirect('/user' . $APPLICATION->GetCurPageParam());

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>