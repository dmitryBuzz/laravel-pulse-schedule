<?php

declare(strict_types=1);

namespace HosmelQ\Laravel\Pulse\Schedule\Livewire;

use Illuminate\Console\Scheduling\ScheduleListCommand;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class Schedule extends Card
{
    public function render(): View
    {
        ScheduleListCommand::resolveTerminalWidthUsing(fn (): int => 120);

        Artisan::call('schedule:list');

        ScheduleListCommand::resolveTerminalWidthUsing(null);

        $outputLines = explode("\n", Artisan::output());

        $commands = [];
        $current = null;

        foreach ($outputLines as $line) {
            $line = trim($line);

            // Skip empty lines
            if ($line === '') {
                continue;
            }

            // Detect start of new command block
            if (preg_match('/^#(\d+):\s*(.+)$/', $line, $matches)) {
                // If we were collecting a previous block, save it
                if ($current) {
                    $commands[] = $current;
                }

                $current = [
                    'id' => (int) $matches[1],
                    'description' => $matches[2],
                    'command' => null,
                    'file_path' => null,
                    'schedule' => null,
                    'next_run' => null,
                    'timezone' => null,
                    'environment' => null,
                ];

                continue;
            }

            // Match table rows like: | Command | cache:prune-stale-tags |
            if (preg_match('/^\|\s*Command\s*\|\s*(.+?)\s*\|$/', $line, $matches)) {
                $current['command'] = $matches[1];
            }
            elseif (preg_match('/^\|\s*File Path\s*\|\s*(.+?)\s*\|$/', $line, $matches)) {
                $current['file_path'] = $matches[1];
            }
            elseif (preg_match('/^\|\s*Schedule\s*\|\s*(.+?)\s*\|$/', $line, $matches)) {
                $current['schedule'] = $matches[1];
            }
            elseif (preg_match('/^\|\s*Next Run\s*\|\s*(.+?)\s*\|$/', $line, $matches)) {
                $current['next_run'] = $matches[1];
            }
            elseif (preg_match('/^\|\s*Timezone\s*\|\s*(.+?)\s*\|$/', $line, $matches)) {
                $current['timezone'] = $matches[1];
            }
            elseif (preg_match('/^\|\s*Environment\s*\|\s*(.+?)\s*\|$/', $line, $matches)) {
                $current['environment'] = $matches[1];
            }
        }

        // Add last block
        if ($current) {
            $commands[] = $current;
        }

//        dd($commands);

        return view('pulse-schedule::livewire.schedule', [
            'events' => $commands,
        ]);
    }
}
