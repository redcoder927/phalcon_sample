<?php


namespace App\Forms;


use Phalcon\Forms\Element\Email;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\Alnum;
use Phalcon\Validation\Validator\Alpha;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\PresenceOf;

class RegisterForm extends Form
{
    public function initialize()
    {
        $username = new Text('username', [
            'placeholder' => 'username',
            'minLength' => 3,
            'maxLength' => 25,
            'required' => 'true'
        ]);

        $password = new Password('password', [
            'placeholder'=> 'password',
            'minLength' => 6,
            'maxLength' => 35,
            'required' => 'true'
        ]);

        $repeat_password = new Password('repeat-password', [
            'placeholder'=> 'repeat password',
            'minLength' => 6,
            'maxLength' => 35,
            'required' => 'true'
        ]);

        $email = new Email('email', [
            'placeholder' => 'mail@example.com',
            'maxLength' => 255,
            'required' => 'true'
        ]);

        $first_name = new Text('first-name', [
            'placeholder' => 'first name',
            'maxLength' => 25
        ]);

        $last_name = new Text('last-name', [
            'placeholder' => 'last name',
            'maxLength' => 35
        ]);

        $register_btn = new Submit('register-btn', [
            'value' => 'register',
            'name' => 'register-btn'
        ]);

        $username->addValidator(new PresenceOf([
            'message' => 'Fill username field'
        ]))->addValidator(new Alnum([
            'message' => 'username is invalid'
        ]));

        $password->addValidator(new PresenceOf([
            'message' => 'Fill password field'
        ]))->addValidator(new Alnum([
            'message' => 'password is invalid'
        ]));

        $repeat_password->addValidator(new PresenceOf([
            'message' => 'Fill repeat password field'
        ]))->addValidator(new Confirmation([
            "message" => "Password doesn't match confirmation",
            "with"    => "password",
        ]))->addValidator(new Alnum([
            'message' => 'password is invalid'
        ]));

        $email->addValidator(new PresenceOf([
            'message' => 'Fill email field'
        ]))->addValidator(new \Phalcon\Validation\Validator\Email([
            'message' => 'email is not valid'
        ]));

        $first_name->addValidator(new Alpha([
            'message' => 'first name is invalid'
        ]));

        $last_name->addValidator(new Alpha([
            'message' => 'last name is invalid'
        ]));


        $this->add($username)->add($password)->add($repeat_password)->add($email)->add($first_name)->add($last_name)->add($register_btn);
    }

}