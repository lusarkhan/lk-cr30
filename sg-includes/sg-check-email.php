<?php

include './../sg-includes/sg-load.php';
$rslt=0;
$q = $_REQUEST["q"];

$hint = "";

if ($q !== "") {
  $q = strtolower($q);
  $qsafe=oracle_escape_string($q);
  $len=strlen($q);
	$sql = "select COUNT(*) AS NUM_ROWS from sg_reg WHERE LOGIN='". htmlspecialchars($qsafe)	. "'";
	$res = oci_parse($conn, $sql);
	oci_define_by_name($res, 'NUM_ROWS', $num_rows);
	oci_execute($res);
	oci_fetch($res);
    if ($num_rows > 0) 
	$rslt=1;
}
echo $rslt === "" ? "" : $rslt;

function oracle_escape_string($str)
{
	return str_replace("'", "", $str);
}
?>
