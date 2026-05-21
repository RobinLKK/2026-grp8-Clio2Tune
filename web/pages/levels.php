<?php
declare(strict_types=1);
session_start();

// Liste des niveaux prédéfinis (doit correspondre à l'ordre dans ton JS)
$niveaux = [
    ['id' => 0, 'nom' => 'Circuit de Départ', 'difficulte' => 1, 'locked' => false],
    ['id' => 1, 'nom' => 'Virage Serré',      'difficulte' => 2, 'locked' => false],
    ['id' => 2, 'nom' => 'Labyrinthe Urbain', 'difficulte' => 3, 'locked' => false],
    ['id' => 3, 'nom' => 'Grand Prix Pro',    'difficulte' => 4, 'locked' => true],
    ['id' => 4, 'nom' => 'L\'Ultime Défi',    'difficulte' => 5, 'locked' => true],
];

$pseudo = $_SESSION['pseudo'] ?? 'Pilote invité';

function afficherEtoiles(int $niveau): string
{
    return str_repeat('★', $niveau) . str_repeat('☆', 5 - $niveau);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choix des niveaux - 2Fast4U</title>
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
    <h2>Circuits Officiels</h2>
    <p>Bienvenue <strong><?= htmlspecialchars($pseudo) ?></strong> 🚗</p>
</section>

<section class="levels-container">

<?php foreach ($niveaux as $niveau): ?>
    
    <div class="card <?= $niveau['locked'] ? 'locked' : '' ?>">
        
        <h3>Niveau <?= $niveau['id'] + 1 ?> - <?= htmlspecialchars($niveau['nom']) ?></h3>

        <p class="difficulty">
            <?= afficherEtoiles($niveau['difficulte']) ?>
        </p>

        <?php if ($niveau['locked']): ?>
            <span class="btn btn-lock">🔒 Verrouillé</span>
        <?php else: ?>
            <!-- On passe l'ID dans l'URL pour que game.php sache quel niveau charger -->
            <a class="btn" href="game.php?type=fixed&id=<?= $niveau['id'] ?>">
                ▶ Jouer
            </a>
        <?php endif; ?>

    </div>

<?php endforeach; ?>

</section>
        <footer>
            © <?= date('Y') ?> 2Fast4U - Leaderboard.
        </footer>
</body>
</html>