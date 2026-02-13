<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru"> 
<head>
<?$APPLICATION->ShowHead()?>
<title><?$APPLICATION->ShowTitle()?></title>
</head>

<body>
<?$APPLICATION->ShowPanel();?>

<div id="tendtix">
  <h1><?$APPLICATION->ShowTitle(false)?></h1>
<br clear="all" />
  <?$APPLICATION->IncludeComponent("pweb.tenderix:user.info", ".default", array(
	"LOT_LIST_URL" => "index.php",
	"LOT_ADD_URL" => "lot.php",
	"PROFILE_URL" => "profile.php",
	"PROFILE_SUPPLIER_URL" => "profile_supplier.php",
	"STATISTIC_URL" => "statistic.php",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "3600"
	),
	false
);?> 
  