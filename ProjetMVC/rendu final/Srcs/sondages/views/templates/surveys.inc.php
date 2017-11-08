<div class="container">
    <br> <br> <br>
    <div class="span7 offset2">
        <ul class="media-list">
            <?php
            $countDisplay = 0;
            foreach ($this->surveys as $survey) {

                $sondage = new Survey($survey['owner'], $survey['question']);
                $sondage->setId($survey['id_surveys']);
                $sondage->setResponses($survey['replies']);
                $countDisplay = $countDisplay + 1;

                require("survey.inc.php");
            }
            ?>
        </ul>
    </div>
</div>
