<!DOCTYPE html>
<html lang='en'>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'		    />
    <meta name='viewport'	    content='width=device-width, initial-scale=1.0' />

    <title>phone quota</title>

    <link   href='js_css/s1.css'   rel='stylesheet' type='text/css'>
    <script src ='js_css/js1.js'></script>
</head>
<body>
    <div id='calcs'>
	<?php echo $htout_calcs; ?>
    </div>
    <div class=''>
	<input type='number' id='ausage' step='1' min='0' max=  '<?php echo $htout_umax; ?>' value='<?php echo $htout_au; ?>' class='usage' /> 
	<label>MB actual usage</label>
    </div>
    <div class='unmusagep'>
	<input type='number' id='unmu' step='1' min='<?php echo $htout_au; ?>' value='<?php echo $htout_unmu;  ?>' class='usage' /> 
	<label>MB unmetered usage</label>
    </div>
    <div class='settings'>
	<div><input type='date'   id='turnday' value='<?php echo $htout_ddate; ?>'/>	<label>bill turnover day (any month / year)</label></div>
	<div><input type='number' id='quota'   value='<?php echo $htout_dquo;  ?>' step='1' min='1' max='99'  />
	    <label>GB / month quota</label> <!-- the min above is one way to avoid divide by zero, although it's only client-side and not secure -->
	</div>
    </div>
</body>
</html>