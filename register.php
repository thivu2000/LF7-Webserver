<!DOCTYPE html>
<html lang="DE">
<head>
	<title>Registrierung</title>
</head>
<body>
<?php
	$showFormular = true;
	
	if(isset($_GET['register'])) {
		$error = false;
		$email = $_POST['email'];
		$password = $_POST['password'];
		$password2 = $_POST['password2'];
	}

	if($showFormular) {
		?>

		<form method="post">
			<p>E-Mail:</p><br>
            <label>
                <input type="email" name="email">
            </label>
            <p>Passwort:</p><br>
            <label>
                <input type="password" name="password">
            </label>
            <p>Passwort wiederholen:</p><br>
            <label>
                <input type="password" name="password2">
            </label>
            <input type="submit" value="Bestätigen">
		</form>

		<?php
	}
?>

</body>
</html>
