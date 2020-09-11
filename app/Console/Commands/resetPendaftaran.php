<?php


namespace App\Console\Commands;

use App\Pendaftaran;
use Illuminate\Console\Command;

class resetPendaftaran extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:resetPendaftaran';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset daily pendaftaran';

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
        $data = [
            'status_berobat' => 'Gagal',
        ];
        $tanggalSekarang = date('Y-m-d');
        $pendaftaran = Pendaftaran::where('status_berobat','=','Mendaftar')->where('tanggal_kunjungan','<',$tanggalSekarang)->get();

        $idx = [];
        for($i=0;$i<count($pendaftaran);$i++){
            $idx[$i] = $pendaftaran[$i]['idx'];
        }

        $updatePendaftaran = new Pendaftaran();
        if($updatePendaftaran->whereIn('idx',$idx)->update($data)){
            return response()->json([
                'success' => true,
                'message' => 'Berhasil'
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Gagal Gangguan Jaringan'
            ]);
        }
    }
}

