<?php require './includes/header.php'; if(@$profile["status"] < 3) die("Moduł administracyjny. Niedostępny dla Ciebie :)");?>
<div id="center">
	<?php require './includes/rightMenu.php';?>
	<div id="content">
		<?php 
		$tableOfUsers = $user->listUsers();

		if(isset($_GET["id"]) && is_numeric($_GET["id"]))
		{
			$profileOfEditUser = $user->data($_GET["id"]);
			if(isset($_POST["editSubmit"]))
			{
				(isset($_POST["clear"])) ? $clearn = 1 : $clearn = 0;
				$user->editProfileByAdmin($_POST["displayName"], $_POST["password"], $_POST["emailAddress"], $_FILES["fileToUpload"], $_POST["statusId"], $profileOfEditUser["id"], $clearn, $_POST["login"]); 
			}
			?>
			<form action="" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					<td>
						<label for="login">Login</label>
					</td>
					<td>
						<input type="text" id="login" name="login" value="<?php echo $profileOfEditUser['login']; ?>" placeholder="Login" maxlength="32">
					</td>
				</tr>
				<tr>
					<td>
						<label for="displayName">Nazwa wyświetlana</label>
					</td>
					<td>
						<input type="text" id="displayName" name="displayName" value="<?php echo $profileOfEditUser['displayName']; ?>" placeholder="Nazwa wyświetlana" maxlength="32">
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
						<label for="emailAddress">Adres email</label>
					</td>
					<td>
						<input type="text" id="emailAddress" name="emailAddress" placeholder="Adres email" value="<?php echo $profileOfEditUser['email']; ?>" maxlength="32">
					</td>
				</tr>
				<tr>
					<td>
						<label for="fileToUpload"><?php echo "<img src='./img/usersImg/".$profileOfEditUser["avatarHash"]."' title='Twoj avatar'>";?></label>
					</td>
					<td>
						<input type="file" name="fileToUpload" id="fileToUpload">
					</td>
				</tr>
				<tr>
					<td>Ostatnio aktywny:</td>
					<td>
						<?php 
						$teraz = strtotime(date("Y-m-d H:i:s"));
						$ostatnio2 = strtotime('+1 minutes', strtotime($profileOfEditUser["lastVisit"]));
						($teraz < $ostatnio2) ? $color = "#00FF00" : $color = "#FF0000";
						echo "<span style='background-color:".$color."'>".$profileOfEditUser["lastVisit"]."</span>"; 
						?>
					</td>
				</tr>
				<tr>
					<td>Data rejestracji:</td><td><?php echo $profileOfEditUser["registerDate"];?></td>
				</tr>
				<tr>
					<td>Ilość nieudanych prób logowania:</td>
					<td>
						<?php 
						($profileOfEditUser["attempts"] > 3) ? $color = "#FF0000" : $color = null;
						echo "<span style='background-color:".$color."'>".$profileOfEditUser["attempts"];

						?><input type="checkbox" name="clear" value="1">Wyzeruj</span>
					</td>
				</tr>
				<tr>
					<td>Data ostatniej NIEUDANEJ próby logowania:</td><td><?php echo $profileOfEditUser["lastUnsuccessfulLogin"]; ?></td>
				</tr>
				<tr>
					<td>Data ostatniej UDANEJ próby logowania:</td><td><?php echo $profileOfEditUser["lastSuccessfulLogin"]; ?></td>
				</tr>
				<tr>
					<td>Adres IP:</td><td><?php echo $profileOfEditUser["ipAddress"]; ?></td>
				</tr>
				<tr>
					<td>Status:</td>
					<td>
						<select name="statusId">
						  <option value="-1" <?php echo ($profileOfEditUser["status"] == '-1' ? ' selected="selected"' : ''); ?>>Zbanowany</option>
						  <option value="0" <?php echo ($profileOfEditUser["status"] == '0' ? ' selected="selected"' : ''); ?>>Użytkownik nieaktywny</option>
						  <option value="1" <?php echo ($profileOfEditUser["status"] == '1' ? ' selected="selected"' : ''); ?>>Użytkownik zarejestrowany</option>
						  <option value="2" <?php echo ($profileOfEditUser["status"] == '2' ? ' selected="selected"' : ''); ?>>Moderator</option>
						  <option value="3" <?php echo ($profileOfEditUser["status"] == '3' ? ' selected="selected"' : ''); ?>>Administrator</option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td><td>
						<input name="editSubmit" value="Aktualizuj" type="submit">
					</td>
				</tr>
			</table>
		</form>
		<?php
		}
		else
		{
			echo "<table><tr><td>User ID</td><td>Login</td><td>Nazwa wyświetlana</td><td>Edytuj profil</td></tr>";
			foreach ($tableOfUsers as $value)
			{
				echo "<tr><td>".$value["id"]."</td><td>".$value["login"]."</td><td>".$value["displayName"]."</td><td><a href='userPanel.php?id=".$value["id"]."'><img src='./img/editButton.png' alt='Edit Button'></a></td></tr>";
				//id, login, email, displayName, lastVisit, registerDate, status, avatarHash, attempts, lastUnsuccessfulLogin, lastSuccessfulLogin, ipAddress
			}
			echo "</table>";
		}
		?>
	</div>
</div>
<?php require './includes/footer.php'; ?>