<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jouer - Queen's Game</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/game.css">
    <script src="../js/regles.js" defer></script>
    <link rel="icon" href="../media/car-icon.ico" type="image/x-icon">
</head>
<body class="game-page">
    <!-- Modal règles -->
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

    <!-- Navbar -->
    <header>
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
    </header>

        <main class="game-main">
            <!-- Chrono -->
            <div id="chrono">00:00</div>

            <!-- Boutons haut -->
            <div id="controls">
                <button id="btn-reset">Réinitialiser</button>
                <button id="btn-new">Nouveau niveau</button>
            </div>

            <!-- Grille + leaderboard -->
            <div class="game-middle">

                <!-- Grille -->
                <div id="game-wrap">
                    <div id="size-row">
                        Taille :
                        <select id="size-select">
                            <option value="5">5×5</option>
                            <option value="6">6×6</option>
                            <option value="7">7×7</option>
                            <option value="8" selected>8×8</option>
                            <option value="9">9×9</option>
                            <option value="10">10×10</option>
                        </select>
                    </div>
                    <div id="grid-container"><table id="grid"></table></div>
                    <p id="msg"></p>
                </div>

                <!-- Leaderboard niveau -->
                <div id="level-leaderboard">
                    <div class="lb-title">Top niveau</div>
                    <div class="lb-empty">Aucun score<br>enregistré</div>
                </div>

            </div>

            <!-- Boutons bas + conseil -->
            <div class="game-bottom">
                <div class="game-bottom-btns">
                    <button id="btn-hint">Un indice</button>
                    <button id="btn-solution">Solution finale</button>
                </div>
                <div id="conseil">Cliquez pour placer une voiture, cliquez 2 fois pour une croix, et 3 fois pour retirer</div>
            </div>

        </div>
    </main>

</body>
</html>