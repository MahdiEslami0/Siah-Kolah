<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class SupportConversation extends Model
{
    protected $table = 'support_conversations';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $guarded = ['id'];

    public function sender()
    {
        return $this->belongsTo('App\User', 'sender_id', 'id');
    }

    public function supporter()
    {
        return $this->belongsTo('App\User', 'supporter_id', 'id');
    }

    public function supporter_role()
    {
        if (isset($this->supporter_id)) {
            $user = User::where('id',  $this->supporter_id)->first();
            $role = Role::where('id', $user->role_id)->first();
        } else {
            $role = null;
        }
        return  $role;
    }
}
