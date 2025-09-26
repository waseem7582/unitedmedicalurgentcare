<table class="custom-table booking-search-table">
    <thead>
        <tr>
            <th>{{ __('Booking ID') }}</th>
            <th>{{ __('Doctor Name') }}</th>
            <th>{{ __('Visit Type') }}</th>
            <th>{{ __('Patient Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Schedule') }}</th>
            <th>{{ __('Status') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transactions ?? [] as $item)

            <tr>
                <td>{{ $item->trx_id ?? '' }}</td>
                <td>{{ $item->doctor->name ?? '' }}</td>
                <td>{{ $item->booking_data->data->visit_type ?? '' }}</td>
                <td>{{ $item->booking_data->data->name ?? '' }}</td>
                <td>{{ $item->booking_data->data->email ?? '' }}</td>
                <td>{{ $item->date ?? '' }} ({{ $item->schedule->from_time }} -
                    {{ $item->schedule->to_time }})</td>
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
