<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/pweb.tenderix/list.suppliers/class.php");

$APPLICATION->SetTitle("Лот детально");
?>

<div class="page">
	<div class="container-fluid">
		<div class="container">
			<div class = "row" style="position:relative">			
			<?$APPLICATION->IncludeComponent(
				"pweb.tenderix:lot.detail",
				"tx_lot_detail",
				Array(
					"CACHE_TIME" => "3600000",
					"CACHE_TYPE" => "N",
					"LOT_ID" => $_REQUEST["LOT_ID"],
					"LOT_URL" => "lot.php?ID=#ID#",
					"PROPOSAL_URL" => "proposal.php?LOT_ID=#LOT_ID#"
				)
			);?>
			</div>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>