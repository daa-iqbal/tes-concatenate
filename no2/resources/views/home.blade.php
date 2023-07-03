@extends('layouts.appfix')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>{{ __('Dashboard') }}</h2>
            </div>
            @if (session('status'))
                <div class="alert alert-dismissible alert-success" role="alert">
                    {{ session('status') }} 
                </div>
            @endif
            {{ __('You are logged in!') }}
            
        </div>
    </section>

@endsection
