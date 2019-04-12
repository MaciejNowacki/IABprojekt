<div id="menu_right">
	<?php
	if($user->isLogged() && $profile["status"] == 0) echo "<p class='infoActivation'>Twój profil czeka na akceptację.<br> Nie masz dostepu do niektórych funkcji np. ustawienia osobistego avatara czy sekcji komentarzy.</p>";

	?>
	<ol>
    	<ul>
			<li><a href="./userList.php">Lista użytkowników</a></li>
    	</ul>
	</ol>
	<?php
	if(@$profile["status"] >= 2 && $user->isLogged())
	{
	?>
	<ol><u>Panel moderatora</u>
    	<ul>
			<li><a href="./news.php?type=add">Dodaj nowy news</a></li>
    	</ul>
	</ol>
	<?php
	}
	if(@$profile["status"] >= 3 && $user->isLogged())
	{
	?>
	<ol><u>Panel administratora</u>
    	<ul>
			<li><a href="./userPanel.php">Panel użytkowników</a></li>
    	</ul>
	</ol>
	<?php
	}
	?>
</div>