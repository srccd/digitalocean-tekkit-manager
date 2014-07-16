<?php
/*
Written by SrcCd.com.
*/

// Add your own client ID here
$myClientID="***ADD YOURS***";

// Add your own API keys here
$myDOApi="***ADD YOURS***";

$thedropletname = "myMC";
$dropletsize = "1gb";
$dropletlocation = "nyc2";
$minecraftport = "24432";

function getDroplets() {
	global $myClientID, $myDOApi;
	$json = file_get_contents("https://api.digitalocean.com/v1/droplets/?client_id=".$myClientID."&api_key=".$myDOApi);
	$getDroplets = json_decode($json);
	unset($json);
	return $getDroplets;
}

function getImages() {
	global $myClientID, $myDOApi;
	$json = file_get_contents("https://api.digitalocean.com/v1/images/?client_id=".$myClientID."&api_key=".$myDOApi."&filter=my_images");
	$getImages = json_decode($json);
	unset($json);
	return $getImages;
}

function getDropletsOnlineCount($data) {
	global $thedropletname;
	$srvcnt = 0;
	foreach($data->droplets as $mydata) {
		$servername=$mydata->name;
		$serverstatus=$mydata->status;
		if ($servername==$thedropletname && $serverstatus=="active") {
			$srvcnt++;
		}
	}
	return $srvcnt;
}

function getDropletsOfflineCount($data) {
	global $thedropletname;
	$srvoffcnt = 0;
	foreach($data->droplets as $mydata) {
		$servername=$mydata->name;
		$serverstatus=$mydata->status;
		if ($servername==$thedropletname && $serverstatus=="off") {
			$srvoffcnt++;
		}
	}
	return $srvoffcnt;
}

function getDropletsOnlineArr($data) {
	global $thedropletname;
	$mcarr = array();
	foreach($data->droplets as $mydata) {
		$serverid=$mydata->id;
		$servername=$mydata->name;
		$serverstatus=$mydata->status;
		if ($servername==$thedropletname && $serverstatus=="active") {
			$mcarr[] = $serverid;
		}
	}
	return $mcarr;
}

function getDropletsOfflineArr($data) {
	global $thedropletname;
	$mcoffarr = array();
	foreach($data->droplets as $mydata) {
		$serverid=$mydata->id;
		$servername=$mydata->name;
		$serverstatus=$mydata->status;
		if ($servername==$thedropletname && $serverstatus=="off") {
			$mcoffarr[] = $serverid;
		}
	}
	return $mcoffarr;
}

function printDroplets($data) {
	global $thedropletname;
	foreach($data->droplets as $mydata) {
		$servername=$mydata->name;
		$serverstatus=$mydata->status;
		if ($servername==$thedropletname) {
			echo $mydata->name . " (" . $serverstatus . "; IP Address " . $mydata->ip_address . ")<br />";
		}
	}
}

function getImagesCount($data) {
	global $thedropletname;
	$imgcnt = 0;
	foreach($data->images as $mydata) {
		$imgname=$mydata->name;
		if ($imgname==$thedropletname."-snap") {
			$imgcnt++;
		}
	}
	return $imgcnt;
}

function getImagesArr($data) {
	global $thedropletname;
	$mcimgarr = array();
	foreach($data->images as $mydata) {
		$imgid=$mydata->id;
		$imgname=$mydata->name;
		if ($imgname==$thedropletname."-snap") {
			$mcimgarr[] = $imgid;
		}
	}
	return $mcimgarr;
}

function stepGo($sarr, $stype, $saction, $totalimages=0) {
	global $myClientID, $myDOApi, $thedropletname;
	if ($stype=="droplets"&&$saction=="snapshot"&&$totalimages>0) {
		echo "already have a snapshot; have to delete it first";
		return;
	}
	$mccnt = 0;
	foreach ($sarr as &$mcvalue) {
		$mccnt++;
		echo "<a target=\"_blank\" href=\"bounce.php?go=".$saction."&type=".$stype."&iid=".$mcvalue."\" onclick=\"return confirm('Really ".$saction."?');\">" . $stype.$mccnt . "</a> | ";
	}
}

function stepNew($sarr, $stype, $saction, $totalservers=0) {
	global $myClientID, $myDOApi, $thedropletname;
	if ($totalservers>0) {
		echo "already have a server; have to delete it first";
		return;
	}
	$mccnt = 0;
	foreach ($sarr as &$mcvalue) {
		$mccnt++;
		echo "<a target=\"_blank\" href=\"bounce.php?go=new&type=".$stype."&iid=".$mcvalue."\" onclick=\"return confirm('Really ".$saction."?');\">" . $stype.$mccnt . "</a> | ";
	}
}

function getEvent($json) {
	$eventid = 0;
	$data = @json_decode($json);
	if (isset($data->status)) {
		$eventstatus = $data->status;
		if ($eventstatus=="ERROR") $eventid = "5"; //5 is just made up so I can pinpoint the issue
		if ($eventstatus=="OK") {
			if (isset($data->event_id)) $eventid = $data->event_id;
			if (isset($data->droplet->event_id)) $eventid = $data->droplet->event_id;
		}
	}
	return $eventid;
}

function getEventMore($eid) {
	global $myClientID, $myDOApi;
	$json = file_get_contents("https://api.digitalocean.com/v1/events/".$eid."/?client_id=".$myClientID."&api_key=".$myDOApi);
	$getEventMore = json_decode($json);
	unset($json);
	return $getEventMore;
}
?>