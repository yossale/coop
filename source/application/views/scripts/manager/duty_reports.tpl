<!-- js -->
<script>var public_path = '{$public_path}';</script>
<script src="{$js_path}/jquery/ckeditor/ckeditor.js"></script>
<script src="{$js_path}/jquery/ckeditor/adapters/jquery.js"></script>
<script src="{$js_path}/jquery/doTimeout.js"></script>
<script src="{$js_path}/duty_report.js"></script>
<!-- report -->
<div class="section">
	<div class="two_cols_title">
		<div class="title">
			<h3>דו"ח תורן - {$date}</h3>
		</div>
		<div class="left" id="saved">הדוח נשמר אוטומטית עם כל שינוי.</div>
	</div>
	<br class="clear_both" />
	{if $report_is_locked != TRUE}	
		{$report_is_locked = TRUE}
		<div class="content list">
		<form action="" method="POST">
			<textarea name="report_content" class="fckme">{$content|stripslashes}</textarea>		<input type="checkbox" name="email" value="1" id="email"><label for="email">שלח העתק למייל הקואופ</label><br />
			<br /><input type="submit" value="שמירת שינויים" />
			<br /><br />
			<div class="navigate">
				<a href="{$public_path}/duty/" class="back">ביטול וחזרה</a>
			</div>
		</form>
		</div>
	{else}
	<p>הדוח נעול כרגע על ידי משתמש אחר.</p>
	{/if}
</div>

