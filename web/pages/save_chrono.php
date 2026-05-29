<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Méthode non autorisée']));
}


if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Non connecté']));
}

$id_niveau      = intval($_POST['id_niveau'] ?? 0);
$chrono         = intval($_POST['chrono'] ?? 0);
$id_utilisateur = intval($_SESSION['user_id']);


if ($id_niveau <= 0 || $chrono <= 0) {
    http_response_code(400);
    exit(json_encode(['error' => 'Données invalides']));
}

try {
   
    $checkNiveau = $pdo->prepare("SELECT COUNT(*) FROM niveau_cree WHERE ID = ?");
    $checkNiveau->execute([$id_niveau]);
    $existeDansNiveauCree = $checkNiveau->fetchColumn() > 0;

    if (!$existeDansNiveauCree) {
       
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        
        $stmt = $pdo->prepare("
            INSERT INTO score (ID_Niveau, ID_Utilisateur, Chrono, Date)
            VALUES (:niv, :usr, :chr, NOW())
        ");
        $stmt->execute([
            ':niv' => $id_niveau,
            ':usr' => $id_utilisateur,
            ':chr' => $chrono
        ]);
        
        
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    } else {
       
        $stmt = $pdo->prepare("
            INSERT INTO score (ID_Niveau, ID_Utilisateur, Chrono, Date)
            VALUES (:niv, :usr, :chr, NOW())
        ");
        $stmt->execute([
            ':niv' => $id_niveau,
            ':usr' => $id_utilisateur,
            ':chr' => $chrono
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Chrono successfully recorded!']);

} catch (PDOException $e) {
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    http_response_code(500);
    exit(json_encode(['error' => 'SQL Error: ' . $e->getMessage()]));
}