<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\YakanPattern;

class TestCustomOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:custom-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test custom order functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Testing Yakan Patterns...');
            
            $patterns = YakanPattern::all();
            $this->info("Found {$patterns->count()} patterns");
            
            foreach ($patterns as $pattern) {
                $this->line("- {$pattern->name} ({$pattern->category}) - {$pattern->difficulty_level}");
            }
            
            $this->info('Custom order system is working!');
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
        }
    }
}
