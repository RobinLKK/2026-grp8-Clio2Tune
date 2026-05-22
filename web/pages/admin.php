<?php
session_start();
require_once '../includes/db.php';

// Vérification admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT Admin FROM utilisateur WHERE ID = ?");
$stmt->execute([$_SESSION['user_id']]);
$me = $stmt->fetch();

if (!$me || !$me['Admin']) {
    header("Location: index.php");
    exit;
}

$message = '';
$erreur  = '';

/* ── ACTIONS POST ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Supprimer un utilisateur
    if ($action === 'delete_user') {
        $id = (int)$_POST['user_id'];
        if ($id !== (int)$_SESSION['user_id']) {
            $pdo->prepare("DELETE FROM utilisateur WHERE ID = ?")->execute([$id]);
            $message = "Utilisateur supprimé.";
        } else {
            $erreur = "Vous ne pouvez pas vous supprimer vous-même.";
        }
    }

    if ($action === 'edit_level') {
        $id   = (int)$_POST['level_id'];
        $nom  = trim($_POST['nom']);
        $diff = max(1, min(5, (int)$_POST['difficulte']));
        $pdo->prepare("UPDATE niveau_cree SET Nom_du_niveau = ?, Difficulte = ? WHERE ID = ?")
            ->execute([$nom, $diff, $id]);
        $message = "Niveau mis à jour.";
    }

    // Bannir / débannir (on réutilise Admin = -1 pour banni)
    if ($action === 'ban_user') {
        $id = (int)$_POST['user_id'];
        $pdo->prepare("UPDATE utilisateur SET Admin = -1 WHERE ID = ? AND ID != ?")->execute([$id, $_SESSION['user_id']]);
        $message = "Utilisateur banni.";
    }
    if ($action === 'unban_user') {
        $id = (int)$_POST['user_id'];
        $pdo->prepare("UPDATE utilisateur SET Admin = 0 WHERE ID = ?")->execute([$id]);
        $message = "Utilisateur débanni.";
    }

    // Promouvoir / rétrograder admin
    if ($action === 'make_admin') {
        $id = (int)$_POST['user_id'];
        $pdo->prepare("UPDATE utilisateur SET Admin = 1 WHERE ID = ?")->execute([$id]);
        $message = "Utilisateur promu admin.";
    }
    if ($action === 'remove_admin') {
        $id = (int)$_POST['user_id'];
        if ($id !== (int)$_SESSION['user_id']) {
            $pdo->prepare("UPDATE utilisateur SET Admin = 0 WHERE ID = ?")->execute([$id]);
            $message = "Droits admin retirés.";
        }
    }

    // Reset scores d'un utilisateur
    if ($action === 'reset_scores') {
        $id = (int)$_POST['user_id'];
        $pdo->prepare("DELETE FROM classement WHERE ID_utilisateur = ?")->execute([$id]);
        $pdo->prepare("UPDATE utilisateur SET Nombre_niveau = 0 WHERE ID = ?")->execute([$id]);
        $message = "Scores réinitialisés.";
    }

    // Reset tous les scores
    if ($action === 'reset_all_scores') {
        $pdo->exec("DELETE FROM classement");
        $pdo->exec("UPDATE utilisateur SET Nombre_niveau = 0");
        $message = "Tous les scores ont été réinitialisés.";
    }

    // Verrouiller / déverrouiller niveau
    if ($action === 'toggle_level') {
        $id     = (int)$_POST['level_id'];
        $locked = (int)$_POST['locked'];
        // On stocke dans la col Difficulte un flag négatif pour locked (hack simple)
        // En vrai on devrait avoir une col locked, mais on va ajouter ça proprement
        // Pour l'instant on utilise une session pour stocker les niveaux locked
        $_SESSION['locked_levels'] = $_SESSION['locked_levels'] ?? [];
        if ($locked) {
            $_SESSION['locked_levels'][] = $id;
            $_SESSION['locked_levels'] = array_unique($_SESSION['locked_levels']);
        } else {
            $_SESSION['locked_levels'] = array_diff($_SESSION['locked_levels'], [$id]);
        }
        $message = "Niveau mis à jour.";
    }

    header("Location: admin.php?msg=" . urlencode($message ?: $erreur));
    exit;
}

if (isset($_GET['msg'])) $message = $_GET['msg'];

/* ── DONNÉES ── */

