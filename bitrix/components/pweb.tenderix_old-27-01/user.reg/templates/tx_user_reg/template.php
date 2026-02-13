<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>
<div style="margin-top:20px;">
<form method="post" name="profile" action="<?= $arResult["FORM_TARGET"] ?>?">
    <?= $arResult["BX_SESSION_CHECK"] ?>
    <input type="hidden" name="lang" value="<?= LANG ?>" />
    <input type="hidden" name="ID" value=<?= $arResult["ID"] ?> />

    <? if (strlen($arResult["ERRORS"]) > 0): ?>
        <div class="errors-tender"><?= $arResult["ERRORS"] ?></div>
    <? endif; ?>
    <table class="t_lot_table table table-striped table-bordered table-hover table-condensed">
        <tr>
            <td><?= GetMessage('NAME') ?></td>
            <td>
	            <div class="form-group">
	            <input class="form-control input-sm" type="text" name="NAME" maxlength="50" value="<?= $arResult["arUser"]["NAME"] ?>" />
	            </div>
	        </td>
        </tr>
        <tr>
            <td><?= GetMessage('LAST_NAME') ?></td>
            <td>
	            <div class="form-group">
	            <input class="form-control input-sm" type="text" name="LAST_NAME" maxlength="50" value="<?= $arResult["arUser"]["LAST_NAME"] ?>" />
	            </div>
	        </td>
        </tr>
        <tr>
            <td><?= GetMessage('SECOND_NAME') ?></font></td>
            <td>
	            <div class="form-group">
	            <input class="form-control input-sm" type="text" name="SECOND_NAME" maxlength="50" value="<?= $arResult["arUser"]["SECOND_NAME"] ?>" />
	            </div>
	        </td>
        </tr>
        <tr>
            <td><?= GetMessage('MOBILE_PHONE') ?></font></td>
            <td>
                <div class="form-group">
                <input class="form-control input-sm" type="text" name="PERSONAL_PHONE" maxlength="50" value="<?= $arResult["arUser"]["PERSONAL_PHONE"] ?>" />
                </div>
            </td>
        </tr>
        <tr>
            <td><?= GetMessage('EMAIL') ?><span class="starrequired">*</span></td>
            <td>
	            <div class="form-group">
	            <input class="form-control input-sm" type="text" name="EMAIL" maxlength="50" value="<? echo $arResult["arUser"]["EMAIL"] ?>" />
	            </div>
	        </td>
        </tr>
        <tr>
            <td><?= GetMessage('LOGIN') ?><span class="starrequired">*</span></td>
            <td><? echo $arResult["arUser"]["LOGIN"] ?></td>
        </tr>
        <tr>
            <td><?= GetMessage('NEW_PASSWORD_REQ') ?></td>
            <td>
	            <div class="form-group">
	            <input class="form-control input-sm" type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" />
	            </div>
	        </td>
        </tr>
        <tr>
            <td><?= GetMessage('NEW_PASSWORD_CONFIRM') ?></td>
            <td>
	            <div class="form-group">
	            <input class="form-control input-sm" type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" />
	            </div>
	        </td>
        </tr>
    </table>
    <p>
	    <input class="btn btn-primary" type="submit" name="save" value="<?= (($arResult["ID"] > 0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD")) ?>">&nbsp;&nbsp;
	    <input class="btn btn-primary" type="reset" value="<?= GetMessage('MAIN_RESET'); ?>"></p>
</form>
</div>