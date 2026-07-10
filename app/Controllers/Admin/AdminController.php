<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;

/** Base dos controllers do painel: exige login e usa o layout admin. */
abstract class AdminController extends Controller
{
    public function __construct()
    {
        Auth::require();
    }

    protected function adminView(string $view, array $data = []): void
    {
        $this->view($view, $data, 'admin');
    }
}
