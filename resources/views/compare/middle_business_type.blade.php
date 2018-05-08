@extends('layouts.co2')
@section('title', '業種別比較(中分類)')
@section('content')
      <!-- #breadcrumbs -->
      <ul id="breadcrumbs">
        <li><a href="{{url('/')}}">メニュー</a></li>
        <li>&gt; <a href="{{url('compare/MajorBusinessType')}}?major_business_type_id={{$major_business_type->id}}">業種別比較(大分類)</a></li>
        <li>&gt; 業種別比較(中分類)</li>
      </ul>
      <!-- /#breadcrumbs -->

        <section>
          <h2>業種別比較(中分類)</h2>
          <!-- 比較フォーム -->
          <section>
            <div class="display-switch">
              <h3>集計条件</h3>
              <div class="display">非表示にする</div>
            </div>
            {!! Form::open(['url' => 'compare/MiddleBusinessType', 'method'=>'get', 'id'=>'search']) !!}
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th><label for="compare_major_business_type_id">業種(大分類)</label></th>
                    <td>{{$major_business_type->name}}</td>
                  </tr>
                  <tr>
                    <th>{!! Form::label('middle_business_type', '業種(中分類)') !!}</th>
                    <td>{!! Form::select('middle_business_type_id', $middle_business_types, 0, ['class' => 'form', 'id' => 'middle_business_type_id']) !!}</td>
                  </tr>
                  <tr>
                    <th>{!! Form::label('regist_year', '年度') !!}</th>
                    <td>{!! Form::select('regist_year_id', $regist_years, $regist_year_id, ['class' => 'form', 'id' => 'regist_year_id']) !!}</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    {!! Form::hidden('major_business_type_id', $major_business_type->id, ['id' => 'major_business_type_id']) !!}
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
            <h3 class="result">比較結果(単位:tCO<sub>2</sub>)</h3>
            <table id="result" class="c2 table table-bordered table-striped resultTable tablesorter-green">
              <thead>
                <tr>
                  <th>中分類</th>
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
                    <a href="/compare/MiddleBusinessType/factory?major={{$major_business_type->id}}&middle={{$discharge['MIDDLE_BUSINESS_TYPE_ID']}}&year={{$discharge['YEAR_ID']}}" title="{{$discharge['MIDDLE_BUSINESS_TYPE_NAME']}}の事業所へ">{{$discharge['MIDDLE_BUSINESS_TYPE_NAME']}}</a>
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
  @include('commons.stacked_graph_javascript')
@endsection