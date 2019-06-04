<?php
	session_start();

	// Connection info. file
	include 'conn.php';	
	
	if (isset($_SESSION['loggedin'])) {
		$tabla=$_SESSION['tabla'];
		
		//validacion anti-inyección sql
		if(isset($_POST['username'])){
			$username = $_POST['username'];
			if(!ctype_alnum($username)){
				$no_permitidos = '!"·$%&/()=?¿¡#@|'+'´ç.-^\*¨Ç:_,;{}[]<>+\'\"';
				for ($i=0; $i<strlen($username); $i++){
					if (strpos($no_permitidos, substr($username,$i,1))===true){
						die();
					}
				}
			}
		}
					
		//validacion anti-inyección sql
		$email = $_POST['correo'];
		$email = filter_var($email, FILTER_VALIDATE_EMAIL);
		if (empty($email)) {
			die();
		}
		
		//validacion anti-inyección sql
		if(isset($_POST['nombre'])){
			$name = $_POST['nombre'];
			if(!ctype_alnum($name)){
				$no_permitidos = '!"·$%&/()=?¿¡#@|'+'´ç.-^\*¨Ç:_,;{}[]<>+\'\"';
				for ($i=0; $i<strlen($name); $i++){
					if (strpos($no_permitidos, substr($name,$i,1))===true){
						die();
					}
				}
			}
		}
		
		//validacion anti-inyección sql
		if(isset($_POST['apellidos'])){
			$surname = $_POST['apellidos'];
			if(!ctype_alnum($surname)){
				$no_permitidos = '!"·$%&/()=?¿¡#@|'+'´ç.-^\*¨Ç:_,;{}[]<>+\'\"';
				for ($i=0; $i<strlen($surname); $i++){
					if (strpos($no_permitidos, substr($surname,$i,1))===true){
						die();
					}
				}
			}
		}
		
		//validacion anti-inyección sql
		if(isset($_POST['telephone'])){
			$telef = $_POST['telephone'];
			$permitidos = '0123456789';
			for ($i=0; $i<strlen($telef); $i++){
				if (strpos($permitidos, substr($telef,$i,1))===false){
					die();
				}
			}
		}
		
		//validacion anti-inyección sql
		if(isset($_POST['nif'])){
			$nif = $_POST['nif'];
			if(!ctype_alnum($nif)){
				$no_permitidos = '!"·$%&/()=?¿¡#@|'+'´ç.-^\*¨Ç:_,;{}[]<>+\'\"';
				for ($i=0; $i<strlen($nif); $i++){
					if (strpos($no_permitidos, substr($nif,$i,1))===true){
						die();
					}
				}
			}
		}
		
		//comprobar campo tabla
		if($tabla !== 'users_admin' && $tabla !== 'teachers' && $tabla !== 'students')
			exit();
		
		$emailAnterior=$_SESSION['email'];
		$sql="";
		if($tabla=="users_admin"){				 //username | name   | email   | password
			$stmt = $conn->prepare("UPDATE " . $tabla . " SET username = ?, name = ?, email = ? WHERE email = ?");
			$stmt->bind_param("ssss", $username, $name, $email, $emailAnterior);
		}else if($tabla=="teachers"){			//name  | surname  | telephone | nif| email
			$stmt = $conn->prepare("UPDATE " . $tabla . " SET name = ?, surname = ?, telephone = ?, nif = ?, email = ? WHERE email = ?");
			$stmt->bind_param("ssss", $name, $surname, $telef, $nif, $email, $emailAnterior);
		}else if($tabla=="students"){			//username  | pass  | email  | name | surname | telephone | nif
			$stmt = $conn->prepare("UPDATE " . $tabla . " SET name = ?, username = ?, surname = ?, telephone = ?, nif = ?, email = ? WHERE email = ?");
			$stmt->bind_param("ssss", $name, $username, $surname, $telef, $nif, $email, $emailAnterior);
		}
		
		try{
			$stmt->execute();
			$_SESSION['email']=$_POST['correo'];
			echo "<div class='alert alert-success mt-4' role='alert'><h3>Your account has been edited.</h3></div>";
		} catch(Exception $e) {
			$mysqli->rollback(); //remove all queries from queue if error (undo)
			echo "Error: " . $sql . "<br>" . mysqli_error($conn);
			throw $e;
		}

		$stmt->close();
		
	}
	
	$conn->close();
?>