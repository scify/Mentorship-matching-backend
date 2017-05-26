<?php

namespace App\Notifications;

use App\Models\eloquent\MentorshipSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MentorSessionInvitation extends Notification
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
            ->subject("JobPairs | Έχετε επιλεγεί για συνεδρία")
            ->greeting('Αγαπητέ mentor,')
            ->line("Θα θέλαμε να σας ενημερώσουμε πως έχετε επιλεγεί για συνεδρία.")
            ->line('<span style="margin-top: 10px;">Μπορείτε να αποδεχτείτε ή να απορρίψετε την πρόσκληση παρακάτω.</span>')
            ->action('Αποδοχή', route('acceptMentorshipSession', [
                'id' => $this->mentorshipSession->mentor->id, 'email' => $this->mentorshipSession->mentor->email, 'mentorshipSessionId' => $this->mentorshipSession->id, 'role' => 'mentor'
            ]))
            ->line('<p style="text-align: center; margin-top: 10px; margin-bottom: 10px;"><a href="' . route('declineMentorshipSession', [
                'id' => $this->mentorshipSession->mentor->id, 'email' => $this->mentorshipSession->mentor->email, 'mentorshipSessionId' => $this->mentorshipSession->id, 'role' => 'mentor'
            ]) . '">Απόρριψη</a></p>')
            ->line('Με εξαιρετική εκτίμηση,')
            ->line('Η ομάδα του Job-Pairs')->to($notifiable->routeNotificationFor('mail'))->cc($this->mentorshipSession->account_manager->email);
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
