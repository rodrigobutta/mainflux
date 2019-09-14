<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Requests\ClientRequest;
use App\Repositories\ClientRepository;
use App\Repositories\ActivityLogRepository;

class ClientController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'client';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        ClientRepository $repo,
        ActivityLogRepository $activity
    ) {
        $this->request  = $request;
        $this->repo     = $repo;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get all Clients
     * @get ("/api/client")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', Client::class);

        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store Client
     * @post ("/api/client")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Client"),
     *      @Parameter("description", type="text", required="optional", description="Client description")
     * })
     * @return Response
     */
    public function store(ClientRequest $request)
    {
        $this->authorize('create', Client::class);

        $client = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $client->id,
            'activity'  => 'added'
        ]);

        return $this->success(['message' => trans('client.added')]);
    }

    /**
     * Used to get Client detail
     * @get ("/api/client/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Client"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $this->authorize('view', Client::class);

        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update Client
     * @patch ("/api/client/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Client"),
     *      @Parameter("name", type="string", required="true", description="Name of Client"),
     *      @Parameter("description", type="text", required="optional", description="Client description")
     * })
     * @return Response
     */
    public function update(ClientRequest $request, $id)
    {
        $this->authorize('update', Client::class);

        $client = $this->repo->findOrFail($id);
        
        $client = $this->repo->update($client, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $client->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('client.updated')]);
    }

    /**
     * Used to delete Client
     * @delete ("/api/client/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Client"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $client = $this->repo->deletable($id);

        $this->authorize('delete', $client);

        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => $client->id,
            'sub_module' => $client->name,
            'activity'   => 'deleted'
        ]);

        $this->repo->delete($client);

        return $this->success(['message' => trans('client.deleted')]);
    }
}
