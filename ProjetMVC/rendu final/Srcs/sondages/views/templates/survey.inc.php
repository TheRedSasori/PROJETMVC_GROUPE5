<script type="text/javascript">
	function diplayComments(id) {

		if (document.getElementById(id).style.display == "none") {
			document.getElementById(id).style.display = "block";
		}
		else {
			document.getElementById(id).style.display = "none";
		}
	}
</script>


<?php


$comments   = $this->comments;
$percentage = $sondage->Computepercentages();
$i          = 0;
$id_survey  = $sondage->getId();
$owner      = $sondage->getOwner();
$question   = $sondage->getQuestion();
?>


<li class="media well">
    <div class="media-body">
        <h4 class="media-heading" style="color:deepskyblue; " "><?php
        /* on recupere la question du survey selectionné */
        echo $sondage->getQuestion() ?>

        <?php
        if (isset($_SESSION['login'])) {
            $login = $_SESSION['login'];
            if ($owner === $login) {
                echo "<a href=\"";
                echo $_SERVER['PHP_SELF'] . '?action=Delete&id=' . $id_survey;
                echo "\">
              <img src=\"icon/delete.png\" alt=\"DeleteButtom\" title=\"Supprimer\" width=\"18\" height=\"23\" align=\"right\" hspace=\"10\" ></a> 
                ";
                echo "<a href=\"";
                echo $_SERVER['PHP_SELF'] . '?action=EditSurvey&id=' . $id_survey;
                echo "\">
              <img src=\"icon/edit.png\" alt=\"EditButtom\" title=\"Modifier\" width=\"18\" height=\"23\" align=\"right\" hspace=\"10\"></a> 
                ";
            }
        }
        ?>
        </h4><br>
        <?php
        /* on recupere les reponses du survey selectionner */
        foreach ($sondage->getResponses() as $response) {

            $title = $response['title'];
            $id    = $response['id'];

            echo "<div class=\"fluid-row\">
            <div class=\"span2\">" . $title . "</div>
            <div class=\"span2 progress progress-striped active\">
                <div class=\"bar\" style=\"width:" . $percentage[$i] . "%\"></div>
            </div> 
             <span class=\"span1\">(" . $percentage[$i] . "%)</span>
            <form class=\"span1\" method=\"post\" action=\"        
          ";
            echo $_SERVER['PHP_SELF'] . '?action=Vote';
            echo " \">
                        <input type=\"hidden\" name=\"responseId\" value=\"" . $id . "\"> 
                        <input type=\"submit\" style=\"margin-left:5px\" class=\"span1 btn btn-small btn-danger\" value=\"Voter\">
                   </form>
              </div>
                        ";
            $i = $i + 1;
        }
        ?>
        <!-- affichage du menu deroulant quand on click ils affiche nos commentaire  du survey conserner -->
        <span id="content" onclick="diplayComments(<?php echo $countDisplay ?>)" style="color: deepskyblue">Afficher les commentaires</span>
        <div id="<?php echo $countDisplay ?>" style="display:none">
            </br>
            <?php

            if (!empty($comments[$id_survey]['content'])) {

                if (isset($id_survey, $comments)) {

                    foreach ($comments[$id_survey]['content'] as $comment) {
                        echo "<div style='border-left: solid 10px deepskyblue; background-color: white;'>";
                        echo "<h6 style='margin-left: 5%'>" . $comment['content'] . "</h6>";
                        echo "<p style='margin-left: 1%'>-- " . $comments[$id_survey]['owner'] . "</p>";
                        echo "</div>";
                        echo "</br>";

                    }
                }
            }

            ?>


            <br>
            <!-- ajout d'un champ pour ajouter un commentaire pour le survey concerner  -->
            <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?action=Comment&id=' . $id_survey . '&question=' . $question; ?>">
                <h4 class="media-heading"> Ajouter un commentaire</h4><TEXTAREA required name="comment" style="width: 85%; "></TEXTAREA> <input class="btn" type="submit" value="Commenter"/>
            </form>

        </div>
    </div>
</li>

