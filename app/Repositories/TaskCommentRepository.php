<?php
namespace App\Repositories;

use App\TaskComment;
use App\Repositories\TaskRepository;
use Illuminate\Validation\ValidationException;

class TaskCommentRepository
{
    protected $task_comment;
    protected $task;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        TaskComment $task_comment,
        TaskRepository $task
    ) {
        $this->task_comment = $task_comment;
        $this->task = $task;
    }

    /**
     * Get task comment query
     *
     * @return TaskComment query
     */
    public function getQuery()
    {
        return $this->task_comment;
    }

    /**
     * Count task comment
     *
     * @return integer
     */
    public function count()
    {
        return $this->task_comment->count();
    }

    /**
     * List all task comment by title & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->task_comment->all()->pluck('title', 'id')->all();
    }

    /**
     * List all task comment by title & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->task_comment->all(['title', 'id']);
    }

    /**
     * Get all task comment
     *
     * @return array
     */
    public function getAll($task_id)
    {
        return $this->task_comment->with('user', 'user.profile', 'reply', 'reply.user', 'reply.user.profile')->filterByTaskId($task_id)->whereNull('reply_id')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Find task comment with given id or throw an error.
     *
     * @param integer $id
     * @return TaskComment
     */
    public function findOrFail($task_id, $id)
    {
        $task_comment = $this->task_comment->filterByTaskId($task_id)->filterById($id)->first();

        if (! $task_comment) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_task_comment')]);
        }

        return $task_comment;
    }

    /**
     * Paginate all task categories using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($task_id, $params)
    {
        $sort_by            = isset($params['sort_by']) ? $params['sort_by'] : 'title';
        $order              = isset($params['order']) ? $params['order'] : 'asc';
        $page_length        = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->task_comment->with('userAdded', 'userAdded.profile', 'task')->filterByTaskId($task_id)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new task.
     *
     * @param array $params
     * @return Task
     */
    public function create($task_id, $params)
    {
        return $this->task_comment->forceCreate($this->formatParams($params, $task_id));
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $task_id)
    {
        return [
            'comment' => isset($params['comment']) ? $params['comment'] : null,
            'reply_id' => isset($params['comment_id']) ? $params['comment_id'] : null,
            'task_id' => $task_id,
            'user_id' => \Auth::user()->id
        ];
    }

    /**
     * Delete task comment.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(TaskComment $task_comment)
    {
        return $task_comment->delete();
    }

    /**
     * Delete multiple task comment.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->task_comment->whereIn('id', $ids)->delete();
    }
}
