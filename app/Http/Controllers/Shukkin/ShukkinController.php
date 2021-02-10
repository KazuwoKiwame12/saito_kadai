<?php

namespace App\Http\Controllers\Shukkin;

use App\Http\Controllers\Controller;
use App\Repository\ShukkinRepositoryInterface;
use App\Services\MessageService;
use Illuminate\Http\Request;
use App\Services\SlackService;

class ShukkinController extends Controller
{
      protected $response;
      protected $notification;

      public function __construct(ShukkinRepositoryInterface $response, SlackService $notification) {
          $this->response     = $response;
          $this->notification = $notification;
      }

     public function handle(Request $request) {
        $payload = $request->all();
        $response = $this->response->workMessage($payload);
        $message = $response['option'] ? MessageService::getForShukkin($response['keyword'], $response['option']) :  MessageService::getForList($response['keyword']);
        $this->notification->send($message);
     }
}
