<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if ($arResult['ERROR']) {?>
	<div class="container">
		<div class="row">
			<div class="alert alert-danger fade in" role="alert">
				<button type="button" class="close" data-dismiss="alert">
					<span aria-hidden="true">×</span>
					<span class="sr-only">Close</span>
				</button>
				<p><?=$arResult['ERROR_MESSAGE']['MESSAGE']?></p>
			</div>		
		</div>
	</div>
<?}?>
<?if($arResult["FORM_TYPE"] == "login"):?>
	<form name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
		<div class="modal fade" id="authModal" tabindex="-1" role="dialog" aria-labelledby="authModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="authModalLabel">Авторизация</h4>
					</div>
					<div class="modal-body">
						<div class="bx-system-auth-form">
							<?foreach ($arResult["POST"] as $key => $value):?>
								<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
							<?endforeach?>
							<input type="hidden" name="AUTH_FORM" value="Y" />
							<input type="hidden" name="TYPE" value="AUTH" />

							<div class="input-group input-group-lg">
								<span class="input-group-addon">
									<div class="icon_auth" style="width:20px;display:block;"><i class="fa fa-at"></i></div>
								</span>
								
								<input type="text" name="USER_LOGIN" class="form-control" placeholder="Логин" value="<?=$arResult["USER_LOGIN"]?>">
							</div>
							<br />
							<div class="input-group input-group-lg">
								<span class="input-group-addon">
									<div class="icon_auth" style="width:20px;display:block;">
										<?if($arResult["SECURE_AUTH"]):?>
											<span class="bx-auth-secure" id="bx_auth_secure<?=$arResult["RND"]?>" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
												<div class="bx-auth-secure-icon"></div>
											</span>
											<noscript>
												<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
													<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
												</span>
											</noscript>
											<script type="text/javascript">
											document.getElementById('bx_auth_secure<?=$arResult["RND"]?>').style.display = 'inline-block';
											</script>
										<?else:?>
											<i class="fa fa-lock"></i>
										<?endif;?>
									</div>
								</span>
								<input type="password" name="USER_PASSWORD" class="form-control" placeholder="Пароль">
							</div>
							<div class="input-group input-group-lg">
								<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
									<input type="checkbox" id="USER_REMEMBER_frm" name="USER_REMEMBER" value="Y" />&nbsp;
									<label for="USER_REMEMBER_frm" title="<?=GetMessage("AUTH_REMEMBER_ME")?>">
										<?echo GetMessage("AUTH_REMEMBER_SHORT")?>
									</label>
								<?endif?>
							</div>
							<div class="input-group input-group-lg">
								<?if ($arResult["CAPTCHA_CODE"]):?>
									<?//echo GetMessage("AUTH_CAPTCHA_PROMT")?>
									<input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
									<span class="input-group-addon" style="padding:0 10px;margin:0;">
										<img width="" src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" alt="CAPTCHA" />
									</span>
									<input type="text" name="captcha_word" class="form-control" placeholder="Код с картинки" />
								<?endif?>
							</div>
						</div>
					</div>
      				
					<div class="modal-footer">
						<div style="float:left;">
							<?if($arResult["NEW_USER_REGISTRATION"] == "Y"):?>
								<noindex><a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_REGISTER")?></a></noindex><br />
							<?endif?>
							<noindex><a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_FORGOT_PASSWORD_2")?></a></noindex>
						</div>
						<input class="btn btn-primary"type="submit" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>" />
					</div>
				</div>
			</div>
		</div>
	</form>
<?endif?>
