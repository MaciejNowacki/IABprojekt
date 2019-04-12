<?php
session_start();
session_destroy();
header('Location: '.$_SERVER["HTTP_REFERER"]);
//header('Location: http://'.$_SERVER["HTTP_HOST"]);
?>