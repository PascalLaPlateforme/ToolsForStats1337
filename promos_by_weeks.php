<?php
// Faire une detection d'abandon precoce
// Fide par semaine
// Prendre en compte les certifs dans le ratage d'une activite
// Ne pas compter les temps de log si ils sont deja compte dans une justif

function THIS___IS___DATAAAA()
{
     ini_set('xdebug.var_display_max_depth', 99);

     $host = '127.0.0.1';
     $db = 'api_live';
     $user = 'root';
     $pass = '';

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

     // Recuperation des promos actives
     $query = "SELECT p.id, p.name FROM promotion AS p WHERE p.is_active = 1";
     $stmt = $pdo->query($query);
     $data = $stmt->fetchAll();

     $promos = [];
     foreach ($data as $d)
     {
          array_push($promos, ["name" => $d["name"], "id" => $d["id"]]);
     }

     // -----------------------------------------------------------------------//
     // -----------------------------------------------------------------------//
     // -----------------------------------------------------------------------//

     $HOURS_PER_DAY = 6;

     // Calcul des semaines depuis le 26 Aout
     $weeks = [];
     $today = new DateTime(date('Y-m-d'));
     $date = new DateTime("2024-08-26");
     while ($today > $date)
     {
          $monday = $date->format('Y-m-d'); 
          $date->modify('next sunday');
          $sunday = $date->format('Y-m-d');
          $date->modify('next monday');
          array_push($weeks, [$monday, $sunday]);
     }

     // Pour stocker les etudiants qui n'ont pas l'historique de leur promo ... Bleh ...
     // $missing_history = [];

     $all_the_data = [];
     $all_the_data["promos"] = [];
     $all_the_data["students"] = [];
     $promotions_databases = &$all_the_data["promos"];
     $students_databases = &$all_the_data["students"];

     // Pour chaque promo ...
     foreach ($promos as $promo)
     {
          if ($promo["id"] == 49) continue;
          // if ($promo["id"] != 97) continue;

          // **** Juste pour verifier qu'il ne manque pas de history promo en faiiiiiiiit **** //
          // Recupere les etudiants d'une promo par les historique
          // $query = "SELECT s.email, a.promotion_fk as promo, ph.promotion_fk, p.name, ph.date FROM promotion_history as ph
          // inner join applicant as a on a.id = ph.applicant_fk
          // inner join student as s on s.applicant_fk = a.id
          // inner join promotion as p on p.id = ph.promotion_fk
          // WHERE a.promotion_fk = ".$promo["id"]."
          // order by ph.date desc";
          // $stmt = $pdo->query($query);
          // $data_students = $stmt->fetchAll();

          // // Recupere les etudiants d'une promo tout court
          // $query = "SELECT s.email, a.promotion_fk, p.name FROM applicant as a
          // inner join student as s on s.applicant_fk = a.id
          // inner join promotion as p on p.id = a.promotion_fk
          // where a.promotion_fk = ".$promo["id"];
          // $stmt = $pdo->query($query);
          // $data_students_too = $stmt->fetchAll();

          // $promo_students = [];
          // foreach ($data_students as $student)
          // {
          //      if (!array_key_exists($student["email"], $promo_students))
          //      {
          //           $promo_students[$student["email"]] = $student;
          //      }
          // }
          
          // // Check si tous les etudiants d'une promo ont bien la promo dans l'historique
          // foreach ($data_students_too as $student_too)
          // {
          //      if (!array_key_exists($student_too["email"], $promo_students))
          //      {
          //           array_push($missing_history, $student_too);
          //      }
          // }

          // *****************************************************************************************************//

          $promotions_databases[$promo["id"]] = [];
          $promotions_databases[$promo["id"]]["id"] = $promo["id"];
          $promotions_databases[$promo["id"]]["name"] = $promo["name"];
          $promotions_databases[$promo["id"]]["weeks"] = [];
          $promotions_databases[$promo["id"]]["all_students"] = [];
          $all_time_students = &$promotions_databases[$promo["id"]]["all_students"];

          // On fait un check sur chaque semaine
          foreach ($weeks as $week)
          {
               // Recupere les etudiants d'une promo par les historiques et jusqu'a une semaine donnee ...
               $query = "SELECT s.id as student, s.email, a.promotion_fk as promo, ph.promotion_fk, p.name as promo_name
               FROM promotion_history as ph inner join applicant as a on a.id = ph.applicant_fk
               inner join student as s on s.applicant_fk = a.id
               inner join promotion as p on p.id = ph.promotion_fk
               WHERE s.id in (
                    SELECT s.id
                    FROM promotion_history as ph inner join applicant as a on a.id = ph.applicant_fk
                    inner join student as s on s.applicant_fk = a.id
                    inner join promotion as p on p.id = ph.promotion_fk
                    WHERE ph.promotion_fk = ".$promo["id"]."
                    order by ph.date desc
               )
               and ph.date < '".$week[1]."' order by ph.date desc";
               $stmt = $pdo->query($query);
               $data_students = $stmt->fetchAll();

               $seen = []; // Deja traite cette semaine
               $students_that_should_come = []; // Tableau qui contient les etudiants dans cette promo cette semaine
               $ids = "(";
               foreach ($data_students as $student)
               {
                    if (!array_key_exists($student["email"], $seen))
                    {
                         $seen[$student["email"]] = $student;
                         if ($student['promotion_fk'] == $promo["id"])
                         {
                              $students_that_should_come[$student["email"]] = [];
                              $students_that_should_come[$student["email"]]["student"] = $student;
                              $students_that_should_come[$student["email"]]["email"] = $student["email"];
                              if (count($students_that_should_come) != 1) { $ids .= ","; }
                              $ids .= $student["student"];

                              if (!array_key_exists($student["email"], $all_time_students))
                              {
                                   $all_time_students[$student["email"]] = [];
                                   $all_time_students[$student["email"]]["student_id"] = $student["student"];
                                   $all_time_students[$student["email"]]["email"] = $student["email"];
                                   $all_time_students[$student["email"]]["missed_weeks"] = 0;
                                   $all_time_students[$student["email"]]["missed_activities"] = 0;
                                   $all_time_students[$student["email"]]["expected_activities"] = 0;
                                   $all_time_students[$student["email"]]["first_week"] = true; // Bof ici

                                   if (!array_key_exists($student["email"], $students_databases))
                                   {
                                        $students_databases[$student["email"]] = [];
                                        $students_databases[$student["email"]]["student_id"] = $student["student"];
                                        $students_databases[$student["email"]]["email"] = $student["email"];
                                        $students_databases[$student["email"]]["ever_came"] = false;
                                        $students_databases[$student["email"]]["weeks"] = [];
                                   }
                              }
                              $all_time_students[$student["email"]]["student"] = $student;
                         }
                         else if (array_key_exists($student["email"], $all_time_students))
                         {
                              $all_time_students[$student["email"]]["student"] = $student;
                         }
                    }
               }
               $ids .= ")";

               if (count($students_that_should_come) > 0)
               {
                    // Recupere les logs des etudiants qui sont dans la promo cette semaine
                    $query = "SELECT s.id as student, p.name, s.email, l.algo1, l.algo2, l.algo3 FROM logtime as l
                    inner join student as s on s.id = l.student_fk
                    inner join applicant as a on a.id = s.applicant_fk
                    inner join promotion as p on a.promotion_fk = p.id
                    where s.id in $ids 
                    and l.day >= '".$week[0]."'
                    and l.day <= '".$week[1]."';";
                    $stmt = $pdo->query($query);
                    $data_logs = $stmt->fetchAll();

                    // Recupere les heures bonus de cette promo
                    $query = "SELECT s.email, le.logtime_date, le.duration FROM logtime_event as le
                    inner join student as s on s.id = le.student_fk
                    inner join applicant as a on a.id = s.applicant_fk
                    where s.id in $ids
                    and le.logtime_date >= '".$week[0]."'
                    and le.logtime_date <= '".$week[1]."';";
                    $stmt = $pdo->query($query);
                    $data_bonus = $stmt->fetchAll();

                    // Recupere les absences justifiees
                    $query = "SELECT s.email, a.start_date, a.end_date, a.status FROM absence as a
                    inner join student as s on s.id = a.student_fk
                    inner join applicant as ap on ap.id = s.applicant_fk
                    where a.status = 1
                    and s.id in $ids
                    and a.start_date >= '".$week[0]."';";
                    $stmt = $pdo->query($query);
                    $data_certifs = $stmt->fetchAll(); // Enlever promo et mettre id..

                    // Recupere les activites
                    $query = "SELECT ac.id, s.email, aa.is_present, ac.is_mandatory, a.promotion_fk, ac.unit_fk, ac.date from activity_attendance as aa
                    inner join activity as ac on ac.id = aa.activity_fk
                    inner join student as s on s.id = aa.student_fk
                    inner join applicant as a on a.id = s.applicant_fk
                    where s.id in $ids
                    and ac.date >= '".$week[0]."'
                    and ac.date <= '".$week[1]."'
                    and ac.is_mandatory = 1;";
                    $stmt = $pdo->query($query);
                    $data_activities = $stmt->fetchAll();
               }

               // Recupere les jours de presence a l'ecole de cette promo
               $query = "SELECT cd.calendar_fk as calendar_id, cd.day, cd.type, c.name, c.promotion_fk FROM calendar_day as cd
               inner join calendar as c on c.id = cd.calendar_fk
               where c.promotion_fk = ".$promo["id"]."
               and type = 1
               and day >= '".$week[0]."'
               and day <= '".$week[1]."'
               ORDER by cd.day desc;";
               $stmt = $pdo->query($query);
               $data_days = $stmt->fetchAll();
               $hours_to_do = count($data_days) * $HOURS_PER_DAY;

               if ($hours_to_do > 0)
               {
                    $promotions_databases[$promo["id"]]["weeks"][$week[0]] = [];

                    $promotions_databases[$promo["id"]]["weeks"][$week[0]]["hours_to_do"] = $hours_to_do;
                    $promotions_databases[$promo["id"]]["weeks"][$week[0]]["subbed"] = count($students_that_should_come);
                    $promotions_databases[$promo["id"]]["weeks"][$week[0]]["ever_subbed"] = count($all_time_students);
                    $promotions_databases[$promo["id"]]["weeks"][$week[0]]["logged_in"] = [];
                    $promotions_databases[$promo["id"]]["weeks"][$week[0]]["did_not_log"] = [];
                    
                    $students_that_have_logged = [];
                    foreach ($data_logs as $log)
                    {
                         if (!array_key_exists($log["email"], $students_that_have_logged))
                         {
                              $students_that_have_logged[$log["email"]] = [];
                              $students_that_have_logged[$log["email"]]["student_id"] = $log["student"];
                              $students_that_have_logged[$log["email"]]["email"] = $log["email"];
                              $students_that_have_logged[$log["email"]]["algo1"] = 0;
                              $students_that_have_logged[$log["email"]]["algo2"] = 0;
                              $students_that_have_logged[$log["email"]]["algo3"] = 0;
                         }
                         $students_that_have_logged[$log["email"]]["algo1"] += $log["algo1"];
                         $students_that_have_logged[$log["email"]]["algo2"] += $log["algo2"];
                         $students_that_have_logged[$log["email"]]["algo3"] += $log["algo3"];
                    }

                    uasort($students_that_have_logged, function ($a, $b)
                    {
                         return ($a["algo2"] < $b["algo2"] ? 1 : -1);
                    });

                    $certifs_list = [];
                    foreach ($data_certifs as $certif)
                    {
                         if (!array_key_exists($certif["email"], $certifs_list))
                         {
                              $certifs_list[$certif["email"]] = [];
                              $certifs_list[$certif["email"]]["email"] = $certif["email"];
                              $certifs_list[$certif["email"]]["days_off"] = [];
                         }
                         $period = new DatePeriod(
                              new DateTime($certif["start_date"]),
                              new DateInterval('P1D'),
                              new DateTime($certif["end_date"])
                         );
                         foreach ($period as $key => $value) {
                              array_push($certifs_list[$certif["email"]]["days_off"], $value->format('Y-m-d'));
                         }
                    }

                    foreach ($students_that_have_logged as $k => $student_that_has_logged)
                    {
                         // Calcul du bonus d'heure et jour d'absence sur cette semaine et sur les jours travailles
                         $bonus_hours = 0;
                         $justified_days = 0;
                         foreach ($data_days as $day)
                         {
                              foreach ($data_bonus as $bonus)
                              {
                                   if ($bonus["email"] == $student_that_has_logged["email"])
                                   {
                                        if ($day["day"] == $bonus["logtime_date"])
                                        {
                                             $bonus_hours += $bonus["duration"];
                                        }
                                   }
                              }
                              if (array_key_exists($student_that_has_logged["email"], $certifs_list))
                              {
                                   foreach ($certifs_list[$student_that_has_logged["email"]]["days_off"] as $day_off)
                                   {
                                        if ($day["day"] == $day_off)
                                        {
                                             $justified_days += 1;
                                        }
                                   }
                              }
                         }

                         // Activiteeees
                         $absences = 0;
                         $total_activities = 0;
                         foreach ($data_activities as $activity)
                         {
                              if ($activity["email"] == $student_that_has_logged["email"])
                              {
                                   ++$total_activities;
                                   ++$all_time_students[$student_that_has_logged["email"]]["expected_activities"];
                                   if ($activity["is_present"] == 0)
                                   {
                                        ++$absences;
                                        ++$all_time_students[$student_that_has_logged["email"]]["missed_activities"];
                                   }
                              }
                         }
 
                         $std = &$promotions_databases[$promo["id"]]["weeks"][$week[0]]["logged_in"][$k];
                         $std["algo1"] = round(($student_that_has_logged["algo1"] / 60), 1);
                         $std["algo2"] = round(($student_that_has_logged["algo2"] / 60), 1);
                         $std["algo3"] = round(($student_that_has_logged["algo3"] / 60), 1);
                         $std["bonus_hours"] = round(($bonus_hours / 60), 1);
                         $std["justified_days"] = $justified_days * 7;
                         $std["global_time"] = round(($student_that_has_logged["algo2"] / 60) + ($bonus_hours / 60) + ($justified_days * 7), 1);
                         $std["missed_activities"] = $absences;
                         $std["total_missed_activities"] = $all_time_students[$student_that_has_logged["email"]]["missed_activities"];
                         $std["first_week"] = $all_time_students[$student_that_has_logged["email"]]["first_week"];
                         $std["missed_weeks"] = $all_time_students[$student_that_has_logged["email"]]["missed_weeks"];

                         $students_databases[$student_that_has_logged["email"]]["ever_came"] = true;
                         $students_databases[$student_that_has_logged["email"]]["weeks"][$week[0]] = [];
                         $students_databases[$student_that_has_logged["email"]]["weeks"][$week[0]]["came"] = true;
                         $students_databases[$student_that_has_logged["email"]]["weeks"][$week[0]]["data"] = $std;

                         $all_time_students[$student_that_has_logged["email"]]["first_week"] = false;
                    }

                    foreach ($all_time_students as $key => &$all_time_student)
                    {
                         if (!array_key_exists($key, $students_that_have_logged))
                         {
                              $all_time_student["missed_weeks"] += 1;

                              $promotions_databases[$promo["id"]]["weeks"][$week[0]]["did_not_log"][$key] = [];
                              $not_logged = &$promotions_databases[$promo["id"]]["weeks"][$week[0]]["did_not_log"][$key];
                              $not_logged["email"] = $key;
                              $not_logged["went_to"] = $all_time_student["student"]["promo_name"];
                              $not_logged["went_to_id"] = $all_time_student["student"]["promotion_fk"];
                              $not_logged["missed_weeks"] = $all_time_student["missed_weeks"];

                              $students_databases[$all_time_student["email"]]["weeks"][$week[0]] = [];
                              $students_databases[$all_time_student["email"]]["weeks"][$week[0]]["came"] = false;
                              $students_databases[$all_time_student["email"]]["weeks"][$week[0]]["promo_id"] = $not_logged["went_to_id"];
                              $students_databases[$all_time_student["email"]]["weeks"][$week[0]]["promo"] = $not_logged["went_to"];
                         }
                    }    
               }
          }
     }

     return ($all_the_data);
}

?>