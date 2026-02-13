<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<ul class="nav navbar-nav">

<?
$previousLevel = 0;
foreach($arResult as $arItem):?>

	<?//echo "<pre>";print_r($arItem);echo "</pre>";?>
	<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
		<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
	<?endif?>

	<?if ($arItem["IS_PARENT"]):?>
		
		<?if ($arItem["DEPTH_LEVEL"] == 1):?>
			<li class="dropdown">
			<a href="<?=$arItem["LINK"]?>" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?=$arItem["TEXT"]?> <span class="caret"></span></a>
			<ul class="dropdown-menu" role="menu">
		<?else:?>
			<li class="dropdown">
			<a href="<?=$arItem["LINK"]?>" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?=$arItem["TEXT"]?> <span class="caret"></span></a>
			<ul class="dropdown-menu" role="menu">
		<?endif?>
	<?else:?>
		<?if ($arItem["PERMISSION"] > "D"):?>

			<?if ($arItem["DEPTH_LEVEL"] == 1):?>
				<li class="<?if ($arItem["SELECTED"]):?> active<?endif?>"><a href="<?=$arItem["LINK"]?>" ><?=$arItem["TEXT"]?></a>
			<?else:?>
				<li class="<?if ($arItem["SELECTED"]):?> active<?endif?>"><a href="<?=$arItem["LINK"]?>" ><?=$arItem["TEXT"]?></a>
			<?endif?>

		<?else:?>

			<?if ($arItem["DEPTH_LEVEL"] == 1):?>
				<li class="<?if ($arItem["SELECTED"]):?> active<?endif?>"><a href="<?=$arItem["LINK"]?>" ><?=$arItem["TEXT"]?></a>
			<?else:?>
				<li class="<?if ($arItem["SELECTED"]):?> active<?endif?>"><a href="<?=$arItem["LINK"]?>" ><?=$arItem["TEXT"]?></a>
			<?endif?>

		<?endif?>

	<?endif?>

	<?$previousLevel = $arItem["DEPTH_LEVEL"];?>

<?endforeach?>

<?if ($previousLevel > 1)://close last item tags?>
	<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
<?endif?>

</ul>
<?endif?>