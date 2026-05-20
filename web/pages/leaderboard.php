<?php
declare(strict_types=1);
session_start();

// Exemple de classement 
$classement = [
    ['pseudo' => 'Pilote1', 'niveau' => 5, 'score' => 500],
    ['pseudo' => 'Pilote2', 'niveau' => 4, 'score' => 400],
    ['pseudo' => 'Pilote3', 'niveau' => 4, 'score' => 400],
    ['pseudo' => 'Pilote4', 'niveau' => 3, 'score' => 300],
    ['pseudo' => 'Pilote5', 'niveau' => 2, 'score' => 200],
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classement - 2Fast4U</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/leaderboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

    <link rel="icon" href="../media/car-icon.ico" type="image/x-icon">
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

<section class="hero">
    <h2>Classement des pilotes</h2>
    <p>Les meilleurs pilotes du circuit 🏁</p>
</section>

<main class="leaderboard-container">

    <!-- PODIUM -->
    <section class="podium">

        <div class="podium-card second">
            <h3>🥈</h3>
            <p><?= htmlspecialchars($classement[1]['pseudo']) ?></p>
            <span><?= $classement[1]['score'] ?> pts</span>
        </div>

        <div class="podium-card first">
            <h3>🥇</h3>
            <p><?= htmlspecialchars($classement[0]['pseudo']) ?></p>
            <span><?= $classement[0]['score'] ?> pts</span>
        </div>

        <div class="podium-card third">
            <h3>🥉</h3>
            <p><?= htmlspecialchars($classement[2]['pseudo']) ?></p>
            <span><?= $classement[2]['score'] ?> pts</span>
        </div>

    </section>

    <!-- TABLEAU -->
    <section class="ranking-table">

        <table>

            <thead>
                <tr>
                    <th>#</th>
                    <th>Pilote</th>
                    <th>Niveau</th>
                    <th>Score</th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($classement as $index => $joueur): ?>

                <tr>
                    <td><?= $index + 1 ?></td>

                    <td>
                        <?= htmlspecialchars($joueur['pseudo']) ?>
                    </td>

                    <td>
                        <?= $joueur['niveau'] ?>
                    </td>

                    <td>
                        <?= $joueur['score'] ?> pts
                    </td>
                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </section>

</main>

<footer>
    © <?= date('Y') ?> 2Fast4U - Classement 
</footer>

</body>
</html>