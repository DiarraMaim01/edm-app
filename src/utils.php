<?php
function page_header(string $title='EDM App') {
    echo "<!doctype html>
    <html>
        <head>
            <meta charset='utf-8'>
            <title>{$title}</title>
            <link rel='stylesheet' href='/edmApp/public/css/app.css'>
        </head>
        <body>";
    
    // Navigation avec info utilisateur
    echo "<nav>";
    echo "<a href='/edmApp/public/'>Accueil</a> | ";
    echo "<a href='/edmApp/public/utilisateurs_list.php'>Utilisateurs</a>";
    
    // Afficher l'utilisateur connecté
   // src/utils.php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

    if (!empty($_SESSION['user'])) {
        $user = $_SESSION['user'];
        echo " | <span>Connecté en tant que: <strong>{$user['prenom']} {$user['nom']}</strong> ({$user['role']})</span>";
        echo " | <a href='/edmApp/public/logout.php'>Déconnexion</a>";
    } else {
        echo " | <a href='/edmApp/public/login.php'>Connexion</a>";
    }
    
    echo "</nav><hr>";
}

function page_footer() { 
    echo "<hr><small>EDM App</small>
        </body>
    </html>"; 
}