<?php
require_once __DIR__.'/../src/utils.php';
page_header('Utilisateurs - Nouveau');
// TODO: plus tard => require _auth.php + _roles.php + requireRole(['ADMINISTRATEUR'])
?>
<h1>Créer un utilisateur</h1>

<form method="post" action="utilisateurs_store.php">
  <label>Nom<input type="text" name="nom" required></label><br>
  <label>Prénom<input type="text" name="prenom" required></label><br>
  <label>Matricule<input type="text" name="matricule" required></label><br>
  <label>Email<input type="email" name="email" required></label><br>
  <label>Téléphone<input type="text" name="telephone"></label><br>
  <label>Rôle
    <select name="role" required>
      <option value="TECHNICIEN">TECHNICIEN</option>
      <option value="CHEF_SERVICE">CHEF_SERVICE</option>
      <option value="ADMINISTRATEUR">ADMINISTRATEUR</option>
    </select>
  </label><br><br>
  <button type="submit">Enregistrer</button>
  <a href="utilisateurs_list.php">Annuler</a>
</form>

<?php page_footer(); ?>
