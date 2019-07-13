<?php

namespace App\Http\Controllers;

use App\Location;
use Illuminate\Http\Request;
use App\Http\Requests\LocationRequest;
use App\Repositories\ActivityLogRepository;
use App\Repositories\LocationRepository;

class LocationController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'location';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        LocationRepository $repo,
        ActivityLogRepository $activity
    ) {
        $this->request    = $request;
        $this->repo       = $repo;
        $this->activity   = $activity;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to fetch Pre-Requisites for location
     * @get ("/api/location/pre-requisite")
     * @return Response
     */
    public function preRequisite()
    {
        $this->authorize('preRequisite', Location::class);

        $top_locations = generateSelectOption($this->repo->listTopLocations());

        return $this->success(compact('top_locations'));
    }

    /**
     * Used to get all Locationes
     * @get ("/api/location")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', Location::class);

        $locations     = $this->repo->paginate($this->request->all());
        $top_locations = $this->repo->getAll();

        return $this->success(compact('locations', 'top_locations'));
    }

    /**
     * Used to store Location
     * @post ("/api/location")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Location"),
     *      @Parameter("description", type="text", required="optional", description="Location description")
     * })
     * @return Response
     */
    public function store(LocationRequest $request)
    {
        $this->authorize('create', Location::class);

        $location = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $location->id,
            'activity'  => 'added'
        ]);

        $new_location = ['id' => $location->id, 'name' => $location->name];

        return $this->success(['message' => trans('location.added'),'new_location' => $new_location]);
    }

    /**
     * Used to get Location detail
     * @get ("/api/location/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Location"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $location = $this->repo->findOrFail($id);

        $this->authorize('view', [$location, $this->repo->getChild(\Auth::user()->Profile->location_id, 1)]);

        $top_locations = generateSelectOption($this->repo->listEditTopLocation($id));

        $selected_top_location = ($location->top_location_id) ? ['id' => $location->top_location_id,'name' => $location->Parent->name] : [];

        return $this->success(compact('location', 'selected_top_location', 'top_locations'));
    }

    /**
     * Used to update Location
     * @patch ("/api/location/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Location"),
     *      @Parameter("name", type="string", required="true", description="Name of Location"),
     *      @Parameter("description", type="text", required="optional", description="Location description")
     * })
     * @return Response
     */
    public function update(LocationRequest $request, $id)
    {
        $location = $this->repo->findOrFail($id);

        $this->authorize('view', [$location, $this->repo->getChild(\Auth::user()->Profile->location_id, 1)]);

        $this->authorize('update', Location::class);
        
        $location = $this->repo->update($location, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $location->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('location.updated')]);
    }

    /**
     * Used to delete Location
     * @delete ("/api/location/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Location"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $location = $this->repo->deletable($id);

        $this->authorize('view', [$location, $this->repo->getChild(\Auth::user()->Profile->location_id, 1)]);

        $this->authorize('delete', $location);
        
        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => $location->id,
            'sub_module' => $location->name,
            'activity'   => 'deleted'
        ]);

        $this->repo->delete($location);

        return $this->success(['message' => trans('location.deleted')]);
    }
}
