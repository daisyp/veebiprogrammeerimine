<?php
	require("functions.php");
	require("../../config.php");
	$database = "if17_pukkdais";
	
	//kui pole sisseloginud, siis sisselogimise lehele
	if(!isset($_SESSION["userId"])){
		header("Location: login.php");
		exit();
	}
	
	//kui logib välja
	if (isset($_GET["logout"])){
		//lõpetame sessiooni
		session_destroy();
		header("Location: login.php");
	}
	
	/*
		while($stmt->fetch()) {
			
		}
	*/
	
	require("header.php");
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>
		Daisy veebiproge
	</title>
</head>
<body>
	<h1><?php  echo "Tervist, " .$_SESSION["userFirstName"] ." " .$_SESSION["userLastName"] .".";?></h1>
	<p>See veebileht on loodud veebiprogrammeerimise kursusel ning ei sisalda mingisugust tõsiseltvõetavat sisu.</p>
	<p><a href="?logout=1">Logi välja</a>!</p>
	<p><a href="main.php">Pealeht</a></p>
	<p>
	<?php
		$gender = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"],$GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT id, email, firstname, lastname, gender, birthday FROM veebiproge");
		$stmt->bind_result($idDb, $emailFromDb, $firstNameDb, $lastNameDb, $genderDb, $birthdayDb);
		$stmt->execute();	
		echo "<table border='1' cellpadding='5px' cellspacing='2px'>";
		echo "<tr><th>ID</th>";
		echo "<th>Email</th>";
		echo "<th>Eesnimi</th>";
		echo "<th>Perekonnanimi</th>";
		echo "<th>Sugu</th>";
		echo "<th>Sünnikuupäev</th></tr>";
		while($stmt->fetch()){ // while tsükkel, mida täidetakse kuni veel mõni kasutaja tuleb
			echo "<tr><td>" .$idDb ."</td>";
			echo "<td>" .$emailFromDb ."</td>";
			echo "<td>" .$firstNameDb ."</td>";
			echo "<td>" .$lastNameDb ."</td>";
			echo "<td>" .genderText($genderDb)."</td>";
			echo "<td>" .$birthdayDb ."</td>";
			echo "</tr>";
		}
		echo "</table>";
		$mysqli->close();
	?>
	</p>
	
<?php
	require("footer.php");
?>
	
</body>
</html>