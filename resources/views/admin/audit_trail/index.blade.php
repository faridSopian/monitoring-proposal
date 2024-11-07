@extends('admin.layouts.app')

@section('content')
<div class="container pt-4 pb-4 mb-5">
    <h2 style="font-weight: 700; color: #2C3E50;">Audit Trail</h2>
    <table class="table table-bordered table-hover shadow-sm" style="background-color: #f8f9fa; border-radius: 8px;">
        <thead background-color: #ECF0F1; color: #2C3E50;>
            <tr>
                <th class="text-sm">ID User</th>
                <th class="text-sm">Username</th>
                <th class="text-sm">Menu yang Diakses</th>
                <th class="text-sm">Method</th>
                <th class="text-sm">Waktu Aktivitas</th>
                <th class="text-sm">Detail</th>
            </tr>
        </thead>
        <tbody>
            @foreach($auditTrails as $trail)
            <tr>
                <td class="text-sm">{{ $trail->user_id }}</td>
                <td class="text-sm">{{ $trail->username }}</td>
                <td class="text-sm">{{ $trail->menu_accessed }}</td>
                <td class="text-sm">{{ $trail->method }}</td>
                <td class="text-sm">{{ $trail->activity_time }}</td>
                <td class="text-sm">{{ $trail->detail }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {!! $auditTrails->links('pagination::bootstrap-5') !!}
</div>
@endsection
