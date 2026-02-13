<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>

<div class="t_block_right">          
    <? if (!empty($arResult["LOTS"])): ?>
        <div class="t_block_sort"> 
            <?= GetMessage("PW_TD_SORT") ?>:
            &nbsp;&nbsp;&nbsp;<?= GetMessage("PW_TD_SORT_NUM_LOT") ?> 
            <a href="<?= $arResult["CURR_URL"] ?>SORT_BY=ID&SORT_ORDER=ASC">&#9650;</a>
            <a href="<?= $arResult["CURR_URL"] ?>SORT_BY=ID&SORT_ORDER=DESC">&#9660;</a>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= GetMessage("PW_TD_SORT_DATE_START") ?> 
            <a href="<?= $arResult["CURR_URL"] ?>SORT_BY=DATE_START&SORT_ORDER=ASC">&#9650;</a>
            <a href="<?= $arResult["CURR_URL"] ?>SORT_BY=DATE_START&SORT_ORDER=DESC">&#9660;</a>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= GetMessage("PW_TD_SORT_DATE_END") ?> 
            <a href="<?= $arResult["CURR_URL"] ?>SORT_BY=DATE_END&SORT_ORDER=ASC">&#9650;</a>
            <a href="<?= $arResult["CURR_URL"] ?>SORT_BY=DATE_END&SORT_ORDER=DESC">&#9660;</a>
        </div>

        <? if ($arParams["DISPLAY_TOP_PAGER"]): ?>
            <div class="t_block_top">                
                <div class="t_block_top_left">
                    <?= $arResult["NAV_STRING"] ?>                    
                </div>
                <div class="t_block_top_right">
                    <span class="t_legend t_active">- <?= GetMessage("PW_TD_LEGEND_ACTIVE") ?></span>
                    <span class="t_legend t_arhive">- <?= GetMessage("PW_TD_LEGEND_ARCHIVE") ?></span>

                </div>
            </div>
        <? endif; ?>
        <div class="t_lots">
            <table>
                <tbody>
                    <? foreach ($arResult["LOTS"] as $arLots): ?>
                        <tr<? if ($arLots["ARCHIVE"] == "Y") echo ' class="odd"'; ?>>
                            <td>
                                <a class="t_lot_title <? if ($arLots["ARCHIVE"] == "Y") echo 't_arhive'; else echo 't_active'; ?>" href="<?= $arLots["DETAIL_URL"] ?>"><?= $arLots["TITLE"] ?></a>
                                <div class="t_lot_descr">
                                    <span><b><?= GetMessage("PW_TD_LOT_SECTION") ?>:</b> <?= $arLots["SECTION"] ?></span>
                                    <span><b><?= GetMessage("PW_TD_LOT_COMPANY") ?>:</b> <?= $arLots["COMPANY"] ?></span>
                                    <span><b><?= GetMessage("PW_TD_LOT_RESPONSIBLE_FIO") ?>:</b> <?= $arLots["RESPONSIBLE_FIO"] ?></span>
                                    <span><b><?= GetMessage("PW_TD_LOT_RESPONSIBLE_PHONE") ?>:</b> <?= $arLots["RESPONSIBLE_PHONE"] ?></span>       
                                    <? if ($arLots["OPEN_PRICE"] == "Y"): ?>
                                        <span><b><?= GetMessage("PW_TD_LOT_OPEN_PRICE") ?></b></span>
                                    <? endif; ?>
                                    <? if ($arLots["PROPOSAL"] > 0): ?>
                                        <span><b><?= GetMessage("PW_TD_LOT_CNT_PROPOSAL") ?>:</b> <?= $arLots["PROPOSAL"] ?></span>
                                    <? endif; ?>
                                </div>
                                <div class="t_lot_meta">
                                    <span><b><?= GetMessage("PW_TD_LOT") ?>:</b> <?= $arLots["ID"] ?></span>
                                    <span><b><?= GetMessage("PW_TD_LOT_DATE_START") ?>:</b><br />
                                        <?= $arLots["DATE_START"] ?></span>
                                    <span><b><?= GetMessage("PW_TD_LOT_DATE_END") ?>: </b><br />
                                        <?= $arLots["DATE_END"] ?></span>
                                </div>
                            </td>
                        </tr>
                    <? endforeach; ?>
                </tbody>
            </table>
        </div>
        <? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
            <div class="t_block_top">                
                <div class="t_block_top_left">
                    <?= $arResult["NAV_STRING"] ?>                    
                </div>
            </div>
        <? endif; ?>
    <? else: ?>
        <?= GetMessage("PW_TD_LOT_NOT_FOUND") ?>
    <? endif; ?>
</div>
<br clear="both" />