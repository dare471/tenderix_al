<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тендер детально");
?><?$APPLICATION->IncludeComponent("pweb.tenderix:lot.detail", ".default", array(
	"LOT_ID" => $_REQUEST["LOT_ID"],
	"PROPOSAL_URL" => "proposal.php?LOT_ID=#LOT_ID#",
	"LOT_URL" => "lot.php?ID=#ID#",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600000"
	),
	false
);?>
<br />
 
<br />
<?$APPLICATION->IncludeComponent("pweb.tenderix:proposal.list", ".default", array(
	"LOT_ID" => $_REQUEST["LOT_ID"],
	"JQUERY" => "Y",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600000",
	"SORT_ITOGO" => "asc"
	),
	false
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>