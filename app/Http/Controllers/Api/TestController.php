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


use \Pusher\PushNotifications\PushNotifications;


use App\Events\TestEvent;

class TestController extends Controller
{

    private $guard;

    public function __construct()
    {
        $this->guard = Auth::guard('api');        
    }


    public function pushNotification(Request $request){
        
        $response = new \stdClass();

        $statusCode = 200;


        event(new TestEvent('hello world'));


        $response->message = 'Push notification sent..';

        $response->code = $statusCode;

        return response()->json($response, $statusCode);
    }


    public function backgroundPushNotification(Request $request){

        $interest = $request->get('interest','hello');


        $beamsClient = new PushNotifications(array(
            "instanceId" => env('PUSHER_BEAM_INSTANCE_ID'),
            "secretKey" => env('PUSHER_BEAM_SECRET_KEY'),
        ));
        
        $publishResponse = $beamsClient->publishToInterests(
            [$interest],
            [
                "fcm" => [
                    "notification" => [
                        "title" => "Hello Back",
                        "body" => "Hello, Background!"
                    ],
                    "data" => [
                        "id" => "111111",
                        "aux" => "laralala"
                    ]
                ]
            ]
        );


        $response = new \stdClass();

        $statusCode = 200;

        $response->message = "Background push notification sent to '" . $interest . "'";

        $response->code = $statusCode;

        return response()->json($response, $statusCode);

    }



    public function notificationNegotiateToken(Request $request){

        $beamsClient = new PushNotifications(array(
            "instanceId" => env('PUSHER_BEAM_INSTANCE_ID'),
            "secretKey" => env('PUSHER_BEAM_SECRET_KEY'),
        ));
        

        $userId = "1";


        $beamsToken = $beamsClient->generateToken($userId);
        return response()->json($beamsToken);


        // $response = new \stdClass();

        // $statusCode = 200;

        // $response->message = 'Background push notification sent..';

        // $response->code = $statusCode;

        // return response()->json($response, $statusCode);

    }


    public function userPushNotification(Request $request){

        $beamsClient = new PushNotifications(array(
            "instanceId" => env('PUSHER_BEAM_INSTANCE_ID'),
            "secretKey" => env('PUSHER_BEAM_SECRET_KEY'),
        ));
        
        $publishResponse = $beamsClient->publishToUsers(
            ["1"],
            [
                "fcm" => [
                    "notification" => [
                        "title" => "Hello RODRI",
                        "body" => "Hello, RODRIGO!"
                    ]
                ]
            ]
        );




        $response = new \stdClass();

        $statusCode = 200;

        $response->message = 'User push notification sent..';

        $response->code = $statusCode;

        return response()->json($response, $statusCode);

    }


}