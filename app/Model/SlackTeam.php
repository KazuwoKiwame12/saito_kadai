<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SlackTeam extends Model
{
    protected $table = 'slack_teams';

    public function slack_users() {
        return $this->hasMany('App\Model\SlackUser');
    }

    public function update($id, $name) {//新規作成か更新
        $model = $this->where('team_id', $id)->first();
        if($model instanceof SlackTeam) {
            $model->team_name = $name;
        }else {
            $model = $this;
            $model->team_id = $id;
            $model->team_name = $name;
        }
        $model->save();
    }
}
