<div class="navigate">
        <a href="{$public_path}/duty/print" class="print" target="_blank">גרסה להדפסה</a>
</div>
{if !$orders}
<a>אין הזמנות</a>
{else}
{foreach from=$orders key=group item=groupOrders}
<div class="title">
        <h3>{if $group=="unpayed"}מחכות לתשלום{else if $group=="payed"}שולמו{/if}</h3>
</div>
<table>
        <th>שם</th>
        <th>טלפון</th>
        {foreach from=$groupOrders item=order}
        <tr>
                <td><a href="{$public_path}/duty/view-order/id/{$order.order_id}">{$order.user_first_name|escape:"html"|stripslashes} {$order.user_last_name|escape:"html"|stripslashes}</a></td>
                <td>{$order.user_phone|escape:"html"|stripslashes}</td>
        </tr>
        {/foreach}
</table>
<br />
{/foreach}
{/if}
