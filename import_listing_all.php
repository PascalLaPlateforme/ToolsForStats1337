<?php

$host = '127.0.0.1';
$db = 'listing_all';
$user = 'root';
$pass = '';

set_time_limit(3600); 

$port = '3306';
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false
];
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
     throw new PDOException($e->getMessage(), (int)$e->getCode());
}

if (($handle = fopen("listing_marseille.csv", "r")) !== FALSE)
{
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
    {
        $row_data = "";
        $first_column = true;
        foreach ($data as $d)
        {
            $value = $d;
            if (strlen($value) == 0)
            {
                $value = "null";
            }
            else
            {
                $value = '"'.addslashes($value).'"';
            }
            
            if ($first_column == false) { $value = ",".$value; }
            else { $first_column = false; }
            $row_data .= $value;
        }

        $sql = "INSERT INTO etudiant (actif, civilite, prenom, nom, groupe,
        email_perso, email_plateforme, naissance, age, lieu_de_naissance,
        telephone, adresse, code_postal, ville, etudes, situation,
        n_de, handicap, autorisation_image, attestation_parentale,
        fin_visa, badge, passage_titre, validation_titre,
        date_demission, motif, dossier, documents_manquants, contrat,
        present_prerentree, badge_ok, commentaires, photo, tshirt,
        check_pc, unknown_1, unknown_2, unknown_3, qpv,
        numero_de_secu, id_typeform, specialite, type_abandon,
        quotien_familial, groupe_admin) VALUES (".$row_data.")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

    }
    fclose($handle);
}

?>