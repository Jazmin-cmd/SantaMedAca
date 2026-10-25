<?php

namespace Tests\Feature;

use App\Mail\MensajeContacto;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactoApiTest extends TestCase
{
    private function datosValidos(array $extra = []): array
    {
        return array_merge([
            'nombre' => 'Ana Pérez',
            'email' => 'ana@ejemplo.com',
            'telefono' => '0981 123 456',
            'motivo' => 'Turnos',
            'mensaje' => 'Quisiera un turno con cardiología para la semana que viene.',
        ], $extra);
    }

    public function test_envia_el_mensaje_a_la_casilla_de_contacto(): void
    {
        Mail::fake();
        config(['services.contacto.destino' => 'contacto@santamed.com.py']);

        $this->postJson('/api/contacto', $this->datosValidos())
            ->assertOk()
            ->assertJsonPath('message', 'Recibimos tu mensaje. Te respondemos a la brevedad.');

        Mail::assertSent(MensajeContacto::class, function (MensajeContacto $mail) {
            return $mail->hasTo('contacto@santamed.com.py')
                && $mail->hasReplyTo('ana@ejemplo.com')
                && $mail->hasSubject('[Web] Turnos — Ana Pérez');
        });
    }

    public function test_el_correo_incluye_los_datos_del_visitante(): void
    {
        $mail = new MensajeContacto($this->datosValidos());

        $mail->assertSeeInText('Ana Pérez');
        $mail->assertSeeInText('ana@ejemplo.com');
        $mail->assertSeeInText('0981 123 456');
        $mail->assertSeeInText('Quisiera un turno con cardiología');
    }

    public function test_el_telefono_es_opcional(): void
    {
        Mail::fake();

        $this->postJson('/api/contacto', $this->datosValidos(['telefono' => null]))->assertOk();

        Mail::assertSent(MensajeContacto::class);
    }

    public function test_valida_los_campos_obligatorios(): void
    {
        Mail::fake();

        $this->postJson('/api/contacto', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nombre', 'email', 'motivo', 'mensaje']);

        Mail::assertNothingSent();
    }

    public function test_rechaza_correo_invalido_y_motivo_desconocido(): void
    {
        Mail::fake();

        $this->postJson('/api/contacto', $this->datosValidos(['email' => 'no-es-un-correo', 'motivo' => 'Cualquiera']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'motivo']);

        Mail::assertNothingSent();
    }

    public function test_descarta_envios_de_bots_sin_avisarles(): void
    {
        Mail::fake();

        $this->postJson('/api/contacto', $this->datosValidos(['sitio_web' => 'http://spam.example']))
            ->assertOk();

        Mail::assertNothingSent();
    }

    public function test_limita_la_cantidad_de_envios_por_minuto(): void
    {
        Mail::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/contacto', $this->datosValidos())->assertOk();
        }

        $this->postJson('/api/contacto', $this->datosValidos())->assertTooManyRequests();
    }
}
