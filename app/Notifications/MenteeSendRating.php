<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MenteeSendRating extends Notification {
    use Queueable;

    private $mentorshipSession;

    /**
     * Create a new notification instance.
     *
     * @param $mentorshipSession
     */
    public function __construct($mentorshipSession) {
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
            ->subject("Job-Pairs | Οι συναντήσεις σας ολοκληρώθηκαν")
            ->greeting('Αγαπητέ mentee,')
            ->line("Θα θέλαμε να σας ευχαριστήσουμε θερμά για τη συμμετοχή σας στο Job-Pairs και την ολοκλήρωση των συναντήσεων σας. Ευχόμαστε κάθε επαγγελματική επιτυχία και θα χαρούμε να μαθαίνουμε νέα σας.")
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Παρακαλούμε, επισκεφθείτε τον παρακάτω σύνδεσμο ώστε να αξιολογήσετε τον mentor και το πρόγραμμα μας.</span>')
            ->action('Αξιολογήστε εδώ', route('showMentorRatingForm', [
                'sessionId' => $this->mentorshipSession->id, 'menteeId' => $this->mentorshipSession->mentee->id, 'mentorId' => $this->mentorshipSession->mentor->id, 'lang' => 'gr'
            ]))
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
