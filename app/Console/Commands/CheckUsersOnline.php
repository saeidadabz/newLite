<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckUsersOnline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-users-online';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rooms = \App\Models\Room::whereNot('workspace_id', null)->get();
        foreach ($rooms as $room) {
            foreach ($room->lkUsers() as $lkUser) {
                $user = $room->users->where('username', $lkUser->getIdentity())->first();
                if ($user === null) {
                    $u = \App\Models\User::where('username', $lkUser->getIdentity())->first();
                    $u->activities()->create([
                        'event_id' => $event->id,
                        'state' => $state,
                        'event_type' => $event->event,
                        'workspace_id' => $event->room()->workspace->id,
                        'room_id' => $event->room()->id,
                        'data' => 'Disconn',
                    ]);
                }
            }
        }
    }
}
