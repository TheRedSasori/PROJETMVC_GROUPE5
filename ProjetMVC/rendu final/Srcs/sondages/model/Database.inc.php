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

        $this->connection->exec("CREATE TABLE IF NOT EXISTS comments (" .
            " id int NOT NULL AUTO_INCREMENT PRIMARY KEY," .
            " id_surveys int," .
            " owner char(20)," .
            " question char(255)," .
            " contents char(255)" .
            ");");

        $this->connection->exec("ALTER TABLE responses ADD FOREIGN KEY (id_surveys) REFERENCES sondages.surveys (id);");

        $this->connection->exec("ALTER TABLE comments ADD FOREIGN KEY (id_surveys) REFERENCES sondages.surveys (id);");
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

        if (strlen($nickname) >= 3 && strlen($nickname) <= 10 && ctype_alpha($nickname)) {
            return true;
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

        if (strlen($password) >= 3 && strlen($password) <= 10) {
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

        $nick   = $this->connection->query("SELECT nickname FROM users WHERE nickname=$nickName");
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
        $nickName = $this->connection->quote($nickname);

        $realpass = $this->connection->query("SELECT password FROM users WHERE nickname=$nickName");
        $result   = $realpass->fetch();
        $pass     = sha1($password);


        if ($result[0] === $pass) {
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


        if ($this->checkNicknameValidity($nickname) === false) {
            $erreur = "Le pseudo doit contenir entre 3 et 10 lettres.";

            return $erreur;
        }

        if ($this->checkPasswordValidity($password) === false) {
            $erreur = "Le mot de passe doit contenir entre 3 et 10 charactères.";

            return $erreur;
        }

        if ($this->checkNicknameAvailability($nickname) === false) {
            $erreur = "Le pseudo est déjà utilisé.";

            return $erreur;
        }

        if ($this->checkNicknameAvailability($nickname) === true &&
            $this->checkPasswordValidity($password) === true &&
            $this->checkNicknameAvailability($nickname) === true
        ) {

            $name      = $this->connection->quote($nickname);
            $pass      = sha1($password);
            $password2 = $this->connection->quote($pass);

            $this->connection->exec("INSERT INTO users VALUES( $name, $password2)");

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
        $nickName = $this->connection->quote($nickname);
        $realpass = $this->connection->query("SELECT password FROM users WHERE nickname=$nickName");
        $result   = $realpass->fetch();
        $pass     = sha1($password);

        if ($this->checkPasswordValidity($password) === true &&
            $result[0] !== $pass
        ) {
            $pass2 = $this->connection->quote($pass);
            $this->connection->exec("UPDATE users SET password=$pass2 WHERE nickname=$nickName");

            return true;
        } else {

            if ($result[0] === $pass) {
                $erreur = "Le mot de passe doit être différent de l'ancien.";
            } else {
                $erreur = "Le mot de passe doit contenir entre 3 et 10 caractères.";
            }

            return $erreur;
        }
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
        $owner    = $this->connection->quote($survey['owner']);
        $question = $this->connection->quote($survey['question']);


        $this->connection->exec("INSERT INTO surveys (owner, question) VALUES($owner,$question);");
        $this->saveResponse($survey);

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
        $replies = [];
        foreach ($response as $key => $item) {
            if ($key !== 'owner' && $key !== 'question') {
                $replies[] = $item;
            }
        }
        $owner    = $this->connection->quote($response['owner']);
        $question = $this->connection->quote($response['question']);

        $id_surveys = $this->connection->query("SELECT id FROM surveys WHERE owner=$owner AND question=$question;");
        $result     = $id_surveys->fetch();


        foreach ($replies as $reply) {
            $rep = $this->connection->quote($reply);
            $this->connection->exec("INSERT INTO responses (id_surveys,title,count ) VALUES($result[0],$rep,'0');");
        }

        return true;

    }

    /**
     * Charge l'ensemble des sondages créés par un utilisateur.
     *
     * @param string $owner Pseudonyme de l'utilisateur.
     *
     * @return array(Survey)|boolean Sondages trouvés par la fonction ou false si une erreur s'est produite.
     */
    public
    function loadSurveysByOwner(
        $owner
    ){

        $owner2  = $this->connection->quote($owner);
        $surveys = $this->connection->query("SELECT owner, question, id_surveys, responses.id, title, count FROM surveys INNER JOIN responses ON surveys.id = id_surveys WHERE owner = $owner2 ");
        $result  = $surveys->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * Charge l'ensemble des sondages dont la question contient un mot clé.
     *
     * @param string $keyword Mot clé à chercher.
     *
     * @return array(Survey)|boolean Sondages trouvés par la fonction ou false si une erreur s'est produite.
     */
    public
    function loadSurveysByKeyword(
        $keyword
    ){
        $keyword2 = '%' . $keyword . '%';
        $keyword2 = $this->connection->quote($keyword2);
        $surveys  = $this->connection->query("SELECT owner, question, id_surveys, responses.id, title, count FROM surveys INNER JOIN responses ON surveys.id = id_surveys WHERE question LIKE $keyword2");
        $result   = $surveys->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }


    /**
     * Enregistre le vote d'un utilisateur pour la réponse d'identifiant $id.
     *
     * @param int $id Identifiant de la réponse.
     *
     * @return boolean True si le vote a été enregistré, false sinon.
     */
    public
    function vote(
        $id
    ){
        $count    = $this->connection->query("SELECT count FROM responses WHERE id='$id';");
        $result   = $count->fetch();
        $newCount = $result[0] + 1;
        $this->connection->exec("UPDATE `responses` SET `count` = '$newCount' WHERE `responses`.`id` ='$id';");

    }

    /**
     * Construit un tableau de sondages à partir d'un tableau de ligne de la table 'surveys'.
     * Ce tableau a été obtenu à l'aide de la méthode fetchAll() de PDO.
     *
     * @param array $arraySurveys Tableau de lignes.
     *
     * @return array(Survey)|boolean Le tableau de sondages ou false si une erreur s'est produite.
     */
    private
    function loadSurveys(
        $arraySurveys
    ){
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
    private
    function loadResponses(
        $survey,
        $arrayResponses
    ){
        $responses = [];
        /* TODO START */

        /* TODO END */

        return $responses;
    }

    /**
     * Construit un tableau qui contient le createur du survey , la question et l'id du survey avec les reponse et leur titre et vote
     */
    public
    function loadAllSurveys()
    {

        $surveys = $this->connection->query("SELECT owner, question, id_surveys, responses.id, title, count FROM surveys INNER JOIN responses ON surveys.id = id_surveys");
        $result  = $surveys->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }


    /**
     * on efface le survey , avec les reponse et les commentaire
     */
    public
    function deleteSurvey(
        $id
    ){
        $this->connection->exec("DELETE FROM surveys WHERE id=$id");
        $this->connection->exec("DELETE FROM responses WHERE id_surveys=$id");
        $this->connection->exec("DELETE FROM comments WHERE id_surveys=$id");

        return true;
    }


    /**
     * Construit un tableau qui contient la question et le titre
     */
    public
    function loadSurveyById(
        $id
    ){
        $survey = $this->connection->query("SELECT  question,title FROM surveys INNER JOIN responses ON surveys.id = id_surveys WHERE surveys.id=$id");
        $result = $survey->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }

    /**
     * on modifie la table reponse , on injectant les nouveaux reponse
     */
    public
    function EditSurvey(
        $id,
        $survey
    ){
        $i = 0;
        foreach ($survey as $reply) {

            $rep = $this->connection->quote($reply);
            $this->connection->exec("UPDATE responses SET title = $rep, count = '0'  WHERE responses.id ='$id[$i]'");
            $i = $i + 1;
        }

    }

    /**
     * Construit un tableau qui contient la question et le titre
     */

    public
    function loadIdResponse(
        $id,
        $responses
    ){
        $id_reponses = [];

        foreach ($responses as $response) {

            $idSurvey      = $this->connection->query("SELECT id FROM responses WHERE title='$response' AND id_surveys=$id;");
            $result        = $idSurvey->fetch();
            $id_reponses[] = $result[0];

        }

        return $id_reponses;

    }


    /**
     * on insert les commentaire dans la table comments qui contient tout nos commentaire
     */
    public
    function AddComment(
        $id_survey,
        $owner,
        $comment,
        $question
    ){

        $id        = $this->connection->quote($id_survey);
        $owner2    = $this->connection->quote($owner);
        $comment2  = $this->connection->quote($comment);
        $question2 = $this->connection->quote($question);

        $this->connection->exec("INSERT INTO comments(id_surveys,owner,question, contents) VALUES($id,$owner2,$question2,$comment2);");

    }

    /**
     * Construit un tableau qui contient tout les commentaire trier par id
     */

    public
    function loadComments()
    {

        $surveys = $this->connection->query("SELECT * FROM `comments` ORDER BY `comments`.`id_surveys`;");
        $result  = $surveys->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }

    /**
     * Construit un tableau qui contient les commentaire d'un nom d'utilisateur et trier par id
     */

    public
    function loadCommentsByOwner(
        $owner
    ){
        $owner2  = $this->connection->quote($owner);
        $surveys = $this->connection->query("SELECT * FROM `comments` WHERE owner=$owner2 ORDER BY `comments`.`id_surveys`");
        $result  = $surveys->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }

    /**
     * Construit un tableau qui contient les commentaire a partire d'un mot clé  $keyword et trier par id
     */
    public
    function loadCommentsByKeyword(
        $keyword
    ){
        $keyword2 = '%' . $keyword . '%';
        $keyword3 = $this->connection->quote($keyword2);
        $surveys  = $this->connection->query("SELECT * FROM `comments` WHERE question LIKE $keyword3 ORDER BY `comments`.`id_surveys`");
        $result   = $surveys->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }

}

?>
