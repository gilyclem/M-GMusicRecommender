<html>
 <head>
  <title>Test PHP</title>
 </head>
 <body>
 <?php
ini_set('display_errors','On');
error_reporting(E_ALL);

$host = "localhost";
$username = "root";
$password = "admin";
$database = "MusicDS";
$tabella = "Autori";

$nomefile = 'AutoriRomantici.txt';

$connessione = mysqli_connect ($host,$username,$password) or die ("Connessione impossibile:::::".mysqli_error());
mysqli_select_db ($connessione,$database) or die ("Database non trovato::::".mysqli_error());


if ($file=fopen("/home/giulia/Scrivania/sii/autoriClassici","r"))//Controllo se si riesce ad aprire in modalita lettura ("r") il file "file.txt"
{
    while (!feof($file))
    {
        $autore=fgets($file,4096);//Leggo una riga intera del file e la inserisco in una variabile
        //$riga=explode("-",$riga);//Taglio la stringa nel punto in cui è presente il carattere -
        //$utenti[$i]['codice']=$riga[0];//Inserisco la prima parte della stringa che rappresenta il codice dell'utente all'interno dell'array
        //$utenti[$i]['nome']=$riga[1];//Inserisco la secondo parte della string che rappresenta il nome dell'utente all'interno dell'array
    
        $query = "INSERT INTO ".$tabella."(Nome,Genere) VALUES ('".$autore."','Classico')";
        mysqli_query ($connessione,$query) or die ("query non eseguita:::::".mysqli_error($connessione));
    
    }
}
else
{
    echo "Impossibile aprire il file";//Nel caso in cui non si riesca ad aprire il file
}
fclose($file);//chiudo il descrittore del file
echo "Fine script";

?>

 </body>
</html>
