<?php
namespace App\Repositories;

use App\Contractor;
use Illuminate\Validation\ValidationException;

class ContractorRepository
{
    protected $contractor;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Contractor $contractor
    ) {
        $this->contractor = $contractor;
    }

    /**
     * Get contractor query
     *
     * @return Contractor query
     */
    public function getQuery()
    {
        return $this->contractor;
    }

    /**
     * Count contractor
     *
     * @return integer
     */
    public function count()
    {
        return $this->contractor->count();
    }

    /**
     * List all contractors by name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->contractor->all()->pluck('name', 'id')->all();
    }

    /**
     * List all contractors by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->contractor->all()->pluck('id')->all();
    }

    /**
     * Get all contractors
     *
     * @return array
     */
    public function getAll()
    {
        return $this->contractor->all();
    }

    /**
     * Find contractor with given id or throw an error.
     *
     * @param integer $id
     * @return Contractor
     */
    public function findOrFail($id)
    {
        $contractor = $this->contractor->find($id);

        if (! $contractor) {
            throw ValidationException::withMessages(['message' => trans('contractor.could_not_find')]);
        }

        return $contractor;
    }

    /**
     * Paginate all contractors using given params.
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

        return $this->contractor->filterByName($name)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new contractor.
     *
     * @param array $params
     * @return Contractor
     */
    public function create($params)
    {
        return $this->contractor->forceCreate($this->formatParams($params));
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
     * Update given contractor.
     *
     * @param Contractor $contractor
     * @param array $params
     *
     * @return Contractor
     */
    public function update(Contractor $contractor, $params)
    {
        $contractor->forceFill($this->formatParams($params, 'update'))->save();

        return $contractor;
    }

    /**
     * Find contractor & check it can be deleted or not.
     *
     * @param integer $id
     * @return Contractor
     */
    public function deletable($id)
    {
        $contractor = $this->findOrFail($id);

        if ($contractor->designations()->count()) {
            throw ValidationException::withMessages(['message' => trans('contractor.has_many_designations')]);
        }
        
        return $contractor;
    }

    /**
     * Delete contractor.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Contractor $contractor)
    {
        return $contractor->delete();
    }

    /**
     * Delete multiple contractors.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->contractor->whereIn('id', $ids)->delete();
    }
}
