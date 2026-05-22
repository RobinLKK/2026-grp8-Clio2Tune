<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

/* ── Vérifications ── */
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'msg' => 'Non connecté']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Méthode invalide']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'save_score') {
    $difficulte = (int)($_POST['difficulte'] ?? 1);
    $chrono     = (int)($_POST['chrono']     ?? 0);
    $id_niveau  = (int)($_POST['id_niveau']  ?? 0);

    /* Vérifie si le joueur a déjà complété ce niveau (mode histoire uniquement) */
    if ($id_niveau > 0) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM classement WHERE ID_utilisateur = ? AND ID_Niveau = ?");
        $check->execute([$_SESSION['user_id'], $id_niveau]);
        if ($check->fetchColumn() > 0) {
            echo json_encode(['ok' => false, 'msg' => 'Niveau déjà complété', 'deja_fait' => true]);
            exit;
        }
    }

    /* Points selon mode et difficulté */
    if ($id_niveau > 0) {
        // Mode histoire — points fixes par difficulté
        $base = match($difficulte) {
            1 => 10,
            2 => 20,
            3 => 30,
            4 => 40,
            5 => 50,
            default => 10,
        };
    } else {
        // Mode random — moitié moins, basé sur taille de grille
        $base = match($difficulte) {
            1 => 5,
            2 => 10,
            3 => 15,
            4 => 20,
            5 => 25,
            default => 5,
        };
    }

    $bonus = 0;
    $points = $base;

    /* Insert dans classement */
    $ins = $pdo->prepare("
        INSERT INTO classement (ID_utilisateur, ID_Niveau, Points)
        VALUES (:uid, :niv, :pts)
    ");
    $ins->execute([
        ':uid' => $_SESSION['user_id'],
        ':niv' => $id_niveau > 0 ? $id_niveau : 1,
        ':pts' => $points,
    ]);

    /* Incrémente Nombre_niveau dans utilisateur */
    $upd = $pdo->prepare("UPDATE utilisateur SET Nombre_niveau = Nombre_niveau + 1 WHERE ID = ?");
    $upd->execute([$_SESSION['user_id']]);

    echo json_encode([
        'ok'     => true,
        'points' => $points,
        'base'   => $base,
        'bonus'  => $bonus,
    ]);
    exit;
}

echo json_encode(['ok' => false, 'msg' => 'Action inconnue']);