@extends('layouts.co2')
@section('title', '事業所検索 | 温室効果ガスデータベース by Tウォッチ')
@section('content')
      <!-- #breadcrumbs -->
      <ul id="breadcrumbs">
        <li><a href="{{url('/')}}">メニュー</a></li>
        <li>&gt; 事業所検索</li>
      </ul>
      <!-- /#breadcrumbs -->

      <section>
      <h2>事業所検索</h2>
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
      </section>
@endsection