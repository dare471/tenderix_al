<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?><div style="overflow-x:auto !important; overflow-y:hidden !important;">

    <? $format = $arParams["FORMAT"];
    switch ($arParams["UNIT"]) {
        case "r":
        $unit = 1;
        $arResult["UNIT_NAME"] = GetMessage("PW_TD_R");
        break;
        case "tr":
        $unit = 1000;
        $arResult["UNIT_NAME"] = GetMessage("PW_TD_TR");
        break;
        case "mr":
        $unit = 1000000;
        $arResult["UNIT_NAME"] = GetMessage("PW_TD_MR");
        break;
    }
	
	$year = (isset($_REQUEST["year"]) ? intval($_REQUEST["year"]) : date('Y'));
    ?>

<div class="filter">
    <form method="POST" name="filter_statistic" action="">
	</br>
    Выберите год: 
	<select name="year" class="form-control input-sm">
	<?foreach($arResult["YEARS_REGISTER"] as $y):?>
		<option <?if ($y == $year) echo 'selected '?> value="<?=$y;?>"><?=$y;?></option>
	<?endforeach;?>
	</select>
	</br>
	Выберите разделы:
	<div style="display: inline-block;">
		<? $sections_id = array(); ?>
		<select class="form-control input-sm" name="SECTION_ID[]" id="section_ids" <?= $dis_arch; ?> multiple size="10">
			<? $selected0 = $_REQUEST["SECTION_ID"][0] == 0 ? " selected" : ""; ?>
			<option <?= $selected0; ?> id="secid_0" value="0">Все</option>
			<? foreach ($arResult["SECTION_ARR"][0] as $sec): ?>
				<? foreach($_REQUEST["SECTION_ID"] as $rksec => $rvsec) {
					if($rvsec == $sec["ID"]) {
						$selected = "selected";
						break;
					} else {
						$selected = "";
					}
				} ?>
				<option <?= $selected; ?> id="secid_<?= $sec["ID"] ?>" onclick='$("#secid_0").prop("selected", false);' value="<?= $sec["ID"] ?>"><?= $sec["TITLE"] ?></option>
			<? endforeach; ?>
			<? foreach ($arResult["CATALOG"] as $cat_id => $cat_name) : ?>
				<optgroup label="<?= $cat_name ?>">
					<? foreach ($arResult["SECTION_ARR"][$cat_id] as $sec): ?>
						<? foreach($_REQUEST["SECTION_ID"] as $rksec => $rvsec) {
							if($rvsec == $sec["ID"]) {
								$selected = "selected";
								break;
							} else {
								$selected = "";
							}
						} ?>
						<option <?= $selected; ?> id="secid_<?= $sec["ID"] ?>" value="<?= $sec["ID"] ?>"><?= $sec["TITLE"] ?></option>
					<? endforeach; ?>
				</optgroup>
			<? endforeach; ?>
		</select>

		<script>
			$("#secid_0").on('click', function() {
				for(var i=1;i<$("select[id=section_ids] option").size()-1; i++) {
					$("#secid_"+i).prop("selected", false);
				}
			});
		</script>

        </div>
        <br/>
        <input class="btn btn-primary btn-block btn-sm" type="submit" value="Показать" name="filter_submit" style="display:inline; width:10%">
        <a href="<?=$_SERVER['PHP_SELF'] ?>" class="btn btn-default btn-block btn-sm" type="submit" name="filter_reset" style="display:inline; width:10%">Сбросить</a>
    </form>
</div>


