<?php

use Phalcon\Mvc\Controller;


class IndexController extends Controller
{
    public function createAction()
    {
        // if got post
        if ($this->request->isPost()) {
            echo "<pre>";
            print_r($this->request->getPost());

            $api = new App\Components\ApiComponent();

            $name = $this->request->getPost()['name'];
            $desc = $this->request->getPost()['desc'];
            $ispublic = $this->request->getPost()['public'] == 'on' ? 'true' : 'false';
            $resp = $api->createPlayLists($name, $desc, $ispublic);
            header("location:/index");
        }
    }
    public function loginAction()
    {
    }
    public function addPlaylistAction()
    {
        if (!$this->request->isAjax()) {
            header("location:/auth");
        } else {
            // echo "from add pl action";
            // echo "<pre>";
            $ajaxData = $this->request->getPost();
            // print_r($ajaxData);
            // print_r($ajaxData["itemId"]);
            $api = new App\Components\ApiComponent();
            $data = $api->addToPlayLists($ajaxData["plId"], $ajaxData["itemId"]);
            if (isset($data["snapshot_id"])) {
                echo 200;
            }
            die();
        }
    }
    public function fetchPlaylistAction()
    {
        if (!$this->request->isAjax()) {
            header("location:/auth");
        } else {
            $ajaxData = $this->request->getPost();
            $api = new App\Components\ApiComponent();
            $data = $api->getPlayListsById($ajaxData["plid"]);
            print_r(json_encode($data));
            die();
        }
    }
    public function delteFromPlaylistAction()
    {
        if (!$this->request->isAjax()) {
            header("location:/auth");
        } else {
            $ajaxData = $this->request->getPost();
            $api = new App\Components\ApiComponent();
            $data = $api->removeItemFromPlayListsByPLId($ajaxData["plid"], $ajaxData["iid"]);
            print_r(json_encode($data));
            die();
        }
    }
    public function indexAction()
    {
        if (!$this->session->has("user_email") || !$this->session->has("access_token")) {
            header("location:/auth");
        }
        $this->assets->addJs("js/handleAdd.js");
        $this->assets->addJs("js/util.js");
        $this->assets->addCss("css/styles.css");
        $api = new App\Components\ApiComponent();
        $device = $api->controllPlayer();
        if ($device) {
            $this->view->device = $device;
        }
        $this->view->userPlaylists = $api->getCurrentUserPlayLists();
        $this->view->profile = $api->getCurrentUserDetails();
        $this->view->recomandations = $api->getReccomandations();
        // if got post
        if ($this->request->isPost()) {
            // echo "<pre>";
            // print_r($this->request->getPost());

            $filters = [];
            foreach (array_keys($this->request->getPost()) as $f) {
                if ($f != "search" && $f != "query") {
                    array_push($filters, $f);
                }
            }
            $searchData = $api->search($filters, $this->request->getPost()["query"]);
            $this->view->searchData = $searchData;
            $this->view->search = $this->request->getPost()["query"];
            $this->view->tabs = array_keys($searchData);
        }
    }
}
