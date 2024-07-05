<?php

namespace App\Notifications;

use App\BusinessLogicLayer\managers\UserManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MenteeRegistered extends Notification implements ShouldQueue {
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
            ->greeting('Αγαπητή/έ mentee,')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Θα θέλαμε να σας ενημερώσουμε πως έχουμε λάβει επιτυχώς την αίτηση σας.</div>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Το Job-Pairs σας βοηθά να δημιουργήσετε τις δικές σας ευκαιρίες και να μεταβείτε επιτυχημένα στο χώρο εργασίας. Μέσα από 4 συναντήσεις με τον μέντορα σας, θα ανακαλύψετε τις ευκαιρίες του κλάδου που επιθυμείτε να εργαστείτε, τα δυνατά και τα αδύνατα σας σημεία, θα ενεργοποιηθείτε σε μια πραγματική διαδικασία εύρεσης εργασίας και θα έχετε την ευκαιρία να μάθετε και να βελτιωθείτε.</div>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;"><b>Σχετικά με την διαδικασία, θα θέλαμε να σας ενημερώσουμε πως υπάρχει ένας μικρός χρόνος αναμονής μέχρι να ξεκινήσουν οι συναντήσεις. Ο χρόνος αυτός εξαρτάται από την εξειδίκευση και τον κλάδο που επιθυμείτε να κάνετε mentoring, καθώς και από τη διαθεσιμότητα κατάλληλων μεντόρων τη συγκεκριμένη χρονική περίοδο. Αμέσως μόλις υπάρξει κατάλληλος job mentor, ένα μέλος της ομάδας μας θα σας ειδοποιήσει μέσω email.</b></div>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Να σας υπενθυμίσουμε ότι η συνεργασία αυτή στηρίζεται στην αλληλεγγύη και στον επαγγελματισμό. Αυτό σημαίνει ότι <b>δεν περιλαμβάνεται καμία χρηματική συναλλαγή ή δέσμευση εύρεσης εργασίας.</b> Τα εμπλεκόμενα μέρη οικειοθελώς προσφέρουν το χρόνο, την εμπειρία και τις γνώσεις τους. Η επιτυχία των συναντήσεων εξαρτάται από την ειλικρινή διάθεση για συνεργασία, την τήρηση των χρονοδιαγραμμάτων και τη συνέπεια ως προς τις υποχρεώσεις σας.</span>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Ελπίζουμε μέσα από αυτήν την διαδικασία να ανακαλύψετε νέες ευκαιρίες και νέα επαγγελματικά μονοπάτια. Η ομάδα του Job Pairs θα είναι στην διάθεσή σας για ο,τιδήποτε άλλο χρειαστείτε.</span>')
            ->line('<div style="margin-top: 1em; color: #74787E; font-size: 16px; line-height: 1.5em;">Με εκτίμηση,</div>')
            ->line('Η ομάδα του Job-Pairs')
            ->line('<a href="mailto:info@job-pairs.gr">info@job-pairs.gr</a>')
            ->cc($this->userManager->getEmailsForCC());
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
