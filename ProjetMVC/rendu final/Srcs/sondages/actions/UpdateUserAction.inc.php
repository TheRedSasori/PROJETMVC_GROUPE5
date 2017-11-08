<?php

require_once("actions/Action.inc.php");

class UpdateUserAction extends Action
{

    /**
     * Met à jour le mot de passe de l'utilisateur en procédant de la façon suivante :
     *
     * Si toutes les données du formulaire de modification de profil ont été postées
     * ($_POST['updatePassword'] et $_POST['updatePassword2']), on vérifie que
     * le mot de passe et la confirmation sont identiques.
     * S'ils le sont, on modifie le compte avec les méthodes de la classe 'Database'.
     *
     * Si une erreur se produit, le formulaire de modification de mot de passe
     * est affiché à nouveau avec un message d'erreur.
     *
     * Si aucune erreur n'est détectée, le message 'Modification enregistrée.'
     * est affiché à l'utilisateur.
     *
     * @see Action::run()
     */
    public function run()
    {

        // charger deux passe dans nos furmulaire de modification avec _POST
        $pass        = $_POST['updatePassword'];
        $passConfirm = $_POST['updatePassword2'];

        // on filtre nos deux password
        $pass2 = (filter_var($pass, FILTER_SANITIZE_STRING) === false)
            ? false
            : filter_var($pass, FILTER_SANITIZE_STRING);

        $passConfirm2 = (filter_var($passConfirm, FILTER_SANITIZE_STRING) === false)
            ? false
            : filter_var($passConfirm, FILTER_SANITIZE_STRING);

        $login = $this->getSessionLogin();

        //si le mot de pass est different de l'ancien alor on alter table notre base de donnée
        if ($pass2 === $passConfirm2 &&
            $this->database->updateUser($login, $pass2) === true) {

            $this->setMessageView('Modification enregistrée.');
        } else {
            $this->setUpdateUserFormView($this->database->updateUser($login, $pass2));
        }
    }

    private function setUpdateUserFormView($message)
    {
        $this->setView(getViewByName("UpdateUserForm"));
        $this->getView()->setMessage($message, "alert-error");
    }

}

?>