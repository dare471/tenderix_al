<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>

<?
$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/pweb.tenderix/colorbox/jquery.colorbox-min.js"></script>', true);
//$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/components/pweb.tenderix/lot.add/templates/.default/autocomplete/jquery.autocomplete.js"></script>', true);
//$APPLICATION->AddHeadString('<link href="/bitrix/components/pweb.tenderix/lot.add/templates/.default/autocomplete/jquery.autocomplete.css" type="text/css" rel="stylesheet">', true);
$APPLICATION->AddHeadString('<link href="/bitrix/js/pweb.tenderix/colorbox/colorbox.css" type="text/css" rel="stylesheet">', true);
?>
<script type="text/javascript">
    $(function() {
        arrItem();
        $("#addItem").click(function() {
            arrItem();
        });
        $("#supplier-count").html(" ("+$("select[id=select-private-supplier] option").size()+")");
        
        $("#supplier-view").colorbox({
            inline:true, 
            href: "#supplier-block",
            opacity: 0.5,
            maxWidth:"80%", 
            maxHheight:"80%",
            top: "10%",
            onClosed: function() {
                $("#select-private-supplier option").each(function(){
                    this.selected=true;
                });
                $("#supplier-count").html(" ("+$("select[id=select-private-supplier] option").size()+")");
            },
            onOpen: function() {
                $('#select-private-supplier option:selected').each(function(){
                    this.selected=false;
                });
            }
        });
        
        $("#button-select-add-supplier").click(function() {
            var select_val = "";
            var select_html = "";
            $("#select-all-supplier option").each(function() {
                select_val = $(this).val();
                select_html = $(this).html();
                var select_true = false;
                if($(this).attr("selected") == "selected") {
                    $("#select-private-supplier option").each(function() {
                        if($(this).val() == select_val) select_true = true;
                    });
                    if(!select_true) {
                        $("#select-private-supplier").append('<option value="'+select_val+'">'+select_html+'</option>');
                    }
                }
            });
        });
        
        $("#button-select-del-supplier").click(function() {
            $('#select-private-supplier option:selected').each(function(){
                $(this).remove();
            });
        });
        
        $("#private").click(function() {
            var supplier_cnt = $("select[id=select-all-supplier] option").size();
            if($(this).is(":checked") && supplier_cnt <= 0) {
                $.ajax({
                    url: "<?= $templateFolder ?>/ajax.php",
                    type: "POST",
                    data: "action=getSupplier",
                    dataType: "json",
                    beforeSend: function() {
                        $("#supplier-view-load").show();
                    },
                    success: function(data) {
                        for(var i=0;i<data.length;i++){
                            $("#select-all-supplier").append('<option value="'+data[i].id+'">'+data[i].company+'</option>');
                        }
                        $("#supplier-view-load").hide();
                        $("#supplier-view").show();
                    }
                });
            }
            if($(this).is(":checked") && supplier_cnt > 0) {
                $("#supplier-view").show();
            } else {
                $("#supplier-view").hide(); 
            }
        });
        
        /* $('#okdp').autocomplete({
            serviceUrl: "<?= $templateFolder ?>/ajax.php",
            minChars: 2,
            maxHeight: 400,
            width: 300,
            zIndex: 9999,
            deferRequestBy: 300,
            params: {action:"getOkdp"}
        });*/
        /*$("#okdp").autocomplete("<?= $templateFolder ?>/ajax.php", {
            width: 500,
            minChars: 2,
            mustMatch: true,
            autoFill: true,
            formatResult: function(row) {
                //$("#okdp").val(row[1]);
                $("#okdp_description").html(row[2]);
            }
        });*/

    });
            
    function arrItem() {
        var numProp = $("#numProp").val();
        var newProp = $("#newProp").val();
        $.ajax({
            url: "<?= $templateFolder ?>/ajax.php",
            type: "POST",
            data: "action=addItem&numProp="+numProp+"&newProp="+newProp,
            beforeSend: function() {
                $("#addItem").attr("disabled", true);
            },
            success: function(data) {
                $("#numProp").val(parseInt(numProp)+1);
                $("#newProp").val(parseInt(newProp)+1);
                $("#table_spec").append(data);
                $("#addItem").attr("disabled", false);
            }
        });
    }
