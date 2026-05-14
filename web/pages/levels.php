<?php
declare(strict_types=1);
session_start();
$niveaux = [
    ['id' => 1, 'nom' => 'nom niveau 1',    'difficulte' => 1, 'locked' => false],
    ['id' => 2, 'nom' => 'nom niveau 2',      'difficulte' => 2, 'locked' => false],
    ['id' => 3, 'nom' => 'nom niveau 3', 'difficulte' => 3, 'locked' => false],
    ['id' => 4, 'nom' => 'nom niveau 4',        'difficulte' => 4, 'locked' => true],
    ['id' => 5, 'nom' => 'nom niveau 5',    'difficulte' => 5, 'locked' => true],
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

    <link rel="stylesheet" href="../css/levels.css">
    <link rel="icon" href="../media/car-icon.ico" type="image/x-icon">
</head>

<body>

<header>
    <h1>2Fast4U</h1>

    <nav>
        <a href="index.php">Accueil</a>
        <a href="leaderboard.php">Classement</a>
        <a href="login.php">Connexion</a>
    </nav>
</header>

<section class="hero">
    <h2>Choisis ton circuit</h2>
    <p>Bienvenue <strong><?= htmlspecialchars($pseudo) ?></strong> 🚗</p>
</section>

<section class="levels-container">

<?php foreach ($niveaux as $niveau): ?>
    
    <div class="card <?= $niveau['locked'] ? 'locked' : '' ?>">
        
        <h3>Niveau <?= $niveau['id'] ?> - <?= htmlspecialchars($niveau['nom']) ?></h3>

        <p class="difficulty">
            <?= afficherEtoiles($niveau['difficulte']) ?>
        </p>

        <?php if ($niveau['locked']): ?>
            <span class="btn btn-lock">🔒 Verrouillé</span>
        <?php else: ?>
            <a class="btn" href="game.php?level=<?= $niveau['id'] ?>">
                ▶ Jouer
            </a>
        <?php endif; ?>

    </div>

<?php endforeach; ?>

</section>

<footer>
    © <?= date('Y') ?> 2Fast4U - Niveaux
</footer>

</body>
</html>