<table style="margin-top:20px;" class="table table-striped table-bordered table-hover table-condensed statistic">
    <tbody>
        <tr>
            <th width="20px;" style="text-align: center;"><?=GetMessage("PW_TD_N")?></th>
            <th width="150px;" style="text-align: center;"><?=GetMessage("PW_TD_NAME_POS")?></th>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
                <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                    <th width="50px" style="text-align: center;"><?=$vm;?></th>
                    <? if($km >= date('n') && $year == date('Y')) {
                       break;
                    }?>
                <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
                <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
                    <th width="50px" style="text-align: center;"><?=$i; ?></th>
                <? } ?>
            <? } ?>
        </tr>
        <tr>
            <td style="text-align: center;">1</td>
            <td><?=GetMessage("PW_TD_ALL_LOTS")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO"][$km]; ?></b></td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO"][$i]; ?></b></td>
                <? } ?>
            <? } ?>
        </tr>
        <!-- Закупка и конкурс -->
        <tr>
            <td style="text-align: center;"></td>
            <td><?=GetMessage("PW_TD_ZAKUP_ALL")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"> <b><?=$arResult["LOTS_ITOGO_N"][$km]; ?></b> ( <?=round(($arResult["LOTS_ITOGO_N"][$km]/$arResult["LOTS_ITOGO"][$km])*100, 2); ?>% )</td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_N"][$i]; ?></b> ( <?=round(($arResult["LOTS_ITOGO_N"][$i]/$arResult["LOTS_ITOGO"][$i])*100, 2); ?>% )</td>
                <? } ?>
            <? } ?>
        </tr>
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align:right;"><?=GetMessage("PW_TD_ZAKUP_LOT_PRICE")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center; white-space: nowrap;"><b><?=CTenderix::formatPrice($arResult["LOTS_PRICE_N"][$km], $format); ?></b></td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center; white-space: nowrap;"><b><?=CTenderix::formatPrice($arResult["LOTS_PRICE_N"][$i], $format); ?></b></td>
                <? } ?>
            <? } ?>
        </tr>
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align:right;"><?=GetMessage("PW_TD_ZAKUP_MIN_PR")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"><b><?=$arResult["LOTS_MIN_PRICE_N"][$km]; ?></b></td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_MIN_PRICE_N"][$i]; ?></b></td>
                <? } ?>
            <? } ?>
        </tr>
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align:right;"><?=GetMessage("PW_TD_ZAKUP_EFFECT")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;">
                    <b><?=$arResult["LOTS_EFFECT_PRICE_N"][$km]; ?></b> <br/>
                    ( <?=round($arResult["LOTS_EFFECT_P_PRICE_N"][$km],2); ?>% )
                </td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;">
                <b><?=$arResult["LOTS_EFFECT_PRICE_N"][$i]; ?></b> <br/>
                ( <?=round($arResult["LOTS_EFFECT_P_PRICE_N"][$i],2); ?>% )
            </td>
                <? } ?>
            <? } ?>
        </tr>

        <!-- Продажа -->
        <tr>
            <td style="text-align: center;"></td>
            <td><?=GetMessage("PW_TD_PROD_ALL")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"> <b><?=$arResult["LOTS_ITOGO_P"][$km] ?></b> ( <?=round(($arResult["LOTS_ITOGO_P"][$km]/$arResult["LOTS_ITOGO"][$km])*100, 2); ?>% )</td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_P"][$i];?></b> (<?=round(($arResult["LOTS_ITOGO_P"][$i]/$arResult["LOTS_ITOGO"][$i])*100, 2); ?>% )</td>
                <? } ?>
            <? } ?>
        </tr>
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align:right;"><?=GetMessage("PW_TD_PROD_LOT_PRICE")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"><b><?=CTenderix::formatPrice($arResult["LOTS_PRICE_P"][$km], $format); ?></b></td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=CTenderix::formatPrice($arResult["LOTS_PRICE_P"][$i], $format); ?></b></td>
                <? } ?>
            <? } ?>
        </tr>
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align:right;"><?=GetMessage("PW_TD_PROD_MIN_PR")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"><b><?=$arResult["LOTS_MIN_PRICE_P"][$km]; ?></b></td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_MIN_PRICE_P"][$i]; ?></b></td>
                <? } ?>
            <? } ?>
        </tr>
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align:right;"><?=GetMessage("PW_TD_PROD_EFFECT")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;">
                    <b><?=$arResult["LOTS_EFFECT_PRICE_P"][$km]; ?></b> <br/>
                    ( <?=round($arResult["LOTS_EFFECT_P_PRICE_P"][$km],2); ?>% )
                </td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;">
                <b><?=$arResult["LOTS_EFFECT_PRICE_P"][$i]; ?></b> <br/>
                ( <?=round($arResult["LOTS_EFFECT_P_PRICE_P"][$i],2); ?>% )
            </td>
                <? } ?>
            <? } ?>
        </tr>

        <tr>
            <td style="text-align: center;">2</td>
            <td style="text-align:left;"><?=GetMessage("PW_TD_PROD_ACTIVE")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_ACTIVE"][$km];?></b> ( <?=round(($arResult["LOTS_ITOGO_ACTIVE"][$km]/$arResult["LOTS_ITOGO"][$km])*100, 2); ?>% )</td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_ACTIVE"][$i]; ?></b> ( <?=round(($arResult["LOTS_ITOGO_ACTIVE"][$i]/$arResult["LOTS_ITOGO"][$i])*100, 2); ?>% )</td>
                <? } ?>
            <? } ?>
        </tr>
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align:right;"><?=GetMessage("PW_TD_ZAKUP_ALL")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_N_ACTIVE"][$km]; ?></b> ( <?=round(($arResult["LOTS_ITOGO_N_ACTIVE"][$km]/$arResult["LOTS_ITOGO"][$km])*100, 2); ?>% )</td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_N_ACTIVE"][$i]; ?></b> ( <?=round(($arResult["LOTS_ITOGO_N_ACTIVE"][$i]/$arResult["LOTS_ITOGO"][$i])*100, 2); ?>% )</td>
                <? } ?>
            <? } ?>
        </tr>
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align:right;"><?=GetMessage("PW_TD_PROD_ALL")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_P_ACTIVE"][$km]; ?></b> ( <?=round(($arResult["LOTS_ITOGO_P_ACTIVE"][$km]/$arResult["LOTS_ITOGO"][$km])*100, 2); ?>% )</td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_P_ACTIVE"][$i] ?></b> ( <?=round(($arResult["LOTS_ITOGO_P_ACTIVE"][$i]/$arResult["LOTS_ITOGO"][$i])*100, 2); ?>% )</td>
                <? } ?>
            <? } ?>
        </tr>

        <tr>
            <td style="text-align: center;">3</td>
            <td style="text-align:left;"><?=GetMessage("PW_TD_PROD_FAIL")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_FAIL"][$km]; ?></b> ( <?=round(($arResult["LOTS_ITOGO_FAIL"][$km]/$arResult["LOTS_ITOGO"][$km])*100, 2); ?>% )</td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_FAIL"][$i]; ?></b> ( <?=round(($arResult["LOTS_ITOGO_FAIL"][$i]/$arResult["LOTS_ITOGO"][$i])*100, 2); ?>% )</td>
                <? } ?>
            <? } ?>
        </tr>
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align:right;"><?=GetMessage("PW_TD_ZAKUP_ALL")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_N_FAIL"][$km] ?></b> ( <?=round(($arResult["LOTS_ITOGO_N_FAIL"][$km]/$arResult["LOTS_ITOGO"][$km])*100, 2); ?>% )</td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_N_FAIL"][$i]; ?></b> ( <?=round(($arResult["LOTS_ITOGO_N_FAIL"][$i]/$arResult["LOTS_ITOGO"][$i])*100, 2); ?>% )</td>
                <? } ?>
            <? } ?>
        </tr>
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align:right;"><?=GetMessage("PW_TD_PROD_ALL")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_P_FAIL"][$km]; ?></b> ( <?=round(($arResult["LOTS_ITOGO_P_FAIL"][$km]/$arResult["LOTS_ITOGO"][$km])*100, 2); ?>% )</td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_P_FAIL"][$i]; ?></b> ( <?=round(($arResult["LOTS_ITOGO_P_FAIL"][$i]/$arResult["LOTS_ITOGO"][$i])*100, 2); ?>% )</td>
                <? } ?>
            <? } ?>
        </tr>

        <tr>
            <td style="text-align: center;">4</td>
            <td style="text-align:left;"><?=GetMessage("PW_TD_PROD_WIN")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_WIN"][$km] ?></b> ( <?=round(($arResult["LOTS_ITOGO_WIN"][$km]/$arResult["LOTS_ITOGO"][$km])*100, 2); ?>% )</td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_WIN"][$i]?></b> ( <?=round(($arResult["LOTS_ITOGO_WIN"][$i]/$arResult["LOTS_ITOGO"][$i])*100, 2); ?>% )</td>
                <? } ?>
            <? } ?>
        </tr>
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align:right;"><?=GetMessage("PW_TD_ZAKUP_ALL")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_N_WIN"][$km];?></b> ( <?=round(($arResult["LOTS_ITOGO_N_WIN"][$km]/$arResult["LOTS_ITOGO"][$km])*100, 2); ?>% )</td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_N_WIN"][$i];?></b> ( <?=round(($arResult["LOTS_ITOGO_N_WIN"][$i]/$arResult["LOTS_ITOGO"][$i])*100, 2); ?>% )</td>
                <? } ?>
            <? } ?>
        </tr>
        <tr>
            <td style="text-align: center;"></td>
            <td style="text-align:right;"><?=GetMessage("PW_TD_PROD_ALL")?></td>
            <? if($_REQUEST["MIN_YEAR"] == $_REQUEST["MAX_YEAR"] || !isset($_REQUEST["MIN_YEAR"]) || !isset($_REQUEST["MAX_YEAR"])) { ?>
            <? foreach($arResult["MONTHS"] as $km => $vm) { ?>
                <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_P_WIN"][$km]; ?></b> ( <?=round(($arResult["LOTS_ITOGO_P_WIN"][$km]/$arResult["LOTS_ITOGO"][$km])*100, 2); ?>% )</td>
                <? if($km >= date('n') && $year == date('Y')) {
                    break;
                }?>
            <? } ?>
            <? } ?>
            <? if($_REQUEST["MIN_YEAR"] != $_REQUEST["MAX_YEAR"]) { ?>
            <? for($i=$_REQUEST["MIN_YEAR"]; $i <= $_REQUEST["MAX_YEAR"]; $i++ ) { ?>
            <td style="text-align: center;"><b><?=$arResult["LOTS_ITOGO_P_WIN"][$i];?></b> ( <?=round(($arResult["LOTS_ITOGO_P_WIN"][$i]/$arResult["LOTS_ITOGO"][$i])*100, 2); ?>% )</td>
                <? } ?>
            <? } ?>
        </tr>
    </tbody>
