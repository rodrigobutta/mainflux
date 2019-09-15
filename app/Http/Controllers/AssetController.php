<?php

namespace App\Http\Controllers;

use App\Asset;
use Illuminate\Http\Request;
use App\Http\Requests\AssetRequest;
use App\Repositories\DepartmentRepository;
use App\Repositories\ActivityLogRepository;
use App\Repositories\AssetRepository;

class AssetController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $department;
    protected $module = 'asset';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        AssetRepository $repo,
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
     * Used to fetch Pre-Requisites for asset
     * @get ("/api/asset/pre-requisite")
     * @return Response
     */
    public function preRequisite()
    {
        $this->authorize('preRequisite', Asset::class);

        $departments      = generateSelectOption($this->department->listAll());
        $top_assets = generateSelectOption($this->repo->listTopAssets());

        return $this->success(compact('departments', 'top_assets'));
    }

    /**
     * Used to get all Assetes
     * @get ("/api/asset")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', Asset::class);

        $assets     = $this->repo->paginate($this->request->all());
        $departments      = $this->department->getAll();
        $top_assets = $this->repo->getAll();

        return $this->success(compact('assets', 'departments', 'top_assets'));
    }

    /**
     * Used to store Asset
     * @post ("/api/asset")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Asset"),
     *      @Parameter("department_id", type="integer", required="true", description="Id of Department"),
     *      @Parameter("description", type="text", required="optional", description="Asset description")
     * })
     * @return Response
     */
    public function store(AssetRequest $request)
    {
        $this->authorize('create', Asset::class);

        $asset = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $asset->id,
            'activity'  => 'added'
        ]);

        $new_asset = ['id' => $asset->id, 'name' => $asset->asset_with_department];

        return $this->success(['message' => trans('asset.added'),'new_asset' => $new_asset]);
    }

    /**
     * Used to get Asset detail
     * @get ("/api/asset/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Asset"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $asset = $this->repo->findOrFail($id);

        $this->authorize('view', [$asset, $this->repo->getChild(\Auth::user()->Profile->asset_id, 1)]);

        $top_assets = generateSelectOption($this->repo->listEditTopAsset($id));

        $selected_top_asset = ($asset->top_asset_id) ? ['id' => $asset->top_asset_id,'name' => $asset->Parent->asset_with_department] : [];

        $selected_department = ['name' => $asset->Department->name, 'id' => $asset->department_id];

        return $this->success(compact('asset', 'selected_department', 'selected_top_asset', 'top_assets'));
    }

    /**
     * Used to update Asset
     * @patch ("/api/asset/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Asset"),
     *      @Parameter("name", type="string", required="true", description="Name of Asset"),
     *      @Parameter("department_id", type="integer", required="true", description="Id of Department"),
     *      @Parameter("description", type="text", required="optional", description="Asset description")
     * })
     * @return Response
     */
    public function update(AssetRequest $request, $id)
    {
        $asset = $this->repo->findOrFail($id);

        $this->authorize('view', [$asset, $this->repo->getChild(\Auth::user()->Profile->asset_id, 1)]);

        $this->authorize('update', Asset::class);
        
        $asset = $this->repo->update($asset, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $asset->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('asset.updated')]);
    }

    /**
     * Used to delete Asset
     * @delete ("/api/asset/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Asset"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $asset = $this->repo->deletable($id);

        $this->authorize('view', [$asset, $this->repo->getChild(\Auth::user()->Profile->asset_id, 1)]);

        $this->authorize('delete', $asset);
        
        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => $asset->id,
            'sub_module' => $asset->name,
            'activity'   => 'deleted'
        ]);

        $this->repo->delete($asset);

        return $this->success(['message' => trans('asset.deleted')]);
    }
}
