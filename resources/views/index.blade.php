@extends('layouts.co2')
@section('title', 'メニュー')
@section('content')
<div id="contents">
<!--- #contents --->
      <ul id="breadcrumbs">
        <li>メニュー</li>
      </ul>
      <!-- /#breadcrumbs -->
      <section>
        <h2>検索</h2>
        <div id="menu">
          <a href="{{url('/company/search')}}" title="事業者検索">
            <button class="btn btn-warning btn-block btn-lg">
              <i class="fa fa-building" aria-hidden="true"></i>事業者検索
            </button>
          </a>
          <p>事業者名・住所から検索します。</p>
          <a href="{{url('/factory/search')}}" title="事業所検索">
            <button class="btn btn-warning btn-block btn-lg">
              <i class="fa fa-industry" aria-hidden="true"></i>事業所検索
            </button>
          </a>
          <p>事業所名・住所から検索します。</p>
        </div>
        <!-- #menu -->
      </section>

      <section>
      <h2>比較</h2>
        <div id="menu">
          <a href="{{url('/compare/MajorBusinessType')}}" title="業種別比較">
            <button class="btn btn-warning btn-block btn-lg">
              <i class="fa fa-balance-scale" aria-hidden="true"></i>業種別比較
            </button>
          </a>
          <p>排出量を業種別に集計し、比較します。</p>
          <a href="{{url('/compare/CompanyDivision')}}" title="輸送者別比較">
            <button class="btn btn-warning btn-block btn-lg">
              <i class="fa fa-balance-scale" aria-hidden="true"></i>輸送者別比較
            </button>
          </a>
          <p>排出量を輸送事業者の指定区分別に集計し、比較します。</p>
          <a href="{{url('/compare/Pref')}}" title="都道府県別比較">
            <button class="btn btn-warning btn-block btn-lg">
              <i class="fa fa-balance-scale" aria-hidden="true"></i>都道府県別比較
            </button>
          </a>
          <p>排出量を都道府県別に集計し、比較します。</p>
          <a href="{{url('/compare/Gas')}}" title="温室効果ガス別比較">
            <button class="btn btn-warning btn-block btn-lg">
              <i class="fa fa-balance-scale" aria-hidden="true"></i>温室効果ガス別比較
            </button>
          </a>
          <p>排出量を温室効果ガス別に集計し、比較します。</p>         
        </div>
        <!-- #menu -->
      </section>
      <div id="caution">
      温室効果ガスデータベースは、「地球温暖化対策の推進に関する法律（温対法）」に基づき、全国の特定排出者(温室効果ガスを相当程度多く排出する者)が届出した温室効果ガスの排出データを、Tウォッチがデータベース化したものです。<br>
      なお、本データベースは2019年度「地球環境基金」の助成を受けています。
      </div>
    </div>
<!--- /#contents --->
@endsection

@section('add_footer')
      <div id="footer-link"><a href="https://www.toxwatch.net"><i class="fa fa-external-link" aria-hidden="true"></i> Tウォッチホームページ</a> | <a href="https://prtr.toxwatch.net"><i class="fa fa-external-link" aria-hidden="true"></i>PRTRデータベース</a></div>
@endsection