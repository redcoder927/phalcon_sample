<?php
declare(strict_types=1);

namespace App\Forms;

use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\Alnum;
use Phalcon\Validation\Validator\PresenceOf;

class LoginForm extends \Phalcon\Forms\Form
{
    public function initialize()
    {
        $username = new Text('username', [
            'placeholder' => 'username',
            'maxLength' => 20
        ]);

        $password = new Password('password', [
            'placeholder' => 'password',
            'maxLength' => 35
        ]);


        $login_btn = new Submit('login-btn', [
            'value' => 'login',
            'name' => 'login-btn'
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


        $this->add($username);
        $this->add($password);
        $this->add($login_btn);

    }

}