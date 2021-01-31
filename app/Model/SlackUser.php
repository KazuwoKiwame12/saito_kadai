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

    public function updateAllUsersInfos($users) {
        foreach($users as $user) {
            $model = $this->where('slack_id', $user['id'])->first();
            if($model instanceof SlackUser) {
                $model->name = $user['name'];
            }else {
                $model = $this;
                $model->slack_id = $user['id'];
                $model->team_id  = $user['team_id'];
                $model->name     = $user['name'];

                $owner           = $user['is_owner'];
                $model->is_owner = $owner;
                $model->mode = $owner ? '管理者' : 'バイト';
            }
            $model->save();
        }
    }
}
