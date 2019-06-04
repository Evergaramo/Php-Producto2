<!doctype html>
<html lang="en">
	<head>		
    	<title>Password Recovery</title>
    	<!-- Required meta tags -->
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    	<!-- Bootstrap CSS -->
    	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
  </head>
<body>
<div class="container">
	<div class="row">
		<div class="col-sm-12 col-md-12 col-lg-12">
			
			<?php
			include 'conn.php';
			
			$email = $_POST['email'];
			$tabla = $_POST['tabla'];
			
			//validacion anti-inyección sql
			$email = filter_var($email, FILTER_VALIDATE_EMAIL);
			if (empty($email)) {
				die();
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
				exit();
			
			if($tabla=="users_admin"){
				$stmt = $conn->prepare("SELECT email, password FROM " . $tabla . " WHERE email=?");
				$stmt->bind_param("s", $email);
			}else if($tabla=="teachers"){
				$stmt = $conn->prepare("SELECT email, nif FROM " . $tabla . " WHERE email=?");
				$stmt->bind_param("s", $email);
			}else if($tabla=="students"){
				$stmt = $conn->prepare("SELECT email, pass FROM " . $tabla . " WHERE email=?");
				$stmt->bind_param("s", $email);
			}
			
			$stmt->execute();
			$result=$stmt->get_result();
			
			if ($row = $result->fetch_assoc()) {	
				
				$subject = "Your password for PHP Login";
				
				if($tabla=="users_admin"){
					$pass = $row['password'];
				}else if($tabla=="teachers"){
					$pass = $row['nif'];
				}else if($tabla=="students"){
					$pass = $row['pass'];
				}
				
				$body = "Your password is:" . $pass;
				
				$headers = 'From: youremail@mail.com' . "\r\n" .
				'Reply-To: youremail@mail.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
				
				mail($email, $subject, $body, $headers);				
				
				echo "<div class='alert alert-success alert-dismissible mt-4' role='alert'>
				<button type='button' class='close' data-dismiss='alert' aria-label='Close'>
				<span aria-hidden='true'>&times;</span></button>

				<p>Email was send! Please check your email.</p>
				<p><a class='alert-link' href=login.html>Login</a></p></div>";
			} else {
				echo "We are sorry, but that email is not in our data base.";
			}
			
			$stmt->close();
			$conn->close();
			
			?>
		</div>
	</div>
</div>
<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
	</body>
</html>