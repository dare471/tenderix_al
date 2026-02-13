<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div class="form-login">	
	<form name="form_auth" class="form-login-form" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
		<input type="hidden" name="AUTH_FORM" value="Y" />
		<input type="hidden" name="TYPE" value="AUTH" />
		<?if (strlen($arResult["BACKURL"]) > 0):?>
		<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?endif?>
		<?foreach ($arResult["POST"] as $key => $value):?>
		<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
		<?endforeach?>
		<table class="form-login-table">
			<tr>
				<td><div class="form-login-logo cursor-pointer mb-5" onclick='location.href="/"'></div></td>
			</tr>
			<tr>
				<td><h3 class="mb-4">Войти</h3></td>
			</tr>
			<? if(isset($arParams["~AUTH_RESULT"]) && $arParams["~AUTH_RESULT"] != ""):?>
			<tr>			
				<td>
					<div class="alert alert-danger"><? ShowMessage($arParams["~AUTH_RESULT"]);?>
					</div>
				</td>
			</tr>
			<? endif;?>
			<tr>
				<td>
					<div class="form-group">
					<input class="bx-auth-input form-control input-sm" type="text" name="USER_LOGIN" placeholder="Логин" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="form-group">
						<input class="bx-auth-input form-control input-sm" type="password" placeholder="Пароль" name="USER_PASSWORD" maxlength="255" />
					</div>
				</td>
			</tr>	
			<?if($arResult["CAPTCHA_CODE"]):?>
				<tr>
					<td><input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
					<img src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" /></td>
				</tr>
				<tr>
					<td><input class="bx-auth-input" type="text" name="captcha_word" maxlength="50" value="" size="15" /></td>
				</tr>
			<?endif;?>
			<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
			<tr>
				<td class="checkbox">
					<div class="float-left">
						<input class="" type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" /><label for="USER_REMEMBER">&nbsp;Запомнить меня</label>
					</div>
					<div class="float-right">
						<a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow">Забыли пароль?</a>
					</div>
					<div style="clear:both"></div>
					<div class="mb-3"></div>
				</td>
			</tr>
			<?endif?>
			<tr>
				<td class="authorize-submit-cell"><input  class="btn mb-3" type="submit" name="Login" value="<?=GetMessage("AUTH_AUTHORIZE")?>" /></td>
			</tr>
			<tr>
				<td><a class="small-link" href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow">Регистрация</a><br /></td>
			</tr>
		</table>
	</form>
	<script type="text/javascript">
	<?if (strlen($arResult["LAST_LOGIN"])>0):?>
	try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
	<?else:?>
	try{document.form_auth.USER_LOGIN.focus();}catch(e){}
	<?endif?>
	</script>
</div>
<div class="bg-login">
</div>
<div class="bg-login-overlay">
	<h1 class="bg-login-heading">Развивайте свой бизнес<br>вместе с нами</h1>
</div>

