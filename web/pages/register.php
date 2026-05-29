<?php
session_start();
require_once '../includes/db.php';

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pseudo = trim($_POST["pseudo"]);
    $mdp    = trim($_POST["mot_de_passe"]);
    $confirm = trim($_POST["confirmer"]);

    if ($mdp !== $confirm) {
        $erreur = "Passwords do not match.";
    } else {
        
        $stmt = $pdo->prepare("SELECT ID FROM utilisateur WHERE Pseudo = ?");
        $stmt->execute([$pseudo]);

        if ($stmt->fetch()) {
            $erreur = "This username is already taken.";
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
    <title>2Fast4U – Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="icon" href="../media/favicon.ico">
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

  <main class="auth-wrap">
    <div class="auth-box">
      <h2>Register</h2>
      <p class="auth-sub">Créer un compte</p>

      <?php if ($erreur): ?>
        <p class="erreur"><?= htmlspecialchars($erreur) ?></p>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          <label>Username</label>
          <input type="text" name="pseudo" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="mot_de_passe" required>
        </div>
        <div class="form-group">
          <label>Confirm Password</label>
          <input type="password" name="confirmer" required>
        </div>
        <button type="submit" class="btn-submit">Register</button>
      </form>

      <p class="auth-footer">
        Already an account ? <a href="login.php">Login</a>
      </p>
    </div>
  </main>

  <footer>
    © <?= date('Y') ?> 2Fast4U - Register 
</footer>
</body>
</html>