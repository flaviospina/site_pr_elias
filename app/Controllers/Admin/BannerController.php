<?php
namespace App\Controllers\Admin;

use App\Models\Banner;

class BannerController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/banners/index', [
            'pageTitle' => 'Banners',
            'banners'   => Banner::allForAdmin(),
        ]);
    }

    public function edit(string $location): void
    {
        $slots = Banner::slots();
        if (!isset($slots[$location])) {
            redirect('admin/banners');
        }
        $this->adminView('admin/banners/form', [
            'pageTitle' => 'Banner — ' . $slots[$location],
            'location'  => $location,
            'label'     => $slots[$location],
            'banner'    => Banner::get($location),
        ]);
    }

    public function save(): void
    {
        $this->requireCsrf();
        $location = $_POST['location'] ?? '';
        if (!isset(Banner::slots()[$location])) {
            redirect('admin/banners');
        }

        Banner::save($location, [
            'enabled'      => !empty($_POST['enabled']) ? 1 : 0,
            'image'        => trim($_POST['image'] ?? '') ?: null,
            'title'        => trim($_POST['title'] ?? '') ?: null,
            'subtitle'     => trim($_POST['subtitle'] ?? '') ?: null,
            'button_text'  => trim($_POST['button_text'] ?? '') ?: null,
            'button_url'   => trim($_POST['button_url'] ?? '') ?: null,
            'button2_text' => trim($_POST['button2_text'] ?? '') ?: null,
            'button2_url'  => trim($_POST['button2_url'] ?? '') ?: null,
            'overlay'      => max(0, min(100, (int) ($_POST['overlay'] ?? 70))),
            'text_color'   => in_array($_POST['text_color'] ?? '', ['light','dark'], true) ? $_POST['text_color'] : 'light',
            'align'        => in_array($_POST['align'] ?? '', ['left','center'], true) ? $_POST['align'] : 'center',
            'height'       => in_array($_POST['height'] ?? '', ['small','medium','large'], true) ? $_POST['height'] : 'medium',
        ]);

        flash('success', 'Banner salvo com sucesso.');
        redirect('admin/banners');
    }
}
