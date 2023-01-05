<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Aplikasi;
use App\Models\TasksAplikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use DB;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Get view file location from menu config
        $view = theme()->getOption('page', 'view');

        // Check if the page view file exist
        if (view()->exists('pages.'.$view)) {
            return view('pages.'.$view);
        }

        // Get the default inner page
        return redirect('/');
    }

    public function app()
    {
        return "<pre>APP SCRUM APLIKASI</pre>";
    }

    public function widgets(Request $request)
    {
        $users = User::count();
        $aplikasi = Aplikasi::count();
        $task = TasksAplikasi::where('type', 'task')->count();

        $return = [
            "overdue" => [
                "title" => "Tasks",
                "data" => [
                    "name" => "Tasks",
                    "count" => $task,
                    "extra" => [
                    "name" => "Data baru hari ini",
                    "count" => 0
                    ]
                ],
                "detail" => "You can show some detailed information about this widget in here."
            ],
            "issues" => [
                "title" => "Aplikasi",
                "data" => [
                    "name" => "Aplikasi",
                    "count" => $aplikasi,
                    "extra" => [
                    "name" => "Data baru hari ini",
                    "count" => 0
                    ]
                ],
                "detail" => "You can show some detailed information about this widget in here."
            ],
            "features" => [
                "title" => "Pengguna",
                "data" => [
                    "name" => "Pengguna",
                    "count" => $users,
                    "extra" => [
                    "name" => "Data baru hari ini",
                    "count" => 0
                    ]
                ],
                "detail" => "You can show some detailed information about this widget in here."
            ],
        ];

        return $return;
    }

    /**
     * Temporary function to replace icon duotone
     */
    public function replaceIcons()
    {
        $fileContent = file_get_contents(public_path('icon_replacement.txt'));
        $lines       = explode("\n", $fileContent);

        $patterns     = [];
        $replacements = [];
        foreach ($lines as $line) {
            $el = explode(' - ', $line);
            if (empty($line)) {
                continue;
            }
            $patterns[]     = trim($el[0]);
            $replacements[] = trim($el[1]);
        }

        $files    = File::allFiles(resource_path());
        $filtered = array_filter($files, function ($str) {
            return strpos($str, ".php") !== false;
        });

        foreach ($filtered as $file) {
            $bladeFileContent = file_get_contents($file->getPathname());

            $bladeFileContent = str_replace($patterns, $replacements, $bladeFileContent);

            file_put_contents($file->getPathname(), $bladeFileContent);
        }
    }
}
