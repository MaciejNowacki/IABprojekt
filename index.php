<?php require './includes/header.php';?>
<div id="center">
	<?php require './includes/rightMenu.php'; ?>
	<div id="content">
		<?php 
		$posts = $post->getLatestNews();
		for($i = 0; $i < sizeof($posts); $i++)
		{
			($posts[$i]['visible'] == 0) ? $color = "background-color:rgba(128,128,128,0.2);" : $color = '';
			if($posts[$i]['visible'] == 1 || $profile['status'] >= 2)
			{
				echo "<div class='news' style='".$color."'><p><h3><a href='./news.php?type=view&id=".$posts[$i]['id']."'>".$posts[$i]['title']."</a></h3><p>".substr($posts[$i]['text'], 0, 150)." <a href='./news.php?type=view&id=".$posts[$i]['id']."'>[Czytaj dalej..]</a></p><h6>Utworzono: ".$posts[$i]['createDate']." przez <a href='./profile.php?id=".$posts[$i]['id1']."'>".$posts[$i]['displayName']."</a></h6>";

				if($posts[$i]['moderateBy'] != null)
					echo "<h6>Ostatnio edytowane w dniu ".$posts[$i]['lastModerate']." przez <a href='./profile.php?id=".$posts[$i]['id2']."'>".$posts[$i]['moderateBy']."</a></h6>";

				echo "</p></div>";
				
				if($i != sizeof($posts)-1)
					echo "<hr>";	
			}			
		}
		?>
	</div>
</div>
<?php require './includes/footer.php'; ?>