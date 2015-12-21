<?php

$name = date('YmdHis');
$newname = "lib/cam/images/" . $name . ".jpg";
$file = file_put_contents($newname, file_get_contents('php://input'));
if (!$file) {
    print "ERROR: Failed to write data to $filename, check permissions\n";
    exit();
}

$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $newname;
print "$url\n";
?>
