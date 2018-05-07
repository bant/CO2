@extends('errors.layouts.base')
@section('title', 'お探しのページが見つかりません')
@section('message', '404 Not Found')
@section('detail', 'お探しのページが見つかりません。')
@section('link')
<a href="/" title="検索メニューへ">
  <i class="fa fa-home fa-3x" aria-hidden="true"></i>メニューに戻る
</a>
@endsection