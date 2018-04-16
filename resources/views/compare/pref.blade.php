@extends('layouts.co2')
@section('title', '都道府県別比較 | 温室効果ガスデータベース by Tウォッチ')
@section('content')
      <!-- #breadcrumbs -->
      <ul id="breadcrumbs">
        <li><a href="{{url('/')}}">メニュー</a></li>
        <li>&gt; 都道府県別比較</li>
      </ul>
      <!-- /#breadcrumbs -->

        <section>
          <h2>都道府県別比較</h2>
          <!-- 比較フォーム -->
          <section>
            <div class="display-switch">
              <h3>集計条件</h3>
              <div class="display">非表示にする</div>
            </div>
            {!! Form::open(['url' => 'compare/Pref', 'method'=>'post', 'id'=>'search']) !!}
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th>{!! Form::label('pref', '都道府県') !!}</th>
                    <td>{!! Form::select('pref_id', $prefs, 0, ['class' => 'form', 'id' => 'pref_id']) !!}</td>
                  </tr>
                  <tr>
                    <th>{!! Form::label('regist_year', '年度') !!}</th>
                    <td>{!! Form::select('regist_year_id', $regist_years, 0, ['class' => 'form', 'id' => 'regist_year_id']) !!}</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="2" class="center">{!! Form::submit('集　計', ['class' => 'btn btn-warning']) !!}</td>
                  </tr>
                </tfoot>
              </table>
            {{ Form::close() }}
          </section>
          <!-- /比較フォーム -->
          <!-- 比較グラフ -->
          <section>
            <hr class="split">
            <div class="display-switch">
              <h3 class="result">比較グラフ(単位:tCO
                <sub>2</sub>)
              </h3>
              <div class="display">非表示にする</div>
            </div>
            <div class="graph">
              <canvas id="myChart"></canvas>
            </div>
          </section>
          <!-- /比較グラフ -->
          <!-- 比較結果リスト -->
          <section>
            <hr class="split">
            <h3 class="result">比較結果(単位:tCO
              <sub>2</sub>)
            </h3>
            <table id="resultTable" class="table table-bordered table-striped resultTable tablesorter-green">
              <thead>
                <tr>
                  <th>都道府県</th>
                  <th class="tablesorter-header">年度</th>
                  <th abbr="エネルギー起源CO2" class="tablesorter-header">エネ起</th>
                  <th abbr="非エネルギー起源CO2" class="tablesorter-header">非エネ</th>
                  <th abbr="非エネルギー廃棄物原燃" class="tablesorter-header">非エ廃</th>
                  <th abbr="CH4" class="tablesorter-header">CH<sub>4</sub></th>
                  <th abbr="N2O" class="tablesorter-header">N<sub>2</sub>O</th>
                  <th abbr="HFC" class="tablesorter-header">HFC</th>
                  <th abbr="PFC" class="tablesorter-header">PFC</th>
                  <th abbr="SF6" class="tablesorter-header">SF<sub>6</sub></th>
                  <th class="tablesorter-header">合計</th>
                  <th class="tablesorter-header">増減率</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($discharges as $discharge)
                <tr>
                  <td>
                  <a href="/compare/FactoryByPref?id={{$discharge['PREF_ID']}}&regist_year_id={{$discharge['YEAR_ID']}}" title="{{$discharge['PREF_NAME']}}の事業所へ">{{$discharge['PREF_NAME']}}</a>
                  </td>
                  <td>{{$discharge['YEAR_ID']}}年</td>
                  <td>{{$discharge['SUM_ENERGY_CO2']}}</td>
                  <td>{{$discharge['SUM_NOENERGY_CO2']}}</td>
                  <td>{{$discharge['SUM_NOENERGY_DIS_CO2']}}</td>
                  <td>{{$discharge['SUM_CH4']}}</td>
                  <td>{{$discharge['SUM_N2O']}}</td>
                  <td>{{$discharge['SUM_HFC']}}</td>
                  <td>{{$discharge['SUM_PFC']}}</td>
                  <td>{{$discharge['SUM_SF6']}}</td>
                  <td>{{$discharge['SUM_OF_EXHARST']}}</td>
                  <td>
                    @if ($discharge['PRE_PERCENT'] == -99999999 or $discharge['PRE_PERCENT'] == 0)
                      -
                    @else
                      {{$discharge['PRE_PERCENT']}}%
                      @if ($discharge['PRE_PERCENT'] > 0)
                        <i class="fa fa-arrow-up"></i>
                      @else
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
          <!-- /比較結果リスト -->
        </section>
@endsection

@section('add_javascript')
<script>
  	  var ctx = document.getElementById("myChart").getContext('2d');
      // 20色
      var colors = ['rgba(70,132,238,0.8)', 'rgba(220,57,18,0.8)', 'rgba(255,153,0,0.8)', 'rgba(0,128,0,0.8)', 'rgba(73,66,204,1.0)', 'rgba(229,46,184,0.8)', 'rgba(140,140,140,0.8)', 'rgba(46,115,229,0.5)', 'rgba(220,57,18,0.5)', 'rgba(255,173,51,0.5)', 'rgba(51,153,51,0.5)', 'rgba(73,66,204,0.5)', 'rgba(234,88,198,0.5)', 'rgba(140,140,140,0.5)', 'rgba(150,185,242,1.0)', 'rgba(220,57,18,0.2)', 'rgba(255,173,51,0.2)', 'rgba(51,153,51,0.2)', 'rgba(73,66,204,0.2)', 'rgba(234,88,198,0.2)'];
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
              text: '都道府県別温室効果ガス排出量合計', //グラフの見出し
              padding: 3
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
                    boxWidth: 20,
                    fontSize: 11,
                    padding: 10 //凡例の各要素間の距離
              },
              display: false
          },
          tooltips: {
            mode:'label' //マウスオーバー時に表示されるtooltip
          }
        }
  	  });    
  	</script>
@endsection