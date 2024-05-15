<div class="w-full p-6 lg:p-8">
    <x-filament::card>

    @php
        dd($getRecord())
    @endphp

    <table class="w-full divide-y divide-gray-200">
        <tr class="divide-gray-200">
            <td></td>
            <td>
                <img src="{{ asset('storage/'.$rent->car->image) }}" alt="image" class="w-25 h-25">
            </td>
        </tr>
        <tr class="divide-gray-200">
            <td>Code Rent</td>
            <td>{{ $rent->code_rent }}</td>
        </tr>
        <tr class="divide-gray-200">
            <td>Brand Name</td>
            <td>{{ $rent->car->brand_name }}</td>
        </tr>
        <tr class="divide-gray-200">
            <td>Customer Name</td>
            <td>{{ $rent->user->name }}</td>
        </tr>
        <tr class="divide-gray-200">
            <td>Start Price</td>
            <td>{{ $rent->start_price }}</td>
        </tr>
        <tr class="divide-gray-200">
            <td>Duration</td>
            <td>{{ $rent->duration }} days ({{ $rent->start_date }} s/d {{ $rent->end_date }})</td>
        </tr>
    </table>
    </x-filament::card>
</div>
