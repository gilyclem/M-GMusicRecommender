<html>
<head> <title> M&G Music Rec </title> </head>
<!--<body background="sfondo1.jpg" style="background-repeat: no-repeat">-->
<body bgcolor="#ffffcc">
<?php

//Raccolta dei dati immessi dall'utente

ini_set('display_errors','On');
error_reporting(E_ALL);

include 'TagMe.php';

$arraytabella=array();

$host = "localhost";
$username = "root";
$password = "admin";
$database = "MusicDS";

$connessione = mysqli_connect ($host,$username,$password) or die ("Connessione impossibile:::::".mysqli_error());
mysqli_select_db ($connessione,$database) or die ("Database non trovato::::".mysqli_error());

$utente=$_POST["ChiaveU"];
echo "Utente: ".$utente."<br>";

echo "<h3> Hai scelto: </h3><br>"; 

$idbrano=$_POST["Brano"];
$sql = "SELECT * FROM `Brani`,`Autori` WHERE IDBrano='".$idbrano."' AND Brani.IDAutore=Autori.IDAutore";
$result = mysqli_query($connessione,$sql);

while ($row = mysqli_fetch_array($result)) {
    $nomeautore=$row['Nome'];
    $titolo=$row['Titolo'];
    $idautore=$row['IDAutore'];
    $paese=$row['Paese'];
    $genere=$row['Genere'];
    $idtagme=$row['IdTagMe'];
    echo "Di <b>". $nomeautore."</b><br>";
    echo "la composizione <b>".$titolo."</b><br>";
    echo "Genere: <b>".$genere."</b><br>";
    echo "Paese di origine dell'autore: <b>".$paese."</b><br>"; 

}

$sql = "SELECT * FROM `Utente` WHERE `IDUtente`=".$utente." AND `Voto`<=2";
$result = mysqli_query($connessione,$sql);
$arrayesclusi=[];
while ($row = mysqli_fetch_array($result)) {
    array_push($arrayesclusi,$row['IDBrano']);
}

echo "<h3> Ti consigliamo: </h3><br>"; 
$esperto=$_POST["Esperto"];
if ($esperto=="si") {$celebre=0;$cel="no";}
if ($esperto=="no") {$celebre=1;$cel="si";}


//Inizio del calcolo di similarità Estrazione di informazioni su autore e brano STESSO GENERE E PAESE

$sql = "SELECT * FROM `Brani`,`Autori` WHERE Genere='".$genere."' AND Paese='".$paese."' AND Brani.IDAutore=Autori.IDAutore AND Celebre=".$celebre." AND NOT(Brani.IDAutore=".$idautore.")";
//echo $sql;
$result = mysqli_query($connessione,$sql);
if($result->num_rows === 0){
	$sql = "SELECT * FROM `Brani`,`Autori` WHERE Paese='".$paese."' AND Brani.IDAutore=Autori.IDAutore AND Celebre=".$celebre." AND NOT(Brani.IDAutore=".$idautore.")";
	$result = mysqli_query($connessione,$sql);
}



