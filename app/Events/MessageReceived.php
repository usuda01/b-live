<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('message.received.'.$this->message->room_id);
    }

    /**
     * ブロードキャストするデータを取得
     *
     * @return array
     */
    public function broadcastWith()
    {
        $this->message->load('user:id,image,name,profile', 'image', 'payment');
        $payment = $this->message->payment;
        $image = $this->message->image;
        $user = $this->message->user;

        return [
            'message' => [
                'id' => $this->message->id,
                'user_id' => $this->message->user_id,
                'room_id' => $this->message->room_id,
                'content' => $this->message->content,
                'created_at' => $this->message->created_at,
                'user_name' => $user->name,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'image' => $user->image,
                    'profile' => $user->profile,
                    'image_path' => $user->getImagePath(),
                ],
                'payment' => $payment ? [
                    'id' => $payment->id,
                    'user_id' => $payment->user_id,
                    'message_id' => $payment->message_id,
                    'product_id' => (string)$payment->product_id,
                    'price' => $payment->price === null ? null : (int)$payment->price,
                    'point' => $payment->point === null ? null : (int)$payment->point,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                ] : null,
                'gift_amount' => $payment ? (int)$payment->point : null,
                'payment_product_id' => $payment ? (string)$payment->product_id : null,
                'image' => $image ? [
                    'id'         => $image->id,
                    // image_path は MessageImage::getImagePathAttribute() を経由（一覧APIと同じ値）
                    'image_path' => $image->image_path,
                ] : null,
            ]
        ];
    }
}
