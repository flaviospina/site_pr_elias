<?php
namespace App\Controllers\Admin;

use App\Models\Book;
use App\Models\ContactMessage;
use App\Models\Devotional;
use App\Models\Order;
use App\Models\Sermon;
use App\Models\Subscriber;

class DashboardController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/dashboard', [
            'pageTitle'    => 'Painel',
            'orderStats'   => Order::stats(),
            'recentOrders' => array_slice(Order::allForAdmin(), 0, 8),
            'unreadMsgs'   => ContactMessage::unreadCount(),
            'subscribers'  => Subscriber::count(),
            'counts'       => [
                'books'       => count(Book::allForAdmin()),
                'sermons'     => count(Sermon::allForAdmin()),
                'devotionals' => count(Devotional::allForAdmin()),
            ],
        ]);
    }
}
