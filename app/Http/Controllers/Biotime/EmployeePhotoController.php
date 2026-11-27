<?php

namespace App\Http\Controllers\Biotime;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class EmployeePhotoController extends Controller
{
    public function download($id, Request $request)
    {
        $request->validate([
            'photo' => 'required|string',
        ]);

        $photoPath = $request->photo;

        /* ===============================
         * 1️⃣ جلب الصورة
         * =============================== */
        if (str_starts_with($photoPath, 'http')) {
            $response = Http::timeout(5)->get($photoPath);
            abort_if(! $response->successful(), 404);
            $binary = $response->body();
        } else {
            $fullPath = public_path($photoPath);
            abort_if(! file_exists($fullPath), 404);
            $binary = file_get_contents($fullPath);
        }

        /* ===============================
         * 2️⃣ إذا الحجم > 500KB → معالجة
         * =============================== */
        $maxSize = 500 * 1024; // 500 KB

        $manager = new ImageManager(new Driver());
        $image   = $manager->read($binary);

        if (strlen($binary) > $maxSize) {

            // 🔧 إعادة تحجيم ذكي (يحافظ على النسبة)
            $image->scaleDown(width: 600);

            // 🔧 ضغط بجودة مناسبة
            $binary = $image->toJpeg(75); // 75% جودة
            $extension = 'jpg';

        } else {
            // لا نلمس الصورة
            $extension = pathinfo($photoPath, PATHINFO_EXTENSION) ?: 'jpg';
        }

        /* ===============================
         * 3️⃣ تحميل باسم ID
         * =============================== */
        $fileName = $id . '.' . $extension;

        return response($binary)
            ->header('Content-Type', 'image/' . $extension)
            ->header(
                'Content-Disposition',
                'attachment; filename="' . $fileName . '"'
            );
    }
}
