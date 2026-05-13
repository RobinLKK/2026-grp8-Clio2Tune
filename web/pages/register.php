<?php
session_start();
require_once '../includes/db.php';

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pseudo = trim($_POST["pseudo"]);
    $mdp    = trim($_POST["mot_de_passe"]);
    $confirm = trim($_POST["confirmer"]);

    if ($mdp !== $confirm) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifie si le pseudo existe déjà
        $stmt = $pdo->prepare("SELECT ID FROM utilisateur WHERE Pseudo = ?");
        $stmt->execute([$pseudo]);

        if ($stmt->fetch()) {
            $erreur = "Ce pseudo est déjà pris.";
        } else {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateur (Pseudo, Mot_de_passe, Admin) VALUES (?, ?, 0)");
            $stmt->execute([$pseudo, $hash]);

            $_SESSION["user_id"] = $pdo->lastInsertId();
            $_SESSION["pseudo"]  = $pseudo;
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>2Fast4U – Inscription</title>
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
        <h2>Inscription</h2>

        <?php if ($erreur): ?>
            <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Pseudo</label>
            <input type="text" name="pseudo" required>

            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" required>

            <label>Confirmer le mot de passe</label>
            <input type="password" name="confirmer" required>

            <button type="submit">S'inscrire</button>
        </form>
        <p></p>


        <p>Déjà un compte ? <a href="login.php" class="btn-outline">Se connecter</a></p>
    </main>
</body>
</html>