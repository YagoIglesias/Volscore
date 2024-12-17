<?php
$title = 'Matches';

ob_start();
?>

<h1>Matchs</h1>
<table class="table table-bordered">
    <thead>
        <tr><th>Date et heure</th><th>Numéro</th><th>Recevante</th><th>Visiteur</th><th>Score</th><th>Action</th></tr>
    </thead>
    <tbody>
    <?php
    
    
    foreach ($games as $game)
    {
        // formatage de la date 
        $date = new DateTime($game->moment);
        //affichage de la date avec le bon format
        echo "<tr><td>".$date->format('d M Y \à H:i')."</td>
        <td>".$game->number."</td><td>".$game->receivingTeamName."</td><td>".$game->visitingTeamName."</td><td>".(($game->scoreReceiving+$game->scoreVisiting) > 0 ? $game->scoreReceiving."-".$game->scoreVisiting : "")."</td><td>";
        if ($game->isMarkable()) {
            echo "<a href='?action=mark&id=".$game->number."' class='btn btn-sm btn-primary m-1'>Marquer</a>";
        }
        if ($game->isEditable()) {
            echo "<a href='?action=edit&id=".$game->number."' class='btn btn-sm btn-primary m-1'>Modifier</a>";
        }
        if (VolscoreDB::gameIsOver($game)) {
            echo "<a href='?action=sheet&gameid=".$game->number."' class='btn btn-sm btn-primary m-1'>Consulter</a>";
        } elseif (count(VolscoreDB::getSets($game)) > 0) {
            echo "<a href='?action=resumeScoring&gameid=".$game->number."' class='btn btn-sm btn-primary m-1'>Continuer</a>";
        }
        echo "</td></tr>";
    }
    ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
require_once 'gabarit.php';
?>

