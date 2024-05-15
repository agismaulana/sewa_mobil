<div class="w-full p-6 lg:p-8">
    <x-filament::card>
    <table class="w-full divide-y divide-gray-200">
        <tr class="divide-gray-200">
            <td></td>
            <td>
                <img src="{{ asset('storage/'.$record->rent->car->image) }}" alt="image" class="w-25 h-25">
            </td>
        </tr>
        <tr class="divide-gray-200">
            <td>Code Number</td>
            <td>{{ $record->code_return }}</td>
        </tr>
        <tr class="divide-gray-200">
            <td>Brand Name</td>
            <td>{{ $record->rent->car->brand_name }}</td>
        </tr>
        <tr class="divide-gray-200">
            <td>Customer Name</td>
            <td>{{ $record->rent->user->name }}</td>
        </tr>
        <tr class="divide-gray-200">
            <td>Start Price</td>
            <td>{{ $record->rent->start_price }}</td>
        </tr>
        <tr class="divide-gray-200">
            <td>End Price</td>
            <td>{{ $record->price }} (Penalty : {{ $record->penalty }})</td>
        </tr>
        <tr class="divide-gray-200">
            <td>Duration</td>
            <td>{{ $record->rent->duration }} days ({{ $record->rent->start_date }} s/d {{ $record->rent->end_date }})</td>
        </tr>
    </table>
    </x-filament::card>
</div>
