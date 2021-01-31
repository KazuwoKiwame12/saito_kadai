<?php


namespace App\Repository;


use App\Model\SlackUser;
use App\Model\Work;
use Carbon\Carbon;
use App\RepositoryInterface\ListRepositoryInterface;

class ListRepository implements ListRepositoryInterface
{
    public function workList($payload)
    {
        $owner = SlackUser::where('slack_id', $payload['user_id'])->first();
        //listコマンドを出したのが管理者かどうかの確認
        if($owner->is_owner) $response = $this->allList($payload);
        else       $response = $this->myList($payload);

        return $response;
    }


    ///////////////////////////////////////////////
    ///////////管理者用、バイト用のデータ取得//////////
    public function allList($payload) {
        $now   = Carbon::now();
        $year = $now->year;
        $date = Carbon::parse($year.$payload['text']);
        $shifts  = Work::where('date', $date)->get();
        $response = [];

        //コマンド入力日の月と年と合致する
        //全ユーザのデータの取得
        foreach($shifts as $shift) {
            $name = SlackUser::where('slack_id', $shift->user_id)->first()->name;
            $parseStart = new Carbon($shift->start_time);
            $parseEnd = new Carbon($shift->end_time);


            $response[] = [
              'name'     =>$name,
              'start'    =>$parseStart->toTimeString(),
              'end'      =>$parseEnd->toTimeString(),
              'is_owner' =>true
            ];
        }

        return $response;
    }

    public function myList($payload) {
        //コマンド入力日の月と年
        $now   = Carbon::now();
        $nowY = $now->year;
        $nowM  = $now->month;

        //該当するユーザのシフト情報の全て取得
        $worksM = Work::where('user_id', $payload['user_id'])->get();
        $response = [];

        //コマンド入力日の月と年と合致する
        //DBのシフトのデータの取得
        foreach($worksM as $work) {
            $workDate = new Carbon($work->date);

            if($workDate->month == $nowM && $workDate->year == $nowY) {
                //carbon型に変更
                $parseDate = new Carbon($workDate);
                $parseStart = new Carbon($work->start_time);
                $parseEnd = new Carbon($work->end_time);

                $response[] = [
                    'date'  => $parseDate->format('Y年m月d日'),
                    'start' => $parseStart->toTimeString(),
                    'end'   => $parseEnd->toTimeString(),
                    'is_owner' =>false
                ];
            }
        }

        return $response;
    }
}
