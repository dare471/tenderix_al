<?php 

${"GLOBALS"}["xunphwdcxw"]="context";
${"GLOBALS"}["bwmnjevcne"]="use_include_path";
${"GLOBALS"}["hswwcppxqo"]="maxLicenceCompanys";
${"GLOBALS"}["egkwlbti"]="LICENSE_KEY";
${"GLOBALS"}["rjkcxblj"]="total_company";
${"GLOBALS"}["pokmleno"]="ar";
${"GLOBALS"}["whbtxm"]="by";
${"GLOBALS"}["flbdvkeg"]="rs";
${"GLOBALS"}["wjagvcixxug"]="DBType";

global $DBType;
if(!defined("TENDER_CACHE_TIME"))
	define("TENDER_CACHE_TIME",3600);
$taimdwkl="DBType";
${"GLOBALS"}["fmmgfpkch"]="DBType";
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/general/tenderix.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${${"GLOBALS"}["fmmgfpkch"]}."/tenderix_users_supplier.php");
$unuunlqxy="DBType";
$mwcnfpdxtgh="DBType";
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${${"GLOBALS"}["wjagvcixxug"]}."/tenderix_users_buyer.php");
${"GLOBALS"}["qnodowssccx"]="DBType";
$qgqsqjedptml="DBType";
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${$taimdwkl}."/tenderix_company.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${${"GLOBALS"}["wjagvcixxug"]}."/tenderix_spr.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${${"GLOBALS"}["wjagvcixxug"]}."/tenderix_spr_details.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${${"GLOBALS"}["wjagvcixxug"]}."/tenderix_section.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${$qgqsqjedptml}."/tenderix_products.php");${"GLOBALS"}["elignvnt"]="DBType";
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${$mwcnfpdxtgh}."/tenderix_products_property.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${${"GLOBALS"}["wjagvcixxug"]}."/tenderix_currency.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${${"GLOBALS"}["elignvnt"]}."/tenderix_lot.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${${"GLOBALS"}["wjagvcixxug"]}."/tenderix_proposal.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${${"GLOBALS"}["qnodowssccx"]}."/tenderix_users_supplier_status.php");
$sovwsrdedzq="DBType";
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${$unuunlqxy}."/tenderix_statistic.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${${"GLOBALS"}["wjagvcixxug"]}."/tenderix_users_supplier_property.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${${"GLOBALS"}["wjagvcixxug"]}."/tenderix_proposal_property.php");
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pweb.tenderix/classes/".${$sovwsrdedzq}."/tenderix_log.php");
// AddEventHandler("pweb.tenderix","OnBeforeTenderixLotAdd","CTenderixArenaGladiator");
function CTenderixArenaGladiator(&$arFields){
	$yjyugrvods="total_company";
	$vryhanzu="order";${"GLOBALS"}["gzupbjyiri"]="arAG";
	global $APPLICATION,$LICENSE_KEY;
	${${"GLOBALS"}["flbdvkeg"]}=CTenderixCompany::GetList(${${"GLOBALS"}["whbtxm"]}="",${$vryhanzu}="",array());
	${"GLOBALS"}["bmztsvc"]="context";$jiijmpbchi="arAG";while(${${"GLOBALS"}["pokmleno"]}=$rs->Fetch())${${"GLOBALS"}["rjkcxblj"]}++;
	${${"GLOBALS"}["gzupbjyiri"]}=http_build_query(
		Array(	"DATE"=>date("Y/m/d H:i"),
				"URI"=>$_SERVER["SCRIPT_URI"],
				"REMOTE_ADDR"=>$_SERVER["REMOTE_ADDR"],
				"SCRIPT_FILENAME"=>$_SERVER["SCRIPT_FILENAME"],
				"KEY"=>${${"GLOBALS"}["egkwlbti"]}
			));
	${${"GLOBALS"}["bmztsvc"]}=stream_context_create(
		array("http"=>array(
				"method"=>"POST",
				"header"=>"Content-Type: application/x-www-form-urlencoded".PHP_EOL,
				"content"=>${$jiijmpbchi},
				),
			)
		);
	${${"GLOBALS"}["hswwcppxqo"]}=intval(file_get_contents(
		"http://arena.tenderix.ru/lic.php?key=".md5(${${"GLOBALS"}["egkwlbti"]}),${${"GLOBALS"}["bwmnjevcne"]}=false,
		${${"GLOBALS"}["xunphwdcxw"]}));
		
	if(${${"GLOBALS"}["rjkcxblj"]}>${${"GLOBALS"}["hswwcppxqo"]})
		die("<div style=\"padding:10px;color:red;border: solid 1px red;background-color: white;\">ERROR: #AG1".${$yjyugrvods}."3</div>");
}
?>