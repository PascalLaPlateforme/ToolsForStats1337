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

function    groupeVenuPreRentree($pdo)
{
    $query = "INSERT INTO `groupe` (`nom`) VALUES ('Venu pre-rentree')";
    $stmt = $pdo->query($query);
    $id = $pdo->lastInsertId();

    $query = "SELECT id FROM etudiant WHERE present_prerentree = 'Oui'";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $d["id"]]);
    }
}

function    groupeVenuCheckPCOK($pdo)
{
    $query = "INSERT INTO `groupe` (`nom`) VALUES ('Venu check PC - OK')";
    $stmt = $pdo->query($query);
    $id = $pdo->lastInsertId();

    $query = "SELECT id FROM etudiant WHERE check_pc = 'OK'";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $d["id"]]);
    }
}

function    groupeVenuCheckPCKO($pdo)
{
    $query = "INSERT INTO `groupe` (`nom`) VALUES ('Venu check PC - KO')";
    $stmt = $pdo->query($query);
    $id = $pdo->lastInsertId();

    $query = "SELECT id FROM etudiant WHERE check_pc = 'KO'";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $d["id"]]);
    }
}

function    groupeMoinsDe18Ans($pdo)
{
    $query = "INSERT INTO `groupe` (`nom`) VALUES ('Moins de 18 ans')";
    $stmt = $pdo->query($query);
    $id = $pdo->lastInsertId();

    $query = "SELECT id FROM etudiant WHERE age < 18";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $d["id"]]);
    }
}

function    groupe20AnsOuMoins($pdo)
{
    $query = "INSERT INTO `groupe` (`nom`) VALUES ('20 ans ou moins')";
    $stmt = $pdo->query($query);
    $id = $pdo->lastInsertId();

    $query = "SELECT id FROM etudiant WHERE age <= 20";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $d["id"]]);
    }
}

function    groupe21A29Ans($pdo)
{
    $query = "INSERT INTO `groupe` (`nom`) VALUES ('21 a 29 ans')";
    $stmt = $pdo->query($query);
    $id = $pdo->lastInsertId();

    $query = "SELECT id FROM etudiant WHERE age > 20 and age < 30";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $d["id"]]);
    }
}

function    groupe30A35Ans($pdo)
{
    $query = "INSERT INTO `groupe` (`nom`) VALUES ('30 a 35 ans')";
    $stmt = $pdo->query($query);
    $id = $pdo->lastInsertId();

    $query = "SELECT id FROM etudiant WHERE age > 30 and age <= 35";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $d["id"]]);
    }
}

function    groupePlusDe35Ans($pdo)
{
    $query = "INSERT INTO `groupe` (`nom`) VALUES ('Plus de 35 ans')";
    $stmt = $pdo->query($query);
    $id = $pdo->lastInsertId();

    $query = "SELECT id FROM etudiant WHERE age > 35";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $d["id"]]);
    }
}

function    groupeDemandeurDEmploi($pdo)
{
    $query = "INSERT INTO `groupe` (`nom`) VALUES (\"Demandeur d'emploi\")";
    $stmt = $pdo->query($query);
    $id = $pdo->lastInsertId();

    $query = "SELECT id FROM etudiant WHERE situation = \"Demandeur d'emploi\"";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $d["id"]]);
    }
}

function    groupeEtudiant($pdo)
{
    $query = "INSERT INTO `groupe` (`nom`) VALUES (\"Etudiant\")";
    $stmt = $pdo->query($query);
    $id = $pdo->lastInsertId();

    $query = "SELECT id FROM etudiant WHERE situation = \"Etudiant\"";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $d["id"]]);
    }
}

function    groupeHommes($pdo)
{
    $query = "INSERT INTO `groupe` (`nom`) VALUES (\"Hommes\")";
    $stmt = $pdo->query($query);
    $id = $pdo->lastInsertId();

    $query = "SELECT id FROM etudiant WHERE civilite = \"Monsieur\"";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $d["id"]]);
    }
}

