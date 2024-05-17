<?php

namespace App\Notifications;

use App\BusinessLogicLayer\managers\UserManager;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentorRegistered extends Notification {
    use Queueable;

    private $userManager;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct() {
        $this->userManager = new UserManager();
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
            ->subject("Καλώς ήλθατε στο JobPairs!")
            ->greeting('Αγαπητέ mentor,')
            ->line("Η αίτηση σας έχει καταχωρηθεί με επιτυχία.")
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Θα θέλαμε να σας καλωσορίσουμε στο Job-Pairs και να σας ευχαριστήσουμε για τη θετική σας διάθεση να συμμετέχετε στο mentoring πρόγραμμα μας. Oι γνώσεις και η εργασιακή σας εμπειρία θα βοηθήσουν νέους ανθρώπους να βρουν το επαγγελματικό μονοπάτι που τους ταιριάζει, σε μια περίοδο που η ανεργία συνιστά ένα από τα σημαντικότερα προβλήματα στην κοινωνία μας.</div>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Σχετικά με τα επόμενα βήματα, θα θέλαμε να σας ενημερώσουμε πως συνήθως υπάρχει ένας μικρός χρόνος αναμονής μέχρι να βρεθεί mentee κατάλληλος για εσάς. Αμέσως μόλις αντιστοιχηθείτε με κάποιον, ένα μέλος της ομάδας μας θα έρθει σε επικοινωνία μαζί σας και θα σας βοηθήσει να συντονίσετε τις συναντήσεις σας.</div>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Σας ευχαριστούμε πολύ για την εμπιστοσύνη σας και τη συμμετοχή σας.</div>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Για ό,τι χρειαστείτε, μη διστάσετε να επικοινωνήσετε μαζί μας στο <a href="mailto:info@job-pairs.gr">info@job-pairs.gr</a></div>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Με εκτίμηση,</div>')
            ->line('Η ομάδα του Job-Pairs')->cc($this->userManager->getEmailsForCC());
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
