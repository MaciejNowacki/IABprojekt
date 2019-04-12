<?php require './includes/header.php'; if($user->isLogged()) die("Strona niedostępna dla zalogowanych!");?>
<div id="center">
	<?php require './includes/rightMenu.php';?>
	<div id="content">
		<?php if(isset($_POST["remindSubmit"])) $user->remindPassword($_POST["emailAddress"]);  ?>
		<form action="" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					<td>
						<label for="emailAddress">Adres email</label>
					</td>
					<td>
						<input type="text" id="emailAddress" name="emailAddress" placeholder="example@domain.pl" maxlength="32">
					</td>
				</tr>
				<tr>
					<td></td><td>
						<input name="remindSubmit" value="Przypomnij hasło!" type="submit">
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<?php require './includes/footer.php'; ?>