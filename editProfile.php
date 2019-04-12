<?php require './includes/header.php'; if(!$user->isLogged()) die("Strona niedostępna dla niezalogowanych!");?>
<div id="center">
	<?php require './includes/rightMenu.php';?>
	<div id="content">
		<?php if(isset($_POST["editSubmit"])) $user->editProfile($_POST["displayName"], $_POST["password"], $_POST["password2"], $_POST["confirm"], $_POST["emailAddress"], $_FILES["fileToUpload"]);  ?>
		<form action="" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					<td>
						<label for="login">Login</label>
					</td>
					<td>
						<input type="text" id="login" name="login" value="<?php echo $profile['login']; ?>" placeholder="Login" maxlength="32" disabled>
					</td>
				</tr>
				<tr>
					<td>
						<label for="displayName">Nazwa wyświetlana</label>
					</td>
					<td>
						<input type="text" id="displayName" name="displayName" value="<?php echo $profile['displayName']; ?>" placeholder="Nazwa wyświetlana" maxlength="32">
					</td>
				</tr>
				<tr>
					<td>
						<label for="password">Hasło</label>
					</td>
					<td>
						<input type="password" id="password" placeholder="Nowe hasło" name="password" maxlength="32">
					</td>
				</tr>
				<tr>
					<td>
						<label for="password2">Powtórz hasło</label>
					</td>
					<td>
						<input type="password" id="password2" placeholder="Nowe hasło" name="password2" maxlength="32">
					</td>
				</tr>
				<tr>
					<td>
						<label for="emailAddress">Adres email</label>
					</td>
					<td>
						<input type="text" id="emailAddress" name="emailAddress" placeholder="Adres email" value="<?php echo $profile['email']; ?>" maxlength="32">
					</td>
				</tr>
				<?php if($profile["status"] > 0)
				{
				?>
				<tr>
					<td>
						<label for="fileToUpload"><?php echo "<img src='./img/usersImg/".$profile["avatarHash"]."' title='Twoj avatar'>";?></label>
					</td>
					<td>
						<input type="file" name="fileToUpload" id="fileToUpload">
					</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td colspan="2">
						----------------------------------------------------
					</td>
				</tr>
				<tr>
					<td>
						<label for="confirm">Potwierdź operację starym hasłem</label>
					</td>
					<td>
						<input type="password" id="confirm" name="confirm" placeholder="Stare hasło" maxlength="32">
					</td>
				</tr>
				<tr>
					<td></td><td>
						<input name="editSubmit" value="Aktualizuj" type="submit">
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<?php require './includes/footer.php'; ?>