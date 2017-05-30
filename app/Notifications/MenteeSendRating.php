<?php

namespace App\Notifications;

use App\Models\eloquent\MentorshipSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MenteeSendRating extends Notification
{
    use Queueable;

    private $rating;

    /**
     * Create a new notification instance.
     *
     * @param $rating menteeSendRating
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
            ->subject("JobPairs | Η συνεδρία σας έχει τελειώσει")
            ->greeting('Αγαπητέ mentee,')
            ->line("Θα θέλαμε να σας ενημερώσουμε πως η συνεδρία έχει φτάσει στο τέλος της.")
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Παρακαλούμε, επισκεφτείτε τον παρακάτω σύνδεσμο ώστε να βαθμολογήσετε τον mentee σας.</span>')
            ->action('Βαθμολογήστε τον mentee σας', route('showMenteeRatingForm', [
                'session-id' => $this->mentorshipSession->id, 'mentee-id' => $this->mentorshipSession->mentee->id, 'mentor-id' => $this->mentorshipSession->mentor->id, 'role' => 'mentee'
//                'id' => $this->mentorshipSession->mentor->id, 'email' => $this->mentorshipSession->mentor->email, 'mentorshipSessionId' => $this->mentorshipSession->id, 'role' => 'mentor'
            ]))
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Με εξαιρετική εκτίμηση,</div>')
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
