<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OfflineOrderWalletMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:offline_order_wallet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate wallet id into offline order table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->info('migration start...  ' . date('d M Y h:i a'));
            $this->migrateWallet();
            $this->info('migration complete!  ' . date('d M Y h:i a'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function migrateWallet()
    {
        \DB::statement("
            UPDATE order_offline a
            SET wallet_id = (
                SELECT
                    CASE
                        WHEN (wallet_id > 0) THEN wallet_id
                        WHEN ((v_token_log.wallet_id IS NULL or v_token_log.wallet_id = 0) and v_token_log.remark like '%Hemma Wallet%') THEN 99
                        ELSE 0
                    END as x
                FROM v_token_log
                WHERE offline_order_id = a.id
                GROUP BY offline_order_id
            )
            WHERE wallet_id = 0;
        ");
    }
}
