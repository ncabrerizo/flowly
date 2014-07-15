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

    public function addproviderAction() {

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
                    providers
                WHERE
                    taxId = :taxId
                    AND country = :country;
                ;
            ");

            $stmt->bindValue('taxId', $data['taxId']);
            $stmt->bindValue('country', $data['country']);

            $stmt->execute();

            $result = $stmt->fetchColumn();

            if ($data['taxId'] != null && $result && $result[0] > 0) {

                $result = 'error_taxidinuse';

            } else {

                $stmt = $conn->prepare("
                    INSERT INTO providers (
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
                    ) VALUES (
                        :taxId,
                        :regName,
                        :tradeName,
                        :address,
                        :postalCode,
                        :city,
                        :province,
                        :country,
                        :telephone,
                        :fax,
                        :mobile,
                        :contactPerson,
                        :email,
                        :iban,
                        :swift
                    );
                ");

                $stmt->bindValue('taxId', $data['taxId']);
                $stmt->bindValue('regName', $data['regName']);
                $stmt->bindValue('tradeName', $data['tradeName']);
                $stmt->bindValue('address', $data['address']);
                $stmt->bindValue('postalCode', $data['postalCode']);
                $stmt->bindValue('city', $data['city']);
                $stmt->bindValue('province', $data['province']);
                $stmt->bindValue('country', $data['country']);
                $stmt->bindValue('telephone', $data['telephone']);
                $stmt->bindValue('fax', $data['fax']);
                $stmt->bindValue('mobile', $data['mobile']);
                $stmt->bindValue('contactPerson', $data['contactPerson']);
                $stmt->bindValue('email', $data['email']);
                $stmt->bindValue('iban', $data['iban']);
                $stmt->bindValue('swift', $data['swift']);

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

    public function editproviderAction($id) {

        if ($this->get('request')->getMethod() == "POST") {

            $dbh = $this->get('atekia_flowly_database_helper');

            $data = $this->get('request')->request->all();

            $result = null;

            $dbh->initializeTable('providers');

            $conn = $dbh->getConnection();

            $stmt = $conn->prepare("
                SELECT
                    count(id)
                FROM
                    providers
                WHERE
                    taxId = :taxId
                    AND country = :country
                    AND id != :id;                ;
            ");

            $stmt->bindValue('id', $id);
            $stmt->bindValue('taxId', $data['taxId']);
            $stmt->bindValue('country', $data['country']);

            $stmt->execute();

            $result = $stmt->fetchColumn();

            if ($data['taxId'] != null && $result && $result[0] > 0) {

                $result = 'error_taxidinuse';

            } else {

                $stmt = $conn->prepare("
                    UPDATE providers SET
                        taxId = :taxId,
                        regName = :regName,
                        tradeName = :tradeName,
                        address = :address,
                        postalCode = :postalCode,
                        city = :city,
                        province = :province,
                        country = :country,
                        telephone = :telephone,
                        fax = :fax,
                        mobile = :mobile,
                        contactPerson = :contactPerson,
                        email = :email,
                        iban = :iban,
                        swift = :swift
                    WHERE
                        id = :id;
                ");

                $stmt->bindValue('id', $id);
                $stmt->bindValue('taxId', $data['taxId']);
                $stmt->bindValue('regName', $data['regName']);
                $stmt->bindValue('tradeName', $data['tradeName']);
                $stmt->bindValue('address', $data['address']);
                $stmt->bindValue('postalCode', $data['postalCode']);
                $stmt->bindValue('city', $data['city']);
                $stmt->bindValue('province', $data['province']);
                $stmt->bindValue('country', $data['country']);
                $stmt->bindValue('telephone', $data['telephone']);
                $stmt->bindValue('fax', $data['fax']);
                $stmt->bindValue('mobile', $data['mobile']);
                $stmt->bindValue('contactPerson', $data['contactPerson']);
                $stmt->bindValue('email', $data['email']);
                $stmt->bindValue('iban', $data['iban']);
                $stmt->bindValue('swift', $data['swift']);

                $stmt->execute();

                if ($stmt->rowCount() > 0) {

                    $result = 'ok';

                } else {

                    $result = 'error';

                }

            }

            return $this->render('AtekiaFlowlyBundle:Expenses:editprovider.html.twig',
                [
                    'data' => $data,
                    'result' => $result
                ]);

        } else {

            $dbh = $this->get('atekia_flowly_database_helper');

            $dbh->initializeTable('providers');

            $conn = $dbh->getConnection();

            $stmt = $conn->prepare("
                SELECT
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
                    providers
                WHERE
                    id = :id;
            ");

            $stmt->bindValue('id', $id);

            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (is_array($result) && count($result) > 0)
                $data = $result[0];
            else
                throw $this->createNotFoundException();

            return $this->render('AtekiaFlowlyBundle:Expenses:editprovider.html.twig',
                [
                    'data' => $data
                ]);

        }

    }

    public function removeproviderAction($id) {

        $dbh = $this->get('atekia_flowly_database_helper');

        $dbh->initializeTable('providers');

        $conn = $dbh->getConnection();

        $stmt = $conn->prepare("
                DELETE
                FROM
                    providers
                WHERE
                    id = :id;
            ");

        $stmt->bindValue('id', $id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $result = 'remok';

        } else {

            $result = 'remerror';

        }

        return $this->redirect(
            $this->generateUrl("atekia_flowly_expenses_manageproviders", [ 'result' => $result ])
        );

    }

}
