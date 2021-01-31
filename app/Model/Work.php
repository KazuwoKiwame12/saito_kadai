<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    protected $table = 'works';

    public function slack_user() {
        return $this->belongsTo('App\Model\SlackUser', 'user_id', 'slack_id');
    }

    public function getListByDate($date) {
        return $this->where('date', $date)->get();
    }
    public function getListByUser($id) {
        return $this->where('user_id', $id)->get();
    }
    public function getByDateAndUser($date, $id): ?Work {
        return $this->where('date', $date)->where('user_id',$id)->find();
    }
    public function getListByUserAndMonth($month, $id) {
        return $this->whereMonth('date', $month)->where('user_id', $id)->get();
    }
}
