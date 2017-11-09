<?php
	require("../../config.php");
	$database = "if17_pukkdais";
	
	//ühe konkreetse mõtte lugemine
	function getSingleIdeaData($edit_id){
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("SELECT idea, ideacolor FROM vpuserideas WHERE id=?");
		$stmt->bind_param("i", $edit_id);
		$stmt->bind_result($ideaText, $ideaColor);
		$stmt->execute();
		//loon objekti ehk klassi
		$ideaObject = new Stdclass();
		if($stmt->fetch()){
			$ideaObject->text = $ideaText;
			$ideaObject->color = $ideaColor;
		} else {
				//kui sellist ideed pole, või on kustutatud
				//header("Location: usersideas.php");
				//exit();
		}
		
		$stmt->close();
		$mysqli->close();
		
		return $ideaObject;
	}
	
	function updateIdea($id, $idea, $color){
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		$stmt = $mysqli->prepare("UPDATE vpuserideas SET idea=?, ideacolor=? WHERE id=? AND deleted IS NULL");
		echo $mysqli->error;
		 //AND deleted IS NULL
		$stmt->bind_param("ssi", $idea, $color, $id);
		if($stmt->execute()){
			echo "Õnnestus!";
		} else {
			echo $stmt->error;
		}
			
		$stmt->close();
		$mysqli->close();
	}
?>