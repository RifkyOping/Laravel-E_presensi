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
        'email',
        'password',
        'role',
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
            'password' => 'hashed',
        ];
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

    // Accessors for SiswaProfile
    public function getNisAttribute() { return $this->siswaProfile?->nis; }
    public function getNisnAttribute() { return $this->siswaProfile?->nisn; }
    public function getKelasAttribute() { return $this->siswaProfile?->kelas; }
    public function getJurusanAttribute() { return $this->siswaProfile?->jurusan; }
    public function getJenisKelaminAttribute() { return $this->siswaProfile?->jenis_kelamin; }
    public function getAgamaAttribute() { return $this->siswaProfile?->agama; }
    
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
