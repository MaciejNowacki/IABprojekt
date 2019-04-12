<?php require './includes/header.php'; if(!$user->isLogged()) die("Strona niedostępna dla niezalogowanych!");?>
<div id="center">
	<?php require './includes/rightMenu.php';?>
	<div id="content">
		<?php 
		$tableOfUsers = $user->listUsers();
		echo "<table><tr><td>Nazwa wyświetlana</td><td>Ilość postów</td><td>Ilość komentarzy</td></tr>";
		foreach ($tableOfUsers as $value)
		{
			echo "<tr><td><a href='./profile.php?id=".$value["id"]."'>".$value["displayName"]."</a></td><td>".$user->getNumberOfPosts($value["id"])["value"]."</td><td>".$user->getNumberOfComments($value["id"])["value"]."</td></tr>";
		}
		echo "</table>";
		?>
	</div>
</div>
<?php require './includes/footer.php'; ?>