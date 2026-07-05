<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\BackupService;
use App\Services\AuditService;

class BackupController extends Controller
{
    public function index(): void
    {
        $this->authorize(['super_admin']);
        $backups = BackupService::listBackups();

        $this->view('dashboard.backups', [
            'pageTitle' => 'Backups',
            'backups' => $backups,
        ]);
    }

    public function create(): void
    {
        $this->authorize(['super_admin']);

        $dbBackup = BackupService::backupDatabase();
        $uploadsBackup = BackupService::backupUploads();

        $msg = [];
        if ($dbBackup) $msg[] = "DB backup: {$dbBackup}";
        if ($uploadsBackup) $msg[] = "Uploads backup: {$uploadsBackup}";

        if (empty($msg)) {
            $this->withFlash('error', 'Backup failed.');
        } else {
            AuditService::log('backup_created', 'system', null, null, implode(', ', $msg));
            $this->withFlash('success', 'Backup created! ' . implode(' | ', $msg));
        }

        $this->redirect('/admin/backups');
    }
}