</table>

</div>

<script src="/bitrix/js/pweb.tenderix/amcharts/3.20.2/amcharts.js" type="text/javascript"></script>
<script src="/bitrix/js/pweb.tenderix/amcharts/3.20.2/serial.js" type="text/javascript"></script>

<script>
    AmCharts.loadJSON = function(url) {
        // create the request
        if (window.XMLHttpRequest) {
            // IE7+, Firefox, Chrome, Opera, Safari
            var request = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            var request = new ActiveXObject('Microsoft.XMLHTTP');
        }

        // load it
        // the last "false" parameter ensures that our code will wait before the
        // data is loaded
        request.open('GET', url, false);
        request.send();

        // parse adn return the output
        return eval(request.responseText);
    };
</script>

<div class="row">
<div class="col-md-6">


	<!-- chart container -->

	<div id="chartdiv" style="width: 550px; height: 300px;"></div>

	<!-- the chart code -->
	<script>
		$(document).ready(function(){
			$.ajax({
				url: "<?= $templateFolder ?>/ajax.php",
				type: "POST",
				dataType: "json",
				data: "action=lots_itogo&year=<?=$year?>",
				success: function (data) {
	console.log(data)
					if (data == false) {
						alert('Ошибка');
						return;
					} else {

						var json0 = [];
						for (var i = 0; i < data["date"].length; i++) {
							json0.push({
								date : data["date"][i],
								value1 : data["value1"][i]
							});
						}

						//console.log(json0);

						var dateformat = "DD-MM-YYYY";

						var chart = AmCharts.makeChart("chartdiv", {
							"titles": [
								{
									"text": "1. Динамика активности ЭТП",
									"size": 15
								}
							],
							"type": "serial",
							"theme": "light",
							"dataProvider": json0,
							"marginRight": 40,
							"marginLeft": 40,
							"autoMarginOffset": 20,
							"mouseWheelZoomEnabled":true,
							"dataDateFormat": dateformat,
							"valueAxes": [{
								"id": "v1",
								"axisAlpha": 0,
								"position": "left",
								"ignoreAxisWidth":true
							}],
							"balloon": {
								"borderThickness": 1,
								"shadowAlpha": 0
							},
							"graphs": [{
								"id": "g1",
								"balloon":{
									"drop":true,
									"adjustBorderColor":false,
									"color":"#0040FF"
								},
								"bullet": "round",
								"bulletBorderAlpha": 1,
								"bulletColor": "#0040FF",
								"bulletSize": 5,
								"hideBulletsCount": 50,
								"lineThickness": 2,
								"title": "red line",
								"useLineColorForBulletBorder": true,
								"valueField": "value1",
								"balloonText": "<span style='font-size:18px;'>[[value]]</span>"
							}],
							"chartCursor": {
								"pan": true,
								"valueLineEnabled": true,
								"valueLineBalloonEnabled": true,
								"cursorAlpha":1,
								"cursorColor":"#258cbb",
								"limitToGraph":"g1",
								"valueLineAlpha":0.2
							},
							"valueScrollbar":{
								"oppositeAxis":false,
								"offset":50,
								"scrollbarHeight":10
							},
							"categoryField": "date",
							"categoryAxis": {
								"parseDates": true,
								"dashLength": 1,
								"minorGridEnabled": true
							},
							"export": {
								"enabled": true
							}
						});

	//                    chart.addListener("rendered", zoomChart);
	//
	//                    zoomChart();
	//
	//                    function zoomChart() {
	//                        chart.zoomToIndexes(chart.dataProvider.length - 40, chart.dataProvider.length - 1);
	//                    }

					}
				},
				error: function(data) {
					console.log(data);
				}

			});



		});


		$("#chartdiv_filter_submit").click(function(){

			$.ajax({
				url: "<?= $templateFolder ?>/ajax.php",
				type: "POST",
				data: "action=lots_itogo&year=<?=$year?>",
				dataType: "json",
				success: function (data) {
					if (data == 'ERROR') {
						alert('Ошибка');
						return;
					} else {
						var json0 = [];
						for (var i = 0; i < data["date"].length; i++) {
							json0.push({
								date : data["date"][i],
								value1 : data["value1"][i]
							});
						}

						var dateformat;
						if($("#MIN_YEAR_chartdiv").val() != $("#MAX_YEAR_chartdiv").val()) {
							dateformat = "YYYY";
						} else if($("#MIN_YEAR_chartdiv").val() == $("#MAX_YEAR_chartdiv").val()) {
							dateformat = "DD-MM-YYYY";
						}

						var chart = AmCharts.makeChart("chartdiv", {
							"titles": [
								{
									"text": "1. Динамика активности ЭТП",
									"size": 15
								}
							],
							"type": "serial",
							"theme": "light",
							"marginRight": 40,
							"marginLeft": 40,
							"autoMarginOffset": 20,
							"mouseWheelZoomEnabled":true,
							"dataDateFormat": dateformat,
							"valueAxes": [{
								"id": "v1",
								"axisAlpha": 0,
								"position": "left",
								"ignoreAxisWidth":true
							}],
							"balloon": {
								"borderThickness": 1,
								"shadowAlpha": 0
							},
							"graphs": [{
								"id": "g1",
								"balloon":{
									"drop":true,
									"adjustBorderColor":false,
									"color":"#0040FF"
								},
								"bullet": "round",
								"bulletBorderAlpha": 1,
								"bulletColor": "#0040FF",
								"bulletSize": 5,
								"hideBulletsCount": 50,
								"lineThickness": 2,
								"title": "red line",
								"useLineColorForBulletBorder": true,
								"valueField": "value1",
								"balloonText": "<span style='font-size:18px;'>[[value]]</span>"
							}],
							"chartCursor": {
								"pan": true,
								"valueLineEnabled": true,
								"valueLineBalloonEnabled": true,
								"cursorAlpha":1,
								"cursorColor":"#258cbb",
								"limitToGraph":"g1",
								"valueLineAlpha":0.2
							},
							"valueScrollbar":{
								"oppositeAxis":false,
								"offset":50,
								"scrollbarHeight":10
							},
							"categoryField": "date",
							"categoryAxis": {
								"parseDates": true,
								"dashLength": 1,
								"minorGridEnabled": true
							},
							"export": {
								"enabled": true
							},
							"dataProvider": json0
						});

					}
				}
			});

		});

	</script>
