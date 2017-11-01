<li class="media well">
    <div class="media-body">
        <h4 class="media-heading"><?php echo $survey->getQuestion() ?></h4>
        <br>
        <?php
        echo "<button type='button' onclick='<?php delSurvey();?>'><span class='glyphicon glyphicon-trash'></span></button>"
        foreach ($survey->getResponses() as $response) {
            /* TODO START */
            /* TODO END */
        }
        function delSurvey() {
            $getSurveyToDel = 'DELETE * FROM surveys WHERE id=$survey';
            $getResponsesToDel = 'DELETE * FROM responses WHERE id_surveys= //ID DU SONDAGE A SUPPRIMER';
            $this->connection->exec $getSurveyToDel;
            $this->connection->exec $getResponsesToDel;
        }
        ?>

        <!--<div class="fluid-row">
			<div class="span2">Réponse 1</div>
			<div class="span2 progress progress-striped active">
				<div class="bar" style="width: 20%"></div>
			</div>
			<span class="span1">(20%)</span>
			<form class="span1" method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?action=Vote'; ?>">
				<input type="hidden" name="responseId" value="1"> 
				<input type="submit" style="margin-left:5px" class="span1 btn btn-small btn-danger" value="Voter">
			</form>
		</div>

		<div class="fluid-row">
			<div class="span2">Réponse 2</div>
			<div class="span3 progress progress-striped active">
				<div class="bar" style="width: 80%"></div>
			</div>
			<span class="span1">(80%)</span>
			<form class="span1" method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?action=Vote'; ?>">
				<input type="hidden" name="responseId" value="2"> 
				<input type="submit" style="margin-left:5px" class="span1 btn btn-small btn-danger" value="Voter">
			</form>
		</div>-->

    </div>
</li>