function    groupeFemmes($pdo)
{
    $query = "INSERT INTO `groupe` (`nom`) VALUES (\"Femmes\")";
    $stmt = $pdo->query($query);
    $id = $pdo->lastInsertId();

    $query = "SELECT id FROM etudiant WHERE civilite = \"Madame\"";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $d["id"]]);
    }
}

function    groupeAutres($pdo)
{
    $query = "INSERT INTO `groupe` (`nom`) VALUES (\"Autres genres\")";
    $stmt = $pdo->query($query);
    $id = $pdo->lastInsertId();

    $query = "SELECT id FROM etudiant WHERE civilite = \"Mx.\"";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $d["id"]]);
    }
}

function    groupeParGroupe($pdo)
{
    for ($i = 1; $i < 15; ++$i)
    {
        $query = "INSERT INTO `groupe` (`nom`) VALUES (\"Groupe ".$i."\")";
        $stmt = $pdo->query($query);
        $id = $pdo->lastInsertId();

        $query = "SELECT id FROM etudiant WHERE groupe = $i";
        $stmt = $pdo->query($query);
        $data = $stmt->fetchAll();

        foreach ($data as $d)
        {
            $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id, $d["id"]]);
        }   
    }
}

function    groupeParProfils($pdo)
{
    $from_recrut = [];
    if (($handle = fopen("eval_recrut.csv", "r")) !== FALSE)
    {
        while (($line = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
            array_push($from_recrut, [
                $line[2], $line[14], $line[15], $line[24], $line[26]
            ]);
        }
        fclose($handle);
    } else { return ; }

    $profil_groups = [
        "5- Exceptionnel" => ["Profil Exceptionnel", 0],
        "4- Elevé" => ["Profil Elevé", 0],
        "3- Moyen" => ["Profil Moyen", 0],
        "2- Faible" => ["Profil Faible", 0],
        "1- Très faible" => ["Profil Tres faible", 0]
    ];
    
    foreach ($profil_groups as &$p)
    {
        $query = "INSERT INTO `groupe` (`nom`) VALUES (\"".$p[0]."\")";
        $stmt = $pdo->query($query);
        $p[1] = $pdo->lastInsertId();
    }

    $test_groups = [
        "Pas de test" => ["Test Pas de test", 0],
        "passable" => ["Test passable", 0],
        "assez bien" => ["Test assez bien", 0],
        "bien" => ["Test bien", 0],
        "très bien" => ["Test très bien", 0],
        "excellent" => ["Test excellent", 0]
    ];

    foreach ($test_groups as &$p)
    {
        $query = "INSERT INTO `groupe` (`nom`) VALUES (\"".$p[0]."\")";
        $stmt = $pdo->query($query);
        $p[1] = $pdo->lastInsertId();
    }

    $query = "SELECT id, prenom, nom, email_perso, email_plateforme FROM etudiant";
    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll();

    foreach ($data as $d)
    {
        $found = false;
        foreach ($from_recrut as $f)
        {
            if ($d["email_perso"] == $f[0])
            {
                $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$profil_groups[$f[3]][1], $d["id"]]);

                $sql = "INSERT INTO groupe_etudiant (groupe_fk, etudiant_fk) VALUES (?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$test_groups[$f[4]][1], $d["id"]]);

                $found = true;
                break; 
            }
        }
        if ($found == false)
        { echo $d["email_perso"]." ".$d["prenom"]." ".$d["nom"]."<br />"; }
    }
}

// groupeVenuPreRentree($pdo);
// groupeVenuCheckPCOK($pdo);
// groupeVenuCheckPCKO($pdo);
// groupeMoinsDe18Ans($pdo);
// groupe20AnsOuMoins($pdo);
// groupe21A29Ans($pdo);
// groupe30A35Ans($pdo);
// groupePlusDe35Ans($pdo);
// groupeDemandeurDEmploi($pdo);
// groupeEtudiant($pdo);
// groupeParGroupe($pdo);
// groupeParProfils($pdo);
// groupeHommes($pdo);
// groupeFemmes($pdo);
// groupeAutres($pdo);

?>