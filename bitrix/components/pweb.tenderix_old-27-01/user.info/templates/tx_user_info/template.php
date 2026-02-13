<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?if ($USER->IsAuthorized()): ?>
    <div class="media user_info">
        <div class="media-body">
            <h4 class="media-heading">
                <a href="/profile.php" style="color:#000;text-decoration:none;" title="Перейти в профиль пользователя">
                    <i class="fa fa-user"></i>&nbsp;<?= $arResult["USER_INFO"]["LAST_NAME"] ?> <?= $arResult["USER_INFO"]["NAME"] ?> <?= $arResult["USER_INFO"]["SECOND_NAME"] ?></a><a href="?logout=yes" title="Выход"><i class="fa fa-sign-out" style="float:right;"></i>
                </a>
            </h4>
            <hr style="margin:8px auto">
            <?if ($arResult["ERROR_PROFILE_SUPPLIER"] == "Y") {
                echo "<a href='" . $arParams["PROFILE_SUPPLIER_URL"] . "'>" . GetMessage("PW_TD_ERROR_PROFILE_SUPPLIER") . "</a><br />";
            }?>

            <? if ($arResult["T_RIGHT"] == "P"): ?>
                <b><?= GetMessage("PW_TD_OUR_STATUS") ?>:</b> <span class="label label-default"><?= $arResult["SUPPLIER_STATUS"]["TITLE"] ?></span><br />
                <div class="proposal_span"><b><?= GetMessage("PW_TD_CNT_PROPOSAL") ?>:</b> <span class="label <?if($arResult["PROPOSAL_CNT"] == 0) {?>label-default<?}else{?>label-info<?}?>"><?= $arResult["PROPOSAL_CNT"] ?></span> </div>
            <div class="proposal_span"><b><?= GetMessage("PW_TD_CNT_WIN") ?>:</b> <span class="label <?if($arResult["PROPOSAL_WIN_CNT"] == 0) {?>label-default<?}else{?>label-info<?}?>"><?= $arResult["PROPOSAL_WIN_CNT"] ?></span></div>
            <? endif; ?>
            <? if ($arResult["T_RIGHT"] == "S"): ?>
                <div class="proposal_span"><b><?= GetMessage("PW_TD_COMPANY") ?>:</b> <?= $arResult["BUYER_INFO"]["COMPANY"] ?></div>
                <div class="proposal_span"><b><?= GetMessage("PW_TD_CNT_LOT") ?>:</b> <?= $arResult["LOT_CNT"] ?></div>
            <? endif; ?>
            <? if ($arResult["T_RIGHT"] == "W"): ?>
                <b><?= GetMessage("PW_TD_OUR_STATUS") ?>:</b> <span class="label label-default">Организатор торгов</span><br />
                <div class="proposal_span"><b><?= GetMessage("PW_TD_COMPANY") ?>:</b> <?= $arResult["BUYER_INFO"]["COMPANY"] ?></div>
                <div class="proposal_span"><b><?= GetMessage("PW_TD_CNT_LOT") ?>:</b> <?= $arResult["LOT_CNT"] ?></div>
            <? endif; ?>
        </div>
    </div>    
<?else :?>
    <div class="alert alert-warning" role="alert" style="margin-top:11px;">
        <!-- Обязательно подключить компонент с мобальным окном авторизации-->
	   Для участия в тендерах необходимо <a href="/profile_supplier.php?register=yes">зарегистрироваться</a> или <a data-toggle="modal" data-target="#authModal" href="#">авторизоваться</a> на площадке.
    </div>
<?endif?>
