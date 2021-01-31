<?php


namespace App\Repository;


use App\Model\SlackUser;
use Illuminate\Support\Arr;

class UserRepository
{

    //追加と更新
    public function saveInfo($users) {
        foreach($users as $user) {
            $query = new SlackUser();//インスタンス作成
            $checker = $query->where('slack_id', $user['id']);//登録済みかの確認用変数

            if($checker->exists()) {
                //更新
                $checker->update(['name' => $user['name']]);
            }else {
                //新規追加
                $query->slack_id = $user['id'];
                $query->team_id  = $user['team_id'];
                $query->name     = $user['name'];
                $owner           = Arr::get($user, 'is_owner', false);
                if($owner == 1) $owner = true;
                $query->is_owner = $owner;

                //modeの設定
                if($owner == false) {
                    $query->mode = 'バイト';
                }else {
                    $query->mode = '管理者';
                }

                $query->save();
            }
        }

    }
}
