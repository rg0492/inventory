@extends('layouts.app')
@section('content')
<style>
    /* Small Box */
.small-box {
    border-radius: 2px;
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    color: #fff;
}

/* Inner */
.small-box .inner {
    padding: 10px;
}

/* Header */
.small-box h3 {
    font-size: 30px;
    font-weight: bold;
    margin: 0 0 10px 0;
}

/* Percentage */
.small-box h3 sup {
    font-size: 20px;
}

/* Description */
.small-box p {
    font-size: 14px;
    margin: 0;
}

/* Icon */
.small-box .icon {
    position: absolute;
    top: -10px;
    right: 10px;
    z-index: 0;
    font-size: 50px;
    color: rgba(255, 255, 255, 0.15);
}

/* Footer */
.small-box-footer {
    position: relative;
    text-align: center;
    padding: 3px 0;
    color: #fff;
    z-index: 10;
    background: rgba(0, 0, 0, 0.1);
    text-decoration: none;
}

/* Footer Icon */
.small-box-footer i {
    margin-left: 5px;
}

</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    

                    {{ __('You are logged in!') }}

                        <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                        <div class="inner">
                        <h3>{{$productCount}}</sup></h3>
                        <p>Total Products</p>
                        </div>
                        <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{route('products.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                        </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection