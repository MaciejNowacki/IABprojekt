<?php require './includes/header.php'; if($user->isLogged()) die("Strona niedostępna dla zalogowanych!");?>
<div id="center">
	<?php require './includes/rightMenu.php';?>
	<div id="content">
		<?php if(isset($_POST["regSubmit"])) $user->regUser($_POST["login"], $_POST["displayName"], $_POST["password"], $_POST["password2"], $_POST["emailAddress"]);  ?>
		<form action="" method="post">
			<table>
				<tr>
					<td>
						<label for="login">Login</label>
					</td>
					<td>
						<input type="text" id="login" name="login" placeholder="Login" maxlength="32">
					</td>
				</tr>
				<tr>
					<td>
						<label for="displayName">Nazwa wyświetlana</label>
					</td>
					<td>
						<input type="text" id="displayName" name="displayName" placeholder="Nazwa wyświetlana" maxlength="32">
					</td>
				</tr>
				<tr>
					<td>
						<label for="password">Hasło</label>
					</td>
					<td>
						<input type="password" id="password" placeholder="Hasło" name="password" maxlength="32">
					</td>
				</tr>
				<tr>
					<td>
						<label for="password2">Powtórz hasło</label>
					</td>
					<td>
						<input type="password" id="password2" placeholder="Powtórz hasło" name="password2" maxlength="32">
					</td>
				</tr>
				<tr>
					<td>
						<label for="emailAddress">Adres email</label>
					</td>
					<td>
						<input type="text" id="emailAddress" name="emailAddress" placeholder="Adres email" maxlength="32">
					</td>
				</tr>
				<tr>
					<td></td><td>
						<input name="regSubmit" value="Zarejestruj się!" type="submit">
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<?php require './includes/footer.php'; ?>