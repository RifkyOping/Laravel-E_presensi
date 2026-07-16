<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    public function siswaProfile()
    {
        return $this->hasOne(SiswaProfile::class);
    }

    public function guruProfile()
    {
        return $this->hasOne(GuruProfile::class);
    }

    // Accessors for SiswaProfile
    public function getNisAttribute() { return $this->siswaProfile?->nis; }
    public function getNisnAttribute() { return $this->siswaProfile?->nisn; }
    public function getKelasAttribute() { return $this->siswaProfile?->kelas; }
    public function getJurusanAttribute() { return $this->siswaProfile?->jurusan; }
    public function getRombelAttribute() { return $this->siswaProfile?->rombel; }
    public function getJenisKelaminAttribute() { return $this->siswaProfile?->jenis_kelamin; }
    public function getAgamaAttribute() { return $this->siswaProfile?->agama; }

    // Accessors for GuruProfile
    public function getIsJadwalSetAttribute() { return $this->guruProfile?->is_jadwal_set ?? false; }
    public function getIsPiketSholatAttribute() { return $this->guruProfile?->is_piket_sholat ?? false; }
    public function getIsPiketMengajarAttribute() { return $this->guruProfile?->is_piket_mengajar ?? false; }
    public function getRppFileAttribute() { return $this->guruProfile?->rpp_file; }
    public function getRppStatusAttribute() { return $this->guruProfile?->rpp_status; }
    public function getRppPesanAttribute() { return $this->guruProfile?->rpp_pesan; }

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