</div>

<div class="col-md-6">



<div id="chartdiv1" style="width: 550px; height: 300px;"></div>
<script>
    $(document).ready(function() {
        $.ajax({
            url: "<?= $templateFolder ?>/ajax.php",
            type: "POST",
            dataType: "json",
            data: "action=lots_price&year=<?=$year?>",
            success: function (data) {
                if (data == false) {
                    alert('Ошибка');
                    return;
                } else {
                    var json1 = [];
                    for (var i = 0; i < data["month"].length; i++) {
                        json1.push({
                            month : data["month"][i],
                            mln : data["mln"][i],
                            color: data["color"][i]
                        });
                    }

                    var chart1 = AmCharts.makeChart("chartdiv1", {
                        "titles": [
                            {
                                "text": "2. Размещено заказов в млн. тенге",
                                "size": 15
                            }
                        ],
                        "type": "serial",
                        "theme": "light",
                        "marginRight": 70,
                        "dataProvider": json1,
                        "valueAxes": [{
                            "axisAlpha": 0,
                            "position": "left"
                        }],
                        "startDuration": 1,
                        "graphs": [{
                            "balloonText": "<b>[[category]]: [[value]]</b>",
                            "fillColorsField": "color",
                            "fillAlphas": 0.9,
                            "lineAlpha": 0.2,
                            "type": "column",
                            "valueField": "mln"
                        }],
                        "chartCursor": {
                            "categoryBalloonEnabled": false,
                            "cursorAlpha": 0,
                            "zoomable": false
                        },
                        "categoryField": "month",
                        "categoryAxis": {
                            "gridPosition": "start",
                            "labelRotation": 45
                        },
                        "export": {
                            "enabled": true
                        }

                    });
                }
            }
        });
    });

    $("#chartdiv1_filter_submit").click(function() {
        var json = "";

        $.ajax({
            url: "<?= $templateFolder ?>/ajax.php",
            type: "POST",
            data: "action=lots_price&year=<?=$year?>",
            dataType: "json",
            success: function (data) {
                if (data == 'ERROR') {
                    alert('Ошибка');
                    return;
                } else {
                    var json1 = [];
                    for (var i = 0; i < data["month"].length; i++) {
                        json1.push({
                            month : data["month"][i],
                            mln : data["mln"][i],
                            color: data["color"][i]
                        });
                    }

                    var chart1 = AmCharts.makeChart("chartdiv1", {
                        "titles": [
                            {
                                "text": "2. Размещено заказов в млн. тенге",
                                "size": 15
                            }
                        ],
                        "type": "serial",
                        "theme": "light",
                        "marginRight": 70,
                        "dataProvider": json1,
                        "valueAxes": [{
                            "axisAlpha": 0,
                            "position": "left"
                        }],
                        "startDuration": 1,
                        "graphs": [{
                            "balloonText": "<b>[[category]]: [[value]]</b>",
                            "fillColorsField": "color",
                            "fillAlphas": 0.9,
                            "lineAlpha": 0.2,
                            "type": "column",
                            "valueField": "mln"
                        }],
                        "chartCursor": {
                            "categoryBalloonEnabled": false,
                            "cursorAlpha": 0,
                            "zoomable": false
                        },
                        "categoryField": "month",
                        "categoryAxis": {
                            "gridPosition": "start",
                            "labelRotation": 45
                        },
                        "export": {
                            "enabled": true
                        }

                    });

                }
            }
        });
    });


