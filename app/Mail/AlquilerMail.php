<?php

namespace App\Mail;

use App\Models\Alquiler;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class AlquilerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $alquiler;

    /**
     * Create a new message instance.
     */
    public function __construct($UsuId, $AlqId)
    {
        $this->usuario = getUsuario($UsuId);
        $this->alquiler = Alquiler::find($AlqId);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('webmaster@app.inmueble', 'Webmaster'),
            subject: 'Alquiler Finalizado - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.alquiler_finalizado',
            with: [
                'usuario' => $this->usuario,
                'alquiler' => $this->alquiler,
            ],
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
