<?php
/*
Written by SrcCd.com.
*/
session_start();

if ($_GET['logout']=="yes") {
	$_SESSION = array();
	session_destroy();
	header( 'Location: index.php' );
}

//pw for the page itself
$secretpasswordA = 'givememinecraft';

if ($_SESSION['hmauthenticated'] == true) {
	$goahead = 'yes';
}else{
	$goahead = 'no';
	$error = null;
	if (!empty($_POST)) {
		$password = empty($_POST['psswrdab']) ? null : $_POST['psswrdab'];
		if ($password == $secretpasswordA) {
			$_SESSION['hmauthenticated'] = true;
			$_SESSION['hmauthlvl'] = "A";
			if ($_GET['ready']!="yes") header( 'Location: index_minecraft.php?ready=yes' );
			$goahead = 'yes';
		} else {
			$error = 'Incorrect password';
		}
	}
}

if ($goahead != 'yes') {
	// Create a login form or something
	echo $error;
	echo '<html><body><form action="index.php" method="POST"><input type="password" name="psswrdab" /><input type="submit" value="login" /></form></body></html>';
	exit;
}

$whatlevel = $_SESSION['hmauthlvl'];
if ($whatlevel=="A") header( 'Location: index_minecraft.php?ready=yes' );
?>