<!-- 指定繼承 layout.master 母模板 -->
@extends('layout.master')

<!-- 傳送資料到母模板，並指定變數為title -->
@section('title', $title)

<!-- 傳送資料到母模板，並指定變數為content -->
@section('content')
<div>
    <p class="body_title">留言板</p>
</div>
<div class="body_show_region form_radius">
    @foreach($boardList as $data)
    @endforeach
    <form action = "" method="POST" />
        
    </form>
</div>
@endsection