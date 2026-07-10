<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;

class AgendaController extends Controller
{
    public function index(): void
    {
        $this->view('agenda/index', [
            'pageTitle' => 'Agenda',
            'upcoming'  => Event::upcoming(),
            'past'      => Event::past(6),
        ]);
    }
}
