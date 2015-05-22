<div class="section">
    <div class="title">
        <h3>חברי הקואופ</h3>
    </div>

    <div class="content">
        {if $users != null}
            <div class="list">
                <table>
                    <th>שם</th>
                    <th>תפקיד</th>
                    <th>טלפון</th>
                    <th>E-mail</th>
                    {foreach from=$users item=row}
                        <tr>
                            <td>
                                {$row.user_first_name|escape:"html"|stripslashes}
                                {$row.user_last_name|escape:"html"|stripslashes}
                            </td>
                            <td>{$row.user_job|escape:"html"|stripslashes}</td>
                            <td align="left">{$row.user_phone|escape:"html"|stripslashes}</td>
                            <td align="left">{$row.user_email|escape:"html"|stripslashes}</td>
                        </tr>
                    {/foreach}
                </table>
            </div>
        {else}
            <p>אין אף משתמש כרגע.</p>
        {/if}
    </div>
</div>
