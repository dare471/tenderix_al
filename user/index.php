<?
define("NEED_AUTH", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Текущие закупки");
?><?//echo "<pre>";print_r($_SERVER);echo "</pre>"?>
<div class="page">
	<div class="container-fluid">
		<div class="container">
			<div class="row">
				<div class="col">
					<h2 class="page-heading">Список лотов</h2>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<?$APPLICATION->IncludeComponent(
						"pweb.tenderix:lot.filter",
						"list",
						Array(
							"CACHE_TIME" => "3600000",
							"CACHE_TYPE" => "A",
							"FILTER_FIELDS" => array(0=>"SECTION_ID",1=>"COMPANY_ID",2=>"TYPE",3=>"TITLE",4=>"ID",5=>"DATE_START",6=>"DATE_END",7=>"ARCHIVE_LOT",8=>"USER",),
							"FILTER_NAME" => "arrFilterLot"
						)
					);?>
				</div>
			</div>
			 <?$APPLICATION->IncludeComponent(
				"pweb.tenderix:lot.list",
				"list",
				Array(
					"ACTIVE_DATE" => "N",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_ADDITIONAL" => "",
					"AJAX_OPTION_HISTORY" => "Y",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"CACHE_TIME" => "3600",
					"CACHE_TYPE" => "A",
					"DETAIL_URL" => "tenders_detail.php?LOT_ID=#LOT_ID#",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"DISPLAY_TOP_PAGER" => "N",
					"FILTER_NAME" => "arrFilterLot",
					"LOTS_COUNT" => "10",
					"PAGER_SHOW_ALL" => "Y",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_TEMPLATE" => "",
					"PAGER_TITLE" => "Лоты",
					"PROPOSAL_URL" => "proposal.php?LOT_ID=#LOT_ID#",
					"SET_TITLE" => "Y",
					"SORT_BY" => "ID",
					"SORT_ORDER" => "desc"
				),
			false,
			Array(
				'ACTIVE_COMPONENT' => 'Y'
			)
			);?>
	</div>
</div>

</div><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>