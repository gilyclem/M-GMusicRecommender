<?php
ini_set('display_errors','On');
error_reporting(E_ALL);

function getID($nomeAutore){
	$nome1=rtrim($nomeAutore);
	$autore1=str_replace(' ', '_',$nome1);
	$url1 = "https://tagme.d4science.org/tagme/tag?lang=en&gcube-token=e79638b9-2880-43ce-b3b9-b976fede17bc-843339462&text=".urlencode($autore1);
	$data1 = file_get_contents($url1);
	$array = explode(",",$data1);
	$id1 = substr($array[4],21);
	return $id1;
	
	
}

function correla($id1,$id2){
	$url ="https://tagme.d4science.org/tagme/rel?lang=en&gcube-token=e79638b9-2880-43ce-b3b9-b976fede17bc-843339462&id=";
	$query = $url.urlencode($id1." ".$id2);
	$data = file_get_contents($query);
	//echo $data;
	$array = explode(",",$data);
	$num = substr($array[2],6,6);
	$res1 = $num*100;
	return $res1;
}
?>
