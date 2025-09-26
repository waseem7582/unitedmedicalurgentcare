<!-- jquery -->
<script src="{{ asset('frontend/js/jquery-3.6.0.js') }}"></script>
<!-- bootstrap js -->
<script src="{{ asset('frontend/js/bootstrap.bundle.js') }}"></script>
<!-- swipper js -->
<script src="{{ asset('frontend/js/swiper.js') }}"></script>
<!-- lightcase js-->
<script src="{{ asset('frontend/js/lightcase.js') }}"></script>
<!-- odometer js -->
<script src="{{ asset('frontend/js/odometer.js') }}"></script>
<!-- viewport js -->
<script src="{{ asset('frontend/js/viewport.jquery.js') }}"></script>
<!-- smooth scroll js -->
<script src="{{ asset('frontend/js/smoothscroll.js') }}"></script>
<!-- nice select js -->
<script src="{{ asset('frontend/js/jquery.nice-select.js') }}"></script>
<!-- Select 2 JS -->
<script src="{{ asset('frontend/js/select2.js') }}"></script>
<!--  Popup -->
<script src="{{ asset('backend/library/popup/jquery.magnific-popup.js') }}"></script>
<script src="{{ asset('backend/library/popup/jquery.magnific-popup.min.js') }}"></script>
<!-- Apex Chart -->
<script src="{{ asset('frontend/js/apexcharts.js') }}"></script>
<!-- aos -->
<script src="{{ asset('frontend/js/aos.js') }}"></script>
<!-- pace -->
<script src="{{ asset('frontend/js/pace.js') }}"></script>
<!-- prettify -->
<script src="{{ asset('frontend/js/prettify.js') }}"></script>
<!-- viewport -->
<script src="{{ asset('frontend/js/viewport.jquery.js') }}"></script>
<!-- lightcase -->
<script src="{{ asset('frontend/js/lightcase.js') }}"></script>
<!-- lightcase -->
<script src="{{ asset('frontend/js/select2.js') }}"></script>
<!-- main -->
<script src="{{ asset('frontend/js/main.js') }}"></script>


<script>
    $(".langSel").on("change", function() {
       window.location.href = "{{route('frontend.index')}}/change/"+$(this).val();
   });
</script>



@include('admin.partials.notify')
