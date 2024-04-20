<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sale_link extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    function getSeller()
    {
        return User::where('id', $this->seller_id)->first();
    }
}
