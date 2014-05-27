<?php

namespace Atekia\FlowlyBundle\Controller;

use Atekia\FlowlyBundle\Helper\DatabaseHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    public function loginAction()
    {

        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('AtekiaFlowlyBundle:Security:login.html.twig', array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));

    }

    public function manageusersAction($active = true) {

        $dbh = $this->get('atekia_flowly_database_helper');

        $dbh->initializeTable('user');

        $conn = $dbh->getConnection();

        $stmt = $conn->prepare("
            SELECT
                id,
                username,
                realname,
                email,
                password,
                role,
                active
            FROM
                users
            WHERE
                active = :active;
        ");

        $stmt->bindValue('active', $active ? 1 : 0);

        $stmt->execute();

        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('AtekiaFlowlyBundle:Security:manageusers.html.twig', array('users' => $users));

    }

    public function enableAction($active, $user) {

        if ($user == $usr= $this->get('security.context')->getToken()->getUser()->getId())
            throw new BadRequestHttpException('Currently logged in user cannot be enabled or disabled.');

        $dbh = $this->get('atekia_flowly_database_helper');

        $dbh->initializeTable('user');

        $conn = $dbh->getConnection();

        $stmt = $conn->prepare("
            UPDATE
                users
            SET
                active = :active
            WHERE
                id = :id;
        ");

        $stmt->bindValue('active', $active ? 1 : 0);
        $stmt->bindValue('id', $user);

        $stmt->execute();

        if ($stmt->rowCount() == 0) throw $this->createException();

        return $this->redirect(
            $active ? $this->generateUrl('atekia_flowly_admin_manageusers_disabled') :
                $this->generateUrl('atekia_flowly_admin_manageusers')
        );

    }

    public function edituserAction() {

        throw $this->createNotFoundException();

        return $this->render('AtekiaFlowlyBundle:Security:manageusers.html.twig', array('users' => []));

    }

    public function adduserAction() {

        if ($this->get('request')->getMethod() == "POST") {

            $dbh = $this->get('atekia_flowly_database_helper');

            $data = $this->get('request')->request->all();

            $result = null;

            $dbh->initializeTable('user');

            $conn = $dbh->getConnection();

            $stmt = $conn->prepare("
                SELECT
                    count(id)
                FROM
                    users
                WHERE
                    username = :username;
            ");

            $stmt->bindValue('username', $data['username']);

            $stmt->execute();

            $result = $stmt->fetchColumn();

            if ($result && $result[0] > 0) {

                $result = 'error_username';

            } else {

                $stmt = $conn->prepare("
                    INSERT INTO users (
                        username,
                        realname,
                        password,
                        email,
                        role,
                        active
                    )
                    VALUES (
                        :username,
                        :realname,
                        :password,
                        :email,
                        :role,
                        :active
                    );
                ");

                $stmt->bindValue('username', $data['username']);
                $stmt->bindValue('realname', $data['realname']);
                $stmt->bindValue('password', base64_encode(hash('sha512', $data['password'],1)));
                $stmt->bindValue('email', $data['email']);
                $stmt->bindValue('role', isset($data['admin']) ? 'ROLE_ADMIN' : 'ROLE_USER');
                $stmt->bindValue('active', 1);

                $stmt->execute();

                if ($stmt->rowCount() > 0) {

                    $data = null;
                    $result = 'ok';

                } else {

                    $result = 'error';

                }

            }

            return $this->render('AtekiaFlowlyBundle:Security:adduser.html.twig',
                [
                    'data' => $data,
                    'result' => $result
                ]);

        } else {

            return $this->render('AtekiaFlowlyBundle:Security:adduser.html.twig');

        }

    }

}
