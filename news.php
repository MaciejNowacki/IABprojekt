<?php require './includes/header.php'; if(!$user->isLogged()) die("Strona niedostępna dla niezalogowanych!");?>
<div id="center">
	<?php require './includes/rightMenu.php';?>
	<div id="content">
		<?php 
		if(isset($_POST["SubmitFormAdd"]))
		{
			if($profile['status'] >= 2)
				$post->addNewPost($_POST["title"], $_POST["text"]);
		}

		if(isset($_POST["SubmitFormEdit"]))
		{

			if(isset($_GET['id']) && Is_Numeric($_GET['id']))
			{
				(isset($_POST["delete"])) ? $delete = 1 : $delete = 0;
				$post->editNews($_POST["title"], $_POST["text"], (isset($_POST["visible"]))? $_POST["visible"]:'null', (isset($_POST["createDate"]))? $_POST["createDate"]:'null', (isset($_POST["lastModerate"]))? $_POST["lastModerate"]:'null', (isset($_POST["moderateBy"]))? $_POST["moderateBy"]:'null', $_GET['id'], $delete);
			}
		}

		if(isset($_POST["SubmitFormNew"]))
		{
			if($user->isLogged() && $profile['status'] >= 1)
				if($user->getNumberOfWarnings() < 5)
					$post->addNewComment($_POST["text"], $_GET['id']);
				else
					echo "<p class='error'>Masz zbyt dużo punktów ostrzeżeń!</p>";
		}

		if($_GET['type'] == 'add')
		{
			if($profile['status'] >= 2)
			{
				?>
				<form action="" method="post" enctype="multipart/form-data">
					<table>
						<tr>
							<td>
								<label for="title">Tytuł</label>
							</td>
							<td>
								<input type="text" id="title" name="title" placeholder="Wpisz tytuł newsa.." maxlength="32">
							</td>
						</tr>
						<tr>
							<td>
								<label for="text">Treść</label>
							</td>
							<td>
								<textarea name="text" id="text" rows="4" cols="50"></textarea>
							</td>
						</tr>
						<tr>
							<td></td><td>
								<input name="SubmitFormAdd" value="Dodaj!" type="submit">
							</td>
						</tr>
					</table>
				</form>
				<?php
			}
		}

		if($_GET['type'] == 'view' && Is_Numeric($_GET['id']))
		{
			$postContent = $post->getPost($_GET['id']);

			echo "<h3><u>".$postContent["title"]."</u></h3>";
			echo "<h6>Utworzono: ".$postContent["createDate"]." przez <a href='./profile.php?id=".$postContent['author']."'>".$postContent['d1']."</a></h6>";
			if($postContent['moderateBy'] != null)
				echo "<h6>Ostatnio edytowane w dniu ".$postContent['lastModerate']." przez <a href='./profile.php?id=".$postContent['moderateBy']."'>".$postContent['d2']."</a></h6>";
			echo "<p>";
			echo $postContent["text"];
			echo "</p>";

			if($postContent["author"] == $_SESSION['user_id'] || @$profile["status"] >= 2)
				echo "<p class='editLink'><a href='./news.php?type=edit&id=".$_GET['id']."'><u>Edytuj</u></a></p>";

			$comments = $post->getCommentsOfPost($_GET['id']);
			for($i = 0; $i < sizeof($comments); $i++)
			{
				if($comments[$i]['visible'] == 1 || $profile['status'] >= 2)
				{
					($comments[$i]['visible'] == 0) ? $color = "background-color:rgba(128,128,128,0.2);" : $color = '';
					echo "<div class='comment' style='".$color."'><div class='leftSide'> <a href='./profile.php?id=".$comments[$i]['id2']."'>".$comments[$i]['displayName']."</a><br><img src='./img/usersImg/".$comments[$i]['avatarHash']."' alt='avatar'><br>Ilość komentarzy: ".$user->getNumberOfComments($comments[$i]['id2'])["value"]."<br>Dołączył: ".date("d-m-Y", strtotime($user->data($comments[$i]['id2'])['registerDate']))."</div><div class='rightSide'><h6>".$comments[$i]['createDate']."</h6>".$comments[$i]['text']."</div>";

					echo "<p style='text-align: right;'>";
					if($comments[$i]['id2'] == $_SESSION['user_id'] || $profile['status'] >= 2)
						echo "<a href='./comment.php?type=edit&id=".$comments[$i]['id1']."'><img src='./img/editButton.png' alt='edytuj komentarz'></a><a href='./comment.php?type=delete&id=".$comments[$i]['id1']."'><img src='./img/delete.png' alt='usun komentarz'></a>";

					if($profile['status'] >= 2)
						echo "<a href='./comment.php?type=display&id=".$comments[$i]['id1']."'><img src='./img/see.png' alt='zmien widocznosc'></a>";

					echo "</p></div>";
				}
			}

			if($user->getNumberOfWarnings() < 5 && $profile['status'] >= 1)
			{
			?>
				<form action="" method="post" enctype="multipart/form-data">
					<table>
						<tr>
							<td>
								<label for="title">Treść</label>
							</td>
							<td>
								<textarea name="text" id="text" rows="4" cols="50"></textarea>
							</td>
						</tr>
						<tr>
							<td></td><td>
								<input name="SubmitFormNew" value="Dodaj!" type="submit">
							</td>
						</tr>
					</table>
				</form>
			<?php
			}
		}

		if($_GET['type'] == 'edit' && Is_Numeric($_GET['id']))
		{
			$postContent = $post->getPost($_GET['id']);

			if($postContent["author"] != $_SESSION['user_id'])
			{
				if(@$profile["status"] < 2)
					die("Moduł moderatora. Niedostępny dla Ciebie :)");	
			}
		?>
		<form action="" method="post" enctype="multipart/form-data">
			<table>
				<tr>
					<td>
						<label for="title">Tytuł</label>
					</td>
					<td>
						<input type="text" id="title" name="title" value="<?php echo $postContent['title'];?>" placeholder="Wpisz tytuł newsa.." maxlength="32">
					</td>
				</tr>
				<tr>
					<td>
						<label for="text">Treść</label>
					</td>
					<td>
						<textarea name="text" id="text" rows="4" cols="50"><?php echo $postContent['text'];?></textarea>
					</td>
				</tr>
				<tr>
					<td>Usunąć</td>
					<td>
						<input type="checkbox" name="delete" value="1">
					</td>
				</tr>
				<?php
				if(@$profile["status"] >= 2)
				{
				?>
				<tr>
					<td>
						<label for="visible">Widoczny</label>
					</td>
					<td>
						<select name="visible" id="visible">
							  <option value="1" <?php echo ($postContent["visible"] == '1') ? ' selected="selected"' : ''?>>TAK</option>
							  <option value="0" <?php echo ($postContent["visible"] == '0') ? ' selected="selected"' : ''?>>NIE</option>
						</select>
					</td>
				</tr>
				<?php
				}

				if(@$profile["status"] >= 3)
				{
				?>
				<tr>
					<td>
						<label for="createDate">Data utworzenia</label>
					</td>
					<td>
						<input type="text" name="createDate" id="createDate" value="<?php echo $postContent["createDate"]; ?>">
					</td>
				</tr>
				<tr>
					<td>
						<label for="lastModerate">Data ostatniej edycji</label>
					</td>
					<td>
						<input type="text" name="lastModerate" id="lastModerate" value="<?php echo $postContent["lastModerate"]; ?>">
					</td>
				</tr>
				<tr>
					<td>
						<label for="moderateBy">Autor ostatniej edycji</label>
					</td>
					<td>
						<input type="text" name="moderateBy" id="moderateBy" value="<?php echo $postContent["moderateBy"]; ?>">
					</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td></td><td>
						<input name="SubmitFormEdit" value="Zapisz" type="submit">
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