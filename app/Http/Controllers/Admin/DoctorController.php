<?php

namespace App\Http\Controllers\Admin;

use App\Models\Doctor;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;

use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        $doctor = $user->doctor;
        return view('admin.doctors.index', compact('doctor'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.doctors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDoctorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDoctorRequest $request)
    {
        $form_data = $request->all();

        $doctor = new Doctor();

        if ($request->hasFile('image')) {
            $image_path = Storage::disk('public')->put('doctors_images', $form_data['image']);
            $form_data['image'] = $image_path;
        }

        if ($request->hasFile('cv')) {
            $cv_path = Storage::disk('public')->put('doctors_cvs', $form_data['cv']);
            $form_data['cv'] = $cv_path;
        }

        $doctor->fill($form_data);

        $doctor->save();


        return redirect()->route('admin.doctors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function show(Doctor $doctor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function edit(Doctor $doctor)
    {

        return view('admin.doctors.edit', compact('doctor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDoctorRequest  $request
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        $form_data = $request->all();

        if ($request->hasFile('image')) {
            if ($doctor->image != null) {
                if (!str_contains($doctor->image, 'https://')) {
                    Storage::disk('public')->delete($doctor->image);
                }
            }
            $image_path = Storage::disk('public')->put('doctors_images', $form_data['image']);
            $form_data['image'] = $image_path;
        }

        if ($request->hasFile('cv')) {
            if ($doctor->cv != null) {
                Storage::disk('public')->delete($doctor->cv);
            }
            $cv_path = Storage::disk('public')->put('doctors_cvs', $form_data['cv']);
            $form_data['cv'] = $cv_path;
        }
        $doctor->update($form_data);


        return redirect()->route('admin.doctors.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Doctor $doctor)
    {
        if ($doctor->image != null) {
            if (!str_contains($doctor->image, 'https://')) {
                Storage::disk('public')->delete($doctor->image);
            }
        }

        if ($doctor->cv != null) {
            Storage::disk('public')->delete($doctor->cv);
        }

        $doctor->delete();
        return redirect()->route('admin.doctors.index');
    }
}
