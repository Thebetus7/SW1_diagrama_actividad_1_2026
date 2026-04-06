<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiagramUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $politicaId;
    public $json;

    /**
     * Create a new event instance.
     */
    public function __construct($politicaId, $json)
    {
        $this->politicaId = $politicaId;
        $this->json = $json;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Se usa PresenceChannel para mostrar luego los usuarios conectados (opcionalmente)
        // o un PrivateChannel/Channel público para la política actual
        return [
            new PresenceChannel('diagrama.' . $this->politicaId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'politicaId' => $this->politicaId,
            'json' => $this->json,
        ];
    }

    public function broadcastAs(): string
    {
        // Esto permite que en Echo escuches '.DiagramUpdated'
        return 'DiagramUpdated';
    }
}
