@extends('layouts.co2')
@section('title', '事業者検索 | 温室効果ガスデータベース by Tウォッチ')
@section('content')
      <!-- #breadcrumbs -->
      <ul id="breadcrumbs">
        <li><a href="{{url('/')}}">メニュー</a></li>
        <li><a href="{{url('company/search')}}">事業者検索</a></li>
        <li><a href="{{url('company/list')}}">事業者リスト</a></li>
        <li>&gt; 事業者情報</li>
      </ul>
      <!-- /#breadcrumbs -->

        <!-- 事業者届出情報 -->
        <section>
          <h2>事業者届出情報</h2>
          <!-- 事業者情報 -->
          <section>
            <div class="display-switch">
              <h3 class="result">事業者情報</h3>
              <div class="display">非表示にする</div>
            </div>
            <table id="companyTable" class="table table-bordered table-striped companyTable">
              <caption>事業者情報</caption>
              <tbody>
                <tr>
                <th>事業者名</th>
                <td>{{$company->name}}</td>
                </tr>
                <tr>
                <th>住所</th>
                <td>{{$company->address}}</td>
                </tr>
                <tr>
                <th>特定輸送者区分</th>
                <td>{{$company->company_division->name}}</td>
                </tr>
                <tr>
                <th>PRTR届出</th>
                <td>
                @if($company->getPrtrCo2()!=0)
                  <a href="http://xxxx.xxx.jp/{{$company->getPrtrCo2()}}" target=”_blank”title="{{$company->name}}のPRTR情報はこちら">{{$company->name}}のPRTR情報はこちら</a>
                @else 
                  なし
                @endif                  
                </td>
              </tbody>
            </table>
          </section>
          <!-- /事業者情報 -->

          <!-- 事業者届出履歴 -->
          <section>
            <hr class="split">
            <div class="display-switch">
              <h3 class="result">事業者届出履歴(単位:tCO
                <sub>2</sub>)
              </h3>
              <div class="display">非表示にする</div>
            </div>
            <table id="historyTable" class="table table-bordered table-striped historyTable">
              <thead>
                <tr>
                <th>年度</th>
                <th>事業者排出量</th>
                <th>輸送排出量</th>
                <th>合計</th>
                <th>増減率(%)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                <td>2014</td>
                <td>17615789</td>
                <td>204941</td>
                <td>17820730</td>
                <td>-0% 
                  <i class="fa fa-arrow-down"></i>
                </td>
                </tr>
                <tr>
                <td>2013</td>
                <td>17619725</td>
                <td>201471</td>
                <td>17821196</td>
                <td>5.98% 
                  <i class="fa fa-arrow-up"></i>
                </td>
                </tr>
                <tr>
                <td>2012</td>
                <td>16561048</td>
                <td>195252</td>
                <td>16756300</td>
                <td>-1.82% 
                  <i class="fa fa-arrow-down"></i>
                </td>
                </tr>
                <tr>
                <td>2011</td>
                <td>16904777</td>
                <td>156329</td>
                <td>17061106</td>
                <td>-2.96% 
                  <i class="fa fa-arrow-down"></i>
                </td>
                </tr>
                <tr>
                <td>2010</td>
                <td>17411132</td>
                <td>154943</td>
                <td>17566075</td>
                <td>8.75% 
                  <i class="fa fa-arrow-up"></i>
                </td>
                </tr>
                <tr>
                <td>2009</td>
                <td>15890365</td>
                <td>138207</td>
                <td>16028572</td>
                <td>-8.54% 
                  <i class="fa fa-arrow-down"></i>
                </td>
                </tr>
                <tr>
                <td>2008</td>
                <td>17220320</td>
                <td>177686</td>
                <td>17398006</td>
                <td>-4.89% 
                  <i class="fa fa-arrow-down"></i>
                </td>
                </tr>
                <tr>
                <td>2007</td>
                <td>18060793</td>
                <td>188791</td>
                <td>18249584</td>
                <td>94.47% 
                  <i class="fa fa-arrow-up"></i>
                </td>
                </tr>
                <tr>
                <td>2006</td>
                <td>835582</td>
                <td>173946</td>
                <td>1009528</td>
                <td>-</td>
                </tr>
              </tbody>
            </table>
          </section>
          <!-- /事業者届出履歴 -->

@endsection