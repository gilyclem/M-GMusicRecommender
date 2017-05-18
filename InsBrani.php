
<?php
//SELECT * FROM `Brani`,`Autori` WHERE Brani.IDAutore=Autori.IDAutore AND IDBrano>550
//UPDATE `Brani` SET `IDAutore`=9 WHERE IDBrano>=896 AND IDBrano<=917
//SELECT COUNT(*) FROM `Brani` 
ini_set('display_errors','On');
error_reporting(E_ALL);

$host = "localhost";
$username = "root";
$password = "admin";
$database = "MusicDS";
$tabella = "Brani";

$nomefile = "/home/giulia/Scrivania/beet";

$connessione = mysqli_connect ($host,$username,$password) or die ("Connessione impossibile:::::".mysqli_error());
mysqli_select_db ($connessione,$database) or die ("Database non trovato::::".mysqli_error());


if ($file=fopen($nomefile,"r"))//Controllo se si riesce ad aprire in modalita lettura ("r") il file "file.txt"
{
    while (!feof($file))
    {
        $autore=fgets($file,4096);//Leggo una riga intera del file e la inserisco in una variabile
        //$riga=explode("-",$riga);//Taglio la stringa nel punto in cui è presente il carattere -
        //$utenti[$i]['codice']=$riga[0];//Inserisco la prima parte della stringa che rappresenta il codice dell'utente all'interno dell'array
        //$utenti[$i]['nome']=$riga[1];//Inserisco la secondo parte della string che rappresenta il nome dell'utente all'interno dell'array
    
       // $query = "INSERT INTO ".$tabella."(Nome) VALUES ('".$autore."')";
        //mysql_query ($query) or die ("query non eseguita:::::".mysql_error());
	$query = "SELECT IDAutore FROM `Autori` WHERE Nome LIKE '".$autore."%'";
        $result = mysqli_query ($connessione,$query) or die ("query non eseguita:::::".mysqli_error());
	while ($row = mysqli_fetch_assoc($result)) {
    		$IDAutore = $row["IDAutore"];
	}
	do{
        $IDAutore = 181;
	$riga=fgets($file,4096);
	if ($riga!="X\n"){
		if (strpos($riga,"(")!=false)
			$riga=substr($riga, 0, strpos($riga,"("));
		$celebre=0;
		if ($riga[0]=="Y"){
		$celebre=1;
		$riga=substr($riga,1);
		}
		$query = "INSERT INTO ".$tabella." (Titolo,IDAutore,Celebre) VALUES ('".$riga."','".$IDAutore."',".$celebre.")";
		echo $query;
        	mysqli_query ($connessione,$query) or die ("query non eseguita:::::".mysqli_error($connessione));
	}
	} while($riga!="X\n"&&!feof($file));

}}
else
{
    echo "Impossibile aprire il file";//Nel caso in cui non si riesca ad aprire il file
}

fclose($file);//chiudo il descrittore del file
echo "Fine script";

?>

