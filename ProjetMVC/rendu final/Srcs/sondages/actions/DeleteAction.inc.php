<?php

require_once("actions/Action.inc.php");

class DeleteAction extends Action
{

    /**
     *
     * @see Action::run()
     */
    public function run()
    {

        //supprimer un sondage , par rapport a l'ID
        $id_surveys  = $_REQUEST['id'];
        $id_surveys2 = (filter_var($id_surveys, FILTER_SANITIZE_NUMBER_INT) === false)
            ? false
            : filter_var($id_surveys, FILTER_SANITIZE_NUMBER_INT);


        $this->database->deleteSurvey((int)$id_surveys2);

        if ($this->database->deleteSurvey((int)$id_surveys2) === true) {

            $this->setMessageView('Sondage supprimé.');
        } else {
            $this->setMessageView('Erreur.');
        }
    }

}

?>
