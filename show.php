<?php

// Le Glow-up ...
echo "
<style>
     table {
          border: 3px solid #dededf;
     }
     td {
          padding-left: 10px;
          padding-right: 10px;
          padding-top: 2px;
          padding-bottom: 2px;
     }
</style>
";

function    showPromoData(&$promos, $pid, $pweek)
{
    foreach ($promos as $id => $promo)
    {
        if ($pid == $id or $pid == 0)
        {
            echo "<div style=\"display: flex; align-items: center; justify-content: center; flex-direction: column\">";
            echo "<div><b>".$promo["name"]."</b> (".$promo["id"].")</div>";

            foreach ($promo["weeks"] as $monday => $week)
            {
                if ($pweek == $monday or $pweek == 0)
                {
                    echo "<div><u>Semaine du : ".$monday."</u> (".$week["hours_to_do"]." heure(s) a faire)</div>";
                    echo "<div>".count($week["logged_in"])." / ".$week["subbed"]." ( ".$week["ever_subbed"]." )</div>";

                    echo "<table rules=all>";
                    echo "<thead><tr>";
                    echo "<td>Etudiant</td><td>Log</td><td>Bonus</td><td>Abs justif</td><td>Presence globale</td>";
                    echo "<td>Absences</td><td>Absences totales</td>";
                    echo "</tr></thead>";
                    foreach ($week["logged_in"] as $mail => $student)
                    {
                        echo "<tr>";
                        echo "<td>";
                        if ($student["first_week"] == true) { echo "<span style=\"background-color:#00FF00;\">"; }
                        echo $mail;
                        if ($student["first_week"] == true) { echo "</span>"; }
                        echo "</td>";
                        echo "<td>".$student["algo2"]."</td>";
                        echo "<td>";
                        if ($student["bonus_hours"] != 0) { echo "<span style=\"background-color:#00FF00;\">"; }
                        echo $student["bonus_hours"];
                        if ($student["bonus_hours"] != 0) { echo "</span>"; }
                        echo "</td>";
                        echo "<td>";
                        if ($student["justified_days"] != 0) { echo "<span style=\"background-color:#00FF00;\">"; }
                        echo $student["justified_days"];
                        if ($student["justified_days"] != 0) { echo "</span>"; }
                        echo "</td>";
                        echo "<td>";
                        echo $student["global_time"];
                        echo "</td>";
                        echo "<td>";
                        echo $student["missed_activities"];
                        echo "</td>";
                        echo "<td>";
                        echo $student["total_missed_activities"];
                        echo "</td>";
                        echo "</tr>";
                    }
                    foreach ($week["did_not_log"] as $mail => $student)
                    {
                        echo "<tr>";
                        echo "<td><span style=\"background-color:#FF0000;\">";
                        echo $mail;
                        echo "</span></td>";
                        echo "<td>Semaines d'absences</td>";
                        echo "<td>";
                        echo $student["missed_weeks"];
                        echo "</td>";
                        
                        if ($student["went_to_id"] != $id)
                        {
                            echo "<td>--> ";
                            echo $student["went_to"];
                            echo "</td>";
                        }

                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "<div>------------------------------------------------------------------------</div>";
                }
            }
            echo "<div>######################################################################</div>";
            echo "</div>";
        }
    }
}

// 'student_id' => int 1452
//           'email' => string 'eltigani.abdallah@laplateforme.io' (length=33)
//           'ever_came' => boolean true
//           'weeks' => 
//             array (size=1)
//               '2024-10-14' => 
//                 array (size=2)
//                   'came' => boolean true
//                   'data' => 
//                     array (size=10)
//                       'algo1' => float 23.6
//                       'algo2' => float 23.9
//                       'algo3' => float 27
//                       'bonus_hours' => float 0
//                       'justified_days' => int 7
//                       'global_time' => float 30.9
//                       'missed_activities' => int 1
//                       'total_missed_activities' => int 1
//                       'first_week' => boolean true
//                       'missed_weeks' => int 0

function    showGroupData(&$groups)
{
    foreach ($groups as $key => $group)
    {
        echo "<div style=\"display: flex; align-items: center; justify-content: center; flex-direction: column\">";

        $never_came = 0;
        foreach ($group as $s)
        {
            if ($s["ever_came"] == false)
            {
                ++$never_came;
            }
        }

        echo "<div><b>".$key."</b> ( ".count($group) - $never_came." / ".count($group)." ( ".$never_came." ) )</div>";
        
        echo "<table>";
        foreach ($group as $s)
        {
            if ($s["ever_came"] == true)
            {
                echo "<tr>";
                echo "<td>";
                echo $s["email"];
                echo "</td>";
                foreach ($s["weeks"] as $week)
                {
                    if ($week["came"] == true)
                    {
                        echo "<td style=\"background-color:#00FF00;\">".$week["data"]["global_time"]."</td>";
                    }
                    else
                    {
                        echo "<td style=\"background-color:#FF0000;\">".$week["promo"]."</td>";
                    }
                }
                echo "</tr>";
            }
        }
        foreach ($group as $s)
        {
            if ($s["ever_came"] == false)
            {
                echo "<tr>";
                echo "<td style=\"background-color:#FF0000;\">";
                echo $s["email"];
                echo "</td>";
                foreach ($s["weeks"] as $week)
                {
                    if ($week["came"] == true)
                    {
                        echo "<td style=\"background-color:#00FF00;\">".$week["data"]["global_time"]."</td>";
                    }
                    else
                    {
                        echo "<td style=\"background-color:#FF0000;\">".$week["promo"]."</td>";
                    }
                }
                echo "</tr>";
            }
        }
        echo "</table>";
    }
}

?>