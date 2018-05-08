@extends('layouts.co2')
@section('title', '輸送排出者別 事業者リスト')
@section('content')
      <!-- #breadcrumbs -->
      <ul id="breadcrumbs">
        <li><a href="{{url('/')}}">メニュー</a></li>
        <li>&gt;  <a href="{{url('compare/CompanyDivision')}}">輸送排出者別 CO<sub>2</sub>排出量集計(指定区分)</a></li>
        <li>&gt; 輸送排出者別 事業者リスト</li>
      </ul>
      <!-- /#breadcrumbs -->
        <!-- 比較フォーム -->
        <section>
          <h2>特定輸送排出者別 事業者リスト</h2>
          <section>
            <div class="display-switch">
              <h3>集計条件</h3>
              <div class="display">非表示にする</div>
            </div>
            <table class="table table-bordered companyTable">
              <caption>区分情報</caption>
              <tbody>
                <tr>
                <th>指定区分</th>
                <td>{{$company_division->name}}</td>
                </tr>
                <th>年度</th>
                <td>{{$regist_year_id}}年</td>
                </tr>
              </tbody>
            </table>
          </section>
          <!-- /比較フォーム -->
          <!-- 比較結果リスト -->
          <section>
            <hr class="split">
            <h3 class="result">事業者リスト(単位:tCO<sub>2</sub>)</h3>
            <!-- ■ToDo -->
            <table id="resultTable" class="table table-bordered table-striped resultTable">
              <caption>該当件数: {{$table_count}}件</caption>
              <thead>
                <tr>
                <th>事業者名</th>
                <th>年度</th>
                <th>エネルギー起源CO<sub>2</sub></th>
                <th>増減率(%)</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($table_datasets as $table_dataset)
                <tr>
                <td>
                  <a href="/company/info?id={{$table_dataset->company->id}}" title="{{$table_dataset->company->name}}の詳細へ">{{$table_dataset->company->name}}</a>
                </td>
                <td>{{$table_dataset->t_d_regist_year_id}}年</td>
                <td>{{$table_dataset->energy_co2}}</td>
                <td> 
                    @if ($table_dataset->pre_percent == -99999999)
                      -
                    @else
                      {{round($table_dataset->pre_percent, 2)}}%
                      @if ($table_dataset->pre_percent > 0)
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

  <!-- ページネーション -->
  {{ $table_datasets->appends($pagement_params)->links() }}
  <!-- /ページネーション -->

@endsection
