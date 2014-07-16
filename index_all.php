<?php
/*
Written by SrcCd.com.
*/
session_start();

$thedropletname = "rpdMC";

if ($_SESSION['hmauthenticated']!==true || $_SESSION['hmauthlvl']!="B") header( 'Location: index.php?logout=yes' );
if ($_GET['ready']!="yes") header( 'Location: index_all.php?ready=yes' ); //doing this so there are no callbacks

// Add your own client keys here
$myClientID="8143ec8b02c507132cd3f2c68389c4c5"; 
// Add your own API keys here
$myDOApi="4375850bd5f9b132f31d62d0d77a07f5"; 

$whatlevel = $_SESSION['hmauthlvl'];
if ($whatlevel!="B") header( 'Location: index.php?logout=yes' );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>DigitalOcean Server Manager (via API)</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
</head>

<body>

<h1>DigitalOcean Server Manager (via API)</h1>
<em><a href="index.php?logout=yes">logout</a></em>

<p>roypdhost is my web sites. <strong>LEAVE THIS ALONE.</strong><br />
rpdMC is the Minecraft Tekkit server.</p>

<p><em>First, every time you click a command a popup page will appear. Just close it and return to this page.</em></p>

<p>When done playing, backup and shutdown:<br/>
<ol>
<li>Next to rpdMC (droplets), click <strong>Shutdown</strong>.</li>
<li>Refresh this page.</li>
<li>Once rpdMC status is "off", continue.</li>
<li>Next to rpdMC-snap (images), click <strong>Delete this snapshot</strong>.</li>
<li>Next to rpdMC (droplets), click <strong>Take a Snapshot</strong>.</li>
<li>Refresh this page.</li>
<li>A new rpdMC-snap (images) will show up and the rpdMC (droplet) will go back to Active.</li>
<li>Next to rpdMC (droplets), click <strong>Shutdown</strong>.</li>
<li>Refresh this page.</li>
<li>Once rpdMC status is "off", continue.</li>
<li>Next to rpdMC (droplets), click <strong>Delete this droplet</strong>.</li>
</ol>
</p>

<p>When ready to play again:<br />
<ol>
<li>Next to rpdMC-snap, click <strong>Create Droplet</strong>.</li>
<li>Refresh this page.</li>
<li>Once the new rpdMC (droplet) appears and goes to Active, you can get its IP address to use in Minecraft.</li>
<li>The port number will be 25565</li>
</ol>
</p>
	&nbsp;&nbsp;&nbsp;&nbsp;

	<table width="100%">
		<tr>
			<td colspan="6">Droplets</td>
		</tr>
		<tr>
			<td  style="text-align:center">Server ID</td>
			<td  style="text-align:center">Server Name</td>
			<td  style="text-align:center">IP</td>
			<td  style="text-align:center">Status</td>
			<td  style="text-align:center">Creation Date</td>
			<td  style="text-align:center">Actions</td>
		 
		</tr>

	<?php
	// Get your data from the API provider
	$json = file_get_contents("https://api.digitalocean.com/v1/droplets/?client_id=$myClientID&api_key=$myDOApi");
    $data = json_decode($json);
	
	// Get live hosts
    $liveCounter = substr_count($json, 'status":"active');
	
	// Get Offline hosts
	$deadCounter = substr_count($json, 'status":"off');
	
	// Sum the total
	$counterSum=$liveCounter + $deadCounter; 
    
    foreach($data -> droplets  as $mydata)
    {
		// Set the droplet id for further actions
		$serverid=$mydata->id;
		$servername=$mydata->name;
	?>
		<tr>				    
			<td  style="text-align:center"><?php echo $mydata->id; ?></td>
			<td  style="text-align:center"><?php echo $mydata->name; ?></td>
			<td  style="text-align:center"><?php echo $mydata->ip_address; ?></td>
			<td  style="text-align:center"><?php echo $mydata->status; ?></td>
			<td  style="text-align:center"><?php echo $mydata->created_at; ?></td>
		    <td class="td_title4" style="text-align:center">
		    <?php
		    echo "<a href=\"https://api.digitalocean.com/droplets/$serverid/reboot/?client_id=$myClientID&api_key=$myDOApi\" target=\"_blank\"><font color=\"red\">Reboot</font></a> - ";
		    echo "<a href=\"https://api.digitalocean.com/droplets/$serverid/shutdown/?client_id=$myClientID&api_key=$myDOApi\" target=\"_blank\"><font color=\"red\" onclick=\"return confirm('Really shutdown?');\">Shutdown</font></a> - ";
		    echo "<a href=\"https://api.digitalocean.com/droplets/$serverid/power_on/?client_id=$myClientID&api_key=$myDOApi\" target=\"_blank\"><font color=\"red\">Power On</font></a> - ";
		    echo "<a href=\"https://api.digitalocean.com/v1/droplets/$serverid/snapshot/?name=$servername-snap&client_id=$myClientID&api_key=$myDOApi\" target=\"_blank\"><font color=\"red\">Take a Snapshot</font></a> - ";
		    echo "<a href=\"https://api.digitalocean.com/v1/droplets/$serverid/destroy/?client_id=$myClientID&api_key=$myDOApi\" target=\"_blank\" onclick=\"return confirm('Really delete?');\"><font color=\"red\">Delete this droplet</font></a>";
		    ?></td>
		   
	<?php
 	}//end for
 	unset($json);
	?>
