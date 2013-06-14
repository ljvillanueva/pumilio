<?php
include("class.db.php");
$version = "1.0.2";
$released = "December 9, 2010";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<title>PHP PDO Wrapper Class</title>
		<link href="style.css" rel="stylesheet" type="text/css"/>
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/> 
	</head>
	<body>
		<div id="header">
			<div id="links">
				<a href="http://code.google.com/p/php-pdo-wrapper-class/">Homepage - Google Code Project Hosting</a>
				<a href="http://php-pdo-wrapper-class.googlecode.com/files/ppwc-<?php echo($version);?>.zip">Download Version <?php echo $version;?></a>
			</div>
			<h2>PHP PDO Wrapper Class</h2>
			<ul>
				<li>Version: <?php echo $version;?></li>
				<li>Released: <?php echo $released;?></li>
			</ul>	
			<div class="clear"></div>
		</div>
		<div id="left">
			<h2 class="first">Table of Contents</h2>
			<ul>
				<li><a href="#project-overview">Project Overview</a></li>
				<li><a href="#system-requirements">System Requirements</a></li>
				<li><a href="#db-class-constructor">db Class Constructor</a></li>
				<li><a href="#db-class-methods">db Class Methods</a></li>
				<li class="indent"><a href="#delete">delete</a></li>
				<li class="indent"><a href="#insert">insert</a></li>
				<li class="indent"><a href="#run">run</a></li>
				<li class="indent"><a href="#select">select</a></li>
				<li class="indent"><a href="#setErrorCallbackFunction">setErrorCallbackFunction</a></li>
				<li class="indent"><a href="#update">update</a></li>
			</ul>
		</div>
		<div id="right">
			<h2 class="first"><a name="project-overview">Project Overview</a></h2>
			<p>This project provides a minimal extension for <a href="http://us3.php.net/manual/en/book.pdo.php">PHP's PDO (PHP Data Objects) class</a> designed for ease-of-use and saving development time/effort.
			This is achived by providing methods - delete, insert, select, and update - for quickly building common SQL statements, handling exceptions when
			SQL errors are produced, and automatically returning results/number of affected rows for the appropriate SQL statement types.</p>

			<h2><a name="system-requirements">System Requirements</a></h2>
			<ul>
				<li>PHP 5</li>
				<li>PDO Extension</li>
				<li>Appropriate PDO Driver(s) - PDO_SQLITE, PDO_MYSQL, PDO_PGSQL</li>
				<li>Only MySQL, SQLite, and PostgreSQL database types are currently supported.</li>
			</ul>	

			<h2><a name="db-class-constructor">db Class Constructor</a></h2>
			<?php

