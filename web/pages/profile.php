<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$erreur = "";
$succes = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nouveau_pseudo = trim($_POST["pseudo"]);
    $email          = trim($_POST["email"]);
    $nouveau_mdp    = trim($_POST["mot_de_passe"]);
    $confirmer      = trim($_POST["confirmer"]);

    $stmt = $pdo->prepare("SELECT ID FROM utilisateur WHERE Pseudo = ? AND ID != ?");
    $stmt->execute([$nouveau_pseudo, $_SESSION["user_id"]]);
    if ($stmt->fetch()) {
        $erreur = "Ce pseudo est déjà pris.";
    } elseif ($nouveau_mdp && $nouveau_mdp !== $confirmer) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        if ($nouveau_mdp) {
            $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE utilisateur SET Pseudo = ?, Email = ?, Mot_de_passe = ? WHERE ID = ?");
            $stmt->execute([$nouveau_pseudo, $email, $hash, $_SESSION["user_id"]]);
        } else {
            $stmt = $pdo->prepare("UPDATE utilisateur SET Pseudo = ?, Email = ? WHERE ID = ?");
            $stmt->execute([$nouveau_pseudo, $email, $_SESSION["user_id"]]);
        }
        $_SESSION["pseudo"] = $nouveau_pseudo;
        $succes = "Profil mis à jour !";
    }
}

$stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE ID = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT Score, Date FROM classement WHERE ID_utilisateur = ? ORDER BY Score DESC LIMIT 5");
$stmt->execute([$_SESSION["user_id"]]);
$scores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>2Fast4U – Profil</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" href="../media/ico-car.ico">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
</head>
<body>
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

    <main>
        <h2>👤 <?= htmlspecialchars($user["Pseudo"]) ?></h2>

        <?php if ($erreur): ?>
            <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>
        <?php if ($succes): ?>
            <p class="succes"><?= htmlspecialchars($succes) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Pseudo</label>
            <input type="text" name="pseudo" value="<?= htmlspecialchars($user["Pseudo"]) ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user["Email"] ?? '') ?>">

            <label>Nouveau mot de passe <span style="color:#aaa;font-size:12px">(laisser vide = inchangé)</span></label>
            <input type="password" name="mot_de_passe">

            <label>Confirmer le mot de passe</label>
            <input type="password" name="confirmer">

            <button type="submit" class="btn-outline">Sauvegarder</button>
        </form>

        <h3 style="margin-top:32px">Meilleurs scores</h3>
        <?php if ($scores): ?>
            <table>
                <thead>
                    <tr><th>Score</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($scores as $s): ?>
                    <tr>
                        <td><?= $s["Score"] ?></td>
                        <td><?= $s["Date"] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun score pour l'instant.</p>
        <?php endif; ?>

        <a href="logout.php" class="btn-outline" style="margin-top:24px;display:inline-block">Se déconnecter</a>
    </main>
</body>
</html>