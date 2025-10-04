<?php
require_once __DIR__.'/../src/db.php';
// TODO: plus tard => require auth.php + roles.php + requireRole(['ADMINISTRATEUR'])
// (on laisse l'accès libre pour l'instant le temps de mettre l'auth en place)
require_once __DIR__.'/auth.php';
require_auth();
require_role(['ADMINISTRATEUR']);
// Redirige si l'appel n'est pas en POST (sécurité basique)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: utilisateurs_list.php'); 
  exit;
}

// Récupération des champs du formulaire
$nom        = trim($_POST['nom'] ?? '');
$prenom     = trim($_POST['prenom'] ?? '');
$matricule  = trim($_POST['matricule'] ?? '');
$email      = trim($_POST['email'] ?? '');
$telephone  = trim($_POST['telephone'] ?? '');
$role       = $_POST['role'] ?? 'TECHNICIEN';
$password   = $_POST['password'] ?? '';   // <- NOUVEAU

// Validation minimale
$errors = [];
if ($nom === '')        $errors[] = "Nom requis";
if ($prenom === '')     $errors[] = "Prénom requis";
if ($matricule === '')  $errors[] = "Matricule requis";
if ($email === '')      $errors[] = "Email requis";
if ($password === '')   $errors[] = "Mot de passe requis";
// (optionnel) forcer une longueur minimale pour le mdp
if ($password !== '' && strlen($password) < 8) $errors[] = "Mot de passe trop court (8 caractères min)";

if ($errors) {
  echo "<p style='color:red'>".implode('<br>', array_map('htmlspecialchars',$errors))."</p>";
  echo "<p><a href='utilisateurs_create.php'>Retour</a></p>";
  exit;
}

// Hash du mot de passe AVANT l'insertion
$passwordHash = password_hash($_POST['password'] ??'', PASSWORD_DEFAULT);

try {
  $pdo = db();

  // (optionnel mais recommandé) : vérifier l'unicité email
  $check = $pdo->prepare("SELECT id FROM utilisateur WHERE email = :email LIMIT 1");
  $check->execute([':email' => $email]);
  if ($check->fetch()) {
    echo "<p style='color:red'>Un utilisateur existe déjà avec cet email.</p>";
    echo "<p><a href='utilisateurs_create.php'>Retour</a></p>";
    exit;
  }

  // Insertion
  $stmt = $pdo->prepare("
    INSERT INTO utilisateur (nom, prenom, matricule, email, telephone, role, equipe_id, password)
    VALUES (:nom, :prenom, :matricule, :email, :telephone, :role, NULL, :password)
  ");
  $stmt->execute([
    ':nom'       => $nom,
    ':prenom'    => $prenom,
    ':matricule' => $matricule,
    ':email'     => $email,
    ':telephone' => $telephone,
    ':role'      => $role,
    ':password'  => $passwordHash,
  ]);

  // Redirection vers la liste
  header('Location: utilisateurs_list.php'); 
  exit;

} catch (Throwable $e) {
  echo "<p style='color:red'>Erreur DB: ".htmlspecialchars($e->getMessage())."</p>";
  echo "<p><a href='utilisateurs_create.php'>Retour</a></p>";
  exit;
}
