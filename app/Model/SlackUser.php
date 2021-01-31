<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SlackUser extends Model
{
    protected $table = 'slack_users';

    public function works() {
        return $this->hasMany('App\Model\Work');
    }

    public function slack_team() {
        return $this->belongsTo('App\Model\SlackTeam', 'team_id', 'team_id');
    }
}
