<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 1-12-2015
 * Time: 15:57
 */

namespace CodeSnippet\Models;

class User extends BaseModel
{
    /**
     * User's first name
     *
     * @var null|string
     */
    protected $db_firstName = null;

    /**
     * User's last name
     *
     * @var null|string
     */
    protected $db_lastName = null;

    /**
     * User preposition (tussenvoegsel)
     *
     * @var null|string corresponds to preposition
     */
    protected $db_preposition = null;

    /**
     * User's gender
     *
     * @var null|string
     * Corresponds to the gender of the user.
     */
    protected $db_gender = null;

    /**
     * User's email address
     *
     * @var null|string
     */
    protected $db_emailAddress = null;

    /**
     * This contains the password of the user
     *
     * @var null|string $db_password Corresponds to the database column 'password'
     */
    protected $db_password = null;

    /**
     * Relation one
     *
     * @var null|int
     */
    protected $db_relationOne = null;

    /**
     * Indicates if the user is an administrator.
     *
     * @var null|int
     */
    protected $db_administrator = null;

    protected $db_signUpDate = null;

    /**
     * Creates new User object.
     * @param array $data
     */
    protected function create($data)
    {
        if (isset($data['firstName'])) {
            $this->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $this->setLastName($data['lastName']);
        }
        if (isset($data['gender'])) {
            $this->setGender($data['gender']);
        }
        if (isset($data['preposition'])) {
            $this->setPreposition($data['preposition']);
        }
        if (isset($data['password'])) {
            $this->setPassword($data['password']);
        }
        if (isset($data['emailAddress'])) {
            $this->setEmailAddress($data['emailAddress']);
        }
        if (isset($data['signUpdate'])) {
            $this->setAdministratorRole($data['administrator']);
        }
        if (isset($data['administrator'])) {
            $this->setAdministratorRole($data['administrator']);
        }
        $this->setSignUpDate(new \DateTime());
    }