// Stats globales
$stats = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM utilisateur) AS nb_users,
        (SELECT COUNT(*) FROM classement)  AS nb_parties,
        (SELECT SUM(Points) FROM classement) AS total_points,
        (SELECT MAX(Points) FROM classement) AS best_score
")->fetch();

// Liste utilisateurs
$users = $pdo->query("
    SELECT u.ID, u.Pseudo, u.Email, u.Admin, u.Nombre_niveau, u.avatar,
           COUNT(c.ID) AS nb_parties,
           COALESCE(SUM(c.Points), 0) AS total_points
    FROM utilisateur u
    LEFT JOIN classement c ON c.ID_utilisateur = u.ID
    GROUP BY u.ID
    ORDER BY u.ID ASC
")->fetchAll();

// Niveaux
$niveaux = $pdo->query("SELECT * FROM niveau_cree ORDER BY ID ASC")->fetchAll();

$lockedLevels = $_SESSION['locked_levels'] ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — 2Fast4U</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" href="../media/ico-car.ico">
</head>
<body>
    <header>
        <a href="index.php" class="logo">
            <img src="../media/2fast.png" alt="2Fast4U" style="height: 40px;">
        </a>
        <nav>
            <a href="index.php">Home</a>
            <a href="leaderboard.php">Leaderboard</a>
        </nav>
        <div class="nav-right">
            <a href="profile.php"><?= htmlspecialchars($_SESSION['pseudo']) ?></a>
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <main style="max-width:100%; padding:0;">
    <div class="admin-wrap">

        <div class="admin-title">⚙ Panel Admin</div>
        <div class="admin-sub">2Fast4U — Gestion du site</div>

        <?php if ($message): ?>
            <div class="admin-msg"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- STATS GLOBALES -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $stats['nb_users'] ?></div>
                <div class="stat-label">Joueurs</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['nb_parties'] ?></div>
                <div class="stat-label">Parties jouées</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= number_format((int)$stats['total_points']) ?></div>
                <div class="stat-label">Points totaux</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['best_score'] ?? 0 ?></div>
                <div class="stat-label">Meilleur score</div>
            </div>
        </div>

        <!-- GESTION UTILISATEURS -->
        <div class="admin-section">
            <h2>👥 Utilisateurs</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Joueur</th>
                        <th>Email</th>
                        <th>Statut</th>
                        <th>Parties</th>
                        <th>Points</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['ID'] ?></td>
                        <td>
                            <?php if (!empty($u['avatar'])): ?>
                                <img src="../<?= htmlspecialchars($u['avatar']) ?>" class="user-avatar-sm" alt="">
                            <?php else: ?>
                                👤
                            <?php endif; ?>
                            <?= htmlspecialchars($u['Pseudo']) ?>
                            <?php if ($u['ID'] == $_SESSION['user_id']): ?>
                                <span style="color:#e2b96f;font-size:0.7rem">(vous)</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($u['Email'] ?? '—') ?></td>
                        <td>
                            <?php if ($u['Admin'] == 1): ?>
                                <span class="badge badge-admin">Admin</span>
                            <?php elseif ($u['Admin'] == -1): ?>
                                <span class="badge badge-banni">Banni</span>
                            <?php else: ?>
                                <span class="badge badge-user">Joueur</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $u['nb_parties'] ?></td>
                        <td><?= number_format((int)$u['total_points']) ?> pts</td>
                        <td style="display:flex; gap:6px; flex-wrap:wrap;">
                            <?php if ($u['ID'] != $_SESSION['user_id']): ?>

                                <!-- Reset scores -->
                                <form method="POST">
                                    <input type="hidden" name="action" value="reset_scores">
                                    <input type="hidden" name="user_id" value="<?= $u['ID'] ?>">
                                    <button class="btn-action btn-warn" onclick="return confirm('Reset les scores de <?= htmlspecialchars($u['Pseudo']) ?> ?')">Reset scores</button>
                                </form>

                                <!-- Ban / Unban -->
                                <?php if ($u['Admin'] == -1): ?>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="unban_user">
                                        <input type="hidden" name="user_id" value="<?= $u['ID'] ?>">
                                        <button class="btn-action btn-success">Débannir</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="ban_user">
                                        <input type="hidden" name="user_id" value="<?= $u['ID'] ?>">
                                        <button class="btn-action btn-warn" onclick="return confirm('Bannir <?= htmlspecialchars($u['Pseudo']) ?> ?')">Bannir</button>
                                    </form>
                                <?php endif; ?>

                                <!-- Admin / Remove admin -->
                                <?php if ($u['Admin'] != 1): ?>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="make_admin">
                                        <input type="hidden" name="user_id" value="<?= $u['ID'] ?>">
                                        <button class="btn-action btn-neutral">Promouvoir</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="remove_admin">
                                        <input type="hidden" name="user_id" value="<?= $u['ID'] ?>">
                                        <button class="btn-action btn-warn">Rétrograder</button>
                                    </form>
                                <?php endif; ?>

                                <!-- Supprimer -->
                                <form method="POST">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?= $u['ID'] ?>">
                                    <button class="btn-action btn-danger" onclick="return confirm('Supprimer définitivement <?= htmlspecialchars($u['Pseudo']) ?> ?')">Supprimer</button>
                                </form>

                            <?php else: ?>
                                <span style="color:rgba(240,236,227,0.25);font-size:0.7rem">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- GESTION NIVEAUX -->
        <div class="admin-section">
            <h2>🏁 Niveaux</h2>
            <div class="levels-grid">
                <?php foreach ($niveaux as $niv):
                    $isLocked = in_array($niv['ID'], $lockedLevels);
                ?>
                <div class="level-card <?= $isLocked ? 'level-locked' : '' ?>">
                    <h3><?= htmlspecialchars($niv['Nom_du_niveau'] ?? 'Niveau '.$niv['ID']) ?></h3>
                    <div class="diff">Taille : <?= $niv['Taille'] ?>×<?= $niv['Taille'] ?> — Diff : <?= str_repeat('★', $niv['Difficulte']) ?></div>

                    <!-- Formulaire édition -->
                    <form method="POST" style="margin-top:0.8rem; text-align:left;">
                        <input type="hidden" name="action" value="edit_level">
                        <input type="hidden" name="level_id" value="<?= $niv['ID'] ?>">
                        <div style="margin-bottom:0.4rem;">
                            <label style="font-size:0.65rem;color:rgba(240,236,227,0.4);text-transform:uppercase;letter-spacing:0.1em;">Nom</label>
                            <input type="text" name="nom" value="<?= htmlspecialchars($niv['Nom_du_niveau']) ?>" style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(226,185,111,0.2);color:#f0ece3;padding:4px 8px;font-size:0.8rem;">
                        </div>
                        <div style="margin-bottom:0.8rem;">
                            <label style="font-size:0.65rem;color:rgba(240,236,227,0.4);text-transform:uppercase;letter-spacing:0.1em;">Difficulté (1-5)</label>
                            <input type="number" name="difficulte" min="1" max="5" value="<?= $niv['Difficulte'] ?>" style="width:100%;background:rgba(255,255,255,0.05);border:1px solid rgba(226,185,111,0.2);color:#f0ece3;padding:4px 8px;font-size:0.8rem;">
                        </div>
                        <button class="btn-action btn-neutral" style="width:100%;">Sauvegarder</button>
                    </form>

                    <!-- Lock/Unlock -->
                    <form method="POST" style="margin-top:0.5rem;">
                        <input type="hidden" name="action" value="toggle_level">
                        <input type="hidden" name="level_id" value="<?= $niv['ID'] ?>">
                        <input type="hidden" name="locked" value="<?= $isLocked ? 0 : 1 ?>">
                        <button class="btn-action <?= $isLocked ? 'btn-success' : 'btn-warn' ?>" style="width:100%;">
                            <?= $isLocked ? '🔓 Déverrouiller' : '🔒 Verrouiller' ?>
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- RESET GLOBAL -->
        <div class="admin-section">
            <h2>⚠ Zone dangereuse</h2>
            <form method="POST" onsubmit="return confirm('Remettre TOUS les scores à zéro ? Cette action est irréversible.')">
                <input type="hidden" name="action" value="reset_all_scores">
                <button class="btn-full-danger">🗑 Réinitialiser tous les scores</button>
            </form>
        </div>

    </div>
    </main>

    <footer>© <?= date('Y') ?> 2Fast4U — Admin</footer>
</body>
</html>