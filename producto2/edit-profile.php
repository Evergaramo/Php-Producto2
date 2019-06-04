<?php
session_start();
?>

<!doctype html>
<html lang="en">
  <head>
    <title>Edit profile page</title>
	
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
  </head>
  
  <body>      
    <?php
    if (isset($_SESSION['loggedin'])) {
		$tabla=$_SESSION['tabla'];
		
		//comprobar campo tabla
		if($tabla !== 'users_admin' && $tabla !== 'teachers' && $tabla !== 'students')
				exit();
    }
    else {
        echo "<div class='alert alert-danger mt-4' role='alert'>
        <h4>You need to login to access this page.</h4>
        <p><a href='login.html'>Login Here!</a></p></div>";
        exit;
    }
    // checking the time now when check-login.php page starts
    $now = time();           
    if ($now > $_SESSION['expire']) {
        session_destroy();
        echo "<div class='alert alert-danger mt-4' role='alert'>
        <h4>Your session has expire!</h4>
        <p><a href='login.html'>Login Here</a></p></div>";
        exit;
        }
    ?>

    <div class="container">
        <p>Welcome: <?php echo $_SESSION['name']; ?></p>
        <h3>Edit your profile</h3>
        <ul>
			<?php
			
			// Connection info. file
			include 'conn.php';	
			
			$email = $_SESSION['email'];
			$stmt = $conn->prepare("SELECT * FROM " . $tabla . " WHERE email = ?");
			$stmt->bind_param("s", $email);
			
			$stmt->execute();
			$result=$stmt->get_result();
			$row = $result->fetch_assoc();
			
			echo "<form action='edit-info.php' method='post'><div>";
			if($tabla=="users_admin"){				 //username | name   | email   | password
				$username = $row['username'];
				$email = $row['email'];
				$name = $row['name'];
				
				echo "<li>Nombre de usuario</li><input type='text' name='username' value='$username'>
				<li>Correo electrónico</li><input type='text' name='correo' value='$email'>
				<li>Nombre</li><input type='text' name='nombre' value='$name'>";
			}else if($tabla=="teachers"){			//name  | surname  | telephone | nif| email
				$email = $row['email'];
				$name = $row['name'];
				$surname = $row['surname'];
				$telef = $row['telephone'];
				$nif = $row['nif'];
				echo "<li>Correo electrónico</li><input type='text' name='correo' value='$email'>
				<li>Nombre</li><input type='text' name='nombre' value='$name'>
				<li>Apellidos</li><input type='text' name='apellidos' value='$surname'>
				<li>Teléfono</li><input type='text' name='telef' value='$telef'>
				<li>NIF</li><input type='text' name='nif' value='$nif'>";
			}else if($tabla=="students"){			//username  | pass  | email  | name | surname | telephone | nif
				$username = $row['username'];
				$email = $row['email'];
				$name = $row['name'];
				$surname = $row['surname'];
				$telef = $row['telephone'];
				$nif = $row['nif'];
				echo "<li>Nombre de usuario</li><input type='text' name='username' value='$username'>
				<li>Correo electrónico</li><input type='text' name='correo' value='$email'>
				<li>Nombre</li><input type='text' name='nombre' value='$name'>
				<li>Apellidos</li><input type='text' name='apellidos' value='$surname'>
				<li>Teléfono</li><input type='text' name='telef' value='$telef'>
				<li>NIF</li><input type='text' name='nif' value='$nif'>";
			}
			
			echo "</div><button type='submit' class='btn btn-dark'>Editar</button></form>";
			
			/*if($tabla!="teachers"){
				echo "<form action='password-recovery.php' method='post'>Nueva contraeña:<input type='text' name='pass'><input name='email' value=$email><button type='submit' class='btn btn-dark'>Editar</button></form>";
			}*/
			
			echo "<form action='check-login.php'><input type='submit' value='Go to Menu' /></form>";
			
			$stmt->close();
			$conn->close();
			
			?>
        </ul>
        <p><a href="logout.php">Logout</a></p>
    </div>

	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

	</body>
</html>