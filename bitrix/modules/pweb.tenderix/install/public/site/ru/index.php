<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тендеры");
?>
<div><?$APPLICATION->IncludeComponent("pweb.tenderix:lot.filter", ".default", array(
	"FILTER_NAME" => "arrFilterLot",
	"FILTER_FIELDS" => array(
		0 => "SECTION_ID",
		1 => "COMPANY_ID",
		2 => "TYPE",
		3 => "TITLE",
		4 => "ID",
		5 => "DATE_START",
		6 => "DATE_END",
		7 => "ARCHIVE_LOT",
		8 => "USER",
	),
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600000"
	),
	false
);?><?$APPLICATION->IncludeComponent("pweb.tenderix:lot.list", ".default", array(
	"DETAIL_URL" => "tenders_detail.php?LOT_ID=#LOT_ID#",
	"PROPOSAL_URL" => "proposal.php?LOT_ID=#LOT_ID#",
	"AJAX_MODE" => "Y",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "Y",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600",
	"LOTS_COUNT" => "4",
	"SORT_BY" => "ID",
	"SORT_ORDER" => "desc",
	"ACTIVE_DATE" => "Y",
	"SET_TITLE" => "Y",
	"FILTER_NAME" => "arrFilterLot",
	"DISPLAY_TOP_PAGER" => "Y",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Лоты",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_SHOW_ALL" => "N",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?> 
  <br />
 
  <br />
 </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>