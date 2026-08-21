<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\JobPost;
use App\Models\PaymentMethod;

class TrashController extends Controller
{
    /**
     * Map type string to actual Model class
     */
    private function getModelClass($type)
    {
        $map = [
            'user' => User::class,
            'course' => Course::class,
            'job' => JobPost::class,
            'payment_method' => PaymentMethod::class,
        ];

        return $map[$type] ?? null;
    }

    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only Super Admin can access the Trash Bin.');
        }

        $trashedUsers = User::onlyTrashed()->get();
        $trashedCourses = Course::onlyTrashed()->get();
        $trashedJobs = JobPost::onlyTrashed()->get();
        $trashedPaymentMethods = PaymentMethod::onlyTrashed()->get();

        return view('admin.trash.index', compact(
            'trashedUsers',
            'trashedCourses',
            'trashedJobs',
            'trashedPaymentMethods'
        ));
    }

    public function restore(Request $request, $type, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $modelClass = $this->getModelClass($type);
        if (!$modelClass) {
            abort(404, 'Unknown entity type.');
        }

        $item = $modelClass::onlyTrashed()->findOrFail($id);
        $item->restore();

        return back()->with('success', ucfirst($type) . ' restored successfully.');
    }

    public function forceDelete(Request $request, $type, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $modelClass = $this->getModelClass($type);
        if (!$modelClass) {
            abort(404, 'Unknown entity type.');
        }

        $item = $modelClass::onlyTrashed()->findOrFail($id);
        $item->forceDelete();

        return back()->with('success', ucfirst($type) . ' permanently deleted.');
    }
}
