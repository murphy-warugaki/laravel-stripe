<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe_public_key = '{{ config('services.stripe.key') }}';
</script>
<script src="{{asset('js/payment.js')}}"></script>
