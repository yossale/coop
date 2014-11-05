<!-- current order -->
<div class="section">
	<div class="title">
		<h3>הוספת הזמנה ל{$user.user_first_name|escape:"html"|stripslashes} {$user.user_last_name|escape:"html"|stripslashes}</h3>
	</div>
	<div class="content">
		<div class="navigate">
			<a href="{$public_path}/duty" class="back">חזרה לרשימת ההזמנות</a>
		</div>
		{include file='common/order_form.tpl' allow_edit=true}
		<div class="navigate">
			<a href="{$public_path}/duty" class="back">חזרה לרשימת ההזמנות</a>
		</div>
	</div>
</div>