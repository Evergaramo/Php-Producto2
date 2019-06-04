<?php 
	include 'conn.php';
	
	if(!isset($_GET['token'])){
		die();
	}
	
	$_SESSION['error'] = '';

	$token = $_GET['token'];
	
	//validacion anti-inyección sql
	$permitidos = '0123456789qwertyuiopñlkjhgfdsazxcvbnm';
	for ($i=0; $i<strlen($token); $i++){
		if (strpos($permitidos, substr($token,$i,1))===false){
			die();
		}
	}
	
	$stmt = $conn->prepare("SELECT * FROM tblinsertar WHERE token = ?");
	$stmt->bind_param("s", $token);
	$stmt->execute();
	$result = $stmt->get_result();

	// If count == 1 that means the email is already on the database
	if($row = $result->fetch_assoc()){

		$email = $row['email'];
		$username = $row['username'];
		$password = $row['password'];
		$tabla = $row['tabla'];
		
		if($tabla=="users_admin"){
			$stmt = $conn->prepare("INSERT INTO " . $tabla . " (name, username, email, password) VALUES (?, ?, ?, SHA2(?,256))");
			$stmt->bind_param("ssss", $name, $username, $email, $pass);
		}else if($tabla=="teachers"){
			$stmt = $conn->prepare("INSERT INTO " . $tabla . " (name, surname, email, nif) VALUES (?, ?, ?, ?)");
			$stmt->bind_param("ssss", $name, $username, $email, $pass);
		}else if($tabla=="students"){
			$stmt = $conn->prepare("INSERT INTO " . $tabla . " (name, username, email, pass) VALUES (?, ?, ?, SHA2(?,256))");
			$stmt->bind_param("ssss", $name, $username, $email, $pass);
		}

		try {
			$stmt->execute();
			echo "<div class='alert alert-success mt-4' role='alert'><h3>Your account has been created.</h3>
			<a class='btn btn-outline-primary' href='login.html' role='button'>Login</a></div>";
			
			$stmt = $conn->prepare("DELETE FROM tblinsertar WHERE email = ?");
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$stmt->close();
			
		} catch(Exception $e) {
		  $mysqli->rollback(); //remove all queries from queue if error (undo)
		  //echo "Error: " . $query . "<br>" . mysqli_error($conn);
		  echo "Error";
		  throw $e;
		}
		
		$stmt->close();
		
	}
	/*else{
		header('Location:../index.php');
	}*/
	
	$conn->close();
?>