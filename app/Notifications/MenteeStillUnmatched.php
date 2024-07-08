<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MenteeStillUnmatched extends Notification implements ShouldQueue {
    use Queueable;

    private $menteeProfile;

    public $tries = 3;

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
            ->subject("Job-Pairs |  απενεργοποίηση της αίτησης Mentee")
            ->greeting('Αγαπητή/έ mentee,')
            ->line('Επικοινωνούμε μαζί σου σχετικά με την αίτηση που έκανες πριν πριν λίγους μήνες μέσω του site μας <a href="https://job-pairs.gr/" target="_blank">www.job-pairs.gr</a> ώστε να ενταχθείς στο πρόγραμμα mentoring του οργανισμού μας.')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Λαμβάνοντας υπόψη πως δεν έχουμε καταφέρει μέχρι σήμερα να βρούμε διαθέσιμο μέντορα που να ταιριάζει στο προφίλ σου, σε ενημερώνουμε πως η αίτηση σου θα απενεργοποιηθεί. Σε περίπτωση που εξακολουθείς να ενδιαφέρεσαι, μπορείς να επικοινωνήσεις μαζί μας στο <a href="mailto:info@job-pairs.gr">info@job-pairs.gr</a></div>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Ευχαριστούμε πολύ για το ενδιαφέρον σου και ευχόμαστε κάθε επιτυχία στις επαγγελματικές σου αναζητήσεις.</div>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Με εκτίμηση,</div>')
            ->line('Η ομάδα του Job-Pairs')
            ->line('<a href="mailto:info@job-pairs.gr">info@job-pairs.gr</a>');
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
