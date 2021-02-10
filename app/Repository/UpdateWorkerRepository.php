<?php


namespace App\Repository;


use App\Model\SlackUser;
use App\RepositoryInterface\UpdateWorkerRepositoryInterface;
use App\Services\ConstService;
use App\Services\MessageService;
use Exception;

class UpdateWorkerRepository implements UpdateWorkerRepositoryInterface
{
    /**
     * @param $payload
     * @return array
     */
    public function update($payload): array
    {
        $owner = SlackUser::where('slack_id', $payload['user_id'])->first()->is_owner;
        if(!$owner) return [ 'keyword' => ConstService::ONLY_FOR_ADMINISTRATOR, 'option' => null];

        $user = SlackUser::where('name', $payload['text'])->first();
        if(!($user instanceof SlackUser)) return [ 'keyword' => ConstService::NOT_EXIST_PERSON, 'option' => null];

        $user->mode = '熟練者';
        $user->save();
        return [ 'keyword' => ConstService::UPDATE_EMPLOYEE, 'option' => ['name' => $payload['text']]];
    }
}
