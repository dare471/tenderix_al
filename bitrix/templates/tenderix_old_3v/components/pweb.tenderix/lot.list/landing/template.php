<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>



<? foreach($arResult["LOTS"] as $item): ?>


<div class="col-sm-4">
	<div class="lot-item-wrapper cursor-pointer">
		<div class="lot-item">
			<table class="table-prop">
				<tr>
					<td colspan="2" class="number">#<?=$item["ID"];?></td>
					<!--<td class="star"><i class="fas fa-star"></i></td>-->
				</tr>
				<tr>
					<td colspan="2"><div class="title"><a href="/auth/"><?=$item["TITLE"];?></a></div></td>
				</tr>
				
				<tr>
					<td colspan="2" class="delivery nowrap"><?=$item["TERM_DELIVERY_VAL"];?></td>
				</tr>
				<tr>
					<td class="date"><?=substr($item["DATE_END"], 0, 10);?></td>
					<td class="currency"><?=CurrencyFormat($item["TOTAL_SUM"], "KZT");?></td>
				</tr>
			</table>			
		</div>
	</div>
</div>

<? endforeach; ?>
