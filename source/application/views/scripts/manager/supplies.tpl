{literal}
<script>
$(document).ready(function() {
	$(".editing").hide();
	
	$(".editBtn").click(function() {
		var tr = $(this).parent().parent();
		tr.find(".editing").each(function() { $(this).show() });
		tr.find(".viewing").each(function() { $(this).hide() });
	});  
});
</script>
{/literal}
<!-- supplies -->
<div class="section">
	<div class="title">
		<h3>המלאי של {$product.product_name|escape:"html"|stripslashes}</h3>
	</div>
	<div class="navigate">
		<form method="post">
			<input type="hidden" name="oper" value="add" />
			<input type="hidden" name="product_id" value="{$product.product_id}" />
			<b>הוספת מלאי: </b>
			<label>תאריך בפורמט dd/mm/yy: </label><input name="supply_date" value="{$date}" class="date" name="date" size="7" dir="ltr" />
			&nbsp;&nbsp;
			<label>כמות: </label><input name="supply_amount" size="3" dir="ltr" />
			&nbsp;&nbsp;
			<input type="submit" value="אישור " />
		</form>
	</div>
	<div class="content">
		{if $supplies == null}
		<p>אין אף מלאי.</p>
		{else}
		<div class="list">
		<table>
			<th>תאריך </th>
			<th>כמות</th>
			<th>אפשרויות</th>
			{foreach from=$supplies item=supply}
			<tr style="height: 1px;">

				<form method="post">
				<input type="hidden" name="oper" value="edit" />
				<input type="hidden" name="product_id" value="{$item.product_id}" />

				<td>
					<a class="viewing">{$supply.supply_date|date_format:"%d/%m/%Y"}</a>
					<input name="supply_date" value="{$supply.supply_date|date_format:"%d/%m/%Y"}" class="date editing" name="date" size="7" dir="ltr" />
				</td>
				<td>
					<a class="viewing">{$supply.supply_amount}</a>
					<input name="supply_amount" value="{$supply.supply_amount}" class="editing" size="3" dir="ltr" />
				</td>
				<td>
					<input type="submit" class="editing" value="שמור" />
					<a href="javascript:void(0);" class="editBtn viewing"><img src="{$img_path}/edit.png" /></a>
					<a href="javascript:void(0);" confirm_msg="למחוק את המלאי?" link="{$public_path}/manager/delete-supply/id/{$supply.supply_id}" class="delete">&nbsp;</a>
				</td>
				
				</form>
			</tr>
			{/foreach}
		</table>		
		</div>
		{/if}
	</div>
</div>

