<?php

class MyEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $message;

  public function __construct($message)
  {
      $this->message = $message;
  }

  public function broadcastOn()
  {
      return ['my-channel']; // important part of realtime notification
  }
  public function broadcastAs()
  {
      return 'my-event';// important part of realtime notification
  }
}