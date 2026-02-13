<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Создание торгов");
?>
<div class="page">
	<div class="container-fluid">
		<div class="container">
			<div class="row">
				<div class="col">
					<h2 class="page-heading">Создать лот</h2>
				</div>
			</div>
			<div class="row">
				<div class="col">
					 <?$APPLICATION->IncludeComponent(
						"pweb.tenderix:lot.add", 
						"tx_lot_add_plus", 
						array(
							"JQUERY" => "Y",
							"VISUAL_EDITOR" => "Y",
							"COMPANY_ONLY" => "N"
						),
						false
					);?>	
					</br>
				</div>
				
			</div>
		</div>
	</div>
</div>
<div class="row">
	
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>