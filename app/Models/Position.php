<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    // Daftarkan kolom 'name' agar diizinkan untuk mass assignment
    protected $fillable = ['name'];

    // Relasi ke kandidat (tetap pertahankan jika nanti kamu butuh relasi ini)
    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'position_id');
    }
}