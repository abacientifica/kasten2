<?php

namespace App\Listeners;

use App\Events\UserLogin;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class LoginUser implements ShouldQueue
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Carbon
     */
    private $carbon;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request, Carbon $carbon)
    {
        $this->request = $request;
        $this->carbon = $carbon;
    }

    /**
     * Handle the event.
     *
     * @param  UserLogin  $event
     * @return void
     */
    public function handle(UserLogin $event)
    {
        $user = $event->user;
        $user->last_login = $this->carbon;
        $user->last_ipclient = $this->request->ip();
        $user->save();
    }
}
