<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Los ajustes globales del sitio, para que el administrador pueda cambiarlos sin pedir
 * que alguien toque el código: los identificadores de analítica, el SEO común a todas las
 * páginas y los datos de la empresa, que hasta ahora estaban repetidos a mano en siete
 * archivos del front.
 *
 * Una sola fila con una columna por ajuste, y no un almacén de clave y valor: el conjunto
 * es pequeño y conocido, y así el formulario del panel es directo y el API no tiene que
 * adivinar tipos.
 *
 * Se siembra con los valores que el sitio tiene hoy escritos en el código, de modo que al
 * desplegar nada cambie y nada dependa de que alguien rellene la pantalla.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('site_settings')) {
            return;
        }

        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();

            // Analítica.
            $table->string('google_ads_id')->nullable();
            $table->string('google_ads_conversion_label')->nullable();
            $table->string('ga4_measurement_id')->nullable();

            // SEO común a todo el sitio.
            $table->string('site_name')->nullable();
            $table->text('default_meta_description')->nullable();
            $table->string('default_share_image')->nullable();
            $table->string('google_site_verification')->nullable();

            // Datos de la empresa.
            $table->string('legal_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('street_address')->nullable();
            $table->string('locality')->nullable();
            $table->string('region')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('psp_license')->nullable();
            $table->string('psp_academy_license')->nullable();

            $table->timestamps();
        });

        DB::table('site_settings')->insert([
            'google_ads_id' => 'AW-679147291',
            'google_ads_conversion_label' => 'Kgj3CILEhrMDEJvu68MC',
            // El cliente todavía no ha dado el suyo; hasta entonces no se carga nada.
            'ga4_measurement_id' => null,

            'site_name' => 'TAP Security',
            'default_meta_description' => 'Private security services and state-approved officer training in Texas, from a veteran owned and operated company.',
            'default_share_image' => null,
            'google_site_verification' => null,

            'legal_name' => 'Texas Asset Protection, LLC',
            'phone' => '+1-210-399-1116',
            'email' => 'admin@txassetpro.com',
            'street_address' => '11503 Jones Maltsberger Rd, Ste 1158',
            'locality' => 'San Antonio',
            'region' => 'TX',
            'postal_code' => '78216',
            'country' => 'US',
            // El sitio opera bajo dos licencias: la de la academia vale en Training
            // Academy, el calendario y los cursos; la otra, en el resto del sitio.
            'psp_license' => 'B05203201',
            'psp_academy_license' => 'F11634201',

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
