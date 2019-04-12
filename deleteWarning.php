<?php require './includes/header.php'; if(@$profile["status"] < 2) die("Moduł moderatora. Niedostępny dla Ciebie :)");?>
<div id="center">
	<?php require './includes/rightMenu.php';?>
	<div id="content">
		<?php 
		if(isset($_GET["id"]) && is_numeric($_GET["id"]))
		{
			$user->deleteWarning($_GET["id"]);
			header('Location: '.$_SERVER["HTTP_REFERER"]); //.'/profile?id='.$_GET["id"]
		}
		?>
	</div>
</div>
<?php require './includes/footer.php'; ?>