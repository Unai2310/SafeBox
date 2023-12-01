<?php
$file = $_FILES['file'];
$file['loco'] = $file['name'];
$file['name'] = "odjaosidjoadjoiadjsoij";
echo json_encode($file);
?>