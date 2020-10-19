<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;

class Users extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $username;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $first_name;

    /**
     *
     * @var string
     */
    public $last_name;

    /**
     *
     * @var string
     */
    public $access_level;

    /**
     *
     * @var string
     */
    public $status;

    /**
     *
     * @var string
     */
    public $authentication_key;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'email',
            new EmailValidator(
                [
                    'model' => $this,
                    'message' => 'Please enter a correct email address',
                ]
            )
        );

        return $this->validate($validator);
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("phalcon");
        $this->setSource("users");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users[]|Users|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function reset()
    {
        // TODO: Implement reset() method.
    }

    public function existence($field, $parameter)
    {
        if ($this->find(['conditions' => $field . ' = :' . $field . ':', 'bind' => [$field => $parameter]])->getFirst()) {
            return true;
        } else {
            return false;
        }
    }

    public function register($username, $password, $email, $first_name, $last_name, $register_date, $access_level, $status)
    {
        $this->assign([
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'register_date' => $register_date,
            'access_level' => $access_level,
            'status' => $status
        ]);


        return $this->save();
    }

    public function authenticationKey($username, $key)
    {
        $result = self::findFirst([
            'conditions' => 'username = :username:',
            'bind' => ['username' => $username]]);

        $result->assign([
            'authentication_key' => $key
        ]);

        return $result->save();
    }

    public function authenticateKeyChecker($authentication_key)
    {
        return self::findFirst([
            'conditions' => 'authentication_key = :authentication_key:',
            'bind' => [
                'authentication_key' => $authentication_key
            ]
        ]);
    }
}
