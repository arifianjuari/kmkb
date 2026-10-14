<?php

namespace Tests\Feature;

use App\Services\SimrsService;
use Mockery\MockInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class SimrsMasterBarangExportTest extends TestCase
{
    public function test_master_barang_can_be_exported_to_excel_with_search_filter(): void
    {
        $this->withoutMiddleware();

        $this->mock(SimrsService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getMasterBarang')
                ->once()
                ->with(1000, 0, 'Paracetamol')
                ->andReturn([
                    'data' => [
                        (object) [
                            'kode_brng' => 'OBT001',
                            'nama_brng' => 'Paracetamol 500 mg',
                            'harga_beli_dasar' => 1000,
                            'ralan' => 1500,
                            'kelas3' => 1250,
                            'isi' => 10,
                            'expire' => '2027-12-31',
                            'status' => '1',
                        ],
                    ],
                    'total' => 1,
                ]);
        });

        $response = $this->get(route('simrs.master-barang.export', [
            'search' => 'Paracetamol',
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString(
            'attachment; filename=master-barang-simrs-',
            (string) $response->headers->get('content-disposition')
        );

        ob_start();
        $response->sendContent();
        $content = (string) ob_get_clean();

        $path = tempnam(sys_get_temp_dir(), 'master-barang-export-');
        file_put_contents($path, $content);

        try {
            $sheet = IOFactory::load($path)->getActiveSheet();

            $this->assertSame('MASTER BARANG SIMRS', $sheet->getCell('A1')->getValue());
            $this->assertSame('Kode Barang', $sheet->getCell('A3')->getValue());
            $this->assertSame('OBT001', $sheet->getCell('A4')->getValue());
            $this->assertSame('Paracetamol 500 mg', $sheet->getCell('B4')->getValue());
            $this->assertSame(1000.0, $sheet->getCell('C4')->getValue());
            $this->assertSame('Aktif', $sheet->getCell('H4')->getValue());
        } finally {
            @unlink($path);
        }
    }

    public function test_empty_export_still_returns_a_valid_excel_file(): void
    {
        $this->withoutMiddleware();

        $this->mock(SimrsService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getMasterBarang')
                ->once()
                ->with(1000, 0, 'Tidak Ada')
                ->andReturn(['data' => [], 'total' => 0]);
        });

        $response = $this->get(route('simrs.master-barang.export', ['search' => 'Tidak Ada']));

        ob_start();
        $response->sendContent();
        $content = (string) ob_get_clean();

        $path = tempnam(sys_get_temp_dir(), 'empty-master-barang-export-');
        file_put_contents($path, $content);

        try {
            $sheet = IOFactory::load($path)->getActiveSheet();
            $this->assertSame('Kode Barang', $sheet->getCell('A3')->getValue());
            $this->assertNull($sheet->getCell('A4')->getValue());
        } finally {
            @unlink($path);
        }
    }

    public function test_master_barang_page_has_excel_export_button(): void
    {
        $this->withoutMiddleware();

        $response = $this->get(route('simrs.master-barang'));

        $response->assertOk();
        $response->assertSee('Export Excel');
        $response->assertSee(route('simrs.master-barang.export'), false);
    }
}
