<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;
use App\Models\Attendance;
use App\Models\AttLog;
use App\Models\AccTransaction;
use App\Jobs\JobPostAtt;
use App\Jobs\JobResetAttendance;
use Carbon\Carbon;

class Helper
{

    public static function validator($data, $rules)
    {
        $validation = Validator::make($data, $rules);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()
            ], 422);    
        } else {
            return true;
        }
    }

    public static function pagination($model, $request, $params)
    {
        $pageSize = $request->input('page_size', 10);
        $page = $request->input('page', 1);
        $keyword = strtolower($request->input('keyword', ''));

        // Mulai query dengan pencarian
        $data = $model->when($keyword, function ($query) use ($keyword, $params) {
            foreach ($params as $param) {
                // Cek jika parameter mengandung "." untuk relasi
                if (strpos($param, '.') !== false) {
                    [$relation, $column] = explode('.', $param, 2);

                    // Gunakan orWhereHas untuk relasi
                    // $query->orWhereHas($relation, function ($q) use ($column, $keyword) {
                    //     $q->whereRaw("LOWER(CAST($column AS TEXT)) LIKE ?", ['%' . $keyword . '%']);
                    // });
                } else {
                    // Cek jika keyword adalah tahun
                    // if (preg_match('/^\d{4}$/', $keyword)) {
                    //     // Gunakan whereYear untuk kolom tanggal
                    //     $query->orWhereYear($param, $keyword);
                    // } else {
                    //     // Gunakan LIKE untuk kolom teks
                    //     $query->orWhereRaw("LOWER(CAST($param AS TEXT)) LIKE ?", ['%' . $keyword . '%']);
                    // }
                }
            }
        });

        return $data->paginate($pageSize, ['*'], 'page', $page);
    }
}
