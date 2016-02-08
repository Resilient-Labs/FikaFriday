<?php
	//MYSQL Script for creating databse:
	//CREATE TABLE team (first TEXT, last TEXT);

	//SLACK INCOMING WEBHOOK URL
	//https://slack.com/signin?redir=%2Fservices%2Fnew%2Fincoming-webhook%2F
	$slackURL = "https://hooks.slack.com/services/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";
	
	// DATABSE HOST
	$dbhost = "localhost";
	// DATABASE USER
	$dbuser = "sid";
	// USER PASSWORD
	$dbpass = "Scooter69";
	// DATABASE NAME
	$dbname = "team";
	$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
?>