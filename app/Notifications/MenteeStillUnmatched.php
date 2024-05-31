<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MenteeStillUnmatched extends Notification implements ShouldQueue {
    use Queueable;

    private $menteeProfile;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable) {
        $this->menteeProfile = $notifiable;
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable) {
        return (new MailMessage)
            ->subject("Job-Pairs | Έχουμε βρει κατάλληλο μέντορα για εσάς")
            ->greeting('Αγαπητέ mentee,')
            ->line("Μετά από αίτηση που κάνατε για να συμμετέχετε στο mentoring πρόγραμμα του Job-Pairs, θα θέλαμε να σας ενημερώσουμε πως έχουμε βρει κατάλληλο μέντορα.")
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Πατώντας "Αποδοχή", κάποιο μέλος της ομάδας μας θα επικοινωνήσει μαζί σας για να ξεκινήσετε τις συναντήσεις με τον μέντορα σας.</span>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Με εξαιρετική εκτίμηση,</div>')
            ->line('Η ομάδα του Job-Pairs');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return [
            //
        ];
    }
}
