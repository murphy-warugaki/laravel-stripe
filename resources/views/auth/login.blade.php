@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3>ようこそUnitoへ</h3>
            <div style="margin-top: 10px; color: #F86262">
                <p style="margin-bottom: 5px;">申し込み情報をメールで送信しました</p>
                <p>まだ入居は確定していません、先ほどのメールアドレスとパスワードを入力してください</p>
            </div>
            <div class="card">
                <div class="card-header">クレジットカードを登録する</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('メールアドレス') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('パスワード') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- 
                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        -->

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary" style="background-color: #FF9C9C; border-color: #FF9C9C;">
                                    ログインする
                                </button>

                                @if (Route::has('password.request'))
                                <!-- 
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                -->
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div style="margin: 20px auto;">
                <p style="color: #4B4B4B;">お申し込みフォームで入力した、メールアドレス・パスワードでログインできます。<br> 上記より会員登録をいただき、家賃の支払い用のクレジットカードをご登録ください。<br>空室確認後、入居時にかかる費用をお支払いいただいた後、ご入居確定となります。</p>
            </div>
        </div>
    </div>
</div>
@endsection
