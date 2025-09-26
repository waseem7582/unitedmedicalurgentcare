<table class="custom-table booking-search-table">
    <thead>
        <tr>
            <th>{{ __('Booking ID') }}</th>
            <th>{{ __('Hospital Name') }}</th>
            <th>{{ __('Time') }}</th>
            <th>{{ __('Shift') }}</th>
            <th>{{ __('Patient Name') }}</th>
            {{-- <th>{{ __('Service') }}</th> --}}
            <th>{{ __('Email') }}</th>
            <th>{{ __('Schedule') }}</th>
            <th>{{ __('Status') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @php
    use Illuminate\Support\Facades\DB;
@endphp
        @forelse ($transactions ?? [] as $item)
        @php
        $investigationIds = $item->booking_data->data->investigations ?? [];
        $investigationNames = DB::table('investigations')
            ->whereIn('id', $investigationIds)
            ->pluck('name')
            ->toArray();
    @endphp

            <tr>
                <td>{{ $item->trx_id ?? '' }}</td>
                <td>{{ $item->hospital->hospital_name ?? '' }}</td>
                <td>{{ $item->booking_data->data->time ?? '' }}</td>
                <td>{{ $item->booking_data->data->shift ?? '' }}</td>
                <td>{{ $item->booking_data->data->name ?? '' }}</td>
                {{-- <th>{{ implode(', ', $investigationNames) }}</th> --}}
                <td>{{ $item->booking_data->data->email ?? '' }}</td>
                <td>{{ $item->date ?? '' }}</td>
                <td>
                    <span
                        class="{{ $item->stringStatus->class }}">{{ __($item->stringStatus->value) }}</span>
                </td>
            </tr>
        @empty
            <td colspan="7">
                <div style="margin-top: 37.5px" class="alert alert-primary w-100 text-center">
                    {{ __('No Record Found!') }}
                </div>
            </td>
        @endforelse
    </tbody>
</table>
