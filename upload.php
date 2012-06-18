<?php

require_once 'includes/global.inc.php';

$dir = $_GET['dir'];
$alert = '';
$file_names = $_FILES["upload_files"]["name"];
$successful = array();
$idx = 0;
foreach ($file_names as $file) {
    $file_path = $dir . $file;
    if (!file_exists($file_path) && move_uploaded_file($_FILES["upload_files"]["tmp_name"][$idx], $file_path)) {
        $successful[] = $file;
    }
    $idx += 1;
}
if (!empty($successful)) {
    $alert .=
        '<div class="alert fade in alert-info">
            <button class="close" data-dismiss="alert" type="button">&times;</button>
            The items <strong>' . implode(", ", $successful) . '</strong> have been uploaded
        </div>' . "\n";
}
if ($diff = array_diff($file_names, $successful)) {
    $alert .=
        '<div class="alert fade in alert-error">
            <button class="close" data-dismiss="alert" type="button">&times;</button>
            <strong>Upload Error: </strong>The selected items: <strong>' . implode(", ", $diff) .
        '</strong> could not be uploaded. Please check if they already exist or contact the admin.
        </div>' . "\n";
}

$_SESSION['alert'] = $alert;
header("Location: index.php?dir=" . urlencode($_SESSION['current-dir']));
die();

