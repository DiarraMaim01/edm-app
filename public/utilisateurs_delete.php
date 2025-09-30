<?php
require_once __DIR__.'/../src/db.php';

// (plus tard) require _auth.php + _roles.php + requireRole(['ADMINISTRATEUR'])

//  lire $_GET['id'] puis faire DELETE FROM utilisateur WHERE id=...
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header('Location: utilisateurs_list.php'); exit;
}

try {
  $pdo = db();

  //  empêcher qu'on supprime "son propre" compte
  session_start();
  if (!empty($_SESSION['user']) && $_SESSION['user']['id'] == $id) {
     throw new Exception("Vous ne pouvez pas supprimer votre propre compte.");
   }

  $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id = :id");
  $stmt->execute([':id' => $id]);

  header('Location: utilisateurs_list.php'); exit;

} catch (PDOException $e) {
  // Si l'utilisateur est référencé par incident/visa, MySQL (FK) va refuser (erreur 1451)
  $msg = $e->getMessage();
  if (strpos($msg, '1451') !== false || stripos($msg, 'foreign key') !== false) {
    // Message clair
    echo "<p style='color:red'>Suppression impossible : cet utilisateur est utilisé dans d'autres enregistrements (incidents/visas...).</p>";
    echo "<p><a href='utilisateurs_list.php'>Retour à la liste</a></p>";
    exit;
  }
  // Autres erreurs
  echo "<p style='color:red'>Erreur DB : ".htmlspecialchars($msg)."</p>";
  echo "<p><a href='utilisateurs_list.php'>Retour à la liste</a></p>";
  exit;

} catch (Exception $e) {
  echo "<p style='color:red'>".htmlspecialchars($e->getMessage())."</p>";
  echo "<p><a href='utilisateurs_list.php'>Retour à la liste</a></p>";
  exit;
}
