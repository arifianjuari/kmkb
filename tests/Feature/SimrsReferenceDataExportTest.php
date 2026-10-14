<?php

namespace Tests\Feature;

use App\Services\SimrsService;
use Mockery\MockInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class SimrsReferenceDataExportTest extends TestCase
{
    public function test_tindakan_rawat_jalan_can_be_exported_to_excel(): void
    {
        $this->withoutMiddleware();

        $this->mock(SimrsService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getTindakanRawatJalan')
                ->once()
                ->with(1000, 0, null)
                ->andReturn([
                    'data' => [
                        (object) [
                            'kd_jenis_prw' => 'RJ001',
                            'nm_perawatan' => 'Konsultasi',
                            'total_byrdrpr' => 75000,
                        ],
                    ],
                    'total' => 1,
                ]);
        });

        $response = $this->get(route('simrs.tindakan-rawat-jalan.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        ob_start();
        $response->sendContent();
        $content = (string) ob_get_clean();

        $path = tempnam(sys_get_temp_dir(), 'tindakan-jalan-export-');
        file_put_contents($path, $content);

        try {
            $sheet = IOFactory::load($path)->getActiveSheet();
            $this->assertSame('TINDAKAN RAWAT JALAN SIMRS', $sheet->getCell('A1')->getValue());
            $this->assertSame('RJ001', $sheet->getCell('A4')->getValue());
            $this->assertSame('Konsultasi', $sheet->getCell('B4')->getValue());
        } finally {
            @unlink($path);
        }
    }

    public function test_simrs_reference_pages_have_excel_export_button(): void
    {
        $this->withoutMiddleware();

        $routes = [
            'simrs.tindakan-rawat-jalan' => 'simrs.tindakan-rawat-jalan.export',
            'simrs.tindakan-rawat-inap' => 'simrs.tindakan-rawat-inap.export',
            'simrs.laboratorium' => 'simrs.laboratorium.export',
            'simrs.radiologi' => 'simrs.radiologi.export',
            'simrs.operasi' => 'simrs.operasi.export',
            'simrs.kamar' => 'simrs.kamar.export',
        ];

        foreach ($routes as $pageRoute => $exportRoute) {
            $response = $this->get(route($pageRoute));
            $response->assertOk();
            $response->assertSee('Export Excel');
            $response->assertSee(route($exportRoute), false);
        }
    }
}
