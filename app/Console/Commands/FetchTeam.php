<?php

namespace App\Console\Commands;

use App\Model\SlackTeam;
use App\Repository\TeamRepository;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class FetchTeam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:slackTeam';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch team information from slack';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //slackAppとのGET通信
        $client = new Client();
        $response = $client->request("GET", "https://slack.com/api/team.info?token=".env('SLACK_TOKEN'));
        $responseBody = json_decode($response->getBody()->getContents(), true);

        //responseから、特定のデータの取得
        if(isset($responseBody['team'])) {
            $team    = $responseBody['team'];
            $id      = $team['id'];
            $name    = $team['name'];
            //DBに内容保存
            (new SlackTeam())->update($id, $name);
        }
    }
}
