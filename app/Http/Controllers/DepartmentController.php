<?php

namespace App\Http\Controllers;

use App\Department;
use Illuminate\Http\Request;
use App\Http\Requests\DepartmentRequest;
use App\Repositories\DepartmentRepository;
use App\Repositories\ActivityLogRepository;

class DepartmentController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'department';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        DepartmentRepository $repo,
        ActivityLogRepository $activity
    ) {
        $this->request  = $request;
        $this->repo     = $repo;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get all Departments
     * @get ("/api/department")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', Department::class);

        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store Department
     * @post ("/api/department")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Department"),
     *      @Parameter("description", type="text", required="optional", description="Department description")
     * })
     * @return Response
     */
    public function store(DepartmentRequest $request)
    {
        $this->authorize('create', Department::class);

        $department = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $department->id,
            'activity'  => 'added'
        ]);

        return $this->success(['message' => trans('department.added')]);
    }

    /**
     * Used to get Department detail
     * @get ("/api/department/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Department"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $this->authorize('view', Department::class);

        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update Department
     * @patch ("/api/department/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Department"),
     *      @Parameter("name", type="string", required="true", description="Name of Department"),
     *      @Parameter("description", type="text", required="optional", description="Department description")
     * })
     * @return Response
     */
    public function update(DepartmentRequest $request, $id)
    {
        $this->authorize('update', Department::class);

        $department = $this->repo->findOrFail($id);
        
        $department = $this->repo->update($department, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $department->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('department.updated')]);
    }

    /**
     * Used to delete Department
     * @delete ("/api/department/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Department"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $department = $this->repo->deletable($id);

        $this->authorize('delete', $department);

        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => $department->id,
            'sub_module' => $department->name,
            'activity'   => 'deleted'
        ]);

        $this->repo->delete($department);

        return $this->success(['message' => trans('department.deleted')]);
    }
}
