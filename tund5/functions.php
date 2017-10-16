<?php
	require("../../config.php");
	$database = "if17_pukkdais";
	
	//alustan sessiooni
	session_start();
	
	//sisselogimise funktsioon
	function signIn($email, $password){
		$notice = "";
		//ühendus serveriga
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT id, email, password, firstname, lastname FROM veebiproge WHERE email = ?");
		$stmt->bind_param("s", $email);
		$stmt->bind_result($id, $emailFromDb, $passwordFromDb, $firstNameDb, $lastNameDb);
		$stmt->execute();
		
		//kontrollime vastavust
		if ($stmt->fetch()){
			$hash = hash("sha512", $password);
			if ($hash == $passwordFromDb){
				$notice = "Logisite sisse!";
				
				//Määran sessiooni muutujad
				$_SESSION["userId"] = $id;
				$_SESSION["userEmail"] = $emailFromDb;
				$_SESSION["userFirstName"] = $firstNameDb;
				$_SESSION["userLastName"] = $lastNameDb;
				
				//liigume edasi pealehele (main.php)
				header("Location: main.php");
				exit();
			} else {
				$notice = "Vale salasõna!";
			}
		} else {
			$notice = 'Sellise kasutajatunnusega "' .$email .'" pole registreeritud!';
		}
		return $notice;
	}
	
	//kasutaja salvestamise funktsioon
	function signUp($signupFirstName, $signupFamilyName, $signupBirthDate, $gender, $signupEmail, $signupPassword){
		//loome andmebaasiühenduse
		
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		//valmistame ette käsu andmebaasiserverile
		$stmt = $mysqli->prepare("INSERT INTO veebiproge (firstname, lastname, birthday, gender, email, password) VALUES (?, ?, ?, ?, ?, ?)");
		echo $mysqli->error;
		//s - string
		//i - integer
		//d - decimal
		$stmt->bind_param("sssiss", $signupFirstName, $signupFamilyName, $signupBirthDate, $gender, $signupEmail, $signupPassword);
		// sssiss tähendab seda et iga parameetri kohta peab käima mis tüüpi ta on, string integer jnejne 
		//$stmt->execute();
		if ($stmt->execute()){
			echo "\n Õnnestus!";
		} else {
			echo "\n Tekkis viga : " .$stmt->error;
		}
		$stmt->close();
		$mysqli->close();
	}
	
	//mõtete salvestamine
	function saveIdea($idea, $color) {
		// echo $color; näitab ekraanil mis värvi kasutaja valis
		$notice = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("INSERT INTO vpuserideas (userid, idea, ideacolor) VALUES (?, ?, ?)");
		echo $mysqli->error;
		$stmt->bind_param("iss", $_SESSION["userId"], $idea, $color);
		if($stmt->execute()) {
			$notice = "mõte on salvestatud";
		} else {
			$notice = "mõtte salvestamisel tekkis viga " .$stmt->error;
		}
		$stmt->close();
		$mysqli->close();
		return $notice;
	}
	
	//sisestuse kontrollimise funktsioon
	function test_input($data){
		$data = trim($data);//ebavajalikud tühiku jms eemaldada
		$data = stripslashes($data);//kaldkriipsud jms eemaldada
		$data = htmlspecialchars($data);//keelatud sümbolid
		return $data;
	}
	
	function genderText($genderDb) {
		if ($genderDb == 1) {
				$gender = "Male";
				return $gender;
			} 	elseif ($genderDb == 2) {
				$gender = "Female";
				return $gender;
			}
	}
	
	//kõikide ideede lugemisefunktsioon
	/* function readAllIdeas() {
		$ideasHTML = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT idea, ideaColor FROM vpuserideas WHERE userid = ? ORDER BY id DESC");
		$stmt->bind_param("i", $_SESSION["userId"]);
		$stmt->bind_result($idea, $color);
		$stmt->execute();
		// $result = array();
		while ($stmt->fetch()) {
			$ideasHTML .= '<p style="background-color:' .$color .'">' .$idea ."<p/> \n";
		}
		$stmt->close();
		$mysqli->close();
		return $ideasHTML;
		
	} */
	
	function readAllIdeas() {
		$tableheads ="";
		$notice="";
		$table="";
		//ühendus serveriga
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"],$GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT vpuserideas.idea, vpuserideas.ideacolor, vpuserideas.userid, veebiproge.firstname, veebiproge.lastname FROM vpuserideas INNER JOIN veebiproge on vpuserideas.userid = veebiproge.id ORDER BY vpuserideas.id DESC;");
		$stmt->bind_result($ideaDb, $colorDb, $userID, $firstNameDb, $lastNameDb);
		$stmt->execute();
		while($stmt->fetch()){ 
			$notice .= '<p style="background-color:'.$colorDb .'">'. $ideaDb .'<i> -' .$firstNameDb .' ' .$lastNameDb. "</i></p> \n";
		}
		$stmt->close();
		$mysqli->close();
		return $notice;
	}
	
	// uusima idee lugemine
	function latestIdea() {
		// $ideaHTML = "";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT idea FROM vpuserideas WHERE id = (SELECT MAX(id) FROM vpuserideas)");
		$stmt->bind_result($ideaDb);
		$stmt->execute();
		$stmt->fetch(); // kui fetchi ei tee siis andmeid ei saa
		$stmt->close();
		$mysqli->close();
		return $ideaDb;
	}
	
	
	/*
	$x = 5;
	$y = 6;
	echo ($x + $y);
	addValues();
	
	function addValues(){
		$z = $GLOBALS["x"] + $GLOBALS["y"];
		echo "Summa on: " .$z;
		$a = 3;
		$b = 4;
		echo "Teine summa on: " .($a + $b);
	}
	echo "Kolmas summa on: " .($a + $b);
	*/
	
?>