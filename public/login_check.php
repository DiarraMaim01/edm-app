<?php
// public/login_check.php
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
require_once __DIR__.'/../src/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /edmApp/public/login.php');
  exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
  header('Location: /edmApp/public/login.php?error=1');
  exit;
}

try {
  $pdo = db();
  $stmt = $pdo->prepare("
    SELECT id, nom, prenom, email, role, password
    FROM utilisateur
    WHERE email = :email
    LIMIT 1
  ");
  $stmt->execute([':email' => $email]);
  $user = $stmt->fetch();

  if (!$user) {
    header('Location: /edmApp/public/login.php?error=1');
    exit;
  }

  $dbPass = (string)$user['password'];
  $ok = false;

  // Cas 1 : mot de passe déjà hashé (bcrypt -> commence souvent par $2y$ et ~60 char)
  if (strlen($dbPass) >= 55 && str_starts_with($dbPass, '$2')) {
    $ok = password_verify($password, $dbPass);

    // (optionnel) si l’algorithme évolue : rehash
    if ($ok && password_needs_rehash($dbPass, PASSWORD_DEFAULT)) {
      $newHash = password_hash($password, PASSWORD_DEFAULT);
      $up = $pdo->prepare("UPDATE utilisateur SET password = :p WHERE id = :id");
      $up->execute([':p' => $newHash, ':id' => $user['id']]);
    }

  } else {
    // Cas 2 : ancien mot de passe en clair en base
    if (hash_equals($dbPass, $password)) {
      $ok = true;
      // On rehash IMMÉDIATEMENT pour sécuriser la base
      $newHash = password_hash($password, PASSWORD_DEFAULT);
      $up = $pdo->prepare("UPDATE utilisateur SET password = :p WHERE id = :id");
      $up->execute([':p' => $newHash, ':id' => $user['id']]);
    } else {
      $ok = false;
    }
  }

  if (!$ok) {
    header('Location: /edmApp/public/login.php?error=1');
    exit;
  }

  // OK : ouverture de session
  session_regenerate_id(true);
  $_SESSION['user'] = [
    'id'     => (int)$user['id'],
    'nom'    => $user['nom'],
    'prenom' => $user['prenom'],
    'email'  => $user['email'],
    'role'   => $user['role'],
  ];

  header('Location: /edmApp/public/utilisateurs_list.php');
  exit;

} catch (Throwable $e) {
  // En cas d’erreur, renvoie au login
  header('Location: /edmApp/public/login.php?error=1');
  exit;
}
