@extends('layouts.co2')
@section('title', '事業所リスト')
@section('content')
      <!-- #breadcrumbs -->
      <ul id="breadcrumbs">
        <li><a href="{{url('/')}}">メニュー</a></li>
        <li>&gt; <a href="{{url('factory/search')}}">事業所検索</a></li>
        <li>&gt; 事業所リスト</li>
      </ul>
      <!-- /#breadcrumbs -->

      <section>
      <h2>事業所リスト</h2>
      <section>
        <h3>検索条件</h3>
        <!-- 検索フォーム -->
        {!! Form::open(['url' => '/factory/list', 'method'=>'post','id'=>'search']) !!}
            <table class="table table-bordered">
            <tbody>
              <tr>
                <th>{!! Form::label('factory_name', '事業所名') !!}</th>
                <td>{!! Form::text('factory_name', null, ['class' => 'form-control', 'placeholder' => '一部でも検索できます。'] ) !!}</td>
              </tr>
              <tr>
                <th>{!! Form::label('factory_pref', '都道府県') !!}</th>
                <td>{!! Form::select('factory_pref_id', $factory_prefs, 0, ['class' => 'form', 'id' => 'factory_pref_id']) !!}</td>
              </tr>
              <tr>
                <th>{!! Form::label('factory_address', '住所') !!}</th>
                <td>{!! Form::text('factory_address', null, ['class' => 'form-control', 'placeholder' => '一部でも検索できます。']) !!}</td>
              </tr>
              <tr>
                <th>{!! Form::label('major_business_type', '業種(大分類)') !!}</th>
                <td>{!! Form::select('major_business_type_id', $major_business_types, 0, ['class' => 'form', 'id' => 'major_business_type_id']) !!}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="2" class="center">
                {!! Form::submit('検 索', ['class' => 'btn btn-warning']) !!}
                </td>
              </tr>
            </tfoot>
          </table>
        {{ Form::close() }}
        <!-- /検索フォーム -->
        </section>
          <!-- 検索結果 -->
          <section>
            <hr class="split">
            <h3 class="result">事業所者リスト</h3>
            <table id="result" class="l24 table table-bordered table-striped resultTable">
              <caption>該当件数: {{$factory_count}}</caption>
              <thead>
                <tr>
                  <th>事業者名
                    <br>事業所名
                  </th>
                  <th>業種</th>
                  <th>年度</th>
                  <th>住所</th>
                  <th>従業員数</th>
                  <th>排出量合計
                    <br>増減率(%)
                  </th>
                </tr>
              </thead>
              <tbody>
              @foreach ($factories as $factory)
                <tr>
                  <td>{{$factory->company->name}}
                    <br>
                    <a href="/factory/info?id={{$factory->id}}" title="{{$factory->name}}の詳細へ">{{$factory->name}}</a>
                  </td>
                  <td>{{$factory->major_business_type->name}}</td>
                  <td>{{$factory->regist_year->name}}年</td>
                  <td>{{$factory->address}}</td>
                  <td>{{$factory->employee}}人</td>
                  <td>{{$factory->getSumOfExharst($factory->regist_year->id)}}
                    <br>
                    @if ($factory->getPrePercent($factory->regist_year->id) == -99999999)
                      -
                    @else
                      {{$factory->getPrePercent($factory->regist_year->id)}}%
                      @if ($factory->getPrePercent($factory->regist_year->id) > 0)
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
          <!-- /検索結果 -->

  <!-- ページネーション -->
  {{ $factories->appends($pagement_params)->links() }}
  <!-- /ページネーション -->
      </section>

@endsection