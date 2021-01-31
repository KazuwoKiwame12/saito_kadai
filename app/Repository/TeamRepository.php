<?php


namespace App\Repository;

use App\Model\SlackTeam;

class TeamRepository
{
    //新しく追加するときのみ
    public function saveInfo($id, $name) {
        $query = new SlackTeam();//インスタンス作成
        $checker = $query->where('team_id', $id);//登録済みかの確認用変数

        if($checker->exists()) {
            $checker->update(['team_name' => $name]);
        }else {
            $query->team_id = $id;
            $query->team_name = $name;
            $query->save();
        }
    }
}
