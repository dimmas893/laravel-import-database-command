<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PDO;
use PDOException;

ini_set('memory_limit', '4G');
ini_set('max_execution_time', 86400);
ini_set('max_allowed_packet', '256M');
class ImportCommand extends Command
{
    protected $signature = 'import';
    protected $description = 'Command description';

    public function handle()
    {
        $this->info('Proses import');

        $filename = public_path('backup/backup.sql'); //file sql
        $mysql_host = '127.0.0.1'; // Ganti dengan host MySQL yang sesuai // ini merupakan database yang di tuju
        $mysql_username = 'root'; // Ganti dengan username MySQL yang sesuai // ini merupakan database yang di tuju
        $mysql_password = ''; // Ganti dengan password MySQL yang sesuai // ini merupakan database yang di tuju
        $mysql_database = 'voting'; // Ganti dengan nama database yang sesuai // ini merupakan database yang di tuju

        try {
            $pdo = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_username, $mysql_password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $templine = '';
            $lines = file($filename);

            foreach ($lines as $line) {
                if (substr($line, 0, 2) == '--' || trim($line) == '') {
                    continue;
                }

                $templine .= $line;

                if (substr(trim($line), -1, 1) == ';') {
                    try {
                        $pdo->exec($templine);
                    } catch (PDOException $e) {
                        echo 'Error performing query \'' . $templine . '\': ' . $e->getMessage() . '<br /><br />';
                    }
                    $templine = '';
                }
            }

            $this->info('Tables imported successfully');
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }

        $this->info('Selesai');
    }
}
