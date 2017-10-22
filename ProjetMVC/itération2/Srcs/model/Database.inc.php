<?php
require_once("model/Survey.inc.php");
require_once("model/Response.inc.php");

class Database
{

    private $connection;

    /**
     * Ouvre la base de données. Si la base n'existe pas elle
     * est créée à l'aide de la méthode createDataBase().
     */
    public function __construct()
    {
        $dbHost  = "localhost";
        $dbBd    = "sondages";
        $dbPass  = "";
        $dbLogin = "root";
        $url     = 'mysql:host=' . $dbHost . ';dbname=' . $dbBd;
        //$url = 'sqlite:database.sqlite';
        $this->connection = new PDO($url, $dbLogin, $dbPass);
        if (!$this->connection) {
            die("impossible d'ouvrir la base de données");
        }
        $this->createDataBase();
    }


    /**
     * Initialise la base de données ouverte dans la variable $connection.
     * Cette méthode crée, si elles n'existent pas, les trois tables :
     * - une table users(nickname char(20), password char(50));
     * - une table surveys(id integer primary key autoincrement,
     *                        owner char(20), question char(255));
     * - une table responses(id integer primary key autoincrement,
     *        id_survey integer,
     *        title char(255),
     *        count integer);
     */
    private function createDataBase()
    {

        $this->connection->exec("CREATE TABLE IF NOT EXISTS users (" .
            " nickname char(20)," .
            " password char(50)" .
            ");");

        $this->connection->exec("CREATE TABLE IF NOT EXISTS surveys (" .
            " id int NOT NULL AUTO_INCREMENT PRIMARY KEY," .
            " owner char(20)," .
            " question char(255)" .
            ");");

        $this->connection->exec("CREATE TABLE IF NOT EXISTS responses (" .
            " id int NOT NULL AUTO_INCREMENT PRIMARY KEY," .
            " id_surveys int," .
            " title char(255)," .
            " count int" .
            ");");

    }

