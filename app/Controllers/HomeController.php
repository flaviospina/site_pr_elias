<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;
use App\Models\Devotional;
use App\Models\Event;
use App\Models\Sermon;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', [
            'pageTitle'    => null, // usa o padrão do site
            'books'        => Book::featured(3),
            'sermons'      => Sermon::published(3),
            'devotionals'  => Devotional::published(3),
            'events'       => Event::upcoming(3),
            'testimonials' => Testimonial::published(6),
        ]);
    }
}
