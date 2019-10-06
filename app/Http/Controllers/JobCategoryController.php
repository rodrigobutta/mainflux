<?php

namespace App\Http\Controllers;

use App\JobCategory;
use Illuminate\Http\Request;
use App\Http\Requests\JobCategoryRequest;
use App\Repositories\JobCategoryRepository;
use App\Repositories\ActivityLogRepository;

class JobCategoryController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'job_category';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        JobCategoryRepository $repo,
        ActivityLogRepository $activity
    ) {
        $this->request  = $request;
        $this->repo     = $repo;
        $this->activity = $activity;
        
        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get all Job Categories
     * @get ("/api/job-category")
     * @return Response
     */
    public function index()
    {
        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store Job Category
     * @post ("/api/job-category")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Job Category"),
     *      @Parameter("description", type="text", required="optional", description="Description of Job Category"),
     * })
     * @return Response
     */
    public function store(JobCategoryRequest $request)
    {
        $job_category = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $job_category->id,
            'activity'  => 'added'
        ]);

        return $this->success(['message' => trans('job.job_category_added')]);
    }

    /**
     * Used to get Job Category detail
     * @get ("/api/job-category/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Job Category"),
     * })
     * @return Response
     */
    public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update Job Category
     * @patch ("/api/job-category/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Job Category"),
     *      @Parameter("name", type="string", required="true", description="Name of Job Category"),
     *      @Parameter("description", type="text", required="optional", description="Description of Job Category")
     * })
     * @return Response
     */
    public function update(JobCategoryRequest $request, $id)
    {
        $job_category = $this->repo->findOrFail($id);
        
        $job_category = $this->repo->update($job_category, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $job_category->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('job.job_category_updated')]);
    }

    /**
     * Used to delete Job Category
     * @delete ("/api/job-category/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Job Category"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $job_category = $this->repo->deletable($id);

        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => $job_category->id,
            'sub_module' => $job_category->name,
            'activity'   => 'deleted'
        ]);

        $this->repo->delete($job_category);

        return $this->success(['message' => trans('job.job_category_deleted')]);
    }
}
