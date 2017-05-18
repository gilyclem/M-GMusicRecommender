<style>@import url(style.css);</style>
<html>
<head> <title> M&G Music Rec </title> </head>
<!--<body background="sfondo1.jpg" style="background-repeat: no-repeat">-->
<body bgcolor="#ffffcc">
<?php

//Raccolta dei dati immessi dall'utente

ini_set('display_errors','On');
error_reporting(E_ALL);

$host = "localhost";
$username = "root";
$password = "admin";
$database = "MusicDS";
$utente=$_POST["ChiaveU"];
$idbrano=$_POST["Brano"];
$idbranovoto=$_POST["BranoVoto"];
$esperto=$_POST["Esperto"];


$connessione = mysqli_connect ($host,$username,$password) or die ("Connessione impossibile:::::".mysqli_error());
mysqli_select_db ($connessione,$database) or die ("Database non trovato::::".mysqli_error());

if(isset($_POST['Vota']) && $_POST['Vota']=="Si") {
	$voto=$_POST["Voto"];
	$query = "INSERT INTO Utente (IDUtente,IDBrano,Voto) VALUES ('".$utente."','".$idbranovoto."','".$voto."')";
        $res = mysqli_query ($connessione,$query);
	if (!$res) {
	$query = "UPDATE `Utente` SET `Voto`=".$voto." WHERE `IDUtente`=".$utente." AND `IDBrano`=".$idbranovoto;
        $res = mysqli_query ($connessione,$query)  or die ("Error::::".mysqli_error());
	echo "La tua preferenza è stata aggiornata. <br>";
	}
	echo "Grazie per aver votato!";

echo "<form action='similarity.php' method='post'>";
echo "<input type='hidden' name='Brano' value='".$idbrano."'/>";

echo "<input type='hidden' name='Esperto' value='".$esperto."'/>";
echo "<input type='hidden' name='Vota' value='Si'/>";
echo "<input type='hidden' name='ChiaveU' value='".$utente."'/>";
?>
<br>
<input type='submit' value='Back'>
</form>
<?php
	}

else {
$idbrano2=$_POST["BranoVoto"];


echo "Utente: ".$utente."<br>";

echo "<h3> Stai votando: </h3><br>"; 

$sql = "SELECT * FROM `Brani`,`Autori` WHERE IDBrano='".$idbrano2."' AND Brani.IDAutore=Autori.IDAutore";
$result = mysqli_query($connessione,$sql);

while ($row = mysqli_fetch_array($result)) {
    $nomeautore=$row['Nome'];
    $titolo=$row['Titolo'];
    $idautore=$row['IDAutore'];
    $paese=$row['Paese'];
    $genere=$row['Genere'];
    echo "Di <b>". $nomeautore."</b><br>";
    echo "la composizione <b>".$titolo."</b><br>";
    echo "Genere: <b>".$genere."</b><br>";
    echo "Paese di origine dell'autore: <b>".$paese."</b><br>"; 

}

?>
<br><br>

<form action='checkbox.php' method='post'>
<span class="rating">
	  <input id="rating5" type="radio" name="Voto" value="5">
	  <label for="rating5">5</label>
	  <input id="rating4" type="radio" name="Voto" value="4">
	  <label for="rating4">4</label>
	  <input id="rating3" type="radio" name="Voto" value="3">
	  <label for="rating3">3</label>
	  <input id="rating2" type="radio" name="Voto" value="2">
	  <label for="rating2">2</label>
	  <input id="rating1" type="radio" name="Voto" value="1">
	  <label for="rating1">1</label>
	</span> <br><br>
<?php
echo "<input type='hidden' name='Brano' value='".$idbrano."'/>";
echo "<input type='hidden' name='Esperto' value='".$esperto."'/>";
echo "<input type='hidden' name='Vota' value='Si'/>";
echo "<input type='hidden' name='BranoVoto' value='".$idbranovoto."'/>";
echo "<input type='hidden' name='ChiaveU' value='".$utente."'/>";
?>
<input type='submit' value='Vota'>
</form>
<?php
} ?>
</body>
</html>

