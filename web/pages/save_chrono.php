<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../includes/db.php';

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
    // --- CORRECTION DU BLOCAGE DE LA CLÉ ÉTRANGÈRE ---
    // On vérifie si l'id_niveau existe vraiment dans la table 'niveau_cree'
    $checkNiveau = $pdo->prepare("SELECT COUNT(*) FROM niveau_cree WHERE ID = ?");
    $checkNiveau->execute([$id_niveau]);
    $existeDansNiveauCree = $checkNiveau->fetchColumn() > 0;

    if (!$existeDansNiveauCree) {
        /* Si le niveau n'existe pas dans 'niveau_cree', c'est un niveau du mode histoire officiel.
          Pour contourner la contrainte de clé étrangère, on passe temporairement ID_Niveau à NULL 
          ou on désactive les clés étrangères pour cette insertion précise.
        */
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
        // Si c'est un niveau personnalisé créé par un joueur, l'insertion classique fonctionne
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

    echo json_encode(['success' => true, 'message' => 'Chrono enregistré !']);

} catch (PDOException $e) {
    // Réactivation de sécurité au cas où ça crash au milieu
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    http_response_code(500);
    exit(json_encode(['error' => 'Erreur SQL lors de l\'enregistrement : ' . $e->getMessage()]));
}