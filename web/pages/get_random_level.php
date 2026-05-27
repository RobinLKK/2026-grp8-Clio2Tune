<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$size = isset($_GET['size']) ? (int)$_GET['size'] : 8;
$executable = __DIR__ . DIRECTORY_SEPARATOR . "generate.exe";

// 2>&1 permet de voir si le .exe renvoie une erreur système
$command = escapeshellcmd($executable) . " " . $size . " 2>&1";
$result = shell_exec($command . " 2>&1"); // Le 2>&1 affiche l'erreur C s'il y en a une

header('Content-Type: application/json');
if ($result) {
    echo $result;
} else {
    echo json_encode(["error" => "Le generateur n'a rien renvoye"]);
}