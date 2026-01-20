<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::all();
        return view('pos.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('pos.tables.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:tables',
        ]);

        Table::create($request->all());

        return redirect()->route('pos.tables.index')->with('success', 'Table created');
    }
    
    public function destroy(Table $table)
    {
        $table->delete();
        return back()->with('success', 'Table deleted');
    }

    public function qr(Table $table)
    {
        // URL for the customer to visit
        $url = route('customer.index', $table->code);
        
        // Generate QR Code
        $qr = QrCode::size(300)->generate($url);

        return view('pos.tables.qr', compact('table', 'qr', 'url'));
    }
}