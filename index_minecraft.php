<?php
/*
Written by SrcCd.com.
*/
session_start();

require 'common.php';

if ($_SESSION['hmauthenticated']!==true || $_SESSION['hmauthlvl']!="A") header( 'Location: index.php?logout=yes' );
if ($_GET['ready']!="yes") header( 'Location: index_minecraft.php?ready=yes' ); //doing this so there are no callbacks

$whatlevel = $_SESSION['hmauthlvl'];
if ($whatlevel!="A") header( 'Location: index.php?logout=yes' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>DigitalOcean Minecraft Server Manager (via API)</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
</head>
<body>
	<h1>DigitalOcean Minecraft Server Manager (via API)</h1>
	<em><a href="index.php?logout=yes">logout</a></em>

	<?php
	$data_droplets = getDroplets();
	$srvcnt = getDropletsOnlineCount($data_droplets);
	$mcarr = getDropletsOnlineArr($data_droplets);
	$srvoffcnt = getDropletsOfflineCount($data_droplets);
	$mcoffarr = getDropletsOfflineArr($data_droplets);
	$totalsrvcnt = $srvcnt + $srvoffcnt;
	$data_images = getImages();
	$imgcnt = getImagesCount($data_images);
	$mcimgarr = getImagesArr($data_images);
	sort($mcarr); sort($mcoffarr); sort($mcimgarr);

	echo "<p>List of MC servers:<br />";
	printDroplets($data_droplets);
	echo "<br />Offline MC server count: " . $srvoffcnt . "<br/>";
	echo "Online MC server count: " . $srvcnt;
	if ($srvcnt>1) echo " - Why do we have more than 1? Jump to #1 and #9 below to shutdown and delete the lowest numbered servers. (e.g., if 3 of them, delete 1 and 2)";
	echo "<br />Snapshot count: " . $imgcnt;
	if ($imgcnt>1) echo " - Why do we have more than 1? Jump to #3 below to delete the lowest numbered snapshots. (e.g., if 3 of them, delete 1 and 2)";
	echo "</p>";
	?>

	<p>Follow the steps in order - some links remain hidden until you complete the steps. Note that clicking most links causes a popup page to appear. Wait for the popup page to load, and follow the directions within.</p>

	<p>When done playing (if it will be more than a couple hours until you play again):
		<ol>
			<li>Let's Shutdown the MC server(s). (<?php stepGo($mcarr, 'droplets', 'shutdown'); ?>)</li>
			<li>Refresh this page to be sure there are no servers listed between the () in #1.</li>
			<li>Delete the old snapshot(s). (<?php stepGo($mcimgarr, 'images', 'destroy'); ?>)</li>
			<li>Refresh this page to be sure there are no old snapshots listed between the () in #3.</li>
			<li>Grab a new Snapshot. (<?php stepGo($mcoffarr, 'droplets', 'snapshot', $imgcnt); ?>)</li>
			<li>Refresh this page until you see a server show up between the () in #7 (since snapshots restart the server).</li>
			<li>Shutdown the MC server(s) again. (<?php stepGo($mcarr, 'droplets', 'shutdown'); ?>)</li>
			<li>Refresh this page to be sure there are no servers listed between the () in #7.</li>
			<li>Now Delete the MC server(s). (<?php stepGo($mcoffarr, 'droplets', 'destroy'); ?>)</li>
			<li>At this point, we should have 0 offline and online servers, and 1 snapshot.</li>
		</ol>
	</p>

	<p>When ready to play again:
		<ol>
			<li>Create a new MC server. (<?php stepNew($mcimgarr, 'droplets', 'create', $totalsrvcnt); ?>)</li>
			<li>A page will popup and inform you of the status. When it is done, grab the IP address.</li>
			<li>Port is <?=$minecraftport?>.</li>
		</ol>
	</p>

</body>
</html>