if(isset($_POST['Espandi1']) && $_POST['Espandi1']=="Si") {

if($result->num_rows != 0){
echo "<u>Brani di autori della stessa corrente culturale e paese:</u><br><br>";
echo "<table><tr><td><u>Titolo</td><td><u>Autore</td><td><u>Genere</td><td><u>Paese</td><td><u>Celebre</td><td><u>Ascolta</u></td><td><u>Vota</u></td></tr>";}

while (($row = mysqli_fetch_array($result))) {
    $idbrano2 = $row['IDBrano'];
    $nomeautore2=$row['Nome'];
    $titolo2=$row['Titolo'];
    $idautore2=$row['IDAutore'];
    $paese2=$row['Paese'];
    $genere2=$row['Genere'];
    $youtube="<a href='https://www.youtube.com/results?search_query=".$nomeautore2." ".$titolo2."'>Ascolta</a>";

    echo "<tr><td>".$titolo2."</td><td>".$nomeautore2."</td><td>".$genere2."</td><td>".$paese2."</td><td>".$cel."</td><td>".$youtube."</ td><td>
<form action='checkbox.php' method='post'>
<input type='hidden' name='Brano' value='".$idbrano."'/>
<input type='hidden' name='Esperto' value='".$esperto."'/>
<input type='hidden' name='BranoVoto' value='".$idbrano2."'/>
<input type='hidden' name='ChiaveU' value='".$utente."'/>
<input type='submit' value='Vota'>
</form></td><td></td></tr>";
}
echo "</table> <br>";

echo "<form action='similarity.php' method='post'>";
echo "<input type='hidden' name='Brano' value='".$idbrano."'/>";
echo "<input type='hidden' name='Esperto' value='".$esperto."'/>";
echo "<input type='hidden' name='ChiaveU' value='".$utente."'/>";
echo "<input type='submit' value='Chiudi'>";
echo "</form><br> <br>";
}
else {
if($result->num_rows != 0){
echo "<u>Brani di autori della stessa corrente culturale e paese:</u><br><br>";
echo "<table><tr><td><u>Titolo</td><td><u>Autore</td><td><u>Genere</td><td><u>Paese</td><td><u>Celebre</td><td><u>Ascolta</u></td><td><u>Vota</u></td><td><u>Similarità</u></td></tr>";}
$arrayautori=[];

while ($row = mysqli_fetch_array($result)) {
    $idbrano2 = $row['IDBrano'];
    $nomeautore2=$row['Nome'];
    $titolo2=$row['Titolo'];
    $idautore2=$row['IDAutore'];
    $paese2=$row['Paese'];
    $genere2=$row['Genere'];
    $idtagme2=$row['IdTagMe'];
    $youtube="<a href='https://www.youtube.com/results?search_query=".$nomeautore2." ".$titolo2."'>Ascolta</a>";

    if (!in_array($idbrano2, $arrayesclusi)) {
	if (!in_array($nomeautore2, $arrayautori)) {    
		array_push($arrayautori,$nomeautore2);
    $percentuale=correla($idtagme,$idtagme2);
    $stringa ="<tr><td>".$titolo2."</td><td>".$nomeautore2."</td><td>".$genere2."</td><td>".$paese2."</td><td>".$cel."</td><td>".$youtube."</ td><td>
<form action='checkbox.php' method='post'>
<input type='hidden' name='Brano' value='".$idbrano."'/>
<input type='hidden' name='Esperto' value='".$esperto."'/>
<input type='hidden' name='BranoVoto' value='".$idbrano2."'/>
<input type='hidden' name='ChiaveU' value='".$utente."'/><input type='submit' value='Vota'>
</form></td><td>".$percentuale."%</td></tr>";
	$arraytabella[$stringa]=$percentuale;}

}}
arsort($arraytabella);
foreach ($arraytabella as $key => $value) {
    echo $key;
}

echo "</table>  <br>";

echo "<form action='similarity.php' method='post'>";
echo "<input type='hidden' name='Espandi1' value='Si' />";
echo "<input type='hidden' name='Brano' value='".$idbrano."'/>";
echo "<input type='hidden' name='Esperto' value='".$esperto."'/>";
echo "<input type='hidden' name='ChiaveU' value='".$utente."'/>";
echo "<input type='submit' value='Espandi1'>";
echo "</form><br> <br>";
}

//STESSA FORMA
$forma = array("gavott","gavott","minuett","menuet","danz","tanz","sicili","passacagli","rondo","messa","missa","messe","ave maria","marcia funebre","magnificat","oratori","litani","vesper","vespr","ave verum","sonat", "sinfoni","concert","toccat","fug","suite","quartetto","sinfoniett","variazion","divertiment","ballat","ballade","capricci","valzer","valse","polk","marcia","marce","marsch","berceuse", "lieder","rapsodi","fantasi","mazurk","mazurc", "studi","improvvis","arabeske","arabesco","arabesque","romanz","moto perpetuo","cantabile","notturn","polacc","prelud","scherz","poema sinfonico","marcia funebre","serenat","pavan","inn","bagatell","ouverture","intermezz","ballet");
$strumenti = array("xilofono","obo","violin", "pianofort", "chitarr","violoncell","flaut","voc","arp","clavicembal","clarinett","corn","contrabass","viol","organ","tenor","orchestra","voce recitante","archi","ottavin","tri","quintett","fagott","bass","tromb","timpan");

$i=0;
while(strpos(strtoupper($titolo), strtoupper($forma[$i])) == false && $i<count($forma)-1) {
    $i++;
    if (strpos(strtoupper($titolo), strtoupper($forma[$i])) !== false) $formabr=$forma[$i];
}


