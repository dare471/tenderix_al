<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Добавление лота");
?><?$APPLICATION->IncludeComponent("pweb.tenderix:lot.add", ".default", array(
	"JQUERY" => "Y",
	"VISUAL_EDITOR" => "Y"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>