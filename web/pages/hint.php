<?php
$size = isset($_POST['size']) ? (int)$_POST['size'] : 0;
$map  = isset($_POST['map']) ? $_POST['map'] : '';
$cars = isset($_POST['cars']) ? $_POST['cars'] : '';

$executable = __DIR__ . DIRECTORY_SEPARATOR . "solveur.exe";


$command = escapeshellcmd($executable) . " " . $size . " " . escapeshellarg($map) . " " . escapeshellarg($cars);

$result = shell_exec($command);
header('Content-Type: application/json');
echo trim($result);