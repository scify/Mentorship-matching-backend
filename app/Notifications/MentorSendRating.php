<?php

namespace App\Notifications;

use App\Models\eloquent\MentorshipSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentorSendRating extends Notification implements ShouldQueue {
    use Queueable;

    private $rating;
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
            ->subject("Job-Pairs | Οι συναντήσεις σας ολοκληρώθηκαν")
            ->greeting('Αγαπητή/έ mentor,')
            ->line("Θα θέλαμε να σας ευχαριστήσουμε θερμά για τη συμμετοχή σας στο Job-Pairs και την ολοκλήρωση των συναντήσεων σας. Χάρη σε εσάς, συνεχίζουμε να δημιουργούμε επαγγελματικές ευκαιρίες.")
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Παρακαλούμε, επισκεφθείτε τον παρακάτω σύνδεσμο ώστε να αξιολογήσετε τον mentee και το πρόγραμμα μας.</span>')
            ->action('Αξιολογήστε εδώ', route('showMenteeRatingForm', [
                'sessionId' => $this->mentorshipSession->id, 'mentorId' => $this->mentorshipSession->mentor->id, 'menteeId' => $this->mentorshipSession->mentee->id, 'lang' => 'gr'
            ]))
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Με εκτίμηση,</div>')
            ->line('Η ομάδα του Job-Pairs')
            ->line('<a href="mailto:info@job-pairs.gr">info@job-pairs.gr</a>')
            ->cc($this->mentorshipSession->account_manager->email);
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
