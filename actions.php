<?php
require_once 'includes/global.inc.php';

$alert = '';
$dir = $_GET['dir'];
if (!isset($_POST['new-folder-name'])) {
    $checked_items = $_POST["checked_items"];
}
// Delete action
if (isset($_POST["delete"])) {
    $items = array();
    $deleted_paths = array();
    $deleted_items = array();
    // Loop through the checked items and delete them
    foreach ($checked_items as $item_path) {
        $item_name = basename($item_path);
        $items[] = $item_name;
        if(!is_dir($item_path) && unlink($item_path)) {
            $deleted_paths[] = $item_path;
            $deleted_items[] = $item_name;
        } else if (is_dir($item_path) && rmdir($item_path)) {
            if (in_array($_SESSION['current-dir'], array('public/', 'private/'))) {
                $userTools->deleteFolder($_SESSION['user'], $_SESSION['current-dir'] . $item_name);
            }
            $deleted_paths[] = $item_path;
            $deleted_items[] = $item_name;
        }
    }
    if (!empty($deleted_items)) {
        $alert .= '<div class="alert fade in">
            <button class="close" data-dismiss="alert" type="button">&times;</button>
            The items <strong>' . implode(", ", $deleted_items) . '</strong> have been deleted</div>' . "\n";
    }
    if ($diff = array_diff($items, $deleted_items)) {
        $alert .= '<div class="alert fade in alert-error">
                <button class="close" data-dismiss="alert" type="button">&times;</button>
                <strong>Error: </strong> You cannot delete the selected items: <strong>' . implode(", ", $diff) .
                '</strong>. Please check if the folders are empty or contact the admin.</div>';
    }
// Download action
} else if(isset($_POST["download"])) {
    if (count($checked_items) > 1) {
        $zip_file = "downloads/webbox.zip";
        if (file_exists($zip_file)) {
            unlink($zip_file);
        }
        $zip = new ziparchive;
        if ($zip->open($zip_file, ziparchive::CREATE) != 0) {
            foreach ($checked_items as $file) {
                if($zip->addfile($file, basename($file)) == 0) {
                    $alert .=
                        '<div class="alert fade in alert-error">
                            <button class="close" data-dismiss="alert" type="button">&times;</button>
                            <strong>error</strong> in archiving multiple files. please try again later or contact the admin.
                        </div>' . "\n";
                    include 'public.php';
                    exit();
                }
            }
            $zip->close();
            header("pragma: public");
            header("expires: 0");
            header("cache-control: must-revalidate, post-check=0, pre-check=0");
            header("cache-control: private",false);
            header("content-type: application/zip");
            header("content-disposition: attachment; filename=" . basename($zip_file));
            header("content-transfer-encoding: binary");
            header("content-length: " . filesize($zip_file));
            ob_clean();
            flush();
            readfile("$zip_file");
        } else {
            $alert .=
             '<div class="alert fade in alert-error">
                <button class="close" data-dismiss="alert" type="button">&times;</button>
                <strong>Error</strong> in archiving multiple files. please try again later or contact the admin.
            </div>' . "\n";
        }
    } else if (count($checked_items) == 1) {
        $file = $checked_items[0];
        header('content-type: ' . mime_content_type($file));
        header('content-disposition: attachment; filename="' . basename($file) . '"');
        header("pragma: no-cache");
        header("expires: 0");
        header('content-length: ' . filesize($file));
        ob_clean();
        flush();
        readfile("$file");
    }
} else if (isset($_POST['new-folder-name'])) { // create new folder
    $name = $_POST['new-folder-name'];
    $full_path = "boxes/" . $_SESSION['user'] . "/$dir" . $name;
    if (mkdir($full_path, 0777)) {
        $alert .=
            '<div class="alert fade in alert-info">
                <button class="close" data-dismiss="alert" type="button">&times;</button>
                Folder <strong>' . $name . '</strong> has been created.
            </div>' . "\n";
        $dir = $dir . $name . '/';
        $userTools->createFolder($_SESSION['user'], $dir);
    } else {
        $alert .=
            '<div class="alert fade in alert-error">
                <button class="close" data-dismiss="alert" type="button">&times;</button>
                <strong>Error: </strong>Folder <strong>' . $name . '</strong> could not be created. Please try again later, or contact the admin.
            </div>' . "\n";
    }
} else if (isset($_POST['access'])) { // grant access
    $grantedUsers = array_map('trim', explode(",",$_POST["granted-users"]));
    // Sanitize the array
    foreach ($grantedUsers as $key => $val) {
        if (empty($val)) {
            unset($grantedUsers[$key]);
        }
    }
    $removedUsers = array_map('trim', explode(",", $_POST["removed-users"]));
    // Sanitize the array
    foreach ($removedUsers as $key => $val) {
        // Also you don't want to remove access from yourself
        if (empty($val) || $val == $_SESSION['user']) {
            unset($removedUsers[$key]);
        }
    }

    foreach ($checked_items as $folder) {
        // TODO: Email each granted user with their key
        foreach ($grantedUsers as $user) {
            $key = $userTools->generateKey();
            $userTools->grantAccess($_SESSION['user'], $_SESSION['current-dir'] . basename($folder) . '/', $user, $key);
        }
        $userTools->removeAccess($_SESSION['user'], $_SESSION['current-dir'] . basename($folder) . '/', $removedUsers);
    }
    if (!empty($grantedUsers)) {
        $alert .=
        '<div class="alert fade in">
            <button class="close" data-dismiss="alert" type="button">&times;</button>
            <strong>Access Granted.</strong> The user(s) <strong>' . implode(', ', $grantedUsers) .
            '</strong> have been granted access to view your file(s)<br>
        </div>' . "\n";

    }
    if (!empty($removedUsers))
        $alert .=
        '<div class="alert fade in">
            <button class="close" data-dismiss="alert" type="button">&times;</button>
            <strong>Access Removed.</strong> The user(s) <strong>' . implode(', ', $removedUsers) .
            '</strong> can no longer view your folder(s).
        </div>' . "\n";
}
$_SESSION['alert'] = $alert;
header("Location: index.php?dir=$dir");
die();

