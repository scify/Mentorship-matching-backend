<?php

namespace App\Notifications;

use App\Models\eloquent\MentorshipSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MenteeSessionInvitation extends Notification
{
    use Queueable;

    private $mentorshipSession;

    /**
     * Create a new notification instance.
     *
     * @param $mentorshipSession MentorshipSession
     */
    public function __construct($mentorshipSession)
    {
        $this->mentorshipSession = $mentorshipSession;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Job-Pairs | Έχουμε βρει κατάλληλο μέντορα για εσάς")
            ->greeting('Αγαπητέ mentee,')
            ->line("Μετά από αίτηση που κάνατε για να συμμετέχετε στο mentoring πρόγραμμα του Job-Pairs, θα θέλαμε να σας ενημερώσουμε πως έχουμε βρει κατάλληλο μέντορα.")
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Πατώντας "Αποδοχή", κάποιο μέλος της ομάδας μας θα επικοινωνήσει μαζί σας για να ξεκινήσετε τις συναντήσεις με τον μέντορα σας.</span>')
            ->action('Αποδοχή', route('acceptMentorshipSession', [
                'id' => $this->mentorshipSession->mentee->id, 'email' => $this->mentorshipSession->mentee->email, 'mentorshipSessionId' => $this->mentorshipSession->id, 'role' => 'mentee', 'lang' => 'gr'
            ]))
            ->line('<p style="text-align: center; margin-top: 10px; margin-bottom: 10px;"><a href="' . route('declineMentorshipSessionConfirmation', [
                'id' => $this->mentorshipSession->mentee->id, 'email' => $this->mentorshipSession->mentee->email, 'mentorshipSessionId' => $this->mentorshipSession->id, 'role' => 'mentee', 'lang' => 'gr'
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
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
