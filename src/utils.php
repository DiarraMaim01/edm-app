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
      echo "<nav><a href='/edmApp/public/'>Accueil</a> | 
              <a href='/edmApp/public/utilisateurs_list.php'>Utilisateurs</a></nav><hr>";
}
function page_footer() { echo "<hr><small>EDM App</small>
    </body>
  </html>"; }
