@extends('layouts.co2')
@section('title', '事業所排出情報 | 温室効果ガスデータベース by Tウォッチ')
@section('content')
      <!-- #breadcrumbs -->
      <ul id="breadcrumbs">
        <li><a href="{{url('/')}}">メニュー</a></li>
        <li>&gt; <a href="{{url('factory/search')}}">事業所検索</a></li>
        <li>&gt; <a href="{{url('factpry/list')}}">事業所リスト</a></li>
        <li>&gt; 事業所排出情報</li>
      </ul>
      <!-- /#breadcrumbs -->
          <!-- 事業所情報 -->
          <section>
            <div class="display-switch">
              <h3 class="result">事業所情報</h3>
              <div class="display">非表示にする</div>
            </div>
            <table id="companyTable" class="table table-bordered table-striped companyTable">
              <caption>事業者情報</caption>
              <tbody>
                <tr>
                <th>事業者名</th>
                <td>{{$factory->company->name}}</td>
                </tr>
                <tr>
                <th>住所</th>
                <td>{{$factory->company->address}}</td>
                </tr>
                <tr>
                <th>特定輸送者区分</th>
                <td>{{$factory->company->company_division->name}}</td>
                </tr>
                <tr>
                <th>PRTR届出</th>
                <td>
                @if($factory->company->getPrtrCo2()!=0)
                  <a href="http://xxxx.xxx.jp/{{$factory->company->getPrtrCo2()}}" target=”_blank”title="{{$factory->company->name}}のPRTR情報はこちら">{{$factory->company->name}}のPRTR情報はこちら</a>
                @else 
                  なし
                @endif   
                </td>
              </tbody>
            </table>

            <table id="factoryTable" class="table table-bordered table-striped companyTable">
              <caption>事業所情報</caption>
              <tbody>
                <tr>
                <th>事業所名</th>
                <td>{{$factory->name}}</td>
                </tr>
                <tr>
                <th>住所</th>
                <td>{{$factory->address}}</td>
                <tr>
                <tr>
                <th>業種</th>
                <td>{{$factory->business_type->name}}</td>
                </tr>
              </tbody>
            </table>
          </section>
          <!-- /事業所情報 -->
          <!-- 事業所届出履歴 -->
          <section>
            <hr class="split">
            <div class="display-switch">
              <h3 class="result">事業所届出履歴(単位:tCO<sub>2</sub>)
              </h3>
              <div class="display">非表示にする</div>
            </div>
            <table id="historyTable" class="table table-bordered table-striped historyTable">
              <thead>
                <tr>
                <th>届出年度</th>
                <th>合計</th>
                <th>増減率(％)</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($histories as $history)
                <tr>
                <td>{{$history['YEAR_NAME']}}</td>
                <td>{{$history['SUM_OF_EXHARST']}}</td>
                <td>
                @if ($history['PRE_PERCENT'] === -99999999.0)
                  -
                @else
                  {{$history['PRE_PERCENT']}}%
                  @if ($history['PRE_PERCENT'] > 0)
                    <i class="fa fa-arrow-up"></i>
                  @elseif ($history['PRE_PERCENT'] < 0)
                  <i class="fa fa-arrow-down"></i>
                  @endif
                @endif
                </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </section>
          <!-- /事業所届出履歴 -->

          <!-- 事業所排出量グラフ -->
          <section>
            <hr class="split">
            <div class="display-switch">
              <h3 class="result">事業所排出量グラフ(単位:tCO
                <sub>2</sub>)
              </h3>
              <div class="display">非表示にする</div>
            </div>
            <div class="graph">
              <canvas id="myChart"></canvas>
            </div>
          </section>
          <!-- /事業所排出量グラフ -->
          <!-- 事業所排出量内訳 -->
          <section>
            <hr class="split">
            <h3 class="result">事業所排出量内訳(単位:tCO <sub>2</sub>)
            </h3>
            <table id="resultTable" class="table table-bordered table-striped resultTable">
              <thead>
                <tr>
                <th>年度</th>
                <th abbr="エネルギー起源CO2">エネ起</th>
                <th abbr="非エネルギー起源CO2">非エネ</th>
                <th abbr="非エネルギー廃棄物原燃">非エ廃</th>
                <th abbr="CH4">CH<sub>4</sub></th>
                <th abbr="N2O">N<sub>2</sub>O</th>
                <th abbr="HFC">HFC</th>
                <th abbr="PFC">PFC</th>
                <th abbr="SF6">SF<sub>6</sub>
                </th>
                <th>合計
                  <br>増減率
                </th>
                </tr>
              </thead>
              <tbody>
              @foreach ($histories as $history)
                <tr>
                <td>{{$history['YEAR_NAME']}}</td>
                <td>{{$history['ENERGY_CO2']}}</td>
                <td>{{$history['NOENERGY_CO2']}}</td>
                <td>{{$history['NOENERGY_DIS_CO2']}}</td>
                <td>{{$history['CH4']}}</td>
                <td>{{$history['N2O']}}</td>
                <td>{{$history['HFC']}}</td>
                <td>{{$history['PFC']}}</td>
                <td>{{$history['SF6']}}</td>
                <td>
                {{$history['SUM_OF_EXHARST']}}<br>
                @if ($history['PRE_PERCENT'] === -99999999.0)
                  -
                @else
                  {{$history['PRE_PERCENT']}}%
                  @if ($history['PRE_PERCENT'] > 0)
                    <i class="fa fa-arrow-up"></i>
                  @elseif ($history['PRE_PERCENT'] < 0)
                  <i class="fa fa-arrow-down"></i>
                  @endif
                @endif
                </td>
                </tr>
              @endforeach
              </tbody>
            </table>
            <p class="caution">※エネ起はエネルギー起源CO
              <sub>2</sub>、非エネは非エネルギー起源CO
              <sub>2</sub>、非エ廃は非エネルギー廃棄物原燃の略
            </p>
          </section>
          <!-- /事業所排出量内訳 -->

@endsection

@section('add_javascript')

@endsection