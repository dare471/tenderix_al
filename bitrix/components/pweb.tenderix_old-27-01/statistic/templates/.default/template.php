<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>

<table class="t_lot_table">
    <tbody>
        <tr>
            <th width="50%"><?=GetMessage("PW_TD_CATEGORY")?></th>
            <th width="5%"><?=GetMessage("PW_TD_ALL_LOTS")?></th>
            <th width="5%"><?=GetMessage("PW_TD_ACTIVE_LOTS")?></th>
            <th width="5%"><?=GetMessage("PW_TD_LIFTED_LOTS")?></th>
            <th width="5%"><?=GetMessage("PW_TD_NOT_WIN")?></th>
            <th width="10%"><?=GetMessage("PW_TD_MAX")?>, <?= $arResult["UNIT_NAME"] ?></th>
            <th width="10%"><?=GetMessage("PW_TD_MIN")?>, <?= $arResult["UNIT_NAME"] ?></th>
            <th width="10%"><?=GetMessage("PW_TD_DEP")?>, <?= $arResult["UNIT_NAME"] ?></th>
        </tr>
        <? foreach ($arResult["STATISTIC"]["L1"] as $L1_ID => $L1_RES): ?>
            <tr class="lvl_1" onclick="viewL2(<?= $L1_ID ?>)">
                <td><b><?= $arResult["NAME"]["L1"][$L1_ID] ?></b></td>    
                <td><?= $L1_RES["LOTS_ALL"] ?></td>    
                <td><?= $L1_RES["LOTS_ACTIVE"] ?></td>    
                <td><?= $L1_RES["LOTS_LIFTED"] ?><br /><span class="percent-effective">(<?= $L1_RES["LOTS_LIFTED_E"] ?>%)</span></td>    
                <td><?= $L1_RES["LOTS_NOT_WIN"] ?><br /><span class="percent-effective">(<?= $L1_RES["LOTS_NOT_WIN_E"] ?>%)</span></td>    
                <td class="price"><?= $L1_RES["MAX"] ?></td>
                <td class="price"><?= $L1_RES["MIN"] ?><br /><span class="percent-effective">(<?= $L1_RES["MIN_E"] ?>%)</span></td>     
                <td class="price"><?= $L1_RES["DEP"] ?><br /><span class="percent-effective">(<?= $L1_RES["DEP_E"] ?>%)</span></td>    
            </tr>
            <? foreach ($arResult["STATISTIC"]["L2"][$L1_ID] as $L2_ID => $L2_RES): ?>
                <tr class="lvl_2 lvltwo_<?= $L1_ID ?>"  onclick="viewL3(<?= $L1_ID ?>,<?= $L2_ID ?>)">
                    <td style="padding-left: 30px;"><?= $arResult["NAME"]["L2"][$L2_ID] ?></td>    
                    <td><?= $L2_RES["LOTS_ALL"] ?></td>    
                    <td><?= $L2_RES["LOTS_ACTIVE"] ?></td>    
                    <td><?= $L2_RES["LOTS_LIFTED"] ?><br /><span class="percent-effective">(<?= $L2_RES["LOTS_LIFTED_E"] ?>%)</span></td>    
                    <td><?= $L2_RES["LOTS_NOT_WIN"] ?><br /><span class="percent-effective">(<?= $L2_RES["LOTS_NOT_WIN_E"] ?>%)</span></td>    
                    <td class="price"><?= $L2_RES["MAX"] ?></td>
                    <td class="price"><?= $L2_RES["MIN"] ?><br /><span class="percent-effective">(<?= $L2_RES["MIN_E"] ?>%)</span></td>     
                    <td class="price"><?= $L2_RES["DEP"] ?><br /><span class="percent-effective">(<?= $L2_RES["DEP_E"] ?>%)</span></td>   
                </tr>
                <? foreach ($arResult["STATISTIC"]["L3"][$L1_ID][$L2_ID] as $L3_ID => $L3_RES): ?>
                    <tr class="lvl_3 lvlthree_<?= $L1_ID ?> lvlthree_<?= $L1_ID ?><?= $L2_ID ?>" onclick="viewL4(<?= $L1_ID ?>,<?= $L2_ID ?>,<?= $L3_ID ?>)">
                        <td style="padding-left: 60px;"><?= $arResult["NAME"]["L3"][$L3_ID] ?></td>    
                        <td><?= $L3_RES["LOTS_ALL"] ?></td>    
                        <td><?= $L3_RES["LOTS_ACTIVE"] ?></td>    
                        <td><?= $L3_RES["LOTS_LIFTED"] ?><br /><span class="percent-effective">(<?= $L3_RES["LOTS_LIFTED_E"] ?>%)</span></td>    
                        <td><?= $L3_RES["LOTS_NOT_WIN"] ?><br /><span class="percent-effective">(<?= $L3_RES["LOTS_NOT_WIN_E"] ?>%)</span></td>    
                        <td class="price"><?= $L3_RES["MAX"] ?></td>
                        <td class="price"><?= $L3_RES["MIN"] ?><br /><span class="percent-effective">(<?= $L3_RES["MIN_E"] ?>%)</span></td>     
                        <td class="price"><?= $L3_RES["DEP"] ?><br /><span class="percent-effective">(<?= $L3_RES["DEP_E"] ?>%)</span></td>   
                    </tr>
                    <? foreach ($arResult["STATISTIC"]["L4"][$L1_ID][$L2_ID][$L3_ID] as $L4_ID => $L4_RES): ?>
                        <tr class="lvl_4 lvlfour_<?= $L1_ID ?> lvlfour_<?= $L1_ID ?><?= $L2_ID ?> lvlfour_<?= $L1_ID ?><?= $L2_ID ?><?= $L3_ID ?>">
                            <td style="padding-left: 90px;"><?= $arResult["NAME"]["L4"][$L4_ID] ?></td>    
                            <td><?= $L4_RES["LOTS_ALL"] ?></td>    
                            <td><?= $L4_RES["LOTS_ACTIVE"] ?></td>    
                            <td><?= $L4_RES["LOTS_LIFTED"] ?><br /><span class="percent-effective">(<?= $L4_RES["LOTS_LIFTED_E"] ?>%)</span></td>    
                            <td><?= $L4_RES["LOTS_NOT_WIN"] ?><br /><span class="percent-effective">(<?= $L4_RES["LOTS_NOT_WIN_E"] ?>%)</span></td>    
                            <td class="price"><?= $L4_RES["MAX"] ?></td> 
                            <td class="price"><?= $L4_RES["MIN"] ?><br /><span class="percent-effective">(<?= $L4_RES["MIN_E"] ?>%)</span></td> 
                            <td class="price"><?= $L4_RES["DEP"] ?><br /><span class="percent-effective">(<?= $L4_RES["DEP_E"] ?>%)</span></td>   
                        </tr>
                    <? endforeach; ?>
                <? endforeach; ?>
            <? endforeach; ?>
        <? endforeach; ?>
        <tr>
            <td><?=GetMessage("PW_TD_ITOGO")?>:</td>
            <td><?= $arResult["STATISTIC"]["ITOG"]["LOTS_ALL"] ?></td>    
            <td><?= $arResult["STATISTIC"]["ITOG"]["LOTS_ACTIVE"] ?></td>    
            <td><?= $arResult["STATISTIC"]["ITOG"]["LOTS_LIFTED"] ?><br /><span class="percent-effective">(<?= $arResult["STATISTIC"]["ITOG"]["LOTS_LIFTED_E"] ?>%)</span></td>    
            <td><?= $arResult["STATISTIC"]["ITOG"]["LOTS_NOT_WIN"] ?><br /><span class="percent-effective">(<?= $arResult["STATISTIC"]["ITOG"]["LOTS_NOT_WIN_E"] ?>%)</span></td>    
            <td class="price"><?= $arResult["STATISTIC"]["ITOG"]["MAX"] ?></td> 
            <td class="price"><?= $arResult["STATISTIC"]["ITOG"]["MIN"] ?><br /><span class="percent-effective">(<?= $arResult["STATISTIC"]["ITOG"]["MIN_E"] ?>%)</span></td>  
            <td class="price"><?= $arResult["STATISTIC"]["ITOG"]["DEP"] ?><br /><span class="percent-effective">(<?= $arResult["STATISTIC"]["ITOG"]["DEP_E"] ?>%)</span></td>   
        </tr>
    </tbody>
