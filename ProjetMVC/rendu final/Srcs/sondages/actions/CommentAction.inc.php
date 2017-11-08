<?php

require_once("actions/Action.inc.php");

class CommentAction extends Action
{

    /**
     *
     * @see Action::run()
     */
    public function run()
    {
        //verifier si l'utilisateur est connectée
        if ($this->getSessionLogin() === null) {
            $this->setMessageView("Vous devez être authentifié.");

            return;
        }

        // verifier si $_POST si il existe , on ajoute le commentaire
        if (!empty($_POST)) {

            $owner = $this->getSessionLogin();

            $id_surveys  = $_REQUEST['id'];
            $id_surveys2 = (filter_var($id_surveys, FILTER_SANITIZE_NUMBER_INT) === false)
                ? false
                : filter_var($id_surveys, FILTER_SANITIZE_NUMBER_INT);

            $question  = $_REQUEST['question'];
            $question2 = (filter_var($question, FILTER_SANITIZE_STRING) === false)
                ? false
                : filter_var($question, FILTER_SANITIZE_STRING);

            $comment  = $_POST['comment'];
            $comment2 = (filter_var($comment, FILTER_SANITIZE_STRING) === false)
                ? false
                : filter_var($comment, FILTER_SANITIZE_STRING);

            $trimmedComment = rtrim($comment2);

            $this->database->AddComment($id_surveys2, $owner, $trimmedComment, $question2);
            $this->setMessageView("Merci, nous avons ajouté votre commentaire.");

        }
    }

}
