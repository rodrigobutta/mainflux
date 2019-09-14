<?php

namespace App\Http\Controllers;

use App\Contractor;
use Illuminate\Http\Request;
use App\Http\Requests\ContractorRequest;
use App\Repositories\ContractorRepository;
use App\Repositories\ActivityLogRepository;

class ContractorController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'contractor';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        ContractorRepository $repo,
        ActivityLogRepository $activity
    ) {
        $this->request  = $request;
        $this->repo     = $repo;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get all Contractors
     * @get ("/api/contractor")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', Contractor::class);

        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store Contractor
     * @post ("/api/contractor")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Contractor"),
     *      @Parameter("description", type="text", required="optional", description="Contractor description")
     * })
     * @return Response
     */
    public function store(ContractorRequest $request)
    {
        $this->authorize('create', Contractor::class);

        $contractor = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $contractor->id,
            'activity'  => 'added'
        ]);

        return $this->success(['message' => trans('contractor.added')]);
    }

    /**
     * Used to get Contractor detail
     * @get ("/api/contractor/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Contractor"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $this->authorize('view', Contractor::class);

        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update Contractor
     * @patch ("/api/contractor/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Contractor"),
     *      @Parameter("name", type="string", required="true", description="Name of Contractor"),
     *      @Parameter("description", type="text", required="optional", description="Contractor description")
     * })
     * @return Response
     */
    public function update(ContractorRequest $request, $id)
    {
        $this->authorize('update', Contractor::class);

        $contractor = $this->repo->findOrFail($id);
        
        $contractor = $this->repo->update($contractor, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $contractor->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('contractor.updated')]);
    }

    /**
     * Used to delete Contractor
     * @delete ("/api/contractor/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Contractor"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $contractor = $this->repo->deletable($id);

        $this->authorize('delete', $contractor);

        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => $contractor->id,
            'sub_module' => $contractor->name,
            'activity'   => 'deleted'
        ]);

        $this->repo->delete($contractor);

        return $this->success(['message' => trans('contractor.deleted')]);
    }
}
