<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $user;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $otp, $type = 'login')
    {
        $this->user = $user;
        $this->otp = $otp;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'login' => 'Your Login OTP - ZYRA',
            'password_reset' => 'Password Reset OTP - ZYRA',
            'email_verification' => 'Email Verification OTP - ZYRA',
            default => 'Your OTP - ZYRA'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

