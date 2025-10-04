<?php

// TODO: plus tard => vérifier la session & rôle (admin/chef)
require_once __DIR__.'/auth.php';
require_auth();
require_role(['ADMINISTRATEUR', 'CHEF_SERVICE']);

require_once __DIR__.'/../src/db.php';
require_once __DIR__.'/../src/utils.php';
page_header('Utilisateurs - Liste');


$pdo = db();
//  SELECT * FROM utilisateur ORDER BY id DESC
try {
  $stmt = $pdo->query("SELECT id, nom, prenom, email, matricule, role FROM utilisateur ORDER BY id DESC");
  $users = $stmt->fetchAll();
} catch (Throwable $e) {
  $users = [];
  echo "<p style='color:red'>Erreur DB: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<h1>Utilisateurs</h1>

<p>
  <!-- TODO: n’afficher ce lien que si ADMIN (plus tard) -->
  <?php if (has_role(['ADMINISTRATEUR'])): ?>
  <a href="utilisateurs_create.php">+ Nouvel utilisateur</a>
  <?php endif; ?>
</p>

<table border="1" cellpadding="6">
  <thead>
    <tr>
      <th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Matricule</th><th>Rôle</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <!-- boucle  pour afficher les lignes -->
     <?php if (empty($users)): ?>
  <tr><td colspan="7" style="text-align:center">Aucun utilisateur pour l’instant</td></tr>
<?php else: ?>
  <?php foreach ($users as $u): ?>
    <tr>
      <td><?= htmlspecialchars($u['id']) ?></td>
      <td><?= htmlspecialchars($u['nom']) ?></td>
      <td><?= htmlspecialchars($u['prenom']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= htmlspecialchars($u['matricule']) ?></td>
      <td>
          <?php
            $role = htmlspecialchars($u['role']);
            $badge = match ($u['role']) {
             'ADMINISTRATEUR' => "<span class='badge badge-admin'>{$role}</span>",
             'CHEF_SERVICE'   => "<span class='badge badge-chef'>{$role}</span>",
              default          => "<span class='badge badge-tech'>{$role}</span>",
            };
            echo $badge;
          ?>
      </td>
      <td>
        <?php if (has_role(['ADMINISTRATEUR'])): ?>
        <a href="utilisateurs_edit.php?id=<?= urlencode($u['id']) ?>">Modifier</a>
        |
        <a href="utilisateurs_delete.php?id=<?= urlencode($u['id']) ?>"
           onclick="return confirm('Supprimer cet utilisateur ?');">Supprimer</a>
        <?php endif; ?>
      </td>
      
    </tr>
  <?php endforeach; ?>
<?php endif; ?>

    </tbody>
</table>

<?php page_footer(); ?>
