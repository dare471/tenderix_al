<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Список поставщиков");
?>
<div class="page">
	<div class="container-fluid">
		<div class="container">
			<div class="row">
				<div class="col">
					<h2 class="page-heading">Поставщики</h2>
				</div>
			</div>
			<div class="row">
				<?$APPLICATION->IncludeComponent(
					"pweb.tenderix:list.suppliers",
					"",
					Array(
						"IBLOCK_TYPE" => "-",
						"IBLOCK_ID" => "0",
						"SHOW_NAV" => "N",
						"COUNT" => "0",
						"SORT_FIELD1" => "ID",
						"SORT_DIRECTION1" => "ASC",
						"SORT_FIELD2" => "ID",
						"SORT_DIRECTION2" => "ASC",
						"CACHE_TYPE" => "N",
						"CACHE_TIME" => "3600"
					)
				);?>
				
			</div>
			</br>
		</div>
	</div>	
</div>
				

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>