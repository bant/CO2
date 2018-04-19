@extends('layouts.co2')
@section('title', '温室効果ガス別比較 | 温室効果ガスデータベース by Tウォッチ')
@section('content')
      <!-- #breadcrumbs -->
      <ul id="breadcrumbs">
        <li><a href="{{url('/')}}">メニュー</a></li>
        <li>&gt; 温室効果ガス別比較</li>
      </ul>
      <!-- /#breadcrumbs -->

      <section>
      <h2>温室効果ガス別比較</h2>
        <!-- 比較フォーム -->
        <section>
  	    <div class="display-switch">
          <h3>集計条件</h3>
          <div class="display">非表示にする</div>
        </div>
        {!! Form::open(['url' => 'compare/Gas', 'method'=>'post', 'id'=>'search']) !!}
          <table class="table table-bordered">
            <tbody>
              <tr>
                <th>{!! Form::label('gas', '指定区分') !!}</th>
                <td>{!! Form::select('gas_id', 
                [
                  'all' => '全ガス',
                  'energy_co2' => 'エネルギー起源CO2',
                  'noenergy_co2' => '非エネルギー起源CO2',
                  'noenergy_dis_co2' => '非エネルギー起源CO2(廃棄物の原燃料使用)',
                  'ch4' => 'CH4',
                  'n2o' => 'N2O',
                  'hfc' => 'HFC',
                  'pfc' => 'PFC',
                  'sf6' => 'SF6',
                  'power_plant_energy_co2' => 'エネルギー起源CO2(発電所等配分前)'
                ], 0, ['class' => 'form', 'id' => 'gas_id']) !!}</td>
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
          <h3 class="result">比較グラフ(単位:tCO<sub>2</sub>)</h3>
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
        <h3 class="result">比較結果(単位:tCO<sub>2</sub>)</h3>
        <table id="resultTable" class="table table-bordered table-striped resultTable tablesorter-green">
          <thead>
            <tr>
              <th>温室効果ガス</th>
              <th>年度</th>
              <th>排出量</th>
              <th>割合(%)</th>
              <th>増減率(%)</th>
            </tr>
          </thead>
          <tbody>
          @foreach ($table_datasets as $table_dataset)
            @if (isset($table_dataset['SUM_OF_ENERGY_CO2']))
            <tr>
              <td><a href="/compare/FactoryByGas?gas=energy_co2&year={{$table_dataset['REGIST_YEAR_ID']}}" title="エネルギー起源CO2の事業者一覧へ">エネルギー起源CO<sub>2</sub></a></td>
              <td>{{$table_dataset['REGIST_YEAR_ID']}}</td>
              <td>{{$table_dataset['SUM_OF_ENERGY_CO2']}}</td>
              <td>{{$table_dataset['PERCENT_SUM_OF_ENERGY_CO2']}}%</td>
              <td>
                @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_ENERGY_CO2'] == -99999999 or $table_dataset['RATE_OF_CHANGE_SUM_OF_ENERGY_CO2'] == 0)
                  -
                @else
                  {{$table_dataset['RATE_OF_CHANGE_SUM_OF_ENERGY_CO2']}}%
                  @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_ENERGY_CO2'] > 0)
                    <i class="fa fa-arrow-up"></i>
                  @else
                    <i class="fa fa-arrow-down"></i>
                  @endif
                @endif
              </td>
            </tr>
            @endif

            @if (isset($table_dataset['SUM_OF_NOENERGY_CO2']))
            <tr>
              <td><a href="/compare/FactoryByGas?gas=noenergy_co2&year={{$table_dataset['REGIST_YEAR_ID']}}" title="非エネルギー起源CO2の事業者一覧へ">非エネルギー起源CO<sub>2</sub></a></td>
              <td>{{$table_dataset['REGIST_YEAR_ID']}}</td>
              <td>{{$table_dataset['SUM_OF_NOENERGY_CO2']}}</td>
              <td>{{$table_dataset['PERCENT_SUM_OF_NOENERGY_CO2']}}%</td>
              <td>
                @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_NOENERGY_CO2'] == -99999999 or $table_dataset['RATE_OF_CHANGE_SUM_OF_NOENERGY_CO2'] == 0)
                  -
                @else
                  {{$table_dataset['RATE_OF_CHANGE_SUM_OF_NOENERGY_CO2']}}%
                  @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_NOENERGY_CO2'] > 0)
                    <i class="fa fa-arrow-up"></i>
                  @else
                    <i class="fa fa-arrow-down"></i>
                  @endif
                @endif
              </td>
            </tr>
            @endif

            @if (isset($table_dataset['SUM_OF_NOENERGY_DIS_CO2']))
            <tr>
              <td><a href="/compare/FactoryByGas?gas=noenergy_dis_co2&year={{$table_dataset['REGIST_YEAR_ID']}}" title="非エネルギー起源CO2(廃棄物の原燃料使用)の事業者一覧へ">非エネルギー起源CO<sub>2</sub>(廃棄物の原燃料使用)</a></td>
              <td>{{$table_dataset['REGIST_YEAR_ID']}}</td>
              <td>{{$table_dataset['SUM_OF_NOENERGY_DIS_CO2']}}</td>
              <td>{{$table_dataset['PERCENT_SUM_OF_NOENERGY_DIS_CO2']}}%</td>
              <td>
                @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_NOENERGY_DIS_CO2'] == -99999999 or $table_dataset['RATE_OF_CHANGE_SUM_OF_NOENERGY_DIS_CO2'] == 0)
                  -
                @else
                  {{$table_dataset['RATE_OF_CHANGE_SUM_OF_NOENERGY_DIS_CO2']}}%
                  @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_NOENERGY_DIS_CO2'] > 0)
                    <i class="fa fa-arrow-up"></i>
                  @else
                    <i class="fa fa-arrow-down"></i>
                  @endif
                @endif
              </td>
            </tr>
            @endif

            @if (isset($table_dataset['SUM_OF_CH4']))
            <tr>
              <td><a href="/compare/FactoryByGas?gas=ch4&year={{$table_dataset['REGIST_YEAR_ID']}}" title="CH4の事業者一覧へ">CH<sub>4</sub></a></td>
              <td>{{$table_dataset['REGIST_YEAR_ID']}}</td>
              <td>{{$table_dataset['SUM_OF_CH4']}}</td>
              <td>{{$table_dataset['PERCENT_SUM_OF_CH4']}}%</td>
              <td>
                @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_CH4'] == -99999999 or $table_dataset['RATE_OF_CHANGE_SUM_OF_CH4'] == 0)
                  -
                @else
                  {{$table_dataset['RATE_OF_CHANGE_SUM_OF_CH4']}}%
                  @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_CH4'] > 0)
                    <i class="fa fa-arrow-up"></i>
                  @else
                    <i class="fa fa-arrow-down"></i>
                  @endif
                @endif
              </td>
            </tr>
            @endif

            @if (isset($table_dataset['SUM_OF_N2O']))
            <tr>
              <td><a href="/compare/FactoryByGas?gas=n20&year={{$table_dataset['REGIST_YEAR_ID']}}" title="N2Oの事業者一覧へ">N<sub>2</sub>O</a></td>
              <td>{{$table_dataset['REGIST_YEAR_ID']}}</td>
              <td>{{$table_dataset['SUM_OF_N2O']}}</td>
              <td>{{$table_dataset['PERCENT_SUM_OF_N2O']}}%</td>
              <td>
                @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_N2O'] == -99999999 or $table_dataset['RATE_OF_CHANGE_SUM_OF_N2O'] == 0)
                  -
                @else
                  {{$table_dataset['RATE_OF_CHANGE_SUM_OF_N2O']}}%
                  @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_N2O'] > 0)
                    <i class="fa fa-arrow-up"></i>
                  @else
                    <i class="fa fa-arrow-down"></i>
                  @endif
                @endif
              </td>
            </tr>
            @endif

            @if (isset($table_dataset['SUM_OF_HFC']))
            <tr>
              <td><a href="/compare/FactoryByGas?gas=hfc&year={{$table_dataset['REGIST_YEAR_ID']}}" title="HFCの事業者一覧へ">HFC</a></td>
              <td>{{$table_dataset['REGIST_YEAR_ID']}}</td>
              <td>{{$table_dataset['SUM_OF_HFC']}}</td>
              <td>{{$table_dataset['PERCENT_SUM_OF_HFC']}}%</td>
              <td>
                @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_HFC'] == -99999999 or $table_dataset['RATE_OF_CHANGE_SUM_OF_HFC'] == 0)
                  -
                @else
                  {{$table_dataset['RATE_OF_CHANGE_SUM_OF_HFC']}}%
                  @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_HFC'] > 0)
                    <i class="fa fa-arrow-up"></i>
                  @else
                    <i class="fa fa-arrow-down"></i>
                  @endif
                @endif
              </td>
            </tr>
            @endif

            @if (isset($table_dataset['SUM_OF_PFC']))
            <tr>
              <td><a href="/compare/FactoryByGas?gas=pfc&year={{$table_dataset['REGIST_YEAR_ID']}}" title="PFCの事業者一覧へ">PFC</a></td>
              <td>{{$table_dataset['REGIST_YEAR_ID']}}</td>
              <td>{{$table_dataset['SUM_OF_PFC']}}</td>
              <td>{{$table_dataset['PERCENT_SUM_OF_PFC']}}%</td>
              <td>
                @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_PFC'] == -99999999 or $table_dataset['RATE_OF_CHANGE_SUM_OF_PFC'] == 0)
                  -
                @else
                  {{$table_dataset['RATE_OF_CHANGE_SUM_OF_PFC']}}%
                  @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_PFC'] > 0)
                    <i class="fa fa-arrow-up"></i>
                  @else
                    <i class="fa fa-arrow-down"></i>
                  @endif
                @endif
              </td>
            </tr>
            @endif

            @if (isset($table_dataset['SUM_OF_SF6']))
            <tr>
              <td><a href="/compare/FactoryByGas?gas=sf62&year={{$table_dataset['REGIST_YEAR_ID']}}" title="SF<sub>6</sub>の事業者一覧へ">SF<sub>6</sub></a></td>
              <td>{{$table_dataset['REGIST_YEAR_ID']}}</td>
              <td>{{$table_dataset['SUM_OF_SF6']}}</td>
              <td>{{$table_dataset['PERCENT_SUM_OF_SF6']}}%</td>
              <td>
                @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_SF6'] == -99999999 or $table_dataset['RATE_OF_CHANGE_SUM_OF_SF6'] == 0)
                  -
                @else
                  {{$table_dataset['RATE_OF_CHANGE_SUM_OF_SF6']}}%
                  @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_SF6'] > 0)
                    <i class="fa fa-arrow-up"></i>
                  @else
                    <i class="fa fa-arrow-down"></i>
                  @endif
                @endif
              </td>
            </tr>
            @endif

            @if (isset($table_dataset['SUM_OF_POWER_PLANT_ENERGY_CO2']))
            <tr>
              <td><a href="/compare/FactoryByGas?gas=power_plant_energy_co2&year={{$table_dataset['REGIST_YEAR_ID']}}" title="エネルギー起源CO2(発電所等配分前)の事業者一覧へ">エネルギー起源CO2(発電所等配分前)</a></td>
              <td>{{$table_dataset['REGIST_YEAR_ID']}}</td>
              <td>{{$table_dataset['SUM_OF_POWER_PLANT_ENERGY_CO2']}}</td>
              <td>{{$table_dataset['PERCENT_SUM_OF_POWER_PLANT_ENERGY_CO2']}}%</td>
              <td>
                @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_POWER_PLANT_ENERGY_CO2'] == -99999999 or $table_dataset['RATE_OF_CHANGE_SUM_OF_POWER_PLANT_ENERGY_CO2'] == 0)
                  -
                @else
                  {{$table_dataset['RATE_OF_CHANGE_SUM_OF_POWER_PLANT_ENERGY_CO2']}}%
                  @if ($table_dataset['RATE_OF_CHANGE_SUM_OF_POWER_PLANT_ENERGY_CO2'] > 0)
                    <i class="fa fa-arrow-up"></i>
                  @else
                    <i class="fa fa-arrow-down"></i>
                  @endif
                @endif
              </td>
            </tr>
            @endif

          @endforeach
           </tbody>
        </table>
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
              text: '温室効果ガス別排出量合計', //グラフの見出し
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
              display: true
          },
          tooltips: {
            mode:'label' //マウスオーバー時に表示されるtooltip
          }
       }
    });
    </script>
@endsection