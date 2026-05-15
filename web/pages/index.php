<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queen's Game</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/game.js" defer></script>
    <script src="../js/regles.js" defer></script>
    <link rel="icon" href="../media/ico-car.ico" type="image/x-icon">
</head>
<body>
    <header>
        <h1>2Fast4U</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="#" id="openRules">Règles</a>
            <?php if (isset($_SESSION["pseudo"])): ?>
                <a href="profile.php"><?= htmlspecialchars($_SESSION["pseudo"]) ?></a>
            <?php else: ?>
                <a href="login.php">Se connecter</a>
            <?php endif; ?>
            </nav>
    </header>

    <main>
        <h2>Clio 2 Tuné</h2>
        <p>Allez vient jouer on est cool </p>
        <a href="game.php" class="btn">Jouer</a>
    </main>

    <div id="overlay" class="overlay">
        <div class="modal">
            <h2>Règles du jeu</h2>

            <ul>
                <li>Une seule voiture par ligne</li>
                <li>Une seule voiture par colonne</li>
                <li>Aucune voiture collée autour d'une autre</li>
                <li>Chaque couleur doit contenir une voiture</li>
            </ul>

            <a href="#" id="closeRules">Close</a>
        </div>
    </div>
</body>
</html>
