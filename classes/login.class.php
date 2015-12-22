<?php

namespace MyPoll\Classes;

use RedBeanPHP\Facade;

/**
 * Class Login
 * @package MyPoll\Classes
 */
class Login
{

    /** @var string */
    private $authMethod = 'Session'; // Session or Cookie

    /** @var string */
    private $logPage = "index.php?do=questions";

    /** @var string */
    private $indexPage = 'index.php';

    /** @var  string */
    private $userName;

    /** @var  int */
    private $userID;

    /** @var  string */
    private $email;

    /**
     * @return string
     */
    public function getIndexPage()
    {
        return $this->indexPage;
    }

    /**
     * @return string
     */
    public function getLogPage()
    {
        return $this->logPage;
    }
    /**
     * @param string $user
     * @param string $pass
     */
    public function check($user, $pass)
    {
        if (isset($user) && !empty($user) && isset($pass) && !empty($pass)) {
            $result = Facade::getRow("SELECT * FROM users WHERE
            user_name = :user AND user_pass = :pass", [':user' => $user, ':pass' => md5($pass)]);
            if (!empty($result)) {
                $this->userName = $result['user_name'];
                $this->userID = $result['id'];
                $this->email = $result['email'];
                $this->authLogin();
            } else {
                echo General::messageSent('Wrong Username or Password', $this->indexPage);
            }
        }
    }

    /**
     * @return boolean
     */
    public function isLoggedIn()
    {
        if (isset($_SESSION['user']) && !empty($_SESSION['user']) && isset($_SESSION['id'])
            && !empty($_SESSION['id'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return void
     */
    private function authLogin()
    {
        if ($this->authMethod == 'Session') {
            $_SESSION['user'] = $this->userName;
            $_SESSION['id'] = $this->userID;
            $_SESSION['email'] = $this->email;
            echo General::Ref($this->logPage);
        }
    }

    /**
     * @return void
     */
    public function logout()
    {
        session_destroy();
    }
}
