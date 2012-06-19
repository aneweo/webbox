<?php
$title = "Authentication";
$current_page = "login";
include_once("includes/header.php");
include_once("includes/authenticate.php");
require_once("includes/global.inc.php");

// check to see if user is logging out
if(isset($_GET['out'])) {
    // destroy session
    session_unset();
    $_SESSION = array();
    unset($_SESSION['user']);
    unset($_SESSION['user-email']);
    // Clean up session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
// check to see if login form has been submitted
if(isset($_POST['userLogin'])){
    // run information through authenticator
    if(authenticate($_POST['userLogin'],$_POST['userPassword']))
    {
        // authentication passed
        $_SESSION['alert'] = '<div class="alert fade in">
            <button class="close" data-dismiss="alert" type="button">&times;</button>
            Logged in successfully.
           </div>' . "\n";
        header("Location: index.php");
        die();
    } else {
        // authentication failed
        $error = 1;
    }
}
// check to see if the user logs in using the key instead
// TODO: fix me
if(isset($_POST['key'])) {
    $email = $_POST['email'];
    $key = $_POST['key'];
    if($userTools->validKey($email, $key)) {
        $_SESSION['user-email'] = $email;
        header("Location: index.php?key=$key");
        die();
    } else {
        $error = 1;
    }
}
// finally check if the user wants to see the public box instead
if(isset($_POST['public-box'])) {
    $box = $_POST['public-box'] . "/";
    header("Location: index.php?public-box=$box");
    die();
}
// output error to user
if (isset($error)) 
    print('<div class="alert fade in alert-error">
            <button class="close" data-dismiss="alert" type="button">&times;</button>
            <strong>Error: </strong>Wrong credentials. Please try again.
           </div>' . "\n");
// output logout success
if (isset($_GET['out']))
    print('<div class="alert fade in">
            <button class="close" data-dismiss="alert" type="button">&times;</button>
            Logout successfully. Please refresh.
           </div>' . "\n");

if (isset($_SESSION['alert'])) {
    echo $_SESSION['alert'];
    unset($_SESSION['alert']);
}
?>
<p><strong>UBC CS department</strong></p>
<form method="post" action="login.php" class="well form-inline">
    <input type="text" class="input-small" name="userLogin" placeholder="Username" required/>
    <input type="password" class="input-small" name="userPassword" placeholder="Password" required/>
    <button type="submit" name="submit" class="btn">Sign in</button>
</form>
<hr>
<p id="private-key">Log in with <strong>private key</strong></p>
<form method="post" action="login.php" class="well form-inline">
    <input type="email" class="input-medium" name="email" placeholder="example@mail.com" required/>
    <input type="text" class="input-large" name="key" placeholder="private key" <?php if (isset($_SESSION['key'])) echo 'value="' . $_SESSION['key'] . '"'; ?> required/>
    <button type="submit" name="submit" class="btn">Submit</button>
</form>
<hr>
<p>To view <strong>public box</strong>, please enter the box name</p>
<form method="post" action="login.php" class="well form-inline">
    <input type="text" class="input-small" name="public-box" placeholder="johndoe" required/>
    <button type="submit" name="submit" class="btn">Submit</button>
</form>
<?php include_once("includes/footer.php"); ?>