</script>
</div>



<div class="col-md-6">


<div id="chartdiv5" style="height: 300px;"></div>
<script>
    $(document).ready(function() {
        $.ajax({
            url: "<?= $templateFolder ?>/ajax.php",
            type: "POST",
            dataType: "json",
            data: "action=effect_lots&year=<?=$year?>",
            success: function (data) {
                if (data == false) {
                    alert('Ошибка');
                    return;
                } else {
                    var json5 = [];
                    for (var i = 0; i < data["year"].length; i++) {
                        json5.push({
                            year: data["year"][i],
                            itogo_fail: data["itogo_fail"][i],
                            itogo_win: data["itogo_win"][i]
                        });
                    }

//                    json5.push({
//                        lots_text: "Признан не состоявшимся",
//                        lots: data["itogo_fail"]
//                    });
//                    json5.push({
//                        lots_text: "Победитель определён",
//                        lots: data["itogo_win"]
//                    });

                    //console.log(json5);

                    var chart = AmCharts.makeChart("chartdiv5", 
					{
						"titles": [
								{
									"text": "3. Победитель определен ",
									"size": 15
								}
							],
                        "type": "serial",
                        "theme": "light",
                        "legend": {
                            "horizontalGap": 10,
                            "maxColumns": 1,
                            "position": "right",
                            "useGraphSettings": true,
                            "markerSize": 10
                        },
                        "dataProvider": json5,
                        "valueAxes": [{
                            "stackType": "100%",
                            "axisAlpha": 0,
                            "gridAlpha": 0,
                            "labelsEnabled": false,
                            "position": "left"
                        }],
                        "graphs": [{
                            "balloonText": "[[title]], [[category]]<br><span style='font-size:14px;'><b>[[value]]</b> ([[percents]]%)</span>",
                            "fillAlphas": 0.9,
                            "fontSize": 11,
                            "labelText": "[[percents]]%",
                            "lineAlpha": 0.5,
                            "title": "Признан не состоявшимся",
                            "type": "column",
                            "valueField": "itogo_fail"
                        }, {
                            "balloonText": "[[title]], [[category]]<br><span style='font-size:14px;'><b>[[value]]</b> ([[percents]]%)</span>",
                            "fillAlphas": 0.9,
                            "fontSize": 11,
                            "labelText": "[[percents]]%",
                            "lineAlpha": 0.5,
                            "title": "Победитель определён",
                            "type": "column",
                            "valueField": "itogo_win"
                        }],
                        "rotate": true,
                        "categoryField": "year",
                        "categoryAxis": {
                            "gridPosition": "start",
                            "axisAlpha": 0,
                            "gridAlpha": 0,
                            "position": "left"
                        },
                        "export": {
                            "enabled": true
                        }

                    });



//                    var chart5 = AmCharts.makeChart("chartdiv5", {
//                        "titles": [
//                            {
//                                "text": "4. Эффективность торговых процедур.",
//                                "size": 15
//                            }
//                        ],
//                        "type": "pie",
//                        "startDuration": 0,
//                        "theme": "light",
//                        "addClassNames": true,
//                        "legend":{
//                            "position":"bottom",
//                            "marginBottom":100,
//                            "autoMargins":false
//                        },
//                        "innerRadius": "30%",
//                        "defs": {
//                            "filter": [{
//                                "id": "shadow",
//                                "width": "200%",
//                                "height": "200%",
//                                "feOffset": {
//                                    "result": "offOut",
//                                    "in": "SourceAlpha",
//                                    "dx": 0,
//                                    "dy": 0
//                                },
//                                "feGaussianBlur": {
//                                    "result": "blurOut",
//                                    "in": "offOut",
//                                    "stdDeviation": 5
//                                },
//                                "feBlend": {
//                                    "in": "SourceGraphic",
//                                    "in2": "blurOut",
//                                    "mode": "normal"
//                                }
//                            }]
//                        },
//                        "dataProvider":  [{ "lots_text":"Признан не состоявшимся",  "lots":2}, { "lots_text":"Победитель определён",  "lots":5}],
//                        "valueField": "lots",
//                        "titleField": "lots_text",
//                        "export": {
//                            "enabled": true
//                        }
//                    });
//
//                    chart5.addListener("init", handleInit);
//
//                    chart5.addListener("rollOverSlice", function(e) {
//                        handleRollOver(e);
//                    });
//
//                    function handleInit(){
//                        chart5.legend.addListener("rollOverItem", handleRollOver);
//                    }
//
//                    function handleRollOver(e){
//                        var wedge = e.dataItem.wedge.node;
//                        wedge.parentNode.appendChild(wedge);
//                    }
                }
            },
            error: function(data) {
                //console.log(data);
            }
        });
    });

    $("#chartdiv5_filter_submit").click(function() {
        var json = "";

        $.ajax({
            url: "<?= $templateFolder ?>/ajax.php",
            type: "POST",
            data: "action=effect_lots&year=<?=$year?>",
            dataType: "json",
            success: function (data) {
                if (data == 'ERROR') {
                    alert('Ошибка');
                    return;
                } else {
                    var json5 = [];
                    for (var i = 0; i < data["year"].length; i++) {
                        json5.push({
                            year: data["year"][i],
                            itogo_fail: data["itogo_fail"][i],
                            itogo_win: data["itogo_win"][i]
                        });
                    }

//                    json5.push({
//                        lots_text: "Признан не состоявшимся",
//                        lots: data["itogo_fail"]
//                    });
//                    json5.push({
//                        lots_text: "Победитель определён",
//                        lots: data["itogo_win"]
//                    });

                    //console.log(json5);

                    var chart = AmCharts.makeChart("chartdiv5", {
                        "type": "serial",
                        "theme": "light",
                        "legend": {
                            "horizontalGap": 10,
                            "maxColumns": 1,
                            "position": "right",
                            "useGraphSettings": true,
                            "markerSize": 10
                        },
                        "dataProvider": json5,
                        "valueAxes": [{
                            "stackType": "100%",
                            "axisAlpha": 0,
                            "gridAlpha": 0,
                            "labelsEnabled": false,
                            "position": "left"
                        }],
                        "graphs": [{
                            "balloonText": "[[title]], [[category]]<br><span style='font-size:14px;'><b>[[value]]</b> ([[percents]]%)</span>",
                            "fillAlphas": 0.9,
                            "fontSize": 11,
                            "labelText": "[[percents]]%",
                            "lineAlpha": 0.5,
                            "title": "Признан не состоявшимся",
                            "type": "column",
                            "valueField": "itogo_fail"
                        }, {
                            "balloonText": "[[title]], [[category]]<br><span style='font-size:14px;'><b>[[value]]</b> ([[percents]]%)</span>",
                            "fillAlphas": 0.9,
                            "fontSize": 11,
                            "labelText": "[[percents]]%",
                            "lineAlpha": 0.5,
                            "title": "Победитель определён",
                            "type": "column",
                            "valueField": "itogo_win"
                        }],
                        "rotate": true,
                        "categoryField": "year",
                        "categoryAxis": {
                            "gridPosition": "start",
                            "axisAlpha": 0,
                            "gridAlpha": 0,
                            "position": "left"
                        },
                        "export": {
                            "enabled": true
                        }

                    });
                }
            }
        });
    });
