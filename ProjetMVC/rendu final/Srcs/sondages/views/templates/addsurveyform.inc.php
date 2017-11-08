<?php

// on genere les reponse dans notre survey
function generateInputForResponse($n)
{
    ?>
    <div class="control-group">
        <label class="control-label" for="responseSurvey<?php echo $n; ?>">Réponse <?php echo $n; ?></label>
        <div class="controls">
            <input class="span3" type="text" name="responseSurvey<?php echo $n; ?>" placeholder="Réponse <?php echo $n; ?>">
        </div>
    </div>
    <?php
}

?>

<!-- on cree notre form pour modifier un sondage qui va contenir tout les information de notre sondage , question et ses reponses-->
<form method="post" action="index.php?action=AddSurvey" class="modal">
    <div class="modal-header">
        <h3>Création d'un sondage</h3>
    </div>
    <div class="form-horizontal modal-body">
        <?php if ($this->message !== "") {
            echo '<div class="alert ' . $this->style . '">' . $this->message . '</div>';
        }
        ?>
        <div class="control-group">
            <label class="control-label" for="questionSurvey">Question</label>
            <div class="controls">
                <input class="span3" type="text" name="questionSurvey" placeholder="Question">
            </div>
        </div>
        <br>
        <!-- on recupere les question -->
        <?php
        for ($i = 1; $i <= 5; $i++) {
            generateInputForResponse($i);
        }
        ?>
    </div>
    <div class="modal-footer">
        <input class="btn btn-danger" type="submit" value="Poster le sondage"/>
    </div>
</form>



