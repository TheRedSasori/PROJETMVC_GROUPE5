<?php
$survey = $this->survey;

$question = $survey['question'];
$id       = $survey['id'];
$replies  = $survey['replies'];

$count    = count($replies);
$newCount = $count - 1;


function generateInputForResponse($n, $replies)
{
    ?>
    <div class="control-group">
        <label class="control-label" for="responseSurvey<?php echo $n; ?>">Réponse <?php echo $n; ?></label>
        <div class="controls">
            <input class="span3" type="text" name="responseSurvey<?php echo $n; ?>" placeholder="<?php
            echo $replies[$n];
            ?>" value="<?php
            echo $replies[$n];
            ?>">
        </div>
    </div>
    <?php
}

?>
<!-- creation du form pour la midification du sondage  -->
<form method="post" action="index.php?action=EditSurvey&id=<?php echo $id; ?>" class="modal">
    <div class="modal-header">
        <h3>Modification d'un sondage</h3>
    </div>
    <div class="form-horizontal modal-body">
        <?php if ($this->message !== "") {
            echo '<div class="alert ' . $this->style . '">' . $this->message . '</div>';
        }
        ?>
        <div class="control-group">
            <label class="control-label" for="questionSurvey">Question</label>
            <div class="controls">
                <input disabled class="span3" type="text" name="questionSurvey" placeholder="Question" value="<?php echo $question; ?>">
            </div>
        </div>
        <br>
        <?php
        for ($i = 0; $i <= $newCount; $i++) {

            generateInputForResponse($i, $replies);

        }
        ?>
    </div>
    <div class="modal-footer">
        <input class="btn btn-danger" type="button" value="Retour" onclick="history.go(-1)"> <input class="btn btn-success" type="submit" value="Modifier le sondage"/>
    </div>
</form>

