<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $comment;
    private $article;

    /**
     * Create a new notification instance.
     */
    public function __construct($comment, $article)
    {
        $this->comment = $comment;
        $this->article = $article;
    }

    /**
     * Determine the delivery channels for the notification.
     */
    public function via($notifiable)
    {
        return ['broadcast', 'database']; // Can include 'mail' or others if needed
    }

    /**
     * Format the notification for database storage.
     */
    public function toDatabase($notifiable)
    {
        return [
            'comment_id' => $this->comment->id,
            'commenter_name' => $this->comment->user->name,
            'article_id' => $this->article->id,
            'article_title' => $this->article->title,
        ];
    }

    /**
     * Format the notification for broadcasting.
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'comment_id' => $this->comment->id,
            'commenter_name' => $this->comment->user->name,
            'article_id' => $this->article->id,
            'article_title' => $this->article->title,
            'message' => "{$this->comment->user->name} commented on your article: {$this->article->title}"
        ]);
    }

    /**
     * Set the type of broadcast event (optional).
     */
    public function broadcastType()
    {
        return 'new-comment';
    }
}
