<?php require './includes/header.php'; if(@$profile["status"] < 2) die("Moduł moderatora. Niedostępny dla Ciebie :)");?>
<div id="center">
	<?php require './includes/rightMenu.php';?>
	<div id="content">
		<?php 
		if(isset($_GET["id"]) && is_numeric($_GET["id"]))
		{
			$userWhoIsWarning = $user->data($_GET["id"]);
			if(isset($_POST["SubmitForm"]))
			{
				$user->addWarning($_GET["id"], $_POST["reason"], $_POST["value"], $_POST["expDate"]);
			}
			?>
			<?php echo "Karzesz użytkownika: ".$userWhoIsWarning["displayName"]."<br>"; ?>
			<form action="" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					<td>
						<label for="reason">Treść</label>
					</td>
					<td>
						<textarea name="reason" id="reason" rows="4" cols="50"></textarea>
					</td>
				</tr>
				<tr>
					<td>
						<label for="value">Wartość</label>
					</td>
					<td>
						<input type="text" id="value" name="value" placeholder="Podaj wartość ostrzeżenia" maxlength="32">
					</td>
				</tr>
				<tr>
					<td>
						<label for="expDate">Okres ważności</label>
					</td>
					<td>
						<select name="expDate" id="expDate">
							  <option value="1">1 Godzina</option>
							  <option value="3">3 Godziny</option>
							  <option value="12">12 Godzin</option>
							  <option value="24">1 Dzień</option>
							  <option value="72">3 Dni</option>
							  <option value="168">1 Tydzień</option>
							  <option value="336">2 Tygodnie</option>
							  <option value="720">1 Miesiąc</option>
							  <option value="2160">3 Miesiące</option>
							  <option value="8760">1 Rok</option>
						</select>
					</td>
				</tr>
				<tr>
					<td></td><td>
						<input name="SubmitForm" value="Dodaj!" type="submit">
					</td>
				</tr>
			</table>
		</form>
	<?php
	}
	?>
	</div>
</div>
<?php require './includes/footer.php'; ?>