echo '<pre>', highlight_string('<?php
//__contruct Method Declaration
public function __construct($dsn, $user="", $passwd="") { }

//MySQL
$db = new db("mysql:host=127.0.0.1;port=8889;dbname=mydb", "dbuser", "dbpasswd");

//SQLite
$db = new db("sqlite:db.sqlite");
?>', true), '</pre>';
			
			?>
			<p>More information can be found on how to set the dsn parameter by following the links provided below.</p>
			<ul>
				<li>MySQL - <a href="http://us3.php.net/manual/en/ref.pdo-mysql.connection.php">http://us3.php.net/manual/en/ref.pdo-mysql.connection.php</a></li>
				<li>SQLite - <a href="http://us3.php.net/manual/en/ref.pdo-sqlite.connection.php">http://us3.php.net/manual/en/ref.pdo-sqlite.connection.php</a></li>
				<li>PostreSQL - <a href="http://us3.php.net/manual/en/ref.pdo-pgsql.connection.php">http://us3.php.net/manual/en/ref.pdo-pgsql.connection.php</a></li>
			</ul>

			<h2><a name="db-class-methods">db Class Methods</a></h2>
			<p>Below you will find a detailed explanation along with code samples for each of the 6 methods included in the db class.</p>

			<h2><a name="delete">delete</a></h2>
			<?php

echo '<pre>', highlight_string('<?php
//delete Method Declaration
public function delete($table, $where, $bind="") { }

//DELETE #1
$db->delete("mytable", "Age < 30");

//DELETE #2 w/Prepared Statement
$lname = "Doe";
$bind = array(
	":lname" => $lname
)
$db->delete("mytable", "LName = :lname", $bind);
?>', true), '</pre>';

			?>
			<p>If no SQL errors are produced, this method will return the number of rows affected by the DELETE statement.</p>

			<h2><a name="insert">insert</a></h2>
			<?php

echo '<pre>', highlight_string('<?php
//insert Method Declaration
public function insert($table, $info) { }

$insert = array(
	"FName" => "John",
	"LName" => "Doe",
	"Age" => 26,
	"Gender" => "male"
);
$db->insert("mytable", $insert);
?>', true), '</pre>';

			?>
			<p>If no SQL errors are produced, this method will return the number of rows affected by the INSERT statement.</p>

			<h2><a name="run">run</a></h2>
			<?php

echo '<pre>', highlight_string('<?php
//run Method Declaration
public function run($sql, $bind="") { }

//MySQL
$sql = <<<STR
CREATE TABLE mytable (
	ID int(11) NOT NULL AUTO_INCREMENT,
	FName varchar(50) NOT NULL,
	LName varchar(50) NOT NULL,
	Age int(11) NOT NULL,
	Gender enum(\'male\',\'female\') NOT NULL,
	PRIMARY KEY (ID)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;
STR;
$db->run($sql);

//SQLite
$sql = <<<STR
CREATE TABLE mytable (
	ID INTEGER PRIMARY KEY,
	LName TEXT,
	FName TEXT,
	Age INTEGER,
	Gender TEXT
)
STR;
$db->run($sql);
?>', true), '</pre>';
			
			?>
			<p>This method is used to run free-form SQL statements that can't be handled by the included delete, insert, select, or update methods.
			If no SQL errors are produced, this method will return the number of affected rows for DELETE, INSERT, and UPDATE statements, or an associate array of results for
			SELECT, DESCRIBE, and PRAGMA statements.</p>

			<h2><a name="select">select</a></h2>
			<?php

echo '<pre>', highlight_string('<?php
//select Method Declaration
public function select($table, $where="", $bind="", $fields="*") { }

//SELECT #1
$results = $db->select("mytable");

//SELECT #2
$results = $db->select("mytable", "Gender = \'male\'");

//SELECT #3 w/Prepared Statement
$search = "J";
$bind = array(
	":search" => "%$search"
);
$results = $db->select("mytable", "FName LIKE :search", $bind);
?>', true), '</pre>';

			?>
			<h2><a name="setErrorCallbackFunction">setErrorCallbackFunction</a></h2>
			<?php

echo '<pre>', highlight_string('<?php
//setErrorCallbackFunction Method Declaration
public function setErrorCallbackFunction($errorCallbackFunction, $errorMsgFormat="html") { }

//The error message can then be displayed, emailed, etc within the callback function.
function myErrorHandler($error) {
}

$db = new db("mysql:host=127.0.0.1;port=8889;dbname=mydb", "dbuser", "dbpasswd");
$db->setErrorCallbackFunction("myErrorHandler");
/*
Text Version
$db->setErrorCallbackFunction("myErrorHandler", "text");

Internal/Built-In PHP Function
$db->setErrorCallbackFunction("echo");
*/
$results = $db->select("mynonexistingtable");
?>', true), '</pre>';

			?>
			<p>When a SQL error occurs, this project will send a formatted (html or text) error message to a callback function specified through the setErrorCallbackFunction
			method.  The callback function's name should be supplied as a string without parenthesis.  As you can see in the examples provided above, you can specify an 
			internal/built-in PHP function or a custom function you've created.</p>
			<p>If no SQL errors are produced, this method will return an associative array of results.</p>

			<h2><a name="update">update</a></h2>
			<?php

echo '<pre>', highlight_string('<?php
//update Method Declaration
public function update($table, $info, $where, $bind="") { }

//Update #1
$update = array(
	"FName" => "Jane",
	"Gender" => "female"
);
$db->update("mytable", $update, "FName = \'John\'");

//Update #2 w/Prepared Statement
$update = array(
	"Age" => 24
);
$fname = "Jane";
$lname = "Doe";
$bind = array(
	":fname" => $fname,
	":lname" => $lname
);
$db->update("mytable", $update, "FName = :fname AND LName = :lname", $bind);
?>', true), '</pre>';

			?>
			<p>If no SQL errors are produced, this method will return the number of rows affected by the UPDATE statement.</p>
		</div>	
		<div class="clear"></div>	
	</body>
</html>	
