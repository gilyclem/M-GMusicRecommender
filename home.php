<html>
<head> <title> M&G Music Rec </title> </head>
<!--<body background="sfondo1.jpg" style="background-repeat: no-repeat">-->
<body bgcolor="#ffffcc">
<h1> Benvenuto in M&G Music Recommendation! </h1>
<h2> Seleziona un brano che ti piace per iniziare la raccomandazione...</h2>
<?php 
ini_set('display_errors','On');
error_reporting(E_ALL);

$host = "localhost";
$username = "root";
$password = "admin";
$database = "MusicDS";

$connessione = mysqli_connect ($host,$username,$password) or die ("Connessione impossibile:::::".mysqli_error());
mysqli_select_db ($connessione,$database) or die ("Database non trovato::::".mysqli_error());
?>

<!-- FORM GENERE -->
<form action="home.php" method="post">

<h3> Genere: </h3>

<?php
$sql = "SELECT DISTINCT Genere FROM `Autori`";
$result = mysqli_query($connessione,$sql);

echo "<select name='Genere'>";
if (isset($_POST["Genere"]) && $_POST["Genere"]!=NULL){
echo "<option name='Genere' value='".$_POST["Genere"]."' selected='selected'>".$_POST["Genere"]."</option>";
}
else{
echo "<option value='' selected='selected'></option>";}
while ($row = mysqli_fetch_array($result)) {
    echo "<option name='Genere' value='" . $row['Genere'] . "'>" . $row['Genere'] . "</option>";
}
echo "</select>";

?>
  <input type="submit" value="Invio">
</form> 

<!--FORM AUTORE -->
<h3> Autore: </h3>
<form action="home.php" method="post">
<?php


echo "<select name='Autore'>";
if (isset($_POST["Autore"]) && $_POST["Autore"]!=NULL){
$sql = "SELECT Nome FROM `Autori` WHERE IDAutore=".$_POST["Autore"];
$result = mysqli_query($connessione,$sql);
while ($row = mysqli_fetch_array($result)) {
	echo "<option value='".$row['Nome']."' selected='selected'>".$row['Nome']."</option>";
}}
else{
echo "<option value='' selected='selected'></option>";}

if (isset($_POST["Genere"]) && $_POST["Genere"]!=NULL){
$sql = "SELECT * FROM `Autori` WHERE Genere='".$_POST["Genere"]."'";
$result = mysqli_query($connessione,$sql);

while ($row = mysqli_fetch_array($result)) {
    echo "<option value='" . $row['IDAutore'] . "'>" . $row['Nome'] . "</option>";
}
echo "</select>";
echo "<input type='hidden' name='Genere' value='".$_POST["Genere"]."'/>";
}

?>
  <input type="submit" value="Invio">
</form> 

<!--FORM BRANO -->
<h3> Brano: </h3>
 <form action="similarity.php" method="post">
<?php
echo "<select name='Brano'>";
echo "<option value='' selected='selected'></option>";
if (isset($_POST["Autore"]) && $_POST["Autore"]!=NULL){
$sql = "SELECT * FROM `Brani` WHERE IDAutore='".$_POST["Autore"]."'";
$result = mysqli_query($connessione,$sql);
while ($row = mysqli_fetch_array($result)) {
    echo "<option value='" . $row['IDBrano'] . "'>" . $row['Titolo'] . "</option>";
}
}
echo "</select>";

?>
<br><br>
<h3> Info Utente: </h3>
<legend>Scrivi la tua chiave utente:</legend><br>
<input type="text" name="ChiaveU"><br><br>
<fieldset>
 <legend>Quale ti descrive meglio:</legend>
 <input type="radio" name="Esperto" value="si"/> "Sono un esperto di musica classica."
 <br /> 
 <input type="radio" name="Esperto" value="no"/> "Mi piace ascoltare la musica classica ma non ho competenze tecniche nel settore."
 <br />
</fieldset>
<br> 
  <input type="submit" value="Invio">
</form>

</body> 
</html>

