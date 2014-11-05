
<!-- debt  -->
<div class="section">
	<div class="title">
		<h3>חובות</h3>
	</div>
	{if $debts == null}
	אין אף חוב כרגע.
	{else}
	<div class="content list">
		<table>
			<th>שם</th>
			<th>טלפון</th>
			<th>חוב</th>
			<th>תאריך</th>
			<th>הערות</th>
			{foreach from=$debts item=debt}
			<tr>
				<td>{$debt.user_first_name|escape:"html"|stripslashes} {$debt.user_last_name|escape:"html"|stripslashes}</td>
				<td>{$debt.user_phone|escape:"html"|stripslashes}</td>
				<td>₪{$debt.debt_amount}</td> 
				<td>{if $debt.debt_date != '0000-00-00'}{$debt.debt_date|date_format:"%d/%m/%y"}{/if}</td>
				<td>{$debt.debt_comments|escape:"html"|stripslashes}</td>
			</tr>
			{/foreach}
		</table>		
	</div>
	{/if}
</div>
