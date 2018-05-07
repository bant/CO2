<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" cntent="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | 温室効果ガスデータベース by Tウォッチ</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="{{url('/css/theme.green.css')}}" type="text/css" rel="stylesheet">
    <link href="{{url('/css/style.css')}}" type="text/css" rel="stylesheet">
</head>
<body>
<!-- ここからscreen -->
<div class="screen">
<!--- ここからヘッダ --->
    <header>
        @include('commons.header')
    </header>
 
    <!--- ここから本文 --->
    <div id="contents">
        <section>
          <h2>@yield('message')</h2>
          <div id="error-box" class="row">
            <div class="col-xs-offset-3 col-xs-6" id="error-link">
                @yield('detail')</br>
                @yield('link')
            </div>
          </div>
          <!-- #error+box -->
          </section>
    </div>

    <!----【追加分】----->
    <!--- ここからフッタ --->
    <footer id="footer">
        @yield('add_footer')
        <address id="address">@include('commons.footer',['year'=>Carbon\Carbon::now()->format('Y')])</address>
    </footer>
</div>
<!-- ここまでscreen -->

<!-- 以下、jsの読み込み -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<!-- ここまでjsの読み込み -->
</body>
</html>