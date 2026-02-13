<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="/favicon.ico">
		<title><?$APPLICATION->ShowTitle()?></title>
		
		<!-- Js -->
		<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.min.js"></script>
		
		<script src="<?=SITE_TEMPLATE_PATH?>/js/bootstrap.bundle.min.js"></script>
		<script src="<?=SITE_TEMPLATE_PATH?>/js/popper.min.js"></script>
		<script src="<?=SITE_TEMPLATE_PATH?>/js/bootstrap.min.js"></script>
		
		<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.easing.min.js"></script>
		<script src="<?=SITE_TEMPLATE_PATH?>/js/scroll.js"></script>
		<script src="<?=SITE_TEMPLATE_PATH?>/js/user-interface.js"></script>
		
		<!-- Css -->
		<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/bootstrap-reboot.min.css">
		<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/bootstrap-grid.min.css">
		<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/user-interface.css">
		
		<!-- Fonts -->
		<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/fontawesome.min.css">
		<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/regular.min.css">
		<link rel="stylesheet" href="<?=SITE_TEMPLATE_PATH?>/css/solid.min.css">	

		<?$APPLICATION->ShowHead()?>
	</head>
	<body class="">
		<?if ($USER->IsAdmin()):?>
			<div>
				<?$APPLICATION->ShowPanel();?>
			</div>
		<?endif;?>
		<div class="wrapper">
			<div class="content">
				<header class="header">
					
					<div class="container-fluid">
						<div class="container">
							<?$APPLICATION->IncludeComponent(
								"bitrix:menu", 
								"tender", 
								array(
									"COMPONENT_TEMPLATE" => "tender",
									"ROOT_MENU_TYPE" => "top",
									"MENU_CACHE_TYPE" => "N",
									"MENU_CACHE_TIME" => "3600",
									"MENU_CACHE_USE_GROUPS" => "Y",
									"MENU_CACHE_GET_VARS" => array(
									),
									"MAX_LEVEL" => "1",
									"CHILD_MENU_TYPE" => "left",
									"USE_EXT" => "N",
									"DELAY" => "N",
									"ALLOW_MULTI_SELECT" => "N"
								),
								false
							);?>
						</div>
					</div>
					<? if ($USER->IsAuthorized()):?>
						<div class="container-fluid currency-wrapper">
							<div class="container">
								<div class="row">
									<div class="col-md-6">
										<?$APPLICATION->IncludeComponent(
											"bitrix:currency.rates",
											"",
											Array(
												"CACHE_TIME" => "86400",
												"CACHE_TYPE" => "A",
												"CURRENCY_BASE" => "KZT",
												"RATE_DAY" => "",
												"SHOW_CB" => "N",
												"arrCURRENCY_FROM" => array("RUB","USD","EUR","UAH")
											)
										);?>
									</div>
									
									<div class="col-md-6">
										
										<ul class="currency-list pull-right highlighted">
											<li>
												<span class="info_wrapper info_phone">
													<span class="info_text"><i class="fa fa-phone"></i>&nbsp;+7 (771) 005 92 92&nbsp;<span>(менеджер)</span></span>
												</span>
											</li>
											<li>
												<span class="info_wrapper info_phone">
													<span class="info_text"><i class="fa fa-phone"></i>&nbsp;+7 (771) 005 40 04&nbsp;<span>(логист)</span></span>
													
												</span>
											</li>
										</ul>

									</div>
								</div>
								
							</div>
						</div>
					<? endif;?>
				</header>