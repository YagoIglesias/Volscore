<?php
error_reporting(E_ERROR);

$action = isset($_GET['action']) ? $_GET['action'] : 'home';
require_once 'controller/controller.php';
require_once 'model/VolscoreDB.php';
require_once 'vendor/autoload.php';
require_once 'helpers/helpers.php';

switch ($action)
{
    case 'mark':
        markGame($_GET['id']);
        break;
    case 'validate':
        validateTeamForGame($_GET['team'],$_GET['game']);
        break;
    case 'setPositions':
        setPositions($_POST['setid'],$_POST['teamid'],$_POST['position1'],$_POST['position2'],$_POST['position3'],$_POST['position4'],$_POST['position5'],$_POST['position6']);
        break;
    case 'teams':
        showTeams();
        break;
    case 'games':
        showGames();
        break;
    case 'unittests':
        executeUnitTests();
        break;
    default:
        require_once 'view/home.php';
}
?>
