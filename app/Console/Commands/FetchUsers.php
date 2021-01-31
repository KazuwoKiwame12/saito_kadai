<?php

namespace App\Console\Commands;

use App\Model\SlackUser;
use Illuminate\Console\Command;
use \GuzzleHttp\Client;

class FetchUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:slackUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch user information from slack';

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
        $response = $client->request("GET", "https://slack.com/api/users.list?token=".env('SLACK_TOKEN'));
        $responseBody = json_decode($response->getBody()->getContents(), true);

        //DBに保存
        (new SlackUser())->updateAllUsersInfos($responseBody['members']);

    }
}
