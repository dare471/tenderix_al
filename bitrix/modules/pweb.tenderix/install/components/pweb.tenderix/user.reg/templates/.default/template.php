<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>

<form method="post" name="profile" action="<?= $arResult["FORM_TARGET"] ?>?">
    <?= $arResult["BX_SESSION_CHECK"] ?>
    <input type="hidden" name="lang" value="<?= LANG ?>" />
    <input type="hidden" name="ID" value=<?= $arResult["ID"] ?> />

    <? if (strlen($arResult["ERRORS"]) > 0): ?>
        <div class="errors-tender"><?= $arResult["ERRORS"] ?></div>
    <? endif; ?>
    <table class="t_lot_table">
        <tr>
            <td><?= GetMessage('NAME') ?></td>
            <td><input type="text" name="NAME" maxlength="50" value="<?= $arResult["arUser"]["NAME"] ?>" /></td>
        </tr>
        <tr>
            <td><?= GetMessage('LAST_NAME') ?></td>
            <td><input type="text" name="LAST_NAME" maxlength="50" value="<?= $arResult["arUser"]["LAST_NAME"] ?>" /></td>
        </tr>
        <tr>
            <td><?= GetMessage('SECOND_NAME') ?></font></td>
            <td><input type="text" name="SECOND_NAME" maxlength="50" value="<?= $arResult["arUser"]["SECOND_NAME"] ?>" /></td>
        </tr>
        <tr>
            <td><?= GetMessage('EMAIL') ?><span class="starrequired">*</span></td>
            <td><input type="text" name="EMAIL" maxlength="50" value="<? echo $arResult["arUser"]["EMAIL"] ?>" /></td>
        </tr>
        <tr>
            <td><?= GetMessage('LOGIN') ?><span class="starrequired">*</span></td>
            <td><? echo $arResult["arUser"]["LOGIN"] ?></td>
        </tr>
        <tr>
            <td><?= GetMessage('NEW_PASSWORD_REQ') ?></td>
            <td><input type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" /></td>
        </tr>
        <tr>
            <td><?= GetMessage('NEW_PASSWORD_CONFIRM') ?></td>
            <td><input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" /></td>
        </tr>
    </table>
    <p><input type="submit" name="save" value="<?= (($arResult["ID"] > 0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD")) ?>">&nbsp;&nbsp;<input type="reset" value="<?= GetMessage('MAIN_RESET'); ?>"></p>
</form>