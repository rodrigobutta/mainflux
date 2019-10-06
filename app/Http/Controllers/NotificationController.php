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



    public function fetchInbox(Request $request)
    {
        
        $user = \Auth::user();
        // $notifications = $user->unreadNotifications;
        $notifications = $user->notifications;

        // TODO ver como fixear el sacar el data o pasar solo data
        foreach ($notifications as &$notification) {
            
            $notification->origin = 'database'; 
            
            // $notification->markReadLink = route('api.notification.read', [
            //     'id' => $notification->id,
            //     'origin' => $notification->origin
            // ]);

            $notification->read = $notification->read()?true:false;
            $notification->unid = $notification->data['unid'];
            $notification->text = $notification->data['text'];
            $notification->jobId = $notification->data['jobId'];
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

        if($notification = $this->getNotificationByOriginId($origin,$id)) {
        
            $notification->markAsRead();

            return $this->success(['message' => 'OK']);
        }
        else{
            return $this->error(['message' => 'NOTFOUND']);
        }
        
    }

    
    public function dismiss($origin,$id)
    {

        if($notification = $this->getNotificationByOriginId($origin,$id)) {
        
            $notification->delete();

            return $this->success(['message' => 'OK']);
        }
        else{
            return $this->error(['message' => 'NOTFOUND']);
        }
        
    }

    // dependiendo de en la instancia de la notificacin que se esta usando, el id se saca o de la columna de id de la base Notifications, o bien, con un unid metido en data desde el Evento origen
    private function getNotificationByOriginId($origin,$id)
    {

        switch ($origin) {

            case 'database':            
                if($notification = DatabaseNotification::find($id)) {
                    return $notification;
                }                
                break;
            
            case 'push':                
                if($notification = DatabaseNotification::whereRaw('JSON_EXTRACT(data,\'$.unid\')=\''.$id.'\'')->first()) {
                    return $notification;
                }
                break;

            default:
                break;
        }
        

        return null;

    }


}