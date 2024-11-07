@extends('dekan.layouts.app')

@section('title', 'Dekan Dashboard')

@section('content_header')
    <h1>Welcome to the Dekan Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6 mt-4">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingApprovals }}</h3>
                    <p>Pending Approvals</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
@stop
