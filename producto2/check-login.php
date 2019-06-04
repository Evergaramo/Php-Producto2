<?php
session_start();
?>

<!doctype html>
<html lang="en">
	<head>
		<title>Check Login and create session</title>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
	</head>
	<body>
		<div class="container">
		
			<?php
			// Connection info. file
			include 'conn.php';	
			
			// data sent from form login.html
			if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['tabla'])){
				$email = $_POST['email']; 
				$password = $_POST['password'];
				$tabla = $_POST['tabla'];
			}else{
				$email = $_SESSION['email']; 
				$password = $_SESSION['password'];
				$tabla = $_SESSION['tabla'];
			}
			
			//validacion anti-inyección sql
			$email = filter_var($email, FILTER_VALIDATE_EMAIL);
			if (empty($email)) {
				die();
			}
			
			//validacion anti-inyección sql
			if(isset($_POST['password'])){
				$password = $_POST['password'];
				if(!ctype_alnum($password)){
					$no_permitidos = '!"·$%&/()=?¿¡#@|'+'´ç.-^\*¨Ç:_,;{}[]<>+\'\"';
					for ($i=0; $i<strlen($password); $i++){
						if (strpos($no_permitidos, substr($password,$i,1))===true){
							die();
						}
					}
				}
			}
			
			//validacion anti-inyección sql
			$permitidos = 'qwertyuiopasdfghjklñzxcvbnm_';
			for ($i=0; $i<strlen($tabla); $i++){
				if (strpos($permitidos, substr($tabla,$i,1))===false){
					die();
				}	
			}
			
			//comprobar campo tabla
			if($tabla !== 'users_admin' && $tabla !== 'teachers' && $tabla !== 'students')
				die();
			
			// Query sent to database
			if($tabla=="users_admin"){
				$stmt = $conn->prepare("SELECT name FROM " . $tabla . " WHERE (email = ? OR username = ?) AND password = SHA2(?,256)");
				$stmt->bind_param("sss", $email, $email, $password);
			}else if($tabla=="teachers"){
				$stmt = $conn->prepare("SELECT name FROM " . $tabla . " WHERE email = ? AND nif = ?");
				$stmt->bind_param("ss", $email, $password);
			}else if($tabla=="students"){
				$stmt = $conn->prepare("SELECT name FROM " . $tabla . " WHERE (email = ? OR username = ?) AND pass = SHA2(?,256)");
				$stmt->bind_param("sss", $email, $email, $password);
			}
			
			$stmt->execute();
			$result=$stmt->get_result();
			
			/* 
			password_Verify() function verify if the password entered by the user
			match the password hash on the database. If everything is OK the session
			is created for one minute. Change 1 on $_SESSION[start] to 5 for a 5 minutes session.
			*/
			//if (password_verify($_POST['password'], $hash)) {	
			if($row = $result->fetch_assoc()){
				
				$_SESSION['loggedin'] = true;
				$_SESSION['name'] = $row['name'];
				$_SESSION['start'] = time();
				$_SESSION['expire'] = $_SESSION['start'] + (15 * 60);//15 min
				
				//para editar
				$_SESSION['tabla'] = $tabla;
				$_SESSION['email'] = $email;
				$_SESSION['password'] = $password;
				
				echo "<div class='alert alert-success mt-4' role='alert'><strong>Welcome!</strong> $row[name]			
				<p><a href='edit-profile.php'>Edit Profile</a></p>";
				if($tabla=="users_admin"){
					echo "<p><a href='http://localhost/phpmyadmin/'>Administrar base de datos</a></p>";
				}
				else{
					echo "<p><a href='calendar.php'>Ver calendario</a></p>";
				}
				echo "<p><a href='logout.php'>Logout</a></p></div>";
			
			} else {
				echo "<div class='alert alert-danger mt-4' role='alert'>Email or Password are incorrects!
				<p><a href='login.html'><strong>Please try again!</strong></a></p></div>";			
			}
			
			$stmt->close();
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