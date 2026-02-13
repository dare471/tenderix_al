<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="currency-list-wrapper">
	<ul class="currency-list">	
		<?foreach ($arResult["CURRENCY"] as $key => $arCurrency):?>
			<li>
				<span class="cur_val_wrapper"><?=$arCurrency["FROM"]?>: <span class="cur_val"><?=$arCurrency["BASE"]?></span></span>
			</li>
		<?endforeach?>
	</ul>
</div>