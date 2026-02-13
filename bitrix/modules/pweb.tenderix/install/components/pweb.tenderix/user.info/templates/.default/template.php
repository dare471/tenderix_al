<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>
<div id="t_top">
    <div class="t_block_left">
        <div class="t_block_menu">
            <ul>
                <? if ($arResult["ERROR_AUTH"] == "Y"): ?>
                    <li>_<a href="<?= $arParams["PROFILE_URL"] ?>"><?= GetMessage("PW_TD_ENTER") ?></a></li>
                    <li>_<a href="<?= $arParams["PROFILE_URL"] ?>?register=yes"><?= GetMessage("PW_TD_REG") ?></a></li>
                <? else: ?>
                    <li>_<a href="<?= $arParams["LOT_LIST_URL"] ?>"><?= GetMessage("PW_TD_LOT_LIST") ?></a></li>
                    <? if ($arResult["T_RIGHT"] == "P"): ?>
                        <li>_<a href="<?= $arParams["PROFILE_SUPPLIER_URL"] ?>"><?= GetMessage("PW_TD_PROFILE_SUPPLIER") ?></a></li>
                    <? endif; ?>
                    <? if ($arResult["T_RIGHT"] >= "S"): ?>
                        <li>_<a href="<?= $arParams["LOT_ADD_URL"] ?>"><?= GetMessage("PW_TD_ADD_LOT") ?></a></li>
                        <li>_<a href="<?= $arParams["STATISTIC_URL"] ?>"><?= GetMessage("PW_TD_STATISTIC") ?></a></li>
                    <? endif; ?>
                    <li>_<a href="<?= $arParams["PROFILE_URL"] ?>"><?= GetMessage("PW_TD_PROFILE") ?></a></li>
                    <li>_<a href="?logout=yes"><?= GetMessage("PW_TD_EXIT") ?></a></li>
                <? endif; ?>
                <li>_<a href="/"><?= GetMessage("PW_TD_SITE") ?></a></li>
            </ul>
        </div>
    </div>
    <div class="t_block_right">
        <div class="t_block_user_info">
            <span class="t_user_name"><?= $arResult["USER_INFO"]["LAST_NAME"] ?> <?= $arResult["USER_INFO"]["NAME"] ?> <?= $arResult["USER_INFO"]["SECOND_NAME"] ?></span><br />
            <?
            if ($arResult["ERROR_AUTH"] == "Y") {
                echo GetMessage("PW_TD_ERROR_AUTH");
            }
            if ($arResult["ERROR_PROFILE_SUPPLIER"] == "Y") {
                echo "<a href='" . $arParams["PROFILE_SUPPLIER_URL"] . "'>" . GetMessage("PW_TD_ERROR_PROFILE_SUPPLIER") . "</a>";
            }
            /*if ($arResult["ERROR_SUPPLIER"] == "Y") {
                if ($arResult["S_RIGHT"] == "A")
                    echo GetMessage("PW_TD_ERROR_SUPPLIER_A");
                if ($arResult["S_RIGHT"] == "D")
                    echo GetMessage("PW_TD_ERROR_SUPPLIER_D");
            }*/
            ?>
            <br />
            <? if ($arResult["T_RIGHT"] == "P"): ?>
                <b><?= GetMessage("PW_TD_OUR_STATUS") ?>:</b> <?= $arResult["SUPPLIER_STATUS"]["TITLE"] ?><br />
                <b><?= GetMessage("PW_TD_CNT_PROPOSAL") ?>:</b> <?= $arResult["PROPOSAL_CNT"] ?><br />
                <b><?= GetMessage("PW_TD_CNT_WIN") ?>:</b> <?= $arResult["PROPOSAL_WIN_CNT"] ?>
            <? endif; ?>
            <? if ($arResult["T_RIGHT"] == "S" || $arResult["T_RIGHT"] == "W"): ?>
                <b><?= GetMessage("PW_TD_COMPANY") ?>:</b> <?= $arResult["BUYER_INFO"]["COMPANY"] ?><br />
                <b><?= GetMessage("PW_TD_CNT_LOT") ?>:</b> <?= $arResult["LOT_CNT"] ?><br />
            <? endif; ?>
        </div>
    </div>
</div>
<br clear="all" />