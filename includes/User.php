<?php
function resultToArray($result) 
{
    $rows = array();
    while($row = $result->fetch_assoc()) 
	{
        $rows[] = $row;
    }
    return $rows;
}

class User
{
	private $connect;

	public function __construct($connect)
	{
		$this->connect = $connect;
	}

	public function isLogged()
	{
		return isset($_SESSION["user_id"]);
	}

	public function data($idUserToGet = null)
    {
        if ($idUserToGet == null)
        {
        	if(isset($_SESSION['user_id']))
        	{
        		$idUserToGet = $_SESSION['user_id'];
        		$statement = $this->connect->prepare('UPDATE `users` SET lastVisit = NOW() WHERE id = ?');
				$statement->bind_param('i', $idUserToGet);
				$statement->execute();
				$this->checkWarnings();
        	}
        	else
        	{
        		die("Użytkownik nie jest zalogowany!");
        	}
        }
    	$statement = $this->connect->prepare('SELECT id, login, email, displayName, lastVisit, registerDate, status, avatarHash, attempts, lastUnsuccessfulLogin, lastSuccessfulLogin, ipAddress  FROM users WHERE id = ?');
		$statement->bind_param('i', $idUserToGet);
		if($statement->execute())
		{
			$result = $statement->get_result();
			if($result->num_rows)
			{
				return $result->fetch_assoc();
			}
			else
			{
				die("Brak użytkownika o takim ID!");
			}		
		}
    }

    public function deleteWarning($idWarning)
    {
		$statement = $this->connect->prepare('DELETE FROM `warningsuser` WHERE id = ?');
		$statement->bind_param('i', $idWarning);
		$statement->execute();
    }

    public function checkWarnings($idUser = null)
    {
 		if ($idUser == null)
        {
        	if(isset($_SESSION['user_id']))
        		$idUser = $_SESSION['user_id'];
        	else
        		die("Użytkownik nie jest zalogowany!");
        }

        $warnings = $this->getUserWarnings($idUser);
        for($i = 0; $i < sizeof($warnings); $i++)
        {
			$teraz = strtotime(date("Y-m-d H:i:s"));
			$wygasa = strtotime($warnings[$i]["expDate"]);
			if($wygasa < $teraz)
			{
				$this->deleteWarning($warnings[$i]["id"]);
			}
        }

        $numberWarnings = $this->getNumberOfWarnings($idUser);

        if($numberWarnings >= 10)
        {
        	$statement = $this->connect->prepare('UPDATE `users` SET status = -1 WHERE id = ?');
			$statement->bind_param('i', $idUser);
			$statement->execute();
			die("Zostałeś zbanowany z powodu licznych ostrzeżeń!");
        }
    }



    public function getUserWarnings($idUserToGet = null)
    {
    	$return_arr = array();
		
        if ($idUserToGet == null)
        {
        	if(isset($_SESSION['user_id']))
        		$idUserToGet = $_SESSION['user_id'];
        	else
        		die("Użytkownik nie jest zalogowany!");
        }
		
    	$statement = $this->connect->prepare('SELECT id, reason, value, warningDate, expDate FROM warningsuser WHERE user_id = ?');
		$statement->bind_param('i', $idUserToGet);
		$statement->execute();
		$result = $statement->get_result();
		
		while ($row = $result->fetch_assoc()) 
		{
			$row_array['id'] = $row['id'];
			$row_array['reason'] = $row['reason'];
			$row_array['value'] = $row['value'];
			$row_array['warningDate'] = $row['warningDate'];
			$row_array['expDate'] = $row['expDate'];
			array_push($return_arr, $row_array);
		}
		return $return_arr;
    }

    public function getNumberOfPosts($idUserToGet = null)
    {
        if ($idUserToGet == null)
        {
        	if(isset($_SESSION['user_id']))
        		$idUserToGet = $_SESSION['user_id'];
        	else
        		die("Użytkownik nie jest zalogowany!");
        }

		$statement = $this->connect->prepare('select count(*) value from posts WHERE author = ?');
		$statement->bind_param('i', $idUserToGet);
		if($statement->execute())
		{
			$result = $statement->get_result();
			return $result->fetch_assoc();
		}
    }

    public function getNumberOfComments($idUserToGet = null)
    {
        if ($idUserToGet == null)
        {
        	if(isset($_SESSION['user_id']))
        		$idUserToGet = $_SESSION['user_id'];
        	else
        		die("Użytkownik nie jest zalogowany!");
        }

		$statement = $this->connect->prepare('select count(*) value from comments WHERE author = ?');
		$statement->bind_param('i', $idUserToGet);
		if($statement->execute())
		{
			$result = $statement->get_result();
			return $result->fetch_assoc();
		}
    }

