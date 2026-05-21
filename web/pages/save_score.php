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

/* ════════════════════════════════
   Fin de partie : enregistre score
   ════════════════════════════════ */
if ($action === 'save_score') {
    $difficulte = (int)($_POST['difficulte'] ?? 1);
    $chrono     = (int)($_POST['chrono']     ?? 0); // en secondes
    $id_niveau  = (int)($_POST['id_niveau']  ?? 0); // 0 = niveau random

    /* Points selon difficulté + bonus vitesse */
    $base = match(true) {
        $difficulte >= 5 => 1200,
        $difficulte >= 4 => 800,
        $difficulte >= 3 => 500,
        $difficulte >= 2 => 250,
        default          => 100,
    };

    /* Bonus si résolu rapidement (max +50% en moins de 30 sec) */
    $bonus = 0;
    if ($chrono > 0 && $chrono < 300) {
        $bonus = (int)($base * 0.5 * max(0, 1 - $chrono / 300));
    }

    $points = $base + $bonus;

    /* Insert dans classement */
    $ins = $pdo->prepare("
        INSERT INTO classement (ID_utilisateur, ID_Niveau, Points)
        VALUES (:uid, :niv, :pts)
    ");
    $ins->execute([
        ':uid' => $_SESSION['user_id'],
        ':niv' => $id_niveau > 0 ? $id_niveau : null,
        ':pts' => $points,
    ]);

    /* Incrémente Nombre_niveau dans utilisateur */
    $upd = $pdo->prepare("
        UPDATE utilisateur SET Nombre_niveau = Nombre_niveau + 1 WHERE ID = ?
    ");
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