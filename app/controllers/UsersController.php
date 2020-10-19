<?php
declare(strict_types=1);


use App\Forms\LoginForm;
use App\Forms\RegisterForm;
use App\Library\EnumHolder;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Form;
use Phalcon\Messages\Message;

class UsersController extends ControllerBase
{

    /**
     * @var Users $model
     */
    protected $model;

    public function initialize()
    {
        $this->model = new Users();
    }

    public function registerAction()
    {
        if ($this->getUserLoggedIn()['user_type'] == 'user') {
            $this->response->redirect(ROOT_FOLDER . $this->url->get('user-area'));
            return;
        }


        $register_form = new RegisterForm();

        if ($this->request->getPost('register-btn') && $register_form->isValid($_POST) && $this->security->checkToken()) {

            if ($this->model->existence('username', $this->request->getPost('username'))) {
                $this->model->appendMessage(new Message('Username Already Exists .'));
                $this->view->form = $register_form;
            } else if ($this->model->existence('email', $this->request->getPost('email'))) {
                $this->model->appendMessage(new Message('Email Already Exists .'));
                $this->view->form = $register_form;
            } else {
                $result = $this->model->register($this->request->getPost('username'), $this->security->hash($this->request->getPost('password'), 12), $this->request->getPost('email'),
                    $this->request->getPost('first-name'), $this->request->getPost('last-name'), Date('Y-m-d H:i:s'), EnumHolder::users_access_level['user'],
                    EnumHolder::users_status['pending']);

                if ($result) {
                    $this->setUserLoggedIn($this->request->getPost('username'), 'user');
                    $this->view->pick('users/success');
                }
            }
        } else {
            $this->view->form = $register_form;
        }

        $this->view->errors = $this->model->getMessages();
    }

    public function loginAction()
    {

        if ($this->getUserLoggedIn()['user_type'] == 'user' || $this->getUserLoggedIn()['user_type'] == 'admin') {
            $this->response->redirect(ROOT_FOLDER . $this->url->get('user-area'));
            return;
        }

        $login_form = new LoginForm();

        if ($this->request->getPost('login-btn') && $login_form->isValid($_POST) && $this->security->checkToken()) {

            $user = $this->model::findFirst([
                "username = :user:",
                "bind" => [
                    "user" => $this->request->getPost('username'),
                ]
            ]);

            if ($user && $this->security->checkHash($this->request->getPost('password'), $user->password)) {
                $this->setUserLoggedIn($user->username, $user->access_level);
                $this->response->redirect(ROOT_FOLDER . $this->url->get('user-area'));
            } else {
                $this->model->appendMessage(new Message('wrong username or password'));
                $this->view->form = new LoginForm();
            }

        } else {

            $this->view->form = new LoginForm();
        }

        $this->view->errors = $this->model->getMessages();
    }

    public function setUserLoggedIn($username, $user_type)
    {
        $this->session->set('user_logged_in', [
            'username' => $username,
            'user_type' => $user_type
        ]);

        $this->setAuthenticationKey($username, $this->security->hash($this->security->getSaltBytes()));

        return true;
    }

    public function getUserLoggedIn()
    {
        return $this->session->get('user_logged_in');
    }

    public function userAreaAction()
    {
        $user_area_form = new Form();
        $user_area_form->add(new Submit('logout'));
        $this->view->form = $user_area_form;

        if ($this->request->isPost()) {
            $this->session->destroy();
            $this->cookies->delete('authentication_key');
            $this->response->redirect(ROOT_FOLDER . $this->url->get('/'));
        }

    }

    public function forgetPasswordAction()
    {
        
    }


    public function setAuthenticationKey($username, $key)
    {
        $query_result = $this->model->authenticationKey($username, $key);
        if ($query_result) {
            $this->cookies->useEncryption(false);
            $this->cookies->set('authentication_key', $key, time() + 15 * 864000);
            return true;
        } else {
            return false;
        }
    }

    public function authenticateKeyChecker($authentication_key)
    {
        $user = $this->model->authenticateKeyChecker($authentication_key);
        if ($user) {
            return $this->setUserLoggedIn($user->username, $user->access_level);
        } else {
            return false;
        }
    }
}