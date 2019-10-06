<?php

namespace App\Http\Controllers\Api;


use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use IlluminateHttpRequest;
use AppHttpRequests;
use AppHttpControllersController;

use App\User;
use App\Job;


class JobController extends Controller
{

    private $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('api');        
    }


    public function list(Request $request){
        
        $response = new \stdClass();

        $statusCode = 200;

        // $parentId = $request->get('parentId',-1);
        // $initial = Job::where('parent_id',$parentId)->orderBy('sort')->get();
        // $data = $this->recursiveJobs($initial);

        $data = Job::where('user_id',1)->get();

        $response->data = $data;

        $response->code = $statusCode;
        return response()->json($response, $statusCode);

    }


    // protected function recursiveJobs($items){
    //     $data = [];

    //     foreach($items as $i){
            
    //         $child = [
    //             'id' => $i->id,
    //             'name' => $i->name,
    //             'children' => $this->recursiveJobs($i->children)
    //         ];

    //         array_push($data, $child);
    //     }

    //     return $data;
    // }


}