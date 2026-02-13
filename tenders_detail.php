<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/pweb.tenderix/list.suppliers/class.php");
$APPLICATION->SetTitle("Лот детально");

LocalRedirect('/user' . $APPLICATION->GetCurPageParam());

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>