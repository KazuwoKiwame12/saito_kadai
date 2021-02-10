<?php


namespace App\Http\Controllers\Shukkin;


use App\Http\Controllers\Controller;
use App\Repository\ListRepositoryInterface;
use App\Services\MessageService;
use App\Services\SlackService;
use Illuminate\Http\Request;

class ListController extends Controller
{
    protected $response;
    protected $notification;

    public function __construct(ListRepositoryInterface $response, SlackService $notification) {
        $this->response     = $response;
        $this->notification = $notification;
    }

    public function handle(Request $request) {
        $payload = $request->all();
        $response = $this->response->workList($payload);
        $message = MessageService::getForList($response['keyword'], $response['option']);
        $this->notification->send($message);
    }
}
