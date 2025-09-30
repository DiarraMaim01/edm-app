<?php
require_once __DIR__.'/../src/db.php';
// TODO: plus tard => require _auth.php + _roles.php + requireRole(['ADMINISTRATEUR'])

// TODO:  lire $_POST et feras l'INSERT dans la table utilisateur

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: utilisateurs_list.php'); exit;
}

$nom       = trim($_POST['nom'] ?? '');
$prenom    = trim($_POST['prenom'] ?? '');
$matricule = trim($_POST['matricule'] ?? '');
$email     = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$role      = $_POST['role'] ?? 'TECHNICIEN';

// Validation 
$errors = [];
if ($nom === '')        $errors[] = "Nom requis";
if ($prenom === '')     $errors[] = "Prénom requis";
if ($matricule === '')  $errors[] = "Matricule requis";
if ($email === '')      $errors[] = "Email requis";

if ($errors) {
  // Affichage simple d’erreurs 
  echo "<p style='color:red'>".implode('<br>', array_map('htmlspecialchars',$errors))."</p>";
  echo "<p><a href='utilisateurs_create.php'>Retour</a></p>";
  exit;
}

try {
  $pdo = db();
  $stmt = $pdo->prepare("
    INSERT INTO utilisateur (nom, prenom, matricule, email, telephone, role, equipe_id)
    VALUES (:nom, :prenom, :matricule, :email, :telephone, :role, NULL)
  ");
  $stmt->execute([
    ':nom'       => $nom,
    ':prenom'    => $prenom,
    ':matricule' => $matricule,
    ':email'     => $email,
    ':telephone' => $telephone,
    ':role'      => $role,
  ]);
  // redirection vers utilisateurs_list.php
  header('Location: utilisateurs_list.php'); exit;

} catch (Throwable $e) {
  echo "<p style='color:red'>Erreur DB: ".htmlspecialchars($e->getMessage())."</p>";
  echo "<p><a href='utilisateurs_create.php'>Retour</a></p>";
  exit;
}



