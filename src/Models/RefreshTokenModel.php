<?php

namespace NathanReus\CI4APIFramework\Models;

use CodeIgniter\Model;
use NathanReus\CI4APIFramework\Entities\RefreshToken;

class RefreshTokenModel extends Model
{
    protected $table            = 'refresh_tokens';
    protected $primaryKey       = 'id';
    protected $returnType       = RefreshToken::class;
    protected $allowedFields    = ['user_id', 'family', 'issued_at'];

    // Validation
    protected $validationRules      = [
        'user_id' => 'required',
        'family' => 'required|is_unique[refresh_tokens.family,id,{id}]',
        'issued_at' => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    
}
