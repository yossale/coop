
<select class="status" order_id="{$order.order_id}" tabindex="2">
	<option id="unpayed" value="unpayed" {if $order.order_status == "unpayed"}selected{/if}>מחכה לתשלום</option>
	<option id="payed" value="payed" {if $order.order_status == "payed"}selected{/if}>שולמה</option>
</select>
<b class="saved" order_id="{$order.order_id}"></b>