    public function getNumberOfWarnings($idUserToGet = null)
    {
    	$value = 0;
        if ($idUserToGet == null)
        {
        	if(isset($_SESSION['user_id']))
        		$idUserToGet = $_SESSION['user_id'];
        	else
        		die("Użytkownik nie jest zalogowany!");
        }

		$statement = $this->connect->prepare('SELECT value FROM warningsuser WHERE user_id = ?');
		$statement->bind_param('i', $idUserToGet);
		$statement->execute();
		$result = $statement->get_result();
		
		while ($row = $result->fetch_assoc()) 
		{
			$value += $row['value'];
		}
		return $value;
    }

    public function regUser($login, $displayName, $password, $password2, $emailAddress)
    {
    	$errorString = null;

    	if(empty($login) || empty($displayName) || empty($password) || empty($password2) || empty($emailAddress))
			$errorString .= "Wypełnij wszystkie pola! <br>";

		if($password != $password2)
			$errorString .= "Podane przez Ciebie hasła różnią się. <br>";

		if(!filter_var($emailAddress, FILTER_VALIDATE_EMAIL))
			$errorString .= "Podaj poprawny adres email. <br>";

		if(strlen($login) < 3)
			$errorString .= "Login nie może być krótszy niż 3 znaki! <br>";

		if(strlen($displayName) < 3)
			$errorString .= "Nazwa wyświetlana nie może być krótsza niż 3 znaki. <br>";

		if(strlen($password) < 5)
			$errorString .= "Hasło nie może być krótsze niż 5 znaków! <br>";

		if($errorString == null)
		{
			$statement = $this->connect->prepare('SELECT login FROM users WHERE login = ?');
			$statement->bind_param('s', $login);
			if($statement->execute())
			{
				$result = $statement->get_result();
				if($result->num_rows)
				{
					$errorString .= "Login ".$login." jest już zajęty. Użyj innego!<br>";
				}
				else
				{
					$statement = $this->connect->prepare("INSERT INTO `users` (`login`, `passwordHash`, `displayName`, `registerDate`, `email`, `ipAddress`) VALUES (?, ?, ?, NOW(), ?, ?)");
					$passhash = password_hash($password, PASSWORD_DEFAULT);
					$ip_address = $_SERVER['REMOTE_ADDR'];
					$statement->bind_param('sssss', $login, $passhash, $displayName, $emailAddress, $ip_address);

					if($statement->execute())
					{
						echo "<p class='success'>Rejestracja udana! Zaloguj się.</p>";
					}
					else
						$errorString = "Coś poszło nie tak, ups... :c";
				}
			}
		}
		
		if($errorString != null)
 			echo "<p class='error'>".$errorString."</p>";
    }

	public function addWarning($userId, $reasonOfWarning, $valueOfWarning, $dataWygasniecia)
	{
		$errorString = null;

		if(strlen($reasonOfWarning) < 10)
			$errorString .= "Powód musi być dłuższy niż 10 znaków!<br>";

		if(empty($valueOfWarning))
			$errorString .= "Musisz podać wartość ostrzeżenia!<br>";

		if($valueOfWarning > 10)
			$errorString .= "Za jednym razem nie możesz dać więcej niż 10 punktów ostrzeżeń!<br>";

		if(empty($errorString))
		{
			$statement = $this->connect->prepare("INSERT INTO `warningsuser` (`user_id`, `value`, `reason`, `expDate`) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL ".$dataWygasniecia." HOUR))");
			$statement->bind_param('iis', $userId, $valueOfWarning, $reasonOfWarning);

			if($statement->execute())
			{
				echo "<p class='success'>Pomyślnie dodano ostrzeżenie!</p>";
			}
			else
				$errorString = "Coś poszło nie tak, ups... :c";
		}

		if($errorString != null)
 			echo "<p class='error'>".$errorString."</p>";
	}

