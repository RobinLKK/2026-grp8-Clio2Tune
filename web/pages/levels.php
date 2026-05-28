<?php
declare(strict_types=1);
session_start();

// Predefined levels list (must match order in JS)
require_once '../includes/db.php';

$niveaux = $pdo->query("SELECT * FROM niveau_cree ORDER BY Difficulte ASC")->fetchAll();
$pseudo = $_SESSION['pseudo'] ?? 'Guest driver';

function afficherEtoiles(int $niveau): string {
    return str_repeat('★', $niveau) . str_repeat('☆', 5 - $niveau);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Level Selection - 2Fast4U</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/levels.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../media/car-icon.ico" type="image/x-icon">
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

<section class="hero">
    <h2>Official Races</h2>
    <p>Welcome <strong><?= htmlspecialchars($pseudo) ?></strong> 🚗</p>
</section>

<section class="levels-container">

<?php foreach ($niveaux as $i => $niveau):
    $isLocked = in_array($niveau['ID'], $_SESSION['locked_levels'] ?? []);
?>
<div class="card <?= $isLocked ? 'locked' : '' ?>">
    <h3>Level <?= $i + 1 ?> — <?= htmlspecialchars($niveau['Nom_du_niveau']) ?></h3>
    <p class="difficulty"><?= afficherEtoiles($niveau['Difficulte']) ?></p>
    <?php if ($isLocked): ?>
        <span class="btn btn-lock">🔒 Locked</span>
    <?php else: ?>
        <a class="btn" href="game.php?type=fixed&id=<?= $i ?>">▶ Play</a>
    <?php endif; ?>
</div>
<?php endforeach; ?>

</section>
        <footer>
            © <?= date('Y') ?> 2Fast4U - Levels.
        </footer>
</body>
</html>