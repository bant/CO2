@extends('layouts.co2')
@section('title', '輸送排出者別 CO2 排出量集計')
@section('content')
      <!-- #breadcrumbs -->
      <ul id="breadcrumbs">
        <li><a href="{{url('/')}}">メニュー</a></li>
        <li>&gt; 輸送排出者別 CO<sub>2</sub>排出量集計</li>
      </ul>
      <!-- /#breadcrumbs -->

        <section>
          <h2>特定輸送排出者別 CO<sub>2</sub>排出量集計</h2>
          <!-- 比較フォーム -->
          <section>
            <div class="display-switch">
              <h3>集計条件</h3>
              <div class="display">非表示にする</div>
            </div>
            {!! Form::open(['url' => 'compare/CompanyDivision', 'method'=>'post', 'id'=>'search']) !!}
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th>{!! Form::label('company_division', '指定区分') !!}</th>
                    <td>{!! Form::select('company_division_id', $company_divisions, 0, ['class' => 'form', 'id' => 'company_division_id']) !!}</td>
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
            <h3 class="result">比較結果(単位:tCO<sub>2</sub>)</h3>
            <table id="resultTable" class="table table-bordered table-striped resultTable tablesorter-green">
              <thead>
                <tr>
                  <th>指定区分</th>
                  <th>年度</th>
                  <th>エネルギー起源CO<sub>2</sub>
                  </th>
                  <th>割合(%)</th>
                  <th>増減率(%)</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($table_datasets as $table_dataset)
                <tr>
                  <td>
                    {{$table_dataset['COMPANY_DIVISION_NAME']}}(
                      @if ($table_dataset['COMPANY_DIVISION_ID'] == 1 || $table_dataset['COMPANY_DIVISION_ID'] ==2)
                        <a href="/compare/TransporterDivision?company_division_id={{$table_dataset['COMPANY_DIVISION_ID']}}" title="{{$table_dataset['COMPANY_DIVISION_NAME']}}の輸送区分へ">輸送区分</a>
                      @else
                      <a href="/compare/FactoryByCompanyDivision?id={{$table_dataset['COMPANY_DIVISION_ID']}}&year={{$table_dataset['YEAR_ID']}}" title="{{$table_dataset['COMPANY_DIVISION_NAME']}}の事業者一覧へ">事業者一覧</a>
                      @endif
                    )
                  </td>
                  <td>{{$table_dataset['YEAR_ID']}}年</td>
                  <td>{{$table_dataset['SUM_ENERGY_CO2']}}</td>
                  <td>{{$table_dataset['PERCENT']}}%</td>
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


@endsection

@section('add_javascript')
  @include('commons.stacked_graph_javascript')
@endsection