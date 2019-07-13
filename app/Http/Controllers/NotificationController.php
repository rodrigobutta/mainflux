<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;

use App\Events\NotificationPosted;
use Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function post(Request $request)
    {
        $text = $request->get('text');

        $userId = 1;
        // event(new NotificationPosted(Auth::user(), $request->get('notification')));
        event(new NotificationPosted($userId,$text));

        return 'MSG: ' . $text;
    }



    public function fetchUnread(Request $request)
    {
        
        $user = \Auth::user();
        $notifications = $user->unreadNotifications;

        // TODO ver como fixear el sacar el data o pasar solo data
        foreach ($notifications as &$notification) {
            
            $notification->origin = 'database'; 
            
            $notification->markReadLink = route('api.notification.read', [
                'id' => $notification->id,
                'origin' => $notification->origin
            ]);

            $notification->unid = $notification->data['unid'];
            $notification->text = $notification->data['text'];
            $notification->taskId = $notification->data['taskId'];
            $notification->user = $notification->data['user'];
            $notification->link = $notification->data['link'];
            $notification->linkTarget = $notification->data['linkTarget'];

        }

        return $this->success(compact('notifications'));

    }
    

    public function markAsRead($origin,$id)
    {

        // $user = \Auth::user();
        // if($notification = $user->notifications()->find($id)) {
        //     $notification->markAsRead();
        // }

        switch ($origin) {
            case 'database':
            
                if($notification = DatabaseNotification::find($id)) {
                    $notification->markAsRead();
                }
                
                break;
            
            case 'push':
                
                if($notification = DatabaseNotification::whereRaw('JSON_EXTRACT(data,\'$.unid\')=\''.$id.'\'')->first()) {
                    $notification->markAsRead();
                }

                break;

            default:
                # code...
                break;
        }
        

        return $this->success(['message' => 'OK']);

    }


}