<?php

require_once("actions/Action.inc.php");

class LoginAction extends Action {

	/**
	 * Traite les données envoyées par le visiteur via le formulaire de connexion
	 * (variables $_POST['nickname'] et $_POST['password']).
	 * Le mot de passe est vérifié en utilisant les méthodes de la classe Database.
	 * Si le mot de passe n'est pas correct, on affiche le message "Pseudo ou mot de passe incorrect."
	 * Si la vérification est réussie, le pseudo est affecté à la variable de session.
	 *
	 * @see Action::run()
	 */
	public function run() {
  	/* TODO START */
        $login = $_POST['nickname'];
        $pw = $_POST['password'];

        if ($this->database->checkPassword($login,$pw )=== true){
                $this->setSessionLogin($login);
            $this->setMessageView("connection OK !");
		}else{
            $msg = "Pseudo ou mot de passe incorrect.";
            $this->setMessageView($msg);
		};
        /* TODO END */
	}

}

?>
