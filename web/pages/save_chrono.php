<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

// Sécurité : POST uniquement
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Méthode non autorisée']));
}

// Sécurité : utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Non connecté']));
}

$id_niveau      = intval($_POST['id_niveau'] ?? 0);
$chrono         = intval($_POST['chrono'] ?? 0);
$id_utilisateur = intval($_SESSION['user_id']);

// Validation des données
if ($id_niveau <= 0 || $chrono <= 0) {
    http_response_code(400);
    exit(json_encode(['error' => 'Données invalides']));
}

try {
    // On vérifie si l'id_niveau existe dans la table 'niveau_cree'
    $checkNiveau = $pdo->prepare("SELECT COUNT(*) FROM niveau_cree WHERE ID = ?");
    $checkNiveau->execute([$id_niveau]);
    $existeDansNiveauCree = $checkNiveau->fetchColumn() > 0;

    if (!$existeDansNiveauCree) {
        // C'est un niveau officiel du mode histoire : on désactive temporairement la contrainte
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
        
        // On réactive tout de suite la contrainte
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    } else {
        // C'est un niveau personnalisé créé par un joueur, la clé étrangère est valide
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

    echo json_encode(['success' => true, 'message' => 'Chrono enregistré avec succès !']);

} catch (PDOException $e) {
    // Au cas où ça plante au milieu, on s'assure de réactiver la sécurité
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    http_response_code(500);
    exit(json_encode(['error' => 'Erreur SQL : ' . $e->getMessage()]));
}