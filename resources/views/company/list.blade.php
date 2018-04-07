@extends('layouts.co2')
@section('title', '事業者検索 | 温室効果ガスデータベース by Tウォッチ')
@section('content')
      <!-- #breadcrumbs -->
      <ul id="breadcrumbs">
        <li><a href="{{url('/')}}">メニュー</a></li>
        <li>&gt; <a href="{{url('company/search')}}">事業者検索</a></li>
        <li>&gt; 事業者リスト</li>
      </ul>
      <!-- /#breadcrumbs -->

      <section>
      <h2>事業者検索</h2>
        <section>
        <h3>検索条件</h3>
        <!-- 検索フォーム -->
        {!! Form::open(['url' => '/company/list', 'method'=>'post','id'=>'search']) !!}
            <table class="table table-bordered">
            <tbody>
              <tr>
                <th>{!! Form::label('company_name', '事業者名') !!}</th>
                <td>{!! Form::text('company_name', null, ['class' => 'form-control', 'placeholder' => '一部でも検索できます。'] ) !!}</td>
              </tr>
              <tr>
                <th>{!! Form::label('company_address', '住所') !!}</th>
                <td>{!! Form::text('company_address', null, ['class' => 'form-control', 'placeholder' => '一部でも検索できます。']) !!}</td>
              </tr>
              <tr>
                <th>{!! Form::label('company_division', '特定輸送者区分') !!}</th>
                <td>{!! Form::select('company_division_id', $company_divisions, 0, ['class' => 'form', 'id' => 'company_division_id']) !!}</td>
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
            <h3 class="result">検索結果</h3>
            <table id="companyTable" class="table table-bordered table-striped resultTable">
              <caption>該当件数: {{$company_count}}件</caption>
              <thead>
                <tr>
                  <th>事業者名</th>
                  <th>住所</th>
                  <th>事業所数</th>
                  <th>特定輸送者区分</th>
                </tr>
              </thead>
              <tbody>
              @foreach ($companies as $company)
                <tr>
                  <td>
                    <a href="/company/info?id={{$company->id}}" title="{{$company->name}}の詳細へ">{{$company->name}}</a>
                  </td>
                  <td>{{$company->address}}</td>
                  <td>{{$company->getFactoryCount()}}</td>
                  <td>{{$company->company_division->name}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </section>
          <!-- /検索結果 -->

  <!-- ページネーション -->
  {{ $companies->appends($pagement_params)->links() }}
  <!-- /ページネーション -->
      </section>

@endsection