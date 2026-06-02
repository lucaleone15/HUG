<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class ReportPreviewController extends Controller
{
    public function show(string $token)
    {
        $data = Cache::get("report_preview:{$token}");

        abort_if(!$data, 404);

        return view('pdf.report-preview', $data);
    }
}
