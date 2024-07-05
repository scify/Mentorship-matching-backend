<?php

namespace App\Notifications;

use App\Models\eloquent\MentorshipSession;
use App\Models\eloquent\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountManagerSessionInvitation extends Notification implements ShouldQueue {
    use Queueable;

    private $mentorshipSession;
    private $accountManager;

    /**
     * Create a new notification instance.
     *
     * @param MentorshipSession $mentorshipSession
     * @param User $accountManager
     */
    public function __construct(MentorshipSession $mentorshipSession, User $accountManager) {
        $this->mentorshipSession = $mentorshipSession;
        $this->accountManager = $accountManager;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage {
        return (new MailMessage)
            ->subject('Job Pairs | You have been invited to manage a new mentorship session | Session: ' . $this->mentorshipSession->id)
            ->greeting('Dear account manager,')
            ->line("You have been invited to manage a new mentorship session!")
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Choose whether to accept or decline the invitation:</span>')
            ->action('Accept', route('acceptToManageMentorshipSession', ['id' => $this->accountManager->id, 'email' => $this->accountManager->email, 'mentorshipSessionId' => $this->mentorshipSession->id]))
            ->line('<p style="text-align: center; margin-top: 10px; margin-bottom: 10px;"><a href="' . route('declineToManageMentorshipSession', ['id' => $this->accountManager->id, 'email' => $this->accountManager->email, 'mentorshipSessionId' => $this->mentorshipSession->id]) . '">Decline</a></p>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Regards,</div>')
            ->line('The Job-Pairs team')
            ->line('<a href="mailto:info@job-pairs.gr">info@job-pairs.gr</a>')
            ->cc($this->mentorshipSession->account_manager->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array {
        return [
            //
        ];
    }
}
