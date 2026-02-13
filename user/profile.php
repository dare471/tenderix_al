<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Профиль постащика");
?>
<div class="page">
	<div class="container-fluid">
		<div class="container">
			<div class="row">
				<div class="col">
					<h2 class="page-heading">Профиль</h2>
				</div>
			</div>
			<div class="row">
				<div class="col">
				<?$APPLICATION->IncludeComponent("pweb.tenderix:supplier.reg", ".default", Array(
	"JQUERY" => "N",	// Подключить библиотеку JQuery
		"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
		"STATUS" => "1",	// Присваивать статус участнику, который может участвовать в поставках и покупках, после регистрации
		"STATUS2" => "1",	// Присваивать статус участнику, который может участвовать только в покупках, после регистрации
		"FIELDS" => array(	// Выводимые поля для регистрации
			0 => "LAST_NAME",
			1 => "NAME",
			2 => "SECOND_NAME",
			3 => "NAME_COMPANY",
			4 => "NAME_DIRECTOR",
			5 => "NAME_ACCOUNTANT",
			6 => "CODE_INN",
			7 => "LEGALADDRESS_REGION",
			8 => "LEGALADDRESS_CITY",
			9 => "POSTALADDRESS_REGION",
			10 => "POSTALADDRESS_CITY",
			11 => "PHONE",
			12 => "FAX",
			13 => "STATEREG_DATE",
			14 => "STATEREG_NDS",
			15 => "BANKING_NAME",
			16 => "BANKING_ACCOUNT",
			17 => "DOP_FIELDS_DIRECTION_ACTIVE",
			18 => "DOP_FIELDS_SUBSCRIBE_ACTIVE",
			19 => "DOP_FIELDS_DOCUMENT_ACTIVE",
		),
		"REG_FIELDS_REQUIRED" => array(	// Обязательные поля
			0 => "LAST_NAME",
			1 => "NAME",
			2 => "SECOND_NAME",
			3 => "CODE_INN",
			4 => "NAME_COMPANY",
			5 => "STATEREG_DATE",
			6 => "STATEREG_NDS",
		),
		"FIELDS2" => array(	// Выводимые поля для регистрации
			0 => "LAST_NAME",
			1 => "NAME",
			2 => "SECOND_NAME",
			3 => "NAME_COMPANY",
			4 => "NAME_DIRECTOR",
			5 => "NAME_ACCOUNTANT",
			6 => "CODE_INN",
			7 => "LEGALADDRESS_REGION",
			8 => "LEGALADDRESS_CITY",
			9 => "POSTALADDRESS_REGION",
			10 => "POSTALADDRESS_CITY",
			11 => "PHONE",
			12 => "FAX",
			13 => "STATEREG_DATE",
			14 => "STATEREG_NDS",
			15 => "BANKING_NAME",
			16 => "BANKING_ACCOUNT",
			17 => "DOP_FIELDS_DIRECTION_ACTIVE",
			18 => "DOP_FIELDS_SUBSCRIBE_ACTIVE",
			19 => "DOP_FIELDS_DOCUMENT_ACTIVE",
		),
		"REG_FIELDS_REQUIRED2" => array(	// Обязательные поля
			0 => "LAST_NAME",
			1 => "NAME",
			2 => "NAME_COMPANY",
			3 => "SECOND_NAME",
			4 => "CODE_INN",
			5 => "STATEREG_DATE",
			6 => "STATEREG_NDS",
		),
		"COMPONENT_TEMPLATE" => "tx_supplier_reg"
	),
	false
);?></br>
				</div>
			</div>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>