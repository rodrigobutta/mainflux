<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\QuestionSetRequest;
use App\Repositories\ActivityLogRepository;
use App\Repositories\QuestionSetRepository;

class QuestionSetController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'question_set';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, QuestionSetRepository $repo, ActivityLogRepository $activity)
    {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get all Question Sets
     * @get ("/api/question-set")
     * @return Response
     */
    public function index()
    {
        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store Question Set
     * @post ("/api/question-set")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Question Set"),
     * })
     * @return Response
     */
    public function store(QuestionSetRequest $request)
    {

    	$question_set = $this->repo->create($this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $question_set->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('task.question_set_added')]);
    }

    /**
     * Used to get Question Set detail
     * @post ("/api/question-set/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Question Set"),
     * })
     * @return Response
     */
    public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to delete Question Set
     * @delete ("/api/question-set")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Question Set to be deleted"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $question_set = $this->repo->deletable($id);
        
        $this->activity->record([
            'module' => $this->module,
            'module_id' => $question_set->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($question_set);

        return $this->success(['message' => trans('task.question_set_deleted')]);
    }
}
