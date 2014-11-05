<!-- current order -->

<script>var status_url = '{$public_path}/duty/change-status/id/%orderid%/status/%status%';</script>
<script src="{$js_path}/status.js"></script>

<div class="section">
	<div class="title">
		<h3>ההזמנה של {$order.user_first_name|escape:"html"|stripslashes} {$order.user_last_name|escape:"html"|stripslashes}</h3>
	</div>
	<div class="content list">
		{include file='common/order_form.tpl' allow_edit=true}
               
	</div>
</div>