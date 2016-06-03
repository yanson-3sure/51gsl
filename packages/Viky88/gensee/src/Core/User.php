<?php
namespace Gensee\Core;

class User
{
    protected $site;
    protected $loginName;
    protected $password;
    protected $sec;

    public function __construct($site,$loginName, $password,$sec)
    {
        $this->site = $site;
        $this->loginName = $loginName;
        $this->password = $password;
        $this->sec = $sec;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function getLoginName()
    {
        return $this->loginName;
    }

    public function getPassword()
    {
        return $this->password;
    }
    public function getSec()
    {
        return $this->sec;
    }

}