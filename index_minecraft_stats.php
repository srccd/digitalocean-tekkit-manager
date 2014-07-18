<?php
/*
Written by SrcCd.com.
Most of this comes from redwallhp:
https://github.com/redwallhp/MCServerStatus
*/

$servers = array(
	$_GET['ip'] . ":" . $_GET['port']
);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>MC Server Status</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<style>
	tr td,tr th {text-align:center !important}
	tr td.motd,tr th.motd{text-align:left !important;}
	.badge-success{background-color:#468847;}
	.badge-important{background-color:#b94a48;}
	.status{width:50px;}
	</style>
</head>
<body>
<h1>MC Server Status</h1>
<p>Basic Minecraft server meta and online/offline status.</p>

<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th class="status">Status</th>
			<th class="motd">Server</th>
			<th>Players</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($servers as $server): ?>
		<?php $stats = Stats::retrieve(new Server($server)); ?>
		<tr>
			<td>
				<?php if($stats->is_online): ?>
				<span class="badge badge-success">&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<?php else: ?>
				<span class="badge badge-important">&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<?php endif; ?>
			</td>
			<td class="motd"><?php echo $stats->motd; ?> <code><?php echo $server; ?></code></td>
			<td><?php printf('%u/%u', $stats->online_players, $stats->max_players); ?></td>
		</tr>
		<?php unset($stats); ?>
		<?php endforeach; ?>
	</tbody>
</table>

</body>
</html>



<?php

class Server {
	protected $hostname;
	protected $port;

	public function __construct($hostname='127.0.0.1', $port=25565) {
		$this->setPort($port);
		$this->setHostname($hostname);
	}

	/**
	 * Must be IP or domain. (only IPv4)
	 */
	public function setHostname($hostname) {

		// Overload for hostname:port syntax.
		if( preg_match('/:\d+$/', $hostname) ) {

			// if protocol (e.g., 'http') was included; strip it out
			if( preg_match('/:\/\//', $hostname) ) {
				list($protocol, $this->hostname, $this->port) = explode(':', str_replace('//', '', $hostname));
			} else {
				list($this->hostname, $this->port) = explode(':', $hostname);
			}

		} else {
			$this->hostname = $hostname;
		}
	}

	public function getHostname() {
		return $this->hostname;
	}

	public function setPort($port) {

		if(is_int($port)) {
			$this->port = $port;
		} else if( is_numeric($port) ) {
			$this->port = intval($port);
		}
	}

	public function getPort() {
		return $this->port;
	}
}

class Stats {
	public static function retrieve( Server $server ) {
		$socket = @stream_socket_client(sprintf('tcp://%s:%u', $server->getHostname(), $server->getPort()), $errno, $errstr, 1);
		$stats = new \stdClass;
		$stats->is_online = false;

		if (!$socket)
			return $stats;

		fwrite($socket, "\xfe");
		$data = fread($socket, 256);
		fclose($socket);

		// Is this a disconnect with the ping?
		if($data == false AND substr($data, 0, 1) != "\xFF") 
			return $stats;

		$data = substr($data, 3);
		$data = mb_convert_encoding($data, 'auto', 'UCS-2');
		$data = explode("\xA7", $data);

		$stats->is_online = true;
		list($stats->motd, $stats->online_players, $stats->max_players) = $data;

		return $stats;
	}
}
?>