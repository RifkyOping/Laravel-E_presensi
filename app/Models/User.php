<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasPushSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nomor_induk',
        'email',
        'password',
        'role',
        'session_token',
        'device_id',
    ];

    /**  
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function jadwalMengajars()
    {
        return $this->hasMany(JadwalMengajar::class);
    }

    public function progresEbook()
    {
        return $this->hasMany(ProgresEbook::class);
    }

    public function catatanQuran()
    {
        return $this->hasMany(CatatanLiterasiQuran::class, 'siswa_id');
    }

    public function absensiGuru()
    {
        return $this->hasMany(AbsensiGuru::class);
    }

    public function aktivitasMengajar()
    {
        return $this->hasMany(AbsensiMengajar::class);
    }

    public function absensiSiswa()
    {
        return $this->hasMany(AbsensiSiswa::class);
    }

    public function absensiKelasSiswa()
    {
        return $this->hasMany(AbsensiKelasSiswa::class, 'siswa_id');
    }

    public function absensiSholat()
    {
        return $this->hasMany(AbsensiSholatSiswa::class);
    }

    public function siswaProfile()
    {
        return $this->hasOne(SiswaProfile::class);
    }

    public function guruProfile()
    {
        return $this->hasOne(GuruProfile::class);
    }

    // Accessors for SiswaProfile
    public function getKelasAttribute() { return $this->siswaProfile?->kelas; }
    public function getJurusanAttribute() { return $this->siswaProfile?->jurusan; }
    public function getRombelAttribute() { return $this->siswaProfile?->rombel; }
    public function getJenisKelaminAttribute() { return $this->siswaProfile?->jenis_kelamin; }
    public function getAgamaAttribute() { return $this->siswaProfile?->agama; }

    // Accessors for GuruProfile
    public function getIsJadwalSetAttribute() { return $this->guruProfile?->is_jadwal_set ?? false; }
    public function getIsPiketSholatAttribute() { return $this->guruProfile?->is_piket_sholat ?? false; }
    public function getIsPiketMengajarAttribute() { return $this->guruProfile?->is_piket_mengajar ?? false; }
    public function getIsPiketRppAttribute() { return $this->guruProfile?->is_piket_rpp ?? false; }
    public function getIsGuruBahasaAttribute() { return $this->guruProfile?->is_guru_bahasa ?? false; }
    public function getIsKepsekAttribute() { return $this->guruProfile?->is_kepsek ?? false; }
    public function getIsKurikulumAttribute() { return $this->guruProfile?->is_kurikulum ?? false; }
    
    public function getJabatanLabelAttribute() {
        if ($this->is_kepsek) return 'Kepala Sekolah';
        if ($this->is_kurikulum) return 'Kurikulum';
        return null;
    }

    /**
     * Relasi ke semua RPP milik guru ini.
     */
    public function rppGurus()
    {
        return $this->hasMany(RppGuru::class);
    }

    /**
     * Ambil RPP aktif (periode bulan ini) untuk tingkat+jurusan tertentu.
     */
    public function getRppForKelas($tingkat, $jurusan)
    {
        return $this->rppGurus()
            ->where('tingkat', $tingkat)
            ->where('jurusan', $jurusan)
            ->where('rpp_periode', date('Y-m'))
            ->first();
    }

    /**
     * Ambil daftar kombinasi unik tingkat+jurusan dari jadwal mengajar guru.
     * Return: Collection of ['tingkat' => 'X', 'jurusan' => 'RPL']
     */
    public function getKelasYangDiajar()
    {
        return JadwalMengajar::where('user_id', $this->id)
            ->get()
            ->map(function ($jadwal) {
                $parts = explode(' ', $jadwal->kelas);
                return [
                    'tingkat' => $parts[0] ?? '',
                    'jurusan' => $parts[1] ?? '',
                ];
            })
            ->unique(function ($item) {
                return $item['tingkat'] . '|' . $item['jurusan'];
            })
            ->values();
    }

    // Accessor for SiswaProfile skip_voice_verification
    public function getSkipVoiceVerificationAttribute() { return $this->siswaProfile?->skip_voice_verification ?? false; }
    
    public function getJenisKelaminLengkapAttribute() {
        if ($this->jenis_kelamin === 'L') return 'Laki-Laki';
        if ($this->jenis_kelamin === 'P') return 'Perempuan';
        return '-';
    }
    
    public function getTempatTanggalLahirAttribute() {
        $tempat = $this->siswaProfile?->tempat_lahir ?? '-';
        $tgl = $this->siswaProfile?->tanggal_lahir ? \Carbon\Carbon::parse($this->siswaProfile->tanggal_lahir)->translatedFormat('d F Y') : '-';
        return "$tempat, $tgl";
    }
}
