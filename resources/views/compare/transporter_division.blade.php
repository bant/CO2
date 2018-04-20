@extends('layouts.co2')
@section('title', '輸送排出者別 CO2排出集計(輸送区分) | 温室効果ガスデータベース by Tウォッチ')
@section('content')
      <!-- #breadcrumbs -->
      <ul id="breadcrumbs">
        <li><a href="{{url('/')}}">メニュー</a></li>
        <li>&gt;  <a href="{{url('compare/CompanyDivision')}}">輸送排出者別 CO<sub>2</sub>排出量集計(指定区分)</a></li>
        <li>&gt; 特定輸送排出者別 CO<sub>2</sub>排出集計(輸送区分)</li>
      </ul>
      <!-- /#breadcrumbs -->

       <section>
          <h2>特定輸送排出者別 CO<sub>2</sub>排出集計(輸送区分)</h2>
          <!-- 比較フォーム -->
          <section>
            <div class="display-switch">
              <h3>集計条件</h3>
              <div class="display">非表示にする</div>
            </div>
            {!! Form::open(['url' => 'compare/TransporterDivision', 'method'=>'post', 'id'=>'search']) !!}
              <table class="table table-bordered">
                <tbody>
                  <tr>
                  <th>指定区分</th>
                  <td>{{$f_company_division->name}}</td>
                  </tr>
                  <tr>
                    <th>{!! Form::label('transporter_division', '輸送区分') !!}</th>
                    <td>{!! Form::select('transporter_division_id', $transporter_divisions, 0, ['class' => 'form', 'id' => 'transporter_division_id']) !!}</td>
                  </tr>
                  <th>
                  <tr>
                    <th>{!! Form::label('regist_year', '年度') !!}</th>
                    <td>{!! Form::select('regist_year_id', $regist_years, 0, ['class' => 'form', 'id' => 'regist_year_id']) !!}</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    {!! Form::hidden('company_division_id', $f_company_division->id, ['id' => 'company_division_id']) !!}
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
                <th>輸送区分</th>
                <th>年度</th>
                <th>エネルギー起源CO<sub>2</sub></th>
                <th>割合(%)</th>
                <th>増減率(%)</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($table_datasets as $table_dataset)
                <tr>
                <td>
                  <a href="/compare/FactoryByTransporterDivision/11/1/2014" title="{{$table_dataset['TRANSPORTER_DIVISION_NAME']}}の事業者一覧へ">{{$table_dataset['TRANSPORTER_DIVISION_NAME']}}</a>
                </td>
                <td>{{$table_dataset['YEAR_ID']}}</td>
                <td>{{$table_dataset['SUM_ENERGY_CO2']}}</td>
                <td>{{$table_dataset['PERCENT']}}</td>
                <td>
                  @if ($table_dataset['PRE_PERCENT'] == -99999999 or $table_dataset['PRE_PERCENT'] == 0)
                    -
                  @else
                    {{$table_dataset['PRE_PERCENT']}}%
                    @if ($table_dataset['PRE_PERCENT'] > 0)
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
          </section>
          <!-- /比較結果リスト -->
        </section>


@endsection

@section('add_javascript')
<script>
  	  var ctx = document.getElementById("myChart").getContext('2d');
      // 6色
      var colors = ['rgba(70,132,238,0.8)', 'rgba(220,57,18,0.8)', 'rgba(255,153,0,0.8)', 'rgba(0,128,0,0.8)', 'rgba(73,66,204,1.0)'];
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
              text: '特定輸送排出者別 CO2排出量合計', //グラフの見出し
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
          tooltips:{
            mode:'label' //マウスオーバー時に表示されるtooltip
          }
        }
      });
    </script>
@endsection