<?php


namespace App\Repository;
use App\RepositoryInterface\ShukkinRepositoryInterface;
use App\Model\SlackUser;
use App\Model\Work;
use Carbon\Carbon;
use Exception;



class ShukkinRepository implements ShukkinRepositoryInterface
{
    public function workMessage($payload) {

        /*slashcommandsからのデータを取得*/
        $now   = Carbon::now();
        $nowYear = $now->year;
        $requestDate = explode(" ", $payload['text']);

        //入力値が間違えていた時の判定
        try {
            $date = Carbon::parse($nowYear.$requestDate[0]);
            $start_time = Carbon::parse($nowYear.$requestDate[0].$requestDate[1]);
            $end_time   = Carbon::parse($nowYear.$requestDate[0].$requestDate[2]);
            $submitWeek = $start_time->weekNumberInMonth-1;
            $user_id = $payload['user_id'];
        } catch(Exception $e) {
            $response = [
                'status'   => -1,
            ];

            return $response;
        }


        $work = new Work();//今後使うので、最初に宣言しておく

        /*過去に登録しているかどうか*/
        $response=$this->checkTime($date, $now);
        if($response['status']== 0) {
            return $response;
        }

        /*人数の確認*/
        $response=$this->checkNum($work, $date);
        if($response['status']== 1) {
            return $response;
        }

        /*一日働ける時間の確認*/
        /*７時間までを前提にしている*/
        $shiftTime = $end_time->diffInMinutes($start_time);
        if($shiftTime > 7*60) {
            $response = [
                'status'   => 4,
            ];

            return $response;
        }

        /*シフトの登録に関して*/
        $user = SlackUser::where('slack_id', $user_id)->first();
        if($user->mode == 'バイト') {
            $checker = $work->where('date', $date)->where('user_id', $user_id);//重複している日にちを確かめる

            /*働けるかの確認*/
            if(!$checker->exists()) {
                $response = $this->canWork($work, $user_id, $shiftTime, $date, $submitWeek);
                if ($response['status'] == 2) {
                    return $response;
                }
            }else {
                $response = $this->canUpdate($work, $user_id, $shiftTime, $date, $submitWeek);
                if ($response['status'] == 2) {
                    return $response;
                }
            }

            /*シフト残り時間の報告*/
            $response = $this->leftTime($work, $user_id, $start_time, $end_time, $date, $submitWeek);
            return $response;

        }else {
            /*時間制限がないバイトがどのくらいシフト入ったか*/
            $response = $this->expertShift($work, $user_id, $start_time, $end_time, $date);
            return $response;
        }
    }




    /***********************************************************************/
    /***************日付け確認,人数確認、時間制限確認、シフト登録*****************/

    /*申請された日が過去ではないか*/
    public function checkTime($date, $now) {
        $result = 3;
        if($date <= $now) {
            $result = 0;
        }

        $response = [
            'status'   => $result,
        ];
        return $response;
    }

    /*人数が５人以上ではないかどうか*/
    public function checkNum($work, $date) {
        $result = 3;
        $workerNum = $work->where('date', $date)->count();

        if($workerNum >=5) {
            $result = 1;
        }

        $response = [
            'status'   => $result,
        ];
        return $response;
    }

    /*申請された時間で働けるかどうか*/
    public function canWork($work, $user_id, $shiftTime, $date, $submitWeek) {
        $result = 3;

        $workListM = $work->getListByUserAndMonth($date->month, $user_id);//１か月のシフトデータ

        $weekLimitTime = 8*60;
        $monthLimitTime= 8*60*5;
        $weekTime = array(0, 0, 0, 0, 0);//各週の勤務時間

        /*各週のデータを取得する*/
        foreach($workListM as $workDay) {
            $endTime = new Carbon($workDay->end_time);
            $startTime = new Carbon($workDay->start_time);
            $dayTime = $endTime->diffInMinutes($startTime);
            $weekTime[$startTime->weekNumberInMonth-1]  += $dayTime;
        }

        /*シフト提出週の確認*/
        if($weekTime[$submitWeek]+$shiftTime > $weekLimitTime) {//8時間
            $result = 2;
        }
        /*今月の確認*/
        if(array_sum($weekTime)+$shiftTime > $monthLimitTime) {
            $result = 2;
        }

        $weekTimeN = $weekTime[$submitWeek];
        $monthTime = array_sum($weekTime);

        $weekLeftTime = $weekLimitTime - $weekTimeN;
        $monthLeftTime = $monthLimitTime- $monthTime;
        $weekHour  = floor($weekLeftTime / 60);
        $weekMin   = $weekLeftTime % 60;
        $monthHour = floor($monthLeftTime / 60);
        $monthMin  = $monthLeftTime % 60;

        $response = [
            'submitWeek'  => $submitWeek,
            'submitMonth' => $date->month,
            'weekHour'    => $weekHour,
            'weekMin'     => $weekMin,
            'monthHour'   => $monthHour,
            'monthMin'    => $monthMin,
            'status'      =>  $result,
        ];
        return $response;
    }

