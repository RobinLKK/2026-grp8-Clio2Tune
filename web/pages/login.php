<?php
session_start();
require_once '../includes/db.php';

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pseudo = trim($_POST["pseudo"]);
    $mdp    = trim($_POST["mot_de_passe"]);

    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE Pseudo = ?");
    $stmt->execute([$pseudo]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user["Mot_de_passe"])) {
        $_SESSION["user_id"] = $user["ID"];
        $_SESSION["pseudo"]  = $user["Pseudo"];
        header("Location: index.php");
        exit;
    } else {
        $erreur = "Pseudo ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>2Fast4U – Connexion</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../media/favicon.ico">
</head>
<body>
    <header>
        <h1>2Fast4U</h1>
        <nav>
            <a href="index.php">Accueil</a>
        </nav>
    </header>

    <main>
        <h2>Connexion</h2>

        <?php if ($erreur): ?>
            <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Pseudo</label>
            <input type="text" name="pseudo" required>

            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" required>

            <button type="submit">Se connecter</button>
        </form>
        <p></p>
        <p>Pas encore de compte ? <a href="register.php" class="btn-outline">S'inscrire</a></p>
    </main>
</body>
</html>