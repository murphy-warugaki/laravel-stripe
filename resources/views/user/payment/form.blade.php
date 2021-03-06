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
        <a class="navbar-brand" href="{{ url('/home') }}">
          <img src="{{ asset('logo-c.png') }}" style="width: 30px;"><span style="display: inline;"> Unito</span>
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
              <a role="button">
                {{ Auth::user()->name }}
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
          <div class="col-md-12 alert alert-danger" role="alert" v-if="error.length">
            @{{ error }}
          </div>
          <div class="col-md-12 alert alert-info" role="alert" v-if="status.length">
            @{{ status }}
          </div>
        <div class="col-md-8">
          <div style="margin: 30px auto;">
            <h2>{{ Auth::user()->name }}さんとしてログイン中</h2>
          </div>
          
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
                 <button type="button" class="btn btn-primary" @click="subscribe" style="background-color: #FF9C9C; border-color: #FF9C9C; width: 40%;">カードを登録する</button>
               </div>
               <div class="form-group" v-else>
                <button type="button" class="btn btn-success">登録が完了しました</button>
               </div>
             </form>
             <!--
              <a href="{{route('user.payment')}}">クレジットカード情報ページに戻る</a>
            -->
           </div>
         </div>
       </div>
       <div style="margin: 20px auto;">
        <p>CVCは、デビットカードやクレジットカードの裏面に記載されている3桁の番号です。</p>
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
                error: '',
                // php
                publicKey: '{{ config('services.stripe.key') }}',
                client_secret: '{{ $intent->client_secret }}'
              },
              methods: {
                subscribe() {
                  this.error = '';
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
                            
                            this.error = '登録に失敗しました';

                        } else {   //　成功

                            const params = {
                                payment_method: result.setupIntent.payment_method
                            };

                            // 登録アクション
                            axios.post(this.url, params).then((result) => {
                              if (result.data.code == 400) {
                                this.error = result.data.message;
                                return;
                              }

                              this.status = 'クレジットカードの登録が完了しました';
                            });
                        }
                        
                    });
                    
                  },
                  setCard() {
                    // クレジットカードセット
                    Vue.nextTick(() => {
                      const selector = '#cardNumber';
                      const style = {
                          base: {
                              fontSize: '12px',
                              color: "#32325d",
                              border: "solid 1px ccc"
                          }
                      };

                      const classes = {
                          base: "form-control"
                      };

                      this.stripeCard = this.stripe.elements().create('card', {
                        style:style,
                        classes:classes,
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