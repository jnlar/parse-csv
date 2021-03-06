<?php

// autoloader dynamically imports classes created in the /classes folder
require 'includes/autoloader.php';

$opt = new opt();
opt::$args = opt::$cli->parse($argv, true);

$parse = new parse();

/*
* Declare the name of the table and table creation query here
*/
$table = dbh::$table = "users";
dbh::$query = "
	DROP TABLE IF EXISTS $table;
	CREATE TABLE $table (
	name VARCHAR(150),
	surname VARCHAR(150),
	email VARCHAR(150) UNIQUE);
";

if (count($argv) == 1) {
	opt::$cli->writeHelp();
}

/*
* Bind options to their corresponding methods
*/
$opt->bind_opt();
