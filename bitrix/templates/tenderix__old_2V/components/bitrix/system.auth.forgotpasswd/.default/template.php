<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="form-login">
	<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>" class="form-login-form">
		<?
		if (strlen($arResult["BACKURL"]) > 0)
		{
		?>
			<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		<?
		}
		?>
		<input type="hidden" name="AUTH_FORM" value="Y">
		<input type="hidden" name="TYPE" value="SEND_PWD">
		
		<table class="form-login-table">
			<tr>
				<td><div class="form-login-logo cursor-pointer mb-5" onclick='location.href="/"'></div></td>
			</tr>
			<tr>
				<td><h3 class="mb-4">Забыли пароль?</h3></td>
			</tr>
			<? if(isset($arParams["~AUTH_RESULT"]) && $arParams["~AUTH_RESULT"] != ""):?>
			<tr>			
				<td>
					<? ShowMessage($arParams["~AUTH_RESULT"]);?>
				</td>
			</tr>
			<? endif;?>
			<tr>
				<td>
					<div class="form-group">
						<input class="form-control input-sm" type="text" placeholder="Логин" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" />
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="mb-3"><?=GetMessage("AUTH_OR")?></div>
				</td>
			</tr>
			<tr> 
				<td>
					<div class="form-group">
					<input class="form-control input-sm" type="text" placeholder="E-mail"  name="USER_EMAIL" maxlength="255" />
					</div>
				</td>
			</tr>
			<tr>
				<td class="checkbox">
					<label><?=GetMessage("AUTH_FORGOT_PASSWORD_1")?></label>
					<div class="mb-3"></div>
				</td>
			</tr>
			<tr> 
				<td>
					<input class="btn mb-3" type="submit" name="send_account_info" value="<?=GetMessage("AUTH_SEND")?>" />
				</td>
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