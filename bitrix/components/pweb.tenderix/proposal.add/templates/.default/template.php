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
    </div>
</div>

<div class="t_block_right">          
    <div class="t_block_top">         
        <h2 class="t_active"><?= GetMessage("PW_TD_LOT_NUM") ?><?= $arResult["LOT"]["ID"] ?> <?= $arResult["LOT"]["TITLE"] ?></h2>
        <div class="t_block_top_left">
            <b><?= GetMessage("PW_TD_BALANCE_TIME") ?>:</b> <span id="time"></span> <span id="time2"></span>
        </div>
    </div>

    <div class="t_lot">
        <form name="proposal_add" action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data">
            <? if (strlen($arResult["ERRORS"]) > 0): ?>
                <div class="errors-tender"><?= $arResult["ERRORS"] ?></div>
            <? endif; ?>
            <? if ($arResult["SEND_OK"] == "Y"): ?>
                <div class="send-ok-tender"><?= GetMessage("PW_TD_SEND_OK") ?></div>
            <? endif; ?>
            <b><?= GetMessage("PW_TD_CURR") ?></b><br />
            <select name="CURRENCY_PROPOSAL">
                <? foreach ($arResult["CURRENCY"] as $nameCurrency => $arCurrency): ?>
                    <option<?
                if ($arResult["CURRENCY_PROPOSAL"] == $nameCurrency || $nameCurrency == $_REQUEST["CURRENCY_PROPOSAL"])
                    echo " selected";
                    ?> value="<?= $nameCurrency ?>"><?= $nameCurrency ?><?
                    if (strlen($arCurrency["RATE"]) > 0)
                        echo " [" . $arCurrency["RATE"] . "]";
                    ?></option>
                <? endforeach; ?>
            </select>
            <? foreach ($arResult["CURRENCY"] as $nameCurrency => $arCurrency): ?>
                <input type="hidden" name="CURR[<?= $nameCurrency ?>]" value="<?= $arCurrency["RATE_NUM"] > 0 ? $arCurrency["RATE_NUM"] : 1 ?>" />
            <? endforeach; ?>
            <input type="hidden" name="CURR_USER" value="<?= strlen($arResult["CURRENCY_PROPOSAL"]) > 0 ? $arResult["CURRENCY_PROPOSAL"] : $arParams["CURR"] ?>" />

            <? if ($arResult["LOT"]["OPEN_PRICE"] == "Y"): ?>
                <b class="t_open"><?= GetMessage("PW_TD_LOT_OPEN_PRICE") ?></b>
            <? endif; ?>

            <? if ($arResult["LOT"]["TYPE_ID"] == "S"): ?>
                <table class="t_lot_table bold">
                    <? $odd_table = 1; ?>
                    <? foreach ($arResult["PROPERTY_PRODUCT"] as $arPropProductId => $arPropProduct): ?>
                        <? if ($arResult["PROPERTY_PRODUCT_BUYER"][$arPropProductId]["VISIBLE"] == "Y"): ?>
                            <?
                            if (isset($arResult["PROPOSAL_PROPERTY_PRODUCTS"]) && $arResult["PROPOSAL_ID"] > 0) {
                                $valueProducts = $arResult["PROPOSAL_PROPERTY_PRODUCTS"][$arResult["PROPERTY_PRODUCT_BUYER"][$arPropProductId]["ID"]];
                            } else {
                                $valueProducts = $arResult["PROPERTY_PRODUCT_BUYER"][$arPropProductId]["VALUE"];
                            }
                            ?>
                            <tr<?
                if ($odd_table % 2 == 0)
                    echo ' class="odd"';
                            ?>>
                                <td>
                                    <? echo $arPropProduct["TITLE"]; ?>
                                </td>
                                <td>
                                    <? if ($arResult["PROPERTY_PRODUCT_BUYER"][$arPropProductId]["EDIT"] == "Y"): ?>
                                        <? $PROPS = isset($_REQUEST["PROPS"][$arResult["PROPERTY_PRODUCT_BUYER"][$arPropProductId]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROPS"][$arResult["PROPERTY_PRODUCT_BUYER"][$arPropProductId]["ID"]]) : $valueProducts ?>
                                        <input type="text" size="10" name="PROPS[<?= $arResult["PROPERTY_PRODUCT_BUYER"][$arPropProductId]["ID"] ?>]" value="<?= $PROPS ?>" />
                                    <? else: ?>
                                        <?= $valueProducts; ?>
                                    <? endif; ?>
                                </td>
                            </tr>
                            <? $odd_table++; ?>
                        <? endif; ?>
                    <? endforeach; ?>

                    <?
                    if ($arResult["PROPOSAL_ID"] > 0) {
                        $COUNT = isset($_REQUEST["proposal_submit"]) ? floatval($_REQUEST["COUNT"]) : $arResult["PROPOSAL_PRODUCTS"]["COUNT"];
                        $NDS = isset($_REQUEST["proposal_submit"]) ? intval($_REQUEST["NDS"]) : $arResult["PROPOSAL_PRODUCTS"]["NDS"];
                        $PRICE_NDS = isset($_REQUEST["proposal_submit"]) ? floatval($_REQUEST["PRICE_NDS"]) : $arResult["PROPOSAL_PRODUCTS"]["PRICE_NDS"];
                    } else {
                        $COUNT = isset($_REQUEST["proposal_submit"]) ? floatval($_REQUEST["COUNT"]) : $arResult["PRODUCT_BUYER"]["COUNT"];
                        $NDS = isset($_REQUEST["proposal_submit"]) ? intval($_REQUEST["NDS"]) : 0;
                        $PRICE_NDS = isset($_REQUEST["proposal_submit"]) ? floatval($_REQUEST["PRICE_NDS"]) : $arResult["PRODUCT_BUYER"]["START_PRICE"];
                    }
                    ?>
                    <tr<?
                if ($odd_table % 2 == 0)
                    echo ' class="odd"';
                    ?>>
                        <td>
                            <?= GetMessage("PW_TD_PRODUCT_COUNT") ?>
                        </td>
                        <td>
                            <? if ($arResult["PRODUCT_BUYER"]["COUNT_EDIT"] == "Y"): ?>
                                <input type="text" size="10" name="COUNT" value="<?= $COUNT; ?>" />
                            <? else: ?>
                                <?= $arResult["PRODUCT_BUYER"]["COUNT"] ?>
                                <input type="hidden" name="COUNT" value="<?= $arResult["PRODUCT_BUYER"]["COUNT"] ?>" />
                            <? endif; ?>
                        </td>
                    </tr>
                    <? $odd_table++; ?>
                    <tr<?
                if ($odd_table % 2 == 0)
                    echo ' class="odd"';
                    ?>>
                        <td>
                            <?= GetMessage("PW_TD_PRODUCT_UNIT") ?>
                        </td>
                        <td>
                            <?= $arResult["PRODUCT"]["UNIT_NAME"] ?>
                        </td>
                    </tr>
                </table>
                <table class="t_lot_table">
                    <tr>
                        <th><?= GetMessage("PW_TD_NDS") ?></th>
                        <th>
                            <? if ($arParams["NDS_TYPE"] == "N"): ?>
                                <?= GetMessage("PW_TD_PRICE_NDS_N") ?>
                            <? else: ?>
                                <?= GetMessage("PW_TD_PRICE_NDS") ?>
                            <? endif; ?>
                        </th>
                        <? if ($arResult["LOT"]["OPEN_PRICE"] == "Y"): ?>
                            <th><?= GetMessage("PW_TD_BEST_PROPOSAL") ?></th>
                        <? endif; ?>
                    </tr>   
                    <tr>
                        <td>
                            <select name="NDS">
                                <option value="0">0</option>
                                <option<?
                    if ($NDS == 10)
                        echo " selected"
                            ?> value="10">10</option>
                                <option<?
                            if ($NDS == 18)
                                echo " selected"
                            ?> value="18">18</option>
                            </select>
                        </td>
                        <td class="item-proposal-price">
                            <? if (intval($arResult["PRODUCT_BUYER"]["START_PRICE"]) > 0): ?>
                                <input type="text" id="item_proposal_price" name="PRICE_NDS" value="<?= round($PRICE_NDS, 2) ?>" readonly /> 
                                <a onclick="stepDownS(<?= $arResult["PRODUCT_BUYER"]["STEP_PRICE"] ?>); return false;" href="#"><img src="/bitrix/components/pweb.tenderix/proposal.add/templates/.default/images/down.gif" width="14" height="15" alt="down"/></a> 
                                <a onclick="stepUpS(<?= $arResult["PRODUCT_BUYER"]["STEP_PRICE"] ?>); return false;" href="#"><img src="/bitrix/components/pweb.tenderix/proposal.add/templates/.default/images/up.gif" width="14" height="15" alt="up"/></a>
                            <? else: ?>
                                <input type="text" id="item_proposal_price" name="PRICE_NDS" value="<?= round($PRICE_NDS, 2) ?>" />
                            <? endif; ?>
                            <input type="hidden" id="item_proposal_price_full" value="<?= $PRICE_NDS ?>" />
                        </td>    
                        <? if ($arResult["LOT"]["OPEN_PRICE"] == "Y"): ?>
                            <td>
                                <span id="best_proposal"></span>
                                <input type="hidden" id="best_proposal_full" />
                            </td>
                        <? endif; ?>
                    </tr>
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
                                <? if ($specProp["NOT_ANALOG"] == "N"): ?>
                                    <th><?= GetMessage("PW_TD_ANALOG") ?></th>
                                <? endif; ?>
                                <th><?= GetMessage("PW_TD_NDS") ?></th>
                                <? if ($arParams["NDS_TYPE"] == "N"): ?>
                                    <th><?= GetMessage("PW_TD_PRICE_NDS_N") ?></th>
                                <? else: ?>
                                    <th><?= GetMessage("PW_TD_PRICE_NDS") ?></th>
                                <? endif; ?>
                                <? if ($arResult["LOT"]["OPEN_PRICE"] == "Y"): ?>
                                    <th><?= GetMessage("PW_TD_BEST_PROPOSAL") ?></th>
                                <? endif; ?>
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
                            <? if ($specProp["NOT_ANALOG"] == "N"): ?>
                                <?
                                //���� ���� �����������
                                if (isset($arResult["PROPOSAL_SPEC"]) && $arResult["PROPOSAL_ID"] > 0) {
                                    $ANALOG = isset($_REQUEST["proposal_submit"]) ? htmlspecialcharsEx($_REQUEST["PROP_" . $specProp["ID"] . "_ANALOG"]) : $arResult["PROPOSAL_SPEC"][$specProp["ID"]]["ANALOG"];
                                } else {
                                    $ANALOG = isset($_REQUEST["proposal_submit"]) ? htmlspecialcharsEx($_REQUEST["PROP_" . $specProp["ID"] . "_ANALOG"]) : "";
                                }
                                ?>
                                <td><textarea name="PROP_<?= $specProp["ID"] ?>_ANALOG" rows="4" cols="15"><?= htmlspecialchars($ANALOG) ?></textarea></td>
                            <? endif; ?>
                            <td> 
                                <?
                                //���� ���� �����������
                                if (isset($arResult["PROPOSAL_SPEC"]) && $arResult["PROPOSAL_ID"] > 0) {
                                    $NDS = isset($_REQUEST["proposal_submit"]) ? intval($_REQUEST["PROP_" . $specProp["ID"] . "_NDS"]) : $arResult["PROPOSAL_SPEC"][$specProp["ID"]]["NDS"];
                                } else {
                                    $NDS = isset($_REQUEST["proposal_submit"]) ? intval($_REQUEST["PROP_" . $specProp["ID"] . "_NDS"]) : 0;
                                }
                                ?>
                                <select name="PROP_<?= $specProp["ID"] ?>_NDS" id="PROP_<?= $specProp["ID"] ?>_NDS">
                                    <option value="0">0</option>
                                    <option<?
                        if ($NDS == 10)
                            echo " selected"
                                    ?> value="10">10</option>
                                    <option<?
                            if ($NDS == 18)
                                echo " selected"
                                    ?> value="18">18</option>
                                </select>
                            </td>
                            <td class="item-proposal-price">
                                <?
                                //���� ���� �����������
                                if (isset($arResult["PROPOSAL_SPEC"]) && $arResult["PROPOSAL_ID"] > 0) {
                                    $priceNDS = isset($_REQUEST["proposal_submit"]) ? floatval($_REQUEST["PROP_" . $specProp["ID"] . "_PRICE_NDS"]) : $arResult["PROPOSAL_SPEC"][$specProp["ID"]]["PRICE_NDS"];
                                } else {
                                    $priceNDS = isset($_REQUEST["proposal_submit"]) ? floatval($_REQUEST["PROP_" . $specProp["ID"] . "_PRICE_NDS"]) : $specProp['START_PRICE'];
                                }
                                ?>
                                <? if (intval($specProp['START_PRICE']) > 0): ?>
                                    <input type="text" size="10" class="item_proposal_price w80" name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS" readonly value="<?= round($priceNDS, 2); ?>" /> 
                                    <a onclick="stepDown(<?= $specProp["ID"] ?>,<?= $specProp['STEP_PRICE'] ?>); return false;" href="#"><img src="/bitrix/components/pweb.tenderix/proposal.add/templates/.default/images/down.gif" width="14" height="15" alt="down"/></a> 
                                    <a onclick="stepUp(<?= $specProp["ID"] ?>,<?= $specProp['STEP_PRICE'] ?>); return false;" href="#"><img src="/bitrix/components/pweb.tenderix/proposal.add/templates/.default/images/up.gif" width="14" height="15" alt="up"/></a>
                                <? else: ?>
                                    <input type="text" size="10" class="item_proposal_price" name="PROP_<?= $specProp["ID"] ?>_PRICE_NDS" value="<?= round($priceNDS, 2); ?>" />
                                <? endif; ?>
                                <input type="hidden" class="id_item_proposal" value="<?= $specProp["ID"]; ?>" />
                                <input type="hidden" id="price_nds_full_<?= $specProp["ID"]; ?>" value="<?= $priceNDS; ?>" />
                            </td>
                            <? if ($arResult["LOT"]["OPEN_PRICE"] == "Y"): ?>
                                <td align="center" class="best_proposal_n" id="best_proposal_<?= $specProp["ID"] ?>"></td>
                            <input type="hidden" id="best_proposal_full_<?= $specProp["ID"] ?>" value="" />
                        <? endif; ?>
                        </tr>
                        <? $numProp++; ?>
                    <? endforeach; ?>
                </table>
            <? endif; ?>

            <? if ($arResult["LOT"]["TERM_PAYMENT_ID"] > 0): ?>
                <p><b><?= GetMessage("PW_TD_TERM_PAYMENT") ?>:</b> <?= $arResult["PAYMENT"] ?><br />
                    <? if ($arResult["LOT"]["TERM_PAYMENT_EDIT"] == "Y"): ?>
                        <textarea name="TERM_PAYMENT_VAL" cols="80" rows="5"><?= isset($_REQUEST["proposal_submit"]) ? htmlspecialcharsEx($_REQUEST["TERM_PAYMENT_VAL"]) : $arResult["LOT"]["TERM_PAYMENT_VAL"] ?></textarea>
                    <? else: ?>
                        <?= $arResult["LOT"]["TERM_PAYMENT_VAL"] ?>
                    <? endif; ?>
                </p>
            <? endif; ?>
            <? if ($arResult["LOT"]["TERM_DELIVERY_ID"] > 0): ?>
                <p><b><?= GetMessage("PW_TD_TERM_DELIVERY") ?>:</b> <?= $arResult["DELIVERY"] ?><br />
                    <? if ($arResult["LOT"]["TERM_DELIVERY_EDIT"] == "Y"): ?>
                        <textarea name="TERM_DELIVERY_VAL" cols="80" rows="5"><?= isset($_REQUEST["proposal_submit"]) ? htmlspecialcharsEx($_REQUEST["TERM_DELIVERY_VAL"]) : $arResult["LOT"]["TERM_DELIVERY_VAL"] ?></textarea>
                    <? else: ?>
                        <?= $arResult["LOT"]["TERM_DELIVERY_VAL"] ?>
                    <? endif; ?>
                </p>
            <? endif; ?>
            <? if (strlen($arResult["LOT"]["NOTE"]) > 0): ?>
                <p><b><?= GetMessage("PW_TD_NOTE") ?>:</b><br />
                    <?= html_entity_decode($arResult["LOT"]["NOTE"]) ?>
                </p>
            <? endif; ?>
            <p><b><?= GetMessage("PW_TD_MESSAGE") ?>: </b>
                <textarea name="MESSAGE" cols="80" rows="5"><?= isset($_REQUEST["proposal_submit"]) ? htmlspecialcharsEx($_REQUEST["MESSAGE"]) : $arResult["MESSAGE"] ?></textarea></p>



            <?
            /*             * ***************
             * PROPERTY
             * *************** */
            ?>
            <script type="text/javascript">
                function addNewElem(id, cnt) {
                    var idProp = parseInt($("#id-prop-"+id).val());
                    var str = $("#prop-"+id+"-"+(idProp-1)).html();
                    var nidProp = idProp-parseInt(cnt);
                    str = str.replace(/\[n\d+\]/g,"[n"+nidProp+"]");
                    $("#prop-"+id).append('<div id="prop-'+id+'-'+idProp+'">'+str+'</div>');
                    idProp += 1;
                    $("#id-prop-"+id).val(idProp);
                }
            </script>

            <?
            $arPropProposal = $arResult["PROP_PROPOSAL"];
            foreach ($arResult["PROP_LIST"] as $arPropList) :
                ?>
                <p>
                    <b>
                        <? if ($arPropList["IS_REQUIRED"] == "Y"): ?>
                            <span class="required">*</span>
                        <? endif; ?>
                        <?= $arPropList["TITLE"] ?>:
                    </b>

                    <? $is_file_prop = false; ?>
                    <? if ($arPropList["PROPERTY_TYPE"] == "F" && ($rsFiles = CTenderixProposal::GetFileListProperty($arResult["PROPOSAL_ID"], $arPropList["ID"])) && ($arFile = $rsFiles->GetNext())) { ?>
                        <? $is_file_prop = true; ?>
                    <table>
                        <tr>
                            <td>
                                <table class="t_lot_table">
                                    <tr>
                                        <th><? echo GetMessage("PW_TD_FILE_NAME") ?></th>
                                        <th><? echo GetMessage("PW_TD_FILE_SIZE") ?></th>
                                        <th><? echo GetMessage("PW_TD_FILE_DELETE") ?></th>
                                    </tr>
                                    <?
                                    do {
                                        ?>
                                        <tr>
                                            <td><a href="/tx_files/property_file.php?PROPOSAL_ID=<?= $arResult["PROPOSAL_ID"] ?>&amp;FILE_ID=<?= $arFile["ID"] ?>&amp;PROPERTY=<?= $arPropList["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a></td>
                                            <td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
                                            <td align="center">
                                                <input type="checkbox" name="FILE_ID_PROP[<? echo $arFile["ID"] ?>]" value="<? echo $arFile["ID"] ?>">
                                                <input type="hidden" name="PROP[<?= $arPropList["ID"] ?>][<?= $arFile["ID"] ?>]" />
                                                <input type="hidden" name="FILE_PROP" value="<?= $arPropList["ID"] ?>" />
                                            </td>
                                        </tr>
                                    <? } while ($arFile = $rsFiles->GetNext()); ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                <? } ?>
                <?
                $result = "";
                if (strlen($arPropList["DEFAULT_VALUE"]) > 0 && $arPropList["MULTI"] == "Y") {
                    $arPropList["MULTI_CNT"]++;
                }
                $cntProp = 0;
                if ($arResult["PROPOSAL_ID"] > 0 && $arPropList["PROPERTY_TYPE"] != "L" && $arPropList["PROPERTY_TYPE"] != "F") {
                    $cntProp = count($arPropProposal[$arPropList["ID"]]);
                    $arPropList["MULTI_CNT"] += $cntProp;
                }
                if (isset($_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]]) &&
                        $_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]] >= $arPropList["MULTI_CNT"] &&
                        $arPropList["PROPERTY_TYPE"] != "L" &&
                        $arPropList["PROPERTY_TYPE"] != "F") {
                    if (strlen($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]] - $cntProp - 1)]) > 0) {
                        $arPropList["MULTI_CNT"] = $_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]] + 1;
                    } else {
                        $arPropList["MULTI_CNT"] = $_REQUEST["PROP_ID_MULTI"][$arPropList["ID"]];
                    }
                }
                if ($arPropList["PROPERTY_TYPE"] == "L" || $arPropList["MULTI"] == "N") {
                    $arPropList["MULTI_CNT"] = 1;
                }
                $result .= '<br /><span id="prop-' . $arPropList["ID"] . '">';
                for ($i = 0; $i < $arPropList["MULTI_CNT"]; $i++) {
                    $result .= '<span id="prop-' . $arPropList["ID"] . '-' . $i . '">';
                    switch ($arPropList["PROPERTY_TYPE"]) {
                        case "S":
                        case "N":
                            if ($i > 0 || $arResult["PROPOSAL_ID"] > 0) {
                                $arPropList["DEFAULT_VALUE"] = "";
                            }
                            if ($arResult["PROPOSAL_ID"] > 0 && $i < $cntProp) {
                                $propName = "PROP[" . $arPropList["ID"] . "][" . $arPropProposal[$arPropList["ID"]][$i]["ID"] . "]";
                                $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"]);
                            } else {
                                $propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
                                $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : htmlspecialcharsEx($arPropList["DEFAULT_VALUE"]);
                            }
                            if ($arPropList["ROW_COUNT"] <= 1) {
                                $result .= '<input name="' . $propName . '" type="text" value="' . $propValue . '" size="' . $arPropList["COL_COUNT"] . '" />';
                            } else {
                                $result .= '<textarea name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $propValue . '</textarea>';
                            }
                            break;
                        case "F":
                            if (!$is_file_prop || $arPropList["MULTI"] == "Y")
                                $result .= '<input type="file" name="PROP[' . $arPropList["ID"] . '][n' . ($i - $cntProp) . ']" size="' . $arPropList["COL_COUNT"] . '" />';
                            break;
                        case "L":
                            $arrList = unserialize(base64_decode($arPropList["DEFAULT_VALUE"]));
                            if ($arResult["PROPOSAL_ID"] > 0) {
                                foreach ($arPropProposal[$arPropList["ID"]] as $arrListSupplier) {
                                    $arrListValue[] = $arrListSupplier["VALUE"];
                                }
                            } else {
                                $arrListValue[] = $arrList["DEFAULT_VALUE_SELECT"];
                            }
                            if (isset($_REQUEST["PROP"][$arPropList["ID"]])) {
                                unset($arrListValue);
                                $arrListValue = $_REQUEST["PROP"][$arPropList["ID"]];
                            }
                            $result .= '<select name="PROP[' . $arPropList["ID"] . '][]"' . ($arPropList["MULTI"] == "Y" ? " multiple" : "") . ' size="' . $arPropList["ROW_COUNT"] . '">';
                            foreach ($arrList["DEFAULT_VALUE"] as $idRow => $listVal) {
                                $result .= '<option' . (in_array($idRow, $arrListValue) ? " selected" : "") . ' value="' . $idRow . '">' . $listVal . '</option>';
                            }
                            $result .= '</select>';
                            break;
                        case "T":
                            if ($i > 0 || $arResult["PROPOSAL_ID"] > 0) {
                                $arPropList["DEFAULT_VALUE"] = "";
                            }
                            if ($arResult["PROPOSAL_ID"] > 0 && $i < $cntProp) {
                                $propName = "PROP[" . $arPropList["ID"] . "][" . $arPropProposal[$arPropList["ID"]][$i]["ID"] . "]";
                                $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"]);
                            } else {
                                $propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
                                $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : htmlspecialcharsEx($arPropList["DEFAULT_VALUE"]);
                            }
                            $result .= '<textarea name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $propValue . '</textarea>';
                            break;
                        case "D":
                            if ($i > 0 || $arResult["PROPOSAL_ID"] > 0) {
                                $arPropList["DEFAULT_VALUE"] = "";
                            }
                            if ($arResult["PROPOSAL_ID"] > 0 && $i < $cntProp) {
                                $propName = "PROP[" . $arPropList["ID"] . "][" . $arPropProposal[$arPropList["ID"]][$i]["ID"] . "]";
                                $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arPropProposal[$arPropList["ID"]][$i]["ID"]]) : (strlen($arPropProposal[$arPropList["ID"]][$i]["VALUE"]) > 0 ? ConvertTimeStamp(strtotime($arPropProposal[$arPropList["ID"]][$i]["VALUE"]), "FULL") : "");
                            } else {
                                $propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
                                $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : (strlen($arPropList["DEFAULT_VALUE"]) > 0 ? ConvertTimeStamp(strtotime($arPropList["DEFAULT_VALUE"]), "FULL") : "");
                            }
                            $result .= '<input type="text" name="' . $propName . '" value="' . $propValue . '" size="20" />';
                            ob_start();
                            $APPLICATION->IncludeComponent(
                                    'bitrix:main.calendar', '', array(
                                'SHOW_INPUT' => 'N',
                                'FORM_NAME' => 'proposal_add',
                                'INPUT_NAME' => $propName,
                                'INPUT_VALUE' => (strlen($propValue) > 0 ? $propValue : date("d.m.Y H:i:s")),
                                'SHOW_TIME' => 'N',
                                'HIDE_TIMEBAR' => 'N'
                                    ), null, array('HIDE_ICONS' => 'Y')
                            );
                            $result .= ob_get_clean();
                            break;
                    }
                    $result .= '</span><br />';
                }
                $result .= '</span>';
                $result .= '<input type="hidden" name="PROP_ID_MULTI[' . $arPropList["ID"] . ']" id="id-prop-' . $arPropList["ID"] . '" value="' . $i . '" />';
                if ($arPropList["MULTI"] == "Y" && $arPropList["PROPERTY_TYPE"] != "L") {
                    $result .= '<input type="button" value="' . GetMessage("PW_TD_PROP_ADD") . '" onclick="addNewElem(' . $arPropList["ID"] . ', ' . $cntProp . ');" />';
                }
                echo $result;
                ?>
                </p>
            <? endforeach; ?>
            <?
            /*             * ***************
             * PROPERTY
             * *************** */
            ?>



            <p><b><?= GetMessage("PW_TD_DOCUMENT") ?>:</b><br />
                <? if (count($arResult["INFO"]["FILE"]) > 0): ?>
                    <br /><?= GetMessage("PW_TD_FILE_ATTACH_LIST") ?>:
                <table>
                    <tr>
                        <td>
                            <table class="t_lot_table">
                                <tr>
                                    <th><? echo GetMessage("PW_TD_FILE_NAME") ?></th>
                                    <th><? echo GetMessage("PW_TD_FILE_SIZE") ?></th>
                                    <th><? echo GetMessage("PW_TD_FILE_DELETE") ?></th>
                                </tr>
                                <? foreach ($arResult["INFO"]["FILE"] as $arFile) : ?>
                                    <tr>
                                        <td><a href="/tx_files/proposal_file.php?PROPOSAL_ID=<?= $arResult["PROPOSAL_ID"] ?>&FILE_ID=<?= $arFile["ID"] ?>"><?= $arFile["ORIGINAL_NAME"] ?></a></td>
                                        <td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
                                        <td align="center">
                                            <input type="checkbox" name="FILE_ID[<? echo $arFile["ID"] ?>]" value="<? echo $arFile["ID"] ?>">
                                        </td>
                                    </tr>
                                <? endforeach; ?>
                            </table>
                        </td>
                    </tr>
                </table>
            <? endif; ?>

            <? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
            <? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
            <? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
            <? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
            <? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
            </p>

            <div class="t_right">
                <? if (isset($arResult["PROPOSAL_ID"]) && $arResult["PROPOSAL_ID"] > 0): ?>
                    <input type="hidden" name="PROPOSAL_ID" value="<?= $arResult["PROPOSAL_ID"] ?>" />
                <? endif; ?>  
                <input type="submit" name="proposal_submit" value="<?= GetMessage("PROPOSAL_FORM_SAVE") ?>" />
            </div>
        </form>
    </div>
