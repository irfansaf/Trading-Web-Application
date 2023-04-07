<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index () {
        $user_banks = DB::select("SELECT ubl.*, bl.name FROM users_bank_list ubl JOIN bank_list bl ON bl.code = ubl.bank_code WHERE ubl.user_id = ?", [auth()->id()]);
        $bank_list = DB::select("SELECT * FROM bank_list");
        return view("settings", ["user_banks" => $user_banks, "bank_list" => $bank_list]);
    }
    public function save_password(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password',
            'new_password_confirmation' => 'required|same:new_password',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }
        DB::update("UPDATE users SET password = ? WHERE id = ?", [Hash::make($request->new_password), Auth::id()]);
        return redirect()->back();
    }
    public function save_profile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
        ]);

        DB::update("UPDATE users SET first_name = ?, last_name = ? WHERE id = ?", [$request->first_name, $request->last_name, Auth::id()]);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function save_bank(Request $request) {
        $request->validate([
            'account_no' => 'required|integer',
            'bank_code' => 'required|integer',
        ]);

        $user = Auth::user();
        DB::insert("INSERT INTO users_bank_list(user_id, bank_code, account_no) VALUES (?, ?, ?)", [Auth()->id(), $request->input("bank_code"), $request->input("account_no")]);
        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function delete_bank(Request $request) {
        $request->validate([
            'account_no' => 'required|max:255',
        ]);
        
        DB::delete("DELETE FROM users_bank_list WHERE account_no = ? AND user_id = ?", [$request->input("account_no"), auth()->id()]);
        return redirect()->back()->with('success', 'Successfully deleted bank1.');
    }
}
