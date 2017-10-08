<?php
    require("../../config.php");
    // echo $serverHost;
	
	$signupFirstName = "";
	$signupFamilyName = "";
	$signupEmail = "";
	$gender = "";
	$signupBirthDay = null;
	$signupBirthMonth = null;
	$signupBirthYear = null;
    $signupBirthDate = null;

    $signupFirstNameError = "";
	$signupFamilyNameError = "";
	$signupBirthDayError = "";
	$signupBirthMonthError = "";
	$signupBirthYearError = "";
	$signupGenderError = "";
	$signupEmailError = "";
	$signupPasswordError = "";
	
	$loginEmail = "";
	
	//kas on kasutajanimi sisestatud
	if (isset ($_POST["loginEmail"])){
		if (empty ($_POST["loginEmail"])){
			$loginEmailError ="NB! Ilma selleta ei saa sisse logida!";
		} else {
			$loginEmail = $_POST["loginEmail"];
		}
	}
	
	//kontrollime, kas kirjutati eesnimi
	if (isset ($_POST["signupFirstName"])){
		if (empty ($_POST["signupFirstName"])){
			$signupFirstNameError ="NB! Väli on kohustuslik!";
		} else {
			$signupFirstName = $_POST["signupFirstName"];
		}
	}
	
	//kontrollime, kas kirjutati perekonnanimi
	if (isset ($_POST["signupFamilyName"])){
		if (empty ($_POST["signupFamilyName"])){
			$signupFamilyNameError ="NB! Väli on kohustuslik!";
		} else {
			$signupFamilyName = $_POST["signupFamilyName"];
		}
	}

	//kas on kasutajanimi sisestatud
	if (isset ($_POST["loginEmail"])){
		if (empty ($_POST["loginEmail"])){
			$loginEmailError ="NB! Ilma selleta ei saa sisse logida!";
		} else {
			$loginEmail = $_POST["loginEmail"];
		}
	}
	
	//kontrollime, kas kirjutati eesnimi
	if (isset ($_POST["signupFirstName"])){
		if (empty($_POST["signupFirstName"])){
			$signupFirstNameError ="NB! Väli on kohustuslik!";
		} else {
			$signupFirstName = $_POST["signupFirstName"];
		}
	}
	
	//kontrollime, kas kirjutati perekonnanimi
	if (isset ($_POST["signupFamilyName"])){
		if (empty($_POST["signupFamilyName"])){
			$signupFamilyNameError ="NB! Väli on kohustuslik!";
		} else {
			$signupFamilyName = $_POST["signupFamilyName"];
		}
	}

    //kas kuup2ev on sisestatud
    if (isset ($_POST["signupBirthDay"])){
		$signupBirthDay = $_POST["signupBirthDay"];
		//echo $signupBirthDay;
	}

	//kas synnikuu on sisestatud
    if(isset($_POST["signupBirthMonth"])){
        $signupBirthMotnh = intval($_POST["signupBirthMonth"]);
        //echo $signupBirthYear;
    }

    //kas synniaasta on sisestatud
    if (isset ($_POST["signupBirthYear"])){
		$signupBirthYear = $_POST["signupBirthYear"];
		//echo $signupBirthYear;
	}


    //kui synnikuup2ev on sisestatud siis kontrollime kas kehtiv
    if(isset ($_POST["signupBirthDay"]) and isset($_POST["signupBirthMonth"]) and isset($_POST["signupBirthYear"])){
        if(checkdate(intval($_POST["signupBirthMonth"]),intval($_POST["signupBirthDay"]), intval($_POST["signupBirthYear"]))) {
            $birthDate = date_create($_POST["signupBirthMonth"] ."/" .$_POST["signupBirthDay"] . "/" .$_POST["signupBirthYear"]);
            $signupBirthDate = date_format($birthDate, "Y-m-d"); 
        } else {
            $signupBirthDayError = "viga sünnikuupäeva sisestamisel";
        }
    }

    if (isset ($_POST["signupBirthDay"]) and isset ($_POST["signupBirthMonth"]) and isset ($_POST["signupBirthYear"])){
		if(checkdate (intval($_POST["signupBirthMonth"]), intval($_POST["signupBirthDay"]) , intval($_POST["signupBirthYear"]) )){
			$test = date_create($_POST["signupBirthMonth"] ."/" .$_POST["signupBirthDay"] ."/" .$_POST["signupBirthYear"]);
			//var_dump($test);
			//echo date_format($test, "Y-m-d"); //sellise stringi saadame andmebaasi
			$signupBirthDate = date_format($test, "Y-m-d");
		}
		
	}


	//kontrollime, kas kirjutati kasutajanimeks email
	if (isset ($_POST["signupEmail"])){
		if (empty ($_POST["signupEmail"])){
			//$signupEmailError ="NB! Väli on kohustuslik!";
		} else {
			$signupEmail = $_POST["signupEmail"];
		}
	}
	
	if (isset ($_POST["signupPassword"])){
		if (empty ($_POST["signupPassword"])){
			$signupPasswordError = "NB! Väli on kohustuslik!";
		} else {
			//polnud tühi
			if (strlen($_POST["signupPassword"]) < 8){
				$signupPasswordError = "NB! Liiga lühike salasõna, vaja vähemalt 8 tähemärki!";
			}
		}
	}
	
	if (isset($_POST["gender"]) && !empty($_POST["gender"])){ //kui on määratud ja pole tühi
			$gender = intval($_POST["gender"]);
		} else {
			$signupGenderError = " (Palun vali sobiv!) Määramata!";
	}

    //UUE KASUTAJA ANDMEBAASI KIRJUTAMINE, kui kõik on olemas	
	if (empty($signupFirstNameError) and empty($signupFamilyNameError) and empty($signupBirthDayError) and empty($signupGenderError) and empty($signupEmailError) and empty($signupPasswordError)){
		echo "Hakkan salvestama!";
		//krüpteerin parooli
		$signupPassword = hash("sha512", $_POST["signupPassword"]);
		//echo "\n Parooli " .$_POST["signupPassword"] ." räsi on: " .$signupPassword;
		//loome andmebaasiühenduse
		$database = "if17_pukkdais";
		$mysqli = new mysqli($serverHost, $serverUsername, $serverPassword, $database);
		//valmistame ette käsu andmebaasiserverile
		$stmt = $mysqli->prepare("INSERT INTO veebiproge (firstname, lastname, birthday, gender, email, password) VALUES (?, ?, ?, ?, ?, ?)");
		echo $mysqli->error;
		//s - string
		//i - integer
		//d - decimal
		$stmt->bind_param("sssiss", $signupFirstName, $signupFamilyName, $signupBirthDate, $gender, $signupEmail, $signupPassword);
		//$stmt->execute();
		if ($stmt->execute()){
			echo "\n Õnnestus!";
		} else {
			echo "\n Tekkis viga : " .$stmt->error;
		}
		$stmt->close();
		$mysqli->close();
	}
        
    //loome kuupäeva valiku
	$signupDaySelectHTML = "";
	$signupDaySelectHTML .= '<select name="signupBirthDay"> \n';
	$signupDaySelectHTML .= '<option value="" selected disabled>päev</option> \n';
	for ($i = 1; $i < 32; $i ++){
		if($i == $signupBirthDay){
			$signupDaySelectHTML .= '<option value="' .$i .'" selected>' .$i .'</option> \n';
		} else {
			$signupDaySelectHTML .= '<option value="' .$i .'">' .$i .'</option> \n';
		}
		
	}
	$signupDaySelectHTML.= "</select> \n";

    // tekitame sünnikuu valiku 
    $signupMonthSelectHTML = "";
    $monthNamesEt = ["jaanuar", "veebruar", "märts", "aprill", "mai", "juuni", "juuli", "august", "september", "oktoober", "november", "detsember"];
    $signupMonthSelectHTML .= '<select name="signupBirthMonth">'. "\n";
    $signupMonthSelectHTML .= '<option value="" selected disabled>Vali sünnikuu</option>' ."\n";
    // teeme tsükli kus me keymonthiga saame kätte mitu korda seda tsüklit läbi $key + 1 kuna see algab 0 aga jaanuar kuup2evaliselt on 1
    foreach ($monthNamesEt as $key=>$month) {
        if($key + 1 === $signupBirthMonth) {
            $signupMonthSelectHTML .= '<option value="' .($key + 1) .'" selected>'
            .$month .'</option>' . "\n";
        } else {
        $signupMonthSelectHTML .= '<option value="' .($key + 1) .'">'
            .$month .'</option>' . "\n";
        }
    }
    $signupMonthSelectHTML .= "</select> \n";

    //loome aasta valiku
	$signupYearSelectHTML = "";
	$signupYearSelectHTML .= '<select name="signupBirthYear"> \n';
	$signupYearSelectHTML .= '<option value="" selected disabled>aasta</option> \n';
	$yearNow = date("Y");
	for ($i = $yearNow; $i > 1900; $i --){
		if($i == $signupBirthYear){
			$signupYearSelectHTML .= '<option value="' .$i .'" selected>' .$i .'</option> \n';
		} else {
			$signupYearSelectHTML .= '<option value="' .$i .'">' .$i .'</option> \n';
		}
		
	}
	$signupYearSelectHTML.= "</select> \n";

