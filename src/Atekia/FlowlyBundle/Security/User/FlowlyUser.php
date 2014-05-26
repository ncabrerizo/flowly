<?php

namespace Atekia\FlowlyBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class FlowlyUser implements UserInterface, EquatableInterface
{
    private $id;
    private $username;
    private $password;
    private $realname;
    private $email;
    private $salt;
    private $roles;

    public function __construct($id, $username, $password, $salt, array $roles, $realname = null, $email = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->realname = $realname !== null ? $realname : $username;
        $this->email = $email;
        $this->salt = $salt;
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getRealname()
    {
        return $this->realname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getId()
    {
        return $this->id;
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof FlowlyUser) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}
