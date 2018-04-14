<?php
include "config.include.php";  //pour récupérer l'identifiant et le mot de passe
$url = "https://www.legtux.org/";
$cookie = "cookie.txt";
$timeout = 60;
$nom_local_zip = "log.zip";
$postdata = "login=" . $login . "&password=" . $password ;

// connexion à legtux via la page de login
$ch = curl_init();
curl_setopt ( $ch, CURLOPT_URL, $url . "index.php?page=connec" );
curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6" );
curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 0 );
curl_setopt ( $ch, CURLOPT_HEADER, 0);
curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt ( $ch, CURLOPT_COOKIEJAR, $cookie );
curl_setopt ( $ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt ( $ch, CURLOPT_REFERER, $url . "index.php" );
curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postdata );
curl_setopt ( $ch, CURLOPT_POST, 1 );

$result = curl_exec ( $ch );
$result = curl_exec ( $ch );

curl_setopt ( $ch, CURLOPT_POST, 0 );

// fin de connexion, on ne ferme pas la curl en cours pour l'utiliser aprés
// récupération du fichier de log_user_YYYMMDD_hhmm.zip

//////////// récupération des log de cette semaine: access.log ////////////////
curl_setopt ( $ch, CURLOPT_URL, $url . "member/apache_log.php" );
if ( $nom_local_zip ) {
    $fp = fopen( $nom_local_zip, 'w+b' ) or die( "Le fichier '$nom_local_zip' n'a pu etre ouvert en ecriture" );
    curl_setopt( $ch, CURLOPT_FILE, $fp );
} else {
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
}

$ret = curl_exec( $ch );

if ( $nom_local_zip ) {
    fclose( $fp );
}
if ( $ret === false ) {
    die( "Une erreur a été rencontrée : " . curl_error() );
}

// décompression de l'archive zip
$zip = new ZipArchive;
$zip->open($nom_local_zip);
$zip->extractTo('./');
$zip->close();

//////////// récupération des log de la semaine dernière: access_old.log //////
curl_setopt ( $ch, CURLOPT_URL, $url . "member/apache_log_old.php" );
if ( $nom_local_zip ) {
    $fp = fopen( $nom_local_zip, 'w+b' ) or die( "Le fichier '$nom_local_zip' n'a pu etre ouvert en ecriture" );
    curl_setopt( $ch, CURLOPT_FILE, $fp );
} else {
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
}
$ret = curl_exec( $ch );

if ( $nom_local_zip ) {
    fclose( $fp );
}
if ( $ret === false ) {
    die( "Une erreur a été rencontrée : " . curl_error() );
}

// décompression de l'archive zip
$zip = new ZipArchive;
$zip->open($nom_local_zip);
$zip->extractTo('./');
$zip->close();

curl_close( $ch );   //on ferme la curl

//nettoyage: efface les fichiers intermédiaires
unlink($cookie);
unlink($nom_local_zip);

//affiche la liste des fichiers dans le repertoire
    $rep = opendir( "./");
    chdir( "./");
    while ( $file = readdir( $rep ) ) {
        if ( $file != '..' && $file != '.' && $file != '' ) {
            if ( ( $file ) ) {
                echo "<a href=$file target=_blank>$file</a><br>";
            }
        }
    }
    closedir( $rep );