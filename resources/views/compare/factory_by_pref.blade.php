@extends('layouts.co2')
@section('title', '都道府県別 事業者リスト | 温室効果ガスデータベース by Tウォッチ')
@section('content')
      <!-- #breadcrumbs -->
      <ul id="breadcrumbs">
        <li><a href="{{url('/')}}">メニュー</a></li>
        <li>&gt;  <a href="{{url('compare/Pref')}}">都道府県別比較</a></li>
        <li>&gt; 都道府県別 事業者リスト</li>
      </ul>
      <!-- /#breadcrumbs -->
        <!-- 比較フォーム -->
        <section>
          <h2>都道府県別 事業者リスト</h2>
          <section>
            <div class="display-switch">
              <h3>都道府県データ</h3>
              <div class="display">非表示にする</div>
            </div>
            <table class="table table-bordered companyTable">
              <caption>都道府県情報</caption>
              <tbody>
                <tr>
                <th>都道府県名</th>
                <td>{{$pref->name}}</td>
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
            <table id="result" class="table table-bordered table-striped resultTable">
              <caption>該当件数: {{$table_count}}件</caption>
              <thead>
                <tr>
                <th>事業者名
                  <br>事業所名[業種]
                </th>
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
              @foreach ($table_datasets as $table_dataset)
                <tr>
                <td>{{$table_dataset->factory->company->name}}
                  <br>
                  <a href="/factory/info?id={{$table_dataset->factory->id}}" title="{{$table_dataset->factory->name}}の詳細へ">{{$table_dataset->factory->name}}</a>
                  [{{$table_dataset->factory->business_type->name}}]
                </td>
                <td>{{$table_dataset->f_d_regist_year_id}}年</td>
                <td>{{$table_dataset->energy_co2}}</td>
                <td>{{$table_dataset->noenergy_co2}}</td>
                <td>{{$table_dataset->noenergy_dis_co2}}</td>
                <td>{{$table_dataset->ch4}}</td>
                <td>{{$table_dataset->n2o}}</td>
                <td>{{$table_dataset->hfc}}</td>
                <td>{{$table_dataset->pfc}}</td>
                <td>{{$table_dataset->sf6}}</td>
                <td>{{$table_dataset->sum_of_exharst}}
                  </br>
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
            <p class="caution">※エネ起はエネルギー起源CO
              <sub>2</sub>、非エネは非エネルギー起源CO
              <sub>2</sub>、非エ廃は非エネルギー廃棄物原燃の略
            </p>
          </section>
          <!-- /比較結果リスト -->


  <!-- ページネーション -->
  {{ $table_datasets->appends($pagement_params)->links() }}
  <!-- /ページネーション -->

@endsection