</div>

<br clear="all" />

<script type="text/javascript">
    function stepUp(id,step) {
        var price_nds = $("input[name='PROP_"+id+"_PRICE_NDS']").val();
        var curr_name = $("[name='CURRENCY_PROPOSAL']").val();
        var curr_val = parseFloat($("[name='CURR["+curr_name+"]']").val());

        price_nds = parseFloat(price_nds) + (parseFloat(step)/curr_val);
        $("input[name='PROP_"+id+"_PRICE_NDS']").val(price_nds.toFixed(2));
        return false;
    }
    function stepDown(id,step) {
        var price_nds = $("input[name='PROP_"+id+"_PRICE_NDS']").val();
        var curr_name = $("[name='CURRENCY_PROPOSAL']").val();
        var curr_val = parseFloat($("[name='CURR["+curr_name+"]']").val());

        price_nds = parseFloat(price_nds) - (parseFloat(step)/curr_val);
        $("input[name='PROP_"+id+"_PRICE_NDS']").val(price_nds.toFixed(2));
        return false;
    }

    function stepUpS(step) {
        var price_nds = $("#item_proposal_price_full").val();
        var curr_name = $("[name='CURRENCY_PROPOSAL']").val();
        var curr_val = parseFloat($("[name='CURR["+curr_name+"]']").val());

        price_nds = parseFloat(price_nds) + (parseFloat(step)/curr_val);
        $("#item_proposal_price_full").val(price_nds);
        $("input[name='PRICE_NDS']").val(price_nds.toFixed(2));
        return false;
    }
    function stepDownS(step) {
        var price_nds = $("#item_proposal_price_full").val();
        var curr_name = $("[name='CURRENCY_PROPOSAL']").val();
        var curr_val = parseFloat($("[name='CURR["+curr_name+"]']").val());

        price_nds = parseFloat(price_nds) - (parseFloat(step)/curr_val);
        $("#item_proposal_price_full").val(price_nds);
        $("input[name='PRICE_NDS']").val(price_nds.toFixed(2));
        return false;
    }

    function dateWrite(CountSec) {
        var days=" <?= GetMessage("DAYS") ?> ";
        var CountFullDays=(parseInt(CountSec/(24*60*60)));
        if (
        CountFullDays==2 ||
            CountFullDays==3 ||
            CountFullDays==4 ||
            CountFullDays==22 ||
            CountFullDays==23 ||
            CountFullDays==24 ||
            CountFullDays==32 ||
            CountFullDays==33 ||
            CountFullDays==34
    ) {
            days=" <?= GetMessage("DAYS2") ?> "
        }
        if (
        CountFullDays==1 ||
            CountFullDays==21 ||
            CountFullDays==31 
    ) {
            days=" <?= GetMessage("DAYS3") ?> "
        }
        var secInLastDay=CountSec-CountFullDays*24*3600;
        var CountFullHours=(parseInt(secInLastDay/3600));
        if (CountFullHours<10){CountFullHours="0"+CountFullHours};
        var secInLastHour=secInLastDay-CountFullHours*3600;
        var CountMinutes=(parseInt(secInLastHour/60));
        if (CountMinutes<10){CountMinutes="0"+CountMinutes};
        var lastSec=secInLastHour-CountMinutes*60;
        if (lastSec<10){lastSec="0"+lastSec};
        
        return CountFullDays+days+CountFullHours+":"+CountMinutes+":"+lastSec;
    }

    var update_flag = true;
    var count_refresh = 60;
    var count_time = 1;
    
    function updateLot() {
        var lot_id = $("#lot_id").val();
        var curr = $("[name='CURR_USER']").val();
        var time_diff = $("#time_diff").val();
   
        $("#time").html(dateWrite(time_diff));
                    
        if(update_flag) {
            update_flag = false;
            $.ajax({
                type: "POST",
                url: "/bitrix/components/pweb.tenderix/proposal.add/update.php",
                data: "LOT_ID="+lot_id+"&CURR="+curr,
                beforeSend: function() { 
                    $("#time2").html("<?= GetMessage("START_UPDATE") ?>"); 
                    $(".best_proposal_n").html("<?= GetMessage("START_UPDATE") ?>");
                    $("#best_proposal").html("<?= GetMessage("START_UPDATE") ?>");   
                },
                success: function(data){
                    var json = eval("(" + data + ")");
                    if(json.time_diff <= 0) {
                        window.location.href=window.location.href;
                    } else {
                        count_time = 0;
                        $("#time_diff").val(json.time_diff);
                        $("#time2").html("");
                        $(".best_proposal_n").html("-");
                        $("#best_proposal").html("-"); 

                        if(json.type != "S") {
                            var proposal_min = eval("(" + json.proposal_min + ")");
                            for (var key in proposal_min) { 
                                $("#best_proposal_"+key).html(proposal_min[key].toFixed(2));
                                $("#best_proposal_full_"+key).val(proposal_min[key]);
                            }
                        } 
                        if(json.type == "S") {
                            var proposal_min = json.proposal_min;
                            $("#best_proposal").html(proposal_min.toFixed(2));
                            $("#best_proposal_full").val(proposal_min);
                        }
                    }
                }
            });
        }
        if(time_diff > 0) {
            time_diff--;
            count_time++;
            $("#time_diff").val(time_diff);
        } 
        if(count_time == count_refresh || time_diff <= 0) {
            update_flag = true;
        }    
        
        setTimeout("updateLot()",1000);
    }
    
    $(function() {
        updateLot();
        $("[name='CURRENCY_PROPOSAL']").change(function() {
            var curr_name = $(this).val(); 
            var curr_val = parseFloat($("[name='CURR["+curr_name+"]']").val()); 
            var curr_name_user = $("[name='CURR_USER']").val();
            var curr_val_user = parseFloat($("[name='CURR["+curr_name_user+"]']").val());
            $("[name='CURR_USER']").val(curr_name);

            //if($(".best_proposal_n").length) {
            $(".id_item_proposal").each(function(){
                var id = $(this).val();
                var best_proposal_full = Number($("#best_proposal_full_"+id).val()) ? parseFloat($("#best_proposal_full_"+id).val()) : 0;
                var price_nds_full = parseFloat($("#price_nds_full_"+id).val()) > 0 ? parseFloat($("#price_nds_full_"+id).val()) : $("[name=PROP_"+id+"_PRICE_NDS]").val();
                    
                if(best_proposal_full > 0) {
                    $("#best_proposal_"+id).text((best_proposal_full*curr_val_user/curr_val).toFixed(2));
                } 
                $("#best_proposal_full_"+id).val(best_proposal_full*curr_val_user/curr_val);
                $("[name=PROP_"+id+"_PRICE_NDS]").val((price_nds_full*curr_val_user/curr_val).toFixed(2));
                $("#price_nds_full_"+id).val(price_nds_full*curr_val_user/curr_val);
                    
            });
            //}
            //if($("#best_proposal").length) {
            var best_proposal_full = Number($("#best_proposal_full").val()) ? parseFloat($("#best_proposal_full").val()) : 0;
            if(best_proposal_full > 0) {
                $("#best_proposal").text(((best_proposal_full*curr_val_user)/curr_val).toFixed(2));
            }
            $("#best_proposal_full").val((best_proposal_full*curr_val_user)/curr_val);
            $("#item_proposal_price").val(((parseFloat($("#item_proposal_price_full").val())*curr_val_user)/curr_val).toFixed(2));
            $("#item_proposal_price_full").val((parseFloat($("#item_proposal_price_full").val())*curr_val_user)/curr_val);
            //}
            
        });
    });
</script>