<?php
	require("functions.php");
	$notice = "";
	
	//kui pole sisseloginud, siis sisselogimise lehele
	if(!isset($_SESSION["userId"])){
		header("Location: login.php");
		exit();
	}
	
	//kui logib v�lja
	if (isset($_GET["logout"])){
		//l�petame sessiooni
		session_destroy();
		header("Location: login.php");
	}
	
	//Algab foto laadimise osa
	$target_dir = "../../pics/";
	$target_file;
	$uploadOk = 1;
	$imageFileType;
	$maxWidth = 600;
	$maxHeight = 400;
	$marginBottom = 10;
	$marginRight = 10;
	
	//Kas on pildi failit��p
	if(isset($_POST["submit"])) {
		
		//kas mingi fail valiti
		if(!empty($_FILES["fileToUpload"]["name"])){
			
			$imageFileType = strtolower(pathinfo(basename($_FILES["fileToUpload"]["name"]))["extension"]);
			//$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			//tekitame failinime koos ajatempliga
			$target_file = $target_dir .pathinfo(basename($_FILES["fileToUpload"]["name"]))["filename"] ."_" .(microtime(1) * 10000) ."." .$imageFileType;
			//$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
				$notice .= "Fail on pilt - " . $check["mime"] . ". ";
				$uploadOk = 1;
			} else {
				$notice .= "See pole pildifail. ";
				$uploadOk = 0;
			}
			
			//Kas selline pilt on juba �les laetud
			if (file_exists($target_file)) {
				$notice .= "Kahjuks on selle nimega pilt juba olemas. ";
				$uploadOk = 0;
			}
			
			//Piirame faili suuruse
			if ($_FILES["fileToUpload"]["size"] > 1000000) {
				$notice .= "Pilt on liiga suur! ";
				$uploadOk = 0;
			}
			
			//Piirame failit��pe
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
				$notice .= "Vabandust, vaid jpg, jpeg, png ja gif failid on lubatud! ";
				$uploadOk = 0;
			}
			
			//Kas saab laadida?
			/*if ($uploadOk == 0) {
				$notice .= "Vabandust, pilti ei laetud �les! ";
			//Kui saab �les laadida
			} else {		
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
					$notice .= "Fail ". basename( $_FILES["fileToUpload"]["name"]). " laeti �les! ";
				} else {
					$notice .= "Vabandust, �leslaadimisel tekkis t�rge! ";
				}
			}*/
			if ($uploadOk == 0) {
				$notice .= "Vabandust, pilti ei laetud �les! ";
			//Kui saab �les laadida
			} else {
				
				//loeme EXIF infot, millal pilt tehti
				@$exif = exif_read_data($_FILES["fileToUpload"]["tmp_name"], "ANY_TAG", 0, true);
				//var_dump($exif);
				if(!empty($exif["DateTimeOriginal"])){
					$textToImage = "Pilt tehti: " .$exif["DateTimeOriginal"];
				} else {
					$textToImage = "Pildistamise aeg teadmata!";
				}
				
				//l�htudes failit��bist, loon sobiva pildiobjekti
				if($imageFileType == "jpg" or $imageFileType == "jpeg"){
					$myTempImage = imagecreatefromjpeg($_FILES["fileToUpload"]["tmp_name"]);
				}
				if($imageFileType == "png"){
					$myTempImage = imagecreatefrompng($_FILES["fileToUpload"]["tmp_name"]);
				}
				if($imageFileType == "gif"){
					$myTempImage = imagecreatefromgif($_FILES["fileToUpload"]["tmp_name"]);
				}
				
				//suuruse muutmine
				//k�sime originaalsuurust
				$imageWidth = imagesx($myTempImage);
				$imageHeight = imagesy($myTempImage);
				$sizeRatio = 1;
				if($imageWidth > $imageHeight){
					$sizeRatio = $imageWidth / $maxWidth;
				} else {
					$sizeRatio = $imageHeight / $maxHeight;
				}
				$myImage = resize_image($myTempImage, $imageWidth, $imageHeight, round($imageWidth / $sizeRatio), round($imageHeight / $sizeRatio));
				
				//vesim�rgi lisamine
				$stamp = imagecreatefrompng("../../graphics/hmv_logo.png");
				$stampWidth = imagesx($stamp);
				$stampHeight = imagesy($stamp);
				$stampPosX = round($imageWidth / $sizeRatio) - $stampWidth - $marginRight;
				$stampPosY = round($imageHeight / $sizeRatio) - $stampHeight - $marginBottom;
				imageCopy($myImage, $stamp, $stampPosX, $stampPosY, 0, 0, $stampWidth, $stampHeight);
				
				//lisame ka teksti vesim�rgina
				//imagecolorallocate
				$textColor = imagecolorallocatealpha($myImage, 150, 150, 150, 50);
				//RGBA alpha 0 -127
				imagettftext($myImage, 20, 0, 10, 25, $textColor, "../../graphics/ARIAL.TTF", $textToImage);
				
				//salvestame pildi
				if($imageFileType == "jpg" or $imageFileType == "jpeg"){
					if(imagejpeg($myImage, $target_file, 95)){
						$notice = "Fail: " . basename( $_FILES["fileToUpload"]["name"]). " laeti �les! ";
					} else {
						$notice .= "Vabandust, �leslaadimisel tekkis t�rge! ";
					}
				}
				if($imageFileType == "png"){
					if(imagepng($myImage, $target_file, 95)){
						$notice = "Fail: " . basename( $_FILES["fileToUpload"]["name"]). " laeti �les! ";
					} else {
						$notice .= "Vabandust, �leslaadimisel tekkis t�rge! ";
					}
				}
				if($imageFileType == "gif"){
					if(imagegif($myImage, $target_file, 95)){
						$notice = "Fail: " . basename( $_FILES["fileToUpload"]["name"]). " laeti �les! ";
					} else {
						$notice .= "Vabandust, �leslaadimisel tekkis t�rge! ";
					}
				}
				
				//m�lu vabastamine
				imagedestroy($myImage);
				imagedestroy($myTempImage);
				
			}
		
		} else {
			$notice = "Palun valige k�igepealt pildifail!";
		} //kas �ldse m�ni fail valiti, l�ppeb
	}//kas vajutati submit nuppu, l�ppeb
	function resize_image($image, $origW, $origH, $w, $h){
		$dst = imagecreatetruecolor($w, $h);
		imagecopyresampled($dst, $image, 0, 0, 0, 0, $w, $h, $origW, $origH);
		return $dst;
	}
	require("header.php");
?>


	<h1>Daisy</h1>
	<p>See veebileht on loodud veebiprogrammeerimise kursusel ning ei sisalda mingisugust t�siseltv�etavat sisu.</p>
	<p><a href="?logout=1">Logi v�lja</a>!</p>
	<p><a href="main.php">Pealeht</a></p>
	<hr>
	<h2>Foto �leslaadimine</h2>
	<form action="photoupload.php" method="post" enctype="multipart/form-data">
		<label>Valige pildifail:</label>
		<input type="file" name="fileToUpload" id="fileToUpload">
		<input type="submit" value="Lae �les" name="submit">
	</form>
	
	<span><?php echo $notice; ?></span>
<?php
	require("footer.php");
?>