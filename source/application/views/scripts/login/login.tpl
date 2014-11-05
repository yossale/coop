<html>
<head>
	<title>התחברות</title>
	<link rel="stylesheet" href="{$css_path}/admin.css" />
	<script src="{$js_path}/jquery/jquery_latest.js"></script>
	<script src="{$js_path}/jquery/validation/jquery.validate.min.js"></script>
	<script>
            var public_path = '{$public_path}';
	{literal}
		$(document).ready(function() {
                    $("form").validate();
                    $("#coop").change(function()
                        {
                            var id = $(this).val();
                            var url = public_path + "/index/index/coop/" + id;
                            window.location = url;
                        });
		});
	</script>
	{/literal}
</head>
<body>
	<div id="canvas">
		<div id="login_page">
                    
                    {if $redirect_error != null}
                        <p>בחר בבקשה קואופ:</p>
                        <select id="coop">
                        	<option value="">בחר...</option>
                            {foreach from=$allcoops item=coop}
                                
                            <option value="{$coop.coop_id}">{$coop.coop_name|escape:"html"|stripslashes}</option>
                            {/foreach}
                        </select>
                    {else}
                    
                    
			<h1>{$coop.coop_name|escape:"html"|stripslashes}</h1>
			<form action="" method="post">
				<fieldset>
					<legend>התחברות</legend>
					{if $error != null}
					<label class="error">{if $error == "nofields"}נא להזין את כל השדות{else if $error == "invalid"}פרטים לא נכונים{/if}</label>
					<br />
					{/if}
					<label for="email">E-mail:</label>
					<br />
					<input type="text" name="email" dir="ltr" class="required" />
					<br />
					
					<label for="password">סיסמא:</label>
					<br />
					<input type="password" name="password" class="required" />					
				</fieldset>
				<input type="submit" id="submit" value="התחבר" />
			</form>
			<br />
			<div id="back_to_site">
				<a href="{$coop.coop_url|escape:"html"|stripslashes}">חזרה לאתר</a>
			</div>
                     {/if}
		</div>
	</div>
</body>
</html>