<?php

namespace App\Console\Commands;

use App\Models\Document;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ProtectSupplierDocuments extends Command
{
    protected $signature = 'catalog:protect-documents {--apply : Pindahkan file dari disk public ke disk private local}';

    protected $description = 'Pindahkan file dokumen pemasok dari disk public ke disk local agar melewati gate otorisasi';

    public function handle(): int
    {
        $documents = Document::query()
            ->whereNotNull('file_path')
            ->where('file_path', '!=', '')
            ->orderBy('id')
            ->get();

        $pending = $documents->filter(function (Document $doc): bool {
            $path = (string) $doc->file_path;

            return $path !== ''
                && ! str_contains($path, '..')
                && ! Storage::disk('local')->exists($path)
                && Storage::disk('public')->exists($path);
        })->values();

        if ($pending->isEmpty()) {
            $this->info('Semua dokumen sudah tersimpan aman di disk private atau tidak ditemukan di disk public.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Supplier ID', 'Judul', 'Premium', 'Path'],
            $pending->map(fn (Document $doc): array => [
                $doc->id,
                $doc->supplier_id,
                $doc->title,
                $doc->is_premium ? 'Ya' : 'Tidak',
                $doc->file_path,
            ])->all()
        );

        if (! $this->option('apply')) {
            $this->info('Pratinjau saja. Jalankan ulang dengan --apply untuk memindahkan file ke disk private.');

            return self::SUCCESS;
        }

        $moved = 0;
        foreach ($pending as $doc) {
            $path = (string) $doc->file_path;
            $stream = Storage::disk('public')->readStream($path);
            if ($stream === null) {
                $this->error("Gagal membaca {$path} untuk dokumen #{$doc->id}.");

                return self::FAILURE;
            }

            $written = Storage::disk('local')->writeStream($path, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }

            if (! $written) {
                $this->error("Gagal menyalin {$path} ke disk private.");

                return self::FAILURE;
            }

            Storage::disk('public')->delete($path);
            $moved++;
        }

        $this->info("{$moved} dokumen berhasil dipindahkan ke disk private (storage/app/private).");

        return self::SUCCESS;
    }
}
