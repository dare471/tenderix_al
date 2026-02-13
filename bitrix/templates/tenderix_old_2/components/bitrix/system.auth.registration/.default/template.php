<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div class="form-login">
	<form method="post" role="form" action="<?=$arResult["AUTH_URL"]?>" name="bform" class="form-login-form">
		<?
		if (strlen($arResult["BACKURL"]) > 0)
		{
		?>
			<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?
		}
		?>
		<input type="hidden" name="AUTH_FORM" value="Y" />
		<input type="hidden" name="TYPE" value="REGISTRATION" />
		<table class="form-login-table">
			<tr>
				<td><div class="form-login-logo cursor-pointer mb-5" onclick='location.href="/"'></div></td>
			</tr>
			<tr>
				<td><h3 class="mb-4">Регистрация</h3></td>
			</tr>
			<? if(isset($arParams["~AUTH_RESULT"]) && $arParams["~AUTH_RESULT"] != ""):?>
			<tr>			
				<td>
					<? ShowMessage($arParams["~AUTH_RESULT"]);?>
				</td>
			</tr>
			<? endif;?>
			<?if($arResult["USE_EMAIL_CONFIRMATION"] === "Y" && is_array($arParams["AUTH_RESULT"]) &&  $arParams["AUTH_RESULT"]["TYPE"] === "OK"):?>
			<tr>
				<td><span class="t_reg_confirm"><?echo GetMessage("AUTH_EMAIL_SENT")?></span></td>
			</tr>
			<?elseif($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):?>
			<tr>
				<td><span class="t_reg_confirm"><?echo GetMessage("AUTH_EMAIL_WILL_BE_SENT")?></span></td>
			</tr>
			<?endif?>	
			<tr>
				<td>
					<div class="form-group ">
						<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" placeholder="<?=GetMessage("AUTH_LOGIN_MIN")?>" class="form-control input-sm" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="form-group ">
						<input type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" placeholder="<?=GetMessage("AUTH_PASSWORD_REQ")?>" class="form-control input-sm" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="form-group ">
						<input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" placeholder="<?=GetMessage("AUTH_CONFIRM")?>" class="form-control input-sm" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="form-group ">
						<input type="text" name="USER_EMAIL" maxlength="255" value="<?=$arResult["USER_EMAIL"]?>" placeholder="<?=GetMessage("AUTH_EMAIL")?>" class="form-control input-sm" />
					</div>
				</td>
			</tr>		
			<?	/* CAPTCHA */
			if ($arResult["USE_CAPTCHA"] == "Y")
			{
				?>
				<tr>
					<td>
						<div class="form-group ">
							<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
							<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
							<input type="text" name="captcha_word" maxlength="50" placeholder="<?=GetMessage("CAPTCHA_REGF_PROMT")?>" class="form-control" value="" />
						</div>
					</td>
				</tr>
				<?
			}
			/* CAPTCHA */
			?>
			<tr>
				<td class="checkbox">
					<input class="" type="checkbox" id="USER_REMEMBER" name="USER_REMEMBER" value="Y" /><label class="" for="USER_REMEMBER">
					&nbsp;Я подтверждаю согласие с условиями политики конфиденциальности</label>
					<div class="mb-3"></div>
				</td>
			</tr>
			<tr>
				<td>
					<input class="btn mb-3" type="submit" name="Register" value="<?=GetMessage("AUTH_REGISTER")?>" />
				</td>
			</tr>
			<tr>
				<td>У вас уже есть акаунт? <a class="small-link" href="<?=$arResult["AUTH_AUTH_URL"]?>" rel="nofollow">Войти</a><br /></td>
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