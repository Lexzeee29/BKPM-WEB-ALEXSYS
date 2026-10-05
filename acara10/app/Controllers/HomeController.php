<?php

namespace App\Controllers;

use App\Core\BaseController;

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->redirect(BASE_URL . '/dashboard');
    }

    public function dashboard(): void
    {
        $title = 'Dashboard | SI Akademik';
        $this->view('dashboard', compact('title'));
    }
}
