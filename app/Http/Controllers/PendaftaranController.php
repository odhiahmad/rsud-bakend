<?php


namespace App\Http\Controllers;


use App\Agama;
use App\CaraBayar;
use App\Pasien;
use App\Pendaftaran;
use App\Poly;
use App\RatingPelayanan;
use App\ShuttleBus;
use App\ShuttleBusDetail;
use App\ShuttleBusPenumpang;
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
        $daftar = Pendaftaran::with('getUserRating')->with('getUserShuttle')->where('idUserDaftar', $request->id)->orderBy('created_at', 'desc');

        $data = $daftar->paginate(10);
        return [
            'data' => $data,
            'status' => 'ok',
            'urlImage' => asset('img/profile/'),
        ];

    }

    public function indexStatus(Request $request)
    {
        $tanggal3Harilalu = date('Y-m-d',strtotime("-3 days"));
        $tanggalSekarang = date('Y-m-d');
        $getPasien = Pasien::where('id_user',$request->id)->first();

        $jumlahDaftar = Pendaftaran::where(['idUserDaftar' => $request->id, 'tanggal_daftar' => date("Y-m-d")])->count();
        $pelanggaran = Pendaftaran::where([
            'no_ktp' => $getPasien->no_ktp,
            'status_berobat' => 'Gagal'
        ])->where('tanggal_kunjungan','>=',$tanggal3Harilalu)->where('tanggal_kunjungan','<=',$tanggalSekarang)->count();


        if ($jumlahDaftar >= 3) {
            return [
                'message' => 'Anda sudah mendaftar sebanyak 3 kali hari ini',
                'status' => 'ok',
                'success' => true,
            ];
        } else {
            if ($pelanggaran == 0) {
                return [
                    'message' => 'Silahkan mendaftar',
                    'status' => 'ok',
                    'success' => false,
                ];
            } else {
                return [
                    'message' =>  'Anda tidak bisa mendaftar berobat saat ini, akun anda sedang dikenakan Penalti',
                    'status' => 'ok',
                    'success' => true,
                ];
            }
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

    public function cekPenalti(Request $request){
        $tanggal3Harilalu = date('Y-m-d',strtotime("-3 days"));
        $tanggalSekarang = date('Y-m-d');
        $cekPendaftaran = Pendaftaran::where([
            'nomr'=>$request->nomorMr,
            'status_berobat'=>'Gagal'
        ])->where('tanggal_kunjungan','>=',$tanggal3Harilalu)->where('tanggal_kunjungan','<=',$tanggalSekarang)->count();

        if($cekPendaftaran >= 0){
            return [
                'message' => 'Anda tidak bisa mendaftar berobat saat ini, akun anda sedang dikenakan Penalti',
                'data' => [],
                'success' => false
            ];
        }else{
            return [
                'message' => 'Silahkan Mendaftar',
                'data' => [],
                'success' => true
            ];
        }
    }

    public function cariNomorMrDaftar(Request $request)
    {
        $cekNomorMr = Pasien::where('nomr', $request->nomorMr)->count();
        $shuttleBus = ShuttleBus::get();
        $shuttleBusDetail = ShuttleBusDetail::get();
        $ruangan = Poly::where(['poly_status' => 'Aktif'])->get();
        $caraBayar = CaraBayar::get();
        if ($cekNomorMr === 1) {
            $getNomor = Pasien::where('nomr', $request->nomorMr)->first();
            $tahunLahir = substr($getNomor->tgl_lahir, 0, 4);

            if ($tahunLahir == $request->tahunLahir) {
                return [
                    'message' => 'Nomor MR Ditemukan',
                    'data' => $getNomor,
                    'dataPoly' => $ruangan,
                    'dataBayar' => $caraBayar,
                    'shuttleBus' => $shuttleBus,
                    'shuttleBusDetail' => $shuttleBusDetail,
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

        $getUser = Pasien::where('id_user', $request->id)->first();
        $ruangan = Poly::where(['poly_status' => 'Aktif'])->get();
        $shuttleBus = ShuttleBus::get();
        $shuttleBusDetail = ShuttleBusDetail::get();
        $caraBayar = CaraBayar::get();
        return [
            'message' => 'Profil Ditemukan',
            'data' => $getUser,
            'dataPoly' => $ruangan,
            'dataBayar' => $caraBayar,
            'shuttleBus' => $shuttleBus,
            'shuttleBusDetail' => $shuttleBusDetail,
            'image' => asset('img/profile/' . $getUser['foto']),
            'success' => true
        ];

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

    public function daftarPasienBaru(Request $request)
    {
        $pendaftaran = new Pendaftaran();
        $pasien = new Pasien();

        $tanggal = substr($request->tanggalKunjungan, 0, 10);
        $tahunLahir = substr($request->tanggalLahir, 0, 10);
        $cekJumlah = Pendaftaran::where(['tanggal_kunjungan' => $tanggal, 'jam_kunjungan' => $request->jamKunjungan])->count();
        $cekDataPasien = Pasien::where(['no_ktp' => $request->nik])->count();
        $nomorAntrian = $cekJumlah + 1;
        $jamAntrian = $nomorAntrian * 10;
        $jamKunjunganLabelSub = substr($request->pilihJamLabel, 0, 3);
        $jamKunjunganLabel = $jamKunjunganLabelSub . $jamAntrian . ':00';
        $jam = substr($request->tanggalKunjungan, 11, 8);
        $tanggalDaftar = $tanggal . ' ' . $request->jamKunjungan;
        $tanggalDaftarCek = substr($request->tanggalDaftar, 0, 10);
        if ($cekDataPasien == 0) {
            if ($request->noBpjs != null) {
                $cekBpjsDaftar = Pendaftaran::where(['tanggal_kunjungan' => $tanggal, 'no_bpjs' => $request->noBpjs])->count();
                if ($cekBpjsDaftar == 0) {
                    $cekDaftar = Pendaftaran::where(['no_ktp' => $request->nomorKtp, 'jam_kunjungan' => $request->jamKunjungan, 'tanggal_kunjungan' => $tanggal, 'id_ruang' => $request->idRuang])->count();

                    if ($cekDaftar == 0) {
                        $cekKuotaDaftar = Pendaftaran::where(['jam_kunjungan' => $request->jamKunjungan, 'tanggal_kunjungan' => $tanggal, 'id_ruang' => $request->idRuang])->count();
                        if ($cekKuotaDaftar <= 10) {

                            $pendaftaran->idUserDaftar = $request->idUser;
                            $pendaftaran->nomor_daftar = $nomorAntrian;
                            $pendaftaran->no_ktp = $request->nomorKtp;
                            $pendaftaran->nama_pasien = $request->namaPasien;
                            $pendaftaran->tempat_lahir = $request->tempatLahir;
                            $pendaftaran->tgl_lahir = $tahunLahir;
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
                            $pendaftaran->tanggal_daftar = $request->$tanggalDaftarCek;

                            $pasien->jns_kelamin = $request->jenisKelamin;
                            $pasien->status_kawin = $request->statusKawin;
                            $pasien->tgl_lahir = $tahunLahir;
                            $pasien->no_telpon = $request->noTelpon;
                            $pasien->tempat_lahir = $request->tempatLahir;
                            $pasien->nama = $request->nama;
                            $pasien->pekerjaan = $request->pekerjaan;
                            $pasien->no_ktp = $request->nik;
                            $pasien->nama_provinsi = $request->pilihProvinsi;
                            $pasien->nama_kab_kota = $request->pilihKota;
                            $pasien->nama_kecamatan = $request->pilihKecamatan;
                            $pasien->nama_kelurahan = $request->pilihDesa;
                            $pasien->suku = $request->pilihSuku;
                            $pasien->bahasa = $request->pilihBahasa;
                            $pasien->nama_negara = $request->pilihNegara;
                            $pasien->agama = $request->agama;
                            $pasien->kewarganegaraan = $request->pilihWn;
                            $pasien->alamat = $request->alamat;
                            $pasien->penanggung_jawab = $request->penanggungJawab;
                            $pasien->no_penanggung_jawab = $request->noHpPenanggungJawab;
                            $pasien->no_bpjs = $request->noBpjs;


                            if ($pendaftaran->save() && $pasien->save()) {

                                $rating = new RatingPelayanan();
                                $rating->id_user = $request->idUser;
                                $rating->id_pendaftaran = $pendaftaran->id;
                                $rating->rating = 0;
                                $rating->status = 1;

                                if ($rating->save()) {
                                    return [
                                        'message' => 'Berhasil Mendaftar',
                                        'data' => $pendaftaran,
                                        'success' => true
                                    ];
                                }


                            } else {
                                return [
                                    'message' => 'Gagal Mendaftar',
                                    'data' => [],
                                    'success' => false
                                ];
                            }
                        } else {
                            return [
                                'message' => 'Kuota sudah penuh pada Ruang, Tanggal, dan Jam yang anda Pilih',
                                'data' => [],
                                'success' => false
                            ];
                        }

                    } else {
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
                $cekDaftar = Pendaftaran::where(['no_ktp' => $request->nomorKtp, 'jam_kunjungan' => $request->jamKunjungan, 'tanggal_kunjungan' => $tanggal, 'id_ruang' => $request->idRuang])->count();
                if ($cekDaftar == 0) {
                    $cekKuotaDaftar = Pendaftaran::where(['jam_kunjungan' => $request->jamKunjungan, 'tanggal_kunjungan' => $tanggal, 'id_ruang' => $request->idRuang])->count();
                    if ($cekKuotaDaftar <= 10) {
                        $pendaftaran->idUserDaftar = $request->idUser;
                        $pendaftaran->nomor_daftar = $nomorAntrian;
                        $pendaftaran->no_ktp = $request->nomorKtp;
                        $pendaftaran->nama_pasien = $request->namaPasien;
                        $pendaftaran->tempat_lahir = $request->tempatLahir;
                        $pendaftaran->tgl_lahir = $tahunLahir;
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
                        $pendaftaran->tanggal_daftar = $request->$tanggalDaftarCek;

                        $pasien->jns_kelamin = $request->jenisKelamin;
                        $pasien->status_kawin = $request->statusKawin;
                        $pasien->tgl_lahir = $tahunLahir;
                        $pasien->no_telpon = $request->noTelpon;
                        $pasien->tempat_lahir = $request->tempatLahir;
                        $pasien->nama = $request->nama;
                        $pasien->pekerjaan = $request->pekerjaan;
                        $pasien->no_ktp = $request->nik;
                        $pasien->nama_provinsi = $request->pilihProvinsi;
                        $pasien->nama_kab_kota = $request->pilihKota;
                        $pasien->nama_kecamatan = $request->pilihKecamatan;
                        $pasien->nama_kelurahan = $request->pilihDesa;
                        $pasien->suku = $request->pilihSuku;
                        $pasien->bahasa = $request->pilihBahasa;
                        $pasien->nama_negara = $request->pilihNegara;
                        $pasien->agama = $request->agama;
                        $pasien->kewarganegaraan = $request->pilihWn;
                        $pasien->alamat = $request->alamat;
                        $pasien->penanggung_jawab = $request->penanggungJawab;
                        $pasien->no_penanggung_jawab = $request->noHpPenanggungJawab;
                        $pasien->no_bpjs = $request->noBpjs;

                        if ($pendaftaran->save() && $pasien->save()) {
                            $rating = new RatingPelayanan();
                            $rating->id_user = $request->idUser;
                            $rating->id_pendaftaran = $pendaftaran->id;
                            $rating->rating = 0;
                            $rating->status = 1;

                            if ($rating->save()) {
                                return [
                                    'message' => 'Berhasil Mendaftar',
                                    'data' => $pendaftaran,
                                    'success' => true
                                ];
                            }
                        } else {
                            return [
                                'message' => 'Gagal Mendaftar',
                                'data' => [],
                                'success' => false
                            ];
                        }
                    } else {
                        return [
                            'message' => 'Kuota sudah penuh pada Ruang, Tanggal, dan Jam yang anda Pilih',
                            'data' => [],
                            'success' => false
                        ];
                    }

                } else {
                    return [
                        'message' => 'Gagal Mendaftar, Anda sudah mendaftar di Jam, Hari, dan Poli yang sama ',
                        'data' => [],
                        'success' => false
                    ];
                }

            }
        } else {
            return [
                'message' => 'Gagal Mendaftar, Anda sudah mendaftar dengan nomor KTP yang sama, silahkan ambil nomor MR Anda',
                'data' => [],
                'success' => false
            ];
        }


    }

    public function daftar(Request $request)
    {
        $pendaftaran = new Pendaftaran();
        $shuttle = new ShuttleBusPenumpang();

        $tanggal = substr($request->tanggalKunjungan, 0, 10);

        $cekJumlah = Pendaftaran::where(['tanggal_kunjungan' => $tanggal, 'jam_kunjungan' => $request->jamKunjungan])->count();
        $nomorAntrian = $cekJumlah + 1;
        $jamAntrian = $nomorAntrian * 10;
        $jamKunjunganLabelSub = substr($request->pilihJamLabel, 0, 3);
        $jamKunjunganLabel = $jamKunjunganLabelSub . $jamAntrian . ':00';
        $jam = substr($request->tanggalKunjungan, 11, 8);
        $tanggalDaftarCek = substr($request->tanggalDaftar, 0, 10);
        $tanggalDaftar = $tanggal . ' ' . $request->jamKunjungan;
        if ($request->noBpjs != null) {
            $cekBpjsDaftar = Pendaftaran::where(['tanggal_kunjungan' => $tanggal, 'no_bpjs' => $request->noBpjs])->count();
            if ($cekBpjsDaftar == 0) {
                $cekDaftar = Pendaftaran::where(['no_ktp' => $request->nomorKtp, 'jam_kunjungan' => $request->jamKunjungan, 'tanggal_kunjungan' => $tanggal, 'id_ruang' => $request->idRuang])->count();

                if ($cekDaftar == 0) {
                    $cekKuotaDaftar = Pendaftaran::where(['jam_kunjungan' => $request->jamKunjungan, 'tanggal_kunjungan' => $tanggal, 'id_ruang' => $request->idRuang])->count();
                    if ($cekKuotaDaftar <= 10) {
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
                        $pendaftaran->tanggal_daftar = $tanggalDaftarCek;

                        if ($request->input('pilihShuttleBusId') != null) {
                            $pendaftaran->shuttle_bus = 1;
                        } else {
                            $pendaftaran->shuttle_bus = 0;
                        }

                        if ($pendaftaran->save()) {
                            if ($request->input('pilihShuttleBusId') != null) {
                                $shuttle->id_pendaftaran = $pendaftaran->id;
                                $shuttle->nama = $request->namaPasien;
                                $shuttle->nik = $request->nomorKtp;
                                $shuttle->id_shuttle = $request->pilihShuttleBusId;
                                $shuttle->id_shuttle_rute = $request->pilihShuttleBusRuteId;
                                $shuttle->id_shuttle_detail = $request->pilihShuttleBusDetailId;
                                $shuttle->rute_jam = $request->pilihShuttleBusRuteNama;
                                $shuttle->rute = $request->pilihShuttleBusNama;
                                $shuttle->jam = $request->pilihShuttleBusDetailJam;
                                $shuttle->tempat_tunggu = $request->pilihShuttleBusDetailNama;
                                $shuttle->tanggal = $tanggalDaftarCek;
                                $shuttle->save();
                            }


                            $rating = new RatingPelayanan();
                            $rating->id_user = $request->idUser;
                            $rating->id_pendaftaran = $pendaftaran->id;
                            $rating->rating = 0;
                            $rating->status = 1;

                            if ($rating->save()) {
                                return [
                                    'message' => 'Berhasil Mendaftar',
                                    'data' => $pendaftaran,
                                    'success' => true
                                ];
                            }
                        } else {
                            return [
                                'message' => 'Gagal Mendaftar',
                                'data' => [],
                                'success' => false
                            ];
                        }
                    } else {
                        return [
                            'message' => 'Kuota sudah penuh pada Ruang, Tanggal, dan Jam yang anda Pilih',
                            'data' => [],
                            'success' => false
                        ];
                    }

                } else {
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
            $cekDaftar = Pendaftaran::where(['no_ktp' => $request->nomorKtp, 'jam_kunjungan' => $request->jamKunjungan, 'tanggal_kunjungan' => $tanggal, 'id_ruang' => $request->idRuang])->count();
            if ($cekDaftar == 0) {
                $cekKuotaDaftar = Pendaftaran::where(['jam_kunjungan' => $request->jamKunjungan, 'tanggal_kunjungan' => $tanggal, 'id_ruang' => $request->idRuang])->count();
                if ($cekKuotaDaftar <= 10) {
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
                    $pendaftaran->tanggal_daftar = $tanggalDaftarCek;

                    if ($request->input('pilihShuttleBusId') != null) {
                        $pendaftaran->shuttle_bus = 1;
                    } else {
                        $pendaftaran->shuttle_bus = 0;
                    }

                    if ($pendaftaran->save()) {
                        if ($request->input('pilihShuttleBusId') != null) {
                            $shuttle->id_pendaftaran = $pendaftaran->id;
                            $shuttle->nama = $request->namaPasien;
                            $shuttle->nik = $request->nomorKtp;
                            $shuttle->id_shuttle = $request->pilihShuttleBusId;
                            $shuttle->id_shuttle_rute = $request->pilihShuttleBusRuteId;
                            $shuttle->id_shuttle_detail = $request->pilihShuttleBusDetailId;
                            $shuttle->rute_jam = $request->pilihShuttleBusRuteNama;
                            $shuttle->rute = $request->pilihShuttleBusNama;
                            $shuttle->jam = $request->pilihShuttleBusDetailJam;
                            $shuttle->tempat_tunggu = $request->pilihShuttleBusDetailNama;
                            $shuttle->tanggal = $tanggalDaftarCek;
                            $shuttle->save();
                        }

                        $rating = new RatingPelayanan();
                        $rating->id_user = $request->idUser;
                        $rating->id_pendaftaran = $pendaftaran->id;
                        $rating->rating = 0;
                        $rating->status = 1;

                        if ($rating->save()) {
                            return [
                                'message' => 'Berhasil Mendaftar',
                                'data' => $pendaftaran,
                                'success' => true
                            ];
                        }
                    } else {
                        return [
                            'message' => 'Gagal Mendaftar',
                            'data' => [],
                            'success' => false
                        ];
                    }
                } else {
                    return [
                        'message' => 'Kuota sudah penuh pada Ruang, Tanggal, dan Jam yang anda Pilih',
                        'data' => [],
                        'success' => false
                    ];
                }
            } else {
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