if (isset($formabr)) {
echo "<u>Brani di analoga forma musicale:</u><br><br>";
$sql = "SELECT * FROM `Brani`,`Autori` WHERE UPPER(Titolo) LIKE UPPER('%".$formabr."%') AND Brani.IDAutore=Autori.IDAutore AND Celebre=".$celebre." AND NOT(Brani.IDAutore=".$idautore.")";
$result = mysqli_query($connessione,$sql);

if(isset($_POST['Espandi2']) && $_POST['Espandi2']=="Si") {
echo "<table>";
echo "<tr><td><u>Titolo</td><td><u>Autore</td><td><u>Genere</td><td><u>Paese</td><td><u>Celebre</td><td><u>Ascolta</u></td><td><u>Vota</u></td></tr>";
while ($row = mysqli_fetch_array($result)) {
    $idbrano2 = $row['IDBrano'];
    $nomeautore2=$row['Nome'];
    $titolo2=$row['Titolo'];
    $idautore2=$row['IDAutore'];
    $paese2=$row['Paese'];
    $genere2=$row['Genere'];
    $youtube="<a href='https://www.youtube.com/results?search_query=".$nomeautore2." ".$titolo2."'>Ascolta</a>";

echo "<tr><td>".$titolo2."</td><td>".$nomeautore2."</td><td>".$genere2."</td><td>".$paese2."</td><td>".$cel."</td><td>".$youtube."</td><td>
<form action='checkbox.php' method='post'>
<input type='hidden' name='Brano' value='".$idbrano."'/>
<input type='hidden' name='Esperto' value='".$esperto."'/>
<input type='hidden' name='BranoVoto' value='".$idbrano2."'/>
<input type='hidden' name='ChiaveU' value='".$utente."'/>
<input type='submit' value='Vota'>
</form></td><td></td>
</tr>";
}
echo "</table> <br>";

echo "<form action='similarity.php' method='post'>";
echo "<input type='hidden' name='Brano' value='".$idbrano."'/>";
echo "<input type='hidden' name='ChiaveU' value='".$utente."'/>";
echo "<input type='hidden' name='Esperto' value='".$esperto."'/>";
echo "<input type='submit' value='Chiudi'>";
echo "</form><br> <br>";
}
else {
echo "<table>";
$arrayautori=[];
$arraytabella=array();
echo "<tr><td><u>Titolo</td><td><u>Autore</td><td><u>Genere</td><td><u>Paese</td><td><u>Celebre</td><td><u>Ascolta</u></td><td><u>Vota</u></td><td><u>Similarità</u></td></tr>";
while ($row = mysqli_fetch_array($result)) {
    $nomeautore2=$row['Nome'];
    $titolo2=$row['Titolo'];
    $idautore2=$row['IDAutore'];
    $paese2=$row['Paese'];
    $idbrano2 = $row['IDBrano'];
    $idtagme2=$row['IdTagMe'];
    $genere2=$row['Genere'];
    $youtube="<a href='https://www.youtube.com/results?search_query=".$nomeautore2." ".$titolo2."'>Ascolta</a>";
    
    
if (!in_array($idbrano2, $arrayesclusi)) {
if (!in_array($nomeautore2, $arrayautori)) {
    array_push($arrayautori,$nomeautore2);
	$percentuale=correla($idtagme,$idtagme2);
	$stringa = "<tr><td>".$titolo2."</td><td>".$nomeautore2."</td><td>".$genere2."</td><td>".$paese2."</td><td>".$cel."</td><td>".$youtube."</td><td>
<form action='checkbox.php' method='post'>
<input type='hidden' name='Brano' value='".$idbrano."'/>
<input type='hidden' name='Esperto' value='".$esperto."'/>
<input type='hidden' name='BranoVoto' value='".$idbrano2."'/>
<input type='hidden' name='ChiaveU' value='".$utente."'/>
<input type='submit' value='Vota'>
</form></td><td>".$percentuale."%</td></tr>";
	$arraytabella[$stringa]=$percentuale;
}}}

arsort($arraytabella);
foreach ($arraytabella as $key => $value) {
    echo $key;
}
echo "</table>  <br>";

echo "<form action='similarity.php' method='post'>";
echo "<input type='hidden' name='Espandi2' value='Si' />";
echo "<input type='hidden' name='Brano' value='".$idbrano."'/>";
echo "<input type='hidden' name='ChiaveU' value='".$utente."'/>";
echo "<input type='hidden' name='Esperto' value='".$esperto."'/>";
echo "<input type='submit' value='Espandi2'>";
echo "</form> <br> <br>";
}}
//STESSO STRUMENTO

