<?php

namespace App\Console\Commands;

use App\Models\Bank;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncDisbursementBanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:xendit-disbursement-banks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync available disbursement banks from Xendit API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('banks')->truncate();

        $this->info('Fetching bank list from Xendit...');

        try {
            $response = Http::withBasicAuth(env('XENDIT_SECRET_KEY'), '')
                ->get('https://api.xendit.co/available_disbursements_banks');

            if (!$response->successful()) {
                $this->error('Failed to fetch from Xendit: ' . $response->body());
                return 1;
            }

            $banks = $response->json();

            $allowedBanks = [
                'BCA',
                'BNI',
                'BRI',
                'MANDIRI',
                'PERMATA',
                'CIMB',
                'DANAMON',
            ];

            $filtered = collect($banks)->filter(function ($bank) use ($allowedBanks) {
                return in_array(strtoupper($bank['code']), $allowedBanks);
            });

            if ($filtered->isEmpty()) {
                $this->warn('No allowed banks found in the response.');
                return 0;
            }

            foreach ($filtered as $bank) {
                Bank::updateOrCreate(
                    ['code' => $bank['code']],
                    [
                        'name' => $bank['name'],
                        'can_disburse' => $bank['can_disburse'],
                        'can_name_validate' => $bank['can_name_validate'],
                        // 'is_deleted' => $bank['is_deleted'],
                    ]
                );
            }

            $this->info('Filtered national banks synced successfully.');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
