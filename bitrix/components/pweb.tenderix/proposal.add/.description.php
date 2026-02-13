<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("PW_TD_PROPOSAL_ADD"),
	"DESCRIPTION" => GetMessage("PW_TD_PROPOSAL_ADD_DESC"),
	"ICON" => "/images/add_proposal.gif",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "pweb_tenderix",
		"SORT" => 2000,
		"NAME" => GetMessage("PWEB_TENDERIX"),
		"CHILD" => array(
			"ID" => "tenderix_proposal",
			"NAME" => GetMessage("PW_TD_PROPOSAL"),
			"SORT" => 10,
		)
	),
);

?>