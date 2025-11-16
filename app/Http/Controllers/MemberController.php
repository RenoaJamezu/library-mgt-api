<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::with('activeBorrowings');

        // search functionality
        if ($request->has('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // filter functionality
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // get members
        $member = $query->paginate(10);

        return MemberResource::collection($member);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $member = Member::create($request->validated());

        return new MemberResource($member);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $member = Member::findOrFail($id);
            $member->load(['activeBorrowings', 'borrowings']);

            return new MemberResource($member);
        } catch (\Exception $th) {
            return response()->json([
                'message' => 'Member not found'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        $member->update($request->validated());

        $member->load(['activeBorrowings', 'borrowings']);
        
        return new MemberResource($member);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        // check if member has any active borrowings
        if ($member->activeBorrowings()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete member with active borrowings',
            ]);
        }

        $member->delete();

        return response()->json([
            'message' => 'Member Deleted Successfully'
        ]);
    }
}
