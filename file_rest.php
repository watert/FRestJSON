<?php 
/** 
 * # File Based Restful JSON Service.
 * 
*/
define(JSON_ENABLED, TRUE);

function http_ret($msg){
	echo($msg);
}
function http_400($json){ 
	header( 'HTTP/1.1 400 BAD REQUEST' );
	http_json($json);
	exit();
}
function http_json_raw($data){ 
	header("Content-Type:application/json");
	http_ret($data); 
}
function http_json($data){ 
	$data = json_encode($data);
	http_json_raw($data);
}

if(!count($_GET)){
	http_ret(
		"Use it as RESTful Server using URL like this: <br>
		<code>http://xxxxxxx/file_rest.php?f=filename.ext</code><br>
		<a href='./'>Test cases here.</a><br>
		<div style='font-family:consolas;color:orange'> <strong>WARNING:</strong> Support JSON ext only. </div>
		");
	exit();
}
if(!isset($_GET["f"])){
	http_400("Not set GET[f].");
}

$fpath = $_GET["f"];
$ext = pathinfo($fpath, PATHINFO_EXTENSION);
if($ext!="json")http_400("EXT $ext NOT SUPPORTED.");

$method = $_SERVER["REQUEST_METHOD"];

// Default use Backbonejs input type: Content inside `php://input` .
$data = json_decode(file_get_contents('php://input'),true);
if(!$data)$data = $_POST;

$is_exists = file_exists($fpath);
if($is_exists){
	$content = file_get_contents($fpath);
}

switch($method){
	case "GET":
		if(!$is_exists)http_400("File not exists.");
		else http_json_raw($content);
		break;
	case "PUT":
	case "POST":
		// print_r($_POST);
		file_put_contents($fpath, json_encode($data));
		http_json($data);
		break;
	case "DELETE":
		unlink($fpath);
		break;
}