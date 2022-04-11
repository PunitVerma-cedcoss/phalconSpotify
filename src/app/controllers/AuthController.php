<?php

use Phalcon\Mvc\Controller;


class AuthController extends Controller
{
    public function indexAction()
    {
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
        $api->getAccessToken($getData["code"]);
        header("location:/index");
    }
    public function logoutAction()
    {
        $this->session->destroy();
        header("location:/auth");
    }
}
