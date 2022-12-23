<table>
	<tr><th>MB</th> <th>%</th><th></th></tr>
		<?php if ($au !== false) { ?> 
			<tr><td><?php echo($au); ?></td> <td class='peru'><?php echo($ap); ?></td>
				<td  class='tdlab'>actual usage</td></tr>
		<?php } ?>
		<tr><td><?php echo($qad); ?></td><td class='peru'>	  <?php echo($ppd); ?></td>
			<td class='tdlab'>can be used by now assuming linear usage</td></tr>
</table>

<table>
	<tr><th>MB</th><th></th></tr>
    <tr><td><?php echo($apd   ); ?></td><td class='tdlab'>per day until turnover can be used, on avg., given actual</td></tr>
	<tr><td><?php echo($perday); ?></td><td class='tdlab'>per day can be used assuming linear usage</td></tr>
</table>

