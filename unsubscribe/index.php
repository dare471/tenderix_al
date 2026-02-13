 <?
//define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Отписаться от рассылки");
?>
<div class="page">
	<div class="container-fluid">
		<div class="container">
			<div class="row">
				<div class="col">
					<h2 class="page-heading">Отписаться от рассылки</h2>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?$APPLICATION->IncludeComponent(
						"bitrix:subscribe.unsubscribe",
						".default",
						Array(
							"ASD_MAIL_ID" => $_REQUEST["mid"],
							"ASD_MAIL_MD5" => $_REQUEST["mhash"],
						),
					false
					);?>
				</div>
			</div>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>