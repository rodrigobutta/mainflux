<?php

namespace App\Http\Controllers;

use App\Designation;
use Illuminate\Http\Request;
use App\Http\Requests\DesignationRequest;
use App\Repositories\DepartmentRepository;
use App\Repositories\ActivityLogRepository;
use App\Repositories\DesignationRepository;

class DesignationController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $department;
    protected $module = 'designation';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        DesignationRepository $repo,
        ActivityLogRepository $activity,
        DepartmentRepository $department
    ) {
        $this->request    = $request;
        $this->repo       = $repo;
        $this->activity   = $activity;
        $this->department = $department;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to fetch Pre-Requisites for designation
     * @get ("/api/designation/pre-requisite")
     * @return Response
     */
    public function preRequisite()
    {
        $this->authorize('preRequisite', Designation::class);

        $departments      = generateSelectOption($this->department->listAll());
        $top_designations = generateSelectOption($this->repo->listTopDesignations());

        return $this->success(compact('departments', 'top_designations'));
    }

    /**
     * Used to get all Designationes
     * @get ("/api/designation")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', Designation::class);

        $designations     = $this->repo->paginate($this->request->all());
        $departments      = $this->department->getAll();
        $top_designations = $this->repo->getAll();

        return $this->success(compact('designations', 'departments', 'top_designations'));
    }

    /**
     * Used to store Designation
     * @post ("/api/designation")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Designation"),
     *      @Parameter("department_id", type="integer", required="true", description="Id of Department"),
     *      @Parameter("description", type="text", required="optional", description="Designation description")
     * })
     * @return Response
     */
    public function store(DesignationRequest $request)
    {
        $this->authorize('create', Designation::class);

        $designation = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $designation->id,
            'activity'  => 'added'
        ]);

        $new_designation = ['id' => $designation->id, 'name' => $designation->designation_with_department];

        return $this->success(['message' => trans('designation.added'),'new_designation' => $new_designation]);
    }

    /**
     * Used to get Designation detail
     * @get ("/api/designation/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Designation"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $designation = $this->repo->findOrFail($id);

        $this->authorize('view', [$designation, $this->repo->getChild(\Auth::user()->Profile->designation_id, 1)]);

        $top_designations = generateSelectOption($this->repo->listEditTopDesignation($id));

        $selected_top_designation = ($designation->top_designation_id) ? ['id' => $designation->top_designation_id,'name' => $designation->Parent->designation_with_department] : [];

        $selected_department = ['name' => $designation->Department->name, 'id' => $designation->department_id];

        return $this->success(compact('designation', 'selected_department', 'selected_top_designation', 'top_designations'));
    }

    /**
     * Used to update Designation
     * @patch ("/api/designation/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Designation"),
     *      @Parameter("name", type="string", required="true", description="Name of Designation"),
     *      @Parameter("department_id", type="integer", required="true", description="Id of Department"),
     *      @Parameter("description", type="text", required="optional", description="Designation description")
     * })
     * @return Response
     */
    public function update(DesignationRequest $request, $id)
    {
        $designation = $this->repo->findOrFail($id);

        $this->authorize('view', [$designation, $this->repo->getChild(\Auth::user()->Profile->designation_id, 1)]);

        $this->authorize('update', Designation::class);
        
        $designation = $this->repo->update($designation, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $designation->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('designation.updated')]);
    }

    /**
     * Used to delete Designation
     * @delete ("/api/designation/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Designation"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $designation = $this->repo->deletable($id);

        $this->authorize('view', [$designation, $this->repo->getChild(\Auth::user()->Profile->designation_id, 1)]);

        $this->authorize('delete', $designation);
        
        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => $designation->id,
            'sub_module' => $designation->name,
            'activity'   => 'deleted'
        ]);

        $this->repo->delete($designation);

        return $this->success(['message' => trans('designation.deleted')]);
    }
}
