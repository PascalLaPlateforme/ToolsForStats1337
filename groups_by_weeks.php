<?php

function DO_YOU_HAVE_SOME_MORE_OF_THEM_DATA()
{
    ini_set('xdebug.var_display_max_depth', 99);

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

    $groups_list = [
        "Venu pre-rentree",
        "Venu check PC - OK",
        "Venu check PC - KO",
        "Demandeur d'emploi", "Etudiant",
        "Moins de 18 ans", "20 ans ou moins",
        "21 a 29 ans", "30 a 35 ans",
        "Plus de 35 ans",
        "Groupe 1", "Groupe 2", "Groupe 3",
        "Groupe 4", "Groupe 5", "Groupe 6",
        "Groupe 7", "Groupe 8", "Groupe 9",
        "Groupe 10", "Groupe 11", "Groupe 12",
        "Groupe 13", "Groupe 14",
        "Profil Exceptionnel", "Profil Elevé",
        "Profil Moyen", "Profil Faible",
        "Profil Tres faible",
        "Test excellent", "Test très bien",
        "Test bien", "Test assez bien",
        "Test passable", "Test Pas de test",
        "Hommes", "Femmes",
        "Autres genres"
    ];

    $groups_data = [];

    foreach ($groups_list as $group)
    {
        $query = "SELECT e.email_plateforme as email FROM etudiant as e
        inner join groupe_etudiant as ge on e.id = ge.etudiant_fk
        inner join groupe as g on g.id = ge.groupe_fk
        where g.nom = \"$group\"";
        $stmt = $pdo->query($query);
        $data_group = $stmt->fetchAll();

        $groups_data[$group] = $data_group;
    }

    return ($groups_data);
}