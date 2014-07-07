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

}
