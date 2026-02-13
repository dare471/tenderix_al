<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("PW_TD_USER_INFO"),
	"DESCRIPTION" => GetMessage("PW_TD_USER_INFO_DESC"),
	"ICON" => "/images/user_info.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "pweb_tenderix",
		"SORT" => 2000,
		"NAME" => GetMessage("PWEB_TENDERIX"),
		"CHILD" => array(
			"ID" => "tenderix_user",
			"NAME" => GetMessage("PW_TD_USER"),
			"SORT" => 10,
		)
	),
);

?>