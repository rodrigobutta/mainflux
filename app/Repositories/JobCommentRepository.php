<?php
namespace App\Repositories;

use App\JobComment;
use App\Repositories\JobRepository;
use Illuminate\Validation\ValidationException;

class JobCommentRepository
{
    protected $job_comment;
    protected $job;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        JobComment $job_comment,
        JobRepository $job
    ) {
        $this->job_comment = $job_comment;
        $this->job = $job;
    }

    /**
     * Get job comment query
     *
     * @return JobComment query
     */
    public function getQuery()
    {
        return $this->job_comment;
    }

    /**
     * Count job comment
     *
     * @return integer
     */
    public function count()
    {
        return $this->job_comment->count();
    }

    /**
     * List all job comment by title & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->job_comment->all()->pluck('title', 'id')->all();
    }

    /**
     * List all job comment by title & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->job_comment->all(['title', 'id']);
    }

    /**
     * Get all job comment
     *
     * @return array
     */
    public function getAll($job_id)
    {
        return $this->job_comment->with('user', 'user.profile', 'reply', 'reply.user', 'reply.user.profile')->filterByJobId($job_id)->whereNull('reply_id')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Find job comment with given id or throw an error.
     *
     * @param integer $id
     * @return JobComment
     */
    public function findOrFail($job_id, $id)
    {
        $job_comment = $this->job_comment->filterByJobId($job_id)->filterById($id)->first();

        if (! $job_comment) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_job_comment')]);
        }

        return $job_comment;
    }

    /**
     * Paginate all job categories using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($job_id, $params)
    {
        $sort_by            = isset($params['sort_by']) ? $params['sort_by'] : 'title';
        $order              = isset($params['order']) ? $params['order'] : 'asc';
        $page_length        = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->job_comment->with('userAdded', 'userAdded.profile', 'job')->filterByJobId($job_id)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new job.
     *
     * @param array $params
     * @return Job
     */
    public function create($job_id, $params)
    {
        return $this->job_comment->forceCreate($this->formatParams($params, $job_id));
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $job_id)
    {
        return [
            'comment' => isset($params['comment']) ? $params['comment'] : null,
            'reply_id' => isset($params['comment_id']) ? $params['comment_id'] : null,
            'job_id' => $job_id,
            'user_id' => \Auth::user()->id
        ];
    }

    /**
     * Delete job comment.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(JobComment $job_comment)
    {
        return $job_comment->delete();
    }

    /**
     * Delete multiple job comment.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->job_comment->whereIn('id', $ids)->delete();
    }
}
