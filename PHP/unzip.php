<?php 
require_once('pclzip.lib.php'); 
$fileName = $_GET["file"];
$ExtractPath = $_GET["path"];
$archive = new PclZip($fileName); 
if ($archive->extract(PCLZIP_OPT_PATH,$ExtractPath) == 0) {
	die("Error : ".$archive->errorInfo(true)); 
}
else{
	echo "upzip_done";
} 
?> 