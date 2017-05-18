<?php
ini_set('display_errors','On');
error_reporting(E_ALL);

function getID($nomeAutore){
	$nome1=rtrim($nomeAutore);
	$autore1=str_replace(' ', '_',$nome1);
	$url1 = "https://tagme.d4science.org/tagme/tag?lang=en&gcube-token=e79638b9-2880-43ce-b3b9-b976fede17bc-843339462&text=".urlencode($autore1);
	$data1 = file_get_contents($url1);
	$array = explode(",",$data1);
	//echo $data1."<br>";
	$id1 = substr($array[4],21);
	return $id1;
}

$host = "localhost";
$username = "root";
$password = "admin";
$database = "MusicDS";
$tabella = "Autori";

$connessione = mysqli_connect ($host,$username,$password) or die ("Connessione impossibile:::::".mysqli_error());
mysqli_select_db ($connessione,$database) or die ("Database non trovato::::".mysqli_error());
$nome="";
$id=0;
for($i=150; $i<200;$i++){
$sql = "SELECT Nome FROM Autori WHERE IDAutore=".$i;
$result = mysqli_query($connessione,$sql);
while ($row = mysqli_fetch_array($result)) {
    $nome = $row['Nome'];
    $id = getID($nome);
    
  }  
if($id!=0){
	  echo $id."<br>";
	  $sql2 = "UPDATE `Autori` SET `IdTagMe`=".$id." WHERE IDAutore=".$i; 
	  mysqli_query($connessione,$sql2); 
}
}


?>
