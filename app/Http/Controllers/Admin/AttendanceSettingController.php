<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;

class AttendanceSettingController extends Controller
{
    public function edit()
    {
        return view('admin.attendance-settings.edit', ['settings' => AttendanceSetting::current()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'gps_enabled' => ['nullable', 'boolean'], 'office_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'office_longitude' => ['nullable', 'numeric', 'between:-180,180'], 'office_radius_meters' => ['required', 'integer', 'min:20', 'max:5000'],
            'network_enabled' => ['nullable', 'boolean'], 'allowed_ips' => ['nullable', 'string', 'max:3000'], 'selfie_enabled' => ['nullable', 'boolean'],
        ]);
        foreach (['gps_enabled', 'network_enabled', 'selfie_enabled'] as $field) {
            $data[$field] = $request->boolean($field);
        }
        if ($data['gps_enabled'] && (! isset($data['office_latitude']) || ! isset($data['office_longitude']))) {
            return back()->withErrors(['office_latitude' => 'Koordinat kantor wajib diisi saat GPS aktif.'])->withInput();
        }
        if ($data['network_enabled'] && blank($data['allowed_ips'])) {
            return back()->withErrors(['allowed_ips' => 'IP jaringan wajib diisi saat jaringan aktif.'])->withInput();
        }
        AttendanceSetting::current()->update($data);

        return back()->with('success', 'Validasi absensi berhasil disimpan.');
    }
}