    /**
     * Vérifie si un pseudonyme est valide, c'est-à-dire,
     * s'il contient entre 3 et 10 caractères et uniquement des lettres.
     *
     * @param string $nickname Pseudonyme à vérifier.
     *
     * @return boolean True si le pseudonyme est valide, false sinon.
     */
    private function checkNicknameValidity($nickname)
    {

        $name = (filter_var($nickname, FILTER_SANITIZE_STRING) === false)
            ? false
            : filter_var($nickname, FILTER_SANITIZE_STRING);


        if ($name !== false) {
            if (strlen($nickname) >= 3 && strlen($nickname) <= 10 && ctype_alpha($nickname)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

    /**
     * Vérifie si un mot de passe est valide, c'est-à-dire,
     * s'il contient entre 3 et 10 caractères.
     *
     * @param string $password Mot de passe à vérifier.
     *
     * @return boolean True si le mot de passe est valide, false sinon.
     */
    private function checkPasswordValidity($password)
    {

        if (strlen($password) > 3 && strlen($password) < 10) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Vérifie la disponibilité d'un pseudonyme.
     *
     * @param string $nickname Pseudonyme à vérifier.
     *
     * @return boolean True si le pseudonyme est disponible, false sinon.
     */
    private function checkNicknameAvailability($nickname)
    {
        $nickName = $this->connection->quote($nickname);

        $nick   = $this->connection->query("SELECT `nickname` FROM users WHERE nickname=$nickName");
        $result = $nick->fetch();


        if ($result === false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Vérifie qu'un couple (pseudonyme, mot de passe) est correct.
     *
     * @param string $nickname Pseudonyme.
     * @param string $password Mot de passe.
     *
     * @return boolean True si le couple est correct, false sinon.
     */
    public function checkPassword($nickname, $password)
    {

        if ($this->checkNicknameValidity($nickname) === true &&
            $this->checkPasswordValidity($password) === true &&
            $this->checkNicknameAvailability($nickname) === true
        ) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Ajoute un nouveau compte utilisateur si le pseudonyme est valide et disponible et
     * si le mot de passe est valide. La méthode peut retourner un des messages d'erreur qui suivent :
     * - "Le pseudo doit contenir entre 3 et 10 lettres.";
     * - "Le mot de passe doit contenir entre 3 et 10 caractères.";
     * - "Le pseudo existe déjà.".
     *
     * @param string $nickname Pseudonyme.
     * @param string $password Mot de passe.
     *
     * @return boolean|string True si le couple a été ajouté avec succès, un message d'erreur sinon.
     */
    public function addUser($nickname, $password)
    {

        if ($this->checkPassword($nickname, $password) === false) {
            return false;

        } else {

            # Protect var
            $name = $this->connection->quote($nickname);
            $pass = $this->connection->quote($password);

            $this->connection->exec("INSERT INTO users VALUES( $name, $pass)");

            return true;

        }
    }

    /**
     * Change le mot de passe d'un utilisateur.
     * La fonction vérifie si le mot de passe est valide. S'il ne l'est pas,
     * la fonction retourne le texte 'Le mot de passe doit contenir entre 3 et 10 caractères.'.
     * Sinon, le mot de passe est modifié en base de données et la fonction retourne true.
     *
     * @param string $nickname Pseudonyme de l'utilisateur.
     * @param string $password Nouveau mot de passe.
     *
     * @return boolean|string True si le mot de passe a été modifié, un message d'erreur sinon.
     */
    public function updateUser($nickname, $password)
    {
        /* TODO START */

        if ($this->checkPasswordValidity($password) === true) {
            $this->connection->exec("UPDATE users SET password='$password' WHERE nickname='$nickname'");
            echo "Le mot de passe est modifié.";

            return true;
        } else {
            echo "Le mot de passe doit contenir entre 3 et 10 caractères.";

            return false;
        }

        /* TODO END */

        return true;
    }

    /**
     * Sauvegarde un sondage dans la base de donnée et met à jour les indentifiants
     * du sondage et des réponses.
     *
     * @param Survey $survey Sondage à sauvegarder.
     *
     * @return boolean True si la sauvegarde a été réalisée avec succès, false sinon.
     */
    public function saveSurvey($survey)
    {
        /* TODO START */


        /* TODO END */
        return true;
    }

    /**
     * Sauvegarde une réponse dans la base de donnée et met à jour son indentifiant.
     *
     * @param Response $response Réponse à sauvegarder.
     *
     * @return boolean True si la sauvegarde a été réalisée avec succès, false sinon.
     */
    private function saveResponse($response)
    {
        /* TODO START */
        /* TODO END */
        return true;
    }

    /**
     * Charge l'ensemble des sondages créés par un utilisateur.
     *
     * @param string $owner Pseudonyme de l'utilisateur.
     *
     * @return array(Survey)|boolean Sondages trouvés par la fonction ou false si une erreur s'est produite.
     */
    public function loadSurveysByOwner($owner)
    {
        /* TODO START */
        /* TODO END */
    }

    /**
     * Charge l'ensemble des sondages dont la question contient un mot clé.
     *
     * @param string $keyword Mot clé à chercher.
     *
     * @return array(Survey)|boolean Sondages trouvés par la fonction ou false si une erreur s'est produite.
     */
    public function loadSurveysByKeyword($keyword)
    {
        /* TODO START */
        /* TODO END */
    }


    /**
     * Enregistre le vote d'un utilisateur pour la réponse d'identifiant $id.
     *
     * @param int $id Identifiant de la réponse.
     *
     * @return boolean True si le vote a été enregistré, false sinon.
     */
    public function vote($id)
    {
        /* TODO START */
        /* TODO END */
    }

    /**
     * Construit un tableau de sondages à partir d'un tableau de ligne de la table 'surveys'.
     * Ce tableau a été obtenu à l'aide de la méthode fetchAll() de PDO.
     *
     * @param array $arraySurveys Tableau de lignes.
     *
     * @return array(Survey)|boolean Le tableau de sondages ou false si une erreur s'est produite.
     */
    private function loadSurveys($arraySurveys)
    {
        $surveys = [];
        /* TODO START */

        /* TODO END */

        return $surveys;
    }

    /**
     * Construit un tableau de réponses à partir d'un tableau de ligne de la table 'responses'.
     * Ce tableau a été obtenu à l'aide de la méthode fetchAll() de PDO.
     *
     * @param Survey $survey       Le sondage.
     * @param array  $arraySurveys Tableau de lignes.
     *
     * @return array(Response)|boolean Le tableau de réponses ou false si une erreur s'est produite.
     */
    private function loadResponses($survey, $arrayResponses)
    {
        $responses = [];
        /* TODO START */

        /* TODO END */

        return $responses;
    }

}

?>
