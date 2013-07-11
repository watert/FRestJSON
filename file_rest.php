<?php 
/** 
 * # File Based Restful JSON Service.
 * 
*/

define(JSON_ENABLED, TRUE);
Class FRest {
	private $_list;
	function __construct(){
		return $this;
	}
	function load($fpath){
		$this->fpath = $fpath;
		$this->is_exists = file_exists($fpath);;
		if($this->is_exists){
			$this->content = file_get_contents($fpath);
			$this->obj = json_decode($this->content,true);
		}else {
			$this->content = "";
			$this->obj = array();
		}
	}
	function save(){
		$this->content = json_encode($this->obj);
		file_put_contents($this->fpath, $this->content);
		return $this;
	}
	function remove(){
		unlink($this->fpath);
	}
	function get_raw(){
		return $this->content;
	}
	function get($key=false){
		if(!$key)return $this->obj;
		else return $this->obj[$key];
	}
	function set($obj){
		$this->obj = $obj;
		$this->content = json_encode($obj);
		return $this;
	}
	function patch($obj){
		foreach ($obj as $key => $value) { // Deep Extending not supported yet. 
			$this->obj[$key] = $value;
		}
		return $this;
	}
	function list_init(){
		if(!isset($this->obj["_list"])){
			$this->obj["_list"]=array();
		}
		$this->_list = &$this->obj["_list"];
		return $this;
	}
	private function list_index_of($id){
		$idx = -1;
		foreach ($this->_list as $key=>&$value) {
			if($value["id"]==$id)$idx = $key;
		}
		return $idx;
	}
	function list_get($id=false){
		if(!$id)return $this->_list;
		else {
			$ret = null;
			foreach ($this->_list as &$value) {
				if($value["id"]==$id)$ret = &$value;
			}
			return $ret;
		}
	}
	function list_add($obj){
		$id = uniqid();
		$obj["id"] = $id;
		$this->lastid = $id;
		array_unshift($this->_list, $obj);
		return $this;
	}
	function list_set($id,$obj){
		$idx = $this->list_index_of($id);
		$item = $this->obj["_list"][$idx];
		$item = array_merge($item,$obj);
		$this->obj["_list"][$idx] = $item;
		return $this;
	}
	function list_remove($id){
		$idx = $this->list_index_of($id);
		unset($this->obj["_list"][$idx]);

	}
	function list_filter($where){

		return $this;
	}
	function list_order($key){

	}
}




/* HTTP Part */


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


$frest = new FRest();
$frest->load($fpath);
if(isset($_GET["list"])){
	$frest->list_init();
	switch($method){
		case "GET":
			http_json($frest->list_get());
			break;
		case "PATCH":
			// $frest->patch($data)->save();
			// http_json($frest->get());
			break;
		case "PUT":
		case "POST":
			if(!$data["id"]){
				$frest->list_add($data)->save();	
				$lastid = $frest->lastid;
				http_json($frest->list_get($lastid));
			}else {
				$id = $data["id"];
				$frest->list_set($id,$data)->save();
				http_json($frest->list_get($id));
			}
			break;
		case "DELETE":
			$frest->list_remove($data["id"]);
			break;
	}
}else {
	switch($method){
		case "GET":
			if(!$frest->is_exists){
				http_400("File not exists.");
			}else {
				http_json_raw($frest->get_raw());
			}
			break;
		case "PATCH":
			$frest->patch($data)->save();
			http_json($frest->get());
			break;
		case "PUT":
		case "POST":
			$frest->set($data)->save();
			http_json($frest->get());
			break;
		case "DELETE":
			$frest->remove();
			break;
	}
}