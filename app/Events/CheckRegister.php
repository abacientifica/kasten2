<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CheckRegister
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    //Las variables que vienen desde el controlador para ser utilizadas en los listeners asociados al evento,
    public  $newCheck;
    public  $ListaCheck;

    public function __construct($newCheck,$ListaCheck)
    {
        $this->newCheck = $newCheck;
        $this->ListaCheck = $ListaCheck;
    }
}
