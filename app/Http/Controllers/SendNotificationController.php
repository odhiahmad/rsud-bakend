<?php


namespace App\Http\Controllers;

use App\Pasien;
use App\User;
use Illuminate\Http\Request;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
class SendNotificationController extends Controller
{
    public function sendNotification(Request $request){
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = "eYSCkn91TMWdqKS1IrTROg:APA91bGAN1OuahFsBiia5IvOUKnkyGmcoP-cN9eIz1cWiwh60Vs41Up5MD0dY-OvIeS-ml_rCZ9LjXAohX6nkxDxxUmL5dYPq6QhPHbF38bpDIIzNEkYN9KQpRtL8O2dw2w5EtYwh2-3";

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

// return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

// return Array (key : oldToken, value : new token - you must change the token in your database)
        $downstreamResponse->tokensToModify();

// return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

// return Array (key:token, value:error) - in production you should remove from your database the tokens
        $downstreamResponse->tokensWithError();
    }

    public function updateToken(Request $request){
        $data = [
            'tokenNotif' => $request->token,
        ];

        $user = new User();
        if ($user->where('id', $request->id)->update($data)) {
            return response()->json([
                'success' => true,
                'message' => 'Token Berhasil di Update'
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Token Gagal di Update'
            ], 200);
        }
    }
}
