<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public Contact $contact;
    public string $replyMessage;

    public function __construct(Contact $contact, string $replyMessage)
    {
        $this->contact      = $contact;
        $this->replyMessage = $replyMessage;
    }

    public function build()
    {
        return $this
            ->to($this->contact->email, $this->contact->name)
            ->subject('Phản hồi liên hệ: ' . ($this->contact->subject ?: 'Liên hệ từ ElectronicShop'))
            ->view('emails.contact_reply');
    }
}
