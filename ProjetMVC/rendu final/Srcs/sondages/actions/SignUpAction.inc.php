<?php

require_once("actions/Action.inc.php");


class SignUpAction extends Action
{

    /**
     * Traite les données envoyées par le formulaire d'inscription
     * ($_POST['signUpLogin'], $_POST['signUpPassword'], $_POST['signUpPassword2']).
     *
     * Le compte est crée à l'aide de la méthode 'addUser' de la classe Database.
     *
     * Si la fonction 'addUser' retourne une erreur ou si le mot de passe et sa confirmation
     * sont différents, on envoie l'utilisateur vers la vue 'SignUpForm' contenant
     * le message retourné par 'addUser' ou la chaîne "Le mot de passe et sa confirmation
     * sont différents.";
     *
     * Si l'inscription est validée, le visiteur est envoyé vers la vue 'MessageView' avec
     * un message confirmant son inscription.
     *
     * @see Action::run()
     */
    public function run()
    {

        //on recuperer dans les donne dans le formulaire d'iscription
        $login       = $_POST['signUpLogin'];
        $pass        = $_POST['signUpPassword'];
        $passConfirm = $_POST['signUpPassword2'];
        // on teste le nom et on le filtre
        $name = (filter_var($login, FILTER_SANITIZE_STRING) === false)
            ? false
            : filter_var($login, FILTER_SANITIZE_STRING);
        // on teste le password et on le filtre
        $pass2 = (filter_var($pass, FILTER_SANITIZE_STRING) === false)
            ? false
            : filter_var($pass, FILTER_SANITIZE_STRING);
        // on teste la confirmation du password et on la filtre
        $passConfirm2 = (filter_var($passConfirm, FILTER_SANITIZE_STRING) === false)
            ? false
            : filter_var($passConfirm, FILTER_SANITIZE_STRING);


        //on verifie si le nom d'uttilisateur existe ou pas dans la base de donnée , si il existe pas on l'integre dans la base de données
        //avec le password
        //sinon erreur
        if ($pass2 === $passConfirm2) {

            if ($this->database->addUser($name, $pass2) === true) {
                $this->setView(getViewByName('Message'));
                $this->getView()->setMessage('Votre compte à été crée avec succès.');
            } else {
                $this->setSignUpFormView($this->database->addUser($name, $pass2));
            }
        } else {
            $this->setSignUpFormView('Le mot de passe et sa confirmation sont différents.');
        }

    }

    private function setSignUpFormView($message)
    {
        $this->setView(getViewByName("SignUpForm"));
        $this->getView()->setMessage($message);
    }

}