?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Sisselogimine või uue kasutaja loomine</title>
</head>
<body>
	<h1>Logi sisse!</h1>
	<p>Siin harjutame sisselogimise funktsionaalsust.</p>
	
	<form method="POST">
		<label>Kasutajanimi (E-post): </label>
		<input name="loginEmail" type="email" value="<?php echo $loginEmail; ?>">
		<br><br>
		<input name="loginPassword" placeholder="Salasõna" type="password">
		<br><br>
		<input type="submit" value="Logi sisse">
	</form>
	
	<h1>Loo kasutaja</h1>
	<p>Kui pole veel kasutajat....</p>
	
	<form method="POST">
		<label>Eesnimi </label>
		<input name="signupFirstName" type="text" value="<?php echo $signupFirstName; ?>"><span><?php echo $signupFirstNameError; ?></span>
		<br>
		<label>Perekonnanimi </label>
		<input name="signupFamilyName" type="text" value="<?php echo $signupFamilyName; ?>"><span><?php echo $signupFamilyNameError; ?></span>
        <br>
        <label>Sisesta oma sünnikuupäev</label>
        <?php 
            echo "\n <br> \n" .$signupDaySelectHTML ."\n" .$signupMonthSelectHTML ."\n" .$signupYearSelectHTML ."\n <br> \n";
        ?>
        <span><?php echo $signupBirthDayError; ?></span>
		<br><br>
		<label>Sugu</label>
		<br>
		<input type="radio" name="gender" value="1" <?php if ($gender == '1') {echo 'checked';} ?>><label>Mees</label> <!-- Kõik läbi POST'i on string!!! -->
		<input type="radio" name="gender" value="2" <?php if ($gender == '2') {echo 'checked';} ?>><label>Naine</label>
		<br><br>
		
		<label>Kasutajanimi (E-post)</label>
		<input name="signupEmail" type="email" value="<?php echo $signupEmail; ?>"><span><?php echo $signupEmailError; ?></span>
		<br><br>
		<input name="signupPassword" placeholder="Salasõna" type="password"><span><?php echo $signupPasswordError; ?></span>
		<br><br>

		
		<input type="submit" value="Loo kasutaja">
    </form>
		
</body>
</html>