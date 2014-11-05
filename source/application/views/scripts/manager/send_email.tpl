<!-- js -->
<script src="{$js_path}/jquery/ckeditor/ckeditor.js"></script>
<script src="{$js_path}/jquery/ckeditor/adapters/jquery.js"></script>
<script src="{$js_path}/jquery/ckfinder/ckfinder.js"></script>
<script>var js_path = '{$js_path}';</script>
<script src="{$js_path}/send_email.js"></script>

<!-- products from -->
<div class="section">
	<div class="title">
		<h3>שליחת מייל</h3>
	</div>
	<div class="content">
		<form action="" method="POST" class="form">
			<p>
				<a>תגיות שאפשר להשתמש בכותרת ובתוכן: [שם פרטי] [שם משפחה] [אימייל] [סיסמא] [טלפון]</a>
			</p>
			<p>
				<label>מאת:</label>
				<input type="text" name="email_from_name" value="{$from_name|escape:"html"|stripslashes}" />
			</p>
			<p>
				<label>מייל לחזרה:</label>
				<input type="text" name="email_from_email" value="{$from_email}" dir="ltr" />
			</p>
			<p>
				<label>נמענים:</label>
				<input type="radio" name="send_to" value="everyone" checked />&nbsp;<a>כולם</a>
				<a>&nbsp;&nbsp;&nbsp;</a>
				<input type="radio" name="send_to" value="specific_users" />&nbsp;<a>בחירת משתמשים</a>
			</p>			
			<p id="list_users">
				<a>בחר משתמשים לשליחה:</a><br />
				{foreach from=$users item=row}
				<input type="checkbox" name="users[{$row.user_id}]" value="1" />&nbsp;<a>{$row.user_first_name|escape:"html"|stripslashes} {$row.user_last_name|escape:"html"|stripslashes}</a><br />
				{/foreach}
			</p>		
			<p>
				<label>כותרת:</label>
				<input type="text" name="email_subject" size="50" />
			</p>
			<p>
				<label>תוכן:</label><br />
			</p>
			<p>
				<textarea name="email_body" class="fckme"><div dir="rtl"></textarea>
			</p>
			<p class="submit">
				<input type="submit" value="אישור" />
			</p>
			<div class="navigate">
				<a href="{$public_path}/manager/" class="back">ביטול וחזרה</a>
			</div>
		</form>
	</div>
</div>

