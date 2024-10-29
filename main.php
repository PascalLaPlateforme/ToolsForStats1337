<?php

include     "promos_by_weeks.php";
include     "groups_by_weeks.php";
include     "show.php";

$week = "2024-10-14";

$all_the_data = THIS___IS___DATAAAA();
$groups_data = DO_YOU_HAVE_SOME_MORE_OF_THEM_DATA();

$groups_infos = [];
foreach ($groups_data as $group_name =>$group)
{
    $groups_infos[$group_name] = [];
    foreach ($group as $student)
    {
        if (array_key_exists($student["email"], $all_the_data["students"]))
        {
            array_push($groups_infos[$group_name], $all_the_data["students"][$student["email"]]);
        }
    }
}

// var_dump($groups_infos);

// var_dump($groups_data);

// showPromoData($all_the_data["promos"], 0, 0);
showGroupData($groups_infos);

// var_dump($all_the_data["students"]);

// yuliiа.sherstiuk@laplateforme.io

?>