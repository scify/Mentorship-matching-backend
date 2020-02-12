<?php

namespace App\Notifications;

use App\Models\eloquent\MentorshipSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MentorSendRating extends Notification
{
    use Queueable;

    private $rating;
    private $mentorshipSession;

    /**
     * Create a new notification instance.
     *
     * @param $rating mentorSendRating
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
            ->subject("Job-Pairs | Οι συναντήσεις σας ολοκληρώθηκαν")
            ->greeting('Αγαπητέ mentor,')
            ->line("Θα θέλαμε να σας ευχαριστήσουμε θερμά για τη συμμετοχή σας στο Job-Pairs και την ολοκλήρωση των συναντήσεων σας. Χάρη σε εσάς, συνεχίζουμε να δημιουργούμε επαγγελματικές ευκαιρίες.")
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Παρακαλούμε, επισκεφθείτε τον παρακάτω σύνδεσμο ώστε να αξιολογήσετε τον mentee και το πρόγραμμα μας.</span>')
            ->action('Αξιολογήστε εδώ', route('showMenteeRatingForm', [
                'session-id' => $this->mentorshipSession->id, 'mentor-id' => $this->mentorshipSession->mentor->id, 'mentee-id' => $this->mentorshipSession->mentee->id, 'lang' => 'gr'
            ]))
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
