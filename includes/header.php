<?php
require_once('includes/global.inc.php');

$ROOT = "/~baopham/webbox";

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $session = '<a href="login.php?out=signout">Sign Out</a>';
} else if (isset($_SESSION['user-email'])) {
    $user = $_SESSION['user-email'];
    $session = '<a href="login.php?out=signout">Sign Out</a>';
} else {
    $user = '';
    $session = '<a href="login.php">Sign In</a>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $title ?></title>
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
</head>
<body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="brand" href="<?php echo $ROOT; ?>/">Webbox</a>
          <ul class="nav">
          <li><a href="index.php">Home</a></li>
          </ul>
          <div class="btn-group pull-right">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
              <i class="icon-user"></i> <?php echo $user; ?>
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><?php echo $session; ?></li>
              <li class="divider"></li>
              <li><a href="login.php">Login page</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="page">
        <div class="container">
