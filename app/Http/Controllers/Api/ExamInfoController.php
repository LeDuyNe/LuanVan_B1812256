<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Examinfo;

class ExamInfoController extends BaseController
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'userID' => 'required|string|min:5|unique:users',
            'password' => 'required|string|min:6',
            're_password' => 'required|same:password',
        ]);


        $examinfo = Examinfo::create([
                'Teacher_id' => $request->input('Teacher_id'),
                'Course' => $request->input('Course'),
                'question_lenth' => $request->input('question_lenth'),
                'uniqueid' => $request->input('uniqueid'),
                'time' => $request->input('time')
            ]);

        return view('makequestion.create', ['examinfo' => $examinfo]);

        // $examinfo->Teacher_id = $request->Teacher_id;
        // $examinfo->Course = $request->Course;
        // $examinfo->question_lenth = $request->question_lenth;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
