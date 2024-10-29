<?php

$host = '127.0.0.1';
$db = 'listing_all';
$user = 'root';
$pass = '';

$host_api = '127.0.0.1';
$db_api = 'api_live';
$user_api = 'root';
$pass_api = '';

// $host = '82.165.190.244';
// $db = 'api_live';
// $user = 'api_ro';
// $pass = 'L9u9b7q3@';

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

$query = "SELECT id FROM etudiant";
$stmt = $pdo->query($query);
$data_all = $stmt->fetchAll();
$total_inscrits = count($data_all);

$query = "SELECT e.id FROM etudiant as e
inner join groupe_etudiant as ge on e.id = ge.etudiant_fk
inner join groupe as g on g.id = ge.groupe_fk
where g.nom = \"Venu pre-rentree\"
or g.nom = \"Venu check PC - OK\"
or g.nom = \"Venu check PC - KO\"
group by e.id";
$stmt = $pdo->query($query);
$data = $stmt->fetchAll();
$venu_a_un_des_deux_rdv = count($data);

echo "Total inscrits: ".$total_inscrits."<br />";
$pourcent_venus_a_un_rdv = ($venu_a_un_des_deux_rdv * 100) / $total_inscrits;
echo "Total venus a un rdv de rentree: ".$venu_a_un_des_deux_rdv." ( ".round($pourcent_venus_a_un_rdv, 2)."% )<br />";
echo "<br />";

$the_groups = [
    "Venu check PC - OK",
    "Venu check PC - KO",
    "Demandeur d'emploi",
    "Etudiant",
    "Moins de 18 ans",
    "20 ans ou moins",
    "21 a 29 ans",
    "30 a 35 ans",
    "Plus de 35 ans",
    // "Groupe 1", "Groupe 2", "Groupe 3",
    // "Groupe 4", "Groupe 5", "Groupe 6",
    // "Groupe 7", "Groupe 8", "Groupe 9",
    // "Groupe 10", "Groupe 11", "Groupe 12",
    // "Groupe 13", "Groupe 14",
    "Profil Exceptionnel",
    "Profil Elevé",
    "Profil Moyen",
    "Profil Faible",
    "Profil Tres faible",
    "Test excellent",
    "Test très bien",
    "Test bien",
    "Test assez bien",
    "Test passable",
    "Test Pas de test",
    "Hommes", "Femmes", "Autres genres"
];

foreach ($the_groups as $group)
{
    $query = "SELECT e.id FROM etudiant as e
    inner join groupe_etudiant as ge on e.id = ge.etudiant_fk
    inner join groupe as g on g.id = ge.groupe_fk
    where g.nom = \"$group\"";
    $stmt = $pdo->query($query);
    $data_groupe = $stmt->fetchAll();
    $total_groupe = count($data_groupe);

    if ($total_groupe > 0)
    {
        $query = "SELECT e.id FROM etudiant as e
        inner join groupe_etudiant as ge on e.id = ge.etudiant_fk
        inner join groupe as g on g.id = ge.groupe_fk
        where g.nom = \"$group\"
        and e.id in (
            SELECT e.id FROM etudiant as e
            inner join groupe_etudiant as ge on e.id = ge.etudiant_fk
            inner join groupe as g on g.id = ge.groupe_fk
            where g.nom = \"Venu pre-rentree\"
            or g.nom = \"Venu check PC - OK\"
            or g.nom = \"Venu check PC - KO\"
            group by e.id
        )";
        $stmt = $pdo->query($query);
        $data = $stmt->fetchAll();
        $total_groupe_venu_a_un_rdv = count($data);

        $pourcent_groupe = ($total_groupe * 100) / $total_inscrits;
        echo "<b>$group</b>: ".$total_groupe." ( ".round($pourcent_groupe, 2)."% )<br />";
        $pourcent_groupe_venus_a_un_rdv = ($total_groupe_venu_a_un_rdv * 100) / $total_groupe;
        echo "-> ".$total_groupe_venu_a_un_rdv." sont venus a un des deux RDV de rentree ( ".round($pourcent_groupe_venus_a_un_rdv, 2)."% )<br />";
        echo "<br />";
    }
}

$query = "SELECT e.id FROM etudiant as e
inner join groupe_etudiant as ge on e.id = ge.etudiant_fk
inner join groupe as g on g.id = ge.groupe_fk
where g.nom = \"Test excellent\"
or g.nom = \"Test très bien\"
and e.id in (
    SELECT e.id FROM etudiant as e
    inner join groupe_etudiant as ge on e.id = ge.etudiant_fk
    inner join groupe as g on g.id = ge.groupe_fk
    where g.nom = \"Profil Exceptionnel\"
    or g.nom = \"Profil Elevé\"
)";
$stmt = $pdo->query($query);
$data = $stmt->fetchAll();
$total = count($data);

$pourcent = ($total * 100) / $total_inscrits;
echo "<b>Exceptionnel ou Eleve (Profil et Test)</b>: ".$total." ( ".round($pourcent, 2)."% )<br />";

$query = "SELECT e.id FROM etudiant as e
inner join groupe_etudiant as ge on e.id = ge.etudiant_fk
inner join groupe as g on g.id = ge.groupe_fk
where e.id in (
    SELECT e.id FROM etudiant as e
    inner join groupe_etudiant as ge on e.id = ge.etudiant_fk
    inner join groupe as g on g.id = ge.groupe_fk
    where g.nom = \"Venu pre-rentree\"
    or g.nom = \"Venu check PC - OK\"
    or g.nom = \"Venu check PC - KO\"
    group by e.id
)
and e.id in (
    SELECT e.id FROM etudiant as e
    inner join groupe_etudiant as ge on e.id = ge.etudiant_fk
    inner join groupe as g on g.id = ge.groupe_fk
    where g.nom = \"Test excellent\"
    or g.nom = \"Test très bien\"
    and e.id in (
        SELECT e.id FROM etudiant as e
        inner join groupe_etudiant as ge on e.id = ge.etudiant_fk
        inner join groupe as g on g.id = ge.groupe_fk
        where g.nom = \"Profil Exceptionnel\"
        or g.nom = \"Profil Elevé\"
        group by e.id
    )
    group by e.id
)
group by e.id";
$stmt = $pdo->query($query);
$data = $stmt->fetchAll();
$total_venu_a_un_rdv = count($data);

$pourcent_venus_a_un_rdv = ($total_venu_a_un_rdv * 100) / $total;
echo "-> ".$total_venu_a_un_rdv." sont venus a un des deux RDV de rentree ( ".round($pourcent_venus_a_un_rdv, 2)."% )<br />";
echo "<br />";

?>