<?php


namespace App\Http\Controllers\Shukkin;


use App\Http\Controllers\Controller;
use App\Repository\UpdateWorkerRepositoryInterface;
use App\Services\MessageService;
use App\Services\SlackService;
use Illuminate\Http\Request;

class UpdateWorkerController extends Controller
{
    protected $response;
    protected $notification;

    public function __construct(UpdateWorkerRepositoryInterface $response, SlackService $notification) {
        $this->response     = $response;
        $this->notification = $notification;
    }

    public function handle(Request $request) {
        $payload = $request->all();
        $response = $this->response->update($payload);
        $message = $response['option'] ? MessageService::getForUpdate($response['keyword'], $response['option']) :  MessageService::getForList($response['keyword']);
        $this->notification->send($message);
    }
}
