<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
//__($arResult);
?>
<div id="top_filter">
	<div class="container">
	<div class="row">
		<div class="buttom_filter">
			<div class="container">
			<span data-toggle="collapse" data-target="#form_filter" aria-expanded="true" aria-controls="form_filter">
			<a class="btn_filter" data-toggle="tooltip" data-placement="right" title="Раскройте фильтр и выберите параметры для поиска нужных лотов">Фильтр&nbsp;<i style="font-size:18px; " class="fa fa-filter"></i></a>
			</span>
			</div>
		</div>
		<br />
		<form id="form_filter" class="collapse in" method = "get" action = "<?= $arResult["FORM_ACTION"] ?>" name = "<?= $arResult["FILTER_NAME"] ?>_form">
		
		<?//echo "<pre>"; print_r($arResult['FORM']); echo "</pre>";?>
			
			<div class="col-md-2">
				<?if($arResult['FORM']['SECTION_ID']) {?>
				<div class="form-group">
						<select class="form-control input-sm" name="<?= $arResult['FORM']['SECTION_ID']["INPUT_NAME"] ?>" id="FILTER_<?= $arResult['FORM']['SECTION_ID']["FIELD_CODE"] ?>">
							<option value=""><?= $arResult['FORM']['SECTION_ID']['LABEL_NAME']?></option>
							<? foreach ($arResult['FORM']['SECTION_ID']["SELECT_VALUE"]["reference_id"] as $key => $value): ?>
								<option<?= $value == $arResult['FORM']['SECTION_ID']["INPUT_VALUE"] ? " selected" : "" ?> value="<?= $value ?>"><?= $arResult['FORM']['SECTION_ID']["SELECT_VALUE"]["reference"][$key] ?></option>
							<? endforeach ?>
						</select>
				</div>
				<?}?>
				<?if($arResult['FORM']['COMPANY_ID']) {?>
					<div class="form-group">
						<select class="form-control input-sm" name="<?= $arResult['FORM']['COMPANY_ID']["INPUT_NAME"] ?>" id="FILTER_<?= $arResult['FORM']['COMPANY_ID']["FIELD_CODE"] ?>">
							<option value=""><?= $arResult['FORM']['COMPANY_ID']['LABEL_NAME']?></option>
							<? foreach ($arResult['FORM']['COMPANY_ID']["SELECT_VALUE"]["reference_id"] as $key => $value): ?>
								<option<?= $value == $arResult['FORM']['COMPANY_ID']["INPUT_VALUE"] ? " selected" : "" ?> value="<?= $value ?>"><?= $arResult['FORM']['COMPANY_ID']["SELECT_VALUE"]["reference"][$key] ?></option>
							<? endforeach ?>
						</select>
					</div>
				<?}?>
			</div>
			<div class="col-md-6">
				<div class="row">
				<div class="col-md-12">
				<div class="form-group">
				<?if($arResult['FORM']['TITLE']) {?>
				<input class="form-control input-sm" type="text" name="<?= $arResult['FORM']['TITLE']['INPUT_NAME'] ?>" id="FILTER_<?= $arResult['FORM']['TITLE']['FIELD_CODE'] ?>" placeholder="<?= $arResult['FORM']['TITLE']['LABEL_NAME'] ?>" value="<?= $arResult['FORM']['TITLE']["INPUT_VALUE"] ?>" />
				<?}?>
				</div>
				</div>
				<div class="col-md-3">
				<?if($arResult['FORM']['TYPE']) {?>
					<div class="form-group">
						 <select class="form-control input-sm" name="<?= $arResult['FORM']['TYPE']["INPUT_NAME"] ?>" id="FILTER_<?= $arResult['FORM']['TYPE']["FIELD_CODE"] ?>">
							<option value=""><?= $arResult['FORM']['TYPE']['LABEL_NAME']?></option>
							<? foreach ($arResult['FORM']['TYPE']["SELECT_VALUE"]["reference_id"] as $key => $value): ?>
								<option<?= $value == $arResult['FORM']['TYPE']["INPUT_VALUE"] ? " selected" : "" ?> value="<?= $value ?>"><?= $arResult['FORM']['TYPE']["SELECT_VALUE"]["reference"][$key] ?></option>
							<? endforeach ?>
						</select> 
					</div>
				<?}?>
				</div>
				<div class="col-md-3">
				<?if($arResult['FORM']['ID']) {?>
				<div class="form-group">
				<input class="form-control input-sm" type="text" name="<?= $arResult['FORM']['ID']['INPUT_NAME'] ?>" id="FILTER_<?= $arResult['FORM']['ID']['FIELD_CODE'] ?>" placeholder="<?= $arResult['FORM']['ID']["LABEL_NAME"] ?>" value="<?= $arResult['FORM']['ID']["INPUT_VALUE"] ?>" />
				</div>
				<?}?>
				</div>
				<div class="col-md-6">
					<? if ($arResult['T_RIGHT'] != 'P'): ?>
				<?if($arResult['FORM']['ARCHIVE_LOT']) {?>
					<div class="checkbox-inline">
					<label>
					<input id="FILTER_<?= $arResult['FORM']['ARCHIVE_LOT']["FIELD_CODE"] ?>" type="checkbox"<?= $arResult['FORM']['ARCHIVE_LOT']["INPUT_VALUE"] == "Y" ? " checked" : ""; ?> name="<?= $arResult['FORM']['ARCHIVE_LOT']["INPUT_NAME"] ?>" value="Y" />
					<?= $arResult['FORM']['ARCHIVE_LOT']["LABEL_NAME"] ?>
					</label>
					</div>
				<?}?>
				<?if($arResult['FORM']['USER'] && $USER->IsAuthorized()) {?>
				<div class="checkbox-inline">
				<label>
					<input id="FILTER_<?= $arResult['FORM']['USER']["FIELD_CODE"] ?>" type="checkbox"<?= $arResult['FORM']['USER']["INPUT_VALUE"] == "Y" ? " checked" : ""; ?> name="<?= $arResult['FORM']['USER']["INPUT_NAME"] ?>" value="Y" />
					<? if ($arResult['T_RIGHT'] == 'W'):?>
						Не опубликованные
					<?else:?>
					<?= $arResult['FORM']['USER']["LABEL_NAME"] ?>
					<?endif;?>
				</label>
					</div>
				<?}?>
				<?endif;?>
				</div>
				</div>
			</div>
			<div class="col-md-2">
			<?if($arResult['FORM']['DATE_START']) {?>
						<div class="form-group form-date">
							<input class="form-control input-sm" type="text" name="<?= $arResult['FORM']['DATE_START']["INPUT_NAME"] ?>" id="FILTER_<?= $arResult['FORM']['DATE_START']["FIELD_CODE"] ?>" placeholder="<?= $arResult['FORM']['DATE_START']['LABEL_NAME']?>" value="<?= $arResult['FORM']['DATE_START']["INPUT_VALUE"] ?>" />
							
							<?
							$APPLICATION->IncludeComponent(
									'bitrix:main.calendar', '', array(
								'SHOW_INPUT' => 'N',
								'FORM_NAME' => $arResult["FILTER_NAME"] . '_form',
								'INPUT_NAME' => $arResult['FORM']['DATE_START']["INPUT_NAME"],
								'INPUT_VALUE' => $arResult['FORM']['DATE_START']["INPUT_VALUE"],
								'SHOW_TIME' => 'Y',
								'HIDE_TIMEBAR' => 'N'
									), null, array('HIDE_ICONS' => 'Y')
							);
							?>
						</div>
			<?}?>
			<?if($arResult['FORM']['DATE_END']) {?>
						<div class="form-group form-date">
							<input class="form-control input-sm" type="text" name="<?= $arResult['FORM']['DATE_END']["INPUT_NAME"] ?>" id="FILTER_<?= $arResult['FORM']['DATE_END']["FIELD_CODE"] ?>" placeholder="<?= $arResult['FORM']['DATE_END']['LABEL_NAME']?>" value="<?= $arResult['FORM']['DATE_END']["INPUT_VALUE"] ?>" />
							<?
							$APPLICATION->IncludeComponent(
									'bitrix:main.calendar', '', array(
								'SHOW_INPUT' => 'N',
								'FORM_NAME' => $arResult["FILTER_NAME"] . '_form',
								'INPUT_NAME' => $arResult['FORM']['DATE_END']["INPUT_NAME"],
								'INPUT_VALUE' => $arResult['FORM']['DATE_END']["INPUT_VALUE"],
								'SHOW_TIME' => 'Y',
								'HIDE_TIMEBAR' => 'N'
									), null, array('HIDE_ICONS' => 'Y')
							);
							?>
						</div>
			<?}?>
			</div>
			<div class="col-md-2">
				<div class="form-group">
				<input class="btn btn-primary btn-block btn-sm" type="submit" name="filter_lot_submit" value="<?= GetMessage("PW_TD_FILTER_SUBMIT_FIND") ?>" /> 
				</div>
				<div class="form-group">
				<input class="btn btn-default btn-block btn-sm" type="submit" name="filter_lot_reset" value="<?= GetMessage("PW_TD_FILTER_SUBMIT_RESET") ?>" />    		
				</div>
			</div>
		</form>
	</div>
	</div>
</div>