<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>

<div class="t_block_left">            
    <div class="t_block_top"></div>
    <div class="t_lot_meta">
        <p>
            <b><?= GetMessage("PW_TD_SECTION") ?>: </b><br />
            <?= $arResult["LOT"]["SECTION"] ?>
        </p>
        <p>
            <b><?= GetMessage("PW_TD_DATE_START") ?>:</b><br />
            <?= $arResult["LOT"]["DATE_START"] ?>                   
        </p>
        <p>
            <b><?= GetMessage("PW_TD_DATE_END") ?>: </b><br />
            <?= $arResult["LOT"]["DATE_END"] ?>
            <? if ($arResult["LOT"]["TIME_EXTENSION"] > 0) : ?>
                <span class="time_ext">(+ <?= round($arResult["LOT"]["TIME_EXTENSION"] / 60, 1); ?> <?= GetMessage("PW_TD_MINUTES") ?>)</span>
            <? endif; ?>
        </p>
        <? if (strlen($arResult["LOT"]["DATE_DELIVERY"]) > 0): ?>
            <p>
                <b><?= GetMessage("PW_TD_DATE_DELIVERY") ?>: </b><br />
                <?= $arResult["LOT"]["DATE_DELIVERY"] ?>
            </p>
        <? endif; ?>
        <br />
        <p>
            <b><?= GetMessage("PW_TD_COMPANY") ?>: </b><br />
            <?= $arResult["LOT"]["COMPANY"] ?>
        </p>
        <p>
            <b><?= GetMessage("PW_TD_RESPONSIBLE") ?>:</b><br />
            <?= $arResult["LOT"]["RESPONSIBLE_FIO"] ?>                    
        </p>
        <p>
            <b><?= GetMessage("PW_TD_RESPONSIBLE_PHONE") ?>:</b><br />
            <?= $arResult["LOT"]["RESPONSIBLE_PHONE"] ?>
        </p>
        <? if (count($arResult["LOT"]["FILE"]) > 0): ?>
            <p>
                <b><?= GetMessage("PW_TD_DOCUMENT") ?>:</b><br />
                <?
                foreach ($arResult["LOT"]["FILE"] as $arFile) {
                    ?>
                    <a href="/tx_files/lot_file.php?LOT_ID=<?= $arResult["LOT"]["ID"] ?>&FILE_ID=<?= $arFile["ID"] ?>"><?= $arFile["ORIGINAL_NAME"] ?></a><br />
                    <?
                }
                ?>
            </p>
        <? endif; ?>
        <? if ($arResult["OWNER"] == "Y"): ?>
            <p>
                <a href="<?= $arResult["LOT_URL"] ?>"><?= GetMessage("PW_TD_EDIT_LOT") ?></a>
            </p>
        <? endif; ?>
        <? if ($arResult["T_RIGHT"] == "P"): ?>
            <p>
                <a href="<?= $arResult["PROPOSAL_URL"] ?>"><?= GetMessage("PW_TD_PROPOSAL_ADD") ?></a>
            </p>
        <? endif; ?>
    </div>
</div>

