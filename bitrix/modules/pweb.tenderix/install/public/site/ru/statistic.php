<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Статистика");
?><?$APPLICATION->IncludeComponent("pweb.tenderix:statistic.lots", ".default", array(
	"JQUERY" => "Y",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "86400",
	"SET_TITLE" => "Y",
	"UNIT" => "r",
	"FORMAT" => "0",
	"LEVEL_COL" => "4",
	"TYPE_L1" => "COMPANY_ID",
	"TYPE_L2" => "SECTION_ID",
	"TYPE_L3" => "DATE_YEAR",
	"TYPE_L4" => "DATE_MONTH"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>