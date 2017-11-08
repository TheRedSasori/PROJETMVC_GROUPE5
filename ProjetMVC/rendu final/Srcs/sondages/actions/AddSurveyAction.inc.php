<?php

require_once("model/Survey.inc.php");
require_once("model/Response.inc.php");
require_once("actions/Action.inc.php");

class AddSurveyAction extends Action
{

    /**
     * Traite les données envoyées par le formulaire d'ajout de sondage.
     *
     * Si l'utilisateur n'est pas connecté, un message lui demandant de se connecter est affiché.
     *
     * Sinon, la fonction ajoute le sondage à la base de données. Elle transforme
     * les réponses et la question à l'aide de la fonction PHP 'htmlentities' pour éviter
     * que du code exécutable ne soit inséré dans la base de données et affiché par la suite.
     *
     * Un des messages suivants doivent être affichés à l'utilisateur :
     * - "La question est obligatoire.";
     * - "Il faut saisir au moins 2 réponses.";
     * - "Merci, nous avons ajouté votre sondage.".
     *
     * Le visiteur est finalement envoyé vers le formulaire d'ajout de sondage en cas d'erreur
     * ou vers une vue affichant le message "Merci, nous avons ajouté votre sondage.".
     *
     * @see Action::run()
     */
    public function run()
    {

        //Ajout d'un sondage , en recuperant les données envoyer par le formulaire
        //et en les integrant dans notre base de donnée

        if (!empty($_POST)) {
            //filter_var () nous permet de filtrer notre variable par securité pour qu'il n'y ai pas d'injection de code en encodant les charactères spéciaux
            $question  = $_POST['questionSurvey'];
            $question2 = (filter_var($question, FILTER_SANITIZE_STRING) === false)
                ? false
                : filter_var($question, FILTER_SANITIZE_STRING);
            //recuperation des reponses et on les controle par securité
            $rep1  = $_POST['responseSurvey1'];
            $rep12 = (filter_var($rep1, FILTER_SANITIZE_STRING) === false)
                ? false
                : filter_var($rep1, FILTER_SANITIZE_STRING);

            $rep2  = $_POST['responseSurvey2'];
            $rep22 = (filter_var($rep2, FILTER_SANITIZE_STRING) === false)
                ? false
                : filter_var($rep2, FILTER_SANITIZE_STRING);

            $rep3  = $_POST['responseSurvey3'];
            $rep32 = (filter_var($rep3, FILTER_SANITIZE_STRING) === false)
                ? false
                : filter_var($rep3, FILTER_SANITIZE_STRING);

            $rep4  = $_POST['responseSurvey4'];
            $rep42 = (filter_var($rep4, FILTER_SANITIZE_STRING) === false)
                ? false
                : filter_var($rep4, FILTER_SANITIZE_STRING);

            $rep5  = $_POST['responseSurvey5'];
            $rep52 = (filter_var($rep5, FILTER_SANITIZE_STRING) === false)
                ? false
                : filter_var($rep5, FILTER_SANITIZE_STRING);

            // on cree un tableau où on intègre nos réponses
            $replies = [
                'responseSurvey1' => $rep12,
                'responseSurvey2' => $rep22,
                'responseSurvey3' => $rep32,
                'responseSurvey4' => $rep42,
                'responseSurvey5' => $rep52
            ];
            //on intègre l'utilisateur connecté
            $survey = [
                'owner' => $this->getSessionLogin()
            ];

            //si on a pas de question on affiche un mesage d'erreur
            //soit on remplie notre tableau de question par les question verifier ci-dessu
            if (empty($question2)) {
                $this->setAddSurveyFormView("La question est obligatoire.");
            } else {
                //on remplie $survey avec les reponse remplite et non vide du tableau $replies
                $survey['question'] = $question2;
                foreach ($replies as $key => $reply) {
                    if (!empty($reply)) {
                        $survey[$key] = $reply;
                    }
                }
                //si on a moin que 2 repenses on affiche un mesage d'erreur
                //soit on in
                if (count($survey) < 4) {
                    $this->setAddSurveyFormView("Il faut saisir au moins 2 réponses.");
                } else {
                    $this->database->saveSurvey($survey);
                    $this->setMessageView("Merci, nous avons ajouté votre sondage.");
                }
            }
        }
    }


    private function setAddSurveyFormView($message)
    {
        $this->setView(getViewByName("AddSurveyForm"));
        $this->getView()->setMessage($message, "alert-error");
    }

}

?>
