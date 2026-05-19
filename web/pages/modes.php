<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Modes - 2Fast4U</title>

    <link rel="stylesheet" href="../css/modes.css">
    <link rel="icon" href="../media/ico-car.ico" type="image/x-icon">
</head>

<body>

<header>
    <h1>2Fast4U</h1>

    <nav>
        <a href="index.php">Home</a>
        <a href="leaderboard.php">Leaderboard</a>

        <?php if (isset($_SESSION["pseudo"])): ?>
            <a href="profile.php"><?= htmlspecialchars($_SESSION["pseudo"]) ?></a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<section class="hero">
    <h1>Choose Your Mode</h1>
    <p>Select how you want to race.</p>

    <div class="hero-line"></div>
</section>

<section class="modes-container">

    <div class="mode-card">
        <div class="mode-icon">🏁</div>

        <h2>Story Mode</h2>

        <p>
            Progress through challenging tracks
            and unlock new levels.
        </p>

        <a href="levels.php" class="btn">
            Play
        </a>
    </div>

    <div class="mode-card">
        <div class="mode-icon">🚗</div>

        <h2>Random Mode</h2>

        <p>
            Play a randomly generated
            puzzle every game.
        </p>

        <a href="random.php" class="btn">
            Play
        </a>
    </div>

    <div class="mode-card">
        <div class="mode-icon">🔧</div>

        <h2>Level Editor</h2>

        <p>
            Build and test your own
            custom racing puzzles.
        </p>

        <a href="editor.php" class="btn">
            Create
        </a>
    </div>

</section>

<footer>
    <p>2Fast4U • Racing Puzzle Experience</p>
</footer>

</body>
</html>