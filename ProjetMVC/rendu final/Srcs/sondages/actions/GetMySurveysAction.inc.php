<?php

require_once("actions/Action.inc.php");

class GetMySurveysAction extends Action
{

    /**
     * Construit la liste des sondages de l'utilisateur et le dirige vers la vue "ServeysView"
     * de façon à afficher les sondages.
     *
     * Si l'utilisateur n'est pas connecté, un message lui demandant de se connecter est affiché.
     *
     * @see Action::run()
     */
    public function run()
    {
        if ($this->getSessionLogin() === null) {
            $this->setMessageView("Vous devez être authentifié.");

            return;
        }

        $this->setView(getViewByName("Surveys"));

        $owner        = $this->getSessionLogin();
        $ownerSurveys = $this->database->loadSurveysByOwner($owner);
        $surveys      = [];
        $replies      = [];

        foreach ($ownerSurveys as $key => $survey) {

            $idSurvey = intval($survey['id_surveys']);
            $id       = intval($survey['id']);
            $user     = $survey['owner'];
            $question = $survey['question'];
            $title    = $survey['title'];
            $count    = $survey['count'];

            if (!isset($surveys[$idSurvey])) {
                $replies = [];
            }

            $replies[] = [
                'id'    => $id,
                'title' => $title,
                'count' => $count
            ];

            $surveys[$idSurvey] = [
                'id_surveys' => $idSurvey,
                'owner'      => $user,
                'question'   => $question,
                'replies'    => $replies
            ];
        }

        $comments = $this->database->loadCommentsByOwner($owner);

        $triComments = [];
        $cont        = [];
        foreach ($comments as $key => $comment) {

            $idSurvey = intval($comment['id_surveys']);
            $id       = intval($comment['id']);
            $owner    = $comment['owner'];
            $contents = $comment['contents'];

            if (!isset($triComments[$idSurvey])) {
                $cont = [];
            }

            $cont[] = [
                'id'      => $id,
                'content' => $contents

            ];

            $triComments[$idSurvey] = [
                'id_surveys' => $idSurvey,
                'owner'      => $owner,
                'content'    => $cont
            ];


        }

        $this->getView()->setComments($triComments);
        $this->getView()->setSurveys($surveys);
    }
}




