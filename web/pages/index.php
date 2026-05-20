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
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="icon" href="../media/ico-car.ico" type="image/x-icon">
</head>
<body>
    <header>
<<<<<<< HEAD
    <a href="index.php" class="logo">2Fast4U</a>
    <nav>
        <a href="index.php">Home</a>
        <a href="leaderboard.php">Leaderboard</a>
        <a href="#" id="openRules">Rules</a>
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
=======
        <h1>2Fast4U</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="#" id="openRules">Rules</a>
            <?php if (isset($_SESSION["pseudo"])): ?>
                <a href="profile.php"><?= htmlspecialchars($_SESSION["pseudo"]) ?></a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </nav>
>>>>>>> d888f75b3ef04d760cea0bee7450fbecedb12dcd
    </header>

    <main>
        <h2>Clio 2 Tuné</h2>
        <p>Allez vient jouer on est cool </p>
        <a href="game.php" class="btn">Play</a>
    </main>

    <div id="overlay" class="overlay">
        <div class="modal">
            <h2>Game Rules</h2>

            <ul>
                <li>One car per row</li>
                <li>One car per column</li>
                <li>No cars touching each other</li>
                <li>Each color must contain one car</li>
            </ul>

            <a href="#" id="closeRules">Close</a>
        </div>
    </div>
</body>
</html>
