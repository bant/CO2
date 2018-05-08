@extends('layouts.co2')
@section('title', '温室効果ガス別 事業者リスト')
@section('content')
      <!-- #breadcrumbs -->
      <ul id="breadcrumbs">
        <li><a href="{{url('/')}}">メニュー</a></li>
        <li>&gt;  <a href="{{url('compare/Gas')}}">温室効果ガス別集計</a></li>
        <li>&gt; 温室効果ガス別 事業所リスト</li>
      </ul>
      <!-- /#breadcrumbs -->
      <section>
          <h2>温室効果ガス別 事業所リスト</h2>
          <!-- 比較フォーム -->
          <section>
            <div class="display-switch">
              <h3>温室効果ガスデータ</h3>
              <div class="display">非表示にする</div>
            </div>
            <table class="table table-bordered search">
              <tbody>
                <tr>
                  <th>温室効果ガス</th>
                  <td>
                  @if ($gas == 'energy_co2')
                    エネルギー起源CO<sub>2</sub>
                  @elseif ($gas == 'noenergy_co2')
                    非エネルギー起源CO<sub>2</sub>
                  @elseif ($gas == 'noenergy_dis_co2')
                    非エネルギー起源CO<sub>2(廃棄物の原燃料使用)
                  @elseif ($gas == 'ch4')
                    CH<sub>4</sub>
                  @elseif ($gas == 'n2o')
                    N<sub>2</sub>O
                  @elseif ($gas == 'hfc')
                    HFC
                  @elseif ($gas == 'pfc')
                    PFC  
                  @elseif ($gas == 'sf6')
                    SF<sub>6</sub> 
                  @elseif ($gas == 'power_plant_energy_co2')
                    エネルギー起源CO<sub>2</sub>(発電所等配分前)   
                  @endif
                  </td>
                </tr>
                <tr>
                  <th>年度</th>
                  <td>{{$regist_year_id}}</td>
                </tr>
              </tbody>
            </table>
          </section>
          <!-- /比較フォーム -->
          <!-- 事業所リスト -->
          <section>
            <hr class="split">
            <h3 class="result">事業者リスト(単位:tCO<sub>2</sub>)
            </h3>
            <table id="resultTable" class="table table-bordered table-striped resultTable">
              <caption>該当件数: {{$table_count}}件</caption>
              <thead>
                <tr>
                  <th>事業者名
                    <br>事業所名[業種]
                  </th>
                  <th>年度</th>
                  <th>排出量</th>
                  <th>増減率(%)</th>
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
                  <td>{{$table_dataset->regist_year_id}}年</td>
                   <td>
                   @if ($gas == 'energy_co2')
                    {{$table_dataset->energy_co2}}
                  @elseif ($gas == 'noenergy_co2')
                    {{$table_dataset->noenergy_co2}}
                  @elseif ($gas == 'noenergy_dis_co2')
                    {{$table_dataset->noenergy_dis_co2}}
                  @elseif ($gas == 'ch4')
                    {{$table_dataset->ch4}}
                  @elseif ($gas == 'n2o')
                    {{$table_dataset->n2o}}
                  @elseif ($gas == 'hfc')
                    {{$table_dataset->hfc}}
                  @elseif ($gas == 'pfc')
                    {{$table_dataset->pfc}}
                  @elseif ($gas == 'sf6')
                    {{$table_dataset->sf6}}
                  @elseif ($gas == 'power_plant_energy_co2')
                    {{$table_dataset->power_plant_energy_co2}}
                  @endif
                   
                   </td>
                  <td>ToDo
                    <i class="fa fa-arrow-up"></i>
                  </td>
                </tr>
              @endforeach
               </tbody>
            </table>
          </section>
          <!-- /事業所リスト -->
  <!-- ページネーション -->
  {{ $table_datasets->appends($pagement_params)->links() }}
  <!-- /ページネーション -->

@endsection
