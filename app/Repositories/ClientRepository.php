<?php
namespace App\Repositories;

use App\Client;
use Illuminate\Validation\ValidationException;

class ClientRepository
{
    protected $client;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Client $client
    ) {
        $this->client = $client;
    }

    /**
     * Get client query
     *
     * @return Client query
     */
    public function getQuery()
    {
        return $this->client;
    }

    /**
     * Count client
     *
     * @return integer
     */
    public function count()
    {
        return $this->client->count();
    }

    /**
     * List all clients by name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->client->all()->pluck('name', 'id')->all();
    }
    

    /**
     * List all client by name & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->client->all(['name', 'id']);
    }

      /**
     * List all clients by client name & id
     *
     * @return array
     */
    public function listAllFilterById($client_ids)
    {
        return $this->client->whereIn('id', $client_ids)->get()->pluck('name', 'id')->all();
    }

    /**
     * List all clients by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->client->all()->pluck('id')->all();
    }

    /**
     * Get all clients
     *
     * @return array
     */
    public function getAll()
    {
        return $this->client->all();
    }

    /**
     * Find client with given id or throw an error.
     *
     * @param integer $id
     * @return Client
     */
    public function findOrFail($id)
    {
        $client = $this->client->find($id);

        if (! $client) {
            throw ValidationException::withMessages(['message' => trans('client.could_not_find')]);
        }

        return $client;
    }

    /**
     * Paginate all clients using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order       = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $name        = isset($params['name']) ? $params['name'] : '';

        return $this->client->filterByName($name)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new client.
     *
     * @param array $params
     * @return Client
     */
    public function create($params)
    {
        return $this->client->forceCreate($this->formatParams($params));
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $action = 'create')
    {
        $formatted = [
            'name'        => isset($params['name']) ? $params['name'] : null,
            'description' => isset($params['description']) ? $params['description'] : null
        ];

        return $formatted;
    }

    /**
     * Update given client.
     *
     * @param Client $client
     * @param array $params
     *
     * @return Client
     */
    public function update(Client $client, $params)
    {
        $client->forceFill($this->formatParams($params, 'update'))->save();

        return $client;
    }

    /**
     * Find client & check it can be deleted or not.
     *
     * @param integer $id
     * @return Client
     */
    public function deletable($id)
    {
        $client = $this->findOrFail($id);

        if ($client->designations()->count()) {
            throw ValidationException::withMessages(['message' => trans('client.has_many_designations')]);
        }
        
        return $client;
    }

    /**
     * Delete client.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Client $client)
    {
        return $client->delete();
    }

    /**
     * Delete multiple clients.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->client->whereIn('id', $ids)->delete();
    }
}
