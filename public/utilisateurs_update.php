<?php
require_once __DIR__.'/../src/db.php';
// TODO: plus tard => require _auth.php + _roles.php + requireRole(['ADMINISTRATEUR'])

// TODO: lire $_GET['id'] et $_POST[...] puis faire UPDATE utilisateur SET ...


$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: utilisateurs_list.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: utilisateurs_edit.php?id='.$id); exit;
}

$nom       = trim($_POST['nom'] ?? '');
$prenom    = trim($_POST['prenom'] ?? '');
$matricule = trim($_POST['matricule'] ?? '');
$email     = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$role      = $_POST['role'] ?? 'TECHNICIEN';

// Validation minimale
$errors = [];
if ($nom === '')        $errors[] = "Nom requis";
if ($prenom === '')     $errors[] = "Prénom requis";
if ($matricule === '')  $errors[] = "Matricule requis";
if ($email === '')      $errors[] = "Email requis";

if ($errors) {
  echo "<p style='color:red'>".implode('<br>', array_map('htmlspecialchars',$errors))."</p>";
  echo "<p><a href='utilisateurs_edit.php?id=".urlencode($id)."'>Retour</a></p>";
  exit;
}

try {
  $pdo = db();

  $sql = "UPDATE utilisateur
          SET nom=:nom, prenom=:prenom, matricule=:matricule, email=:email,
              telephone=:telephone, role=:role
          WHERE id=:id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':nom'       => $nom,
    ':prenom'    => $prenom,
    ':matricule' => $matricule,
    ':email'     => $email,
    ':telephone' => $telephone,
    ':role'      => $role,
    ':id'        => $id,
  ]);

  header('Location: utilisateurs_list.php'); exit;

} catch (PDOException $e) {
  // Gestion des doublons (si UNIQUE sur email/matricule)
  $msg = $e->getMessage();
  if (stripos($msg, 'Duplicate') !== false) {
    echo "<p style='color:red'>Email ou matricule déjà utilisé.</p>";
  } else {
    echo "<p style='color:red'>Erreur DB: ".htmlspecialchars($msg)."</p>";
  }
  echo "<p><a href='utilisateurs_edit.php?id=".urlencode($id)."'>Retour</a></p>";
  exit;
}

header('Location: utilisateurs_list.php'); exit;