</tr>
	<tr>
		<td colspan="6">
		Online Droplets: <?php echo  "<font color=\"green\">"  . $liveCounter . "</font>"?><br />
		Offline Droplets: <?php echo  "<font color=\"red\">"  . $deadCounter . "</font>"?><br />
		Total Droplets: <?php echo  "<font color=\"black\">"  . $counterSum . "</font>"?><br />
		</td>
	</tr>
</table>

<br /><br />


<table width="100%">
	<tr>
		<td colspan="6">Images</td>
	</tr>
	<tr>
		<td style="text-align:center">Image ID</td>
		<td style="text-align:center">Image Name</td>
		<td style="text-align:center">Image Distribution</td>
		<td style="text-align:center">Image Slug</td>
		<td style="text-align:center">Actions</td>
	</tr>

	<?php
	// Get your data from the API provider
	$json = file_get_contents("https://api.digitalocean.com/v1/images/?client_id=$myClientID&api_key=$myDOApi&filter=my_images");
    $data = json_decode($json);

    foreach($data -> images  as $mydata)
    {
		// Set the droplet id for further actions
		$imageid=$mydata->id;
		$imagename=$mydata->name;
		$imagename=str_replace("-snap", "", $imagename);
	?>
	        
			<tr>				    
			<td  style="text-align:center"><?php echo $mydata->id; ?></td>
			<td  style="text-align:center"><?php echo $mydata->name; ?></td>
			<td  style="text-align:center"><?php echo $mydata->distribution; ?></td>
			<td  style="text-align:center"><?php echo $mydata->slug; ?></td>
		    <td class="td_title4" style="text-align:center">
		    <?php
		    echo "<a href=\"https://api.digitalocean.com/v1/images/$imageid/destroy/?client_id=$myClientID&api_key=$myDOApi\" target=\"_blank\" onclick=\"return confirm('Really delete?');\"><font color=\"red\">Delete this snapshot</font></a> - ";
		    echo "<a href=\"https://api.digitalocean.com/v1/droplets/new?client_id=$myClientID&api_key=$myDOApi&name=$imagename&size_slug=1gb&image_id=$imageid&region_slug=nyc1\" target=\"_blank\" onclick=\"return confirm('Really create a droplet?');\"><font color=\"red\">Create Droplet</font></a>";
		    ?></td>
		   
	<?php
 	}//end for
 	unset($json);
	?>
</tr>
</table>	 

</body>
</html>