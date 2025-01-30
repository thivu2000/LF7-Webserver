<!DOCTYPE html>
<html>
<head>
	<title>Registrierung</title>
</head>
<body>
<?php
	$showFormular = true;
	
	if(isset($_GET['register'])) {
		$error = false;
		$email = $_POST['email']
		$password = $_PORT['password']
		$password2 = $_PORT['password2']
	}

	if($showFormular) {
		?>

		<form method="post">
			<p>E-Mail:</p><br>
			<input type="email" name="email"></input>
			<p>Passwort:</p><br>
			<input type="password" name="password"></input>
			<p>Passwort wiederholen:</p><br>
			<input type="password" name="password2"></input>
			<input type="submit" value="Bestätigen"></input>
		</form>

		<?php
	}
?>

</body>
</html>
