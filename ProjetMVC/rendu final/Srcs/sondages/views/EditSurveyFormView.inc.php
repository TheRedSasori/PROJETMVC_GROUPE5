<?php
require_once("views/View.inc.php");

class EditSurveyFormView extends View
{
    private $survey;

    /**
     * Affiche le formulaire de modification de sondage.
     *
     * @see View::displayBody()
     */
    public function displayBody()
    {
        require("templates/editsurveyform.inc.php");
    }

    public function setSurvey($survey)
    {
        $this->survey = $survey;

        return true;
    }
}


