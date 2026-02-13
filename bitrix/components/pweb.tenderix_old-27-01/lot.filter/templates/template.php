<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>


<div class="t_block_left">            
    <div class="t_block_top">

    </div>
    <div class="t_form_filter">
        <form method = "get" action = "<?= $arResult["FORM_ACTION"] ?>" name = "<?= $arResult["FILTER_NAME"] ?>_form">
            <? foreach ($arResult["ITEMS"] as $arItem): ?>
                <label for="FILTER_<?= $arItem["FIELD_CODE"] ?>" <?= ($arItem["INPUT_TYPE"] == "checkbox") ? "class='checkbox'" : ""; ?>>
                    <? if ($arItem["INPUT_TYPE"] == "checkbox"): ?>
                        <input id="FILTER_<?= $arItem["FIELD_CODE"] ?>" type="checkbox"<?= $arItem["INPUT_VALUE"] == "Y" ? " checked" : ""; ?> name="<?= $arItem["INPUT_NAME"] ?>" value="Y" /> 
                        <?= $arItem["LABEL_NAME"] ?>
                    <? elseif ($arItem["INPUT_TYPE"] == "text"): ?>
                        <?= $arItem["LABEL_NAME"] ?>:
                        <input type="text" name="<?= $arItem["INPUT_NAME"] ?>" id="FILTER_<?= $arItem["FIELD_CODE"] ?>" value="<?= $arItem["INPUT_VALUE"] ?>" />
                    <? elseif ($arItem["INPUT_TYPE"] == "date_start"): ?>
                        <?= $arItem["LABEL_NAME"] ?>:
                        <div>
                            <input type="text" name="<?= $arItem["INPUT_NAME"] ?>" id="FILTER_<?= $arItem["FIELD_CODE"] ?>" value="<?= $arItem["INPUT_VALUE"] ?>" style="width:80% !important;" />
                            <?
                            $APPLICATION->IncludeComponent(
                                    'bitrix:main.calendar', '', array(
                                'SHOW_INPUT' => 'N',
                                'FORM_NAME' => $arResult["FILTER_NAME"] . '_form',
                                'INPUT_NAME' => $arItem["INPUT_NAME"],
                                'INPUT_VALUE' => $arItem["INPUT_VALUE"],
                                'SHOW_TIME' => 'Y',
                                'HIDE_TIMEBAR' => 'N'
                                    ), null, array('HIDE_ICONS' => 'Y')
                            );
                            ?>
                        </div>
                    <? elseif ($arItem["INPUT_TYPE"] == "date_end"): ?>
                        <?= $arItem["LABEL_NAME"] ?>:
                        <div>
                            <input type="text" name="<?= $arItem["INPUT_NAME"] ?>" id="FILTER_<?= $arItem["FIELD_CODE"] ?>" value="<?= $arItem["INPUT_VALUE"] ?>" style="width:80% !important;" />
                            <?
                            $APPLICATION->IncludeComponent(
                                    'bitrix:main.calendar', '', array(
                                'SHOW_INPUT' => 'N',
                                'FORM_NAME' => $arResult["FILTER_NAME"] . '_form',
                                'INPUT_NAME' => $arItem["INPUT_NAME"],
                                'INPUT_VALUE' => $arItem["INPUT_VALUE"],
                                'SHOW_TIME' => 'Y',
                                'HIDE_TIMEBAR' => 'N'
                                    ), null, array('HIDE_ICONS' => 'Y')
                            );
                            ?>
                        </div>
                    <? else: ?>
                        <?= $arItem["LABEL_NAME"] ?>:
                        <select name="<?= $arItem["INPUT_NAME"] ?>" id="FILTER_<?= $arItem["FIELD_CODE"] ?>">
                            <option value="">--</option>
                            <? foreach ($arItem["SELECT_VALUE"]["reference_id"] as $key => $value): ?>
                                <option<?= $value == $arItem["INPUT_VALUE"] ? " selected" : "" ?> value="<?= $value ?>"><?= $arItem["SELECT_VALUE"]["reference"][$key] ?></option>
                            <? endforeach ?>
                        </select>
                    <? endif; ?>
                </label>
            <? endforeach; ?>

            <div class="t_center"> 
                <input type="submit" name="filter_lot_submit" value="<?= GetMessage("PW_TD_FILTER_SUBMIT_FIND") ?>" /> 
                <input type="submit" name="filter_lot_reset" value="<?= GetMessage("PW_TD_FILTER_SUBMIT_RESET") ?>" />
            </div>
        </form>
    </div>
</div>