$i=0;
while(strpos(strtoupper($titolo), strtoupper($strumenti[$i])) == false && $i<count($strumenti)-1) {
    $i++;
    if (strpos(strtoupper($titolo), strtoupper($strumenti[$i])) !== false) $strumbr=$strumenti[$i];
}

if (isset($strumbr)) {
echo "<u>Brani che riportano lo stesso organico:</u><br><br>";
$sql = "SELECT * FROM `Brani`,`Autori` WHERE UPPER(Titolo) LIKE UPPER('%".$strumbr."%') AND Brani.IDAutore=Autori.IDAutore AND Celebre=".$celebre." AND NOT(Brani.IDAutore=".$idautore.")";
//echo $sql;
$result = mysqli_query($connessione,$sql);

if(isset($_POST['Espandi3']) && $_POST['Espandi3']=="Si") {

echo "<table>";

echo "<tr><td><u>Titolo</td><td><u>Autore</td><td><u>Genere</td><td><u>Paese</td><td><u>Celebre</td><td><u>Ascolta</u></td><td><u>Vota</u></td></tr>";
while ($row = mysqli_fetch_array($result)) {
    $nomeautore2=$row['Nome'];
    $titolo2=$row['Titolo'];
    $idautore2=$row['IDAutore'];
    $paese2=$row['Paese'];
    $idbrano2 = $row['IDBrano'];
    $genere2=$row['Genere'];
    $youtube="<a href='https://www.youtube.com/results?search_query=".$nomeautore2." ".$titolo2."'>Ascolta</a>";

echo "<tr><td>".$titolo2."</td><td>".$nomeautore2."</td><td>".$genere2."</td><td>".$paese2."</td><td>".$cel."</td><td>".$youtube."</td><td>
<form action='checkbox.php' method='post'>
<input type='hidden' name='Brano' value='".$idbrano."'/>
<input type='hidden' name='Esperto' value='".$esperto."'/>
<input type='hidden' name='BranoVoto' value='".$idbrano2."'/>
<input type='hidden' name='ChiaveU' value='".$utente."'/>
<input type='submit' value='Vota'>
</form></td><td></td></tr>";
}
echo "</table> <br> <br>";

echo "<form action='similarity.php' method='post'>";
echo "<input type='hidden' name='Brano' value='".$idbrano."'/>";
echo "<input type='hidden' name='Esperto' value='".$esperto."'/>";
echo "<input type='hidden' name='ChiaveU' value='".$utente."'/>";
echo "<input type='submit' value='Chiudi'>";
echo "</form><br> <br>";
}
else {
$arraytabella=array();
echo "<table>";

echo "<tr><td><u>Titolo</td><td><u>Autore</td><td><u>Genere</td><td><u>Paese</td><td><u>Celebre</td><td><u>Ascolta</u></td><td><u>Vota</u></td><td><u>Similarità</u></td></tr>";
$arrayautori=[];
while ($row = mysqli_fetch_array($result)) {
    $nomeautore2=$row['Nome'];
    $titolo2=$row['Titolo'];
    $idbrano2 = $row['IDBrano'];
    $idautore2=$row['IDAutore'];
    $paese2=$row['Paese'];
    $idtagme2=$row['IdTagMe'];
    $genere2=$row['Genere'];
    $youtube="<a href='https://www.youtube.com/results?search_query=".$nomeautore2." ".$titolo2."'>Ascolta</a>";

if (!in_array($idbrano2, $arrayesclusi)) {
if (!in_array($nomeautore2, $arrayautori)) {
    array_push($arrayautori,$nomeautore2);

	$percentuale=correla($idtagme,$idtagme2);
	$stringa = "<tr><td>".$titolo2."</td><td>".$nomeautore2."</td><td>".$genere2."</td><td>".$paese2."</td><td>".$cel."</td><td>".$youtube."</td><td>
<form action='checkbox.php' method='post'>
<input type='hidden' name='Brano' value='".$idbrano."'/>
<input type='hidden' name='Esperto' value='".$esperto."'/>
<input type='hidden' name='BranoVoto' value='".$idbrano2."'/>
<input type='hidden' name='ChiaveU' value='".$utente."'/>
<input type='submit' value='Vota'>
</form></td><td>".$percentuale."%</td></tr>";
	$arraytabella[$stringa]=$percentuale;
}}}
arsort($arraytabella);
foreach ($arraytabella as $key => $value) {
    echo $key;
}
echo "</table> <br>";
echo "<form action='similarity.php' method='post'>";
echo "<input type='hidden' name='Espandi3' value='Si' />";
echo "<input type='hidden' name='Brano' value='".$idbrano."'/>";
echo "<input type='hidden' name='ChiaveU' value='".$utente."'/>";
echo "<input type='hidden' name='Esperto' value='".$esperto."'/>";
echo "<input type='submit' value='Espandi3'>";
echo "</form> <br> <br>";

}//end else
} //end if

