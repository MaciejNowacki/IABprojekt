<?php
session_start();
require "User.php";
require "Post.php";

$connect = @new mysqli("localhost", "root", "", "database");
if($connect->connect_errno)
{
	die("[ERROR #".$connect->connect_errno."] ".$connect->connect_error);
}
$connect->set_charset("utf8");

$user = new User($connect);
$post = new Post($connect);

if($user->isLogged())
{
	$profile = $user->data();
	if($profile["status"] == -1) die("Zostałeś zbanowany!");
}
?>