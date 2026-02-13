<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подача предложения");
?><?$APPLICATION->IncludeComponent("pweb.tenderix:proposal.add", ".default", Array(
	"LOT_ID" => $_REQUEST["LOT_ID"],	// ID лота
	"CURR" => "RUB",	// Валюта для поставщиков по умолчанию
	"JQUERY" => "Y",	// Подключить библиотеку JQuery
	"PROPERTY" => array(	// Дополнительные свойства выводимые при подаче предложения на поставку
		0 => "PROP_1",
		1 => "PROP_5",
		2 => "PROP_8",
	),
	"PROPERTY_REQUIRED" => array(	// Обязательные поля
		0 => "PROP_1",
		1 => "PROP_5",
	),
	"PROPERTY2" => "",	// Дополнительные свойства выводимые при подаче предложения на покупку
	"PROPERTY_REQUIRED2" => array(	// Обязательные поля
		0 => "PROP_1",
		1 => "PROP_2",
		2 => "PROP_5",
		3 => "PROP_6",
	)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>