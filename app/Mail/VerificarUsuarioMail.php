<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class VerificarUsuarioMail extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $tipo;

    /**
     * Create a new message instance.
     */
    public function __construct($UsuId, $tipo)
    {
        $this->usuario = getUsuario($UsuId);
        $this->tipo = $tipo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $asunto = $this->tipo == 'alta' ? 'Alta de Usuario' : 'Actualización de Usuario';

        return new Envelope(
            from: new Address('webmaster@app.inmueble', 'Webmaster'),
            subject: $asunto .' - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.verificar_usuario',
            with: [
                'usuario' => $this->usuario,
                'tipo' => $this->tipo,
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
