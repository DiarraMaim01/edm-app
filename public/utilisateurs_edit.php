<?php
require_once __DIR__.'/../src/db.php';
require_once __DIR__.'/../src/utils.php';
page_header('Utilisateurs - Modifier');
// TODO: plus tard => require _auth.php + _roles.php + requireRole(['ADMINISTRATEUR'])

// TODO: récupérer ?id=... via $_GET['id']

// TODO: SELECT ... WHERE id=? et préremplir le formulaire

// 1) je Récupère l'id depuis la query string
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  echo "<p style='color:red'>ID invalide</p>";
  page_footer();
  exit;
}

// 2) Charger l'utilisateur correspondant
$pdo = db();
$stmt = $pdo->prepare("
  SELECT id, nom, prenom, matricule, email, telephone, role
  FROM utilisateur
  WHERE id = :id
");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch();

if (!$user) {
  echo "<p style='color:red'>Utilisateur introuvable</p>";
  page_footer();
  exit;
}
?>
<h1>Modifier un utilisateur</h1>

<form method="post" action="utilisateurs_update.php?id=<?= htmlspecialchars($_GET['id'] ?? '') ?>">
  <!--   inputs pre-rempli avec les valeurs en base -->
  <label>Nom
    <input type="text" name="nom" required value="<?= htmlspecialchars($user['nom']) ?>">
  </label><br>

  <label>Prénom
    <input type="text" name="prenom" required value="<?= htmlspecialchars($user['prenom']) ?>">
  </label><br>

  <label>Matricule
    <input type="text" name="matricule" required value="<?= htmlspecialchars($user['matricule']) ?>">
  </label><br>

  <label>Email
    <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">
  </label><br>

  <label>Téléphone
    <input type="text" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>">
  </label><br>

  <label>Rôle
    <select name="role" required>
      <?php
        // Pré-sélectionner le rôle actuel de l'utilisateur
        $roles = ['TECHNICIEN','CHEF_SERVICE','ADMINISTRATEUR'];
        foreach ($roles as $r) {
          $sel = ($user['role'] === $r) ? 'selected' : '';
          echo "<option value='".htmlspecialchars($r)."' $sel>".htmlspecialchars($r)."</option>";
        }
      ?>
    </select>
  </label><br><br>

  <button type="submit">Mettre à jour</button>
  <a href="utilisateurs_list.php">Annuler</a>
</form>

<?php page_footer(); ?>
