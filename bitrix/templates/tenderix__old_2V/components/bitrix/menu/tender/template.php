<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?

function stringToColorCode($str) {
  $code = dechex(crc32($str));
  $code = substr($code, 0, 6);
  return $code;
}
?>

<?if (!$USER->IsAuthorized()): ?>
	<?if (true):?>
		<?if (!empty($arResult)):?>
			<nav class="navbar navbar-expand-lg">
			 <a class="navbar-brand logo" href="/?#firstPage"></a>			
					<ul class="navbar-nav ml-auto pr-5 mr-5" id="menu-tender">
						<?foreach($arResult as $arItem):
							if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
							continue;
						?>
							<?if($arItem["SELECTED"]):?>
								<li data-menuanchor="<?=$arItem["PARAMS"]["data-menuanchor"]?>" class="nav-item active"><a class="nav-link" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
							<?else:?>
								<li data-menuanchor="<?=$arItem["PARAMS"]["data-menuanchor"]?>" class="nav-item"><a class="nav-link" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
							<?endif?>
						<?endforeach?>
					</ul>			
				<button class="btn btn-login my-2 my-sm-0" onclick="location.href='/auth/'">Войти</button>
			</nav>
		<?endif?>
	<?endif?>
<?else:?>
	<style>
		header { 
			background:#fff !important;
			-webkit-box-shadow: 0 1px 6px 0 rgba(32, 33, 36, 0.28);
			-moz-box-shadow: 0 1px 6px 0 rgba(32, 33, 36, 0.28);
			box-shadow: 0 1px 6px 0 rgba(32, 33, 36, 0.28);	
		}
	</style>
	
	<nav class="navbar navbar-expand-lg nav-user">
		 <a class="navbar-brand logo-red" href="/user/"></a>			
				<ul class="navbar-nav mr-5 ml-5" id="menu-tender">
					<?foreach($arResult as $arItem):
						if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
						continue;
					?>
						<?if($arItem["SELECTED"]):?>
							<li data-menuanchor="<?=$arItem["PARAMS"]["data-menuanchor"]?>" class="nav-item active"><a class="nav-link" href="<?=$arItem["LINK"]?>" <?=$arItem["PARAMS"]["target"]?>><?=$arItem["TEXT"]?></a></li>
						<?else:?>
							<li data-menuanchor="<?=$arItem["PARAMS"]["data-menuanchor"]?>" class="nav-item"><a class="nav-link" href="<?=$arItem["LINK"]?>" <?=$arItem["PARAMS"]["target"]?>><?=$arItem["TEXT"]?></a></li>
						<?endif?>
					<?endforeach?>
				</ul>	
				<?
				$userID = $USER->GetID(); //Ищем id тек. пользователя.
				$photoID = CUser::GetByID($userID)->Fetch()['PERSONAL_PHOTO']; //Получаем ID Фотографии по ID пользователя.
				if ($photoID) {
					$photoElementHTML  = '<img class="user-avatar cursor-pointer" width="48px" height="48px" src="'. CFile::GetPath($photoID) . '" />'; 
				} else {
					$sFirstLetter = substr($USER->GetLogin(), 0, 1);
										
					$photoElementHTML = '<div class="user-info">
											<div class="user-info-avatar" style="background-color:#' . stringToColorCode($USER->GetLogin()) . '">' . strtoupper($sFirstLetter) . '</div>
										</div>';
				}											
				$vUserString = $USER->GetFullName() . " [" . $USER->GetLogin() . "]"; 
				?>
				<div class="ml-auto mr-3" data-toggle="tooltip" data-placement="bottom" title="<?=$vUserString;?>">
					<?=$photoElementHTML;?>
				</div>
				
				
			<button class="btn btn-logout" onclick="location.href='/?logout=yes'">Выйти</button>
		</nav>

<?endif?>