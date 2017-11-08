<?php

require_once("actions/Action.inc.php");

class LoginAction extends Action
{

    /**
     * Traite les données envoyées par le visiteur via le formulaire de connexion
     * (variables $_POST['nickname'] et $_POST['password']).
     * Le mot de passe est vérifié en utilisant les méthodes de la classe Database.
     * Si le mot de passe n'est pas correct, on affiche le message "Pseudo ou mot de passe incorrect."
     * Si la vérification est réussie, le pseudo est affecté à la variable de session.
     *
     * @see Action::run()
     */
    public function run()
    {
        //on recuperer dans les danne dans le formulaire de connection
        $login    = $_POST['nickname'];
        $password = $_POST['password'];

        // on teste le nom et on le filtre
        $name = (filter_var($login, FILTER_SANITIZE_STRING) === false)
            ? false
            : filter_var($login, FILTER_SANITIZE_STRING);
        // on teste le password et on le filtre
        $pass = (filter_var($password, FILTER_SANITIZE_STRING) === false)
            ? false
            : filter_var($password, FILTER_SANITIZE_STRING);
        //on verifie si le nom d'uttilisateur et son mot de passe dans la base de données
        //si c'est correcte on se connecte , sinon erreur
        if ($this->database->checkPassword($name, $pass) === true) {
            $this->setSessionLogin($name);
            $this->setMessageView("Bienvenue $name !");
        } else {
            $msg = "Pseudo ou mot de passe incorrect.";
            $this->setMessageView($msg);
        };

    }

}

?>
