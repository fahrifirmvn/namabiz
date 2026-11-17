<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PortalPageController extends Controller
{
    public function show(string $page): View
    {
        $content = config("namabiz.pages.{$page}");
        abort_unless(is_array($content), 404);

        return view('portal-page', ['page' => $page, 'content' => $content]);
    }
}
