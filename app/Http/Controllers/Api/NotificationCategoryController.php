<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotificationCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NotificationCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notificationCategories = NotificationCategory::with('notices')->get();

        return response()->json([
            'data' => $notificationCategories,
            'success' => true,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_category_name' => ['required', 'string', 'max:255'],
            'notification_category_slug' => ['required', 'string', 'max:255', Rule::unique('notification_categories')],
            'notification_category_type' => ['required', 'string', 'max:255'],
            'notification_category_status' => ['required','boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 403);
        }

        $notificationCategory = NotificationCategory::create($request->all());

        if ($notificationCategory) {
            return response()->json([
                'success' => true,
                'message' => 'Notification Category created successfully'
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $notificationCategory = NotificationCategory::find($id);

        if ($notificationCategory) {
            return response()->json([
                'data' => $notificationCategory,
                'success' => true,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Notification Category not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $notificationCategory = NotificationCategory::find($id);

        if (!$notificationCategory) {
            return response()->json([
                'success' => false,
                'message' => 'Notification Category not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'notification_category_name' => ['required', 'string', 'max:255'],
            'notification_category_slug' => ['required', 'string', 'max:255', Rule::unique('notification_categories')->ignore($id, 'notification_category_id')],
            'notification_category_type' => ['required', 'string', 'max:255'],
            'notification_category_status' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ], 403);
        }

        $notificationCategory->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Notification Category updated successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notificationCategory = NotificationCategory::find($id);

        if (!$notificationCategory) {
            return response()->json([
                'success' => false,
                'message' => 'Notification Category not found'
            ], 404);
        }

        $notificationCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification Category deleted successfully'
        ], 200);
    }
}
