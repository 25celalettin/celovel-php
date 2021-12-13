@extends('layouts.main')

@section('title'){{ $title }}@endsection

@section('sidebar')
@if($role == 'user')
    kullanici
@elseif($role == 'admin')
    admin
@else
    hicbiri
@endif
<hr>
<form action="/blog/add" method="POST" enctype="multipart/form-data">
    <input type="file" name="blog_image">
    
    <button type="submit">Gonder</button>
</form>
@endsection

@section('content')
<div class="template-engine-test">
    <p class="name">{{ $name }}</p>

    <ul>
        @foreach($todos as $todo)
            <li>{{ $todo }}</li>
        @endforeach
    </ul>
</div>

<main class="main-section">        

    <div class="single-blog">
        <h1 class="blog-title">Baslikk</h1>
        <p class="blog-content">
            icerik
        </p>
    </div>

</main>
@endsection