<?php
namespace Gensee\Core;

use Gensee\Core\User;

abstract class AbstractAPI
{
    protected $user;
    public function __construct(User $user)
    {
        $this->setUser($user);
    }
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function getWebcastUrl($suffix,$sec=true)
    {
        $password = $sec ? md5($this->user->getPassword()) : $this->user->getPassword();
        $prefix = $this->user->getSite().'integration/site';
        $sec = $sec ? 'true' : 'false';
        return $prefix.$suffix.'?loginName='.$this->user->getLoginName().'&password='.$password.'&sec='.$sec;
    }
    public function getUserData()
    {
        $sec = $this->user->getSec();
        $password = $sec ? md5($this->user->getPassword()) : $this->user->getPassword();
        $sec = $sec ? 'true' : 'false';
        return [
            'loginName'=>$this->user->getLoginName(),
            'password' =>$password,
            'sec'      =>$sec
        ];
    }
}