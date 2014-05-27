<?php

namespace Atekia\FlowlyBundle\Security\User;

use Atekia\FlowlyBundle\Helper\DatabaseHelper;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class FlowlyUserProvider implements UserProviderInterface
{
    private $dbh;

    public function __construct(DatabaseHelper $dbh)
    {
        $this->dbh = $dbh;
        $dbh->initializeTable('user');
    }

    public function loadUserByUsername($username)
    {

        $conn = $this->dbh->getConnection();

        $stmt = $conn->prepare("
            SELECT
                id,
                username,
                realname,
                email,
                password,
                role
            FROM
                users
            WHERE
                active = 1 AND username = :username;
        ");

        $stmt->bindValue('username', $username);

        $stmt->execute();

        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $user = false;

        if (count($users) == 1) $user = $users[0];

        if ($user) {

            return new FlowlyUser(
                $user['id'],
                $user['username'],
                $user['password'],
                null,
                $user['role'] != null ? [$user['role']] : ['ROLE_USER'],
                $user['realname'],
                $user['email']);

        } else {

            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $username)
            );

        }

    }

    public function refreshUser(UserInterface $user)
    {

        if (!$user instanceof FlowlyUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());

    }

    public function supportsClass($class)
    {
        return $class === 'Atekia\FlowlyBundle\Security\User\FlowlyUser';
    }
}