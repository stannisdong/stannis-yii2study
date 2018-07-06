<?php
	$mysqli = mysqli_connect("localhost","root","111111","test");
	if (!$mysqli) {
		echo 'Database connect failed!';
	} else {
		echo 'Database connect successfully!';
	}
?>