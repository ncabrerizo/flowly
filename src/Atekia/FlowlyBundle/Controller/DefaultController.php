<?php

namespace Atekia\FlowlyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $conn = $this->get('database_connection');

        $conn->exec("CREATE TABLE IF NOT EXISTS messages (
                    id INTEGER PRIMARY KEY,
                    title TEXT,
                    message TEXT,
                    time INTEGER)");

        $messages = array(
            array('title' => 'Hello!',
                'message' => 'Just testing...'),
            array('title' => 'Hello again!',
                'message' => 'More testing...'),
            array('title' => 'Hi!',
                'message' => 'SQLite3 is cool...')
        );

        $conn->beginTransaction();

        $stmt = $conn->prepare("INSERT INTO messages (title, message, time) VALUES (:title, :message, :time)");

        //for ($i = 0; $i < 10; $i++)
        foreach ($messages as $m) {

            $time = time();

            $stmt->bindParam(':title', $name);
            $stmt->bindParam(':message', $m['message']);
            $stmt->bindParam(':time', $time);

            $stmt->execute();

        }

        $conn->commit();

        $stmt = $conn->prepare("SELECT * FROM messages");
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($results as &$row) {
            $row['time'] = date('c', $row['time']);
        }

        return $this->render('AtekiaFlowlyBundle:Default:index.html.twig', array('results' => $results));
    }
}
