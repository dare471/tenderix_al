<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
?>

<div class="supplier-add">
    <? if (strlen($arResult["ERRORS"]) > 0): ?>
        <div class="errors-tender"><?= $arResult["ERRORS"] ?></div>
    <? endif; ?>
    <? if ($arResult["SEND_OK"] == "Y"): ?>
        <div class="send-ok-tender"><?= GetMessage("PW_TD_SEND_OK") ?></div>
    <? endif; ?>
    <form name="reg_form" action="<?= POST_FORM_ACTION_URI ?>" method="post" enctype="multipart/form-data">
        <h3><?= GetMessage("PW_TD_STEP_1") ?></h3>
        <table class="t_lot_table">
            <? if (in_array("LAST_NAME", $arParams["FIELDS"])): ?>
                <tr>
                    <td class="reg-field" width="40%">
                        <? if (in_array("LAST_NAME", $arParams["REG_FIELDS_REQUIRED"])): ?>
                            <span class="required">*</span>
                        <? endif; ?>
                        <?= GetMessage("PW_TD_SUPPLIER_LAST_NAME") ?>:
                    </td>
                    <td width="60%"><input type="text" name="INFO[LAST_NAME]" value="<?= $arResult["INFO"]["LAST_NAME"] ?>" /></td>
                </tr>
            <? endif; ?>
            <? if (in_array("NAME", $arParams["FIELDS"])): ?>
                <tr>
                    <td class="reg-field" width="40%">
                        <? if (in_array("NAME", $arParams["REG_FIELDS_REQUIRED"])): ?>
                            <span class="required">*</span>
                        <? endif; ?>
                        <?= GetMessage("PW_TD_SUPPLIER_NAME") ?>:
                    </td>
                    <td width="60%"><input type="text" name="INFO[NAME]" value="<?= $arResult["INFO"]["NAME"] ?>" /></td>
                </tr>
            <? endif; ?>
            <? if (in_array("SECOND_NAME", $arParams["FIELDS"])): ?>
                <tr>
                    <td class="reg-field" width="40%">
                        <? if (in_array("SECOND_NAME", $arParams["REG_FIELDS_REQUIRED"])): ?>
                            <span class="required">*</span>
                        <? endif; ?>
                        <?= GetMessage("PW_TD_SUPPLIER_SECOND_NAME") ?>:
                    </td>
                    <td width="60%"><input type="text" name="INFO[SECOND_NAME]" value="<?= $arResult["INFO"]["SECOND_NAME"] ?>" /></td>
                </tr>
            <? endif; ?>
            <tr>
                <td class="reg-field" width="40%"><span class="required">*</span><?= GetMessage("PW_TD_SUPPLIER_LOGIN") ?>:</td>
                <td width="60%"><?= $arResult["INFO"]["LOGIN"] ?></td>
            </tr>
            <tr>
                <td class="reg-field" width="40%"><span class="required">*</span><?= GetMessage("PW_TD_SUPPLIER_EMAIL") ?>:</td>
                <td width="60%"><input type="text" name="INFO[EMAIL]" value="<?= $arResult["INFO"]["EMAIL"] ?>" /></td>
            </tr>
            <tr>
                <td class="reg-field" width="40%"><span class="required">*</span><?= GetMessage('PW_TD_SUPPLIER_PASSWORD') ?>:</td>
                <td width="60%"><input type="password" name="INFO[PASSWORD]" value="" autocomplete="off" /></td>
            </tr>
            <tr>
                <td class="reg-field" width="40%"><span class="required">*</span><?= GetMessage('PW_TD_SUPPLIER_PASSWORD_CONFIRM') ?>:</td>
                <td width="60%"><input type="password" name="INFO[PASSWORD_CONFIRM]" value="" autocomplete="off" /></td>
            </tr>
        </table>

        <h3><?= GetMessage("PW_TD_STEP_2") ?></h3>
        <table class="t_lot_table">
            <tr>
                <td class="reg-field" width="40%">
                    <?= GetMessage("PW_TD_SUPPLIER_TYPE") ?>:
                </td>
                <td width="60%">
                    <select onchange="if(this[this.selectedIndex].value!='') window.location=this[this.selectedIndex].value;">
                        <option<?= $arResult["TYPE"] == 0 ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE=0", array("TYPE")) ?>"><?= GetMessage("PW_TD_SUPPLIER_TYPE_VAL1") ?></option>
                        <option<?= $arResult["TYPE"] == 1 ? " selected" : ""; ?> value="<?= $APPLICATION->GetCurPageParam("TYPE=1", array("TYPE")) ?>"><?= GetMessage("PW_TD_SUPPLIER_TYPE_VAL2") ?></option>
                    </select>
                </td>
            </tr>
            <? if (in_array("NAME_COMPANY", $arParams["FIELDS"])): ?>
                <tr>
                    <td class="reg-field" width="40%">
                        <? if (in_array("NAME_COMPANY", $arParams["REG_FIELDS_REQUIRED"])): ?>
                            <span class="required">*</span>
                        <? endif; ?>
                        <?= GetMessage("PW_TD_SUPPLIER_NAME_COMPANY") ?>:
                    </td>
                    <td width="60%">
                        <input type="text" name="INFO[NAME_COMPANY]" value="<?= htmlspecialcharsEx($arResult["INFO"]["NAME_COMPANY"]) ?>" size="30" />
                    </td>
                </tr>
            <? endif; ?>
            <? if (in_array("NAME_DIRECTOR", $arParams["FIELDS"])): ?>
                <tr>
                    <td class="reg-field" width="40%">
                        <? if (in_array("NAME_DIRECTOR", $arParams["REG_FIELDS_REQUIRED"])): ?>
                            <span class="required">*</span>
                        <? endif; ?>
                        <?= GetMessage("PW_TD_SUPPLIER_NAME_DIRECTOR") ?>:
                    </td>
                    <td width="60%">
                        <input type="text" name="INFO[NAME_DIRECTOR]" value="<?= htmlspecialcharsEx($arResult["INFO"]["NAME_DIRECTOR"]) ?>" size="30" />
                    </td>
                </tr>
            <? endif; ?>
            <? if (in_array("NAME_ACCOUNTANT", $arParams["FIELDS"])): ?>
                <tr>
                    <td class="reg-field" width="40%">
                        <? if (in_array("NAME_ACCOUNTANT", $arParams["REG_FIELDS_REQUIRED"])): ?>
                            <span class="required">*</span>
                        <? endif; ?>
                        <?= GetMessage("PW_TD_SUPPLIER_NAME_ACCOUNTANT") ?>:
                    </td>
                    <td width="60%">
                        <input type="text" name="INFO[NAME_ACCOUNTANT]" value="<?= htmlspecialcharsEx($arResult["INFO"]["NAME_ACCOUNTANT"]) ?>" size="30" />
                    </td>
                </tr>
            <? endif; ?>

            <? if ($arParams["DOP_FIELDS_CODE_ACTIVE"] == "Y"): ?>    
                <tr class="heading">
                    <td colspan="2"><b><? echo GetMessage("PW_TD_GROUP_SUPPLIER_CODE") ?></b></td>
                </tr>

                <? if (in_array("CODE_INN", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("CODE_INN", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_CODE_INN") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[CODE_INN]" value="<?= htmlspecialcharsEx($arResult["INFO"]["CODE_INN"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("CODE_KPP", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("CODE_KPP", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_CODE_KPP") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[CODE_KPP]" value="<?= htmlspecialcharsEx($arResult["INFO"]["CODE_KPP"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("CODE_OKVED", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("CODE_OKVED", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_CODE_OKVED") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[CODE_OKVED]" value="<?= htmlspecialcharsEx($arResult["INFO"]["CODE_OKVED"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("CODE_OKPO", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("CODE_OKPO", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_CODE_OKPO") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[CODE_OKPO]" value="<?= htmlspecialcharsEx($arResult["INFO"]["CODE_OKPO"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
            <? endif; ?>

            <? if ($arParams["DOP_FIELDS_LEGALADDRESS_ACTIVE"] == "Y"): ?>   
                <tr class="heading">
                    <td colspan="2"><b><? echo GetMessage("PW_TD_GROUP_SUPPLIER_LEGALADDRESS") ?></b></td>
                </tr>
                <? if (in_array("LEGALADDRESS_REGION", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("LEGALADDRESS_REGION", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_REGION") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[LEGALADDRESS_REGION]" value="<?= htmlspecialcharsEx($arResult["INFO"]["LEGALADDRESS_REGION"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("LEGALADDRESS_CITY", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("LEGALADDRESS_CITY", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_CITY") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[LEGALADDRESS_CITY]" value="<?= htmlspecialcharsEx($arResult["INFO"]["LEGALADDRESS_CITY"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("LEGALADDRESS_INDEX", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("LEGALADDRESS_INDEX", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_INDEX") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[LEGALADDRESS_INDEX]" value="<?= htmlspecialcharsEx($arResult["INFO"]["LEGALADDRESS_INDEX"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("LEGALADDRESS_STREET", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("LEGALADDRESS_STREET", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_STREET") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[LEGALADDRESS_STREET]" value="<?= htmlspecialcharsEx($arResult["INFO"]["LEGALADDRESS_STREET"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("LEGALADDRESS_POST", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("LEGALADDRESS_POST", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_LEGALADDRESS_POST") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[LEGALADDRESS_POST]" value="<?= htmlspecialcharsEx($arResult["INFO"]["LEGALADDRESS_POST"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
            <? endif; ?>

            <? if ($arParams["DOP_FIELDS_POSTALADDRESS_ACTIVE"] == "Y"): ?>  
                <tr class="heading">
                    <td colspan="2"><b><? echo GetMessage("PW_TD_GROUP_SUPPLIER_POSTALADDRESS") ?></b></td>
                </tr>
                <? if (in_array("POSTALADDRESS_REGION", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("POSTALADDRESS_REGION", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_REGION") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[POSTALADDRESS_REGION]" value="<?= htmlspecialcharsEx($arResult["INFO"]["POSTALADDRESS_REGION"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("POSTALADDRESS_CITY", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("POSTALADDRESS_CITY", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_CITY") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[POSTALADDRESS_CITY]" value="<?= htmlspecialcharsEx($arResult["INFO"]["POSTALADDRESS_CITY"]) ?>" size="30" />
                        </td>
                    </tr> 
                <? endif; ?>
                <? if (in_array("POSTALADDRESS_INDEX", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("POSTALADDRESS_INDEX", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_INDEX") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[POSTALADDRESS_INDEX]" value="<?= htmlspecialcharsEx($arResult["INFO"]["POSTALADDRESS_INDEX"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("POSTALADDRESS_STREET", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("POSTALADDRESS_STREET", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_STREET") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[POSTALADDRESS_STREET]" value="<?= htmlspecialcharsEx($arResult["INFO"]["POSTALADDRESS_STREET"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("POSTALADDRESS_POST", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("POSTALADDRESS_POST", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_POSTALADDRESS_POST") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[POSTALADDRESS_POST]" value="<?= htmlspecialcharsEx($arResult["INFO"]["POSTALADDRESS_POST"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("PHONE", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("PHONE", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_PHONE") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[PHONE]" value="<?= htmlspecialcharsEx($arResult["INFO"]["PHONE"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("FAX", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("FAX", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_FAX") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[FAX]" value="<?= htmlspecialcharsEx($arResult["INFO"]["FAX"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
            <? endif; ?>

            <? if ($arParams["DOP_FIELDS_STATEREG_ACTIVE"] == "Y"): ?>
                <tr class="heading">
                    <td colspan="2"><b><? echo GetMessage("PW_TD_GROUP_SUPPLIER_STATEREG") ?></b></td>
                </tr>
                <? if (in_array("STATEREG_PLACE", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("STATEREG_PLACE", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_STATEREG_PLACE") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[STATEREG_PLACE]" value="<?= htmlspecialcharsEx($arResult["INFO"]["STATEREG_PLACE"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("STATEREG_DATE", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("STATEREG_DATE", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_STATEREG_DATE") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[STATEREG_DATE]" value="<?= $arResult["INFO"]["STATEREG_DATE"] ?>" class="valid" readonly size="20" />
                            <?
                            $APPLICATION->IncludeComponent(
                                    'bitrix:main.calendar', '', array(
                                'SHOW_INPUT' => 'N',
                                'FORM_NAME' => 'reg_form',
                                'INPUT_NAME' => 'INFO[STATEREG_DATE]',
                                'INPUT_VALUE' => $arResult["INFO"]["STATEREG_DATE"],
                                'SHOW_TIME' => 'N',
                                'HIDE_TIMEBAR' => 'Y'
                                    ), null, array('HIDE_ICONS' => 'Y')
                            );
                            ?>
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("STATEREG_OGRN", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("STATEREG_OGRN", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_STATEREG_OGRN") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[STATEREG_OGRN]" value="<?= htmlspecialcharsEx($arResult["INFO"]["STATEREG_OGRN"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
            <? endif; ?>

            <? if ($arParams["DOP_FIELDS_BANK_ACTIVE"] == "Y"): ?>
                <tr class="heading">
                    <td colspan="2"><b><? echo GetMessage("PW_TD_GROUP_SUPPLIER_BANK") ?></b></td>
                </tr>
                <? if (in_array("BANKING_NAME", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("v", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_BANKING_NAME") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[BANKING_NAME]" value="<?= htmlspecialcharsEx($arResult["INFO"]["BANKING_NAME"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("BANKING_ACCOUNT", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("BANKING_ACCOUNT", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNT") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[BANKING_ACCOUNT]" value="<?= htmlspecialcharsEx($arResult["INFO"]["BANKING_ACCOUNT"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("BANKING_ACCOUNTCORR", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("BANKING_ACCOUNTCORR", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_BANKING_ACCOUNTCORR") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[BANKING_ACCOUNTCORR]" value="<?= htmlspecialcharsEx($arResult["INFO"]["BANKING_ACCOUNTCORR"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
                <? if (in_array("BANKING_BIK", $arParams["FIELDS"])): ?>
                    <tr>
                        <td class="reg-field" width="40%">
                            <? if (in_array("BANKING_BIK", $arParams["REG_FIELDS_REQUIRED"])): ?>
                                <span class="required">*</span>
                            <? endif; ?>
                            <?= GetMessage("PW_TD_SUPPLIER_BANKING_BIK") ?>:
                        </td>
                        <td width="60%">
                            <input type="text" name="INFO[BANKING_BIK]" value="<?= htmlspecialcharsEx($arResult["INFO"]["BANKING_BIK"]) ?>" size="30" />
                        </td>
                    </tr>
                <? endif; ?>
            <? endif; ?>

            <? if ($arParams["DOP_FIELDS_DIRECTION_ACTIVE"] == "Y"): ?>
                <tr class="heading">
                    <td colspan="2"><b><? echo GetMessage("PW_TD_DIRECTION_SUPPLIER") ?></b></td>
                </tr>
                <tr>
                    <td class="reg-field" width="40%">&nbsp;</td>
                    <td width="60%" class="input-checked">
                        <?
                        foreach ($arResult["SECTION"] as $arSectionID => $arSectionTITLE):
                            $checked = in_array($arSectionID, $arResult["INFO"]["DIRECTION"]) ? " checked" : "";
                            ?>
                            <input<?= $checked ?> type="checkbox" value="<?= $arSectionID ?>" name="INFO[DIRECTION][]" /> <?= $arSectionTITLE ?> <br />
                        <? endforeach; ?>
                    </td>
                </tr>
            <? endif; ?>

            <? //property  ?>
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
            <? $ID = $arResult["INFO"]["USER_ID"] ?>
            <? foreach ($arResult["PROP"] as $k => $arPropList): ?>
                <tr> 
                    <td>
                        <? if ($arPropList["IS_REQUIRED"] == "Y"): ?>
                            <span class="required">*</span>
                        <? endif; ?>
                        <b><?= $arPropList["TITLE"] ?></b>
                    </td>
                    <td>
                        <? if (count($arPropList["FILE"]) > 0): ?>
                            <table border="0" cellpadding="0" cellspacing="0" class="t_lot_table">
                                <tr>
                                    <th><? echo GetMessage("PW_TD_FILE_NAME") ?></th>
                                    <th><? echo GetMessage("PW_TD_FILE_SIZE") ?></th>
                                    <th><? echo GetMessage("PW_TD_FILE_DELETE") ?></th>
                                </tr>
                                <? foreach ($arPropList["FILE"] as $arFile) : ?>
                                    <tr>
                                        <td><a href="/tx_files/supplier_file.php?USER_ID=<? echo $ID ?>&amp;FILE_ID=<? echo $arFile["ID"] ?>&amp;PROPERTY=<? echo $arPropList["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a></td>
                                        <td align="right"><? echo round($arFile["FILE_SIZE"] / 1024, 2) ?></td>
                                        <td align="center">
                                            <input type="checkbox" name="FILE_ID_PROP[<? echo $arFile["ID"] ?>]" value="<? echo $arFile["ID"] ?>">
                                            <input type="hidden" name="PROP[<?= $arPropList["ID"] ?>][<?= $arFile["ID"] ?>]" />
                                        </td>
                                    </tr>
                                <? endforeach; ?>
                            </table>
                        <? endif; ?>
                        <?
                        $result = "";
                        if (strlen($arPropList["DEFAULT_VALUE"]) > 0 && $arPropList["MULTI"] == "Y") {
                            $arPropList["MULTI_CNT"]++;
                        }
                        $cntProp = 0;
                        if ($ID > 0 && $arPropList["PROPERTY_TYPE"] != "L" && $arPropList["PROPERTY_TYPE"] != "F") {
                            $cntProp = count($arResult["PROP_SUPPLIER"][$arPropList["ID"]]);
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

                        $result .= '<div class="prop-elem" id="prop-' . $arPropList["ID"] . '">';
                        for ($i = 0; $i < $arPropList["MULTI_CNT"]; $i++) {
                            $result .= '<div id="prop-' . $arPropList["ID"] . '-' . $i . '">';
                            switch ($arPropList["PROPERTY_TYPE"]) {
                                case "S":
                                case "N":
                                    if ($i > 0 || $ID > 0) {
                                        $arPropList["DEFAULT_VALUE"] = "";
                                    }
                                    if ($ID > 0 && $i < $cntProp) {
                                        $propName = "PROP[" . $arPropList["ID"] . "][" . $arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"] . "]";
                                        $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["VALUE"]);
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
                                    if (count($arPropList["FILE"]) <= 0 || $arPropList["MULTI"] == "Y")
                                        $result .= '<input type="file" name="PROP[' . $arPropList["ID"] . '][n' . ($i - $cntProp) . ']" size="' . $arPropList["COL_COUNT"] . '" />';
                                    break;
                                case "L":
                                    $arrList = unserialize(base64_decode($arPropList["DEFAULT_VALUE"]));
                                    if ($ID > 0) {
                                        foreach ($arResult["PROP_SUPPLIER"][$arPropList["ID"]] as $arrListSupplier) {
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
                                    if ($i > 0 || $ID > 0) {
                                        $arPropList["DEFAULT_VALUE"] = "";
                                    }
                                    if ($ID > 0 && $i < $cntProp) {
                                        $propName = "PROP[" . $arPropList["ID"] . "][" . $arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"] . "]";
                                        $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"]]) : htmlspecialcharsEx($arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["VALUE"]);
                                    } else {
                                        $propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
                                        $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : htmlspecialcharsEx($arPropList["DEFAULT_VALUE"]);
                                    }
                                    $result .= '<textarea name="' . $propName . '" cols="' . $arPropList["COL_COUNT"] . '" rows="' . $arPropList["ROW_COUNT"] . '">' . $propValue . '</textarea>';
                                    break;
                                case "D":
                                    if ($i > 0 || $ID > 0) {
                                        $arPropList["DEFAULT_VALUE"] = "";
                                    }
                                    if ($ID > 0 && $i < $cntProp) {
                                        $propName = "PROP[" . $arPropList["ID"] . "][" . $arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"] . "]";
                                        $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]][$arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"]]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]][$arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["ID"]]) : (strlen($arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["VALUE"]) > 0 ? ConvertTimeStamp(strtotime($arResult["PROP_SUPPLIER"][$arPropList["ID"]][$i]["VALUE"]), "FULL") : "");
                                    } else {
                                        $propName = "PROP[" . $arPropList["ID"] . "][n" . ($i - $cntProp) . "]";
                                        $propValue = isset($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) ? htmlspecialcharsEx($_REQUEST["PROP"][$arPropList["ID"]]["n" . ($i - $cntProp)]) : (strlen($arPropList["DEFAULT_VALUE"]) > 0 ? ConvertTimeStamp(strtotime($arPropList["DEFAULT_VALUE"]), "FULL") : "");
                                    }
                                    $result .= '<input type="text" name="' . $propName . '" value="' . $propValue . '" size="20" />';
                                    ob_start();
                                    $APPLICATION->IncludeComponent(
                                            'bitrix:main.calendar', '', array(
                                        'SHOW_INPUT' => 'N',
                                        'FORM_NAME' => 'reg_form',
                                        'INPUT_NAME' => $propName,
                                        'INPUT_VALUE' => (strlen($propValue) > 0 ? $propValue : date("d.m.Y H:i:s")),
                                        'SHOW_TIME' => 'N',
                                        'HIDE_TIMEBAR' => 'N'
                                            ), null, array('HIDE_ICONS' => 'Y')
                                    );
                                    $result .= ob_get_clean();
                                    break;
                            }
                            $result .= '</div>';
                        }
                        $result .= '</div>';
                        $result .= '<input type="hidden" name="PROP_ID_MULTI[' . $arPropList["ID"] . ']" id="id-prop-' . $arPropList["ID"] . '" value="' . $i . '" />';
                        if ($arPropList["MULTI"] == "Y" && $arPropList["PROPERTY_TYPE"] != "L") {
                            $result .= '<input type="button" value="' . GetMessage("PW_TD_PROP_ADD") . '" onclick="addNewElem(' . $arPropList["ID"] . ', ' . $cntProp . ');" />';
                        }
                        echo $result;
                        ?>
                    </td>
                </tr>
            <? endforeach; ?>
        </table>

        <? if ($arParams["DOP_FIELDS_SUBSCRIBE_ACTIVE"] == "Y" || $arParams["DOP_FIELDS_DOCUMENT_ACTIVE"] == "Y"): ?>
            <h3><?= GetMessage("PW_TD_STEP_3") ?></h3>
            <table class="t_lot_table">
                <? if ($arParams["DOP_FIELDS_SUBSCRIBE_ACTIVE"] == "Y"): ?>
                    <tr class="heading">
                        <td colspan="2"><b><? echo GetMessage("PW_TD_SUBSCRIBE_SUPPLIER") ?></b></td>
                    </tr>
                    <tr>
                        <td class="reg-field" width="40%">&nbsp;</td>
                        <td width="60%" class="input-checked">
                            <?
                            foreach ($arResult["SECTION"] as $arSectionID => $arSectionTITLE):
                                $checked = in_array($arSectionID, $arResult["INFO"]["SUBSCRIBE"]) ? " checked" : "";
                                ?>
                                <input<?= $checked ?> type="checkbox" value="<?= $arSectionID ?>" name="INFO[SUBSCRIBE][]" /> <?= $arSectionTITLE ?> <br />
                            <? endforeach; ?>
                        </td>
                    </tr>
                <? endif; ?>
                <? if ($arParams["DOP_FIELDS_DOCUMENT_ACTIVE"] == "Y"): ?>
                    <tr class="heading">
                        <td colspan="2"><b><? echo GetMessage("PW_TD_DOCUMENT") ?></b></td>
                    </tr>
                    <? if (count($arResult["INFO"]["FILE"]) > 0): ?>
                        <tr>
                            <td valign="top"><?= GetMessage("PW_TD_FILE_ATTACH_LIST") ?>:</td>
                            <td>
                                <table class="t_lot_table">
                                    <tr>
                                        <th><? echo GetMessage("PW_TD_FILE_NAME") ?></th>
                                        <th><? echo GetMessage("PW_TD_FILE_SIZE") ?></th>
                                        <th><? echo GetMessage("PW_TD_FILE_DELETE") ?></th>
                                    </tr>
                                    <? foreach ($arResult["INFO"]["FILE"] as $arFile) : ?>
                                        <tr>
                                            <td><a href="/tx_files/supplier_file.php?USER_ID=<? echo $ID ?>&amp;FILE_ID=<? echo $arFile["ID"] ?>"><? echo $arFile["ORIGINAL_NAME"] ?></a></td>
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
                        <td class="reg-field" width="40%">&nbsp;</td>
                        <td width="60%">
                            <? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
                            <? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
                            <? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
                            <? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
                            <? echo CFile::InputFile("NEW_FILE[]", 40, 0) ?><br />
                        </td>
                    </tr>
                <? endif; ?>
            </table>
        <? endif; ?>
        <input type="submit" name="reg_submit" value="<?= GetMessage("PW_TD_FORM_REG") ?>" />
    </form>
</div>     
<br clear="all" />
