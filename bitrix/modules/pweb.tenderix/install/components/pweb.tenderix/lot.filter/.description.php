<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("PW_TD_LOT_FILTER"),
	"DESCRIPTION" => GetMessage("PW_TD_LOT_FILTER_DESC"),
	"ICON" => "/images/lot_filter.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "pweb_tenderix",
		"SORT" => 2000,
		"NAME" => GetMessage("PWEB_TENDERIX"),
		"CHILD" => array(
			"ID" => "tenderix_lot",
			"NAME" => GetMessage("PW_TD_LOT"),
			"SORT" => 10,
		)
	),
);

?>