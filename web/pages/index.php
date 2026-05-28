<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2Fast4Ugame</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/index.css">
    <script src="../js/regles.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="icon" href="../media/ico-car.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <header>
    <a href="index.php" class="logo">
        <img src="../media/2fast.png" alt="2Fast4U" style="height: 40px;">
    </a>    
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
    </header>

        <main>
        <h1>2<span style="color:#e2b96f">FAST</span>4U</h1>
        <a href="gamemodes.php" class="btn-play">Play</a>
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
    <!-- FEATURES -->
<section class="features-section">

    <div class="feature-card">
        <div class="feature-icon">🏁</div>
        <h3>Puzzle Racing</h3>
        <p>Solve strategic racing grids and complete the track.</p>
    </div>

    <div class="feature-card">
        <div class="feature-icon">🧠</div>
        <h3>Strategic Gameplay</h3>
        <p>Every placement matters. Think before you move.</p>
    </div>

    <div class="feature-card">
        <div class="feature-icon">⚡</div>
        <h3>Time Challenge</h3>
        <p>Beat the timer and improve your best score.</p>
    </div>

    <div class="feature-card">
        <div class="feature-icon">🏆</div>
        <h3>Global Leaderboard</h3>
        <p>Compete against the best players worldwide.</p>
    </div>

</section>
    <footer>
    © <?= date('Y') ?> 2Fast4U - Home
  </footer>
</body>
</html>