	public function editProfileByAdmin($newDisplayName, $newPassword, $newEmailAddress, $userAvatar, $newStatusId, $userId, $isClear, $newLogin)
	{
		if($userAvatar['size'] > 0)
    	{
    		$avatarHash = bin2hex(random_bytes(16));
    		move_uploaded_file($userAvatar["tmp_name"], "./img/usersImg/".$avatarHash);
	    	$statement = $this->connect->prepare('UPDATE `users` SET avatarHash = ? WHERE id = ?');
			$statement->bind_param('si', $avatarHash, $userId);
			$statement->execute();
		}

		if(!empty($newPassword))
		{
			$statement = $this->connect->prepare('UPDATE `users` SET login = ?, displayName = ?, passwordHash = ?, email = ?, status = ? WHERE id = ?');
			$passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
			$statement->bind_param('ssssii', $newLogin, $newDisplayName, $passwordHash, $newEmailAddress, $newStatusId, $userId);
			$statement->execute();
		}
		else
		{
			$statement = $this->connect->prepare('UPDATE `users` SET login = ?, displayName = ?, email = ?, status = ? WHERE id = ?');
			$statement->bind_param('sssii', $newLogin, $newDisplayName, $newEmailAddress, $newStatusId, $userId);
			$statement->execute();
		}

		if($isClear == 1)
		{
			$statement = $this->connect->prepare('UPDATE `users` SET attempts = 0 WHERE id = ?');
			$statement->bind_param('i', $userId);
			$statement->execute();
		}
		header('Location: '.$_SERVER["HTTP_REFERER"]);
	}

    public function editProfile($newDisplayName, $newPassword, $newPassword2, $oldPassword, $newEmailAddress, $userAvatar)
    {
    	$errorString = null;
    	if(empty($oldPassword)) 
    		$errorString .= "Wprowadź stare hasło w celu potwierdzenia! <br>";

    	if($newPassword != $newPassword2)
    		$errorString .= "Podane hasła nie są jednakowe. <br>";

    	if(!empty($newPassword) && strlen($newPassword) < 5)
    		$errorString .= "Hasło musi mieć minimalną długość 3 znaków.. <br>";

    	if(!filter_var($newEmailAddress, FILTER_VALIDATE_EMAIL))
    		$errorString .= "Podaj poprawny adres email. <br>";

    	if($userAvatar['size'] > 0)
    	{
    		$check = getimagesize($userAvatar["tmp_name"]); //podobno czasami źle wykrywa image
			if($check == false) 
		        $errorString .= "Wybrany plik nie jest obrazkiem!<br>";
		   	else
		   	{
		   		if($userAvatar['size'] > 100000)
		   			$errorString .= "Plik jest zbyt duży. Zadbaj o kompresję! (maks. 100kB)<br>";
		   		else
		   		{
		   			$avatarHash = bin2hex(random_bytes(16));
		    		$tmpName = $userAvatar['tmp_name'];
		    		list($width, $height, $type, $attr) = getimagesize($tmpName);
		    		if($width != 100 || $height != 100)
		    			$errorString .= "Avatar musi mieć wymiary 100x100.<br>";
		   		}
		   	}
    	}

    	if($errorString == null)
    	{
    		$userId = $_SESSION['user_id'];
    		$statement = $this->connect->prepare('SELECT passwordHash, login, avatarHash FROM users WHERE id = ?');
			$statement->bind_param('i', $userId);
			if($statement->execute())
			{
				$result = $statement->get_result();
				$usernameData = $result->fetch_assoc();
				if(!$result->num_rows)
				{
					$errorString = "Takie konto nie istnieje.";
				}
				else 
				{
					if(password_verify($oldPassword, $usernameData["passwordHash"]))
					{
						$profile = $this->data();
						if($userAvatar['size'] > 0 && $userAvatar['error'] == 0 && $profile["status"] > 0)
						{
							move_uploaded_file($userAvatar["tmp_name"], "./img/usersImg/".$avatarHash);
		    				$statement = $this->connect->prepare('UPDATE `users` SET avatarHash = ? WHERE id = ?');
							$statement->bind_param('si', $avatarHash, $_SESSION['user_id']);
							$statement->execute();
						}
		    			
						if(!empty($newPassword))
						{
							$statement = $this->connect->prepare('UPDATE `users` SET displayName = ?, passwordHash = ?, email = ? WHERE id = ?');
							$passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
							$statement->bind_param('sssi', $newDisplayName, $passwordHash, $newEmailAddress, $userId);
						}
						else
						{
							$statement = $this->connect->prepare('UPDATE `users` SET displayName = ?, email = ? WHERE id = ?');
							$statement->bind_param('ssi', $newDisplayName, $newEmailAddress, $userId);
						}
							
						if($statement->execute())
						{
							header('Location: '.$_SERVER["HTTP_REFERER"]);
							//header('Location: http://'.$_SERVER["HTTP_HOST"]);
						}
						else
							$errorString = "Błąd z zapytaniem!";
					}
					else
						$errorString .= "Hasło weryfikacyjne zostało odrzucone.";
				}
			}
    	}

    	if($errorString != null)
 			echo "<p class='error'>".$errorString."</p>";
    }

