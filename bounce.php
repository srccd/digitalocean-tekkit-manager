<?php
/*
Written by SrcCd.com.
*/
session_start();

require 'common.php';

if ($_SESSION['hmauthenticated']!==true) header( 'Location: index.php?logout=yes' );

$getgo = "";
$gettype = "";
$getiid = "";
$geteventid = "";
if (isset($_GET['go'])) $getgo = $_GET['go'];
if (isset($_GET['type'])) $gettype = $_GET['type'];
if (isset($_GET['iid'])) $getiid = $_GET['iid'];
if (isset($_GET['eventid'])) $geteventid = $_GET['eventid'];

if ($getgo=="") header( 'Location: index.php?logout=yes' );
if ($getgo!="refresh"&&$getgo!="new"&&$getgo!="shutdown"&&$getgo!="destroy"&&$getgo!="snapshot") header( 'Location: index.php?logout=yes' );

if ($geteventid=="") {
	if ($getgo=="new"&&$gettype!=""&&$getiid!="") {
	$theactionurl = "https://api.digitalocean.com/v1/".$gettype."/new?client_id=".$myClientID."&api_key=".$myDOApi."&name=".$thedropletname."&size_slug=".$dropletsize."&image_id=".$getiid."&region_slug=".$dropletlocation;
	}

	if (($getgo=="shutdown"||$getgo=="destroy"||$getgo=="snapshot")&&$gettype!=""&&$getiid!="") {
		$theactionurl = "https://api.digitalocean.com/v1/".$gettype."/".$getiid."/".$getgo."/?";
		if ($getgo=="snapshot") $theactionurl .= "name=".$thedropletname."-snap&";
		$theactionurl .= "client_id=".$myClientID."&api_key=".$myDOApi;
	}

	$json = @file_get_contents($theactionurl);
	$geteventid = getEvent($json);
	unset($json);
	if ($geteventid==0) {
		echo "Something went wrong. The event id is missing. Close this window, and return to the Manager."; exit;
	} elseif ($geteventid==5) {
		echo "Digital Ocean reported an error. Close this window, and return to the Manager."; exit;
	} else {
		header( 'Location: bounce.php?go=refresh&eventid=' . $geteventid );
	}
}

$dataevents = getEventMore($geteventid);
$currentpercentage = $dataevents->event->percentage;
if ($currentpercentage < 100) {
?>
<html>
<head>
	<META HTTP-EQUIV="refresh" CONTENT="3" />
</head>
<body>
	Do not close this page yet.<br />
	Still working. Currently at <?=$currentpercentage?>%. This page will refresh until the action is complete.
</body>
</html>

<?php
} else {
	echo "All done. You can close this page, and refresh the Manager.";
}
?>