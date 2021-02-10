<?php


namespace App\Services;


class MessageService
{
    /**
     * @param int $keyword
     * @param array $option
     * @return string
     */
    public static function getForShukkin(
        int $keyword,
        array $option = [
            'submitMonth' => 0,
            'submitWeek'  => 0,
            'weekHour'    => 0,
            'weekMin'     => 0,
            'monthHour'   => 0,
            'monthMin'    => 0
        ]
    ): string {
        switch ($keyword) {
            case ConstService::CANNOT_REGISTER_FOR_PAST:
                return '過去に登録はできません！';
            case ConstService::CANNOT_REGISTER_FOR_NUMBER:
                return '人数が５人に達しています！'."\n".'シフト登録できません！代表に連絡してください';
            case ConstService::CANNOT_REGISTER_FOR_WORKLIMI:
                return '申し込まれた時間では働けません!!'."\n".
                    $option['submitMonth'].'月の'.$option['submitWeek'].'週目の残り勤務可能時間は'.$option['weekHour'].'時間'.$option['weekMin'].'分です。'."\n".
                    $option['submitMonth'].'月の残り勤務可能時間は'.$option['monthHour'].'時間'.$option['monthMin'].'分です。';;
            case ConstService::CANNOT_REGISTER_FOR_WORKLIMIT_IN_DAY:
                return '1日に働ける時間は7時間です！';
            case ConstService::NOT_CORRECT_INPUT:
                return '正しい入力値ではありません！'."\n".'以下のように入力してください！！！'."\n".'ex)/shukkin 0101 13:00 19:00';
            case ConstService::REGISTER_BY_EMPLOYEE:
                return  $option['submitMonth'].'月の'.$option['submitWeek'].'週目の残り勤務可能時間は'.$option['weekHour'].'時間'.$option['weekMin'].'分です。'."\n".
                    $option['submitMonth'].'月の残り勤務可能時間は'.$option['monthHour'].'時間'.$option['monthMin'].'分です。';;
            case ConstService::REGISTER_BY_EXPERT_EMPLOYEE:
                return $option['submitMonth'].'月の勤務時間は'.$option['monthHour'].'時間'.$option['monthMin'].'分になっています。';
            default:
                return '引数が誤っています';
        }
    }

    /**
     * @param int $keyword
     * @param array $options
     * @return string
     */
    public static function getForList(
        int $keyword,
        array $options = [[
            'name' => 'no_name',
            'date' => '0000年00月00日',
            'start' => '00:00',
            'end'   => '00:00'
        ]]
    ): string {
        $message = '';
        switch ($keyword) {
            case ConstService::SHIFTS_FOR_ADMINISTRATOR:
                foreach ($options as $option) {
                        $message .= $option['name'].'さんのシフト:'."\t".$option['start'].'-'.$option['end']."\n";
                }
                return $message;
            case ConstService::SHIFTS_FOR_EMPLOYEE:
                foreach ($options as $option) {
                    $message .= $option['date'].':'."\t".$option['start'].'-'.$option['end']."\n";
                }
                return $message;
            default:
                return '引数が誤っています';
        }
    }

    /**
     * @param int $keyword
     * @param array $option
     * @return string
     */
    public static function getForUpdate(int $keyword, array $option = ['name' => 'no_name']): string {
        switch ($keyword) {
            case ConstService::NOT_EXIST_PERSON:
                return 'そのような人物は存在しません!';
            case ConstService::ONLY_FOR_ADMINISTRATOR:
                return 'このコマンドは管理者のみしか使えません!';
            case ConstService::UPDATE_EMPLOYEE:
                return $option['name'].'のシフト時間制限がなくなりました';
            default:
                return '引数が誤っています';
        }
    }
}
