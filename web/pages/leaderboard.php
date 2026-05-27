<?php
declare(strict_types=1);
session_start();
require_once '../includes/db.php';

/* ── Classement : total des points par joueur ── */
$stmt = $pdo->query("
    SELECT u.Pseudo, u.avatar,
           SUM(c.Points)  AS total_points,
           COUNT(c.ID)    AS nb_parties
    FROM classement c
    JOIN utilisateur u ON u.ID = c.ID_utilisateur
    GROUP BY u.ID, u.Pseudo, u.avatar
    ORDER BY total_points DESC
    LIMIT 50
");
$classement = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ── Rang du joueur connecté (compatible MySQL 5.7) ── */
$myRank = null;
if (isset($_SESSION['user_id'])) {
    $myScore = $pdo->prepare("SELECT SUM(Points) FROM classement WHERE ID_utilisateur = ?");
    $myScore->execute([$_SESSION['user_id']]);
    $myTotal = $myScore->fetchColumn();

    if ($myTotal) {
        $rankQ = $pdo->prepare("
            SELECT COUNT(*) + 1 FROM (
                SELECT ID_utilisateur
                FROM classement
                GROUP BY ID_utilisateur
                HAVING SUM(Points) > ?
            ) better
        ");
        $rankQ->execute([$myTotal]);
        $myRank = (int)$rankQ->fetchColumn();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Classement — 2Fast4U</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/leaderboard.css">
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="icon" href="../media/ico-car.ico" type="image/x-icon">
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
                <?php if (isset($_SESSION['pseudo'])): ?>
                    <a href="profile.php"><?= htmlspecialchars($_SESSION['pseudo']) ?></a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </header>
        <main>
            <div class="lb-hero">
                <h2>Driver rankings</h2>
                <p>The best drivers on the circuit 🏁</p>
            </div>

            <?php if (isset($_SESSION['user_id']) && $myRank): ?>
            <div class="my-rank-bar">
                Your rank : <strong>#<?= $myRank ?></strong>
            </div>
            <?php endif; ?>

            <?php if (empty($classement)): ?>
                <p class="lb-empty">No scores recorded yet.</p>
            <?php else: ?>

            <!-- Podium -->
            <div class="podium">
                <?php
                $order   = [1, 0, 2];
                $classes = ['second', 'first', 'third'];
                $medals  = ['🥈', '🥇', '🥉'];
                foreach ($order as $k => $i):
                    if (!isset($classement[$i])) continue;
                    $p = $classement[$i];
                ?>
                <div class="podium-card <?= $classes[$k] ?>">
                    <div class="podium-medal"><?= $medals[$k] ?></div>
                    <?php if (!empty($p['avatar'])): ?>
                        <img src="../<?= htmlspecialchars($p['avatar']) ?>" class="podium-avatar" alt="">
                    <?php else: ?>
                        <div class="podium-avatar-placeholder">👤</div>
                    <?php endif; ?>
                    <div class="podium-pseudo"><?= htmlspecialchars($p['Pseudo']) ?></div>
                    <div class="podium-points"><?= number_format((int)$p['total_points']) ?> pts</div>
                    <div class="podium-parties"><?= $p['nb_parties'] ?> game<?= $p['nb_parties'] > 1 ? 's' : '' ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Tableau -->
            <div class="lb-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Driver</th>
                            <th>Points</th>
                            <th>Games</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($classement as $index => $joueur):
                        $isMe = isset($_SESSION['pseudo']) && $_SESSION['pseudo'] === $joueur['Pseudo'];
                        $rank = $index + 1;
                    ?>
                        <tr <?= $isMe ? 'class="current-user"' : '' ?>>
                            <td>
                                <span class="rank-num <?= $rank <= 3 ? 'rank-'.$rank : '' ?>">
                                    <?= $rank <= 3 ? ['🥇','🥈','🥉'][$rank-1] : $rank ?>
                                </span>
                            </td>
                            <td>
                                <div class="player-cell">
                                    <?php if (!empty($joueur['avatar'])): ?>
                                        <img src="../<?= htmlspecialchars($joueur['avatar']) ?>" alt="">
                                    <?php else: ?>
                                        <div class="no-avatar">👤</div>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($joueur['Pseudo']) ?>
                                    <?php if ($isMe): ?>
                                        <span style="color:#e2b96f;font-size:0.7rem">(vous)</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= number_format((int)$joueur['total_points']) ?> pts</td>
                            <td><?= $joueur['nb_parties'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php endif; ?>


        </main>

        <footer>
            © <?= date('Y') ?> 2Fast4U - Leaderboard.
        </footer>
    </body>
</html>