<?php

require_once("actions/Action.inc.php");

class SearchAction extends Action
{

    /**
     * Construit la liste des sondages dont la question contient le mot clé
     * contenu dans la variable $_POST["keyword"]. L'utilisateur est ensuite
     * dirigé vers la vue "ServeysView" permettant d'afficher les sondages.
     *
     * Si la variable $_POST["keyword"] est "vide", le message "Vous devez entrer un mot clé
     * avant de lancer la recherche." est affiché à l'utilisateur.
     *
     * @see Action::run()
     */
    public function run()
    {
        //on recupere le mot cleé dans la recherche
        $keyword = $_POST['keyword'];
        //on la filtre
        $filKeyword = (filter_var($keyword, FILTER_SANITIZE_STRING) === false)
            ? false
            : filter_var($keyword, FILTER_SANITIZE_STRING);

        //on recupere les survey correspendant a la recherche
        $this->setView(getViewByName("Surveys"));
        $keywordSurveys = $this->database->loadSurveysByKeyword($filKeyword);

        $surveys = [];
        $replies = [];
        //trie des survey
        foreach ($keywordSurveys as $key => $survey) {

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

        //on charge les commentaire de la recherche
        $comments    = $this->database->loadCommentsByKeyword($filKeyword);
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
        //on charge dans la vue
        $this->getView()->setComments($triComments);
        $this->getView()->setSurveys($surveys);

    }

}

?>
