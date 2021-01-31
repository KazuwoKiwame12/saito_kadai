<?php


namespace App\Repository;


use App\Model\SlackUser;
use App\RepositoryInterface\UpdateWorkerRepositoryInterface;
use Exception;

class UpdateWorkerRepository implements UpdateWorkerRepositoryInterface
{
    public function update($payload)
    {
        $owner = SlackUser::where('slack_id', $payload['user_id'])->first()->is_owner;
        if(!$owner) return 'このコマンドは管理者のみしか使えません!';


        $user = SlackUser::where('name', $payload['text'])->first();
        if(!($user instanceof SlackUser)) return 'そのような人物は存在しません!';


        $user->mode = '熟練者';
        $user->save();
        return $payload['text'].'のシフト時間制限がなくなりました';
    }
}
