<?php
require_once('includes/global.inc.php');
include_once('includes/helpers.php');

// Check for authorization
if (!isset($_SESSION['user']) && !request_okay($_GET, 'public-box') && !isset($_SESSION['user-email'])) {
    if (!isset($_SESSION['user-email']) && isset($_GET['key']) ) { // If the user goes straight to a URL without logging in
        $_SESSION['alert'] =
            '<div class="alert fade in alert-error">
                <button class="close" data-dismiss="alert" type="button">&times;</button>
                Please confirm your email address with the key <strong>' . $_GET['key'] .
            "</strong></div>\n";
    }
    header("Location: login.php");
    die();
}

include_once('includes/dirlisting.php');

if (isset($_SESSION['user'])) {
    $box = $_SESSION['user'];
    $home = $box;
    $home_href = strip_tags($_SERVER['PHP_SELF']);
}
if (request_okay($_GET, 'public-box')) {
    $box = rtrim($_GET['public-box'], '/');
    $home = 'public';
    $home_href = strip_tags($_SERVER['PHP_SELF']) . "?public-box=" . urlencode("$box/");
}
if (request_okay($_GET, 'key')) {
    $key = $_GET['key'];
    $box = $userTools->getOwnerFromKey($key);
    $home = 'private';
    $home_href = strip_tags($_SERVER['PHP_SELF']) . "?key=" . urlencode($_GET['key']);
}

$title = "Webbox";
include_once('includes/header.php');

if (isset($_SESSION['alert'])) {
    echo $_SESSION['alert'];
    unset($_SESSION['alert']);
}
$current_dir = str_replace($startdir, '', $leadon);
$_SESSION['current-dir'] = $current_dir;
// Track current dir
$isPublic = $isPrivate = $isP = False;
if ($dirok && $_GET['dir'] == 'public/' || request_okay($_GET, 'public-box')) {
    $isPublic = True;
    $isPrivate = False;
    if (!request_okay($_GET, 'public-box'))
        $isP = True;
} else if ($dirok && $_GET['dir'] == 'private/') {
    $isPublic = False;
    $isPrivate = True;
    $isP = True;
}

?>
<div>
    <h2><?php echo $box; ?>@cs.ubc.ca</h2>
    <br>
    <!-- Breadcrumb -->
    <ul class="breadcrumb">
        <li>
            <a href="<?php echo $home_href; ?>"><?php echo $home; ?></a>
            <span class="divider">/</span>
        </li>
        <?php
        $breadcrumbs = split('/', $current_dir);
        if(($bsize = sizeof($breadcrumbs))>0) {
            $sofar = '';
            for($bi=0;$bi<($bsize-1);$bi++) {
                $sofar = $sofar . $breadcrumbs[$bi] . '/';
                if (request_okay($_GET, 'public-box')) {
                    print('<li>
                        <a href="'.strip_tags($_SERVER['PHP_SELF']).'?dir='.urlencode($sofar).'&amp;public-box='.$box .'">'.
                        $breadcrumbs[$bi] . '</a>
                            <span class="divider">/</span>
                        </li>');
                } else if (request_okay($_GET, 'key')) {
                    print('<li>
                        <a href="'.strip_tags($_SERVER['PHP_SELF']).'?dir='.urlencode($sofar). '&amp;key='. $key .'">'
                        .$breadcrumbs[$bi].'</a>
                            <span class="divider">/</span>
                        </li>');
                } else {
                    print('<li>
                            <a href="'.strip_tags($_SERVER['PHP_SELF']).'?dir='.urlencode($sofar).'">'.$breadcrumbs[$bi].'</a>
                            <span class="divider">/</span>
                        </li>');
                }
            }
        }
        $baseurl = strip_tags($_SERVER['PHP_SELF']) . '?dir='.strip_tags($_GET['dir']) . '&amp;';
        ?>
    </ul>
    <!-- /breadcrumb -->

    <!-- Table listing -->
    <?php if(isset($_SESSION['user']) && !request_okay($_GET, 'public-box')) {
        include_once('includes/authorized_users.php');
    } else if(request_okay($_GET, 'public-box')) {
        include_once('includes/public_users.php');
    } else if(request_okay($_GET, 'key')) {
        include_once('includes/private_users.php');
    }?>
    <!-- /Table listing -->

    </div>

    <br><br>

    <!-- Upload -->
    <?php
    // Show upload when there's permission and we're not at the $startdir
    if($allowuploads && ($isPublic || !$isP) && !request_okay($_GET, 'public-box') && $dirok) {
        $phpallowuploads = (bool) ini_get('file_uploads');
        $phpmaxsize = ini_get('upload_max_filesize');
        $phpmaxsize = trim($phpmaxsize);
        $last = strtolower($phpmaxsize{strlen($phpmaxsize)-1});
        switch($last) {
        case 'g':
            $phpmaxsize *= 1024;
        case 'm':
            $phpmaxsize *= 1024;
        }
    ?>
    <div>
        <h3>Upload files</h3> <!--(Max Filesize: <?php echo $phpmaxsize;?>KB)-->
        <?php if($uploaderror) echo '<div class="upload-error">'.$uploaderror.'</div>'; ?>
        <form method="post"
            action="upload.php?dir=<?php echo urlencode($leadon);?>"
            enctype="multipart/form-data">
            <input type="file" multiple name="upload_files[]" /><br>
            <input type="submit" value="Upload" class="btn"/>
        </form>
    </div>
    <?php
    }
    ?>

<?php include_once('includes/footer.php'); ?>
