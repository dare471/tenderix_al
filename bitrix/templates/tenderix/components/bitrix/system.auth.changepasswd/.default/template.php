<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="form-login">
	<form method="post" action="<?=$arResult["AUTH_FORM"]?>" name="bform" class="form-login-form">
		<?if (strlen($arResult["BACKURL"]) > 0): ?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<? endif ?>
		<input type="hidden" name="AUTH_FORM" value="Y">
		<input type="hidden" name="TYPE" value="CHANGE_PWD">	
		<table class="form-login-table">
			<tr>
				<td colspan="2"><div class="form-login-logo cursor-pointer mb-5" onclick='location.href="/"'></div></td>
			</tr>
			<tr>
				<td colspan="2"><h3 class="mb-4"><?=GetMessage("AUTH_CHANGE_PASSWORD")?></h3></td>
			</tr>
			<? if(isset($arParams["~AUTH_RESULT"]) && $arParams["~AUTH_RESULT"] != ""):?>
			<tr>			
				<td colspan="2">
					<? ShowMessage($arParams["~AUTH_RESULT"]);?>
				</td>
			</tr>
			<? endif;?>
	
	

			<tr>
				<td>
					<div class="form-group">
						<input class="form-control input-sm" placeholder="Логин" type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" class="bx-auth-input" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="form-group">
						<input class="form-control input-sm" placeholder="Контрольная строка" type="text" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" class="bx-auth-input" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="form-group">
						<input class="form-control input-sm" placeholder="Новый пароль" type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" class="bx-auth-input" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="form-group">
						<input class="form-control input-sm" placeholder="Подтверждение пароля" type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" class="bx-auth-input"  />
					</div>
				</td>
			</tr>
			<tr>
				<td class="checkbox">
					<label>* <?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></label>
					<div class="mb-3"></div>
				</td>
			</tr>
			<tr>
				<td><input class="btn mb-3" type="submit" name="change_pwd" value="<?=GetMessage("AUTH_CHANGE")?>" /></td>
			</tr>
			<tr> 
				<td>
					<a href="<?=$arResult["AUTH_AUTH_URL"]?>"><?=GetMessage("AUTH_AUTH")?></a>
				</td>
			</tr>
		</table>
	</form>
	<script type="text/javascript">
	document.bform.USER_LOGIN.focus();
	</script>
</div>

<div class="bg-login">
</div>
<div class="bg-login-overlay">
	<h1 class="bg-login-heading">Развивайте свой бизнес<br>вместе с нами</h1>
</div>