<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>

<?
if (!isset($arResult["PROPOSAL"]))
    return false;
?>

<div class="t_prov">
    <a name="proposal_table"></a>
    <h3><?= GetMessage("PW_TD_LIST_PROPOSAL") ?></h3> [<?= GetMessage("PW_TD_CURRENCY") ?>: <?=$arResult["LOT"]["CURRENCY"]?>] <br />
    <form name="win_add" action="<?= POST_FORM_ACTION_URI ?>#proposal_table" method="post" enctype="multipart/form-data">
        <table class="t_lot_table">
            <tr>
                <? if (($arResult["OWNER"] == "Y" && $arResult["LOT_END"] == "Y" && count($arResult["WIN"]) == 0) || ($arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y")): ?>
                    <th><?= GetMessage("PW_TD_WINNER") ?></th>
                <? endif; ?>
                <th><?= GetMessage("PW_TD_SUPPLIER") ?></th>
                <th><?= GetMessage("PW_TD_MESSAGE") ?></th>
                <? if ($arParams["NDS_TYPE"] != "N"): ?>
                    <th><?= GetMessage("PW_TD_ITOGO") ?></th>
                    <th><?= GetMessage("PW_TD_ITOGO_N") ?></th>
                <? else: ?>
                    <th><?= GetMessage("PW_TD_ITOGO_N") ?></th>
                    <th><?= GetMessage("PW_TD_ITOGO") ?></th>
                <? endif; ?>
                <? if ($arResult["TYPE_ID"] != "S"): ?>
                    <th><?= GetMessage("PW_TD_SPEC") ?></th>
                <? endif; ?>
                <th><?= GetMessage("PW_TD_DOP_INFO") ?></th>
                <? if (($arResult["OWNER"] == "Y" && $arResult["LOT_END"] == "Y" && count($arResult["WIN"]) == 0) || ($arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y")): ?>
                    <th><?= GetMessage("PW_TD_COMMENTS") ?></th>
                <? endif; ?>
            </tr>
            <? foreach ($arResult["PROPOSAL"] as $idProp => $vProp): ?>
                <?
                $itogo = $arResult["PROPOSAL"][$idProp]["ITOGO"];
                $itogo_n = $arResult["PROPOSAL"][$idProp]["ITOGO_N"];
                if ($arResult["TYPE_ID"] != "S") {
                    $history = $arResult["PROPOSAL"][$idProp]["HISTORY"];
                }
                ?>
                <tr>
                    <?
                    $checked = in_array($vProp["USER_ID"], $arResult["WIN"]) ? " checked" : "";
                    ?>
                    <? if (($arResult["OWNER"] == "Y" && $arResult["LOT_END"] == "Y" && count($arResult["WIN"]) == 0) || ($arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y")): ?>
                        <td>
                            <input<?= $checked ?> type="checkbox" name="win[<?= $vProp["USER_ID"] ?>]" />
                        </td>
                    <? endif; ?>
                    <td> 
                        <? if (is_file($vProp["USER_INFO"]["LOGO_SMALL"])): ?>
                            <img src="<?= $vProp["USER_INFO"]["LOGO_SMALL"] ?>" alt="<?= $vProp["USER_INFO"]["STATUS_NAME"] ?>" /> 
                        <? endif; ?>
                        <a class="user_view" href="#" onclick="userView(<?= $vProp["ID"] ?>)"><?= strlen($vProp["USER_INFO"]["NAME_COMPANY"]) > 0 ? $vProp["USER_INFO"]["NAME_COMPANY"] : $vProp["USER_INFO"]["FIO"] ?></a>
                        <div style="display:none"> 
                            <div id="user_<?= $vProp["ID"] ?>">
                                <table class="t_lot_table">
                                    <tbody>
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_FIO") ?></td>
                                            <td><?= $vProp["USER_INFO"]["FIO"] ?></td>
                                        </tr> 
                                        <tr class="odd">
                                            <td><?= GetMessage("PW_TD_SUPPLIER_NAME_COMPANY") ?></td>
                                            <td><?= $vProp["USER_INFO"]["NAME_COMPANY"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_NAME_DIRECTOR") ?></td>
                                            <td><?= $vProp["USER_INFO"]["NAME_DIRECTOR"] ?></td>
                                        </tr> 
                                        <tr class="odd">
                                            <td><?= GetMessage("PW_TD_SUPPLIER_NAME_ACCOUNTANT") ?></td>
                                            <td><?= $vProp["USER_INFO"]["NAME_ACCOUNTANT"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_CODE") ?></b></td>
                                        </tr>    
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_CODE_INN") ?></td>
                                            <td><?= $vProp["USER_INFO"]["CODE_INN"] ?></td>
                                        </tr> 
                                        <tr class="odd">
                                            <td><?= GetMessage("PW_TD_SUPPLIER_CODE_KPP") ?></td>
                                            <td><?= $vProp["USER_INFO"]["CODE_KPP"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_CODE_OKVED") ?></td>
                                            <td><?= $vProp["USER_INFO"]["CODE_OKVED"] ?></td>
                                        </tr> 
                                        <tr class="odd">
                                            <td><?= GetMessage("PW_TD_SUPPLIER_CODE_OKPO") ?></td>
                                            <td><?= $vProp["USER_INFO"]["CODE_OKPO"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_LEGALADDRESS") ?></b></td>
                                        </tr>
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_REGION") ?></td>
                                            <td><?= $vProp["USER_INFO"]["LEGALADDRESS_REGION"] ?></td>
                                        </tr> 
                                        <tr class="odd">
                                            <td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_CITY") ?></td>
                                            <td><?= $vProp["USER_INFO"]["LEGALADDRESS_CITY"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_INDEX") ?></td>
                                            <td><?= $vProp["USER_INFO"]["LEGALADDRESS_INDEX"] ?></td>
                                        </tr> 
                                        <tr class="odd">
                                            <td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_STREET") ?></td>
                                            <td><?= $vProp["USER_INFO"]["LEGALADDRESS_STREET"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_POST") ?></td>
                                            <td><?= $vProp["USER_INFO"]["LEGALADDRESS_POST"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_POSTALADDRESS") ?></b></td>
                                        </tr>
                                        <tr class="odd">
                                            <td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_REGION") ?></td>
                                            <td><?= $vProp["USER_INFO"]["POSTALADDRESS_REGION"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_CITY") ?></td>
                                            <td><?= $vProp["USER_INFO"]["POSTALADDRESS_CITY"] ?></td>
                                        </tr> 
                                        <tr class="odd">
                                            <td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_INDEX") ?></td>
                                            <td><?= $vProp["USER_INFO"]["POSTALADDRESS_INDEX"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_STREET") ?></td>
                                            <td><?= $vProp["USER_INFO"]["POSTALADDRESS_STREET"] ?></td>
                                        </tr> 
                                        <tr class="odd">
                                            <td><?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_POST") ?></td>
                                            <td><?= $vProp["USER_INFO"]["POSTALADDRESS_POST"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_PHONE") ?></td>
                                            <td><?= $vProp["USER_INFO"]["PHONE"] ?></td>
                                        </tr> 
                                        <tr class="odd">
                                            <td><?= GetMessage("PW_TD_SUPPLIER_FAX") ?></td>
                                            <td><?= $vProp["USER_INFO"]["FAX"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_STATEREG") ?></b></td>
                                        </tr>
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_STATEREG_PLACE") ?></td>
                                            <td><?= $vProp["USER_INFO"]["STATEREG_PLACE"] ?></td>
                                        </tr> 
                                        <tr class="odd">
                                            <td><?= GetMessage("PW_TD_SUPPLIER_STATEREG_DATE") ?></td>
                                            <td><?= $vProp["USER_INFO"]["STATEREG_DATE"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_STATEREG_OGRN") ?></td>
                                            <td><?= $vProp["USER_INFO"]["STATEREG_OGRN"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td colspan="2"><b><?= GetMessage("PW_TD_GROUP_SUPPLIER_BANK") ?></b></td>
                                        </tr>
                                        <tr class="odd">
                                            <td><?= GetMessage("PW_TD_SUPPLIER_BANKING_NAME") ?></td>
                                            <td><?= $vProp["USER_INFO"]["BANKING_NAME"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNT") ?></td>
                                            <td><?= $vProp["USER_INFO"]["BANKING_ACCOUNT"] ?></td>
                                        </tr> 
                                        <tr class="odd">
                                            <td><?= GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNTCORR") ?></td>
                                            <td><?= $vProp["USER_INFO"]["BANKING_ACCOUNTCORR"] ?></td>
                                        </tr> 
                                        <tr>
                                            <td><?= GetMessage("PW_TD_SUPPLIER_BANKING_BIK") ?></td>
                                            <td><?= $vProp["USER_INFO"]["BANKING_BIK"] ?></td>
                                        </tr> 
                                        <? if ($vProp["USER_INFO"]["PROP"]): ?>
                                            <tr>
                                                <td colspan="2"><b><?= GetMessage("PW_TD_GROUP_DOP_PROP") ?></b></td>
                                            </tr>
                                            <? foreach ($vProp["USER_INFO"]["PROP"] as $arProp): ?>
                                                <? if ($vProp["USER_INFO"]["PROP_SUPPLIER"][$arProp["ID"]]): ?>
                                                    <tr>
                                                        <td><?= $arProp["TITLE"] ?></td>
                                                        <?
                                                        if ($arProp["PROPERTY_TYPE"] == "L") {
                                                            $arPropDef = unserialize(base64_decode($arProp["DEFAULT_VALUE"]));
                                                        }
                                                        $rsPropSupp = array();
                                                        $arFile = array();
                                                        foreach ($vProp["USER_INFO"]["PROP_SUPPLIER"][$arProp["ID"]] as $arPropSupp) {
                                                            if ($arProp["PROPERTY_TYPE"] == "L") {
                                                                $arPropSupp["VALUE"] = $arPropDef["DEFAULT_VALUE"][$arPropSupp["VALUE"]];
                                                            }
                                                            if ($arProp["PROPERTY_TYPE"] == "F") {
                                                                $rsFile = CFile::GetByID($arPropSupp["VALUE"]);
                                                                $arFile = $rsFile->Fetch();
                                                                $arPropSupp["VALUE"] = "<a href='" . CFile::GetPath($arPropSupp["VALUE"]) . "'>" . $arFile["ORIGINAL_NAME"] . "</a>";
                                                            }
                                                            if ($arProp["PROPERTY_TYPE"] == "D") {
                                                                $arPropSupp["VALUE"] = date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), strtotime($arPropSupp["VALUE"]));
                                                            }
                                                            $rsPropSupp[] = $arPropSupp["VALUE"];
                                                        }
                                                        ?>
                                                        <td><?= implode(",", $rsPropSupp) ?></td>
                                                    </tr> 
                                                <? endif; ?>
                                            <? endforeach; ?>
                                        <? endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                    <td align="center">
                        <? if (strlen($vProp["MESSAGE"]) > 0 || count($vProp["FILE"]) > 0): ?>
                            <a href="#" class="mess_view" onclick="messView(<?= $vProp["ID"] ?>)"><img src="/bitrix/components/pweb.tenderix/proposal.list/templates/.default/images/message_activ.png" /></a>
                            <div style="display:none"> 
                                <div id="mess_<?= $vProp["ID"] ?>">
                                    <h3><?= GetMessage("PW_TD_MESSAGE_FILE_SUPPLIER") ?> <?= $vProp["USER_INFO"]["NAME_COMPANY"] ?></h3>
                                    <p><b><?= GetMessage("PW_TD_FILE_SUPPLIER") ?>: </b><br />
                                        <?
                                        if (count($vProp["FILE"]) > 0) {
                                            foreach ($vProp["FILE"] as $arFile) {
                                                ?>
                                                <a href="/tx_files/proposal_file.php?PROPOSAL_ID=<?= $vProp["ID"] ?>&FILE_ID=<?= $arFile["ID"] ?>"><?= $arFile["ORIGINAL_NAME"] ?></a><br />
                                                <?
                                            }
                                        } else {
                                            echo "-";
                                        }
                                        ?>
                                    </p>
                                    <p><b><?= GetMessage("PW_TD_MESSAGE_SUPPLIER") ?>: </b><br />
                                        <?
                                        if (strlen($vProp["MESSAGE"]) > 0) {
                                            echo nl2br($vProp["MESSAGE"]);
                                        } else {
                                            echo "-";
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        <? else: ?>
                            <img src="/bitrix/components/pweb.tenderix/proposal.list/templates/.default/images/message_no.png" />    
                        <? endif; ?>
                    </td>
                    <td>
                <nobr><?php
                // Определяем, какой столбец показывать в зависимости от порядка заголовков и WITH_NDS
                if ($arParams["NDS_TYPE"] != "N") {
                    // Порядок: ITOGO (с НДС), ITOGO_N (без НДС)
                    // Первый столбец - с НДС
                    echo ($itogo > 0 && $arResult["LOT"]["WITH_NDS"] == "Y") ? number_format($itogo, 2, '.', ' ') : '--';
                } else {
                    // Порядок: ITOGO_N (без НДС), ITOGO (с НДС)
                    // Первый столбец - без НДС
                    echo ($itogo_n > 0 && $arResult["LOT"]["WITH_NDS"] != "Y") ? number_format($itogo_n, 2, '.', ' ') : '--';
                }
                ?></nobr>
                <? if (strlen($checked) > 0): ?>
                    <img src="/bitrix/components/pweb.tenderix/proposal.list/templates/.default/images/best-price.png" />  
                <? endif; ?> 
                </td>
                <td>
                <nobr><?php
                // Второй столбец
                if ($arParams["NDS_TYPE"] != "N") {
                    // Порядок: ITOGO (с НДС), ITOGO_N (без НДС)
                    // Второй столбец - без НДС
                    echo ($itogo_n > 0 && $arResult["LOT"]["WITH_NDS"] != "Y") ? number_format($itogo_n, 2, '.', ' ') : '--';
                } else {
                    // Порядок: ITOGO_N (без НДС), ITOGO (с НДС)
                    // Второй столбец - с НДС
                    echo ($itogo > 0 && $arResult["LOT"]["WITH_NDS"] == "Y") ? number_format($itogo, 2, '.', ' ') : '--';
                }
                ?></nobr>
                <? if (strlen($checked) > 0): ?>
                    <img src="/bitrix/components/pweb.tenderix/proposal.list/templates/.default/images/best-price.png" />  
                <? endif; ?> 
                </td>
                <? if ($arResult["TYPE_ID"] != "S"): ?>
                    <td align="center">
                        <a href="#" class="spec_view" onclick="specView(<?= $vProp["ID"] ?>)"><img src="/bitrix/components/pweb.tenderix/proposal.list/templates/.default/images/specification.png" /></a>
                        <div style="display:none"> 
                            <div id="spec_table_<?= $vProp["ID"] ?>">
                                <table class="t_lot_table" >
                                    <?
                                    $numProp = 1;
                                    $sum_item = 0;
                                    $itogo_sum_item = 0;
                                    ?>
                                    <? foreach ($history as $idPropBuyer => $specProp) : ?>
                                        <? if ($numProp == 1): ?>
                                            <tr>
                                                <th><?= GetMessage("PW_TD_SPEC_NUM") ?></th>
                                                <th><?= GetMessage("PW_TD_SPEC_TOVAR") ?></th>
                                                <th><?= GetMessage("PW_TD_SPEC_NDS") ?></th>
                                                <th>
                                                <?
                                                // Логика для заголовка столбца цены в зависимости от типа НДС лота
                                                if ($arResult['LOT']['WITH_NDS'] == 'Y') {
                                                    echo "Цена за ед. с НДС";
                                                } else {
                                                    echo "Цена за ед. без НДС";
                                                }
                                                ?>
                                                </th>
                                                <th><?= GetMessage("PW_TD_SPEC_PRICE_NDS_SUM") ?></th>
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
                                            <td align="center"><?= $specProp["NDS"] ?></td>
                                            <td align="center"><?= number_format($specProp["PRICE_NDS"], 2, '.', ' ') ?></td>
                                            <?
                                            $sum_item = floatval($specProp["PRICE_NDS"]) * floatval($specProp["COUNT"]);
                                            $itogo_sum_item += $sum_item;
                                            ?>
                                            <td align="center"><?= number_format($sum_item, 2, '.', ' ') ?></td>
                                        </tr>
                                        <? $numProp++; ?>
                                    <? endforeach; ?>
                                    <tr>
                                        <td colspan="4" align="right"><?= GetMessage("PW_TD_ITOGO_ALL") ?>:</td>
                                        <td><?= number_format($itogo_sum_item, 2, '.', ' ') ?></td>
                                    </tr>    
                                </table>
                            </div>
                        </div>
                    </td>
                <? endif; ?>
                <td>
                    <? if (strlen($vProp["TERM_PAYMENT_VAL"]) > 0): ?>
                        <b><?= GetMessage("PW_TD_PAYMENT") ?>:</b> <?= $vProp["TERM_PAYMENT_VAL"] ?><br />
                    <? endif; ?>
                    <? if (strlen($vProp["TERM_DELIVERY_VAL"]) > 0): ?>
                        <b><?= GetMessage("PW_TD_DELIVERY") ?>:</b> <?= $vProp["TERM_DELIVERY_VAL"] ?><br />
                    <? endif; ?>
                    <?
                    if ($arResult["TYPE_ID"] == "S") {
                        foreach ($vProp["PROP"] as $arProductProp) {
                            ?>
                            <b><?= $arProductProp["TITLE"] ?>:</b> <?= $arProductProp["VALUE"] ?><br />
                            <?
                        }
                    }
                    ?>

                    <?
                    /*                     * **************
                     * PROPERTY
                     * *************** */
                    ?>

                    <?
                    $arPropProposal = $arResult["PROP_PROPOSAL"][$idProp];
                    foreach ($arResult["PROP_LIST"] as $arPropList) :
                        ?>
                        <?
                        $is_file_prop = false;
                        $result_list = array();

                        if ($arPropList["PROPERTY_TYPE"] == "F" && ($rsFiles = CTenderixProposal::GetFileListProperty($idProp, $arPropList["ID"])) && ($arFile = $rsFiles->GetNext())) {
                            ?>
                            <?
                            $is_file_prop = true;
                            do {
                                $result_list[] = '<a href="/tx_files/property_file.php?PROPOSAL_ID=' . $idProp . '&amp;FILE_ID=' . $arFile["ID"] . '&amp;PROPERTY=' . $arPropList["ID"] . '">' . $arFile["ORIGINAL_NAME"] . '</a>';
                            } while ($arFile = $rsFiles->GetNext());
                        }

                        if (strlen($arPropList["DEFAULT_VALUE"]) > 0 && $arPropList["MULTI"] == "Y") {
                            $arPropList["MULTI_CNT"]++;
                        }
                        $cntProp = 0;
                        if ($arPropList["PROPERTY_TYPE"] != "L" && $arPropList["PROPERTY_TYPE"] != "F") {
                            $cntProp = count($arPropProposal[$arPropList["ID"]]);
                            $arPropList["MULTI_CNT"] = $cntProp;
                        }
                        if ($arPropList["PROPERTY_TYPE"] == "L" || $arPropList["MULTI"] == "N") {
                            $arPropList["MULTI_CNT"] = 1;
                        }

                        for ($i = 0; $i < $arPropList["MULTI_CNT"]; $i++) {
                            switch ($arPropList["PROPERTY_TYPE"]) {
                                case "S":
                                case "N":
                                case "T":
                                    if (strlen(htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"])) > 0)
                                        $result_list[] = htmlspecialcharsEx($arPropProposal[$arPropList["ID"]][$i]["VALUE"]);
                                    break;
                                case "L":
                                    $arrListValue = array();
                                    $arrList = unserialize(base64_decode($arPropList["DEFAULT_VALUE"]));
                                    foreach ($arPropProposal[$arPropList["ID"]] as $arrListSupplier) {
                                        $arrListValue[] = $arrListSupplier["VALUE"];
                                    }
                                    foreach ($arrList["DEFAULT_VALUE"] as $idRow => $listVal) {
                                        if (in_array($idRow, $arrListValue))
                                            $result_list[] = $listVal;
                                    }
                                    break;
                                case "D":
                                    $result_list[] = ConvertTimeStamp(strtotime($arPropProposal[$arPropList["ID"]][$i]["VALUE"]), "FULL");
                                    break;
                            }
                        }
                        if (count($result_list) > 0) {
                            echo "<b>" . $arPropList["TITLE"] . ":</b>" . implode(", ", $result_list) . "<br />";
                        }
                        ?>
                    <? endforeach; ?>
                    <?
                    /*                     * **************
                     * PROPERTY
                     * *************** */
                    ?>
                </td>
                <? if (($arResult["OWNER"] == "Y" && $arResult["LOT_END"] == "Y" && count($arResult["WIN"]) == 0) || ($arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y")): ?>
                    <td>
                        <textarea name="comment[<?= $vProp["USER_ID"] ?>]" rows="2" cols="15"><?=$arResult["WIN_COMMENT"][$vProp["USER_ID"]]?></textarea>
                    </td>
                <? endif; ?>
                </tr>
            <? endforeach; ?>
        </table>
        <br /><br /> 
        <? if (($arResult["OWNER"] == "Y" && $arResult["LOT_END"] == "Y" && count($arResult["WIN"]) == 0) || ($arResult["RIGHT"] == "W" && $arResult["LOT_END"] == "Y")): ?> 
            <input type="submit" name="win_add_submit" value="<?= GetMessage("PW_TD_SUBMIT_WIN") ?>" />
        <? endif; ?>
    </form>
</div>     
<br clear="all" />
