<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Los ajustes globales que el administrador edita en el panel y el sitio lee al arrancar.
 */
class SiteSettingsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();
            foreach ([
                'google_ads_id', 'google_ads_conversion_label', 'ga4_measurement_id',
                'site_name', 'default_meta_description', 'default_share_image', 'google_site_verification',
                'legal_name', 'phone', 'email', 'street_address', 'locality', 'region',
                'postal_code', 'country', 'psp_license', 'psp_academy_license',
            ] as $columna) {
                $table->string($columna)->nullable();
            }
            $table->timestamps();
        });
    }

    public function test_the_endpoint_returns_the_three_groups(): void
    {
        SiteSetting::create([
            'google_ads_id' => 'AW-679147291',
            'google_ads_conversion_label' => 'Kgj3CILEhrMDEJvu68MC',
            'ga4_measurement_id' => 'G-ABC1234567',
            'site_name' => 'TAP Security',
            'default_meta_description' => 'Private security services in Texas.',
            'phone' => '+1-210-399-1116',
            'psp_license' => 'B05203201',
        ]);

        $this->getJson('/api/v1/settings')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.analytics.google_ads_id', 'AW-679147291')
            ->assertJsonPath('data.analytics.ga4_measurement_id', 'G-ABC1234567')
            ->assertJsonPath('data.seo.site_name', 'TAP Security')
            ->assertJsonPath('data.company.phone', '+1-210-399-1116')
            ->assertJsonPath('data.company.psp_license', 'B05203201');
    }

    /**
     * Sin fila no puede quedarse en blanco: el sitio pide estos datos en cada visita y una
     * respuesta rota dejaría el pie de página sin teléfono ni licencia.
     */
    public function test_the_endpoint_answers_even_with_an_empty_table(): void
    {
        $this->assertSame(0, SiteSetting::count());

        $this->getJson('/api/v1/settings')
            ->assertOk()
            ->assertJsonPath('data.analytics.google_ads_id', null)
            ->assertJsonPath('data.seo.site_name', null);

        $this->assertSame(1, SiteSetting::count());
    }

    /** La imagen de compartir se guarda como ruta y debe servirse como dirección completa. */
    public function test_the_share_image_comes_back_as_a_full_url(): void
    {
        SiteSetting::create(['default_share_image' => 'site/compartir.png']);

        $url = $this->getJson('/api/v1/settings')->assertOk()->json('data.seo.default_share_image');

        $this->assertStringStartsWith('http', $url);
        $this->assertStringEndsWith('site/compartir.png', $url);
    }
}