<div class="t_block_right">          
    <div class="t_block_top">         
        <h2 <? if ($arResult["LOT"]["ARCHIVE"] == "Y") echo 'class="t_arhive"'; else echo 'class="t_active"'; ?>><?= GetMessage("PW_TD_LOT_NUM") ?><?= $arResult["LOT"]["ID"] ?> <?= $arResult["LOT"]["TITLE"] ?></h2>
        <div class="t_block_top_left">
            <b></b>
        </div>
    </div>

    <div class="t_lot">
        <? if ($arResult["LOT"]["OPEN_PRICE"] == "Y"): ?>
            <b class="t_open"><?= GetMessage("PW_TD_LOT_OPEN_PRICE") ?></b>
        <? endif; ?>
            <br />[<?= GetMessage("PW_TD_CURRENCY") ?>: <?= $arResult["LOT"]["CURRENCY"] ?>]<br />
        <? if ($arResult["LOT"]["TYPE_ID"] == "S"): ?>
            <table class="t_lot_table bold">
                <? $odd_table = 1; ?>
                <? foreach ($arResult["PROPERTY_PRODUCT"] as $arPropProductId => $arPropProduct): ?>
                    <? if ($arResult["PROPERTY_PRODUCT_BUYER"][$arPropProductId]["VISIBLE"] == "Y"): ?>
                        <tr<? if ($odd_table % 2 == 0) echo ' class="odd"'; ?>>
                            <td>
                                <? echo $arPropProduct["TITLE"]; ?>
                            </td>
                            <td>
                                <?= $arResult["PROPERTY_PRODUCT_BUYER"][$arPropProductId]["VALUE"]; ?>
                            </td>
                        </tr>
                        <? $odd_table++; ?>
                    <? endif; ?>
                <? endforeach; ?>

                <?
                $COUNT = $arResult["PRODUCT_BUYER"]["COUNT"];
                $NDS = 0;
                $PRICE_NDS = $arResult["PRODUCT_BUYER"]["START_PRICE"];
                ?>
                <tr<? if ($odd_table % 2 == 0) echo ' class="odd"'; ?>>
                    <td>
                        <?= GetMessage("PW_TD_PRODUCT_COUNT") ?>
                    </td>
                    <td>
                        <?= $arResult["PRODUCT_BUYER"]["COUNT"] ?>
                    </td>
                </tr>
                <? $odd_table++; ?>
                <tr<? if ($odd_table % 2 == 0) echo ' class="odd"'; ?>>
                    <td>
                        <?= GetMessage("PW_TD_PRODUCT_UNIT") ?>
                    </td>
                    <td>
                        <?= $arResult["PRODUCT"]["UNIT_NAME"] ?>
                    </td>
                </tr>
                <? if (intval($arResult["PRODUCT_BUYER"]["START_PRICE"]) > 0): ?>
                    <? $odd_table++; ?>
                    <tr<? if ($odd_table % 2 == 0) echo ' class="odd"'; ?>>
                        <td>
                            <? if ($arParams["NDS_TYPE"] == "N"): ?>
                                <?= GetMessage("PW_TD_PRICE_START_PRICE_NDS_N") ?>
                            <? else: ?>
                                <?= GetMessage("PW_TD_PRICE_START_PRICE_NDS") ?>
                            <? endif; ?>
                        </td>
                        <td>
                            <?= $arResult["PRODUCT_BUYER"]["START_PRICE"] ?>
                        </td>
                    </tr>
                <? endif; ?>
                <? if (intval($arResult["PRODUCT_BUYER"]["STEP_PRICE"]) > 0): ?>
                    <? $odd_table++; ?>
                    <tr<? if ($odd_table % 2 == 0) echo ' class="odd"'; ?>>
                        <td>
                            <?= GetMessage("PW_TD_PRODUCT_STEP_PRICE") ?>
                        </td>
                        <td>
                            <?= $arResult["PRODUCT_BUYER"]["STEP_PRICE"] ?>
                        </td>
                    </tr>
                <? endif; ?>
            </table>
        <? endif; ?>

        <? if ($arResult["LOT"]["TYPE_ID"] != "S"): ?>
            <table class="t_lot_table">
                <? $numProp = 1; ?>
                <? foreach ($arResult["PROPERTY_SPEC"] as $specProp): ?>
                    <? if ($numProp == 1): ?>
                        <tr>
                            <th><?= GetMessage("PW_TD_NUM") ?></th>
                            <th><?= GetMessage("PW_TD_TOVAR") ?></th>
                            <? // if ($specProp["START_PRICE"] > 0 && $specProp["STEP_PRICE"] > 0):   ?>
                            <th>
                                <? if ($arParams["NDS_TYPE"] == "N"): ?>
                                    <?= GetMessage("PW_TD_SPEC_START_PRICE_NDS_N") ?>
                                <? else: ?>
                                    <?= GetMessage("PW_TD_SPEC_START_PRICE_NDS") ?>
                                <? endif; ?>
                            </th>
                            <th><?= GetMessage("PW_TD_SPEC_STEP_PRICE") ?></th>
                            <? // endif;   ?>
                        </tr>
                    <? endif; ?>
                    <tr>
                        <td align="center"><? echo $numProp ?></td>
                        <td>
                            <b><?= GetMessage("PW_TD_SPEC_NAME_PROD") ?>:</b> <?= $specProp["TITLE"] ?><br />
                            <b><?= GetMessage("PW_TD_SPEC_COUNT") ?>:</b> <?= $specProp["COUNT"] ?>  <?= $specProp["UNIT_NAME"] ?><br />
                            <? if (strlen($specProp["ADD_INFO"]) > 0): ?>
                                <b><?= GetMessage("PW_TD_SPEC_ADD_INFO") ?>:</b> <?= $specProp["ADD_INFO"] ?>
                            <? endif; ?>    
                        </td>
                        <? //if ($specProp["START_PRICE"] > 0 && $specProp["STEP_PRICE"] > 0):   ?>
                        <td align="center">
                            <?= $specProp["START_PRICE"] ?>
                        </td>
                        <td align="center">
                            <?= $specProp["STEP_PRICE"] ?>
                        </td>
                        <? //endif;   ?>
                    </tr>
                    <? $numProp++; ?>
                <? endforeach; ?>
            </table>
        <? endif; ?>

        <? if ($arResult["LOT"]["TERM_PAYMENT_ID"] > 0): ?>
            <p><b><?= GetMessage("PW_TD_TERM_PAYMENT") ?>:</b> <?= $arResult["PAYMENT"] ?><br />
                <?= $arResult["LOT"]["TERM_PAYMENT_VAL"] ?>
            </p>
        <? endif; ?>
        <? if ($arResult["LOT"]["TERM_DELIVERY_ID"] > 0): ?>
            <p><b><?= GetMessage("PW_TD_TERM_DELIVERY") ?>:</b> <?= $arResult["DELIVERY"] ?><br />
                <?= $arResult["LOT"]["TERM_DELIVERY_VAL"] ?>
            </p>
        <? endif; ?>
        <? if (strlen($arResult["LOT"]["NOTE"]) > 0): ?>
            <p><b><?= GetMessage("PW_TD_NOTE") ?>:</b><br />
                <?= html_entity_decode($arResult["LOT"]["NOTE"]) ?>
            </p>
        <? endif; ?>

    </div>
</div>
<br clear="all" />