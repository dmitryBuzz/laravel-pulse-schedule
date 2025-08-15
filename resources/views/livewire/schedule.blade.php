<x-pulse::card :cols="$cols" :rows="$rows" :class="$class" style="min-height: 475px;">
    <x-pulse::card-header name="Schedule">
        <x-slot:icon>
            <x-pulse::icons.command-line />
        </x-slot:icon>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        @empty($events)
            <x-pulse::no-results />
        @else
            <x-pulse::table>
                <tbody>
                @foreach($events as $event)
                    <tr wire:key="{{ $event['command'] }}-row">
                        <x-pulse::td style="padding: 0;">
                            <x-pulse::table class="min-w-full border-collapse">
                                <tbody>
                                @foreach([
                                    'Command' => $event['command'],
                                    'Description' => $event['description'],
                                    'File Path' => $event['file_path'],
                                    'Schedule' => $event['schedule'],
                                    'Next Run' => $event['next_run'],
                                    'Timezone' => $event['timezone'],
                                    'Environment' => $event['environment'],
                                ] as $key => $value)
                                    <tr>
                                        <x-pulse::td class="pr-4" style="width: 150px; min-width: 150px; font-weight: 600; padding: 3px 5px;">{{ $key }}</x-pulse::td>
                                        <x-pulse::td style="padding: 2px 5px;">{{ $value }}</x-pulse::td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </x-pulse::table>
                        </x-pulse::td>
                    </tr>
                    <tr class="h-2 first:h-0" wire:key="{{ $event['command'] }}-spacer"></tr>
                @endforeach
                </tbody>
            </x-pulse::table>
        @endempty
    </x-pulse::scroll>
</x-pulse::card>
