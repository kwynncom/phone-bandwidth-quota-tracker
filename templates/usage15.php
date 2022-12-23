<div style='border: solid black 1px; display: inline-block; padding: 0ex 0.7ex 0ex 0.7ex; margin-bottom: 1ex; '>
	
	<table>
		<tr><th colspan='1'>MB</th>
			<th >%</th><th style='text-align: left'><span style='font-size: 110%; margin: 0.5ex 0 0 0; '>lower quota</span></th>
		</tr>
			<?php if ($au !== false) { ?> 
				<tr><td><?php echo($au); ?></td> <td class='peru'><?php echo($ap); ?></td>
					<td  class='tdlab'>actual usage</td></tr>
			<?php } ?>
			<tr><td><?php echo($qad); ?></td><td class='peru'>	  <?php echo($ppd); ?></td>
				<td class='tdlab'>can be used by now assuming linear usage</td></tr>
	</table>

	<table style='padding-bottom: 0; margin-bottom: 0; '>
		<tr><th colspan='2' style='text-align: left;'>MB</th></tr>
		<tr><td><?php echo($apd   ); ?></td><td class='tdlab'>per day until turnover can be used, on avg., given actual</td></tr>
		<tr><td><?php echo($perday); ?></td><td class='tdlab'>per day can be used assuming linear usage</td></tr>
	</table>
</div>

