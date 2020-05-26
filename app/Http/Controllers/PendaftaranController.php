<?php


namespace App\Http\Controllers;


use App\Agama;
use App\Pasien;
use App\Pendaftaran;
use App\SimpanNomorMr;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PDF;

class PendaftaranController extends Controller
{


    public function CetakLaporan()
    {
        $pegawai = Pendaftaran::where('idx', 69)->first();

        $pdf = PDF::loadview('pdf', ['pegawai' => $pegawai]);
        return $pdf->download('laporan-pegawai-pdf');
    }

    public function index(Request $request)
    {
        $daftar = Pendaftaran::where('idUserDaftar', $request->id)->orderBy('created_at', 'desc');

        $data = $daftar->paginate(5);
        return [
            'data' => $data,
            'status' => 'ok',
            'urlImage' => asset('img/profile/'),
        ];

    }

    public function indexStatus(Request $request)
    {

        $jumlahDaftar = Pendaftaran::where(['idUserDaftar' => $request->id, 'tanggal_daftar' => date("Y-m-d")])->count();

        if ($jumlahDaftar >= 3) {
            return [
                'message' => 'Anda sudah mendaftar sebanyak 3 kali hari ini',
                'status' => 'ok',
                'success' => true,
            ];
        } else {
            return [
                'message' => 'Silahkan mendaftar',
                'status' => 'ok',
                'success' => false,
            ];
        }


    }

    public function indexSemua(Request $request)
    {
        $daftar = Pendaftaran::where('tgl_masuk', $request->tanggalMasuk)->get();

        return [
            'data' => $daftar,
            'status' => 'ok'
        ];

    }

    public function cariNomorMr(Request $request)
    {
        $cekNomorMr = Pasien::where('nomr', $request->nomorMr)->count();

        if ($cekNomorMr === 1) {
            $getNomor = Pasien::where('nomr', $request->nomorMr)->first();
            $tahunLahir = substr($getNomor->tgl_lahir, 0, 4);

            if ($tahunLahir == $request->tahunLahir) {
                return [
                    'message' => 'Nomor MR Ditemukan',
                    'data' => $getNomor,
                    'image' => asset('img/profile/' . $getNomor['foto']),
                    'success' => true
                ];
            } else {
                return [
                    'message' => 'Tahun Lahir Salah',
                    'data' => [],
                    'success' => false
                ];
            }

        } else {
            return [
                'message' => 'Nomor MR Tidak Ditemukan',
                'data' => [],
                'success' => false
            ];
        }
    }


    public function getProfilDaftar(Request $request)
    {
        if ($getUser = Pasien::where('id_user', $request->id)->first()) {
            return [
                'message' => 'Profil Ditemukan',
                'data' => $getUser,
                'image' => asset('img/profile/' . $getUser['foto']),
                'success' => true
            ];
        } else {
            return [
                'message' => 'Profil Tidak Ditemukan',
                'data' => [],
                'success' => false
            ];
        }
    }

    public function getUserLengkapiPendaftaran(Request $request)
    {
        if ($getUser = Pasien::where('nomr', $request->nomorMr)->first()) {
            return [
                'message' => 'Profil Ditemukan',
                'dataProfile' => $getUser,
                'success' => true
            ];
        } else {
            return [
                'message' => 'Profil Tidak Ditemukan',
                'dataProfile' => [],
                'success' => false
            ];
        }


    }

