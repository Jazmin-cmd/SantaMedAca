<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mensaje enviado desde el formulario de contacto del sitio web.
 * Responder al correo le contesta directamente al visitante (reply-to).
 */
class MensajeContacto extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{nombre: string, email: string, telefono: ?string, motivo: string, mensaje: string}  $datos
     */
    public function __construct(public array $datos) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->datos['email'], $this->datos['nombre'])],
            subject: "[Web] {$this->datos['motivo']} — {$this->datos['nombre']}",
        );
    }

    public function content(): Content
    {
        return new Content(text: 'emails.contacto');
    }
}