//SCELTI PER TE

$arrayautori=[];

$sql = "SELECT AVG(Voto) AS Voto FROM Utente WHERE IDUtente=".$utente;
$result = mysqli_query($connessione,$sql);
while ($row = mysqli_fetch_array($result)) {
    	$media = $row['Voto'];
} 

$sql = "SELECT * FROM `Utente` WHERE `IDUtente`=".$utente." AND `Voto`>".$media;
$result = mysqli_query($connessione,$sql);
$arrayinclusi=[];
while ($row = mysqli_fetch_array($result)) {
		//arrayinclusi contiene gli id dei brani
    		array_push($arrayinclusi,$row['IDBrano']);
		//echo $row['IDBrano']." ";
}
echo "<h3>Scelti per te:</h3>";
echo "La tua media voti: ".$media."<br>";
echo "<table>";
echo "<tr><td><u>Titolo</td><td><u>Autore</td><td><u>Genere</td><td><u>Paese</td><td><u>Ascolta</u></td><td><u>Vota</u></td><td>Compatibilità</td></tr>";
foreach ($arrayinclusi as $valbrano){
	//echo $valbrano;
	$sql = "SELECT IDAutore FROM Brani WHERE `IDBrano`=".$valbrano;
	$result = mysqli_query($connessione,$sql);
	while ($row = mysqli_fetch_array($result)) {
    		$idautore=$row['IDAutore'];
		//echo $idautore." ";
	}
	$sql = "SELECT * FROM Brani,Autori WHERE Brani.IDAutore=Autori.IDAutore AND Brani.IDAutore=".$idautore." AND Brani.IDBrano!=".$valbrano;
	$result = mysqli_query($connessione,$sql);
	while ($row = mysqli_fetch_array($result)) {
			$nomeautore=$row['Nome'];
    			$titolo=$row['Titolo'];
    			$paese=$row['Paese'];
    			$genere=$row['Genere'];
    			$youtube="<a href='https://www.youtube.com/results?search_query=".$nomeautore." ".$titolo."'>Ascolta</a>";
		if (!in_array($nomeautore, $arrayautori)) {
		$sql = "SELECT AVG(Voto) AS Voto FROM `Utente`,`Brani` WHERE Utente.IDBrano=Brani.IDBrano AND `IDUtente`=".$utente." AND `IDAutore`=".$idautore;
		//echo $sql;
		$result = mysqli_query($connessione,$sql);
	while ($row = mysqli_fetch_array($result)) {$voto=$row['Voto'];}
		array_push($arrayautori,$nomeautore);
		$compatibilita=20*$voto;
	echo "<tr><td>".$titolo."</td><td>".$nomeautore."</td><td>".$genere."</td><td>".$paese."</td><td>".$youtube."</td><td>
<form action='checkbox.php' method='post'>
<input type='hidden' name='Brano' value='".$idbrano."'/>
<input type='hidden' name='Esperto' value='".$esperto."'/>
<input type='hidden' name='BranoVoto' value='".$idbrano2."'/>
<input type='hidden' name='ChiaveU' value='".$utente."'/>
<input type='submit' value='Vota'>
</form></td><td> ".$compatibilita."%</td></tr>";
	
		}
	}
}


?>


</body>
</html>
