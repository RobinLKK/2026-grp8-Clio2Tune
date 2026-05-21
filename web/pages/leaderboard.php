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
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="icon" href="../media/ico-car.ico" type="image/x-icon">
        <style>
            .lb-hero {
                text-align: center;
                padding: 2.5rem 1rem 1rem;
            }
            .lb-hero h2 {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 2.5rem;
                letter-spacing: 0.15em;
                color: #e2b96f;
                text-shadow: 0 2px 8px rgba(0,0,0,0.6);
                margin-bottom: 0.3rem;
            }
            .lb-hero p {
                color: rgba(240,236,227,0.5);
                font-size: 0.85rem;
                letter-spacing: 0.1em;
                margin-bottom: 0;
                text-shadow: none;
            }

            .my-rank-bar {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                font-family: 'Barlow', sans-serif;
                font-size: 0.85rem;
                color: rgba(240,236,227,0.6);
                background: rgba(10,15,30,0.6);
                backdrop-filter: blur(6px);
                border: 1px solid rgba(226,185,111,0.15);
                padding: 0.5rem 1.5rem;
                margin: 0 auto 1.5rem;
                max-width: 400px;
            }
            .my-rank-bar strong {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 1.1rem;
                color: #e2b96f;
                letter-spacing: 0.1em;
            }

            /* Podium */
            .podium {
                display: flex;
                align-items: flex-end;
                justify-content: center;
                gap: 1rem;
                margin-bottom: 2rem;
                padding: 0 1rem;
            }
            .podium-card {
                background: rgba(10,15,30,0.72);
                backdrop-filter: blur(8px);
                border: 1px solid rgba(226,185,111,0.15);
                padding: 1.2rem 1rem;
                text-align: center;
                min-width: 130px;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.4rem;
            }
            .podium-card.first  { border-color: rgba(255,215,0,0.4);   padding-bottom: 2rem; }
            .podium-card.second { border-color: rgba(192,192,192,0.3); padding-bottom: 1.2rem; }
            .podium-card.third  { border-color: rgba(205,127,50,0.3); }

            .podium-medal { font-size: 1.8rem; line-height: 1; }
            .podium-avatar {
                width: 52px; height: 52px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid rgba(226,185,111,0.4);
            }
            .podium-avatar-placeholder {
                width: 52px; height: 52px;
                border-radius: 50%;
                background: rgba(255,255,255,0.06);
                border: 2px solid rgba(226,185,111,0.2);
                display: flex; align-items: center; justify-content: center;
                font-size: 1.4rem;
            }
            .podium-pseudo {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 0.95rem;
                letter-spacing: 0.1em;
                color: #f0ece3;
            }
            .podium-card.first  .podium-points { color: #ffd700; }
            .podium-card.second .podium-points { color: #c0c0c0; }
            .podium-card.third  .podium-points { color: #cd7f32; }
            .podium-points {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 1.3rem;
                letter-spacing: 0.08em;
            }
            .podium-parties {
                font-size: 0.68rem;
                color: rgba(240,236,227,0.35);
                letter-spacing: 0.08em;
            }

            /* Tableau */
            .lb-table-wrap {
                max-width: 700px;
                margin: 0 auto;
                padding: 0 1rem 3rem;
            }
            .lb-table-wrap table { max-width: 100%; margin: 0; }
            .lb-table-wrap table th {
                font-family: 'Bebas Neue', sans-serif;
                letter-spacing: 0.12em;
                font-size: 0.8rem;
            }
            .lb-table-wrap table td { background: rgba(10,15,30,0.55) !important; }
            .lb-table-wrap table tr:hover td { background: rgba(226,185,111,0.06) !important; }

            tr.current-user td { color: #e2b96f !important; font-weight: 600; }

            .player-cell {
                display: flex;
                align-items: center;
                gap: 0.6rem;
            }
            .player-cell img {
                width: 28px; height: 28px;
                border-radius: 50%;
                object-fit: cover;
                border: 1px solid rgba(226,185,111,0.25);
                flex-shrink: 0;
            }
            .player-cell .no-avatar {
                width: 28px; height: 28px;
                border-radius: 50%;
                background: rgba(255,255,255,0.06);
                display: flex; align-items: center; justify-content: center;
                font-size: 0.8rem;
                flex-shrink: 0;
            }

            .rank-num { font-family: 'Bebas Neue', sans-serif; font-size: 1rem; }
            .rank-1 { color: #ffd700; }
            .rank-2 { color: #c0c0c0; }
            .rank-3 { color: #cd7f32; }

            .lb-empty {
                text-align: center;
                color: rgba(240,236,227,0.35);
                font-size: 0.9rem;
                padding: 3rem 0;
            }

            footer {
                text-align: center;
                padding: 1.5rem;
                font-size: 0.75rem;
                color: rgba(240,236,227,0.25);
                letter-spacing: 0.1em;
            }
        </style>
    </head>
    <body>

        <header>
            <a href="index.php" class="logo">2Fast4U</a>
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
                <h2>Classement des pilotes</h2>
                <p>Les meilleurs pilotes du circuit 🏁</p>
            </div>

            <?php if (isset($_SESSION['user_id']) && $myRank): ?>
            <div class="my-rank-bar">
                Votre rang : <strong>#<?= $myRank ?></strong>
            </div>
            <?php endif; ?>

            <?php if (empty($classement)): ?>
                <p class="lb-empty">Aucun score enregistré pour l'instant.</p>
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
                    <div class="podium-parties"><?= $p['nb_parties'] ?> partie<?= $p['nb_parties'] > 1 ? 's' : '' ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Tableau -->
            <div class="lb-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pilote</th>
                            <th>Points</th>
                            <th>Parties</th>
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