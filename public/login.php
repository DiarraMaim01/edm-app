<?php
require_once __DIR__.'/../src/utils.php';
page_header('Connexion');

// si l’utilisateur est déjà connecté
if (!empty($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

$error = isset($_GET['error']) ? "Email ou mot de passe incorrect" : "";
?>
<h1>Connexion</h1>

<?php if ($error): ?>
  <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="login_check.php">
  <div>
    <label>Email :</label>
    <input type="email" name="email" required>
  </div>
  <div>
    <label>Mot de passe :</label>
    <input type="password" name="password" required>
  </div>
  <button type="submit">Se connecter</button>
</form>

<p><small>Mot de passe par défaut : <b>123456</b></small></p>
<?php page_footer(); ?>
