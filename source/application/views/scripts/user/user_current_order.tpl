<!-- current order -->
<div class="section">
	<div class="title">
		<h3>הזמנה השבועית</h3>
	</div>
	<div class="content">
		{if $allow_edit}
		<div class="navigate">
			<a href="{$public_path}/user/current" class="cancel">ביטול עריכה</a>
		</div>	
		{else}
		<div class="navigate">
			<a href="{$public_path}/user/current/edit/1" class="edit">עריכת הזמנה</a>
		</div>	
		{/if}
		{include file='common/order_form.tpl' allow_edit=$allow_edit}
		<div class="navigate">
			<a href="{$public_path}/user" class="back">חזרה אחורה</a>
		</div>
	</div>
</div>