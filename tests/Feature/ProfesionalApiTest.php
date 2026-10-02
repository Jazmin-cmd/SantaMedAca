<?php

namespace Tests\Feature;

use App\Models\Especialidad;
use App\Models\Estudio;
use App\Models\Profesional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfesionalApiTest extends TestCase
{
    use RefreshDatabase;

    private Especialidad $especialidad;

    protected function setUp(): void
    {
        parent::setUp();
        $this->especialidad = Especialidad::create(['nombre' => 'Cardiología']);
    }

    private function datosValidos(array $extra = []): array
    {
        $estudio = Estudio::create(['nombre' => 'Electrocardiograma', 'categoria' => 'Cardiología']);

        return array_merge([
            'nombre' => 'Dra. Ana Benítez',
            'especialidad_id' => $this->especialidad->id,
            'rango_edad_atencion' => 'a partir de 12 años',
            'estudios' => [$estudio->id],
            'horarios' => [
                ['dia_semana' => 'lunes', 'hora_inicio' => '08:00', 'hora_fin' => '12:00'],
                ['dia_semana' => 'lunes', 'hora_inicio' => '14:00', 'hora_fin' => '18:00'],
            ],
        ], $extra);
    }

    public function test_crea_un_profesional_con_estudios_y_horarios(): void
    {
        $this->postJson('/api/profesionales', $this->datosValidos())
            ->assertCreated()
            ->assertJsonPath('data.nombre', 'Dra. Ana Benítez')
            ->assertJsonPath('data.especialidad.nombre', 'Cardiología')
            ->assertJsonCount(1, 'data.estudios')
            ->assertJsonCount(2, 'data.horarios')
            ->assertJsonPath('data.horarios.0.hora_inicio', '08:00');

        $this->assertDatabaseCount('horarios', 2);
    }

    public function test_lista_y_filtra_profesionales(): void
    {
        $this->postJson('/api/profesionales', $this->datosValidos())->assertCreated();
        $otra = Especialidad::create(['nombre' => 'Pediatría']);
        Profesional::create(['nombre' => 'Dr. Luis Gómez', 'especialidad_id' => $otra->id]);

        $this->getJson('/api/profesionales')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson("/api/profesionales?especialidad_id={$otra->id}")
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.nombre', 'Dr. Luis Gómez');
        $this->getJson('/api/profesionales?dia=lunes')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/profesionales?buscar=ana')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_muestra_un_profesional(): void
    {
        $id = $this->postJson('/api/profesionales', $this->datosValidos())->json('data.id');

        $this->getJson("/api/profesionales/{$id}")->assertOk()->assertJsonPath('data.id', $id);
        $this->getJson('/api/profesionales/9999')->assertNotFound();
    }

    public function test_actualiza_y_reemplaza_horarios(): void
    {
        $id = $this->postJson('/api/profesionales', $this->datosValidos())->json('data.id');

        $this->putJson("/api/profesionales/{$id}", [
            'nombre' => 'Dra. Ana Benítez Ruiz',
            'especialidad_id' => $this->especialidad->id,
            'horarios' => [['dia_semana' => 'martes', 'hora_inicio' => '09:00', 'hora_fin' => '13:00']],
        ])
            ->assertOk()
            ->assertJsonPath('data.nombre', 'Dra. Ana Benítez Ruiz')
            ->assertJsonCount(1, 'data.horarios')
            ->assertJsonPath('data.horarios.0.dia_semana', 'martes')
            ->assertJsonCount(1, 'data.estudios'); // no se mandaron estudios: se conservan
    }

    public function test_patch_parcial_solo_cambia_lo_enviado(): void
    {
        $id = $this->postJson('/api/profesionales', $this->datosValidos())->json('data.id');

        $this->patchJson("/api/profesionales/{$id}", ['rango_edad_atencion' => 'adultos'])
            ->assertOk()
            ->assertJsonPath('data.rango_edad_atencion', 'adultos')
            ->assertJsonPath('data.nombre', 'Dra. Ana Benítez')
            ->assertJsonCount(2, 'data.horarios');
    }

    public function test_elimina_un_profesional_y_sus_relaciones(): void
    {
        $id = $this->postJson('/api/profesionales', $this->datosValidos())->json('data.id');

        $this->deleteJson("/api/profesionales/{$id}")->assertNoContent();

        $this->assertDatabaseMissing('profesionales', ['id' => $id]);
        $this->assertDatabaseCount('horarios', 0);
        $this->assertDatabaseCount('profesional_estudio', 0);
    }

    public function test_campos_obligatorios(): void
    {
        $this->postJson('/api/profesionales', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nombre', 'especialidad_id']);
    }

    public function test_no_permite_nombre_duplicado(): void
    {
        $this->postJson('/api/profesionales', $this->datosValidos())->assertCreated();

        $this->postJson('/api/profesionales', $this->datosValidos())
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nombre']);
    }

    public function test_especialidad_y_estudios_deben_existir(): void
    {
        $this->postJson('/api/profesionales', $this->datosValidos(['especialidad_id' => 999, 'estudios' => [999]]))
            ->assertUnprocessable()
            
            ->assertJsonValidationErrors(['especialidad_id', 'estudios.0']);
    }

    public function test_hora_fin_debe_ser_posterior_a_inicio(): void
    {
        $this->postJson('/api/profesionales', $this->datosValidos([
            'horarios' => [['dia_semana' => 'lunes', 'hora_inicio' => '12:00', 'hora_fin' => '08:00']],
        ]))->assertUnprocessable()->assertJsonValidationErrors(['horarios.0.hora_fin']);
    }

    public function test_dia_invalido(): void
    {
        $this->postJson('/api/profesionales', $this->datosValidos([
            'horarios' => [['dia_semana' => 'domingo', 'hora_inicio' => '08:00', 'hora_fin' => '12:00']],
        ]))->assertUnprocessable()->assertJsonValidationErrors(['horarios.0.dia_semana']);
    }

    public function test_no_permite_horarios_superpuestos_el_mismo_dia(): void
    {
        $this->postJson('/api/profesionales', $this->datosValidos([
            'horarios' => [
                ['dia_semana' => 'lunes', 'hora_inicio' => '08:00', 'hora_fin' => '12:00'],
                ['dia_semana' => 'lunes', 'hora_inicio' => '11:00', 'hora_fin' => '15:00'],
                ['dia_semana' => 'martes', 'hora_inicio' => '11:00', 'hora_fin' => '15:00'],
            ],
        ]))->assertUnprocessable()->assertJsonValidationErrors(['horarios.1.hora_inicio']);
    }

    public function test_catalogos(): void
    {
        Estudio::create(['nombre' => 'Ecografía', 'categoria' => 'Ecografías']);

        $this->getJson('/api/especialidades')->assertOk()->assertJsonPath('data.0.nombre', 'Cardiología');
        $this->getJson('/api/estudios')->assertOk()->assertJsonCount(1, 'data');
    }
}