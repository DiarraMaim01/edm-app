<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function is_logged_in(): bool {
    return !empty($_SESSION['user']);
}

/**
 * Redirige vers la page de login si non connecté
 */
function require_auth(): void {
    if (!is_logged_in()) {
        header('Location: /edmApp/public/login.php?error=2');
        exit;
    }
}

/**
 * Vérifie si l'utilisateur a un des rôles requis
 */
function has_role(array $allowed_roles): bool {
    if (!is_logged_in()) {
        return false;
    }
    return in_array($_SESSION['user']['role'], $allowed_roles);
}

/**
 * Redirige si l'utilisateur n'a pas le rôle requis
 */
function require_role(array $allowed_roles): void {
    require_auth();
    
    if (!has_role($allowed_roles)) {
        http_response_code(403);
        echo "<h1>Accès interdit</h1>";
        echo "<p>Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
        echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
        exit;
    }
}