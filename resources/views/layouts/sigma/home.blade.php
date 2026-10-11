@extends('app')

@section('content')
{!! redirect()->to('/dashboard')->send() !!}
@endsection