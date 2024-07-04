<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SimpleTestCommand extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:hello';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Writes a simple message to the console and the log file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int {
        $message = now()->toDateTimeString() . ': Hello, this is a simple test command!';
        $this->info($message);
        Log::info($message);
        return 0;
    }

}