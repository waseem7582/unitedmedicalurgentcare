<table class="custom-table currency-search-table">
    <thead>
        <tr>
            <th></th>
            <th>{{ __("Name") }} | {{ __("Code") }}</th>
            <th>{{ __("Symbol") }}</th>
            <th>{{ __("Status") }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($currencies ?? [] as $item)
            <tr data-item="{{ $item->editData }}">
                <td>
                    <ul class="user-list">
                        <li><img src="{{ get_image($item->flag,'currency-flag') }}" alt="flag"></li>
                    </ul>
                </td>
                <td>{{ $item->name }}
                    @if ($item->default)
                        <span class="badge badge--success ms-1">{{ __("Default") }}</span>
                    @endif
                    <br> <span>{{ $item->code }}</span></td>
                <td>{{ $item->symbol }}</td>
                <td><span class="text--info">{{ $item->type }}</span> <br> 1 {{ get_default_currency_code($default_currency) }} = {{ get_amount($item->rate,$item->code) }}</td>
                <td>
                    @include('admin.components.link.edit-default',[
                        'href'          => "javascript:void(0)",
                        'class'         => "edit-modal-button",
                        'permission'    => "admin.currency.update",
                    ])
             
                </td>
            </tr>
        @empty
            @include('admin.components.alerts.empty',['colspan' => 7])
        @endforelse
    </tbody>
</table>

@push("script")
    <script>
        $(document).ready(function(){
            // Switcher
            switcherAjax("{{ setRoute('admin.currency.status.update') }}");
        })
    </script>
    <script>
        $(document).on('change','#select-all', function () {
            let isChecked = $(this).is(':checked');
            $('input[name="select_currency[]"]').prop('checked', isChecked);
            toggleActionBtn();
        });
        $(document).on('change', 'input[name="select_currency[]"]', function () {
            let total = $('input[name="select_currency[]"]').length;
            let checked = $('input[name="select_currency[]"]:checked').length;
            $('#select-all').prop('checked', total === checked);
            toggleActionBtn();
        });
        function toggleActionBtn() {
            let selectedCount = $('input[name="select_currency[]"]:checked').length;
            if (selectedCount > 0) {
                $('.action-btn-wrapper').removeClass('d-none');
            } else {
                $('.action-btn-wrapper').addClass('d-none');
            }
        }
        $(document).ready(function () {
            $('.action-btn-wrapper').addClass('d-none');
            switcherAjax("{{ setRoute('admin.currency.status.update') }}");
        });
    </script>

@endpush
