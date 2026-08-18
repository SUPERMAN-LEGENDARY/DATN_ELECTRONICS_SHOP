<?php

namespace App\Mail;

use App\Models\News;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsPublishedMail extends Mailable
{
    use SerializesModels;

    public $news;

    public function __construct(News $news)
    {
        $this->news = $news;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bản tin mới: ' . $this->news->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.news_published',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
