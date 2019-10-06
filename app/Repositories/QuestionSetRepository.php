<?php
namespace App\Repositories;

use App\Question;
use App\QuestionSet;
use Illuminate\Validation\ValidationException;

class QuestionSetRepository
{
    protected $question_set;
    protected $question;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(QuestionSet $question_set, Question $question)
    {
        $this->question_set = $question_set;
        $this->question = $question;
    }

    /**
     * Get all question sets
     *
     * @return QuestionSet
     */

    public function getAll()
    {
        return $this->question_set->all();
    }

    /**
     * List all question sets by name & id
     *
     * @return QuestionSet
     */

    public function list()
    {
        return $this->question_set->all()->pluck('name', 'id')->all();
    }

    /**
     * List all question sets by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->question_set->all()->pluck('id')->all();
    }

    /**
     * List all question sets by name & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->question_set->all(['name', 'id']);
    }

    /**
     * Find question set with given id or throw an error.
     *
     * @param integer $id
     * @return QuestionSet
     */

    public function findOrFail($id)
    {
        $question_set = $this->question_set->with('questions')->find($id);

        if (! $question_set) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_question_set')]);
        }

        return $question_set;
    }

    /**
     * Paginate all question sets using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */

    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'name';
        $order       = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->question_set->with('questions')->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new question set.
     *
     * @param array $params
     * @return QuestionSet
     */
    public function create($params)
    {
        $this->validateInput($params);

        $name        = isset($params['name']) ? $params['name'] : null;
        $description = isset($params['description']) ? $params['description'] : null;

        $question_set = $this->question_set->forceCreate([
            'name' => $name,
            'description' => $description
        ]);

        $questions = isset($params['questions']) ? $params['questions'] : [];

        foreach($questions as $question){
            $name = isset($question['question']) ? $question['question'] : null;

            $this->question->forceCreate([
                'question_set_id' => $question_set->id,
                'question' => $name
            ]);
        }

        return $question_set;
    }

    public function validateInput($params)
    {
        $questions = isset($params['questions']) ? $params['questions'] : [];

        if (! $questions) {
            throw ValidationException::withMessages(['message' => trans('validation.required',['attribute' => trans('job.question')])]);
        }

        foreach ($questions as $index => $question) {
            $name = isset($question['question']) ? $question['question'] : null;

            if (! $name) {
                throw ValidationException::withMessages(['question_'.$index => trans('validation.required',['attribute' => trans('job.question')])]);
            }
        }
    }

    /**
     * Find question set & check it can be deleted or not.
     *
     * @param integer $id
     * @return QuestSet
     */
    public function deletable($id)
    {
        $question_set = $this->findOrFail($id);

        if ($question_set->Jobs->count()) {
            throw ValidationException::withMessages(['message' => trans('job.question_set_has_many_jobs')]);
        }
        
        return $question_set;
    }

    /**
     * Delete question set.
     *
     * @param integer $id
     * @return bool|null
     */

    public function delete(QuestionSet $question_set)
    {
        return $question_set->delete();
    }

    /**
     * Delete multiple question sets.
     *
     * @param array $ids
     * @return bool|null
     */

    public function deleteMultiple($ids = array())
    {
        return $this->question_set->whereIn('id', $ids)->delete();
    }
}
