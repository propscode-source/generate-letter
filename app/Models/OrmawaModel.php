<?php

namespace App\Models;

use CodeIgniter\Model;

class OrmawaModel extends Model
{
    protected $table = 'ormawa';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_ormawa'];
}

?>