<?php require './includes/header.php'; if(!$user->isLogged()) die("Strona niedostępna dla niezalogowanych!");?>
<div id="center">
	<?php require './includes/rightMenu.php';?>
	<div id="content">
		<?php 
		if(isset($_POST["SubmitFormEdit"]))
		{
			if(isset($_GET['id']) && Is_Numeric($_GET['id']))
				$post->editComment($_POST["text"], $_GET['id']);
		}

		if($_GET['type'] == 'edit' && Is_Numeric($_GET['id']))
		{
			$commentContent = $post->getComment($_GET['id']);

			if($commentContent["author"] != $_SESSION['user_id'])
			{
				if(@$profile["status"] < 2)
					die("Moduł moderatora. Niedostępny dla Ciebie :)");	
			}
		?>
		<form action="" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					<td>
						<label for="text">Treść</label>
					</td>
					<td>
						<textarea name="text" id="text" rows="4" cols="50"><?php echo $commentContent['text'];?></textarea>
					</td>
				</tr>
				<tr>
					<td></td><td>
						<input name="SubmitFormEdit" value="Zapisz" type="submit">
					</td>
				</tr>
			</table>
		</form>
		<?php
		}

		if($_GET['type'] == 'delete' && Is_Numeric($_GET['id']))
		{
			$commentContent = $post->getComment($_GET['id']);

			if($commentContent["author"] != $_SESSION['user_id'])
			{
				if(@$profile["status"] < 2)
					die("Moduł moderatora. Niedostępny dla Ciebie :)");	
			}
			$post->deleteComment($_GET['id']);
		}

		if($_GET['type'] == 'display' && Is_Numeric($_GET['id']))
		{
			$commentContent = $post->getComment($_GET['id']);

			if(@$profile["status"] < 2)
					die("Moduł moderatora. Niedostępny dla Ciebie :)");	

			$flag = ($commentContent['visible'])? 0 : 1;
			$post->setVisibleFlag($flag, $_GET['id']);
		}
		?>
	</div>
</div>
<?php require './includes/footer.php'; ?>