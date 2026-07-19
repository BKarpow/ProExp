<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendExpire
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public User $user;
    public string $message;
    public bool $isExp;

    public function __construct(User $user, bool $uniq = true)
    {
        $this->user = $user;
        $exps = $user->expiredDays();
        $this->isExp = $exps->count() > 0;
        $this->message = "<b>🔔 Звіт по термінах:</b>\n";
        $i = 1;
        $ex = [];
        $uniqCount = 0;
        foreach($exps as $e) {
            $key = md5($e->product_id . $e->user_id .  $e->end);
            if (isset($ex[$key]) && $uniq) {
                $uniqCount++;
                continue;
            }
            $ex[$key] = true;
            $this->message .= "🔹 {$i}. <a href='".route('date.show', ['dateProduct'=>$e->id])."'>".
                $e->productName($user->id);
            $this->message .= ($e->markdown) ? "УЦІНКА" : "" ;
            $this->message  .= "</a>";
            // $this->message .= (string)$i .". ". $e->product->name . ": " . $e->days_remaining . " день(ів) (".$e->count.")";
            $this->message .= ": " . $e->days_remaining . " день(ів) \n";
            $i++;
        }
        $ex = null;
        $this->message .= "\n";
        $this->message .= "Термінів: ".(string)$exps->count();
        $this->message .= "\n👇👇👇 Повний список термінів тут 👇👇👇\n";
        $this->message .= "👉 <a href='".url('/')."'>ПЕРЕЙТИ В Теміни</a>";

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
