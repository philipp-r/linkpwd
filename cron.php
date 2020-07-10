<?php
echo "deleting links WHERE ".time()."> `expireDate` AND `expireDate` != 0 ";

// get data from MySQL database
require "includes/bdd.php";
$dbQuery = $db->prepare("DELETE FROM `".MYSQL_TABLEPREFIX."links` WHERE :timest > `expireDate` AND `expireDate` != 0;");
$dbExecData = array(
	":timest" => time()
);
$dbQuery->execute($dbExecData);
