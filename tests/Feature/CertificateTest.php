<?php

namespace Tests\Feature;

use App\Models\Certification;
use App\Models\Course;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserCourse;
use App\Support\CertificateTemplate;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    /**
     * El certificado salía con el recuadro de imagen rota: `setOptions()` sustituye la
     * configuración entera de dompdf si no se le pasa `true`, y al sustituirla se pierde
     * el `chroot`, con lo que dompdf deja de poder leer el arte del disco (#1832).
     *
     * Un PDF sin el arte pesa ~16 KB; con él, cientos.
     */
    public function test_certificate_embeds_the_artwork(): void
    {
        $arte = 'certs/certificado2_psp36.png';

        $this->assertFileExists(public_path($arte), 'Falta el arte del certificado en public/certs.');

        $pdf = CertificateTemplate::make($this->enrollment($arte))->output();

        $this->assertGreaterThan(100 * 1024, strlen($pdf), 'El PDF no lleva el arte incrustado.');
    }

    public function test_artwork_path_points_to_the_file_on_disk(): void
    {
        $this->assertSame(
            public_path('certs/certificado2_psp36.png'),
            CertificateTemplate::artworkPath('certs/certificado2_psp36.png'),
        );
    }

    /** Cada certificación tiene su propia plantilla; sin certificación, la genérica. */
    public function test_each_certification_has_its_own_template(): void
    {
        foreach ([1 => 'pdf.certificado1', 2 => 'pdf.certificado2', 3 => 'pdf.certificado3', 4 => 'pdf.certificado4', 9 => 'pdf.index'] as $id => $view) {
            $course = new Course();
            $course->certification_id = $id;

            $this->assertSame($view, CertificateTemplate::forCourse($course));
        }
    }

    /** Una matrícula aprobada completa, sin tocar la base de datos. */
    private function enrollment(string $arte): UserCourse
    {
        $user = new User(['name' => 'Kevin Jayru']);
        $user->setRelation('profile', new Profile([
            'firstname' => 'Kevin',
            'middlename' => 'A',
            'lastname' => 'Jayru',
            'social_number' => '123456789',
        ]));

        $course = new Course(['titulo' => 'Level II']);
        $course->certification_id = 2;
        $course->setRelation('certification', new Certification(['image' => $arte]));

        $userCourse = new UserCourse();
        $userCourse->aprobado = true;
        $userCourse->updated_at = now();
        $userCourse->setRelation('user', $user);
        $userCourse->setRelation('course', $course);

        return $userCourse;
    }
}
