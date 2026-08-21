<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'name','locale','background_path','title','body_text','signer_name','signer_position',
        'signature_path','stamp_path','show_score','show_qr','is_active'
    ];

    protected $casts = [
        'show_score'=>'boolean',
        'show_qr'=>'boolean',
        'is_active'=>'boolean',
    ];

    public function courses(){ return $this->hasMany(Course::class); }
    public function certificates(){ return $this->hasMany(Certificate::class); }
}
