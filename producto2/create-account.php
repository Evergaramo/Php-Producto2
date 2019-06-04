<!doctype html>
<html lang="en">
  <head>
    <title>Create account on database</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
  </head>
<body>

<div class="container">

	<?php
	
	/*
	If the email don't exist, the data from the form is sended to the
	database and the account is created
	*/
	$name = $_POST['name'];
	$username = $_POST['username'];
	$email = $_POST['email'];
	$pass = $_POST['password'];
	$tabla = $_POST['tabla'];
	
	//validacion anti-inyección sql
	/*if(!ctype_alnum($name)){
		$no_permitidos = '!"·$%&/()=?¿¡#@|'+'´ç.-^\*¨Ç:_,;{}[]<>+\'\"';
		for ($i=0; $i<strlen($name); $i++){
			if (strpos($no_permitidos, substr($name,$i,1))===true){
				die();
			}
		}
	}
	
	//validacion anti-inyección sql
	if(!ctype_alnum($username)){
		$no_permitidos = '!"·$%&/()=?¿¡#@|'+'´ç.-^\*¨Ç:_,;{}[]<>+\'\"';
		for ($i=0; $i<strlen($username); $i++){
			if (strpos($no_permitidos, substr($username,$i,1))===true){
				die();
			}
		}
	}
	
	//validacion anti-inyección sql
	$email = filter_var($email, FILTER_VALIDATE_EMAIL);
	if (empty($email)) {
		die();
	}
	
	//validacion anti-inyección sql
	if(!ctype_alnum($pass)){
		$no_permitidos = '!"·$%&/()=?¿¡#@|'+'´ç.-^\*¨Ç:_,;{}[]<>+\'\"';
		for ($i=0; $i<strlen($pass); $i++){
			if (strpos($no_permitidos, substr($pass,$i,1))===true){
				die();
			}
		}
	}
			
	//validacion anti-inyección sql
	$permitidos = 'qwertyuiopasdfghjklñzxcvbnm_';
	for ($i=0; $i<strlen($tabla); $i++){
		if (strpos($permitidos, substr($tabla,$i,1))===false){
			die();
		}	
	}*/
	
	//comprobar campo tabla
	if($tabla !== 'users_admin' && $tabla !== 'teachers' && $tabla !== 'students')
		exit();

	include 'conn.php';
	
	// Query to check if the email already exist
	$stmt = $conn->prepare("SELECT * FROM ". $tabla . " WHERE email = ?");
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$result = $stmt->get_result();

	// If count == 1 that means the email is already on the database
	if ($result->num_rows === 1) {
	echo "<div class='alert alert-warning mt-4' role='alert'>
					<p>That email is already in our database.</p>
					<p><a href='login.html'>Please login here</a></p>
				</div>";
	} /*else {
		$stmt->close();
	
		// The password_hash() function convert the password in a hash before send it to the database
		//$passHash = password_hash($pass, PASSWORD_DEFAULT);
		
		// Query to send Name, Email and Password hash to the database
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
		} catch(Exception $e) {
		  $mysqli->rollback(); //remove all queries from queue if error (undo)
		  echo "Error: " . $query . "<br>" . mysqli_error($conn);
		  throw $e;
		}
		
		$stmt->close();
		
	}*/
	else {
		
		function generarLinkTemporal($email, $username, $password, $tabla){

			$cadena = $email.$username.rand(1,9999999).date('Y-m-d');
			$token = sha1($cadena);
			
			// Connection variables
			$dbhost	= "localhost";	   // localhost or IP
			$dbuser	= "root";		  // database username
			$dbpass	= "";		     // database password
			$dbname	= "producto2";    // database name

			// Create connection
			$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
			$conn->set_charset("utf8mb4");

			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			
			$stmt = $conn->prepare("INSERT INTO tblinsertar (email, username, password, tabla, token, creado) VALUES(?,?,SHA2(?,512),?,?,NOW());");
			$stmt->bind_param("sssss", $email, $username, $password, $tabla, $token);
			if ($stmt->execute()) {
				$stmt->close();
				//$enlace = $_SERVER["SERVER_NAME"].':8000/cuenta/insert.php?email='.sha1($email).'&username='.sha1($username).'&password='.sha1($password).'&token='.$token;
				$enlace = $_SERVER["SERVER_NAME"].'/cuenta/insert.php?email='.sha1($email).'&username='.sha1($username).'&password='.sha1($password).'&token='.$token;
				return $enlace;
			}
			else
				return FALSE;
		}

		function enviarEmail( $email, $link ){

			require("class.phpmailer.php");
			
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPAuth = true;
			//$mail->SMTPSecure = "ssl";
			$mail->Host = "localhost"; // SMTP a utilizar. Por ej. smtp.elserver.com
			$mail->Port = 587; // Puerto a utilizar
			$mail->Username = "ejemplo@localhost.com"; // Correo completo a utilizar
			$mail->Password = ""; // Contraseña
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
			//====== DE QUIEN ES ========
			$mail->From = "ejemplo@localhost.com"; // Desde donde enviamos (Para mostrar)
			$mail->FromName = "Ejemplo";
			//$mail->AddAttachment("images/foto.jpg", "foto_regalo.jpg");
			//====== PARA QUIEN =========
			$mail->AddAddress($email); // Esta es la dirección a donde enviamos
			//$mail->AddBCC("cuenta@dominio.com"); // Copia oculta
			//====== MENSAJE =========
			$mail->Subject = "Cuenta nueva"; // Este es el titulo del email.
			$mail->IsHTML(true); // El correo se envía como HTML
			//Cuerpo del mensaje
			$body = '<html>
			<head>
				<title>Completar registro</title>
			</head>
			<body>
				<p>Para completar el registro, haga click en el enlace.</p>
				<p>
					<strong>Enlace para completar:</strong><br>
					<a href="'.$link.'">Click aquí para completar</a>
				</p>
			</body>
			</html>';
			$mail->Body = $body; // Mensaje a enviar
			$mail->AltBody = "Cuenta nueva"; // Texto sin html
			//$mail->AddAttachment("imagenes/imagen.jpg", "imagen.jpg");
			
			$exito = $mail->Send(); // Envía el correo.
			
			return $exito;
		}
		
		if( $email != "" ){

			$linkTemporal = generarLinkTemporal( $email, $username, $pass, $tabla );
			if($linkTemporal){
				if(enviarEmail( $email, $linkTemporal ))
					$_SESSION['error'] = "error";
				else
					$_SESSION['error'] = "error";
			}
			else
				$_SESSION['error'] = "error";
		}
		else
			$_SESSION['error'] = "error";
		
		echo "<div class='alert alert-success mt-4' role='alert'><h3>Recibirá un email para completar el registro.</h3>
			<a class='btn btn-outline-primary' href='login.html' role='button'>Login</a></div>";
		
		
	}
	$conn->close();
	?>
</div>
	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
  </body>
</html>