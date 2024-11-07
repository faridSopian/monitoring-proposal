<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index()
    {
        $auditTrails = AuditTrail::orderBy('activity_time', 'desc')->paginate(4); // Menampilkan data dengan paginasi
        return view('admin.audit_trail.index', compact('auditTrails'));
    }
}
