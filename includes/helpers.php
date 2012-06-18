<?php
function format_bytes($size) {
    $units = array(' B', ' KB', ' MB', ' GB', ' TB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
    return round($size, 2).$units[$i];
}
// Check if request is valid
function request_okay($type, $keyname) {
    if (isset($type[$keyname]) && !empty($type[$keyname])) return True;
    return False;
}
