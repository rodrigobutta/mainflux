<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;
use App\Http\Requests\ProjectRequest;
use App\Repositories\DepartmentRepository;
use App\Repositories\ClientRepository;
use App\Repositories\ContractorRepository;
use App\Repositories\ActivityLogRepository;
use App\Repositories\ProjectRepository;

class ProjectController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $department;
    protected $client;
    protected $contractor;
    protected $module = 'project';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        ProjectRepository $repo,
        ActivityLogRepository $activity,
        DepartmentRepository $department,
        ClientRepository $client,
        ContractorRepository $contractor
    ) {
        $this->request    = $request;
        $this->repo       = $repo;
        $this->activity   = $activity;
        $this->department = $department;
        $this->client = $client;
        $this->contractor = $contractor;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to fetch Pre-Requisites for project
     * @get ("/api/project/pre-requisite")
     * @return Response
     */
    public function preRequisite()
    {
        $this->authorize('preRequisite', Project::class);

        $departments      = generateSelectOption($this->department->listAll());
        $clients      = generateSelectOption($this->client->listAll());
        $contractors      = generateSelectOption($this->contractor->listAll());
        $top_projects = generateSelectOption($this->repo->listTopProjects());

        return $this->success(compact('departments', 'clients','contractors', 'top_projects'));
    }

    /**
     * Used to get all Projectes
     * @get ("/api/project")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', Project::class);

        $projects     = $this->repo->paginate($this->request->all());
        $departments      = $this->department->getAll();
        $clients      = $this->client->getAll();
        $contractors      = $this->contractor->getAll();
        $top_projects = $this->repo->getAll();

        return $this->success(compact('projects', 'departments','clients', 'contractors', 'top_projects'));
    }

    /**
     * Used to store Project
     * @post ("/api/project")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Project"),
     *      @Parameter("department_id", type="integer", required="true", description="Id of Department"),
     *      @Parameter("client_id", type="integer", required="true", description="Id of Client"),
     * *      @Parameter("contractor_id", type="integer", required="true", description="Id of Contractor"),
     *      @Parameter("description", type="text", required="optional", description="Project description")
     * })
     * @return Response
     */
    public function store(ProjectRequest $request)
    {
        $this->authorize('create', Project::class);

        $project = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $project->id,
            'activity'  => 'added'
        ]);

        $new_project = ['id' => $project->id, 'name' => $project->project_with_department];

        return $this->success(['message' => trans('project.added'),'new_project' => $new_project]);
    }

    /**
     * Used to get Project detail
     * @get ("/api/project/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Project"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $project = $this->repo->findOrFail($id);

        $this->authorize('view', [$project, $this->repo->getChild(\Auth::user()->Profile->project_id, 1)]);

        $top_projects = generateSelectOption($this->repo->listEditTopProject($id));

        $selected_top_project = ($project->top_project_id) ? ['id' => $project->top_project_id,'name' => $project->Parent->project_with_department] : [];

        $selected_department = ['name' => $project->Department->name, 'id' => $project->department_id];
        $selected_client = ['name' => $project->Client->name, 'id' => $project->client_id];
        $selected_contractor = ['name' => $project->Contractor->name, 'id' => $project->contractor_id];

        return $this->success(compact('project', 'selected_department', 'selected_client','selected_contractor', 'selected_top_project', 'top_projects'));
    }

    /**
     * Used to update Project
     * @patch ("/api/project/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Project"),
     *      @Parameter("name", type="string", required="true", description="Name of Project"),
     *      @Parameter("department_id", type="integer", required="true", description="Id of Department"),
     * *      @Parameter("client_id", type="integer", required="true", description="Id of Client"),
     * * *      @Parameter("contractor_id", type="integer", required="true", description="Id of Contractor"),
     *      @Parameter("description", type="text", required="optional", description="Project description")
     * })
     * @return Response
     */
    public function update(ProjectRequest $request, $id)
    {
        $project = $this->repo->findOrFail($id);

        $this->authorize('view', [$project, $this->repo->getChild(\Auth::user()->Profile->project_id, 1)]);

        $this->authorize('update', Project::class);
        
        $project = $this->repo->update($project, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $project->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('project.updated')]);
    }

    /**
     * Used to delete Project
     * @delete ("/api/project/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Project"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $project = $this->repo->deletable($id);

        $this->authorize('view', [$project, $this->repo->getChild(\Auth::user()->Profile->project_id, 1)]);

        $this->authorize('delete', $project);
        
        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => $project->id,
            'sub_module' => $project->name,
            'activity'   => 'deleted'
        ]);

        $this->repo->delete($project);

        return $this->success(['message' => trans('project.deleted')]);
    }
}
