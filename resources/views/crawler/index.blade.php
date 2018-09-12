@extends('layouts.app')
@section('content')
    <div class="container">
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
            <tr>
                <th class="sorting">Адрес страницы</th>
                <th>Количество тегов</th>
                <th>Время обработки</th>
            </tr>
            </thead>
            <tbody>
            @foreach($images as $img)
                <tr>
                    <td>{{$img->page_link}}</td>
                    <td>{{$img->cont_img}}</td>
                    <td>{{$img->time_load}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
