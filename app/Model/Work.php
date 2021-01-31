<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    protected $table = 'works';

    public function slack_user() {
        return $this->belongsTo('App\Model\SlackUser', 'user_id', 'slack_id');
    }
}
