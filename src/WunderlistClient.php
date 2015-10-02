<?php

namespace Wunderlist;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;

class WunderlistClient
{
    /**
     * @var Client
     */
    private $guzzle;

    /**
     * Constructor
     *
     * @param Client $guzzle
     */
    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * Check the response status code.
     * 
     * @param ResponseInterface $response           The response from the guzzle HTTP call
     * @param int               $expectedStatusCode The expected status code
     * 
     * @throws \RuntimeException On unexpected status code
     */
    private function checkResponseStatusCode(ResponseInterface $response, $expectedStatusCode)
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode !== $expectedStatusCode) {
            throw new \RuntimeException('Wunderlist API returned status code ' . $statusCode . ' expected ' . $expectedStatusCode);
        }
    }

    /**
     * Returns all the lists
     *
     * @return array
     */
    public function getLists()
    {
        $response = $this->guzzle->get('lists');

        $this->checkResponseStatusCode($response, 200);

        return json_decode($response->getBody(), true);
    }

    /**
     * Returns a specific list
     *
     * @param int $listId The list id
     *
     * @return array
     */
    public function getList($listId)
    {
        if (!is_numeric($listId)) {
            throw new \InvalidArgumentException('The list id must be numeric');
        }

        $response = $this->guzzle->get('lists/' . $listId);

        $this->checkResponseStatusCode($response, 200);

        return json_decode($response->getBody(), true);
    }

    /**
     * Return all the tasks of a given list
     *
     * @param int $listId The list id
     *
     * @return array
     */
    public function getListTasks($listId)
    {
        if (!is_numeric($listId)) {
            throw new \InvalidArgumentException('The list id must be numeric');
        }

        $response = $this->guzzle->get('tasks', ['query' => ['list_id' => $listId]]);

        $this->checkResponseStatusCode($response, 200);

        return json_decode($response->getBody(), true);
    }

    /**
     * Creates a new task
     *
     * See: https://developer.wunderlist.com/documentation/endpoints/task
     *
     * @param string $name       The name of the task
     * @param int    $listId     The id of the list to add the task to
     * @param array  $parameters Other task parameters
     * 
     * @return array
     */
    public function createTask($name, $listId, array $parameters)
    {
        if (!is_numeric($listId)) {
            throw new \InvalidArgumentException('The list id must be numeric');
        }

        $parameters['name']   = $name;
        $parameters['listId'] = $listId;

        $response = $this->guzzle->post('tasks', ['body' => json_encode($parameters)]);

        $this->checkResponseStatusCode($response, 201);

        return json_decode($response->getBody(), true);
    }

    /**
     * Completes a task
     *
     * @param int $taskId
     * @param int $revision
     * 
     * @return array
     */
    public function completeTask($taskId, $revision)
    {
        if (!is_numeric($taskId)) {
            throw new \InvalidArgumentException('The list id must be numeric');
        } elseif (!is_numeric($revision)) {
            throw new \InvalidArgumentException('The revision must be numeric');
        }

        $response = $this->guzzle->patch('tasks/' . $taskId, ['body' => json_encode(['revision' => (int) $revision, 'completed' => true])]);

        $this->checkResponseStatusCode($response, 200);

        return json_decode($response->getBody(), true);
    }
}
