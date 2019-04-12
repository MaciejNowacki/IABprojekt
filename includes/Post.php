<?php
class Post
{
	private $connect;

	public function __construct($connect)
	{
		$this->connect = $connect;
	}

	public function getCommentsOfPost($postId)
	{
		$return_arr = array();
    	$statement = $this->connect->prepare('SELECT c.id id1, c.createDate, u.avatarHash, c.text, c.author id2, u.displayName, c.visible FROM comments c LEFT JOIN users u ON c.author = u.id WHERE c.post_id = ? ORDER BY c.id DESC');
    	$statement->bind_param('i', $postId);
		$statement->execute();
		$result = $statement->get_result();
		
		while ($row = $result->fetch_assoc()) 
		{
			$row_array['id1'] = $row['id1']; //id komentarza
			$row_array['id2'] = $row['id2']; //id usera
			$row_array['displayName'] = $row['displayName']; //nick usera
			$row_array['createDate'] = $row['createDate'];
			$row_array['text'] = $row['text'];
			$row_array['avatarHash'] = $row['avatarHash'];
			$row_array['visible'] = $row['visible'];
			array_push($return_arr, $row_array);
		}
		return $return_arr;
	}

	public function deleteAllCommentsOfPost($postId)
	{
    	$statement = $this->connect->prepare('SELECT id FROM comments WHERE post_id = ?');
    	$statement->bind_param('i', $postId);
		$statement->execute();
		$result = $statement->get_result();
		
		while ($row = $result->fetch_assoc()) 
		{
			$this->deleteCommentWithoutHeader($row['id']);
		}
	}

	public function getLatestNews()
	{
		$return_arr = array();
    	$statement = $this->connect->prepare('SELECT posts.id, posts.author id1, u1.displayName d1, posts.createDate, posts.text, posts.title, posts.lastModerate, posts.moderateBy, u2.displayName d2, posts.moderateBy id2, posts.visible FROM posts LEFT JOIN users u1 ON posts.author = u1.id LEFT JOIN users u2 ON posts.moderateBy = u2.id ORDER BY `posts`.`createDate` DESC');
		$statement->execute();
		$result = $statement->get_result();
		
		while ($row = $result->fetch_assoc()) 
		{
			$row_array['id'] = $row['id'];
			$row_array['id1'] = $row['id1'];
			$row_array['id2'] = $row['id2'];
			$row_array['displayName'] = $row['d1'];
			$row_array['createDate'] = $row['createDate'];
			$row_array['text'] = $row['text'];
			$row_array['title'] = $row['title'];
			$row_array['lastModerate'] = $row['lastModerate'];
			$row_array['moderateBy'] = $row['d2'];
			$row_array['visible'] = $row['visible'];
			array_push($return_arr, $row_array);
		}
		return $return_arr;
    }

    public function getPost($postId)
	{
    	$statement = $this->connect->prepare('SELECT posts.id, posts.author, u1.displayName d1, posts.createDate, posts.text, posts.title, posts.visible, posts.lastModerate, posts.moderateBy, u2.displayName d2 FROM posts LEFT JOIN users u1 ON posts.author = u1.id LEFT JOIN users u2 ON posts.moderateBy = u2.id WHERE posts.id = ?');
    	$statement->bind_param('i', $postId);
		if($statement->execute())
		{
			$result = $statement->get_result();
			if(!$result->num_rows)
				die("News o podanym ID nie istnieje!");
			else
				return $result->fetch_assoc();
		}		
    }

    public function getComment($commentId)
	{
    	$statement = $this->connect->prepare('SELECT text, author, visible FROM comments WHERE id = ?');
    	$statement->bind_param('i', $commentId);
		if($statement->execute())
		{
			$result = $statement->get_result();
			if(!$result->num_rows)
				die("Komentarz o podanym ID nie istnieje!");
			else
				return $result->fetch_assoc();
		}		
    }

    public function deleteComment($commentId)
    {
		$statement = $this->connect->prepare('DELETE FROM `comments` WHERE id = ?');
		$statement->bind_param('i', $commentId);
		$statement->execute();
		header('Location: '.$_SERVER["HTTP_REFERER"]);
    }

    public function deleteCommentWithoutHeader($commentId)
    {
		$statement = $this->connect->prepare('DELETE FROM `comments` WHERE id = ?');
		$statement->bind_param('i', $commentId);
		$statement->execute();
		header('Location: '.$_SERVER["HTTP_REFERER"]);
    }

    public function setVisibleFlag($flag, $commentId)
    {
		$statement = $this->connect->prepare('UPDATE `comments` SET visible = ? WHERE id = ?');
		$statement->bind_param('ii', $flag, $commentId);
		$statement->execute();
		header('Location: '.$_SERVER["HTTP_REFERER"]);
    }

    public function editComment($text, $commentId)
    {
    	$errorString = null;

    	if(strlen($text) < 10)
			$errorString .= "Treść nie może być krótsza niż 10 znaków!<br>";

		if(strlen($text) > 500)
			$errorString .= "Treść nie może być dłuższa niż 500 znaków!<br>";

		if($errorString == null)
    	{
    		$statement = $this->connect->prepare('SELECT id FROM comments WHERE id = ?');
			$statement->bind_param('i', $commentId);
			if($statement->execute())
			{
				$result = $statement->get_result();
				if(!$result->num_rows)
				{
					$errorString = "Komentarz o podanym ID nie istnieje!";
				}
				else 
				{
					$statement = $this->connect->prepare('UPDATE `comments` SET text = ? WHERE id = ?');
					$statement->bind_param('si', $text, $commentId);
					if(!$statement->execute())
						$errorString = "Błąd z zapytaniem!";
					header('Location: '.$_SERVER["HTTP_REFERER"]);
				}
			}
			else
				$errorString = "Błąd z zapytaniem!";
		}

		if($errorString != null)
 			echo "<p class='error'>".$errorString."</p>";
    }

