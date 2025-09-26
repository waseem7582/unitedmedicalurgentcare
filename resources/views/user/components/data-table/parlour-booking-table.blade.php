<table class="custom-table booking-search-table">
    <thead>
        <tr>
            <th>{{ __('Booking ID') }}</th>
            <th>{{ __('Parlour Name') }}</th>
            <th>{{ __('Payment Type') }}</th>
            <th>{{ __('Service') }}</th>
            <th>{{ __('Schedule') }}</th>
            <th>{{ __('Price') }}</th>
            <th>{{ __('Status') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transactions ?? [] as $item)
            <tr>
                <td>{{ $item->trx_id ?? '' }}</td>
                <td>{{ $item->parlour->name ?? '' }}</td>
                <td>{{ $item->type ?? '' }}</td>
                <td>{{ is_array($item->service) ? implode(', ', $item->service) : $item->service }}</td>
                <td>{{ $item->date ?? '' }} ({{ $item->schedule->from_time }} -
                    {{ $item->schedule->to_time }})</td>
                <td>{{ get_default_currency_symbol() }}{{ get_amount($item->price) }}</td>
                <td>
                    <span
                        class="{{ $item->stringStatus->class }}">{{ __($item->stringStatus->value) }}</span>
                </td>
                <td><a href="{{ setRoute('user.my.booking.details', $item->slug) }}"
                        class="btn btn--base btn--primary"><i class="fas fa-eye"></i></a></td>
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
