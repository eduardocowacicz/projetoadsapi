<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function dashboard()
    {
        return view('pages.dashboard');
    }

    public function eventos()
    {
        return view('pages.eventos');
    }

    public function participantes()
    {
        return view('pages.participantes');
    }

    public function inscricoes()
    {
        return view('pages.inscricoes');
    }
}
