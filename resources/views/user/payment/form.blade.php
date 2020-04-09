<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>クレジットカード登録</title>

  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

  <!-- Styles -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
  <div>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
      <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
          {{ __('クレジットカード情報') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <!-- Left Side Of Navbar -->
          <ul class="navbar-nav mr-auto">

          </ul>

          <!-- Right Side Of Navbar -->
          <ul class="navbar-nav ml-auto">
            <!-- Authentication Links -->
            @guest
            <li class="nav-item">
              <a class="nav-link" href="{{ route('login') }}">{{ __('ログイン') }}</a>
            </li>
            @if (Route::has('register'))
            <li class="nav-item">
              <a class="nav-link" href="{{ route('register') }}">{{ __('新規登録') }}</a>
            </li>
            @endif
            @else
            <li class="nav-item dropdown">
              <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->name }} <span class="caret"></span>
              </a>

              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route('logout') }}"
                onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
              </a>

              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </div>
          </li>
          @endguest
        </ul>
      </div>
    </div>
  </nav>

  <main class="py-4">
    <div class="container" id="app">
      <div class="row justify-content-center">
        <div class="col-md-8">
          @if (session('errors'))
          <div class="alert alert-danger" role="alert">
            {{ session('errors') }}
          </div>
          @endif
          <div class="card">
            <div class="card-header">クレジットカード登録</div>

            <div class="card-body">
              <div class="card-form" id="form_payment">
                @csrf
                <div class="form-group">
                 <div id="cardNumber"></div>
               </div>

               <div class="form-group">
                 <label for="name">カード名義</label>
                 <input type="text" id="cardName" class="form-control" value="" v-model="card_holder_name" placeholder="カード名義を入力" required>
               </div>
               <div class="form-group" v-if='!status'>
                 <button type="button" class="btn btn-primary" @click="subscribe">カードを登録する</button>
               </div>
               <div class="form-group" v-else>
                <button type="button" class="btn btn-success">登録が完了しました</button>
               </div>
             </form>
             <a href="{{route('user.payment')}}">クレジットカード情報ページに戻る</a>
           </div>
         </div>
       </div>
     </div>
   </div>
 </main>
</div>
<script src="https://js.stripe.com/v3/"></script>
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
<script>

  new Vue({
    el: '#app',
    data: {
                // init
                stripe: null,
                stripeCard: null,
                card_holder_name : '',
                plan: '',
                url: '{{ route('user.payment.store') }}',
                status: '',
                // php
                publicKey: '{{ config('services.stripe.key') }}',
                client_secret: '{{ $intent->client_secret }}'
              },
              methods: {
                subscribe() {

                    this.stripe.confirmCardSetup(
                        this.client_secret,
                        {
                            payment_method: {
                                card: this.stripeCard,
                                billing_details: { name: this.card_holder_name }
                            }
                        }
                    ).then((result) => {
                        
                        if (result.error) {  //　失敗
                            
                            console.log('登録失敗');

                        } else {   //　成功

                            const params = {
                                payment_method: result.setupIntent.payment_method
                            };

                            // 登録アクション
                            axios.post(this.url, params).then((result) => {
                              this.status = result.status;
                            });
                        }
                        
                    });
                    
                  },
                  setCard() {
                    // クレジットカードセット
                    Vue.nextTick(() => {
                      const selector = '#cardNumber';

                      this.stripeCard = this.stripe.elements().create('card', {
                        hidePostalCode: true
                      });
                      
                      this.stripeCard.mount(selector);

                    });
                  }
                },
                computed: {
                },
                watch: {
                },
                mounted() {
                // stripe.jsセット 
                this.stripe = Stripe(this.publicKey);
                this.setCard();
              }
            });

</script>
</body>
</html>