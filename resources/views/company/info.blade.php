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
              @foreach ($histories as $history)
                <tr>
                <td>{{$history['YEAR_NAME']}}</td>
                <td>{{$history['TOTAL_SUM_OF_EXHARST']}}</td>
                <td>{{$history['TOTAL_SUM_OF_ENERGY_CO2']}}</td>
                <td>{{$history['TOTAL_SUM']}}</td>
                <td>{{$history['ZOUGEN']}}% 
                @if ($history['ZOUGEN'] > 0)
                  <i class="fa fa-arrow-up"></i>
                @elseif ($history['ZOUGEN'] < 0)
                  <i class="fa fa-arrow-down"></i>
                @endif
                </td>
                @endforeach
              </tbody>
            </table>
          </section>
          <!-- /事業者届出履歴 -->
      <!-- 事業者排出量グラフ -->
      <section>
        <hr class="split">
  	    <div class="display-switch">
          <h3 class="result">事業者排出量グラフ(単位:tCO<sub>2</sub>)</h3>
          <div class="display">非表示にする</div>
        </div>
        <div class="graph">
          <canvas id="myChart"></canvas>
        </div>
      </section>
      <!-- /事業者排出量グラフ -->

      <!-- 事業所別内訳 -->
      <section>
        <hr class="split">
        <h3 class="result">事業者排出量内訳(単位:tCO<sub>2</sub>)</h3>
        <table id="resultTable" class="table table-bordered table-striped resultTable">
          <thead>
            <tr>
              <th>事業所名/業種</th>
              <th>年度</th>
              <th abbr="エネルギー起源CO2">エネ起</th>
              <th abbr="非エネルギー起源CO2">非エネ</th>
              <th abbr="非エネルギー廃棄物原燃">非エ廃</th>
              <th abbr="CH4">CH<sub>4</sub></th>
              <th abbr="N2O">N<sub>2</sub>O</th>
              <th abbr="HFC">HFC</th>
              <th abbr="PFC">PFC</th>
              <th abbr="SF6">SF<sub>6</sub></th>
              <th>合計<br>
                増減率</th>
            </tr>
          </thead>
          <tbody>
          @foreach ($discharges as $discharge)
            <tr>
              <td><a href="/factory/info?id={{$discharge['FACTORY_ID']}}" title="{{$discharge['FACTORY_NAME']}}の詳細へ">{{$discharge['FACTORY_NAME']}}</a><br>
                ({{$discharge['BUSINESS_TYPE']}})</td>
              <td>{{$discharge['REGIST_YEAR']}}年</td>
              <td>{{$discharge['ENERGY_CO2']}}</td>
              <td>{{$discharge['NO_ENERGY_CO2']}}</td>
              <td>{{$discharge['NO_ENERGY_DIS_CO2']}}</td>
              <td>{{$discharge['CH4']}}</td>
              <td>{{$discharge['N2O']}}</td>
              <td>{{$discharge['HFC']}}</td>
              <td>{{$discharge['PFC']}}</td>
              <td>{{$discharge['SF6']}}</td>
              <td>{{$discharge['SUM_OF_EXHARST']}}</br>
                {{$discharge['PRE_PERCENT']}}%
                @if ($discharge['PRE_PERCENT'] > 0)
                  <i class="fa fa-arrow-up"></i>
                @elseif ($discharge['PRE_PERCENT'] < 0)
                  <i class="fa fa-arrow-down"></i>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <p class="caution">※エネ起はエネルギー起源CO<sub>2</sub>、非エネは非エネルギー起源CO<sub>2</sub>、非エ廃は非エネルギー廃棄物原燃の略</p>
      </section>
      <!-- /事業所別内訳 -->
@endsection

@section('add_javascript')
<script>
  var ctx = document.getElementById("myChart").getContext('2d');
      // 6色
      var colors = ['rgba(70,132,238,0.6)', 'rgba(220,57,18,0.6)', 'rgba(255,153,0,0.6)', 'rgba(0,128,0,0.6)', 'rbga(73,66,204,0.6)', 'rgba(140,140,140,0.6)'];
  var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
          labels: [
            @foreach ($graph_labels as $graph_label)
            "{{$graph_label}}", 
            @endforeach
          ],
          datasets: [
          @foreach ($graph_datasets as $graph_dataset)
          {
              label: "{{$graph_dataset['NAME']}}",
              borderWidth:1,
              backgroundColor: ""+ colors[{{$graph_dataset['POS']}}] +"",
              data: [
                @foreach($graph_dataset['DATA'] as $graph_data)
                {{$graph_data}},
                @endforeach
                ]
          },
          @endforeach
        ]
      },
      options: {
          title: {
              display: true,
              text: '温室効果ガス別排出量合計', //グラフの見出し
              padding:3
          },
          scales: {
              xAxes: [{
                    stacked: true, //積み上げ棒グラフにする設定
                    categoryPercentage:0.4 //棒グラフの太さ
              }],
              yAxes: [{
                    stacked: true //積み上げ棒グラフにする設定
              }]
          },
          legend: {
              labels: {
                    boxWidth:30,
                    padding:20 //凡例の各要素間の距離
              },
              display: true
          },
          tooltips:{
            mode:'label' //マウスオーバー時に表示されるtooltip
          }
        }
      });
    </script>
@endsection