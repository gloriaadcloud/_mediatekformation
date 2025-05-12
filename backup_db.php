<?php
// Configuration de la base de données
$dbHost = "localhost";  // O2switch utilise souvent "localhost"
$dbUser = "adgl8886_gloria";
$dbPass = "Foligloria7@";
$dbName = "adgl8886__mediatekformation";

// Chemin de sauvegarde
$backupDir = "/public_html/_mediatekformation/backups/";
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0777, true);
}

// Nom du fichier avec la date
$date = date("Y-m-d");
$backupFile = "/home/adgl8886/public_html/_mediatekformation/backups/backup_{$dbName}_{$date}.sql";

// Commande mysqldump avec compression gzip
$dumpCommand = "mysqldump -h $dbHost -u $dbUser -p'$dbPass' $dbName  > $backupFile";
exec($dumpCommand, $output, $returnVar);

if ($returnVar === 0) {
    echo "✅ Sauvegarde réussie : $backupFile\n";
} else {
    echo "❌ Erreur lors de la sauvegarde !\n";
}

?>