    public function remindPassword($emailAddress)
    {
    	$errorString = null;

		if(empty($emailAddress))
		{
			$errorString .= "Wypełnij wszystkie pola! <br>";
		}

		if(!filter_var($emailAddress, FILTER_VALIDATE_EMAIL))
    		$errorString .= "Podaj poprawny adres email. <br>";

		if($errorString == null)
		{
			$statement = $this->connect->prepare('SELECT id FROM users WHERE email = ?');
			$statement->bind_param('s', $emailAddress);
			if($statement->execute())
			{
				$result = $statement->get_result();
				if(!$result->num_rows)
				{
					$errorString = "Nie znaleziono podanego maila w naszej bazie.";
				}
				else
				{
					$newPassword = bin2hex(random_bytes(4));
					echo $newPassword;
					$passhash = password_hash($newPassword, PASSWORD_DEFAULT);
					$statement = $this->connect->prepare('UPDATE `users` SET passwordHash = ? WHERE email = ?');
					$statement->bind_param('ss', $passhash, $emailAddress);
					if($statement->execute())
					{
						$to      = $emailAddress;
						$subject = '[myWebApp] Reset hasła!';
						$message = 'Cześć!\r\nRozpocząłeś procedurę przypomnienia hasła.\r\nTwoje nowe hasło to:\r\n'.$newPassword.'\r\nPozdrawiamy,\r\nTeam developerów!';
						$headers = 'From: kontakt@projektiab.usermd.net' . "\r\n" .
						    'Reply-To: kontakt@projektiab.usermd.net' . "\r\n" .
						    'X-Mailer: PHP/' . phpversion();
						mail($to, $subject, $message, $headers);
						echo "<p class='success'>Wysłaliśmy do Ciebie maila z nowym hasłem!</p>";
					}
				}
			}
		}

		if($errorString != null)
 			echo "<p class='error'>".$errorString."</p>";

    }

    public function listUsers()
    {
		$statement = $this->connect->prepare('SELECT id, login, email, displayName, lastVisit, registerDate, status, avatarHash, attempts, lastUnsuccessfulLogin, lastSuccessfulLogin, ipAddress FROM users');
		if($statement->execute())
		{
			$result = $statement->get_result();
			if(!$result->num_rows)
			{
				return 'Błąd zapytania';
			}
			else
			{
				$rows = resultToArray($result);
				return $rows;
			}
		}
    }

	public function logIn($login, $passwordHash)
	{
		if(empty($login) || empty($passwordHash))
		{
			echo "Wypełnij wszystkie pola!";
		}
		else
		{
			$statement = $this->connect->prepare('SELECT id, attempts, lastUnsuccessfulLogin, passwordHash, login FROM users WHERE login = ?');
			$statement->bind_param('s', $login);
			if($statement->execute())
			{
				$result = $statement->get_result();
				if(!$result->num_rows)
				{
					echo "Takie konto nie istnieje.";
				}
				else
				{
					$usernameData = $result->fetch_assoc();
					if($usernameData["attempts"] > 3 && strtotime($usernameData["lastUnsuccessfulLogin"]) + 300>= time()) //300 sekund - czas dodatkowy, aby uniknąć brute force
					{
						echo "Próbowałeś zalogować się zbyt wiele razy. Spróbuj ponownie za 5 minut.";
					}
					else if(password_verify($passwordHash, $usernameData["passwordHash"]))
					{
						$_SESSION["user_id"] = $usernameData["id"];
						$ip_address = $_SERVER['REMOTE_ADDR'];
						$statement = $this->connect->prepare('UPDATE `users` SET lastSuccessfulLogin = NOW(), attempts = 0, ipAddress = ? WHERE login = ?');
						$statement->bind_param('ss', $ip_address, $usernameData["login"]);
						$statement->execute();
						echo "Zalogowałeś się!";
						header('Location: '.$_SERVER["HTTP_REFERER"]);
					}
					else
					{
						echo "Błędny login lub hasło!";
						$statement = $this->connect->prepare('UPDATE `users` SET lastUnsuccessfulLogin = NOW(), attempts = attempts + 1 WHERE login = ?');
						$statement->bind_param('s', $usernameData["login"]);
						$statement->execute();
					}
				}
			}
		}
	}
}