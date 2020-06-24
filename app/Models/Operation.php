<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model {
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

}