    public function daftar(Request $request)
    {
        $pendaftaran = new Pendaftaran();

        $tanggal = substr($request->tanggalKunjungan, 0, 10);

        $cekJumlah = Pendaftaran::where(['tanggal_kunjungan' => $tanggal, 'jam_kunjungan' => $request->jamKunjungan])->count();
        $nomorAntrian = $cekJumlah + 1;
        $jamAntrian = $nomorAntrian * 10;
        $jamKunjunganLabelSub = substr($request->pilihJamLabel, 0, 3);
        $jamKunjunganLabel = $jamKunjunganLabelSub . $jamAntrian . ':00';
        $jam = substr($request->tanggalKunjungan, 11, 8);
        $tanggalDaftar = $tanggal . ' ' . $request->jamKunjungan;
        if ($request->noBpjs != null) {
            $cekBpjsDaftar = Pendaftaran::where(['tanggal_kunjungan' => $tanggal, 'no_bpjs' => $request->noBpjs])->count();
            if ($cekBpjsDaftar == 0) {
                $cekDaftar = Pendaftaran::where(['no_ktp'=>$request->nomorKtp,'jam_kunjungan' => $request->jamKunjungan, 'tanggal_kunjungan' => $tanggal, 'id_ruang' => $request->idRuang])->count();

                if($cekDaftar == 0){
                    $cekKuotaDaftar = Pendaftaran::where(['jam_kunjungan' => $request->jamKunjungan, 'tanggal_kunjungan' => $tanggal, 'id_ruang' => $request->idRuang])->count();
                    if($cekKuotaDaftar <= 10){
                        $pendaftaran->idUserDaftar = $request->idUser;
                        $pendaftaran->nomr = $request->nomorMr;
                        $pendaftaran->nomor_daftar = $nomorAntrian;
                        $pendaftaran->no_ktp = $request->nomorKtp;
                        $pendaftaran->nama_pasien = $request->namaPasien;
                        $pendaftaran->tempat_lahir = $request->tempatLahir;
                        $pendaftaran->tgl_lahir = $request->tanggalLahir;
                        $pendaftaran->jns_kelamin = $request->jenisKelamin;
                        $pendaftaran->jns_layanan = 'RJ';
                        $pendaftaran->tgl_masuk = $tanggalDaftar;
                        $pendaftaran->tanggal_kunjungan = $tanggal;
                        $pendaftaran->jam_kunjungan = $request->jamKunjungan;
                        $pendaftaran->id_ruang = $request->idRuang;
                        $pendaftaran->nama_ruang = $request->namaRuang;
                        $pendaftaran->id_cara_bayar = $request->idCaraBayar;
                        $pendaftaran->cara_bayar = $request->pilihCaraBayar;
                        $pendaftaran->dokterJaga = $request->pilihNrp;
                        $pendaftaran->jam_kunjunganLabel = $request->pilihJamLabel;
                        $pendaftaran->jam_kunjunganAntrian = $jamKunjunganLabel;
                        $pendaftaran->namaDokterJaga = $request->pilihDokter;
                        $pendaftaran->status_berobat = 'Mendaftar';
                        $pendaftaran->no_bpjs = $request->noBpjs;
                        $pendaftaran->no_jaminan = $request->nomorRujukan;
                        $pendaftaran->id_kelas = $request->idKelas;
                        $pendaftaran->kelas_layanan = $request->kelas;

                        if ($pendaftaran->save()) {
                            return [
                                'message' => 'Berhasil Mendaftar',
                                'data' => $pendaftaran,
                                'success' => true
                            ];
                        } else {
                            return [
                                'message' => 'Gagal Mendaftar',
                                'data' => [],
                                'success' => false
                            ];
                        }
                    }else{
                        return [
                            'message' => 'Kuota sudah penuh pada Ruang, Tanggal, dan Jam yang anda Pilih',
                            'data' => [],
                            'success' => false
                        ];
                    }

                }else{
                    return [
                        'message' => 'Gagal Mendaftar, Anda sudah mendaftar di Jam, Hari, dan Poli yang sama ',
                        'data' => [],
                        'success' => false
                    ];
                }

            } else {
                return [
                    'message' => 'Gagal Mendaftar, Anda sudah mendaftar dengan BPJS pada tanggal ' . $tanggal,
                    'data' => [],
                    'success' => false
                ];
            }


        } else {
            $cekDaftar = Pendaftaran::where(['no_ktp'=>$request->nomorKtp,'jam_kunjungan' => $request->jamKunjungan, 'tanggal_kunjungan' => $tanggal, 'id_ruang' => $request->idRuang])->count();
            if($cekDaftar == 0){
                $cekKuotaDaftar = Pendaftaran::where(['jam_kunjungan' => $request->jamKunjungan, 'tanggal_kunjungan' => $tanggal, 'id_ruang' => $request->idRuang])->count();
                if($cekKuotaDaftar <= 10){
                    $pendaftaran->idUserDaftar = $request->idUser;
                    $pendaftaran->nomr = $request->nomorMr;
                    $pendaftaran->nomor_daftar = $nomorAntrian;
                    $pendaftaran->no_ktp = $request->nomorKtp;
                    $pendaftaran->nama_pasien = $request->namaPasien;
                    $pendaftaran->tempat_lahir = $request->tempatLahir;
                    $pendaftaran->tgl_lahir = $request->tanggalLahir;
                    $pendaftaran->jns_kelamin = $request->jenisKelamin;
                    $pendaftaran->jns_layanan = 'RJ';
                    $pendaftaran->tgl_masuk = $tanggalDaftar;
                    $pendaftaran->tanggal_kunjungan = $tanggal;
                    $pendaftaran->jam_kunjungan = $request->jamKunjungan;
                    $pendaftaran->id_ruang = $request->idRuang;
                    $pendaftaran->nama_ruang = $request->namaRuang;
                    $pendaftaran->id_cara_bayar = $request->idCaraBayar;
                    $pendaftaran->cara_bayar = $request->pilihCaraBayar;
                    $pendaftaran->dokterJaga = $request->pilihNrp;
                    $pendaftaran->jam_kunjunganLabel = $request->pilihJamLabel;
                    $pendaftaran->jam_kunjunganAntrian = $jamKunjunganLabel;
                    $pendaftaran->namaDokterJaga = $request->pilihDokter;
                    $pendaftaran->status_berobat = 'Mendaftar';

                    if ($pendaftaran->save()) {
                        return [
                            'message' => 'Berhasil Mendaftar',
                            'data' => $pendaftaran,
                            'success' => true
                        ];
                    } else {
                        return [
                            'message' => 'Gagal Mendaftar',
                            'data' => [],
                            'success' => false
                        ];
                    }
                }else{
                    return [
                        'message' => 'Kuota sudah penuh pada Ruang, Tanggal, dan Jam yang anda Pilih',
                        'data' => [],
                        'success' => false
                    ];
                }

            }else{
                return [
                    'message' => 'Gagal Mendaftar, Anda sudah mendaftar di Jam, Hari, dan Poli yang sama ',
                    'data' => [],
                    'success' => false
                ];
            }

        }


    }