    /**
     * Load User by ID
     * @param int $id
     * @throws NotFoundException
     */
    public function load($id)
    {
        //ServiceContainer = PDO class
        $db = ServiceContainer::getInstance()->database();

        //query = prepare + execute
        $stmt = $db->query("SELECT * FROM `users` WHERE `id` = :id", array('id' => $id));
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new NotFoundException(sprintf('User with id: %d could not be found.', $id));
        }
        $this->instantiate($data);
    }

    public function save()
    {
        //ServiceContainer = PDO class
        $db = ServiceContainer::getInstance()->database();

        $now = new \DateTime();
        
        $parameters = array(
            'id'                        => $this->db_id,
            'firstName'                 => $this->db_firstName,
            'preposition'               => $this->db_preposition,
            'lastName'                  => $this->db_lastName,
            'gender'                    => $this->db_gender,
            'emailAddress'              => $this->db_emailAddress,
            'relationOne'               => $this->db_relationOne,
            'administrator'             => $this->db_administrator,
            'signUpDate'                => $now->format('d-m-Y'),

        );
        if ($this->db_id === null) {
            $stmt = $db->prepare(
                "INSERT INTO `user`
                (`id`, `firstBame`, `preposition`, `lastName`, `gender`, `emailAddress`,`relationOne`, `administrator`)
                VALUES (:id, :firstName, :preposition, :lastName, :gender, :emailAddress, :relationOne, :administrator)"
            );
            $stmt->execute($parameters);
            $this->db_id = (int)$db->getLastInsertedId();
        } else {
            $stmt = $db->prepare(
                "UPDATE `user` SET
                                `firstName` = :firstName,
                                `preposition` = :preposition,
                                `lastName` = :lastName,
                                `gender` = :gender,
                                `emailAddress` = :emailAddress,
                                `relationOne` = :relationOne,
                                `administrator` = :administrator
                                  WHERE `id` = :id"
            );
            $stmt->execute($parameters);
        }
    }

    protected function instantiate($data)
    {
        $this->db_id                = (int)$data['id'];
        $this->db_firstName         = $data['first_name'];
        $this->db_lastName          = $data['last_name'];
        $this->db_gender            = $data['gender'];
        $this->db_preposition       = $data['preposition'];
        $this->db_emailAddress      = $data['emailAddress'];
        $this->db_relationOne       = (int)$data['relationOne'];
        $this->db_administrator     = (int)$data['administrator'];
        $this->db_signUpDate        = \DateTime::createFromFormat('d-m-Y',$data['signUpDate']);
    }

    /**
     * Delete user
     */
    public function delete()
    {
        $db = ServiceContainer::getInstance()->database();

        $query = "DELETE FROM `user` WHERE `id` =:id";
        $stmt  = $db->prepare($query);
        $stmt->execute(array('id' => $this->getId()));
    }

    /**
     * Returns all Users
     *
     * @return User[]
     */
    public static function all()
    {
        $db = ServiceContainer::getInstance()->database();

        $users = array();

        /* @var $stmt \PDOStatement */
        $stmt = $db->query("SELECT * FROM `user`");

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $users[] = new User($row);
        }
        return $users;
    }

    protected function setFirstName($firstName)
    {
        $firstName = filter_var(trim($firstName), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

        if ($firstName === false) {
            throw new \InvalidArgumentException(sprintf("Method %s expects argument name to be an string", __METHOD__));
        }
        $this->db_firstName = $firstName;
    }

    protected function setLastName($lastName)
    {
        $lastName = filter_var(trim($lastName), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

        if ($lastName === false) {
            throw new \InvalidArgumentException(sprintf("Method %s expects argument name to be an string", __METHOD__));
        }
        $this->db_lastName = $lastName;
    }

    protected function setPreposition($preposition)
    {
        $preposition = filter_var(trim($preposition), FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

        if ($preposition === false) {
            throw new \InvalidArgumentException(sprintf("Method %s expects argument name to be an string", __METHOD__));
        }
        $this->db_preposition = $preposition;
    }

    protected function setGender($gender)
    {
        $gender = strtolower($gender);
        $genders = array('m','v');
        //(in_array($gender, $genders) ? $this->db_gender = $gender : new \InvalidArgumentException('Gender is a required field'));

        if(in_array($gender, $genders)) {
            $this->db_gender = $gender;
        } else {
            throw new \InvalidArgumentException('Gender is a required field');
        }
    }

    protected function setPassword($password)
    {
        //TODO regex validation
        //'/[\\/\!@#\$€%\^\&\*\(\)\<\>,.?\[\]\{\}\+\-]/'

        if (preg_match_all('regex!', $password, $matches) < 1) {
            throw new \InvalidArgumentException('');
        }

        $this->db_password = password_hash($password, PASSWORD_DEFAULT);
    }

    protected function setEmailAddress($emailAddress)
    {
        if(filter_var(FILTER_SANITIZE_EMAIL === false)){
            throw new \InvalidArgumentException('Invalid Email');
        }
        if ($this->emailExists($emailAddress) && $emailAddress !== $this->db_emailAddress){
            throw new \InvalidArgumentException('email address already exists');
        }
        $this->db_emailAddress = $emailAddress;
    }

    protected function setRelationOne($relationOne)
    {
        $this->db_relationOne = $relationOne;
    }

    protected function setAdministratorRole(int $administratorRole)
    {
        $administratorRole = filter_var($administratorRole, FILTER_VALIDATE_INT);
        if ($administratorRole === false) {
            throw new \InvalidArgumentException(sprintf("Method %s expects argument administratorRole to be an integer", __METHOD__));
        }

        $this->db_administrator = $administratorRole;
    }

    /**
     * Set the Sign up Date for the User.
     *
     * @param \DateTime $signUpDate
     */
    protected function setSignUpDate(\DateTime $signUpDate)
    {
        $this->db_signUpDate = $signUpDate;
    }

    /**
     * get First Name of the user
     */
    public function getFirstName()
    {
        return $this->db_firstName;
    }

    /**
     * get last name of the user
     * @return null|string
     */
    public function getLastName()
    {
        return $this->db_lastName;
    }

    /**
     * Get the preposition of the user (tussenvoegsel)
     * @return null|string
     */
    public function getPreposition()
    {
        return $this->db_preposition;
    }

    protected function getPasswordHash()
    {
        return $this->db_password;
    }

    /**
     * get the gender of the User
     * @return null|string
     */
    public function getGender()
    {
        return $this->db_gender;
    }

    /**
     * Get email address of the user
     * @return null|string
     */
    public function getEmailAddress()
    {
        return $this->db_emailAddress;
    }

    /**
     * get relation One id.
     * @return int|null
     */
    public function getRelationOne()
    {
        return $this->db_relationOne;
    }

    public function getSignUpDate()
    {
        return $this->db_signUpDate;
    }

    /**
     * get User administrator Role
     * Returns 1 if the user is an administrator.
     *
     * @return int|null
     */
    public function getAdministratorRole()
    {
        return $this->db_administrator;
    }

    protected function emailExists($email)
    {
        //serviceContainer = PDO class
        $db = ServiceContainer::getInstance()->database();

        /* @var $stmt \PDOStatement */  //
        $stmt = $db->query("SELECT `id` FROM `user` WHERE `email_adres` = :email_adres LIMIT 1", array('email_adres' => $email));
        $exists = $stmt->rowCount();
        ($exists > 0 ? true : false);

//        $exists = $stmt->fetch(\PDO::FETCH_ASSOC);
//        ($exists !== null ? true : false);
    }

    /**
     * Login Function
     *
     * @param $emailAddress
     * @param $password
     * @return int
     * @throws \Exception
     */
    public function login($emailAddress, $password)
    {
        //serviceContainer = PDO class
        $db = ServiceContainer::getInstance()->database();

        /* @var $stmt \PDOStatement */
        $stmt = $db->query("SELECT * FROM `users` WHERE `emailAddress` = :emailAddress LIMIT 1",
            array('emailAddress' => $emailAddress));
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($data === false) {
            throw new \Exception('EmailAddress or password incorrect.');
        }
        $this->instantiate($data);

        $hash = $this->getPasswordHash();

        if(password_verify($password, $hash)) {
            return $this->getId();
        }else {
            throw new \Exception('EmailAddress or password incorrect.');
        }
    }

    /**
     * @param \DateTime $date
     * @return User[]
     */
    public static function fromSignUpDate(\DateTime $date)
    {
        //serviceContainer = PDO class
        $db = ServiceContainer::getInstance()->database();

        $users = array();

        /* @var $stmt \PDOStatement */
        $stmt = $db->query('SELECT * FROM `users` WHERE `signUpdate` > ?', array($date->format('d-m-Y')));

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $users[] = new User($row);
        }

        return $users;
    }
}

