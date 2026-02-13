<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("PW_TD_STATISTIC_LOTS"),
	"DESCRIPTION" => GetMessage("PW_TD_STATISTIC_LOTS_DESC"),
	"ICON" => "/images/statistic_lots.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "pweb_tenderix",
		"SORT" => 2000,
		"NAME" => GetMessage("PWEB_TENDERIX"),
		"CHILD" => array(
			"ID" => "tenderix_statistic",
			"NAME" => GetMessage("PW_TD_STATISTIC"),
			"SORT" => 10,
		)
	),
);

?>