    /*申請された時間で働けるかどうか(重複している場合)*/
    public function canUpdate($work, $user_id, $shiftTime,  $date, $submitWeek) {
        $result = 3;

        $workListM = $work->getListByUserAndMonth($date->month, $user_id);//１か月のシフトデータ

        $weekLimitTime = 8*60;
        $monthLimitTime= 8*60*5;
        $subTime = 0;//申請時間と元々の時間の差分
        $weekTime = array(0, 0, 0, 0, 0);//各週の勤務時間

        /*各週のデータを取得する*/
        foreach($workListM as $workDay) {
            $endTime = new Carbon($workDay->end_time);
            $startTime = new Carbon($workDay->start_time);

            /*重複している日時に対しての処理*/
            if($date->toDateString() == $workDay->date) {
                $dayTime = $shiftTime;
                $subTime = $shiftTime - $endTime->diffInMinutes($startTime);
            }else {
                $dayTime = $endTime->diffInMinutes($startTime);
            }

            $weekTime[$startTime->weekNumberInMonth-1]  += $dayTime;
        }

        /*シフト提出週の確認*/
        if($weekTime[$submitWeek] > $weekLimitTime) {
            $weekTime[$submitWeek] -= $subTime;
            $result = 2;
        }
        /*今月の確認*/
        if(array_sum($weekTime) > $monthLimitTime) {
            $result = 2;
        }

        $weekTimeN = $weekTime[$submitWeek];
        $monthTime = array_sum($weekTime);

        $weekLeftTime = $weekLimitTime - $weekTimeN;
        $monthLeftTime = $monthLimitTime- $monthTime;
        $weekHour  = floor($weekLeftTime / 60);
        $weekMin   = $weekLeftTime % 60;
        $monthHour = floor($monthLeftTime / 60);
        $monthMin  = $monthLeftTime % 60;

        $response = [
            'submitWeek'  => $submitWeek,
            'submitMonth' => $date->month,
            'weekHour'    => $weekHour,
            'weekMin'     => $weekMin,
            'monthHour'   => $monthHour,
            'monthMin'    => $monthMin,
            'status'      =>  $result,
        ];
        return $response;
    }

    /*残り勤務可能時間を求める関数*/
    public function leftTime($work, $user_id, $start_time, $end_time, $date, $submitWeek) {
        /*DBに保存*/
        $checker = $work->where('user_id', $user_id)->where('date', $date);
        if($checker->exists()) {
            $checker->update(['start_time' => $start_time, 'end_time' => $end_time]);
        }else {
            $work->user_id      = $user_id;
            $work->start_time   = $start_time;
            $work->end_time     = $end_time;
            $work->date         = $date;
            $work->save();
        }

        $workListM = $work->getListByUserAndMonth($date->month, $user_id);//１か月のシフトデータ

        $weekLimitTime = 8*60;
        $monthLimitTime= 8*60*5;
        $weekTime = array(0, 0, 0, 0, 0);//各週の勤務時間

        /*各週のデータを取得する*/
        foreach($workListM as $workDay) {
            $endTime = new Carbon($workDay->end_time);
            $startTime = new Carbon($workDay->start_time);
            $dayTime = $endTime->diffInMinutes($startTime);
            $weekTime[$startTime->weekNumberInMonth-1]  += $dayTime;
        }

        $weekTimeN = $weekTime[$submitWeek];
        $monthTime = array_sum($weekTime);

        $weekLeftTime = $weekLimitTime - $weekTimeN;
        $monthLeftTime = $monthLimitTime- $monthTime;
        $weekHour  = floor($weekLeftTime / 60);
        $weekMin   = $weekLeftTime % 60;
        $monthHour = floor($monthLeftTime / 60);
        $monthMin  = $monthLeftTime % 60;

        $response = [
            'submitWeek'  => $submitWeek,
            'submitMonth' => $date->month,
            'weekHour'    => $weekHour,
            'weekMin'     => $weekMin,
            'monthHour'   => $monthHour,
            'monthMin'    => $monthMin,
            'status'      => 3,
        ];

        return $response;

    }

    /*勤務時間を計算するバイトの時間制限がない者用の関数*/
    public function expertShift($work, $user_id, $start_time, $end_time, $date) {
        /*DBに保存*/
        $checker = $work->where('user_id', $user_id)->where('date', $date);
        if($checker->exists()) {
            $checker->update(['start_time' => $start_time, 'end_time' => $end_time]);
        }else {
            $work->user_id      = $user_id;
            $work->start_time   = $start_time;
            $work->end_time     = $end_time;
            $work->date         = $date;
            $work->save();
        }

        $workListM = $work->getListByUserAndMonth($date->month, $user_id);//１か月のシフトデータ

        $weekTime = array(0, 0, 0, 0, 0);//各週の勤務時間

        /*各週のデータを取得する*/
        foreach($workListM as $workDay) {
            $endTime = new Carbon($workDay->end_time);
            $startTime = new Carbon($workDay->start_time);
            $dayTime = $endTime->diffInMinutes($startTime);
            $weekTime[$startTime->weekNumberInMonth-1]  += $dayTime;
        }

        $monthTime = array_sum($weekTime);

        $monthHour = floor($monthTime / 60);
        $monthMin  = $monthTime % 60;

        $response = [
            'submitMonth' => $date->month,
            'monthHour' => $monthHour,
            'monthMin'  => $monthMin,
            'status'   => 5,
        ];

        return $response;

    }

}
