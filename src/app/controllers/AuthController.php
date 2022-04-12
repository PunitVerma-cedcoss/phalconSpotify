<?php

use Phalcon\Mvc\Controller;


class AuthController extends Controller
{
    public function indexAction()
    {
        // if user is not logged in
        if (!$this->session->has("user_email")) {
            header("location:/auth/login");
        }
        // if already connected then go to index
        if ($this->session->has("access_token")) {
            header("location:/index");
        }
        // prepare the auth url
        $authUrl = "https://accounts.spotify.com/authorize?response_type=code&client_id=" . $this->CLIENT_ID . "&state=" . $this->STATE . "&scope=" . $this->SCOPE . "&redirect_uri=" . $this->REDIRECT_URI;

        // pass it to the view
        $this->view->link = $authUrl;
    }
    public function errorAction()
    {
    }
    public function connectBackAction()
    {
        if (!isset($this->request->getQuery()["code"])) {
            header("location:/auth");
        }
        $getData = $this->request->getQuery();
        // if error occured
        if (isset($getData["error"])) {
            die("some error occured");
        }
        // else try to get access token
        $api = new App\Components\ApiComponent();
        // save the token in db
        $api->getAccessToken($getData["code"]);
        $users = new Users();
        $users2 = $users::findFirst(
            [
                'conditions' => 'email = :email:',
                'bind' => [
                    'email' => $this->session->get("user_email")
                ]
            ]
        );
        $users2->assign(
            [
                "token" => $this->session->get("access_token"),
            ]
        );
        $users->save();
        header("location:/index");
    }
    public function RegisterAction()
    {
        if ($this->session->has("user_email")) {
            header("location:/auth");
        }
        // if got post
        if ($this->request->isPost()) {
            $postData = $this->request->getPost();
            echo "<pre>";
            print_r($postData);
            $users = new Users();
            $users->assign(
                [
                    "email" => $postData["email"],
                    "password" => $postData["password"],
                ]
            );
            if ($users->save()) {
                header("location:/auth");
            } else {
                die("some error occured");
            }
        }
    }
    public function logoutAction()
    {
        $this->session->destroy();
        header("location:/auth");
    }
    public function LoginAction()
    {
        if ($this->session->has("user_email")) {
            header("location:/auth");
        }
        // if got post
        if ($this->request->isPost()) {
            $postData = $this->request->getPost();
            echo "<pre>";
            print_r($postData);
            $users = new Users();
            $data = $users::findFirst(
                [
                    "conditions" => "email = :email: AND password = :password:",
                    "bind" => [
                        "email" => $postData["email"],
                        "password" => $postData["password"],
                    ]
                ]
            );
            echo "<pre>";
            if ($data) {
                if ($data->email) {
                    // set the session
                    $this->session->set("user_email", $postData["email"]);
                    // if token is present then auto connect to spotify
                    if ($data->refresh_token) {
                        // fetch data from db

                        $users = new Users();
                        $users = $users::findFirst(
                            [
                                'conditions' => 'email = :email:',
                                'bind' => [
                                    'email' => $this->session->get("user_email")
                                ]
                            ]
                        );

                        $this->session->set("access_token", $users->token);
                        $this->session->set("expires_in", $users->exp);
                        $this->session->set("refresh_token", $users->refresh_token);
                        $this->session->set("scope", $users->scope);
                    }
                    header("location:/auth");
                } else {
                    die("some error occured");
                    // set the session
                    // $this->session->set("user_email", $postData["email"]);
                    // header("location:/auth");
                }
            } else {
                die("invalid creds");
            }
        }
    }
}
