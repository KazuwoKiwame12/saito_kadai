<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SlackTeam extends Model
{
    protected $table = 'slack_teams';

    public function slack_users() {
        return $this->hasMany('App\Model\SlackUser');
    }
}