</table>


<? if (intval($arParams["LEVEL_COL"]) >= 3): ?>
    <style type="text/css"> 
        .lvl_2 {cursor: pointer;}
    </style>
<? endif ?>
<? if (intval($arParams["LEVEL_COL"]) == 4): ?>
    <style type="text/css"> 
        .lvl_3 {cursor: pointer;}
    </style>
<? endif ?>


<script type="text/javascript">
    $(function() {
        $(".lvl_2").hide();
        $(".lvl_3").hide();
        $(".lvl_4").hide();
    });
    
    function viewL2(L1ID) {
        var lvl2 = $(".lvltwo_"+L1ID);
        var lvl3 = $(".lvlthree_"+L1ID);
        var lvl4 = $(".lvlfour_"+L1ID);
            
        if(lvl2.hasClass('open')) {
            lvl2.removeClass('open');
            lvl2.addClass('close');
            lvl2.hide();
            lvl3.removeClass('open');
            lvl3.addClass('close');
            lvl3.hide();                  
            lvl4.removeClass('open');
            lvl4.addClass('close');
            lvl4.hide();                  
        } else {
            lvl2.removeClass('close');
            lvl2.addClass('open');
            lvl2.show();
        }
    }
    
    function viewL3(L1ID,L2ID) {
        var lvl3 = $(".lvlthree_"+L1ID+L2ID);
        var lvl4 = $(".lvlfour_"+L1ID+L2ID);
            
        if(lvl3.hasClass('open')) {
            lvl3.removeClass('open');
            lvl3.addClass('close');
            lvl3.hide();                  
            lvl4.removeClass('open');
            lvl4.addClass('close');
            lvl4.hide();                  
        } else {
            lvl3.removeClass('close');
            lvl3.addClass('open');
            lvl3.show();
        }
    }
    
    function viewL4(L1ID,L2ID,L3ID) {
        var lvl4 = $(".lvlfour_"+L1ID+L2ID+L3ID);
            
        if(lvl4.hasClass('open')) {                
            lvl4.removeClass('open');
            lvl4.addClass('close');
            lvl4.hide();                  
        } else {
            lvl4.removeClass('close');
            lvl4.addClass('open');
            lvl4.show();
        }
    }
</script>