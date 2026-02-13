<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("NAME"),
	"DESCRIPTION" => GetMessage("DESCRIPTION"),
	"ICON" => "/images/detail.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "demo",
		"NAME" => "demo",
		"CHILD" => array(
			"ID" => "demo_other",
			"NAME" => GetMessage("DIR_NAME")
		),

	),
);

?>