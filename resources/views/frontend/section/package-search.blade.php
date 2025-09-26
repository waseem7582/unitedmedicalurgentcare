
<section class="find-doctor-section pt-60">
    <div class="banner-flotting-section">
        <form class="banner-flotting-item-form" action="{{ setRoute('frontend.package.search') }}" method="GET">
            @csrf
        <div class="container">
            <div class="banner-flotting-item">
                <div class="row mb-20-none justify-content-center">
                    <div class="col-lg-9 col-md-9 col-sm-8 mb-20">
                        <div class="search-investigation">
                            <input type="text" name="name" value="{{ @$nameString }}" class="form--control" placeholder="Search...">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-4 mb-20">
                        <div class="search-btn-area">
                            <button type="submit" class="btn--base search-btn w-100"><i class="fas fa-search me-1"></i>
                                {{ __("Search") }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
</section>
@push('script')


    <script>

    </script>
@endpush
