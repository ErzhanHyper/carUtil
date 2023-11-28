<?php

namespace App\Http\Controllers;

use App\Http\Resources\PreOrderHistoryResource;
use App\Models\Client;
use App\Models\OrderHistory;
use App\Models\PreOrderCar;
use App\Models\PreorderComment;
use App\Services\AuthService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function get(){

        $user = app(AuthService::class)->auth();
        $notification = [];

        if($user->role === 'liner'){
            $client = Client::select('id')->where('idnum', $user->idnum)->get();

            $preorder_ids = [];
            foreach ($client as $item){
                $preorder_ids = PreOrderCar::select(['id', 'client_id'])->where('client_id', $item->id)->pluck('id')->toArray();
            }

            $preorder_history = PreorderComment::whereIn('preorder_id', $preorder_ids)->where('action', '<>', 'SEND_TO_MODERATOR')->orderByDesc('created_at')->get();
            $notification = PreOrderHistoryResource::collection($preorder_history);
        }

        return response()->json($notification);
    }
}
