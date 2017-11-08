<?php

require_once("actions/Action.inc.php");

class HomeAction extends Action
{
    /**
     *
     * @see Action::run()
     */
    public function run()
    {

        // on recupere tout les sondages
        $this->setView(getViewByName("Surveys"));
        $allSurveys = $this->database->loadAllSurveys();

        $surveys = [];
        $replies = [];
        // tries des sondages
        foreach ($allSurveys as $key => $survey) {

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

        //on charges tout les commentaires
        $comments = $this->database->loadComments();

        $triComments = [];
        $cont        = [];

        //tries les commentaire
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
        //on charges les commentaires et les sondages dans la view
        $this->getView()->setSurveys($surveys);
        $this->getView()->setComments($triComments);

    }

}

?>
