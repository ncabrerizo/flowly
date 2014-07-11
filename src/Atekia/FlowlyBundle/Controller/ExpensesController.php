<?php

namespace Atekia\FlowlyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\SecurityContext;

class ExpensesController extends Controller
{

    public function manageprovidersAction($active = true) {

        $dbh = $this->get('atekia_flowly_database_helper');

        $dbh->initializeTable('providers');

        $conn = $dbh->getConnection();

        $stmt = $conn->prepare("
            SELECT
                id,
                taxId,
                regName,
                tradeName,
                address,
                postalCode,
                city,
                province,
                country,
                telephone,
                fax,
                mobile,
                contactPerson,
                email,
                iban,
                swift
            FROM
                providers;
        ");

        $stmt->execute();

        $providers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('AtekiaFlowlyBundle:Expenses:manageproviders.html.twig',
            [
                'providers' => $providers
            ]);

    }

    public function adduserAction() {

        if ($this->get('request')->getMethod() == "POST") {

            $dbh = $this->get('atekia_flowly_database_helper');

            $data = $this->get('request')->request->all();

            $result = null;

            $dbh->initializeTable('users');

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
                    ) VALUES (
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
                $stmt->bindValue('password', base64_encode(hash('sha512', $data['password'], 1)));
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

            return $this->render('AtekiaFlowlyBundle:Expenses:addprovider.html.twig',
                [
                    'data' => $data,
                    'result' => $result
                ]);

        } else {

            return $this->render('AtekiaFlowlyBundle:Expenses:addprovider.html.twig');

        }

    }

}