    public function simpanNomr(Request $request)
    {
        $cekFavorit = SimpanNomorMr::where(['id_user' => $request->id, 'nomr' => $request->nomorMr])->count();

        if ($cekFavorit == 0) {
            $simpanFavorit = new SimpanNomorMr();
            $simpanFavorit->id_user = $request->id;
            $simpanFavorit->nomr = $request->nomorMr;
            $simpanFavorit->nama = $request->nama;

            if ($simpanFavorit->save()) {
                return [
                    'message' => 'Berhasil Menambahkan ke favorit',
                    'data' => $simpanFavorit,
                    'success' => true
                ];
            } else {
                return [
                    'message' => 'Gagal Menambahkan ke favorit',
                    'data' => [],
                    'success' => false
                ];
            }
        } else {

            return [
                'message' => 'Sudah ada di favorit',
                'data' => [],
                'success' => true
            ];

        }

    }

    public function getNomorMrSimpan(Request $request)
    {

        $berita = SimpanNomorMr::where('id_user', $request->id)->orderBy('nama', 'desc');

        $searchValue = $request->input('search');


        if ($searchValue) {
            $berita->where(function ($berita) use ($searchValue) {
                $berita->where('nama', 'like', '%' . $searchValue . '%');
                $berita->where('nomr', 'like', '%' . $searchValue . '%');
            });
        }

        $data = $berita->paginate(10);
        return [
            'data' => $data,
            'status' => 'ok'
        ];


    }

}
