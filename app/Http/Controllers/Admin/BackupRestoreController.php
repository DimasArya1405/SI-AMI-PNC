<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class BackupRestoreController extends Controller
{
    private string $backupDirectory = 'backups/database';

    public function index()
    {
        $backups = collect(File::files($this->backupPath()))
            ->filter(fn ($file) => $file->getExtension() === 'sql')
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'created_at' => Carbon::createFromTimestamp($file->getMTime())
                    ->locale('id')
                    ->translatedFormat('d F Y H:i'),
            ])
            ->sortByDesc('name')
            ->values();

        return view('admin.backup-restore.index', compact('backups'));
    }

    public function store()
    {
        try {
            $filename = $this->createBackupFile('backup');

            return back()->with('success', "Backup database berhasil dibuat: {$filename}");
        } catch (\Throwable $e) {
            return back()->with('error', 'Backup database gagal: ' . $e->getMessage());
        }
    }

    public function download(string $filename)
    {
        $path = $this->resolveBackupFile($filename);

        abort_unless($path && File::exists($path), 404);

        return response()->download($path);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'max:51200'],
        ], [
            'backup_file.required' => 'File backup wajib dipilih.',
            'backup_file.file' => 'File backup tidak valid.',
            'backup_file.max' => 'Ukuran file backup maksimal 50 MB.',
        ]);

        $extension = strtolower($request->file('backup_file')->getClientOriginalExtension());

        if (!in_array($extension, ['sql', 'txt'], true)) {
            return back()->with('error', 'Tipe file tidak didukung. Gunakan file SQL atau TXT.');
        }

        try {
            set_time_limit(0);

            $safetyBackup = $this->createBackupFile('sebelum-restore');
            $sql = File::get($request->file('backup_file')->getRealPath());

            if (!str_contains(strtolower($sql), 'create table') && !str_contains(strtolower($sql), 'insert into')) {
                return back()->with('error', 'File restore tidak terlihat seperti file backup database SIAMI.');
            }

            DB::unprepared($sql);

            return back()->with('success', "Restore database berhasil. Backup sebelum restore dibuat: {$safetyBackup}");
        } catch (\Throwable $e) {
            return back()->with('error', 'Restore database gagal: ' . $e->getMessage());
        }
    }

    public function destroy(string $filename)
    {
        $path = $this->resolveBackupFile($filename);

        if (!$path || !File::exists($path)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        File::delete($path);

        return back()->with('success', 'File backup berhasil dihapus.');
    }

    private function createBackupFile(string $prefix): string
    {
        set_time_limit(0);
        File::ensureDirectoryExists($this->backupPath());

        $database = DB::getDatabaseName();
        $filename = Str::slug($prefix . '-' . $database) . '-' . now()->format('Ymd-His') . '.sql';
        $path = $this->backupPath($filename);

        $handle = fopen($path, 'w');

        if (!$handle) {
            throw new \RuntimeException('Tidak dapat membuat file backup.');
        }

        fwrite($handle, "-- Backup Database SIAMI\n");
        fwrite($handle, "-- Database: {$database}\n");
        fwrite($handle, "-- Dibuat pada: " . now()->locale('id')->translatedFormat('d F Y H:i:s') . "\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");

        foreach ($this->getTables() as $table) {
            $wrappedTable = $this->wrapIdentifier($table);
            $createTable = $this->getCreateTableStatement($table);

            fwrite($handle, "\n-- Struktur tabel {$table}\n");
            fwrite($handle, "DROP TABLE IF EXISTS {$wrappedTable};\n");
            fwrite($handle, $createTable . ";\n\n");

            fwrite($handle, "-- Data tabel {$table}\n");
            foreach (DB::table($table)->cursor() as $row) {
                $row = (array) $row;

                if ($row === []) {
                    continue;
                }

                $columns = collect(array_keys($row))
                    ->map(fn ($column) => $this->wrapIdentifier($column))
                    ->implode(', ');

                $values = collect(array_values($row))
                    ->map(fn ($value) => $this->quoteValue($value))
                    ->implode(', ');

                fwrite($handle, "INSERT INTO {$wrappedTable} ({$columns}) VALUES ({$values});\n");
            }
        }

        fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);

        return $filename;
    }

    private function getTables(): array
    {
        $database = DB::getDatabaseName();

        return collect(DB::select(
            'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = ? ORDER BY TABLE_NAME',
            [$database, 'BASE TABLE']
        ))
            ->pluck('TABLE_NAME')
            ->values()
            ->all();
    }

    private function getCreateTableStatement(string $table): string
    {
        $result = (array) DB::selectOne('SHOW CREATE TABLE ' . $this->wrapIdentifier($table));

        return $result['Create Table'] ?? throw new \RuntimeException("Gagal membaca struktur tabel {$table}.");
    }

    private function quoteValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if ($value instanceof \DateTimeInterface) {
            $value = $value->format('Y-m-d H:i:s');
        }

        return DB::getPdo()->quote((string) $value);
    }

    private function wrapIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    private function backupPath(?string $filename = null): string
    {
        $basePath = storage_path('app/' . $this->backupDirectory);
        File::ensureDirectoryExists($basePath);

        return $filename ? $basePath . DIRECTORY_SEPARATOR . basename($filename) : $basePath;
    }

    private function resolveBackupFile(string $filename): ?string
    {
        $filename = basename($filename);

        if (!Str::endsWith($filename, '.sql')) {
            return null;
        }

        return $this->backupPath($filename);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}
