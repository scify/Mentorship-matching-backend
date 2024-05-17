<?php

namespace App\Notifications;

use App\Models\eloquent\MentorshipSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentorSessionInvitation extends Notification
{
    use Queueable;

    private $mentorshipSession;

    /**
     * Create a new notification instance.
     *
     * @param $mentorshipSession MentorshipSession
     */
    public function __construct(MentorshipSession $mentorshipSession)
    {
        $this->mentorshipSession = $mentorshipSession;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage {
        return (new MailMessage)
            ->subject("Job-Pairs | Έχουμε βρει κατάλληλο mentee για εσάς")
            ->greeting('Αγαπητέ mentor,')
            ->line("Θα θέλαμε να σας ενημερώσουμε πως εγγράφηκε στη βάση μας mentee που ενεργά ψάχνει για εργασία στην ειδικότητα σας και η διαδικασία των συναντήσεων μπορεί να ξεκινήσει.")
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Σε αυτό το σημείο, χρειάζεται να σας θυμίσουμε πως το πρόγραμμα περιλαμβάνει 4 ωριαίες συναντήσεις (περίπου 1 συνάντηση ανά εβδομάδα). Σας ενθαρρύνουμε να μένουν σταθερές οι μέρες και οι ώρες των συναντήσεων, όσο αυτό είναι εφικτό, και να γίνονται στα γραφεία της εταιρίας σας, καθώς θεωρούμε ότι συμβάλλει στην εμπειρία του mentee. Αν αυτό δεν είναι δυνατό, συνεργαζόμαστε με ένα χώρο στο κέντρο της Αθήνας ο οποίος θα μπορέσει να φιλοξενήσει τις συναντήσεις σας. Σε εξαιρετικές περιπτώσεις και όταν η γεωγραφική απόσταση είναι απαγορευτική, τότε μπορούν  να γίνουν συναντήσεις μέσω skype.</span>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Σε επόμενο στάδιο, θα λάβετε από εμάς υποστηρικτικό υλικό για όλες τις συναντήσεις με τον mentee σας. Η εργασιακή σας εμπειρία είναι σίγουρα το μεγαλύτερο πλεονέκτημα στο οποίο στηρίζεται η επιτυχία του προγράμματος και το υλικό μας μπορεί να λειτουργήσει μόνο συμβουλευτικά.</span>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Μπορείτε να αποδεχτείτε ή να απορρίψετε την έναρξη των συναντήσεων παρακάτω.</span>')
            ->action('Αποδοχή', route('acceptMentorshipSession', [
                'id' => $this->mentorshipSession->mentor->id, 'email' => $this->mentorshipSession->mentor->email, 'mentorshipSessionId' => $this->mentorshipSession->id, 'role' => 'mentor', 'lang' => 'gr'
            ]))
            ->line('<p style="text-align: center; margin-top: 10px; margin-bottom: 10px;"><a href="' . route('declineMentorshipSessionConfirmation', [
                'id' => $this->mentorshipSession->mentor->id, 'email' => $this->mentorshipSession->mentor->email, 'mentorshipSessionId' => $this->mentorshipSession->id, 'role' => 'mentor', 'lang' => 'gr'
            ]) . '">Απόρριψη</a></p>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Με εξαιρετική εκτίμηση,</div>')
            ->line('Η ομάδα του Job-Pairs')->cc($this->mentorshipSession->account_manager->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array {
        return [
            //
        ];
    }
}
