<?php

namespace App\Notifications;

use App\Models\eloquent\MentorshipSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MenteeFollowUp extends Notification implements ShouldQueue {
    use Queueable;

    private $mentorshipSession;

    /**
     * Create a new notification instance.
     *
     * @param $mentorshipSession MentorshipSession
     */
    public function __construct(MentorshipSession $mentorshipSession) {
        $this->mentorshipSession = $mentorshipSession;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array {
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
            ->subject("Job-Pairs | Μοιραστείτε μαζί μας τα επαγγελματικά σας νέα")
            ->greeting('Αγαπητέ mentee,')
            ->line("Θα ήταν χαρά μας να μοιραστείτε μαζί μας τα επαγγελματικά σας νέα και τις αλλαγές που έχουν γίνει στη ζωή σας. ")
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Παρακαλώ, συμπληρώστε το ερωτηματολόγιο πατώντας εδώ:</span>')
            ->action('Συμπληρώστε', 'https://goo.gl/forms/ebgF1Bzrefi9mJxL2')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Με εξαιρετική εκτίμηση,</div>')
            ->line('Η ομάδα του Job-Pairs')->cc($this->mentorshipSession->account_manager->email);
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
