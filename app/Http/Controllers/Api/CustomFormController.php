<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\UserExistPreviouslyException;
use App\Models\FormData;

class CustomFormController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return response
     */
    public function index()
    {
        $customForm = CustomForm::with('formData')->get();

        return response()->json([
                                'data' => $customForm ?? [],
                                'success' => true,
                                ], 200);
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_name' => ['required', 'string', 'max:255'],
            'form_slug' => ['required', 'string', 'max:255', Rule::unique('custom_forms', 'form_slug')->whereNull('deleted_at')],
            'form_email' => 'required|email',
            'form_components' => 'required|json',
            'form_status' => 'nullable|boolean',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        if (CustomForm::withTrashed()
                        ->where('form_slug', $request->form_slug)
                        ->exists()
        ) {
            throw new UserExistPreviouslyException('Oops! It appears that the chosen Form slug is already in use. Please select a different one and try again');
        }

        $customForm = CustomForm::create($request->all());

        if ($customForm) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Custom form created successfully'
                                    ], 202);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param string $name
     * @return response
     */
    public function restore(string $name)
    {
        $customForm = CustomForm::withTrashed()->whereForm_slug($name)->first();

        if (!$customForm) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Custom form not found'
                                    ], 404);
        }

        $result = $customForm->restore();

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Custom form restored successfully'
                                    ], 202);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Display the specified resource.
     * 
     * @param string $id
     * @return response
     */
    public function show(string $id)
    {
        $customForm = CustomForm::find($id);

        if ($customForm) {

            return response()->json([
                                    'data' => $customForm,
                                    'success' => true,
                                    'message' => ''
                                    ], 200);
        } else {

            return response()->json([
                                    'data' => [],
                                    'success' => false,
                                    'message' => 'Custom form not found'
                                    ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function update(Request $request, string $id)
    {
        $customForm = CustomForm::find($id);

        if (!$customForm) {

            return response()->json([
                'success' => false,
                'message' => 'Custom form not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'form_name' => ['required', 'string', 'max:255'],
            'form_slug' => ['required', 'string', 'max:255', Rule::unique('custom_forms', 'form_slug')->ignore($id, 'custom_form_id')->whereNull('deleted_at')],
            'form_email' => 'required|email',
            'form_components' => 'required|json',
            'form_status' => 'nullable|boolean',
        ]);

        //if the request have some validation errors
        if ($validator->fails()) {

            return response()->json([
                                    'success' => false,
                                    'message' => $validator->messages()
                                    ], 403);
        }

        $result = $customForm->update($request->all());

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Custom form updated successfully'
                                    ], 202);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function edit(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param string $id
     * @return Response
     */
    public function destroy(string $id)
    {
        $customForm = CustomForm::find($id);

        if (!$customForm) {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Custom form not found'
                                    ], 404);
        }

        $result = $customForm->delete();

        if ($result) {

            return response()->json([
                                    'success' => true,
                                    'message' => 'Custom form deleted successfully'
                                    ], 202);
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Something went wrong, please try again later'
                                    ], 422);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function submitFormStore(Request $request, string $id)
    {
        $customForm = CustomForm::whereForm_slug($id)
                                  ->whereForm_status(true)
                                  ->first();

        if ($customForm) {

            // Decode JSON string into an associative array
            $formData = json_decode($customForm->form_components, true);

            // Extract input field names
            $inputFieldFromForm = [];
            foreach ($formData['form']['fields'] as $field) {
                if (isset($field['name'])) {
                    $inputFieldFromForm[] = $field['name'];
                }
            }

            $inputFieldNamesFromRequest = array_keys($request->all());
            $fieldsDiff = array_diff($inputFieldFromForm, $inputFieldNamesFromRequest);

            if (empty($fieldsDiff)) {
            
                $form_data = json_encode($request->all());

                $formData = [   
                    'form_id' => $customForm->custom_form_id,
                    'form_data' => $form_data,
                    'form_status' => true,
                    'form_data_response' => 'success',
                ];
            
                $formdata =  FormData::create($formData);

                return response()->json([
                                        'success' => true,
                                        'message' => 'Form Submitted Successfully',
                                        ], 201);

            } else {


                return response()->json([
                                        'success' => false,
                                        'message' => 'Invalid response',
                                        ], 422);
            }
        } else {

            return response()->json([
                                    'success' => false,
                                    'message' => 'Somthing went wrong, please try again',
                                    ], 422);
        }
    }
}
