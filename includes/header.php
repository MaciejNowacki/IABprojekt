<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="pl">
<head>
	<link rel="stylesheet" href="css/style.css">
	<title>myWebApp</title>
	<meta name="description" content="">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<meta name="keywords" content="">
</head>
<body>
	<div id="main">
		<div id="header">
			<div id="header_form">
				<?php
				if(!$user->isLogged())
				{
					if(isset($_POST["loginSubmit"])) 
						$user->logIn($_POST["login"], $_POST["password"]);
					?>
					<form action="" method="post">
						<input type="text" name="login" placeholder="Login" maxlength="32">
						<input type="password" name="password" placeholder="Hasło" maxlength="32">
						<input name="loginSubmit" value="Zaloguj się!" type="submit">
					</form>
					<p class="menuBar">
						<a href="./forgottenPassword.php" title="Przypomnij hasło">Przypomnij hasło</a> |
						<a href="./register.php" title="Zarejestruj się">Zarejestruj się!</a>
					</p>
				<?php
				}
				else
				{
					echo "Ostatnie nieudane logowanie: ".$profile["lastUnsuccessfulLogin"]."<br>";
					echo "<img src='./img/usersImg/".$profile["avatarHash"]."' title='Twoj avatar'><br>";
					echo "Witaj <a href='./profile.php?id=".$_SESSION['user_id']."'>".$profile["displayName"]."</a>!";
					echo "<a href='./logOut.php'> | Wyloguj się!</a>";
					echo "<a href='./editProfile.php'> | Edytuj profil</a>";
				}
				?>
			</div>
			<div id="header_logo">
				<a href="./" title="Powrót do strony głównej"><img src="./img/logo.png" title="logo"></a>
			</div>
		</div>