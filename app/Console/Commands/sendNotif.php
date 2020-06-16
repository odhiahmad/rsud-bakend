<?php

namespace App\Console\Commands;

use App\Notifikasi;
use App\Obat;
use App\ObatDetail;
use App\Pendaftaran;
use App\User;
use Illuminate\Console\Command;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use DateTime;
class sendNotif extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendNotif';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily notif to all user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pendaftarans = Pendaftaran::where('status_berobat','Selesai')->get();
        date_default_timezone_set("Asia/Bangkok");
        $getDate = Date('Y-m-d');
        $jam = Date('h:i');
        $jam1 = '08:00:00';
        $jam2 = '12:00:00';
        $jam3 = '19:00:00';

        $dateTime1 = new DateTime($jam1);
        $dateTime2 = new DateTime($jam2);
        $dateTime3 = new DateTime($jam3);

        foreach ($pendaftarans as $pendaftaran) {
           $user = User::where('id',$pendaftaran->idUserDaftar)->first();
           $getNotifikasiJumlah = Notifikasi::where('id_user',$pendaftaran->idUserDaftar)->count();

           $getObat = Obat::where(['IDPENDAFTARAN'=>$pendaftaran->idx,'JNSLAYANAN'=>'RJ'])->first();
           $getObatDetail = ObatDetail::where(['KDJL'=>$getObat['KDJL']])->get();


           $dataPesan = '';
           for($i = 0;$i<count($getObatDetail);$i++){
              if($getObatDetail[$i]['AP_TGLSELESAI'] >= $getDate){
                  if($getObatDetail[$i]['AP_JMLHARI'] == 3 ){
                      if ($dateTime1->diff(new DateTime)->format('%R') == '+') {
                          $dataPesan .= 'Jangan Lupa Minum Obat Berikut : ';
                          $dataPesan .= $getObatDetail[$i]['NMBRG'].' '.substr($getObatDetail[$i]['AP_WAKTUPAKAI'],'2').'. ';
                      }else if ($dateTime2->diff(new DateTime)->format('%R') == '+') {
                          $dataPesan .= 'Jangan Lupa Minum Obat Berikut : ';
                          $dataPesan .= $getObatDetail[$i]['NMBRG'].' '.substr($getObatDetail[$i]['AP_WAKTUPAKAI'],'2').'. ';
                      }else if ($dateTime3->diff(new DateTime)->format('%R') == '+') {
                          $dataPesan .= $getObatDetail[$i]['NMBRG'].' '.substr($getObatDetail[$i]['AP_WAKTUPAKAI'],'2').'. ';
                      }
                  }else if($getObatDetail[$i]['AP_JMLHARI'] == 2){

                      if ($dateTime1->diff(new DateTime)->format('%R') == '+') {
                          $dataPesan .= 'Jangan Lupa Minum Obat Berikut : ';
                          $dataPesan .= $getObatDetail[$i]['NMBRG'].' '.substr($getObatDetail[$i]['AP_WAKTUPAKAI'],'2').'. ';
                      }else if ($dateTime2->diff(new DateTime)->format('%R') == '+') {
                          $dataPesan .= 'Jangan Lupa Minum Obat Berikut : ';
                          $dataPesan .= $getObatDetail[$i]['NMBRG'].' '.substr($getObatDetail[$i]['AP_WAKTUPAKAI'],'2').'. ';
                      }
                  }else if($getObatDetail[$i]['AP_JMLHARI'] == 1){
                      if ($dateTime1->diff(new DateTime)->format('%R') == '+') {
                          $dataPesan .= 'Jangan Lupa Minum Obat Berikut : ';
                          $dataPesan .= $getObatDetail[$i]['NMBRG'].' '.substr($getObatDetail[$i]['AP_WAKTUPAKAI'],'2').'. ';
                      }
                  }

              }
           }

            if($getNotifikasiJumlah <= 50 ){
                $notifikasi = new Notifikasi();
                $notifikasi->id_user = $pendaftaran->idUserDaftar;
                $notifikasi->judul = 'RSUD Padang Panjang';
                $notifikasi->keterangan = $dataPesan;
                $notifikasi->save();
            }else{
                Notifikasi::where('id_user',$pendaftaran->idUserDaftar)->orderBy('id', 'desc')->limit(1)->delete();
                $notifikasi = new Notifikasi();
                $notifikasi->id_user = $pendaftaran->idUserDaftar;
                $notifikasi->judul = 'RSUD Padang Panjang';
                $notifikasi->keterangan = $dataPesan;
                $notifikasi->save();
            }

            if($dataPesan != ''){
                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60*20);

                $notificationBuilder = new PayloadNotificationBuilder('RSUD Padang Panjang');
                $notificationBuilder->setBody($dataPesan)
                    ->setSound('default');


                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData(['a_data' => 'my_data']);

                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data = $dataBuilder->build();

                $token = $user['tokenNotif'];


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
            }else{

            }


        }

        $this->info($user);
    }
}
