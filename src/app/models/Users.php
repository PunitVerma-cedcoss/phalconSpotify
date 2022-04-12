<?php

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $id;
    public $email;
    public $password;
    public $token;
    public $exp;
    public $refresh_token;
    public $scope;
}
