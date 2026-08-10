<?php

namespace App\Console\Commands;

use App\Models\Companies\Company;
use App\Models\Settings\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateInvoiceNumbersCommand extends Command
{
    protected $signature = 'invoice:generate-numbers {--count=10 : Number of invoice numbers to pre-generate}';

    protected $description = 'Pre-generate invoice numbers for all companies';

    public function handle(): int
    {
        $count = $this->option('count');
        $companies = Company::where('status', 'active')->get();

        $bar = $this->output->createProgressBar($companies->count());

        foreach ($companies as $company) {
            $prefix = Setting::where('company_id', $company->id)
                ->where('key', 'invoice_number_prefix')
                ->value('value') ?? 'INV-';

            $startNumber = (int) Setting::where('company_id', $company->id)
                ->where('key', 'invoice_number_start')
                ->value('value') ?? 1000;

            $sequence = Setting::where('company_id', $company->id)
                ->where('key', 'invoice_number_sequence')
                ->first();

            if (!$sequence) {
                $sequence = Setting::create([
                    'id' => Str::uuid(),
                    'company_id' => $company->id,
                    'group' => 'invoice',
                    'key' => 'invoice_number_sequence',
                    'value' => $startNumber,
                    'type' => 'integer',
                    'is_public' => false,
                ]);
            }

            $currentNumber = (int) $sequence->value;

            for ($i = 0; $i < $count; $i++) {
                $invoiceNumber = $prefix . str_pad($currentNumber + $i, 6, '0', STR_PAD_LEFT);
                $this->line("Generated: {$invoiceNumber} for {$company->name}");
            }

            $sequence->update(['value' => $currentNumber + $count]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully pre-generated {$count} invoice numbers for " . $companies->count() . " companies.");

        return Command::SUCCESS;
    }
}
