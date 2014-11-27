<script src="{$js_path}/jquery/jquery_latest.js"></script>
<script>
{literal}
$(document).ready(function() {
	var opens = new Array();	
	
	$(".report_content").each(function() {
		$(this).hide();
	});
	
	$(".view_report").click(function() {
		var report_id = $(this).attr("report_id");
		if (opens[report_id] == undefined || opens[report_id] == false)
		{
			$(".report_content[report_id='" + report_id + "']").show();
			opens[report_id] = true;
		}
		else
		{
			$(".report_content[report_id='" + report_id + "']").hide();
			opens[report_id] = false;			
		}
		
	});
});
{/literal}
</script>

<!-- duty reports -->
<div class="section">
	<div class="title">
		<h3>דוחות קודמים</h3>
	</div>
	לחץ על הדו"ח כדי לראות את תוכנו. לחץ עליו בשנית על מנת להסתירו.<br /><br />
	{if $reports == false}
	אין אף דו"ח.
	{else}
	<div class="content list">
		<table style="width: 500px;">
			<th>תאריך</th>
			{foreach from=$reports item=row}
			<tr>
				<td><a href="javascript:void(0);" style="color: black;" class="view_report" report_id="{$row.report_id}">
					השבוע ה-{$row.report_week_number} של {$row.report_year}
					<span>  
					{weeknumber_daterange year="{$row.report_year}" week="{$row.report_week_number}"}
					&nbsp; &nbsp;  
					בין התאריכים: {$weekStartDate|date_format:"%d-%m-%Y"} 
					&nbsp; ל- 
					{$weekEndDate|date_format:"%d-%m-%Y"}
					</span>
				</a></td>
			</tr>
			<tr>
				<td style="background-color: white; background-image: none;" class="report_content" report_id="{$row.report_id}">
				{$row.report_content|stripslashes}
				</td>
			</tr>
			{/foreach}
		</table>		
	</div>
	{/if}
</div>

