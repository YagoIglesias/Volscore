<?php
$title = 'Préparation du match '.$game->number;
ob_start();
?>

<div class="d-flex flex-column align-items-center m-5">
    <h1>Préparation du match <?= $game->number ?></h1>
    <h3>Vérification des présences, photos, licenses et numéros de maillot</h3>
    <table>
        <tr><th><?= $game->receivingTeamName ?></th><th><?= $game->visitingTeamName ?></th></tr>
        <tr>
            <td>
                <table>
                    <th>Photo</th>
                    <th>Nom</th>
                    <th>License</th>
                    <th>N°maillot</th>
                    <?php foreach ($receivingRoster as $player) : ?>
                        
                        <!-- Ajouter les photos -->
                        <tr>
                            <td><a href="javascript:void(0);" class="open-popup" data-image="../images/<?=$player->photo?>">
                                <img class="clickable-image" src="../images/<?=$player->photo?>" alt="img player"></a>
                            </td>
                            <td><?= $player->last_name ?></td>
                            <td><?= $player->license ?></td>
                            <td><?= $player->number ?></td>
                        </tr>
                        
                    <?php endforeach; ?>
                </table>

                <!-- Pop-up pour afficher l'image agrandie -->
                <div id="popup" class="popup">
                    <span class="close-btn" id="close-btn">&times;</span>
                    <img id="popup-image" class="popup-image" src="" alt="Image agrandie">
                </div>

                <?php if (rosterIsValid($receivingRoster)) : ?>
                    <div><span class="checkmark"></span>Présences validées</div>
                <?php else : ?>
                    <form method="post" action="?action=validate&game=<?= $game->number ?>&team=<?= $game->receivingTeamId ?>">
                        <input type="submit" class="btn btn-primary btn-sm" value="Valider">
                    </form>
                <?php endif; ?>
            </td>
            <td>
                <table>
                    <th>Photo</th>
                    <th>Nom</th>
                    <th>License</th>
                    <th>N°maillot</th>
                    <?php foreach ($visitingRoster as $player) : ?>
                        <tr>
                            <td><a href="javascript:void(0);" class="open-popup" data-image="../images/<?=$player->photo?>">
                                <img class="clickable-image" src="../images/<?=$player->photo?>" alt="img player"></a>
                            </td>

                            <td><?= $player->last_name ?></td>
                            <td><?= $player->license ?></td>
                            <td><?= $player->number ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <?php if (rosterIsValid($visitingRoster)) : ?>
                    <div><span class="checkmark"></span>Présences validées</div>
                <?php else : ?>
                    <form method="post" action="?action=validate&game=<?= $game->number ?>&team=<?= $game->visitingTeamId ?>">
                        <input type="submit" class="btn btn-primary btn-sm" value="Valider">
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    </table>
</div>
<div class="d-flex flex-column align-items-center m-5">
    <h3>Tirage au sort gagné par</h3>
    <form method="post" action="?action=registerToss">
        <input type="hidden" name="gameid" value=<?= $game->number ?> />
        <button type="submit" class="btn btn-success btn-sm m-3" name="cmdTossWinner" value="1"><?= $game->receivingTeamName ?></button>
        <button type="submit" class="btn btn-success btn-sm m-3" name="cmdTossWinner" value="2"><?= $game->visitingTeamName ?></button>
    </form>
</div>
<!-- Script JS à la fin de la page -->
<script>
    // Récupérer tous les liens de pop-up
    const openPopupLinks = document.querySelectorAll('.open-popup');
    const popup = document.getElementById('popup');
    const popupImage = document.getElementById('popup-image');
    const closeBtn = document.getElementById('close-btn');

    // Ouvrir le pop-up quand on clique sur une image
    openPopupLinks.forEach(link => {
        link.addEventListener('click', function() {
            const imageUrl = this.getAttribute('data-image'); // Récupère l'URL de l'image
            popupImage.src = imageUrl; // Met l'URL de l'image dans le pop-up
            popup.style.display = 'flex'; // Affiche le pop-up
        });
    });

    // Fermer le pop-up quand on clique sur le bouton de fermeture
    closeBtn.addEventListener('click', function() {
        popup.style.display = 'none'; // Cache le pop-up
    });

    // Fermer le pop-up quand on clique sur le fond sombre
    popup.addEventListener('click', function(e) {
        if (e.target === popup) {
            popup.style.display = 'none'; // Cache le pop-up
        }
    });
</script>
<?php
$content = ob_get_clean();
require_once 'gabarit.php';
?>