</script>

<div class="lot-add">
    <? if (strlen($arResult["ERRORS"]) > 0): ?>
        <div class="errors-tender"><?= $arResult["ERRORS"] ?></div>
    <? endif; ?>
    <form name="lotadd_form" action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data">
        <h3><?= GetMessage("PW_TD_BASE_TITLE") ?></h3>
        <table class="t_lot_table">
            <? if ($arResult["LOT"]["ID"] > 0): ?>
                <tr>
                    <td width="40%" class="left-col"><?= GetMessage("PW_TD_NUM_LOT") ?>:</td>
                    <td width="60%" class="right-col"><? echo $arResult["LOT"]["ID"] ?></td>
                </tr>
            <? endif; ?>

            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_ACTIVE") ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="checkbox" name="ACTIVE" value="Y" <?
                    if ($arResult["LOT"]["ACTIVE"] == "Y")
                        echo " checked";
                    ?> />
                </td>
            </tr>

            <? if ($arResult["LOT"]["ID"] <= 0): ?>
                <tr>
                    <td width="40%" class="left-col"><span class="required">*</span><?= GetMessage("PW_TD_TYPE_PRODUCT") ?>:</td>
                    <td width="60%" class="right-col">
                        <select onchange="if(this[this.selectedIndex].value!='') window.location=this[this.selectedIndex].value;" <?= (defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ? "disabled=\"disabled\"" : "") ?>>     
                            <option<?= $arResult["TYPE_ID"] == "N" ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE_ID=N", array("TYPE_ID")) ?>"><?= GetMessage("PW_TD_TYPE_PRODUCT_NST") ?></option>
                            <option<?= $arResult["TYPE_ID"] == "S" ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE_ID=S", array("TYPE_ID")) ?>"><?= GetMessage("PW_TD_TYPE_PRODUCT_ST") ?></option>
                            <option<?= $arResult["TYPE_ID"] == "P" ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE_ID=P", array("TYPE_ID")) ?>"><?= GetMessage("PW_TD_TYPE_SALE") ?></option>
                        </select>
                        <input type="hidden" name="TYPE_ID" value="<?= $arResult["TYPE_ID"] ?>" />
                    </td>
                </tr>
            <? else: ?>
                <input type="hidden" name="TYPE_ID" value="<?= $arResult["TYPE_ID"] ?>" />
            <? endif; ?>

            <? if ($arResult["TYPE_ID"] != 'S'): ?>

                <tr>
                    <td width="40%" class="left-col">
                        <span class="required">*</span><?= GetMessage("PW_TD_TITLE") ?>:
                    </td>
                    <td width="60%" class="right-col">
                        <input type="text" name="TITLE" value="<?= htmlspecialcharsEx($arResult["LOT"]["TITLE"]) ?>" size="50" />
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="left-col">
                        <span class="required">*</span><?= GetMessage("PW_TD_SECTION_NAME") ?>:
                    </td>
                    <td width="60%" class="right-col">
                        <select name="SECTION_ID">
                            <option value="">--</option>
                            <? foreach ($arResult["SECTION_ARR"][0] as $sec): ?>
                                <option<?= $arResult["LOT"]["SECTION_ID"] == $sec["ID"] ? " selected" : ""; ?> value="<?= $sec["ID"] ?>"><?= $sec["TITLE"] ?></option>
                            <? endforeach; ?>
                            <? foreach ($arResult["CATALOG"] as $cat_id => $cat_name) : ?>
                                <optgroup label="<?= $cat_name ?>">
                                    <? foreach ($arResult["SECTION_ARR"][$cat_id] as $sec): ?>
                                        <option<?= $arResult["LOT"]["SECTION_ID"] == $sec["ID"] ? " selected" : ""; ?> value="<?= $sec["ID"] ?>"><?= $sec["TITLE"] ?></option>
                                    <? endforeach; ?>
                                </optgroup>
                            <? endforeach; ?>
                            </section>
                    </td>
                </tr>
                <? if ($arResult["TYPE_ID"] == 'P'): ?>
                    <input type="hidden" name="NOT_ANALOG" value="Y" />
                <? endif; ?>
                <? if ($arResult["TYPE_ID"] == 'N'): ?>
                    <tr>
                        <td width="40%" class="left-col">
                            <?= GetMessage("PW_TD_FULL_SPEC") ?>:
                        </td>
                        <td width="60%" class="right-col">
                            <input type="checkbox" name="FULL_SPEC" value="Y"<?= $arResult["LOT"]["FULL_SPEC"] == "Y" ? " checked" : ""; ?> />
                        </td>
                    </tr>
                    <tr>
                        <td width="40%" class="left-col">
                            <?= GetMessage("PW_TD_NOT_ANALOG") ?>:
                        </td>
                        <td width="60%" class="right-col">
                            <input type="checkbox" name="NOT_ANALOG" value="Y"<?= $arResult["LOT"]["NOT_ANALOG"] == "Y" ? " checked" : ""; ?> />
                        </td>
                    </tr>
                <? endif; ?>
            <? endif; ?>
            <? if ($arResult["TYPE_ID"] == 'S'): ?>
                <tr>
                    <td width="40%" class="left-col"><span class="required">*</span><?= GetMessage("PW_TD_LIST_PRODUCT") ?>:</td>
                    <td width="60%" class="right-col">
                        <select<?= $arResult["LOT"]["ID"] > 0 ? " disabled" : ""; ?> onchange="if(this[this.selectedIndex].value!='') window.location=this[this.selectedIndex].value;" <?= (defined('BX_PUBLIC_MODE') && BX_PUBLIC_MODE == 1 ? "disabled=\"disabled\"" : "") ?>>
                            <? foreach ($arResult["PRODUCTS"] as $products_id => $products_title): ?>
                                <option<?= $arResult["PRODUCTS_ID"] == $products_id ? " selected" : ""; ?> value="<?= $arResult["PRODUCTS_URL"][$products_id] ?>"><?= $products_title ?></option>  
                            <? endforeach; ?>
                        </select>
                        <input type="hidden" value="<?= $arResult["PRODUCTS_ID"] ?>" name="PRODUCTS_ID" />
                    </td>
                </tr>
            <? endif; ?>

            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_OPEN_PRICE") ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="checkbox" name="OPEN_PRICE" value="Y"<?= $arResult["LOT"]["OPEN_PRICE"] == "Y" ? " checked" : ""; ?> />
                </td>
            </tr>
            <? if ($arResult["TYPE_ID"] != 'P'): ?>
                <? if ($arResult["T_RIGHT"] == "W" || ($arResult["T_RIGHT"] == "S" && $arResult["LOT"]["ID"] == 0) || $arResult["LOT"]["NOTVISIBLE_PROPOSAL"] == "N"): ?>
                    <tr>
                        <td width="40%" class="left-col">
                            <?= GetMessage("PW_TD_NOTVISIBLE_PROPOSAL") ?>:
                        </td>
                        <td width="60%" class="right-col">
                            <input type="checkbox" name="NOTVISIBLE_PROPOSAL" value="Y"<?= $arResult["LOT"]["NOTVISIBLE_PROPOSAL"] == "Y" ? " checked" : ""; ?> />
                        </td>
                    </tr>
                <? endif; ?>
                <tr>
                    <td width="40%" class="left-col">
                        <?= GetMessage("PW_TD_PRIVATE") ?>:
                    </td>
                    <td width="60%" class="right-col">
                        <input type="checkbox" id="private" name="PRIVATE" value="Y"<?= $arResult["LOT"]["PRIVATE"] == "Y" ? " checked" : ""; ?> />&nbsp;&nbsp;
                        <a id="supplier-view" href="#"<?= $arResult["LOT"]["PRIVATE"] == "Y" ? '' : ' style="display:none;"'; ?>><?= GetMessage("PW_TD_PRIVATE_USER_SELECTED") ?><span id="supplier-count"></span></a>
                        <span id="supplier-view-load" style="display:none;"><?= GetMessage("PW_TD_PRIVATE_USER_LOAD") ?></span>
                        <div style="display:none;">
                            <div id="supplier-block">
                                <h3><?= GetMessage("PW_TD_PRIVATE_USER") ?></h3>
                                <table>
                                    <tr>
                                        <td><?= GetMessage("PW_TD_PRIVATE_USER_ALL") ?>:</td>
                                        <td></td>
                                        <td><?= GetMessage("PW_TD_PRIVATE_USER_SELECT") ?>:</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select id="select-all-supplier" class="supplier-select" multiple size="20">
                                                <? if ($arResult["LOT"]["PRIVATE"] == "Y"): ?>
                                                    <? foreach ($arResult["LOT"]["PRIVATE_USER"] as $arSupplier) : ?>
                                                        <option value="<?= $arSupplier["id"] ?>"><?= $arSupplier["company"] ?></option>
                                                    <? endforeach; ?>
                                                <? endif; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="button" id="button-select-add-supplier" value=" > " /><br />
                                            <input type="button" id="button-select-del-supplier" value=" < " />
                                        </td>
                                        <td>
                                            <select name="PRIVATE_LIST[]" id="select-private-supplier" class="supplier-select" multiple size="20">
                                                <? if ($arResult["LOT"]["PRIVATE"] == "Y"): ?>
                                                    <? foreach ($arResult["LOT"]["PRIVATE_LIST"] as $arSupplier) : ?>
                                                        <option selected value="<?= $arSupplier["id"] ?>"><?= $arSupplier["company"] ?></option>
                                                    <? endforeach; ?>
                                                <? endif; ?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            <? endif; ?>

            <? if ($arParams["COMPANY_ONLY"] == "N"): ?>    
                <tr>
                    <td width="40%" class="left-col">
                        <span class="required">*</span><?= GetMessage("PW_TD_COMPANY_LOT") ?>:
                    </td>
                    <td width="60%" class="right-col">
                        <select name="COMPANY_ID">
                            <? foreach ($arResult["COMPANY"] as $comapny_id => $company_title): ?>
                                <option<?= $arResult["LOT"]["COMPANY_ID"] == $comapny_id ? " selected" : ""; ?> value="<? echo $comapny_id ?>"><? echo $company_title ?></option>
                            <? endforeach; ?>
                        </select>
                    </td>
                </tr>
            <? endif; ?>

            <tr>
                <td width="40%" class="left-col">
                    <span class="required">*</span><?= GetMessage("PW_TD_RESPONSIBLE_FIO") ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="text" name="RESPONSIBLE_FIO" value="<?= trim(htmlspecialcharsEx($arResult["LOT"]["RESPONSIBLE_FIO"])) ?>" size="50" />
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <span class="required">*</span><?= GetMessage("PW_TD_RESPONSIBLE_PHONE") ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="text" name="RESPONSIBLE_PHONE" value="<?= trim(htmlspecialcharsEx($arResult["LOT"]["RESPONSIBLE_PHONE"])) ?>" size="50" />
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <span class="required">*</span><?= GetMessage("PW_TD_LOT_DATE") . " (" . CLang::GetDateFormat() . ")" ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="text" name="DATE_START" value="<?= $arResult["LOT"]["DATE_START"] ?>" class="valid" readonly size="19" />
                    <?
                    $APPLICATION->IncludeComponent(
                            'bitrix:main.calendar', '', array(
                        'SHOW_INPUT' => 'N',
                        'FORM_NAME' => 'lotadd_form',
                        'INPUT_NAME' => 'DATE_START',
                        'INPUT_VALUE' => $arResult["LOT"]["DATE_START"],
                        'SHOW_TIME' => 'Y',
                        'HIDE_TIMEBAR' => 'N'
                            ), null, array('HIDE_ICONS' => 'Y')
                    );
                    ?>
                    ...
                    <input type="text" name="DATE_END" value="<?= $arResult["LOT"]["DATE_END"] ?>" class="valid" readonly size="19" />
                    <?
                    $APPLICATION->IncludeComponent(
                            'bitrix:main.calendar', '', array(
                        'SHOW_INPUT' => 'N',
                        'FORM_NAME' => 'lotadd_form',
                        'INPUT_NAME' => 'DATE_END',
                        'INPUT_VALUE' => $arResult["LOT"]["DATE_END"],
                        'SHOW_TIME' => 'Y',
                        'HIDE_TIMEBAR' => 'N'
                            ), null, array('HIDE_ICONS' => 'Y')
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_TIME_UP") ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="text" name="TIME_EXTENSION" value="<?= htmlspecialcharsEx($arResult["LOT"]["TIME_EXTENSION"]) ?>" size="10" />
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_TIME_UPDATE") ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="text" name="TIME_UPDATE" value="<?= htmlspecialcharsEx($arResult["LOT"]["TIME_UPDATE"]) ?>" size="10" />
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_TYPE_NDS") ?>:
                </td>
                <td width="60%" class="right-col">
                    <select name="WITH_NDS">
                        <option<?= $arResult["LOT"]["WITH_NDS"] == "Y" ? " selected" : ""; ?> value="Y"><?= GetMessage("PW_TD_PRICE_NDS") ?></option>
                        <option<?= $arResult["LOT"]["WITH_NDS"] == "N" ? " selected" : ""; ?> value="N"><?= GetMessage("PW_TD_PRICE_NDS_N") ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_CURRENCY") ?>:
                </td>
                <td width="60%" class="right-col">
                    <select name="CURRENCY">
                        <? foreach ($arResult["LOT"]["CURRENCY_ARRAY"] as $nameCurrency => $arCurrency): ?>
                            <option<?
                        if ($arResult["LOT"]["CURRENCY"] == $nameCurrency)
                            echo " selected";
                            ?> value="<?= $nameCurrency ?>"><?= $nameCurrency ?><?
                            if (strlen($arCurrency) > 0)
                                echo " [" . $arCurrency . "]";
                            ?></option>
                        <? endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_DATE_DELIVERY") ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="text" name="DATE_DELIVERY" value="<?= htmlspecialcharsEx($arResult["LOT"]["DATE_DELIVERY"]) ?>" size="50" />
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_NOTE_LOT") ?>:
                </td>
                <td width="60%" class="right-col">
                    <? if ($arParams["VISUAL_EDITOR"] == "Y"): ?>
                        <?
                        $APPLICATION->IncludeComponent(
                                "bitrix:fileman.light_editor", ".default", Array(
                            "CONTENT" => $arResult["LOT"]["NOTE"],
                            "INPUT_NAME" => "NOTE",
                            "WIDTH" => "600px",
                            "HEIGHT" => "200px",
                            "USE_FILE_DIALOGS" => "N",
                            "FLOATING_TOOLBAR" => "Y",
                            "ARISING_TOOLBAR" => "Y",
                                )
                        );
                        ?>
                    <? else: ?>
                        <textarea name="NOTE" cols="50" rows="5"><?= htmlspecialcharsEx($arResult["LOT"]["NOTE"]) ?></textarea>
                    <? endif; ?>
                </td>
            </tr>

            <? /*             * ************
              DELIVERY_SECTION
             * ************ */ ?>
            <tr class="heading">
                <td colspan="2"><?= GetMessage("PW_TD_DELIVERY_SECTION") ?></td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_DELIVERY_SELECT") ?>:
                </td>
                <td width="60%" class="right-col">
                    <select name="TERM_DELIVERY_ID">
                        <option value="0">--</option>
                        <? foreach ($arResult["DELIVERY"] as $delivery_id => $delivery_title) : ?>
                            <option<?
                        if ($arResult["LOT"]["TERM_DELIVERY_ID"] == $delivery_id)
                            echo " selected";
                            ?> value="<?= $delivery_id ?>"><?= $delivery_title ?></option>
                            <? endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_DELIVERY_VALUE") ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="text" name="TERM_DELIVERY_VAL" value="<?= htmlspecialcharsEx($arResult["LOT"]["TERM_DELIVERY_VAL"]) ?>" size="50" />
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_DELIVERY_REQUIRED") ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="checkbox" name="TERM_DELIVERY_REQUIRED" value="Y" <?
                    if ($arResult["LOT"]["TERM_DELIVERY_REQUIRED"] == "Y")
                        echo " checked";
                    ?> />
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_DELIVERY_EDIT") ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="checkbox" name="TERM_DELIVERY_EDIT" value="Y" <?
                    if ($arResult["LOT"]["TERM_DELIVERY_EDIT"] == "Y")
                        echo " checked";
                    ?> />
                </td>
            </tr>
            <?
            /*             * ************
              PAYMENT_SECTION
             * ************ */
            ?>
            <tr class="heading">
                <td colspan="2"><?= GetMessage("PW_TD_PAYMENT_SECTION") ?></td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_PAYMENT_SELECT") ?>:
                </td>
                <td width="60%" class="right-col">
                    <select name="TERM_PAYMENT_ID">
                        <option value="0">--</option>
                        <? foreach ($arResult["PAYMENT"] as $payment_id => $payment_title) : ?>
                            <option<?
                        if ($arResult["LOT"]["TERM_PAYMENT_ID"] == $payment_id)
                            echo " selected";
                            ?> value="<?= $payment_id ?>"><?= $payment_title ?></option>
                            <? endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_PAYMENT_VALUE") ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="text" name="TERM_PAYMENT_VAL" value="<?= htmlspecialcharsEx($arResult["LOT"]["TERM_PAYMENT_VAL"]) ?>" size="50" />
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_PAYMENT_REQUIRED") ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="checkbox" name="TERM_PAYMENT_REQUIRED" value="Y" <?
                    if ($arResult["LOT"]["TERM_PAYMENT_REQUIRED"] == "Y")
                        echo " checked";
                    ?> />
                </td>
            </tr>
            <tr>
                <td width="40%" class="left-col">
                    <?= GetMessage("PW_TD_PAYMENT_EDIT") ?>:
                </td>
                <td width="60%" class="right-col">
                    <input type="checkbox" name="TERM_PAYMENT_EDIT" value="Y" <?
                    if ($arResult["LOT"]["TERM_PAYMENT_EDIT"] == "Y")
                        echo " checked";
                    ?> />
                </td>
            </tr>


            <tr class="heading">
                <td colspan="2"><? echo GetMessage("PW_TD_DOCUMENT") ?></td>
            </tr>
            <? if (count($arResult["LOT"]["FILE"]) > 0): ?>
                <tr>
                    <td valign="top"><?= GetMessage("PW_TD_FILE_ATTACH_LIST") ?>:</td>
                    <td>
                        <table class="t_lot_table">
                            <tr>
                                <th><? echo GetMessage("PW_TD_FILE_NAME") ?></th>
                                <th><? echo GetMessage("PW_TD_FILE_SIZE") ?></th>
                                <th><? echo GetMessage("PW_TD_FILE_DELETE") ?></th>
                            </tr>
                            <? foreach ($arResult["LOT"]["FILE"] as $arFile) : ?>
                                <tr>
                                    <td><a href="/tx_files/lot_file.php?LOT_ID=<? echo $arResult["LOT"]["ID"] ?>&amp;FILE_ID=<? echo $arFile["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a></td>
                                    <td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
                                    <td align="center">
                                        <input type="checkbox" name="FILE_ID[<? echo $arFile["ID"] ?>]" value="<? echo $arFile["ID"] ?>">
                                    </td>
                                </tr>
                            <? endforeach; ?>
                        </table>
                    </td>
                </tr>
            <? endif; ?>
            <tr>
                <td class="reg-field" width="40%" class="left-col">&nbsp;</td>
                <td width="60%" class="right-col">
                    <? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
                    <? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
                    <? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
                </td>
            </tr>
        </table>


        <?
        /*         * ************
          NESTANDART LOT SECTION
         * ************ */
        if ($arResult["TYPE_ID"] != 'S') :
            ?>

            <h3><?= GetMessage("PW_TD_SPEC_TITLE") ?></h3>    
            <table id="table_spec" cellpadding="0" cellspacing="0" width="100%" class="t_lot_table">
                <tr>
                    <td align="center" width="3%"><? echo GetMessage("PW_TD_NUM") ?></td>
                    <td align="center" width="30%"><span class="required">*</span><? echo GetMessage("PW_TD_SPEC_NAME") ?></td>
                    <td align="center" width="30%"><? echo GetMessage("PW_TD_SPEC_DOP") ?></td>
                    <td align="center" width="5%"><span class="required">*</span><? echo GetMessage("PW_TD_SPEC_UNIT") ?></td>
                    <td align="center" width="10%"><span class="required">*</span><? echo GetMessage("PW_TD_SPEC_COUNT") ?></td>
                    <td align="center" width="10%"><? echo GetMessage("PW_TD_SPEC_START_PRICE") ?></td>
                    <td align="center" width="10%"><? echo GetMessage("PW_TD_SPEC_STEP_PRICE") ?></td>
                    <td align="center" width="2%"><? echo GetMessage("PW_TD_SPEC_DEL") ?></td>
                </tr>
                <? $numProp = 1; ?>
                <? foreach ($arResult["LOT"]["SPEC"] as $PROP): ?>
                    <tr>
                        <td align="center" width="5"><? echo $numProp ?></td>
                        <td align="center" width="120">
                            <input type="text" name="PROP_<?= $PROP["PROP_ID"] ?>_TITLE" value="<?= htmlspecialcharsEx($PROP["TITLE"]) ?>" style="width: 98%" />
                        </td>
                        <td align="center" width="80">
                            <input type="text" name="PROP_<?= $PROP["PROP_ID"] ?>_ADD_INFO" value="<?= htmlspecialcharsEx($PROP["ADD_INFO"]) ?>" style="width: 98%" />
                        </td> 
                        <td align="center" width="20">
                            <select name="PROP_<?= $PROP["PROP_ID"] ?>_UNIT_ID">
                                <? foreach ($arResult["UNIT"] as $unit_id => $unit_title): ?>
                                    <option<?
                        if ($PROP["UNIT_ID"] == $unit_id)
                            echo " selected";
                                    ?> value="<?= $unit_id ?>"><?= $unit_title ?></option>
                                    <? endforeach; ?>
                            </select>
                        </td>
                        <td align="center" width="20">
                            <input type="text" name="PROP_<?= $PROP["PROP_ID"] ?>_COUNT" value="<?= $PROP["COUNT"] ?>" style="width: 98%" />
                        </td>
                        <td align="center" width="50">
                            <input type="text" name="PROP_<?= $PROP["PROP_ID"] ?>_START_PRICE" value="<?= $PROP["START_PRICE"] ?>" style="width: 98%" />
                        </td>
                        <td align="center" width="50">
                            <input type="text" name="PROP_<?= $PROP["PROP_ID"] ?>_STEP_PRICE" value="<?= $PROP["STEP_PRICE"] ?>" style="width: 98%" />
                        </td>
                        <td align="center" width="5">
                            <? if (intval($PROP["PROP_ID"]) > 0): ?>
                                <input type="checkbox" name="PROP_<?= $PROP["PROP_ID"] ?>_DEL" value="Y" />
                            <? endif ?>
                            <input type="hidden" name="PROP_HIDDEN_ID[]" value="<?= $PROP["PROP_ID"] ?>" />
                        </td>
                    </tr>
                    <? $numProp++; ?>
                <? endforeach; ?>    

            </table>
            <input type="hidden" id="numProp" value="<?= $numProp ?>" />
            <input type="hidden" id="newProp" name="newProp" value="<?= isset($arResult["SPEC_NEW_PROP"]) ? $arResult["SPEC_NEW_PROP"] : 0; ?>" />

        <? endif; ?>
        <? if ($arResult["TYPE_ID"] == 'S'): ?>
            <? if ($arResult["PRODUCTS_ID"] > 0): ?>
                <?
                /*                 * ************
                  STANDART LOT SECTION
                 * ************ */
                ?>
                <h3><?= GetMessage("PW_TD_TOVAR_TITLE") ?></h3>
                <table cellpadding="0" cellspacing="0" width="100%" class="t_lot_table">
                    <?
                    if ($arResult["ID"] <= 0) {
                        $str_PROP_PROD_VISIBLE = "Y";
                    }
                    ?>

                    <tr class="heading">
                        <td align="center" width="5%"><? echo GetMessage("PW_TD_PRODUCT_PROP_VISIBLE") ?></td>
                        <td align="center" width="40%"><? echo GetMessage("PW_TD_PRODUCT_NAME") ?></td>
                        <td align="center" width="40%"><? echo GetMessage("PW_TD_PRODUCT_VALUE") ?></td>
                        <td align="center" width="10%"><? echo GetMessage("PW_TD_PRODUCT_REQUIRED") ?></td>
                        <td align="center" width="20%"><? echo GetMessage("PW_TD_PRODUCT_EDIT") ?></td>
                    </tr>


                    <? foreach ($arResult["LOT"]["TOVAR"]["PROP"] as $prop_arr): ?>
                        <tr>
                            <td align="center" width="5">
                                <input type="checkbox" name="PROP_PROD_<?= $prop_arr["ID"] ?>_VISIBLE" <?
            if ($prop_arr["VISIBLE"] == "Y")
                echo "checked";
                        ?> value="Y" />
                            </td>
                            <td align="center" width="120">
                                <? echo $prop_arr["TITLE"] ?>
                            </td>
                            <td align="left" width="80">
                                <? if (is_array($prop_arr["SPR_ID"])): ?>
                                    <select name="PROP_PROD_<?= $prop_arr["ID"] ?>_VALUE">
                                        <? foreach ($prop_arr["SPR_ID"] as $sprId => $sprTitle): ?>
                                            <option<?
                        if ($sprId == $prop_arr["VALUE"])
                            echo " selected";
                                            ?> value="<?= $sprId ?>"><?= $sprTitle ?></option>
                                            <? endforeach; ?>
                                    </select>
                                <? else: ?>
                                    <input type="text" name="PROP_PROD_<?= $prop_arr["ID"] ?>_VALUE" value="<?= htmlspecialcharsEx($prop_arr["VALUE"]) ?>" style="width: 98%">
                                <? endif; ?>
                            </td>
                            <td align="center" width="20">
                                <input type="checkbox" name="PROP_PROD_<?= $prop_arr["ID"] ?>_REQUIRED" <?
                    if ($prop_arr["REQUIRED"] == "Y")
                        echo "checked";
                                ?> value="Y" />
                            </td>
                            <td align="center" width="20">
                                <input type="checkbox" name="PROP_PROD_<?= $prop_arr["ID"] ?>_EDIT" <?
                           if ($prop_arr["EDIT"] == "Y")
                               echo "checked";
                                ?> value="Y" />
                            </td>
                        </tr>
                    <? endforeach; ?>
                    <tr>
                        <td align="center" width="5"></td>
                        <td align="center" width="120"><?= GetMessage("PW_TD_STANDART_COUNT_NAME") ?></td>
                        <td align="left" width="80">
                            <input type="text" name="COUNT" value="<?= $arResult["LOT"]["TOVAR"]["COUNT"] ?>" style="width: 98%">
                        </td>
                        <td align="center" width="20"></td>
                        <td align="center" width="20">
                            <input type="checkbox" name="COUNT_EDIT" <?
            if ($arResult["LOT"]["TOVAR"]["COUNT_EDIT"] == "Y")
                echo "checked";
                    ?> value="Y" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" width="5"></td>
                        <td align="center" width="120"><?= GetMessage("PW_TD_STANDART_UNIT_NAME") ?></td>
                        <td align="left" width="80"><input type="text" value="<?= $arResult["LOT"]["TOVAR"]["UNIT_NAME"] ?>" disabled /></td>
                        <td align="center" width="20"></td>
                        <td align="center" width="20"></td>
                    </tr>

                    <tr class="heading">
                        <td colspan="5"><?= GetMessage("PW_TD_STANDART_PRICE_SECTION") ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <?= GetMessage("PW_TD_STANDART_START_PRICE") ?>:
                        </td>
                        <td>
                            <input type="text" name="START_PRICE" value="<?= htmlspecialcharsEx($arResult["LOT"]["TOVAR"]["START_PRICE"]) ?>" />
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <?= GetMessage("PW_TD_STANDART_STEP_PRICE") ?>:
                        </td>
                        <td>
                            <input type="text" name="STEP_PRICE" value="<?= htmlspecialcharsEx($arResult["LOT"]["TOVAR"]["STEP_PRICE"]) ?>" />
                        </td>
                        <td></td>
                        <td></td>
                    </tr>

                <? else: ?>
                    <tr><td>
                            <? echo GetMessage("PW_TD_PRODUCTS_NOSELECT") ?>
                        </td></tr>
                <? endif; ?>
            </table>
        <? endif; ?>




        <input type="submit" name="lotadd_submit" value="<?= GetMessage("PW_TD_ADD_LOT") ?>" />
        <? if ($arResult["TYPE_ID"] != 'S'): ?>
            <input type="button" id="addItem" value="<?= GetMessage("PW_TD_BUTTON_ADD_ITEM") ?>" />
        <? endif; ?>
    </form>
</div>     
<br clear="all">