</script>
</div>
<div class="col-md-6">


<div id="chartdiv3" style="width: 550px; height: 300px; display: inline-block;"></div>

<script>
    $(document).ready(function() {
        $.ajax({
            url: "<?= $templateFolder ?>/ajax.php",
            type: "POST",
            dataType: "json",
            data: "action=count_users&year=<?=$year?>&type=graph",
            success: function (data) {
                if (data == false) {
                    alert('Ошибка');
                    return;
                } else {
                    var json3 = [];
                    for (var i = 0; i < data["year"].length; i++) {
                        json3.push({
                            year: data["year"][i],
                            users: data["users"][i],
                            
                        });
                    }

                    //console.log(json3);

                    var chart3 = AmCharts.makeChart("chartdiv3", {
                        "titles": [
                            {
                                "text": "4. Динамика регистрации пользователей.",
                                "size": 15
                            }
                        ],
                        "type": "serial",
                        "theme": "light",
                        "marginRight": 70,
                        "dataProvider": json3,
                        "valueAxes": [{
                            "axisAlpha": 0,
                            "position": "left"
                        }],
                        "startDuration": 1,
                        "graphs": [{
                            "balloonText": "<b>[[category]]: [[value]]</b>",
                            "fillColorsField": "color",
                            "fillAlphas": 0.9,
                            "lineAlpha": 0.2,
                            "type": "column",
                            "valueField": "users"
                        }],
                        "chartCursor": {
                            "categoryBalloonEnabled": false,
                            "cursorAlpha": 0,
                            "zoomable": false
                        },
                        "categoryField": "year",
                        "categoryAxis": {
                            "gridPosition": "start",
                            "labelRotation": 45
                        },
                        "export": {
                            "enabled": true
                        }

                    });
                }
            }
        });
    });
</script>

</div>



</div>