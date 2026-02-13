<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подача предложения");
?>
<div class="page">
	<div class="container-fluid">
		<div class="container">
			<? 				
			CModule::IncludeModule('pweb.tenderix');
			$S_RIGHT = CTenderixUserSupplierStatus::GetStatusRight();
			if ($S_RIGHT != "A") {
			?>
			<div class="row">
				<?$APPLICATION->IncludeComponent(
				"pweb.tenderix:proposal.add",
				"tx_proposal_add",
				Array(
					"LOT_ID" => $_REQUEST["LOT_ID"],
					"CURR" => "RUB",
					"JQUERY" => "N",
					"PROPERTY" => array(),
					"PROPERTY_REQUIRED" => array(),
					"PROPERTY2" => array(),
					"PROPERTY_REQUIRED2" => array()
				)
				);?>
			</div>
				<? } else {
				ShowError(GetMessage("ACCESS_DENIED"));
				header("Location: /");
			}?>
		</div>
	</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>