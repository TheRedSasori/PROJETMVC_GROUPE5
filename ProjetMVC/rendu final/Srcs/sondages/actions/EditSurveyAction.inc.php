<?php

require_once("model/Survey.inc.php");
require_once("model/Response.inc.php");
require_once("actions/Action.inc.php");

class EditSurveyAction extends Action
{

    /**
     * @see Action::run()
     */
    public function run()
    {//verifier si l'utilisateur est connectée
        if ($this->getSessionLogin() === null) {
            $this->setMessageView("Vous devez être authentifié.");

            return;
        }
        $this->setEditSurveyFormView("La modification du sondage réinitialise les votes.");

        //rucuperation du sondage par l'ID
        $id_surveys  = $_REQUEST['id'];
        $id_surveys2 = (filter_var($id_surveys, FILTER_SANITIZE_NUMBER_INT) === false)
            ? false
            : filter_var($id_surveys, FILTER_SANITIZE_NUMBER_INT);

        $surveyId = $this->database->loadSurveyById((int)$id_surveys2);

        //on trie les sondages
        $triSurvey = [];
        $replies   = [];
        foreach ($surveyId as $key => $survey) {

            $question = $survey['question'];
            $title    = $survey['title'];

            $replies[] = $title;

            $triSurvey = [

                'question' => $question,
                'id'       => $id_surveys2,
                'replies'  => $replies
            ];

        }
        // si la vue est deja charger on la recharge pas
        if ($this->getView()->setSurvey($triSurvey) === true) {

        } else {
            $this->getView()->setSurvey($triSurvey);
        }

        //on recuperes toute les reponses modifier avec $_POST
        if (!empty($_POST)) {

            if (isset($_POST['responseSurvey0'])) {

                $rep1  = $_POST['responseSurvey0'];
                $rep12 = (filter_var($rep1, FILTER_SANITIZE_STRING) === false)
                    ? false
                    : filter_var($rep1, FILTER_SANITIZE_STRING);

                $repliesImput['responseSurvey0'] = $rep12;
            }

            if (isset($_POST['responseSurvey1'])) {

                $rep2  = $_POST['responseSurvey1'];
                $rep22 = (filter_var($rep2, FILTER_SANITIZE_STRING) === false)
                    ? false
                    : filter_var($rep2, FILTER_SANITIZE_STRING);

                $repliesImput['responseSurvey1'] = $rep22;
            }

            if (isset($_POST['responseSurvey2'])) {

                $rep3  = $_POST['responseSurvey2'];
                $rep32 = (filter_var($rep3, FILTER_SANITIZE_STRING) === false)
                    ? false
                    : filter_var($rep3, FILTER_SANITIZE_STRING);

                $repliesImput['responseSurvey2'] = $rep32;
            }

            if (isset($_POST['responseSurvey3'])) {

                $rep4  = $_POST['responseSurvey3'];
                $rep42 = (filter_var($rep4, FILTER_SANITIZE_STRING) === false)
                    ? false
                    : filter_var($rep4, FILTER_SANITIZE_STRING);

                $repliesImput['responseSurvey3'] = $rep42;
            }

            if (isset($_POST['responseSurvey4'])) {

                $rep5  = $_POST['responseSurvey4'];
                $rep52 = (filter_var($rep5, FILTER_SANITIZE_STRING) === false)
                    ? false
                    : filter_var($rep5, FILTER_SANITIZE_STRING);

                $repliesImput['responseSurvey4'] = $rep52;
            }
            $filterReplies = array_filter($repliesImput);
            //charge les ancienne reponses par rapport  a l'ID
            $id_reponse = $this->database->loadIdResponse($id_surveys2, $replies);
            //modification
            if (count($filterReplies) < 1) {
                $this->setEditSurveyFormView("Il faut saisir au moins 2 réponses.");
            } else {
                $this->database->EditSurvey($id_reponse, $filterReplies);
                $this->setMessageView("Merci, nous avons modifié votre sondage.");
            }
        }
    }


    private function setEditSurveyFormView($message)
    {
        $this->setView(getViewByName("EditSurveyForm"));
        $this->getView()->setMessage($message, "alert-error");
    }

}

?>
