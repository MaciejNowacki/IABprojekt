<?php require './includes/header.php'; if(!$user->isLogged()) die("Strona niedostępna dla niezalogowanych!");?>
<div id="center">
	<?php require './includes/rightMenu.php';?>
	<div id="content">
		<?php 
		if(isset($_GET["id"]) && is_numeric($_GET["id"]))
		{
			$userProfile = $user->data($_GET["id"]);
		}
		?>
		<table>
			<tr>
				<td colspan="2">
					<?php echo "<img src='./img/usersImg/".$userProfile["avatarHash"]."' title='avatar użytkownika'>";?>
				</td>
			</tr>
			<tr>
				<td>
					Nazwa wyświetlana
				</td>
				<td>
					<?php echo $userProfile['displayName']; ?>
				</td>
			</tr>
			<tr>
				<td>
					Adres email
				</td>
				<td>
					<?php echo $userProfile['email']; ?>
				</td>
			</tr>
			<tr>
				<td>
					Ilość postów
				</td>
				<td>
					<?php echo $user->getNumberOfPosts($_GET["id"])["value"]; ?>
				</td>
			</tr>
			<tr>
				<td>
					Ilość komentarzy
				</td>
				<td>
					<?php echo $user->getNumberOfComments($_GET["id"])["value"]; ?>
				</td>
			</tr>
			<?php
				if($_GET["id"] == $_SESSION['user_id'] || $profile["status"] >= 2)
				{
					echo "<tr><td>Ilość ostrzeżeń:</td><td>".$user->getNumberOfWarnings($_GET["id"])."</td></tr>";
					if($user->getNumberOfWarnings($_GET["id"]) > 0)
					{
						echo "<table><br><tr><td>Powód</td><td>Wartość</td><td>Data nadania</td><td>Data wygaśnięcia</td></tr>";
						$array = $user->getUserWarnings($_GET["id"]);
						for($i = 0; $i < sizeof($array); $i++)
						{
							echo "<tr><td>".$array[$i]['reason']."</td><td>".$array[$i]['value']."</td><td>".$array[$i]['warningDate']."</td><td>".$array[$i]['expDate']."</td>";
							if($profile["status"] >= 2)
								echo "<td><a href='./deleteWarning.php?id=".$array[$i]['id']."'><img src='./img/delete.png'></a></td>";
							echo "</tr>";
						}
						echo "</table>";
					}
				}
			?>
			</table>
			<?php
			if($profile["status"] >= 2)
			{
				echo "<tr><td><a href='./addWarning.php?id=".$_GET["id"]."'>Dodaj nowe ostrzeżenie</a></td></tr>";
			}
			?>
	</div>
</div>
<?php require './includes/footer.php'; ?>