    public function editNews($title, $text, $visible, $createDate, $lastModerate, $moderateBy, $postId, $deleting)
    {
    	$errorString = null;
    	if($deleting == 1)
    	{
    		$this->deleteAllCommentsOfPost($postId);
			$statement = $this->connect->prepare('DELETE FROM `posts` WHERE id = ?');
			$statement->bind_param('i', $postId);
			$statement->execute();
			header('Location: ./index.php');
   		}

    	if(strlen($title) < 3)
			$errorString .= "Tytuł musi zawierać co najmniej 3 znaki!<br>";

		if(strlen($title) > 250)
			$errorString .= "Tytuł może mieć maksymalnie 250 znaków!<br>";

		if(strlen($text) < 100)
			$errorString .= "Treść nie może być krótsza niż 100 znaków.<br>";
		
    	if($errorString == null)
    	{
    		$userId = $_SESSION['user_id'];
    		$statement = $this->connect->prepare('SELECT id FROM posts WHERE id = ?');
			$statement->bind_param('i', $postId);
			if($statement->execute())
			{
				$result = $statement->get_result();
				$usernameData = $result->fetch_assoc();
				if(!$result->num_rows)
				{
					$errorString = "Post o podanym ID nie istnieje!";
				}
				else 
				{
					$statement = $this->connect->prepare('UPDATE `posts` SET text = ?, title = ?, lastModerate = NOW(), moderateBy = ? WHERE id = ?');
					$statement->bind_param('ssii', $text, $title, $userId, $postId);
					if(!$statement->execute())
						$errorString = "Błąd z zapytaniem!";

					if($visible != null)
					{
						$statement = $this->connect->prepare('UPDATE `posts` SET visible = ? WHERE id = ?');
						$statement->bind_param('ii', $visible, $postId);
						if(!$statement->execute())
							$errorString = "Błąd z zapytaniem!";
					}

					if($createDate != null)
					{
						$statement = $this->connect->prepare('UPDATE `posts` SET createDate = ? WHERE id = ?');
						$statement->bind_param('si', $createDate, $postId);
						if(!$statement->execute())
							$errorString = "Błąd z zapytaniem!";

						if(!empty($moderateBy))
						{
							if(empty($lastModerate))
							{
								$statement = $this->connect->prepare('UPDATE `posts` SET moderateBy = ?, lastModerate = NOW() WHERE id = ?');
								$statement->bind_param('ii', $moderateBy, $postId);
								if(!$statement->execute())
									$errorString = "Błąd z zapytaniem!";
							}
							else
							{
								$statement = $this->connect->prepare('UPDATE `posts` SET moderateBy = ?, lastModerate = ? WHERE id = ?');
								$statement->bind_param('isi', $moderateBy, $lastModerate, $postId);
								if(!$statement->execute())
									$errorString = "Błąd z zapytaniem!";
							}
						}
						else
						{
							$statement = $this->connect->prepare("UPDATE `posts` SET moderateBy = null, lastModerate = '' WHERE id = ?");
							$statement->bind_param('i', $postId);
							if(!$statement->execute())
								$errorString = "Błąd z zapytaniem!";
						}
					}

					header('Location: '.$_SERVER["HTTP_REFERER"]);
				}
			}
			else
				$errorString = "Błąd z zapytaniem!";
		}

		if($errorString != null)
 			echo "<p class='error'>".$errorString."</p>";
    }

    public function addNewPost($title, $text)
	{
		$errorString = null;

		if(strlen($title) < 3)
			$errorString .= "Tytuł musi zawierać co najmniej 3 znaki!<br>";

		if(strlen($title) > 250)
			$errorString .= "Tytuł może mieć maksymalnie 250 znaków!<br>";

		if(strlen($text) < 100)
			$errorString .= "Treść nie może być krótsza niż 100 znaków.<br>";

		if(empty($errorString))
		{
			$userId = $_SESSION['user_id'];
			$statement = $this->connect->prepare("INSERT INTO `posts` (`createDate`, `author`, `text`, `title`) VALUES (NOW(), ?, ?, ?)");
			$statement->bind_param('iss', $userId, $text, $title);

			if($statement->execute())
			{
				echo "<p class='success'>Pomyślnie dodano posta!</p>";
			}
			else
				$errorString = "Coś poszło nie tak, ups... :c";
		}

		if($errorString != null)
 			echo "<p class='error'>".$errorString."</p>";
    }

    public function addNewComment($text, $postId)
	{
		$errorString = null;

    	if(strlen($text) < 10)
			$errorString .= "Treść nie może być krótsza niż 10 znaków!<br>";

		if(strlen($text) > 500)
			$errorString .= "Treść nie może być dłuższa niż 500 znaków!<br>";

		if(empty($errorString))
		{
			$userId = $_SESSION['user_id'];
			$statement = $this->connect->prepare("INSERT INTO `comments` (`post_id`, `createDate`, `author`, `text`) VALUES (?, NOW(), ?, ?)");
			$statement->bind_param('iis', $postId, $userId, $text);

			if($statement->execute())
			{
				echo "<p class='success'>Pomyślnie dodano komentarz!</p>";
			}
			else
				$errorString = "Coś poszło nie tak, ups... :c";
		}

		if($errorString != null)
 			echo "<p class='error'>".$errorString."</p>";
    }
}